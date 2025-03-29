<?php
require_once '../config_db/config.php';
$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL);

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tankId = mysqli_real_escape_string($conn, $_POST['tank_id'] ?? '');
    $capacity = (int) ($_POST['capacity'] ?? 0);

    // Validate input
    if (empty($tankId) || $capacity <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }

    // Update query
    $query = "UPDATE assigned_motor_tanks SET capacity = $capacity WHERE tank_id = '$tankId'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $query_1 = "UPDATE tanks_status SET capacity = $capacity WHERE tank_id = '$tankId'";
        mysqli_query($conn, $query_1);

        $updateQuery = "INSERT INTO `tank_updates` (tank_id, setting_type, setting_flag) VALUES ('$tankId', 'TANK_CAPACITY', '1')  ON DUPLICATE KEY UPDATE setting_flag = VALUES(setting_flag)";
        mysqli_query($conn, $updateQuery);

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating capacity']);
    }
}

// Close connection
mysqli_close($conn);
?>