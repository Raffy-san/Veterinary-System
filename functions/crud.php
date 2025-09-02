<?php
function addClient($pdo, $data)
{
    try {
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $access_type = 'owner';
        $status = 1;

        $pdo->beginTransaction();

        // Insert into users table
        $stmt = $pdo->prepare("INSERT INTO users (username, password, access_type, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data['username'], $hashedPassword, $access_type, $status]);
        $user_id = $pdo->lastInsertId();

        // Insert into owners table
        $stmt = $pdo->prepare("INSERT INTO owners (user_id, name, email, phone, emergency, address, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $data['name'], $data['email'], $data['phone'], $data['emergency'], $data['address'], $status]);

        $pdo->commit();
        return json_encode(["status" => "success", "message" => "Client added successfully"]);

    } catch (PDOException $e) {
        $pdo->rollBack();
        return json_encode(["status" => "error", "message" => "Error adding client: " . $e->getMessage()]);
    }
}

function updateClient($pdo, $data)
{
    try {
        $pdo->beginTransaction();

        // Update owners table
        $stmt = $pdo->prepare("UPDATE owners SET name = ?, email = ?, phone = ?, emergency = ?, address = ? WHERE id = ?");
        $stmt->execute([
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['emergency_contact'],
            $data['address'],
            $data['owner_id']
        ]);

        // Get user_id linked to this owner
        $stmt = $pdo->prepare("SELECT user_id FROM owners WHERE id = ?");
        $stmt->execute([$data['owner_id']]);
        $user_id = $stmt->fetchColumn();

        if (!$user_id) {
            return json_encode(["status" => "error", "message" => "User not found for this owner"]);
        }

        // Check duplicate username
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $checkStmt->execute([$data['username'], $user_id]);
        if ($checkStmt->fetch()) {
            return json_encode(["status" => "error", "message" => "Username already exists"]);
        }

        // Update users table
        if (!empty($data['password'])) {
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET username = ?, password = ? WHERE id = ?");
            $stmt->execute([$data['username'], $hashedPassword, $user_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
            $stmt->execute([$data['username'], $user_id]);
        }

        $pdo->commit();
        return json_encode(["status" => "success", "message" => "Client updated successfully"]);

    } catch (PDOException $e) {
        $pdo->rollBack();
        return json_encode(["status" => "error", "message" => "Error updating client: " . $e->getMessage()]);
    }
}

function deleteClient($pdo, $user_id)
{
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("DELETE FROM owners WHERE user_id = ?");
        $stmt->execute([$user_id]);

        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);

        $pdo->commit();
        return json_encode(["status" => "success", "message" => "Client deleted successfully"]);

    } catch (PDOException $e) {
        $pdo->rollBack();
        return json_encode(["status" => "error", "message" => "Error deleting client: " . $e->getMessage()]);
    }
}

function addPet($pdo, $data)
{
    try {
        $stmt = $pdo->prepare("
            INSERT INTO pets (name, species, breed, age, gender, weight, color, owner_id, notes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$data['name'], $data['species'], $data['breed'], $data['age'], $data['gender'], $data['weight'], $data['color'], $data['owner_id'], $data['notes']]);
        return json_encode(["status" => "success", "message" => "Pet added successfully"]);

    } catch (PDOException $e) {
        return json_encode(["status" => "error", "message" => "Error adding pet: " . $e->getMessage()]);
    }
}

function updatePet($pdo, $data)
{
    try {
        $stmt = $pdo->prepare("
            UPDATE pets SET name = ?, species = ?, breed = ?, age = ?, gender = ?, weight = ?, color = ?, notes = ? WHERE id = ?
        ");
        $stmt->execute([$data['name'], $data['species'], $data['breed'], $data['age'], $data['gender'], $data['weight'], $data['color'], $data['notes'], $data['pet_id']]);
        return json_encode(["status" => "success", "message" => "Pet updated successfully"]);

    } catch (PDOException $e) {
        return json_encode(["status" => "error", "message" => "Error updating pet: " . $e->getMessage()]);
    }
}

function deletePet($pdo, $id)
{
    try {
        $stmt = $pdo->prepare("DELETE FROM pets WHERE id = ?");
        $stmt->execute([$id]);
        return json_encode(["status" => "success", "message" => "Pet deleted successfully"]);

    } catch (PDOException $e) {
        return json_encode(["status" => "error", "message" => "Error deleting pet: " . $e->getMessage()]);
    }
}

function addMedicalRecord($pdo, $data)
{
    try {
        $stmt = $pdo->prepare("
            INSERT INTO medical_records (pet_id, visit_date, visit_type, weight, temperature, diagnosis, treatment, medications, notes, follow_up_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['pet_id'],
            $data['visit_date'],
            $data['visit_type'],
            $data['weight'],
            $data['temperature'],
            $data['diagnosis'],
            $data['treatment'],
            $data['medications'],
            $data['notes'],
            $data['follow_up_date']
        ]);
        return json_encode(["status" => "success", "message" => "Medical record added successfully"]);

    } catch (PDOException $e) {
        return json_encode(["status" => "error", "message" => "Error adding medical record: " . $e->getMessage()]);
    }
}

function updateMedicalRecord($pdo, $data)
{
    try {
        $stmt = $pdo->prepare("
            UPDATE medical_records SET visit_date = ?, visit_type = ?, weight = ?, temperature = ?, diagnosis = ?, treatment = ?, medications = ?, notes = ?, follow_up_date = ? WHERE id = ?
        ");
        $stmt->execute([
            $data['visit_date'],
            $data['visit_type'],
            $data['weight'],
            $data['temperature'],
            $data['diagnosis'],
            $data['treatment'],
            $data['medications'],
            $data['notes'],
            $data['follow_up_date'],
            $data['medical_record_id']
        ]);
        return json_encode(["status" => "success", "message" => "Medical record updated successfully"]);

    } catch (PDOException $e) {
        return json_encode(["status" => "error", "message" => "Error updating medical record: " . $e->getMessage()]);
    }
}

function deleteMedicalRecord($pdo, $id)
{
    try {
        $stmt = $pdo->prepare("DELETE FROM medical_records WHERE id = ?");
        $stmt->execute([$id]);
        return json_encode(["status" => "success", "message" => "Medical record deleted successfully"]);

    } catch (PDOException $e) {
        return json_encode(["status" => "error", "message" => "Error deleting medical record: " . $e->getMessage()]);
    }
}

function updateAdmin($pdo, $data)
{
    try {
        // Validate username
        if (empty($data['username'])) {
            return json_encode(["status" => "error", "message" => "Username cannot be empty"]);
        }

        // If password is provided, update both username and password
        if (!empty($data['password'])) {
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET username = ?, password = ? WHERE id = ?");
            $stmt->execute([$data['username'], $hashedPassword, $data['admin_id']]);
        } else {
            // Only update username
            $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
            $stmt->execute([$data['username'], $data['admin_id']]);
        }

        if ($stmt->rowCount() > 0) {
            return json_encode(["status" => "success", "message" => "Admin updated successfully"]);
        } else {
            return json_encode(["status" => "error", "message" => "No changes were made"]);
        }

    } catch (PDOException $e) {
        return json_encode(["status" => "error", "message" => "Error updating admin: " . $e->getMessage()]);
    }
}

