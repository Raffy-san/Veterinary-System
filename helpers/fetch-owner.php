<?php
include_once '../config/config.php'; // make sure $pdo is defined here
include_once '../functions/session.php';

if (isset($_GET['query'])) {
    $search = "%" . $_GET['query'] . "%";

    $stmt = $pdo->prepare("SELECT id, name, phone, email FROM owners WHERE name LIKE ? LIMIT 5");
    $stmt->execute([$search]);

    $owners = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($owners);
}
