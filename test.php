<?php
// test.php

// Include the config file
include 'config/config.php';

// Sample query to fetch data from a table
$sql = "SELECT * FROM pets";
$result = $conn->query($sql);

// Check if there are results
if ($result->num_rows > 0) {
    // Output data for each row
    while ($row = $result->fetch_assoc()) {
        echo "Pet ID: " . $row["id"] . " - Name: " . $row["name"] . "<br>";
    }
} else {
    echo "No pets found.";
}

// Close the database connection
$conn->close();
?>