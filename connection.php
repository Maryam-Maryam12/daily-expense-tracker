<?php 
$host='localhost';
$user='root';
$password='';
$db='daily_expense_tracker';

$connection=mysqli_connect($host,$user,$password,$db);
if ($connection) {	

}
else {
	echo "connection not establish";
}
?>