<?php
/**
 * Two independent, opaque tokens per companion:
 *   kind='identity' → printed on the back of the PETICA ID Card
 *   kind='tag'      → engraved into the physical QR Tag medallion
 *
 * The URL actually encoded in each physical QR is an unguessable random
 * token, not the sequential serial number — so nobody can enumerate every
 * companion in the system by incrementing a counter in the URL. The
 * serial number itself is still resolvable (§6 of the ecosystem brief
 * wants it in the URL shown on dashboards) through a SEPARATE route
 * (see id/index.php) — both resolve to the same public view.
 */
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/errors.php';

function uuid4(): string {
    $data = random_bytes(16);
    $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
    $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function qr_issue_for_companion(PDO $pdo, int $companionId): void {
    $pdo->prepare('INSERT INTO qr_identities (companion_id, kind, token) VALUES (?, "identity", ?)')
        ->execute([$companionId, uuid4()]);
    $pdo->prepare('INSERT INTO qr_identities (companion_id, kind, token) VALUES (?, "tag", ?)')
        ->execute([$companionId, uuid4()]);
}

function qr_reissue_tag(int $companionId): string {
    throw ApiError::forbidden('Permanent QR destinations cannot be rotated.');
}

/** Returns ['kind' => ..., 'companion' => [...]] or null. */
function qr_resolve_token(string $token): ?array {
    $stmt = db()->prepare('SELECT * FROM qr_identities WHERE token = ?');
    $stmt->execute([$token]);
    $row = $stmt->fetch();
    if (!$row) return null;
    $stmt = db()->prepare('SELECT * FROM companions WHERE id = ?');
    $stmt->execute([$row['companion_id']]);
    $companion = $stmt->fetch();
    if (!$companion) return null;
    return ['kind' => $row['kind'], 'companion' => $companion];
}

function qr_for_companion(int $companionId): array {
    $stmt = db()->prepare('SELECT kind, token FROM qr_identities WHERE companion_id = ?');
    $stmt->execute([$companionId]);
    return $stmt->fetchAll();
}

/** Builds the {identity:{token,url}, tag:{...}, identity_serial_url, lost_qr_legacy_url} block. */
function qr_urls(array $companion): array {
    $rows = qr_for_companion((int)$companion['id']);
    $out = [];
    foreach ($rows as $r) {
        $prefix = $r['kind'] === 'tag' ? 'q' : 'id';
        $out[$r['kind']] = ['token' => $r['token'], 'url' => APP_URL . '/' . $prefix . '/' . $r['token']];
    }
    $out['identity_print_url'] = isset($out['identity']) ? PUBLIC_URL.'/id/'.$out['identity']['token'] : null;
    $out['identity_serial_url'] = APP_URL . '/id/' . $companion['serial_number'];
    $out['lost_qr_legacy_url'] = APP_URL . '/user/PETICAQR/index.php?id=' . $companion['id'] . '&petica=' . $companion['serial_number'];
    $out['tag_print_url'] = PUBLIC_URL . '/user/PETICAQR/index.php?id=' . $companion['id'] . '&petica=' . rawurlencode($companion['serial_number']);
    return $out;
}

/**
 * Privacy boundary: what a stranger scanning a QR is allowed to see.
 * Never includes medical_info, microchip_number, weight, date_of_birth,
 * or anything about the owner beyond what was explicitly opted to publish.
 */
function companion_public_view(array $c, string $kind): array {
    $base = [
        'name' => $c['name'],
        'species' => $c['species'],
        'breed' => $c['public_show_breed'] ? $c['breed'] : null,
        'colors_marks' => $c['colors_marks'],
        'photo_url' => $c['photo_path'],
        'status' => $c['status'],
        'lost_message' => $c['status'] === 'lost' ? $c['lost_message'] : null,
        'contact_name' => !empty($c['public_contact_consent']) ? $c['public_contact_name'] : null,
        'contact_phone' => !empty($c['public_contact_consent']) ? $c['public_contact_phone'] : null,
        'serial_number' => $c['serial_number'],
    ];
    return $base;
}
