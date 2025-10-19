<?php
function getDefaultPassword(PDO $pdo, string $access_type): ?string
{
    $key = 'default_' . strtolower($access_type) . '_password';
    $stmt = $pdo->prepare("SELECT setting_value FROM system_settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    return $stmt->fetchColumn();
}

function updateDefaultPassword(PDO $pdo, string $access_type, string $newPassword): bool
{
    $key = 'default_' . strtolower($access_type) . '_password';
    $stmt = $pdo->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = ?");
    return $stmt->execute([$newPassword, $key]);
}

