<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/config.php'; // DB connection

use Dompdf\Dompdf;
use Dompdf\Options;

$death_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($death_id <= 0) {
  die("Invalid certificate request.");
}

// Fetch death record details
$stmt = $pdo->prepare("
    SELECT 
        c.certificate_number,
        dr.date_of_death,
        dr.time_of_death,
        dr.cause_of_death,
        dr.location_of_death,
        dr.recorded_by,
        c.certificate_date,
        p.name AS pet_name,
        p.species,
        p.breed,
        p.gender,
        p.color,
        p.age,
        p.age_unit,
        o.name AS owner_name,
        o.phone,
        o.email,
        o.address
    FROM death_records dr
    LEFT JOIN pets p ON dr.pet_id = p.id
    LEFT JOIN certificates c ON c.death_record_id = dr.id
    LEFT JOIN owners o ON p.owner_id = o.id
    WHERE dr.id = ?
");
$stmt->execute([$death_id]);
$death = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$death) {
  die("Death record not found.");
}

// Format time of death
$timeOfDeath = '';
if (!empty($death['time_of_death'])) {
  $t = DateTime::createFromFormat('H:i:s', $death['time_of_death']);
  if ($t) {
    $timeOfDeath = $t->format('h:i A');
  }
}

// Format issued date
$issued_date = date('F d, Y', strtotime($death['certificate_date']));
$date_of_death = date('F d, Y', strtotime($death['date_of_death']));

// Setup Dompdf
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// Build HTML
$html = "
<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Pet Death Certificate</title>
</head>

