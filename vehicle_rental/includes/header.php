<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Vehicle Rental</title>
    <link rel="stylesheet" href="/vehicle_rental/assets/css/style.css">
    <style>
        .btn {
            display: inline-block;
            padding: 6px 12px;
            margin: 2px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .btn-edit {
            background-color: #3498db;
            color: white;
        }
        .btn-edit:hover {
            background-color: #217dbb;
        }

        .btn-delete {
            background-color: #e74c3c;
            color: white;
        }
        .btn-delete:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
<header>
    <h1>Vehicle Rental Management</h1>
    <nav>
        <a href="/vehicle_rental/public/index.php">Home</a>
        <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="/vehicle_rental/public/add.php">Add Vehicle</a>
            <a href="/vehicle_rental/public/bookings.php">View Bookings</a>
        <?php endif; ?>
        <a href="/vehicle_rental/public/search.php">Search</a>
        <a href="/vehicle_rental/public/book.php">Book</a>
        <?php if (!empty($_SESSION['user_id'])): ?>
            <span>Welcome, <?php echo e($_SESSION['username']); ?></span>
            <a href="/vehicle_rental/public/logout.php">Logout</a>
        <?php else: ?>
            <a href="/vehicle_rental/public/login.php">Login</a>
        <?php endif; ?>
        <a href="/vehicle_rental/public/register.php">Register</a>
    </nav>
</header>
<main>