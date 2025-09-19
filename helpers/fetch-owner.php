<?php
include_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/session.php';

if (isset($_GET['query'])) {
    $search = "%" . $_GET['query'] . "%";

    $stmt = $pdo->prepare("SELECT id, name, phone, email FROM owners WHERE name LIKE ? LIMIT 5");
    $stmt->execute([$search]);

    $owners = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($owners);
}
