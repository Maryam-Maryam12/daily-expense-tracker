<?php
session_start();
include ('connection.php');

if (isset($_SESSION['signup_id'])) {
    $signup_id = $_SESSION['signup_id'];

    $query = "SELECT * FROM signup WHERE signup_id = $signup_id";
    $result = $connection->query($query);

    if ($result) {
        if ($result->num_rows > 0) {
            $profile_data = $result->fetch_assoc();
            echo json_encode($profile_data);
        } else {
            echo json_encode(['error' => 'Profile data not found.']);
        }
    } else {
        echo json_encode(['error' => 'SQL error: ' . $connection->error]);
    }
} else {
    echo json_encode(['error' => 'User not logged in.']);
}
?>


