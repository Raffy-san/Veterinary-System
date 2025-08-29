<?php
include_once '../config/config.php';

if(isset($_GET['id'])) {
    $recordId = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM medical_records WHERE id = ?");
    $stmt->execute(['id' => $recordId]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($record);
}
