<?php
include_once '../config/config.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("
        SELECT 
            o.id,
            o.name,
            o.email,
            o.phone,
            o.emergency,
            o.address,
            u.id AS user_id,
            u.username,
            u.password
        FROM owners o
        INNER JOIN users u ON o.user_id = u.id
        WHERE o.id = ?
    ");
    $stmt->execute([$id]);
    $client = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($client);
}
