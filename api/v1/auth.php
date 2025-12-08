<?php
session_start();

// Session timeout: 10 minutes of inactivity
$timeout_duration = 10 * 60;

if (!isset($_SESSION['admin_authenticated'])) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Check if session has expired
if (isset($_SESSION['last_activity'])) {
    $elapsed_time = time() - $_SESSION['last_activity'];
    
    if ($elapsed_time > $timeout_duration) {
        session_destroy();
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Session expired. Please login again.']);
        exit;
    }
}

// Update last activity timestamp
$_SESSION['last_activity'] = time();
?>