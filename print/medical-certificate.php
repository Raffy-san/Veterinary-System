<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/config.php'; // DB connection

use Dompdf\Dompdf;
use Dompdf\Options;

$record_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($record_id > 0) {
    // Fetch the medical record + join with pet + owner
    $stmt = $pdo->prepare("
        SELECT 
            m.visit_date,
            c.certificate_number,
            c.certificate_date,
            m.visit_type,
            m.visit_time,
            m.diagnosis,
            m.medications,
            m.treatment,
            m.notes,
            m.veterinarian,
            m.temperature,
            m.temp_unit,
            m.weight AS record_weight,
            m.weight_unit AS record_weight_unit,
            m.follow_up_date,
            p.name AS pet_name,
            p.species,
            p.breed,
            p.color,
            p.birth_date,
            p.weight AS pet_weight,
            p.weight_unit AS pet_weight_unit,
            o.name AS owner_name,
            o.email,
            o.emergency,
            o.phone
        FROM medical_records m
        INNER JOIN pets p ON m.pet_id = p.id
        INNER JOIN certificates c ON m.id = c.record_id
        INNER JOIN owners o ON p.owner_id = o.id
        WHERE m.id = ?
    ");
    $stmt->execute([$record_id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$record) {
        die('Medical record not found!');
    }

    $record['visit_date'] = date('F j, Y', strtotime($record['visit_date']));
    $record['birth_date'] = date('F j, Y', strtotime($record['birth_date']));

    if (!empty($record['follow_up_date']) && $record['follow_up_date'] !== '0000-00-00') {
        $followUp = date('F j, Y', strtotime($record['follow_up_date']));
    } else {
        $followUp = 'No follow-up date';
    }

    $visitTimeFormatted = '';
    if (!empty($record['visit_time'])) {
        $t = DateTime::createFromFormat('H:i:s', $record['visit_time']);
        if ($t) {
            $visitTimeFormatted = $t->format('h:i A');
        }
    }

    $issued_date = date('F d, Y', strtotime($record['certificate_date']));
    // Output: October 06, 2025

    $record['diagnosis'] = !empty($record['diagnosis']) ? $record['diagnosis'] : 'No diagnosis provided';
    $record['treatment'] = !empty($record['treatment']) ? $record['treatment'] : 'No treatment provided';
    $record['medications'] = !empty($record['medications']) ? $record['medications'] : 'No medications provided';
    $record['notes'] = !empty($record['notes']) ? $record['notes'] : 'No additional notes';


    $logoPath = 'file://' . realpath(__DIR__ . '/../assets/img/green-paw.png');

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
    <title>Medical Record</title>
</head>

<body style='font-family: Arial, Helvetica, sans-serif; margin: 18px;'>
    <header style='text-align: center; margin-bottom: 8px;'>
    <img src='{$logoPath}' alt='Clinic Logo' width='55' style='margin-bottom: 4px;'>
    <h2 style='color: green; margin: 0; font-size: 18px;'>SOUTHERN LEYTE VETERINARY CLINIC</h2>
    <p style='margin: 0; font-size: 14px;'>Maasin City, Southern Leyte</p>
    <h3 style='margin-top: 8px; font-size: 15px;'>OFFICIAL MEDICAL RECORD CERTIFICATE</h3>
       <table style='width: 100%; border: none;'>
            <tr>
                <td style='text-align: left;'>
                <h4 style='font-size: 14px; margin: 5px 0;'>Certificate No: {$record['certificate_number']}</h4>
                </td>
                <td style='text-align: right;'>
                <h4 style='font-size: 14px; margin: 5px 0;'>Date Issued: {$issued_date}</h4>
                </td>
            </tr>
            </table>
    </header>

    <p style='text-align: justify; margin: 8px 0; font-size: 14px;'>
    This is to certify that the following pet has been examined and treated at
    <strong>Southern Leyte Veterinary Clinic</strong>. The information below represents
    an accurate record of the medical findings, diagnosis, and treatment
    administered by the attending veterinarian on the stated date.
    </p>

    <section style='margin-bottom: 8px;'>
        <div style='border: 1px solid black; background-color: rgb(230,230,230); text-align: center; padding: 4px; font-size: 14px;'>
            <strong>PET INFORMATION</strong>
        </div>

        <table style='width: 100%; border-collapse: collapse; font-size: 14px; margin-top: 4px;'>
            <tr>
                <th style='text-align: left; border: 1px solid black; padding: 3px; width: 20%;'>Pet Name</th>
                <td style='border: 1px solid black; padding: 3px;'>{$record['pet_name']}</td>
                <th style='text-align: left; border: 1px solid black; padding: 3px; width: 20%;'>Date of Birth</th>
                <td style='border: 1px solid black; padding: 3px;'>{$record['birth_date']}</td>
            </tr>
            <tr>
                <th style='text-align: left; border: 1px solid black; padding: 3px;'>Species</th>
                <td style='border: 1px solid black; padding: 3px;'>{$record['species']}</td>
                <th style='text-align: left; border: 1px solid black; padding: 3px;'>Breed</th>
                <td style='border: 1px solid black; padding: 3px;'>{$record['breed']}</td>
            </tr>
            <tr>
                <th style='text-align: left; border: 1px solid black; padding: 3px;'>Color</th>
                <td style='border: 1px solid black; padding: 3px;'>{$record['color']}</td>
                <th style='text-align: left; border: 1px solid black; padding: 3px;'>Weight</th>
                <td style='border: 1px solid black; padding: 3px;'>{$record['pet_weight']} {$record['pet_weight_unit']}</td>
            </tr>
        </table>
    </section>
    <section style='margin-bottom: 8px;'>
        <div style='border: 1px solid black; background-color: rgb(230,230,230); text-align: center; padding: 4px; font-size: 14px;'>
            <strong>OWNER INFORMATION</strong>
        </div>

        <table style='width: 100%; border-collapse: collapse; font-size: 14px; margin-top: 4px;'>
            <tr>
                <th style='text-align: left; border: 1px solid black; padding: 3px; width: 20%;'>Owner Name</th>
                <td style='border: 1px solid black; padding: 3px;'>{$record['owner_name']}</td>
                <th style='text-align: left; border: 1px solid black; padding: 3px; width: 20%;'>Email Address</th>
                <td style='border: 1px solid black; padding: 3px;'>{$record['email']}</td>
            </tr>

            <tr>
                <th style='text-align: left; border: 1px solid black; padding: 3px; width: 20%;'>Phone Number</th>
                <td style='border: 1px solid black; padding: 3px;'>{$record['phone']}</td>
                <th style='text-align: left; border: 1px solid black; padding: 3px; width: 20%;'>Emergency Contact</th>
                <td style='border: 1px solid black; padding: 3px;'>{$record['emergency']}</td>
            </tr>
        </table>
    </section>
    <section style='margin-bottom: 8px;'>
        <div style='border: 1px solid black; background-color: rgb(230,230,230); text-align: center; padding: 4px; font-size: 14px;'>
            <strong>RECORDS INFORMATION</strong>
        </div>

        <table style='width: 100%; border-collapse: collapse; font-size: 14px; margin-top: 4px;'>
            <tr>
                <th style='text-align: left; border: 1px solid black; padding: 3px; width: 20%;'>Visit Date</th>
                <td style='border: 1px solid black; padding: 3px;'>{$record['visit_date']}</td>
                <th style='text-align: left; border: 1px solid black; padding: 3px; width: 20%;'>Visit Time</th>
                <td style='border: 1px solid black; padding: 3px;'>{$visitTimeFormatted}</td>
            </tr>

            <tr>
                <th style='text-align: left; border: 1px solid black; padding: 3px; width: 20%;'>Visit Type</th>
                <td style='border: 1px solid black; padding: 3px;'>{$record['visit_type']}</td>
                <th style='text-align: left; border: 1px solid black; padding: 3px; width: 20%;'>Veterinarian</th>
                <td style='border: 1px solid black; padding: 3px;'>{$record['veterinarian']}</td>
            </tr>

            <tr>
                <th style='text-align: left; border: 1px solid black; padding: 3px; width: 20%;'>Weight</th>
                <td style='border: 1px solid black; padding: 3px;'>{$record['record_weight']} {$record['record_weight_unit']}</td>
                <th style='text-align: left; border: 1px solid black; padding: 3px; width: 20%;'>Temperature</th>
                <td style='border: 1px solid black; padding: 3px;'>{$record['temperature']} {$record['temp_unit']}</td>
            </tr>
        </table>
    </section>

    <section style='margin-bottom: 8px;'>
        <div style='border: 1px solid black; background-color: rgb(230,230,230); text-align: center; padding: 4px; font-size: 14px;'>
            <strong>MEDICAL DETAILS</strong>
        </div>

        <table style='width: 100%; border-collapse: collapse; font-size: 14px; margin-top: 4px;'>
            <tr>
                <th style='text-align: left; border: 1px solid black; padding: 3px; width: 20%;'>Diagnosis</th>
                <td colspan='3' style='border: 1px solid black; padding: 3px;'>
                    {$record['diagnosis']}
                </td>
            </tr>

            <tr>
                <th style='text-align: left; border: 1px solid black; padding: 3px;'>Treatment</th>
                <td colspan='3' style='border: 1px solid black; padding: 3px;'>
                    {$record['treatment']}
                </td>
            </tr>

            <tr>
                <th style='text-align: left; border: 1px solid black; padding: 3px;'>Medications</th>
                <td colspan='3' style='border: 1px solid black; padding: 3px;'>
                    {$record['medications']}
                </td>
            </tr>

            <tr>
                <th style='text-align: left; border: 1px solid black; padding: 3px;'>Additional Notes</th>
                <td colspan='3' style='border: 1px solid black; padding: 3px;'>
                    {$record['notes']}
                </td>
            </tr>

            <tr>
                <th style='text-align: left; border: 1px solid black; padding: 3px;'>Follow-up Date</th>
                <td colspan='3' style='border: 1px solid black; padding: 3px;'>{$followUp}</td>
            </tr>
        </table>
    </section>

    <p style='margin-top: 20px; text-align: justify; font-size: 14px;'>
    I hereby certify that the above information is true and correct to the best of my knowledge
    and belief. This record serves as an official documentation of the medical examination,
    findings, and treatment provided to the pet stated herein.
    </p>


    <p style='margin-top: 20px; text-align: right; font-size: 14px;'>
        <strong>Authorized Veterinarian:</strong> ___________________________<br>
         <span style='font-size: 14px;'>{$record['veterinarian']}</span>
    </p>

    <footer style='position: fixed; bottom: 10px; right: 10px; text-align: center; font-size: 11px; width: 100%;'>
    <p>Southern Leyte Veterinary Clinic • Official Medical Record Certificate • This document is valid without a signature if digitally issued.</p>
    </footer>

</body>

</html>";

    // Load into Dompdf
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Output
    $dompdf->stream("{$record['pet_name']}_medical_record.pdf", ['Attachment' => false]);
}
