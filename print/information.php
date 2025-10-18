<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/config.php'; // DB connection

use Dompdf\Dompdf;
use Dompdf\Options;

$owner_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($owner_id <= 0) {
    die("Invalid owner information request.");
}

// Fetch owner details
$stmt = $pdo->prepare("
    SELECT 
        o.name AS owner_name,
        o.phone,
        o.email,
        o.emergency,
        o.address
    FROM owners o
    WHERE o.id = ?
");
$stmt->execute([$owner_id]);
$owner = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$owner) {
    die("Owner record not found.");
}

// Fetch all pets for this owner
$stmt = $pdo->prepare("
    SELECT 
        p.name AS pet_name,
        p.species,
        p.breed,
        p.gender,
        p.color,
        p.age,
        p.age_unit,
        p.weight,
        p.weight_unit,
        p.birth_date
    FROM pets p
    WHERE p.owner_id = ?
");
$stmt->execute([$owner_id]);
$pets = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$pets) {
    die("No pets found for this owner.");
}

$logoPath = 'file://' . realpath(__DIR__ . '/../assets/img/logo.webp');

// Setup Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('chroot', realpath(__DIR__ . '/..'));
$dompdf = new Dompdf($options);

// Build HTML
$html = "
<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Owner and Pet Information</title>
</head>

<body style='font-family: Arial, Helvetica, sans-serif; margin: 18px;'>

    <header style='text-align: center; margin-bottom: 8px;'>
        <img src='{$logoPath}' alt='Clinic Logo' width='55' style='margin-bottom: 4px;'>
        <h2 style='color: green; margin: 0; font-size: 18px;'>SOUTHERN LEYTE VETERINARY CLINIC</h2>
        <p style='margin: 0; font-size: 14px;'>Maasin City, Southern Leyte</p>
        <h3 style='margin-top: 8px; font-size: 15px;'>OWNER AND PET INFORMATION</h3>
    </header>

    <p style='text-align: justify; margin: 10px 0; font-size: 14px;'>
        This document contains verified details of the pet(s) and their owner as recorded in the official database
        of <strong>Southern Leyte Veterinary Clinic</strong>. The information provided below is accurate as of the date of issuance.
    </p>

    <section style='margin-bottom: 10px;'>
        <div style='border: 1px solid black; background-color: rgb(230,230,230); text-align: center; padding: 5px; font-size: 14px;'>
            <strong>OWNER INFORMATION</strong>
        </div>

        <table style='width: 100%; border-collapse: collapse; font-size: 14px; margin-top: 5px;'>
            <tr>
                <th style='text-align: left; border: 1px solid black; padding: 4px; width: 25%;'>Owner Name</th>
                <td colspan='3' style='border: 1px solid black; padding: 4px;'>{$owner['owner_name']}</td>
            </tr>
            <tr>
                <th style='text-align: left; border: 1px solid black; padding: 4px;'>Address</th>
                <td colspan='3' style='border: 1px solid black; padding: 4px;'>{$owner['address']}</td>
            </tr>
            <tr>
                <th style='text-align: left; border: 1px solid black; padding: 4px;'>Phone</th>
                <td style='border: 1px solid black; padding: 4px;'>{$owner['phone']}</td>
                <th style='text-align: left; border: 1px solid black; padding: 4px;'>Emergency Contact</th>
                <td style='border: 1px solid black; padding: 4px;'>{$owner['emergency']}</td>
            </tr>
              <tr>
                <th style='text-align: left; border: 1px solid black; padding: 4px;'>Email</th>
                <td colspan='3' style='border: 1px solid black; padding: 4px;'>{$owner['email']}</td>
            </tr>
        </table>
    </section>
";

// Loop through each pet and display their info
foreach ($pets as $index => $pet) {
    $date_of_birth = date('F d, Y', strtotime($pet['birth_date']));
    $html .= "
    <!-- PET INFORMATION -->
    <section style='margin-bottom: 10px; page-break-inside: avoid;'>
        <div style='border: 1px solid black; background-color: rgb(230,230,230); text-align: center; padding: 5px; font-size: 14px;'>
            <strong>PET INFORMATION " . (count($pets) > 1 ? '(' . ($index + 1) . ')' : '') . "</strong>
        </div>

        <table style='width: 100%; border-collapse: collapse; font-size: 14px; margin-top: 5px;'>
            <tr>
                <th style='text-align: left; border: 1px solid black; padding: 4px; width: 25%;'>Pet Name</th>
                <td style='border: 1px solid black; padding: 4px;'>{$pet['pet_name']}</td>
                <th style='text-align: left; border: 1px solid black; padding: 4px; width: 25%;'>Gender</th>
                <td style='border: 1px solid black; padding: 4px;'>{$pet['gender']}</td>
            </tr>
            <tr>
                <th style='text-align: left; border: 1px solid black; padding: 4px;'>Species</th>
                <td style='border: 1px solid black; padding: 4px;'>{$pet['species']}</td>
                <th style='text-align: left; border: 1px solid black; padding: 4px;'>Breed</th>
                <td style='border: 1px solid black; padding: 4px;'>{$pet['breed']}</td>
            </tr>
            <tr>
                <th style='text-align: left; border: 1px solid black; padding: 4px;'>Color</th>
                <td style='border: 1px solid black; padding: 4px;'>{$pet['color']}</td>
                <th style='text-align: left; border: 1px solid black; padding: 4px;'>Age</th>
                <td style='border: 1px solid black; padding: 4px;'>{$pet['age']} {$pet['age_unit']} old</td>
            </tr>
             <tr>
                <th style='text-align: left; border: 1px solid black; padding: 4px;'>Weight</th>
                <td style='border: 1px solid black; padding: 4px;'>{$pet['weight']} {$pet['weight_unit']}</td>
                <th style='text-align: left; border: 1px solid black; padding: 4px;'>Birth Date</th>
                <td style='border: 1px solid black; padding: 4px;'>{$date_of_birth}</td>
            </tr>
        </table>
    </section>
    ";
}

// OWNER INFORMATION (shown once)
$html .= "

    <p style='margin-top: 25px; text-align: justify; font-size: 14px;'>
        Certified true and correct as per the clinic's official records.
    </p>

    <p style='margin-top: 40px; text-align: right; font-size: 14px;'>
        <strong>Authorized Veterinarian:</strong> ___________________________<br>
        <span style='font-size: 14px;'>Southern Leyte Veterinary Clinic</span>
    </p>

    <footer style='position: fixed; bottom: 10px; right: 10px; text-align: center; font-size: 12px; width: 100%;'>
      <p>Southern Leyte Veterinary Clinic • Official Owner and Pet Information Record</p>
    </footer>

</body>
</html>
";

// Render PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Output
$filename = strtolower(str_replace(' ', '_', $owner['owner_name'])) . "_Owner_Pet_Info.pdf";
$dompdf->stream($filename, ['Attachment' => false]);
