<?php
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/config.php'; // DB connection

use Dompdf\Dompdf;
use Dompdf\Options;

// Setup options (optional but useful)
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true); // allow loading of external images

$dompdf = new Dompdf($options);

// HTML content
$html = '
<html>
<head>
  <style>
    body { font-family: Arial, sans-serif; }
    h2 { color: black; text-align: center}
    p { font-size: 14px; }
  </style>
</head>
<body>
  <h1>MEDICAL RECORD</h1>
  <p>Customer: John Doe</p>
  <p>Total: <strong>$123.45</strong></p>
</body>
</html>
';

// Load HTML into dompdf
$dompdf->loadHtml($html);

// Set paper size and orientation
$dompdf->setPaper('A4', 'portrait');

// Render HTML to PDF
$dompdf->render();

// Stream PDF to browser
$dompdf->stream("receipt.pdf", ["Attachment" => false]);
// "false" = open in browser, "true" = force download
