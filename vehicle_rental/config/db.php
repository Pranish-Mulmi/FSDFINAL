<?php
$DB_HOST = 'localhost';
$DB_NAME = 'vehicle_rental';
$DB_USER = 'root';
$DB_PASS = '';
// $DB_HOST = 'localhost';
// $DB_NAME = 'np03cs4a240016';
// $DB_USER = 'np03cs4a240016';
// $DB_PASS = 'sleTYUajQY';
$dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, $options);
} catch (Exception $e) {
    http_response_code(500);
    echo "Database connection error.";
    exit;
}