<body style='font-family: Arial, Helvetica, sans-serif; margin: 20px;'>
    <header>
        <h2 style='text-align: center; color: green; margin-bottom: 10px;'>Pet Death Certificate</h2>
        <table style='width: 100%; border: none;'>
            <tr>
                <td style='text-align: left;'>
                    <h4>Certificate No: {$death['certificate_number']}</h4>
                </td>
                <td style='text-align: right;'>
                    <h4>Date Issued: {$issued_date}</h4>
                </td>
            </tr>
        </table>
    </header>

    <p style='text-align: justify; margin: 10px 0; font-size: 14px;'>
    This is to certify that the following pet has been reported deceased and recorded at
    <strong>Southern Leyte Veterinary Clinic</strong>. The details below accurately reflect the
    date, time, and circumstances of death as documented by the attending veterinarian.
    </p>


    <section style='margin-bottom: 10px;'>
        <div style='border: 1px solid black; background-color: rgb(230,230,230); text-align: center; padding: 5px;'>
            <strong>PET INFORMATION</strong>
        </div>

        <table style='width: 100%; border-collapse: collapse; font-size: 14px; margin-top: 5px;'>
            <tr>
                <th style='text-align: left; border: 1px solid black; padding: 4px; width: 25%;'>Pet Name</th>
                <td style='border: 1px solid black; padding: 4px;'>{$death['pet_name']}</td>
                <th style='text-align: left; border: 1px solid black; padding: 4px; width: 25%;'>Gender</th>
                <td style='border: 1px solid black; padding: 4px;'>{$death['gender']}</td>
            </tr>
            <tr>
                <th style='text-align: left; border: 1px solid black; padding: 4px;'>Species</th>
                <td style='border: 1px solid black; padding: 4px;'>{$death['species']}</td>
                <th style='text-align: left; border: 1px solid black; padding: 4px;'>Breed</th>
                <td style='border: 1px solid black; padding: 4px;'>{$death['breed']}</td>
            </tr>
            <tr>
                <th style='text-align: left; border: 1px solid black; padding: 4px;'>Color</th>
                <td style='border: 1px solid black; padding: 4px;'>{$death['color']}</td>
                <th style='text-align: left; border: 1px solid black; padding: 4px;'>Age</th>
                <td style='border: 1px solid black; padding: 4px;'>{$death['age']} {$death['age_unit']}</td>
            </tr>
        </table>
    </section>

    <section style='margin-bottom: 10px;'>
        <div style='border: 1px solid black; background-color: rgb(230,230,230); text-align: center; padding: 5px;'>
            <strong>OWNER INFORMATION</strong>
        </div>

        <table style='width: 100%; border-collapse: collapse; font-size: 14px; margin-top: 5px;'>
            <tr>
                <th style='text-align: left; border: 1px solid black; padding: 4px; width: 25%;'>Owner Name</th>
                <td colspan='3' style='border: 1px solid black; padding: 4px;'>{$death['owner_name']}</td>
            </tr>
            <tr>
                <th style='text-align: left; border: 1px solid black; padding: 4px; width: 25%;'>Address</th>
                <td colspan='3' style='border: 1px solid black; padding: 4px;'>{$death['address']}</td>
            </tr>
            <tr>
                <th style='text-align: left; border: 1px solid black; padding: 4px;'>Phone</th>
                <td style='border: 1px solid black; padding: 4px;'>{$death['phone']}</td>
                <th style='text-align: left; border: 1px solid black; padding: 4px;'>Email</th>
                <td style='border: 1px solid black; padding: 4px;'>{$death['email']}</td>
            </tr>

        </table>
    </section>

    <section style='margin-bottom: 10px;'>
        <div style='border: 1px solid black; background-color: rgb(230,230,230); text-align: center; padding: 5px;'>
            <strong>DEATH DETAILS</strong>
        </div>

        <table style='width: 100%; border-collapse: collapse; font-size: 14px; margin-top: 5px;'>
            <tr>
                <th style='text-align: left; border: 1px solid black; padding: 4px;'>Date of Death</th>
                <td style='border: 1px solid black; padding: 4px;'>{$date_of_death}</td>
                <th style='text-align: left; border: 1px solid black; padding: 4px;'>Time of Death</th>
                <td style='border: 1px solid black; padding: 4px;'>{$timeOfDeath}</td>
            </tr>
            <tr>
                <th style='text-align: left; border: 1px solid black; padding: 4px;'>Cause of Death</th>
                <td colspan='3' style='border: 1px solid black; padding: 4px;'>{$death['cause_of_death']}</td>
            </tr>
            <tr>
                <th style='text-align: left; border: 1px solid black; padding: 4px;'>Location of Death</th>
                <td colspan='3' style='border: 1px solid black; padding: 4px;'>{$death['location_of_death']}</td>
            </tr>
        </table>
    </section>

    <section style='margin-bottom: 10px;'>
        <div style='border: 1px solid black; background-color: rgb(230,230,230); text-align: center; padding: 5px;'>
            <strong>CERTIFICATE DETAILS</strong>
        </div>

        <table style='width: 100%; border-collapse: collapse; font-size: 14px; margin-top: 5px;'>
            <tr>
                <th style='text-align: left; border: 1px solid black; padding: 4px; width: 25%;'>Issued By</th>
                <td colspan='3' style='border: 1px solid black; padding: 4px;'>Dr. {$death['recorded_by']}</td>
            </tr>
        </table>
    </section>

    <p style='margin-top: 20px; text-align: justify; font-size: 14px;'>
    This certificate is issued to officially record the death of the pet named 
    <strong>{$death['pet_name']}</strong>. The information provided herein is based on
    the documentation submitted and verified by the attending veterinarian at the
    <strong>Southern Leyte Veterinary Clinic</strong>.
    </p>


    <p style='margin-top: 40px; text-align: right;'>
        <strong>Authorized Veterinarian:</strong> ___________________________
    </p>

    <footer style='position: fixed; bottom: 20px; right: 10px; text-align: center; font-size: 12px; width: 100%;'>
      <p>Southern Leyte Veterinary Clinic • Official Pet Death Certificate • This document is valid without a signature if digitally issued.</p>
    </footer>

</body>

</html>";

// Load into Dompdf
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Output
$filename = strtolower(str_replace(' ', '_', $death['pet_name'])) . "_death_certificate.pdf";
$dompdf->stream($filename, ['Attachment' => false]);
