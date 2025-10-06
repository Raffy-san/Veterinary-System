<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/config.php'; // DB connection

use Dompdf\Dompdf;
use Dompdf\Options;

// Get death record ID
$death_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($death_id <= 0) {
  die("Invalid certificate request.");
}

// Fetch data from database
$stmt = $pdo->prepare("
    SELECT 
        dr.certificate_number,
        dr.date_of_death,
        dr.time_of_death,
        dr.cause_of_death,
        dr.location_of_death,
        dr.recorded_by,
        dr.certificate_date,
        p.name AS pet_name,
        p.species,
        p.breed,
        p.gender,
        p.color,
        p.age,
        p.age_unit,
        o.name AS owner_name
    FROM death_records dr
    LEFT JOIN pets p ON dr.pet_id = p.id
    LEFT JOIN owners o ON p.owner_id = o.id
    WHERE dr.id = ?
");
$stmt->execute([$death_id]);
$death = $stmt->fetch(PDO::FETCH_ASSOC);

  $timeOfDeath = '';
  if (!empty($death['time_of_death'])) {
    $t = DateTime::createFromFormat('H:i:s', $death['time_of_death']);
    if ($t) {
      $timeOfDeath = $t->format('h:i A');
    }
  }

if (!$death) {
  die("Death record not found.");
}

// Dompdf setup
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

// Format HTML (using HEREDOC for cleaner variable embedding)
$html = <<<HTML
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Pet Death Certificate</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 40px; }
    .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; }
    .certificate-number { text-align: right; font-weight: bold; margin-top: -20px; }
    .section { margin-top: 20px; }
    .label { font-weight: bold; }
    .footer { margin-top: 60px; text-align: center; }
  </style>
</head>
<body>
  <div class="header">
    <h2>Veterinary Clinic</h2>
    <h3>Pet Death Certificate</h3>
    <div class="certificate-number">Certificate No: {$death['certificate_number']}</div>
  </div>

  <div class="section">
    <p><span class="label">Pet Name:</span> {$death['pet_name']}</p>
    <p><span class="label">Species / Breed:</span> {$death['species']} - {$death['breed']}</p>
    <p><span class="label">Gender:</span> {$death['gender']}</p>
    <p><span class="label">Color:</span> {$death['color']}</p>
    <p><span class="label">Age:</span> {$death['age']} {$death['age_unit']}</p>
  </div>

  <div class="section">
    <p><span class="label">Owner Name:</span> {$death['owner_name']}</p>
    <p><span class="label">Date of Death:</span> {$death['date_of_death']}</p>
    <p><span class="label">Time of Death:</span> {$timeOfDeath}</p>
    <p><span class="label">Cause of Death:</span> {$death['cause_of_death']}</p>
    <p><span class="label">Location of Death:</span> {$death['location_of_death']}</p>
  </div>

  <div class="section">
    <p><span class="label">Issued By:</span> Dr. {$death['recorded_by']}</p>
    <p><span class="label">Date Issued:</span> {$death['certificate_date']}</p>
  </div>

  <div class="footer">
    <p>__________________________</p>
    <p>Authorized Veterinarian Signature</p>
  </div>
</body>
</html>
HTML;

// Load and render
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Output to browser
$filename = "death_certificate_" . preg_replace('/\s+/', '_', strtolower($death['pet_name'])) . ".pdf";
$dompdf->stream($filename, ["Attachment" => false]);
