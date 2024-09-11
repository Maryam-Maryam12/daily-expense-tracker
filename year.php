<?php 
session_start();
include('connection.php');
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $startYear = $_POST["startYear"];
    $endYear = $_POST["endYear"];
    $sql = "SELECT add_id, item_date, item_name, item_cost FROM add_expense WHERE item_date BETWEEN '$startYear' AND '$endYear'";

    $result = $connection->query($sql);

    if ($result) {
        if ($result->num_rows > 0) {
            $records = array();

            while ($row = $result->fetch_assoc()) {
                $records[] = $row;
            }

            echo json_encode($records);
        } else {
            echo json_encode(array("message" => "No records found"));
        }
    } else {
        echo json_encode(array("error" => "Error: " . $connection->error));
    }

    $connection->close();
}
 ?>