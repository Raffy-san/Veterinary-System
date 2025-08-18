<?php
include_once '../config/config.php';
require_once '../functions/session.php';
SessionManager::requireLogin();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $petName = $_POST['petname'];
    $petSpecies = $_POST['petspecies'];
    $breed = $_POST['breed'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $notes = $_POST['notes'];
    $ownerId = $_POST['owner_id']; // 👈 passed from the form (autocomplete or dropdown)

    // ✅ Insert pet with the selected owner_id
    $stmt = $pdo->prepare("
        INSERT INTO pets (name, species, breed, age, gender, owner_id, notes) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$petName, $petSpecies, $breed, $age, $gender, $ownerId, $notes]);

    // Redirect to pets page
    header('Location: pet-management.php');
    exit;
}
?>