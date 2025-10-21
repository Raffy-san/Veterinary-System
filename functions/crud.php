<?php
function addClient($pdo, $data)
{
    try {
        $access_type = 'owner';
        $status = 1;

        // Real password (could be random or admin-defined)
        $realPassword = $data['password'] ?? '';
        $hashedPassword = password_hash($realPassword, PASSWORD_DEFAULT);

        // Default password is only for backup — DO NOT store it in users table
        // It’s retrieved later from settings table via getDefaultPassword()

        // Check for existing email
        $check = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $check->execute([$data['email']]);
        if ($check->fetchColumn() > 0) {
            return json_encode(["status" => "error", "message" => "Email already exists"]);
        }

        $pdo->beginTransaction();

        $stmt = $pdo->prepare("
            INSERT INTO users (email, password, access_type, status)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$data['email'], $hashedPassword, $access_type, $status]);
        $user_id = $pdo->lastInsertId();

        $stmt = $pdo->prepare("
            INSERT INTO owners (user_id, name, email, phone, emergency, address, status)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $user_id,
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['emergency'],
            $data['address'],
            $status
        ]);

        $pdo->commit();
        return json_encode([
            "status" => "success",
            "message" => "Client added successfully"
        ]);

    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log("AddClient failed: " . $e->getMessage());
        return json_encode([
            "status" => "error",
            "message" => "Unable to add client. Please try again."
        ]);
    }
}

