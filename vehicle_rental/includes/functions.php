<?php
function csrf_token() {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_validate($token) {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}

function e($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

function valid_date_range($start, $end) {
    $s = strtotime($start);
    $e = strtotime($end);
    return $s && $e && $s <= $e;
}