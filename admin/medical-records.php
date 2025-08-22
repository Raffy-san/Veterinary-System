<?php
include_once '../config/config.php';
require_once '../functions/session.php';
require_once '../helpers/fetch.php';
require_once '../functions/crud.php';
SessionManager::requireLogin();

if (isset($_POST['submit'])) {
    $data = [
        'pet_id' => $_POST['patient'],
        'date' => $_POST['date'],
        'visit_type' => $_POST['visit_type'],
        'weight' => $_POST['weight'],
        'temperature' => $_POST['temperature'],
        'diagnosis' => $_POST['diagnosis'],
        'treatment' => $_POST['treatment'],
        'medications' => $_POST['medications'],
        'notes' => $_POST['notes']
    ];

    if (addMedicalRecord($pdo, $data)) {
        header("Location: medical-records.php?added=1");
        exit;
    } else {
        header("Location: medical-records.php?added=0");
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
                <form class="flex flex-wrap items-center justify-between" method="POST" action="">
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

                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo '<option value="' . htmlspecialchars($row['pet_id']) . '">'
                                        . htmlspecialchars($row['pet_name']) . ' (' . htmlspecialchars($row['owner_name']) .
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
                        <input type="date" name="date"
                            class="w-44 border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            required>
                    </div>
                    <div class="flex flex-row space-x-2">
                        <div class="mb-4 w-auto">
                            <label for="" class="block text-gray-700 mb-1 text-sm font-semibold">Visit Type</label>
                            <select name="visit_type"
                                class="w-auto border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                                <option value="" selected disabled>Select Type</option>
                                <option value="Routine Checkup">Routine Checkup</option>
                                <option value="vaccination">Vaccination</option>
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
                        <textarea name="" id=""
                            class="w-full border px-2 py-1 resize-none rounded text-sm focus:outline-none focus:ring-2 focus:ring-green-500 none"
                            placeholder="Describe the treatment of procedures performed"></textarea>
                    </div>
                    <div class="mb-4 w-full">
                        <label for="" class="block text-gray-700 mb-1 text-sm font-semibold">Medications</label>
                        <textarea name="" id=""
                            class="w-full border px-2 py-1 resize-none rounded text-sm focus:outline-none focus:ring-2 focus:ring-green-500 none"
                            placeholder="List the medications prescribed with dosage and frequency"></textarea>
                    </div>
                    <div class="mb-4 w-full">
                        <label for="" class="block text-gray-700 mb-1 text-sm font-semibold">Additional Notes</label>
                        <textarea name="" id=""
                            class="w-full border px-2 py-1 resize-none rounded text-sm focus:outline-none focus:ring-2 focus:ring-green-500 none"
                            placeholder="Add additional observations, instructions, or notes"></textarea>
                    </div>

                    <div class="mb-4 w-full">
                        <div class="flex flex-row space-x-2">
                            <input type="checkbox" name="required">
                            <label for="" class="text-sm font-semibold">Follow-up appointment required</label>
                        </div>
                        <label for="" class="block text-gray-700 mb-1 text-sm font-semibold">Follow-Up Date (if applicable)</label>
                        <input type="date" name="date"
                            class="w-full border rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                            required>
                    </div>

                    <div class="flex justify-end w-full">
                        <button type="button"
                            class="close mr-2 px-4 py-2 bg-gray-300 rounded hover:bg-gray-400 text-xs">Cancel</button>
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
        </section>
    </main>

    <script>
        document.querySelectorAll(".open-modal").forEach(button => {
            button.addEventListener("click", () => {
                const modalId = button.dataset.modal;

                document.getElementById(modalId).classList.remove("hidden");
                document.body.style.overflow = "hidden"; // Prevent background scroll
            });
        });

        // Close modal when clicking on .close
        document.querySelectorAll(".modal .close").forEach(closeBtn => {
            closeBtn.addEventListener("click", () => {
                const modal = closeBtn.closest(".modal");
                modal.classList.add("hidden");
                document.body.style.overflow = "auto";

                const form = modal.querySelector("form");
                if (form) form.reset();
            });
        });

        // Close modal when clicking outside content
        document.querySelectorAll(".modal").forEach(modal => {
            modal.addEventListener("click", e => {
                if (e.target === modal) modal.classList.add("hidden");
            });
        });

        document.getElementById('search').addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase();
            rows.forEach(row => {
                const name = row.cells[0].textContent.toLowerCase();
                const contact = row.cells[1].textContent.toLowerCase();
                row.style.display = (name.includes(searchTerm) || contact.includes(searchTerm)) ? '' : 'none';
            });
        });
    </script>
</body>

</html>