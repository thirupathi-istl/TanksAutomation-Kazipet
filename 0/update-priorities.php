<?php

require_once '../config_db/config.php';
$conn = mysqli_connect(HOST, USERNAME, PASSWORD);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get data from POST
$device_id = $_POST['device_id'];
$priorities = json_decode($_POST['priorities'], true);

// Insert or update data
if (!empty($priorities)) {
    foreach ($priorities as $priority) {
        $tank_id = $priority['tankId'];
        $priority_value = $priority['priorityValue'];

        // Check if the record exists
        $checkQuery = "SELECT id FROM `motor_pumps`.`assigned_motor_tanks` WHERE motor_id = ? AND tank_id = ?";
        $stmt = mysqli_prepare($conn, $checkQuery);
        mysqli_stmt_bind_param($stmt, 'ss', $device_id, $tank_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            // Update existing record
            $updateQuery = "UPDATE `motor_pumps`.`assigned_motor_tanks` SET priority = ?, created_at = NOW() WHERE motor_id = ? AND tank_id = ?";
            $updateStmt = mysqli_prepare($conn, $updateQuery);
            mysqli_stmt_bind_param($updateStmt, 'iss', $priority_value, $device_id, $tank_id);
            mysqli_stmt_execute($updateStmt);
        } else {
            // Insert new record
            $insertQuery = "INSERT INTO `motor_pumps`.`assigned_motor_tanks` (motor_id, tank_id, priority, created_at) VALUES (?, ?, ?, NOW())";
            $insertStmt = mysqli_prepare($conn, $insertQuery);
            mysqli_stmt_bind_param($insertStmt, 'ssi', $device_id, $tank_id, $priority_value);
            mysqli_stmt_execute($insertStmt);
        }

        mysqli_stmt_close($stmt);

        $d_id=strtolower($device_id);
        
        $setting_sql = "INSERT INTO `$d_id`.`device_settings` (`setting_type`, `setting_flag`) VALUES ('TANKS_PRIORITY', '1') 
        ON DUPLICATE KEY UPDATE setting_flag='1'";
        mysqli_query($conn, $setting_sql);
    }
    echo "Data updated successfully!";
} else {
    echo "No data received.";
}

mysqli_close($conn);
?>
