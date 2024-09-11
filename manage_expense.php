<?php
session_start();
include('connection.php');

if (isset($_SESSION['signup_id'])) {
    $signup_id = $_SESSION['signup_id'];
    $res_arr = array(); 
    $selectquery = "SELECT * FROM add_expense WHERE signup_id='$signup_id'";
    $result = $connection->query($selectquery);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $res_arr[] = $row;
        }
        echo json_encode($res_arr);

    } else {
        echo json_encode(array('error' => 'No data found.'));
    }
}
?>



