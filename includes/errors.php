<?php
/**
 * A single ApiError class covers every "expected" failure (validation,
 * not-found, forbidden, conflict) — mirrors server/middleware/errors.js
 * from the original Node backend so the frontend's existing error
 * handling (which reads {error:{code,message,details}}) needs no changes.
 */
class ApiError extends Exception {
    public int $status;
    public string $apiCode;
    public $details;

    public function __construct(int $status, string $code, string $message, $details = null) {
        parent::__construct($message);
        $this->status = $status;
        $this->apiCode = $code;
        $this->details = $details;
    }

    public static function notFound(string $what = 'Resource'): self {
        return new self(404, 'not_found', "$what not found.");
    }
    public static function forbidden(string $msg = 'You do not have permission to do this.'): self {
        return new self(403, 'forbidden', $msg);
    }
    public static function unauthorized(string $msg = 'Authentication required.'): self {
        return new self(401, 'unauthorized', $msg);
    }
    public static function badRequest(string $msg, $details = null): self {
        return new self(400, 'bad_request', $msg, $details);
    }
    public static function conflict(string $msg): self {
        return new self(409, 'conflict', $msg);
    }
}

function send_json($data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function send_error(ApiError $e): void {
    $body = ['error' => ['code' => $e->apiCode, 'message' => $e->getMessage()]];
    if ($e->details !== null) $body['error']['details'] = $e->details;
    send_json($body, $e->status);
}

/** Reads and JSON-decodes the request body once per request. */
function body(): array {
    static $parsed = null;
    if ($parsed === null) {
        $raw = file_get_contents('php://input');
        $parsed = $raw ? (json_decode($raw, true) ?? []) : [];
    }
    return $parsed;
}

/** Required-field + type-checking helper. Throws bad_request with a
 *  field-level details array shaped like the original Zod validation
 *  errors, so the same frontend error-rendering code works unchanged. */
function require_fields(array $data, array $fields): void {
    $missing = [];
    foreach ($fields as $f) {
        if (!isset($data[$f]) || (is_string($data[$f]) && trim($data[$f]) === '')) {
            $missing[] = ['path' => $f, 'message' => 'Required.'];
        }
    }
    if ($missing) {
        throw ApiError::badRequest('Some fields are invalid.', $missing);
    }
}

function str_field($data, string $key, $default = null) {
    if (!isset($data[$key])) return $default;
    $v = trim((string)$data[$key]);
    return $v === '' ? $default : $v;
}
