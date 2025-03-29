<?php

require_once '../config_db/config.php';

// Establish the database connection
$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get data from POST
$tank_id = $_POST['tank_id'];
$status = $_POST['status'];

// Determine the value based on the status
$value =  $status ;
/*if($status==true)
{
    $value =  1 ;
}
else if($status==false)
{
    $value =  0;
}*/


// Insert into `tank_value_updates` table
$valueInsertQuery = "INSERT INTO `tank_value_updates` (`tank_id`, `valve_status`, `date_time`) 
VALUES (?, ?, current_timestamp())";
$valueStmt = mysqli_prepare($conn, $valueInsertQuery);
if (!$valueStmt) {
    die("Error preparing value insert query: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($valueStmt, 'si', $tank_id, $value);
if (!mysqli_stmt_execute($valueStmt)) {
    die("Error executing value insert query: " . mysqli_stmt_error($valueStmt));
}
mysqli_stmt_close($valueStmt);

// Insert or update `tank_updates` table
$updateQuery = "INSERT INTO `tank_updates` (tank_id, setting_type, setting_flag) VALUES (?, 'VALVE_ONOFF', ?)  ON DUPLICATE KEY UPDATE setting_flag = VALUES(setting_flag)";
$updateStmt = mysqli_prepare($conn, $updateQuery);
if (!$updateStmt) {
    die("Error preparing update query: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($updateStmt, 'si', $tank_id, $value);
if (!mysqli_stmt_execute($updateStmt)) {
    die("Error executing update query: " . mysqli_stmt_error($updateStmt));
}
mysqli_stmt_close($updateStmt);

// Success message
echo "Data updated successfully!";

// Close the database connection
mysqli_close($conn);

?>
