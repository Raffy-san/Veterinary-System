<?php
// ✅ Helper function
function jsonResponse($status, $message, $extra = [])
{
    echo json_encode(array_merge([
        "status" => $status,
        "message" => $message
    ], $extra));
    exit;
}