<?php
/**
 * Database Setup Script for StudySync
 * Run this script to set up the database
 */

require_once __DIR__ . '/config/Database.php';

$db = new Database();
$conn = $db->connect();

if ($conn) {
    echo "Connected to MySQL successfully.<br>";

    // Read the SQL file
    $sql = file_get_contents(__DIR__ . '/database/database.sql');

    // Split into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $statement) {
        if (!empty($statement)) {
            if ($conn->query($statement) === TRUE) {
                echo "Executed: " . substr($statement, 0, 50) . "...<br>";
            } else {
                echo "Error executing: " . $conn->error . "<br>";
            }
        }
    }

    echo "Database setup complete.";
} else {
    echo "Failed to connect to MySQL. Please ensure MySQL server is running and credentials are correct.";
}
?>