<?php
include_once __DIR__ . '/../config/config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare(
        "SELECT * FROM pets WHERE id = ?
            "
    );
    $stmt->execute([$id]);
    $pet = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($pet);
}