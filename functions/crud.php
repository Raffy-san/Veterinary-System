<?php
function addClient($pdo, $data)
{
    $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
    $access_type = 'owner'; // or whatever value you use for pet owners
    $status = 1; // Set to 1 for active, 0 for inactive

    // 1. Insert into users table
    $stmt = $pdo->prepare("INSERT INTO users (username, password, access_type, status) VALUES (?, ?, ?, ?)");
    $stmt->execute([$data['username'], $hashedPassword, $access_type, $status]);
    $user_id = $pdo->lastInsertId();

    // 2. Insert into owners table, referencing the user_id
    $stmt = $pdo->prepare("INSERT INTO owners (user_id, name, email, phone, emergency, address, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $data['name'], $data['email'], $data['phone'], $data['emergency'], $data['address'], $status]);

    return true;
}


function updateClient($pdo, $data)
{
    try {
        $pdo->beginTransaction();

        // ✅ Update owners table
        $stmt = $pdo->prepare("UPDATE owners SET name = ?, email = ?, phone = ?, emergency = ?, address = ? WHERE id = ?");
        $stmt->execute([
            $data['name'],
            $data['email'],
            $data['phone'],
            $data['emergency_contact'],
            $data['address'],
            $data['owner_id'] // from the form
        ]);

        // ✅ Get user_id linked to this owner
        $stmt = $pdo->prepare("SELECT user_id FROM owners WHERE id = ?");
        $stmt->execute([$data['owner_id']]);
        $user_id = $stmt->fetchColumn();

        if (!$user_id) {
            throw new Exception("User not found for this owner");
        }

        // ✅ Check duplicate username excluding current user
        $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $checkStmt->execute([$data['username'], $user_id]);
        if ($checkStmt->fetch()) {
            throw new Exception("Username already exists");
        }

        // ✅ Update users table
        if (!empty($data['password'])) {
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET username = ?, password = ? WHERE id = ?");
            $stmt->execute([$data['username'], $hashedPassword, $user_id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
            $stmt->execute([$data['username'], $user_id]);
        }

        $pdo->commit();
        return true;

    } catch (Exception $e) {
        $pdo->rollBack();
        throw $e;
    }
}



function deleteClient($pdo, $user_id)
{
    // First, delete from the owners table
    $stmt = $pdo->prepare("DELETE FROM owners WHERE user_id = ?");
    $stmt->execute([$user_id]);

    // Then, delete from the users table
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    return $stmt->execute([$user_id]);

}


function addPet($pdo, $data)
{
    $stmt = $pdo->prepare("
        INSERT INTO pets (name, species, breed, age, gender, weight, color, owner_id, notes) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    return $stmt->execute([$data['name'], $data['species'], $data['breed'], $data['age'], $data['gender'], $data['weight'], $data['color'], $data['owner_id'], $data['notes']]);
}

function updatePet($pdo, $data)
{
    $stmt = $pdo->prepare("
        UPDATE pets SET name = ?, species = ?, breed = ?, age = ?, gender = ?, weight = ?, color = ?, notes = ? WHERE id = ?
    ");
    return $stmt->execute([$data['name'], $data['species'], $data['breed'], $data['age'], $data['gender'], $data['weight'], $data['color'], $data['notes'], $data['pet_id']]);
}

function deletePet($pdo, $id)
{
    $stmt = $pdo->prepare("DELETE FROM pets WHERE id = ?");
    return $stmt->execute([$id]);
}


function addMedicalRecord($pdo, $data)
{
    $stmt = $pdo->prepare("
          INSERT INTO medical_records (pet_id, visit_date, visit_type, weight, temperature, diagnosis, treatment, medications, notes, follow_up_date) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
      ");
    return $stmt->execute([
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
}

function updateMedicalRecord($pdo, $data)
{
    $stmt = $pdo->prepare("
        UPDATE medical_records SET pet_id = ?, visit_date = ?, visit_type = ?, weight = ?, temperature = ?, diagnosis = ?, treatment = ?, medications = ?, notes = ?, follow_up_date = ? WHERE id = ?
    ");
    return $stmt->execute([
        $data['pet_id'],
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
}

function deleteMedicalRecord($pdo, $id)
{
    $stmt = $pdo->prepare("DELETE FROM medical_records WHERE id = ?");
    return $stmt->execute([$id]);
}
