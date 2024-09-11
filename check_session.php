<?php
session_start();
$response = array();

if (isset($_SESSION['signup_id'])) {
    $response['redirect'] = 'dashboard.php';
} else {
    $response['redirect'] = 'login.php';
}
header('Content-Type: application/json');
echo json_encode($response);
exit();
?>