function updateClient($pdo, $data)
{
    try {
        $pdo->beginTransaction();

        // -----------------------
        // 1. Update owners table
        // -----------------------
        $ownerFields = [];
        $ownerValues = [];

        if (isset($data['name'])) {
            $ownerFields[] = "name = ?";
            $ownerValues[] = $data['name'];
        }
        if (isset($data['email'])) {
            $ownerFields[] = "email = ?";
            $ownerValues[] = $data['email'];
        }
        if (isset($data['phone'])) {
            $ownerFields[] = "phone = ?";
            $ownerValues[] = $data['phone'];
        }
        if (isset($data['emergency_contact'])) {
            $ownerFields[] = "emergency = ?";
            $ownerValues[] = $data['emergency_contact'];
        }
        if (isset($data['address'])) {
            $ownerFields[] = "address = ?";
            $ownerValues[] = $data['address'];
        }

        if (!empty($ownerFields)) {
            $ownerValues[] = $data['owner_id'];
            $sql = "UPDATE owners SET " . implode(", ", $ownerFields) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($ownerValues);
        }

        // -----------------------
        // 2. Get linked user_id
        // -----------------------
        $stmt = $pdo->prepare("SELECT user_id FROM owners WHERE id = ?");
        $stmt->execute([$data['owner_id']]);
        $user_id = $stmt->fetchColumn();

        if (!$user_id) {
            $pdo->rollBack();
            return json_encode(["status" => "error", "message" => "User not found for this owner"]);
        }

        // -----------------------
        // 3. Update users table
        // -----------------------
        if (!empty($data['email'])) {
            // check duplicate email
            $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $checkStmt->execute([$data['email'], $user_id]);
            if ($checkStmt->fetch()) {
                return json_encode(["status" => "error", "message" => "Email already exists"]);
            }

            if (!empty($data['password'])) {
                $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET email = ?, password = ? WHERE id = ?");
                $stmt->execute([$data['email'], $hashedPassword, $user_id]);

                $logStmt = $pdo->prepare("INSERT INTO password_resets (user_id, reset_by_admin_id) VALUES (?, ?)");
                $logStmt->execute([$user_id, $_SESSION['user_id']]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
                $stmt->execute([$data['email'], $user_id]);
            }
        } elseif (!empty($data['password'])) {
            // update only password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashedPassword, $user_id]);
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
            INSERT INTO pets (name, species, breed, age, age_unit, gender, weight, weight_unit, color, owner_id, notes, birth_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$data['name'], $data['species'], $data['breed'], $data['age'], $data['age_unit'], $data['gender'], $data['weight'], $data['weight_unit'], $data['color'], $data['owner_id'], $data['notes'], $data['birth_date']]);
        return json_encode(["status" => "success", "message" => "Pet added successfully"]);

    } catch (PDOException $e) {
        return json_encode(["status" => "error", "message" => "Error adding pet: " . $e->getMessage()]);
    }
}

function updatePet($pdo, $data)
{
    try {
        $stmt = $pdo->prepare("
            UPDATE pets SET name = ?, species = ?, breed = ?, age = ?, age_unit = ?, gender = ?, weight = ?, weight_unit = ?, color = ?, birth_date = ?, notes = ? WHERE id = ?
        ");
        $stmt->execute([$data['name'], $data['species'], $data['breed'], $data['age'], $data['age_unit'], $data['gender'], $data['weight'], $data['weight_unit'], $data['color'], $data['birth_date'], $data['notes'], $data['pet_id']]);
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
        // ✅ If follow_up_date is empty, store NULL instead of 0000-00-00
        $follow_up_date = !empty($data['follow_up_date']) ? $data['follow_up_date'] : null;

        $stmt = $pdo->prepare("
            INSERT INTO medical_records 
                (pet_id, visit_date, visit_time, visit_type, veterinarian, weight, weight_unit, temperature, temp_unit, diagnosis, treatment, medications, notes, follow_up_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $data['pet_id'],
            $data['visit_date'],
            $data['visit_time'],
            $data['visit_type'],
            $data['veterinarian'],
            $data['weight'],
            $data['weight_unit'],
            $data['temperature'],
            $data['temp_unit'],
            $data['diagnosis'],
            $data['treatment'],
            $data['medications'],
            $data['notes'],
            $follow_up_date // ✅ use the normalized variable
        ]);

        // ✅ get the new record id
        $newId = $pdo->lastInsertId();

        return json_encode([
            "status" => "success",
            "message" => "Medical record added successfully",
            "id" => $newId
        ]);

    } catch (PDOException $e) {
        return json_encode([
            "status" => "error",
            "message" => "Error adding medical record: " . $e->getMessage()
        ]);
    }
}



function updateMedicalRecord($pdo, $data)
{
    try {
        // ✅ Normalize follow_up_date (NULL if empty)
        $follow_up_date = !empty($data['follow_up_date']) ? $data['follow_up_date'] : null;

        $stmt = $pdo->prepare("
            UPDATE medical_records 
            SET visit_date = ?, 
                visit_time = ?,
                visit_type = ?, 
                veterinarian =?,
                weight = ?, 
                weight_unit = ?,
                temperature = ?,
                temp_unit = ?, 
                diagnosis = ?, 
                treatment = ?, 
                medications = ?, 
                notes = ?, 
                follow_up_date = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $data['visit_date'],
            $data['visit_time'],
            $data['visit_type'],
            $data['veterinarian'],
            $data['weight'],
            $data['weight_unit'],
            $data['temperature'],
            $data['temp_unit'],
            $data['diagnosis'],
            $data['treatment'],
            $data['medications'],
            $data['notes'],
            $follow_up_date,        // ✅ use normalized value
            $data['record_id']
        ]);

        return json_encode([
            "status" => "success",
            "message" => "Medical record updated successfully"
        ]);

    } catch (PDOException $e) {
        return json_encode([
            "status" => "error",
            "message" => "Error updating medical record: " . $e->getMessage()
        ]);
    }
}


function deleteMedicalRecord($pdo, $id)
{
    try {
        // Instead of deleting the record, mark it as deleted
        $stmt = $pdo->prepare("UPDATE medical_records SET deleted_at = NOW(), is_deleted = 1 WHERE id = ?");
        $stmt->execute([$id]);

        return json_encode([
            "status" => "success",
            "message" => "Medical record archived successfully"
        ]);
    } catch (PDOException $e) {
        return json_encode([
            "status" => "error",
            "message" => "Error archiving medical record: " . $e->getMessage()
        ]);
    }
}


function updateAdmin($pdo, $data)
{
    try {
        // Validate email
        if (empty($data['email'])) {
            return json_encode(["status" => "error", "message" => "Email cannot be empty"]);
        }

        // Fetch current user info
        $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
        $stmt->execute([$data['admin_id']]);
        $currentEmail = $stmt->fetchColumn();

        if (!$currentEmail) {
            return json_encode(["status" => "error", "message" => "Admin not found"]);
        }

        // Check for duplicate email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$data['email'], $data['admin_id']]);
        if ($stmt->fetch()) {
            return json_encode(["status" => "error", "message" => "Email already exists"]);
        }

        // If no changes, return success
        if ($currentEmail === $data['email'] && empty($data['password'])) {
            return json_encode(["status" => "success", "message" => "No changes were made"]);
        }

        // Build update query dynamically
        if (!empty($data['password'])) {
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET email = ?, password = ? WHERE id = ?");
            $stmt->execute([$data['email'], $hashedPassword, $data['admin_id']]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET email = ? WHERE id = ?");
            $stmt->execute([$data['email'], $data['admin_id']]);
        }

        // ✅ Refresh session data after update
        $_SESSION['email'] = $data['email'];

        return json_encode(["status" => "success", "message" => "Admin updated successfully"]);
    } catch (PDOException $e) {
        // Log actual error instead of exposing it
        error_log("UpdateAdmin Error: " . $e->getMessage());
        return json_encode(["status" => "error", "message" => "An error occurred while updating admin"]);
    }
}


