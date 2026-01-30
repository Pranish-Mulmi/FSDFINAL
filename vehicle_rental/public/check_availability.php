<?php
require_once __DIR__ . '/../config/db.php';
header('Content-Type: application/json');

$vehicle_id = (int)($_GET['vehicle_id'] ?? 0);
$start_date = trim($_GET['start_date'] ?? '');
$end_date   = trim($_GET['end_date'] ?? '');

$response = ['ok' => false, 'message' => 'Missing or invalid parameters.'];

if ($vehicle_id > 0 && $start_date !== '' && $end_date !== '') {
    $s = strtotime($start_date);
    $e = strtotime($end_date);
    if ($s && $e && $s <= $e) {
        $q = "SELECT COUNT(*) FROM bookings WHERE vehicle_id = ? AND start_date <= ? AND end_date >= ?";
        $st = $pdo->prepare($q);
        $st->execute([$vehicle_id, $end_date, $start_date]);
        $conflict = (int)$st->fetchColumn();
        if ($conflict > 0) {
            $response = ['ok' => true, 'available' => false, 'message' => 'Not available'];
        } else {
            $response = ['ok' => true, 'available' => true, 'message' => 'Available'];
        }
    } else {
        $response = ['ok' => false, 'message' => 'Invalid date range.'];
    }
}

echo json_encode($response);