<?php
require_once '../config_db/config.php';

$device_id = $_POST['device_id'];

$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL);

if (!$conn) {
    die('Database connection error: ' . mysqli_connect_error());
}

$response = [
    'tankStatus' => [],
    'mainTankStatus' => [],
    'motorsStatus' => []
];

// Fetch Tank Status
$sql_tank = "SELECT * FROM tanks_status WHERE tank_id IN (SELECT tank_id FROM assigned_motor_tanks WHERE motor_id = '$device_id')";
$result_tank = mysqli_query($conn, $sql_tank);
if ($result_tank && mysqli_num_rows($result_tank) > 0) {
    while ($row = mysqli_fetch_assoc($result_tank)) {
        $response['tankStatus'][] = $row;
    }
}

// Fetch Main Tank Status
$sql_main_tank = "SELECT * FROM tanks_status WHERE tank_id IN (SELECT tank_id FROM assigned_motor_tanks WHERE motor_id = (SELECT motor_id FROM motors_group WHERE group_list = 'kazipet-1' AND flow = 'IN'))";
$result_main_tank = mysqli_query($conn, $sql_main_tank);
if ($result_main_tank && mysqli_num_rows($result_main_tank) > 0) {
    while ($row = mysqli_fetch_assoc($result_main_tank)) {
        $response['mainTankStatus'][] = $row;
    }
}

// Fetch Motor Status
$sql_motor = "SELECT * FROM motor_status_update WHERE group_list = 'kazipet-1' AND (flow = 'IN' OR motor_id = '$device_id')";
$result_motor = mysqli_query($conn, $sql_motor);
if ($result_motor && mysqli_num_rows($result_motor) > 0) {
    while ($row = mysqli_fetch_assoc($result_motor)) {
        $response['motorsStatus'][] = $row;
    }
}


mysqli_close($conn);

echo json_encode($response);
?>
