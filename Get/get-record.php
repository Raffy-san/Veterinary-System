<?php
include_once '../config/config.php';

header('Content-Type: application/json');

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    echo json_encode(["error" => "Invalid or missing ID"]);
    exit;
}

$recordId = intval($_GET['id']);

try {
    $stmt = $pdo->prepare("
        SELECT 
            m.id, 
            m.pet_id, 
            p.name AS pet_name, 
            DATE_FORMAT(m.visit_date, '%Y-%m-%d') AS visit_date, 
            m.visit_type, 
            m.weight, 
            m.temperature, 
            m.diagnosis, 
            m.treatment, 
            m.medications, 
            m.notes, 
            CASE 
                WHEN m.follow_up_date IS NOT NULL 
                THEN DATE_FORMAT(m.follow_up_date, '%Y-%m-%d') 
                ELSE '' 
            END AS follow_up_date
        FROM medical_records m
        INNER JOIN pets p ON m.pet_id = p.id
        WHERE m.id = ?
        LIMIT 1
    ");
    $stmt->execute([$recordId]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($record) {
        echo json_encode($record);
    } else {
        echo json_encode(["error" => "Record not found"]);
    }
} catch (Exception $e) {
    error_log("Error fetching record: " . $e->getMessage());
    echo json_encode(["error" => "An error occurred while fetching record"]);
}
