<?php 
  function addClient($pdo, $data) {
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


  function updateClient($pdo, $data) {
      $stmt = $pdo->prepare("UPDATE owners SET name = ?, email = ?, phone = ?, emergency = ?, address = ? WHERE user_id = ?");
      return $stmt->execute([$data['name'], $data['email'], $data['phone'], $data['emergency_contact'], $data['address'], $data['user_id']]);
  }


  function deleteClient($pdo, $user_id) {
      // First, delete from the owners table
      $stmt = $pdo->prepare("DELETE FROM owners WHERE user_id = ?");
      $stmt->execute([$user_id]);

      // Then, delete from the users table
      $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
      return $stmt->execute([$user_id]);

  }

  function activateClient($pdo, $user_id) {
      $stmt = $pdo->prepare("UPDATE owners SET status = 1 WHERE id = ?");
      return $stmt->execute([$user_id]);
  }

  function deactivateClient($pdo, $user_id) {
      $stmt = $pdo->prepare("UPDATE owners SET status = 0 WHERE id = ?");
      return $stmt->execute([$user_id]);
  }


  function addPet($pdo, $data) {
      $stmt = $pdo->prepare("
        INSERT INTO pets (name, species, breed, age, gender, weight, color, owner_id, notes) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
      return $stmt->execute([$data['name'], $data['species'], $data['breed'], $data['age'], $data['gender'], $data['weight'], $data['color'], $data['owner_id'], $data['notes']]);
  }

  function addMedicalRecord($pdo, $data) {
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