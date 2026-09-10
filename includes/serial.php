<?php
/**
 * PETICA serial numbers: exactly a 3-letter species prefix + 9 digits
 * (e.g. DOG-000000001). Never 6/7/8 digits, never a generic "PET-" prefix.
 * The counter increment happens inside a single transaction so two
 * simultaneous registrations of the same species can never collide.
 */
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/errors.php';

function next_serial(PDO $pdo, string $species): string {
    $stmt = $pdo->prepare('SELECT code FROM species_codes WHERE species = ?');
    $stmt->execute([$species]);
    $code = $stmt->fetchColumn();
    if (!$code) throw ApiError::badRequest("Unknown species: $species");

    // UPSERT-style atomic increment, safe under concurrent requests because
    // it happens inside the caller's transaction with InnoDB row locking.
    $pdo->prepare('INSERT INTO serial_counters (species_code, last_value) VALUES (?, 1)
                   ON DUPLICATE KEY UPDATE last_value = last_value + 1')->execute([$code]);
    $stmt = $pdo->prepare('SELECT last_value FROM serial_counters WHERE species_code = ?');
    $stmt->execute([$code]);
    $n = (int)$stmt->fetchColumn();

    return $code . '-' . str_pad((string)$n, 9, '0', STR_PAD_LEFT);
}
