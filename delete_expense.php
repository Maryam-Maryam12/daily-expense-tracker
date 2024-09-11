<?php
session_start();
include('connection.php');

if (isset($_SESSION['signup_id'])) {
    $signup_id = $_SESSION['signup_id'];


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_id'])) {
    $addId = $_POST['add_id'];

    $deleteQuery = "DELETE FROM add_expense WHERE add_id = $addId";

    if ($connection->query($deleteQuery) === TRUE) {
        $response['success'] = 'Expense deleted successfully!';
    } else {
        $response['error'] = "Error deleting record: " . $connection->error;
    }

    echo json_encode($response);
} else {
    // Handle invalid request
    header('HTTP/1.1 400 Bad Request');
    exit();
}
}
?>

