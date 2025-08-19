<?php
include_once '../config/config.php';
include_once '../functions/session.php';

if (isset($_GET['owner_id'])) {
    $ownerId = intval($_GET['owner_id']);

    $stmt = $pdo->prepare("SELECT name, species, breed, age, gender, registered_at FROM pets WHERE owner_id = ?");
    $stmt->execute([$ownerId]);
    $pets = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($pets);
    exit;
}

echo json_encode([]);
exit;
