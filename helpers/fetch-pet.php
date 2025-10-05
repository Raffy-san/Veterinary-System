<?php
include_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/session.php';

if (isset($_GET['owner_id'])) {
    $ownerId = intval($_GET['owner_id']);

    $stmt = $pdo->prepare("SELECT id, name, species, breed, age, age_unit, gender, weight, weight_unit, color, notes, birth_date , registered_at FROM pets WHERE owner_id = ?");
    $stmt->execute([$ownerId]);
    $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($pets);
    exit;
}

echo json_encode([]);
exit;
