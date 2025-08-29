<?php
include_once '../config/config.php';
require_once '../functions/session.php';
require_once '../helpers/fetch.php';
require_once '../functions/crud.php';
SessionManager::requireLogin();
SessionManager::requireRole('admin');

$admin = SessionManager::getUser($pdo);

if (!$admin) {
    SessionManager::logout('../login.php');
}

if (isset($_POST['submit'])) {
    $data = [
        'pet_id' => $_POST['patient'],
        'visit_date' => $_POST['visit_date'],
        'visit_type' => $_POST['visit_type'],
        'weight' => $_POST['weight'],
        'temperature' => $_POST['temperature'],
        'diagnosis' => $_POST['diagnosis'],
        'treatment' => $_POST['treatment'],
        'medications' => $_POST['medications'],
        'notes' => $_POST['notes'],
        'follow_up_date' => $_POST['follow_up_date']
    ];

    if (addMedicalRecord($pdo, $data)) {
        header("Location: medical-records.php?added=1");
        exit;
    } else {
        header("Location: medical-records.php?added=0");
        exit;
    }
}

if (isset($_GET['delete_id'])) {
    $recordId = $_GET['delete_id'];

    if (deleteMedicalRecord($pdo, $recordId)) {
        header("Location: medical-records.php?deleted=1");
        exit;
    } else {
        header("Location: medical-records.php?deleted=0");
        exit;
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="../assets/img/green-paw.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="../assets/js/script.js"></script>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>Medical Records</title>
</head>

<body class="w-full bg-green-100 h-screen overflow-y-auto">
    <?php
    include_once '../includes/admin-header.php';
    ?>
    <main class="p-10">
        <div id="addNewRecord"
            class="modal fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
            <div
                class="custom-scrollbar bg-green-100 rounded-lg shadow-lg w-full max-w-md p-6 relative max-h-[80vh] overflow-y-auto">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-m font-semibold">Create Medical Record</h3>
                        <h4 class="text-sm text-gray-600">Document a medical visit, treatment, or procedure.</h4>
                    </div>
                    <button class="close text-xl">&times;</button>
                </div>
                <form class="flex flex-wrap items-center justify-between" method="POST" action="medical-records.php">
                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Patient</label>
                        <select
                            class="w-44 border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            name="patient" id="patient" required>
                            <option value="" Selected disabled>Select Patient</option>
                            <?php
                            try {
                                $stmt = $pdo->query("
                                    SELECT pets.id AS pet_id, pets.name AS pet_name, owners.name AS owner_name
                                    FROM pets
                                    INNER JOIN owners ON pets.owner_id = owners.id
                                    ORDER BY owners.name, pets.name
                                ");

                                while ($record = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<option value="' . htmlspecialchars($record['pet_id']) . '">'
                                        . htmlspecialchars($record['pet_name']) . ' (' . htmlspecialchars($record['owner_name']) .
                                        ')</option>';
                                }
                            } catch (PDOException $e) {
                                echo '<option disabled>Error loading patients</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="mb-4 w-auto">
                        <label class="block text-gray-700 mb-1 text-sm font-semibold">Date</label>
                        <input type="date" name="visit_date"
                            class="w-44 border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            required>
                    </div>
                    <div class="flex flex-record space-x-2">
                        <div class="mb-4 w-auto">
                            <label for="" class="block text-gray-700 mb-1 text-sm font-semibold">Visit Type</label>
                            <select name="visit_type"
                                class="w-auto border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="" selected disabled>Select Type</option>
                                <option value="Routine Checkup">Routine Checkup</option>
                                <option value="Vaccination">Vaccination</option>
                                <option value="Treatment">Treatment</option>
                                <option value="Emergency">Emergency</option>
                                <option value="Surgery">Surgery</option>
                            </select>
                        </div>
                        <div class="mb-4 w-auto">
                            <label for="" class="block text-gray-700 mb-1 text-sm font-semibold">Weight</label>
                            <input type="text"
                                class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                                name="weight" placeholder="e.g, 20lbs" required>
                        </div>
                        <div class="mb-4 w-auto">
                            <label for="" class="block text-gray-700 mb-1 text-sm font-semibold">Temperature</label>
                            <input type="text"
                                class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                                name="temperature" placeholder="e.g, 36°C" required>
                        </div>
                    </div>
                    <div class="mb-4 w-full">
                        <label for="" class="block text-gray-700 mb-1 text-sm font-semibold">Diagnosis</label>
                        <input type="text"
                            class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            name="diagnosis" placeholder="Primary diagnosis or reason for visit" required>
                    </div>
                    <div class="mb-4 w-full">
                        <label for="" class="block text-gray-700 mb-1 text-sm font-semibold">Treatment</label>
                        <textarea name="treatment" id=""
                            class="w-full border px-2 py-1 resize-none rounded text-sm focus:outline-none focus:ring-2 focus:ring-green-500 none"
                            placeholder="Describe the treatment of procedures performed"></textarea>
                    </div>
                    <div class="mb-4 w-full">
                        <label for="" class="block text-gray-700 mb-1 text-sm font-semibold">Medications</label>
                        <textarea name="medications" id=""
                            class="w-full border px-2 py-1 resize-none rounded text-sm focus:outline-none focus:ring-2 focus:ring-green-500 none"
                            placeholder="List the medications prescribed with dosage and frequency"></textarea>
                    </div>
                    <div class="mb-4 w-full">
                        <label for="" class="block text-gray-700 mb-1 text-sm font-semibold">Additional Notes</label>
                        <textarea name="notes" id=""
                            class="w-full border px-2 py-1 resize-none rounded text-sm focus:outline-none focus:ring-2 focus:ring-green-500 none"
                            placeholder="Add additional observations, instructions, or notes"></textarea>
                    </div>

                    <div class="mb-4 w-full">
                        <div class="flex flex-record space-x-2">
                            <input type="checkbox" id="followUpRequired" name="required">
                            <label for="followUpRequired" class="text-sm font-semibold">Follow-up appointment
                                required</label>
                        </div>
                        <label for="follow_up_date" class="block text-gray-700 mb-1 text-sm font-semibold">Follow-Up
                            Date (if applicable)</label>
                        <input type="date" id="follow_up_date" name="follow_up_date"
                            class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>

                    <div class="flex justify-end w-full">
                        <button type="btn" class="close mr-2 px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-xs">
                            Cancel</button>
                        <button type="submit" name="submit"
                            class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-700 text-xs">Add
                            Record</button>
                    </div>
                </form>
            </div>
        </div>
        <section class="w-full bg-white rounded-lg shadow-md p-8">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-lg">Medical Records</h3>
                    <h4 class="text-gray-600">Track treatments, medications, and medical history</h4>
                </div>
                <button data-modal="addNewRecord"
                    class="open-modal mt-4 px-4 py-2 bg-green-600 text-white rounded-lg text-xs hover:bg-green-700"><i
                        class="fa-solid fa-plus mr-2"></i>New Record</button>
            </div>
            <div class="mb-4">
                <form>
                    <i class="fa-solid fa-search text-sm"></i>
                    <input type="search" id="search" placeholder="Search Medical Records..."
                        class="bg-gray-100 rounded px-3 py-2 mb-4 text-sm w-64 focus:outline-none focus:ring-2 focus:ring-green-500">
                </form>
            </div>
            <table class="w-full table-collapse">
                <thead>
                    <tr class="text-sm text-left border-b">
                        <th class="font-semibold py-2">Date</th>
                        <th class="font-semibold py-2">Patient</th>
                        <th class="font-semibold py-2">Type</th>
                        <th class="font-semibold py-2">Diagnosis</th>
                        <th class="font-semibold py-2">Follow-up-Date</th>
                        <th class="font-semibold py-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="recordsBody">
                    <?php
                    $records = fetchAllData(
                        $pdo,
                        "SELECT m.id AS medical_record_id, m.visit_date, m.weight, m.temperature, p.name AS patient_name, o.name AS owner_name, m.visit_type, m.diagnosis, m.treatment, m.medications, m.notes, m.follow_up_date
                        FROM medical_records m
                        JOIN pets p ON m.pet_id = p.id
                        JOIN owners o ON p.owner_id = o.id"
                    );

                    $visitType = [
                        'Routine Checkup' => ['icon' => 'fa-solid fa-stethoscope', 'color' => 'text-green-800', 'bg' => 'bg-green-100'],
                        'Surgery' => ['icon' => 'fa-solid fa-kit-medical', 'color' => 'text-red-700', 'bg' => 'bg-red-100'],
                        'Vaccination' => ['icon' => 'fa-solid fa-syringe', 'color' => 'text-blue-700', 'bg' => 'bg-blue-100'],
                        'Treatment' => ['icon' => 'fa-solid fa-pills', 'color' => 'text-yellow-700', 'bg' => 'bg-yellow-100'],
                        'Emergency' => ['icon' => 'fa-solid fa-triangle-exclamation', 'color' => 'text-orange-700', 'bg' => 'bg-orange-100']
                    ];

                    foreach ($records as $record) {
                        $Type = $record['visit_type'];
                        $typeinfo = $visitType[$Type] ?? ['icon' => 'fa-solid fa-question', 'color' => 'text-gray-700', 'bg' => 'bg-gray-200'];

                        echo '<tr class="border-b hover:bg-green-50 text-sm">';
                        echo '<td class="py-2">' . htmlspecialchars($record['visit_date']) . '</td>';
                        echo "<td class='py-2'>
                                <span class='font-medium'>" . htmlspecialchars($record['patient_name']) . "</span><br>
                                <span class='text-gray-500 text-xs'>" . htmlspecialchars($record['owner_name']) . "</span>
                            </td>";
                        echo '<td class="py-2">
                              <div class="' . $typeinfo['bg'] . ' ' . $typeinfo['color'] . ' rounded-lg px-3 py-1 inline-flex items-center gap-2">
                                    <i class="' . $typeinfo['icon'] . '"></i>
                                    <span>' . htmlspecialchars($record['visit_type']) . '</span>
                                </div>
                            </td>';
                        echo '<td class="py-2">' . htmlspecialchars($record['diagnosis']) . '</td>';
                        echo '<td class="py-2">' . htmlspecialchars($record['follow_up_date'] ?? 'No Follow-up date') . '</td>';
                        echo '<td class="py-2 text-right space-x-1">
                                <button  
                                    data-modal = "viewModal"
                                    data-id="' . $record['medical_record_id'] . '"
                                    data-date="' . htmlspecialchars($record['visit_date'] ?? '') . '"                  data-type="' . htmlspecialchars($record['visit_type'] ?? '') . '" 
                                    data-type-bg="' . htmlspecialchars($typeinfo['bg']) . '"
                                    data-type-color="' . htmlspecialchars($typeinfo['color']) . '"
                                    data-type-icon="' . htmlspecialchars($typeinfo['icon']) . '"
                                    data-patient="' . htmlspecialchars($record['patient_name'] ?? '') . '" 
                                    data-owner="' . htmlspecialchars($record['owner_name'] ?? '') . '" 
                                    data-weight="' . htmlspecialchars($record['weight'] ?? '') . '"
                                    data-temperature="' . htmlspecialchars($record['temperature'] ?? '') . '"
                                    data-diagnosis="' . htmlspecialchars($record['diagnosis'] ?? '') . '" 
                                    data-treatment="' . htmlspecialchars($record['treatment'] ?? '') . '" 
                                    data-medications="' . htmlspecialchars($record['medications'] ?? '') . '" 
                                    data-follow="' . htmlspecialchars($record['follow_up_date'] ?? 'No Follow-up date') . '" 
                                    data-notes="' . htmlspecialchars($record['notes'] ?? '') . '" 
                                    class="open-modal fa-solid fa-eye text-gray-700 bg-green-100 p-2 rounded hover:bg-green-300" data-id="' . $record['medical_record_id'] . '"></button>
                                <button class="open-edit-modal fa-solid fa-pencil text-gray-700 bg-green-100 p-2 rounded hover:bg-green-300" data-id="' . $record['medical_record_id'] . '"></button>
                                <button class="open-delete-modal fa-solid fa-trash text-gray-700 bg-green-100 p-2 rounded hover:bg-green-300" data-id="' . $record['medical_record_id'] . '"></button>
                            </td>';
                        echo '</tr>';
                    }
                    ?>

                    <tr id="noResults" class="hidden">
                        <td colspan="8" class="text-center py-4 text-gray-500">No results found</td>
                    </tr>
                </tbody>
            </table>
            <div id="viewModal"
                class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                <div class="custom-scrollbar bg-green-100 rounded-lg p-4 max-h-[60vh] max-w-[450px] overflow-y-auto">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-semibold text-m">Medical Record - <span id="petName"></span></h3>
                            <h4 class="text-gray-500 text-sm"><span id="recordDate"></span></h4>
                        </div>
                        <button class="close text-xl" aria-label="Close">&times;</button>
                    </div>
                    <div class="flex flex-row justify-between space-x-2">
                        <div id="medicalDetails" class="text-sm bg-white p-4 border-green-400 rounded-lg mt-4 w-full">
                            <!-- row details will be populated here -->
                        </div>
                        <div id="followUpDetail" class="text-sm bg-white p-4 border-green-400 rounded-lg mt-4 w-full">
                            <!-- Additional details will be populated here -->
                        </div>
                    </div>
                    <div id="diagnosisDetails" class="text-sm bg-white p-4 border-green-400 rounded-lg mt-4 w-full">
                        <!-- Additional details will be populated here -->
                    </div>
                    <div id="treatmentDetails" class="text-sm bg-white p-4 border-green-400 rounded-lg mt-4 w-full">
                        <!-- Additional details will be populated here -->
                    </div>
                    <div id="medicationsDetails" class="text-sm bg-white p-4 border-green-400 rounded-lg mt-4 w-full">
                        <!-- Additional details will be populated here -->
                    </div>
                    <div id="notesDetails" class="text-sm bg-white p-4 border-green-400 rounded-lg mt-4 w-full">
                        <!-- Additional details will be populated here -->
                    </div>
                </div>
            </div>
            <div id="updateMedicalRecordModal"
                class="modal fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
                <div
                    class="custom-scrollbar bg-green-100 rounded-lg shadow-lg w-full max-w-md p-6 relative max-h-[80vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-m font-semibold">Update Medical Record</h3>
                            <h4 class="text-sm text-gray-600">Document a medical visit, treatment, or procedure.</h4>
                        </div>
                        <button class="close text-xl">&times;</button>
                    </div>
                    <form class="flex flex-wrap items-center justify-between" method="POST"
                        action="medical-records.php">
                        <div class="mb-4 w-auto">
                            <label class="block text-gray-700 mb-1 text-sm font-semibold">Patient</label>
                            <input type="text" name="pet_name" id="updatePetName"
                                class="w-44 border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                                required>
                        </div>
                        <div class="mb-4 w-auto">
                            <label class="block text-gray-700 mb-1 text-sm font-semibold">Date</label>
                            <input type="date" name="visit_date" id="updateVisitDate"
                                class="w-44 border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                                required>
                        </div>
                        <div class="flex flex-record space-x-2">
                            <div class="mb-4 w-auto">
                                <label for="" class="block text-gray-700 mb-1 text-sm font-semibold">Visit Type</label>
                                <select name="visit_type" id="updateVisitType"
                                    class="w-auto border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                                    <option value="" selected disabled>Select Type</option>
                                    <option value="Routine Checkup">Routine Checkup</option>
                                    <option value="Vaccination">Vaccination</option>
                                    <option value="Treatment">Treatment</option>
                                    <option value="Emergency">Emergency</option>
                                    <option value="Surgery">Surgery</option>
                                </select>
                            </div>
                            <div class="mb-4 w-auto">
                                <label for="" class="block text-gray-700 mb-1 text-sm font-semibold">Weight</label>
                                <input type="text" id="updateWeight"
                                    class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                                    name="weight" placeholder="e.g, 20lbs" required>
                            </div>
                            <div class="mb-4 w-auto">
                                <label for="" class="block text-gray-700 mb-1 text-sm font-semibold">Temperature</label>
                                <input type="text" id="updateTemperature"
                                    class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                                    name="temperature" placeholder="e.g, 36°C" required>
                            </div>
                        </div>
                        <div class="mb-4 w-full">
                            <label for="" class="block text-gray-700 mb-1 text-sm font-semibold">Diagnosis</label>
                            <input type="text" id="updateDiagnosis"
                                class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                                name="diagnosis" placeholder="Primary diagnosis or reason for visit" required>
                        </div>
                        <div class="mb-4 w-full">
                            <label for="" class="block text-gray-700 mb-1 text-sm font-semibold">Treatment</label>
                            <textarea name="treatment" id="updateTreatment"
                                class="w-full border px-2 py-1 resize-none rounded text-sm focus:outline-none focus:ring-2 focus:ring-green-500 none"
                                placeholder="Describe the treatment of procedures performed"></textarea>
                        </div>
                        <div class="mb-4 w-full">
                            <label for="" class="block text-gray-700 mb-1 text-sm font-semibold">Medications</label>
                            <textarea name="medications" id="updateMedications"
                                class="w-full border px-2 py-1 resize-none rounded text-sm focus:outline-none focus:ring-2 focus:ring-green-500 none"
                                placeholder="List the medications prescribed with dosage and frequency"></textarea>
                        </div>
                        <div class="mb-4 w-full">
                            <label for="" class="block text-gray-700 mb-1 text-sm font-semibold">Additional
                                Notes</label>
                            <textarea name="notes" id="updateNotes"
                                class="w-full border px-2 py-1 resize-none rounded text-sm focus:outline-none focus:ring-2 focus:ring-green-500 none"
                                placeholder="Add additional observations, instructions, or notes"></textarea>
                        </div>

                        <div class="mb-4 w-full">
                            <label for="follow_up_date" class="block text-gray-700 mb-1 text-sm font-semibold">Follow-Up
                                Date</label>
                            <input type="date" id="updateFollowUpDate" name="follow_up_date"
                                class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                        </div>

                        <div class="flex justify-end w-full">
                            <button type="btn"
                                class="close mr-2 px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-xs">
                                Cancel</button>
                            <button type="submit" name="submit"
                                class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-700 text-xs">Update
                                Record</button>
                        </div>
                    </form>
                </div>
            </div>
            <div id="deleteModal"
                class="modal hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                <div class="bg-white rounded-lg p-6 max-w-md w-full">
                    <div class="flex flex-record items-center mb-4">
                        <i class="fa-solid fa-circle-exclamation mr-2" style="color: #c00707;"></i>
                        <h3 class="font-semibold text-lg">Delete Medical Record</h3>
                    </div>
                    <p id="deleteMessage" class="mb-4 text-sm text-gray-600">
                        Are you sure you want to delete this medical record?
                    </p>
                    <div class="flex justify-end space-x-2">
                        <button class="close px-3 py-2 bg-gray-300 rounded hover:bg-gray-400 text-xs">Cancel</button>
                        <a id="confirmDeleteBtn" href="#"
                            class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700 text-xs">
                            Delete
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const medicalDetails = document.getElementById('medicalDetails');
            const followUpDetail = document.getElementById('followUpDetail');
            const diagnosisDetails = document.getElementById('diagnosisDetails');
            const treatmentDetails = document.getElementById('treatmentDetails');
            const medicationsDetails = document.getElementById('medicationsDetails');
            const notesDetails = document.getElementById('notesDetails');
            const petName = document.getElementById('petName');
            const recordDate = document.getElementById('recordDate');

            const addField = (container, label, value) => {
                const p = document.createElement("p");
                p.innerHTML = `<span class="font-semibold">${label}</span> ${value || "N/A"}`;
                container.appendChild(p);
            };

            // Modal Open/Close
            document.querySelectorAll(".open-modal").forEach(btn => {
                btn.addEventListener("click", () => {
                    const modalId = btn.dataset.modal;
                    document.getElementById(modalId).classList.remove("hidden");
                    updateBodyScroll();

                    medicalDetails.innerHTML = "<h3 class='font-semibold mb-4'><i class='fa-solid fa-stethoscope mr-2 text-green-600'></i>Visit information</h3>";
                    followUpDetail.innerHTML = "<h4 class='font-semibold mb-4'><i class='fa-solid fa-calendar mr-2 text-green-600'></i>Follow-up date</h4>";
                    diagnosisDetails.innerHTML = "<h4 class='font-semibold mb-4'><i class='fa-solid fa-notes-medical mr-2 text-green-600'></i>Diagnosis</h4>";
                    treatmentDetails.innerHTML = "<h4 class='font-semibold mb-4'><i class='fa-solid fa-syringe mr-2 text-green-600'></i>Treatment</h4>";
                    medicationsDetails.innerHTML = "<h4 class='font-semibold mb-4'><i class='fa-solid fa-pills mr-2 text-green-600'></i>Medications</h4>";
                    notesDetails.innerHTML = "<h4 class='font-semibold mb-4'><i class='fa-solid fa-comment mr-2 text-green-600'></i>Notes</h4>";
                    petName.textContent = btn.dataset.patient;
                    recordDate.textContent = btn.dataset.date;


                    addField(medicalDetails, "Date:", btn.dataset.date);
                    const visitTypeHTML = `
                            <div class="${btn.dataset.typeBg} ${btn.dataset.typeColor} rounded-lg px-2 py-1 inline-flex items-center">
                                <span class="text-xs">${btn.dataset.type}</span>
                            </div>
                        `;

                    medicalDetails.insertAdjacentHTML('beforeend', `
                                <div>
                                    <span class="font-semibold">Visit Type:</span>
                                    ${visitTypeHTML}
                                </div>
                            `);

                    addField(medicalDetails, "Patient:", btn.dataset.patient);
                    addField(medicalDetails, "Weight:", btn.dataset.weight);
                    addField(medicalDetails, "Temperature:", btn.dataset.temperature);
                    addField(medicalDetails, "Owner:", btn.dataset.owner);

                    addField(followUpDetail, "", btn.dataset.follow);

                    addField(diagnosisDetails, "", btn.dataset.diagnosis);

                    addField(treatmentDetails, "", btn.dataset.treatment);

                    addField(medicationsDetails, "", btn.dataset.medications);

                    addField(notesDetails, "", btn.dataset.notes);
                });
            });

            document.querySelectorAll(".modal .close").forEach(closeBtn => {
                closeBtn.addEventListener("click", () => {
                    const modal = closeBtn.closest(".modal");
                    modal.classList.add("hidden");
                    updateBodyScroll();
                    const form = modal.querySelector("form");
                    if (form) form.reset();
                });
            });

            document.querySelectorAll(".modal").forEach(modal => {
                modal.addEventListener("click", e => {
                    if (e.target === modal) modal.classList.add("hidden");
                });
            });

            // Follow-up checkbox logic
            const followUpCheckbox = document.getElementById('followUpRequired');
            const followUpDate = document.getElementById('follow_up_date');

            if (followUpCheckbox) {
                followUpCheckbox.addEventListener('change', () => {
                    if (followUpCheckbox.checked) {
                        followUpDate.removeAttribute('disabled');
                        followUpDate.setAttribute('required', 'true');
                    } else {
                        followUpDate.setAttribute('disabled', 'true');
                        followUpDate.removeAttribute('required');
                        followUpDate.value = '';
                    }
                });

                // Initially disable date
                followUpDate.setAttribute('disabled', 'true');
            }

            // Search functionality
            const tableBody = document.getElementById("recordsBody");
            const records = tableBody.querySelectorAll("tr:not(#noResults)");
            const noResults = document.getElementById("noResults");
            const searchInput = document.getElementById("search");

            searchInput.addEventListener("input", function () {
                const term = this.value.toLowerCase();
                let hasResults = false;

                records.forEach(record => {
                    const text = record.textContent.toLowerCase();
                    if (text.includes(term)) {
                        record.style.display = "";
                        hasResults = true;
                    } else {
                        record.style.display = "none";
                    }
                });

                noResults.style.display = hasResults ? "none" : "";
            });
        });


        document.querySelectorAll(".open-delete-modal").forEach(btn => {
            btn.addEventListener("click", () => {
                const modal = document.getElementById("deleteModal");
                const medicalRecordid = btn.dataset.id;

                document.getElementById("deleteMessage").textContent =
                    `Are you sure you want to delete this medical record? This action cannot be undone.`;
                document.getElementById("confirmDeleteBtn").href = `medical-records.php?delete_id=${medicalRecordid}`;

                modal.classList.remove("hidden");
                updateBodyScroll();
            });
        });
        
        document.querySelectorAll(".open-edit-modal").forEach(btn => {
            btn.addEventListener("click", () => {
                
            })
        })

    </script>
</body>

</html>