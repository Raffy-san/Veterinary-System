<?php
// ✅ Helper function

function jsonResponse($status, $message, $extra = [])
{
    header('Content-Type: application/json');

    $response = array_merge([
        "status" => $status,
        "message" => $message
    ], $extra);

    echo json_encode($response);
    exit; // 🔑 ensures nothing else is sent
}
