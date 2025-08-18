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
    $ownerName = $_POST['owner_name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $notes = $_POST['notes'];

    // 🔎 Get owner_id based on email or phone (unique identifier)
    $stmt = $pdo->prepare("SELECT id FROM owners WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $owner = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($owner) {
        $ownerId = $owner['id'];
    } else {
        // If owner doesn't exist, insert a new owner
        $stmt = $pdo->prepare("INSERT INTO owners (owner_name, phone, email) VALUES (?, ?, ?)");
        $stmt->execute([$ownerName, $phone, $email]);
        $ownerId = $pdo->lastInsertId();
    }

    // ✅ Insert pet with the owner_id
    $stmt = $pdo->prepare("INSERT INTO pets (name, species, breed, age, gender, owner_id, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$petName, $petSpecies, $breed, $age, $gender, $ownerId, $notes]);

    // Redirect or display success message
    header('Location: pet-management.php');
    exit;
}
?>