<?php
require_once '../config_db/config.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') 
{
    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL);
    if (!$conn) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit;
    }

    $deviceId = mysqli_real_escape_string($conn, $_POST['device_id'] ?? '');
    //$query = "SELECT tank_id, capacity FROM assigned_motor_tanks where motor_id='$deviceId' ORDER BY tank_id DESC";
    $query = "SELECT tank_id, capacity FROM assigned_motor_tanks where motor_id IN (SELECT motor_id FROM motors_group WHERE group_list =(SELECT group_list FROM `motors_group` WHERE motor_id='$deviceId') ) ORDER BY tank_id DESC";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $tanks = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $tanks[] = $row;
        }
        echo json_encode(['success' => true, 'tanks' => $tanks]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error fetching tanks']);
    }

    mysqli_close($conn);
}
?>