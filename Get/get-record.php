<?php
include_once '../config/config.php';

if (isset($_GET['id'])) {
    $recordId = intval($_GET['id']);

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
            CASE WHEN m.follow_up_date IS NOT NULL 
                THEN DATE_FORMAT(m.follow_up_date, '%Y-%m-%d') 
                ELSE '' 
            END AS follow_up_date
        FROM medical_records m
        INNER JOIN pets p ON m.pet_id = p.id
        WHERE m.id = ?
    ");
    $stmt->execute([$recordId]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($record);
}
