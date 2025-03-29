<?php
require_once '../config_db/config.php';

// Sanitize and validate input
$tank_id = isset($_POST['tank_id']) ? $_POST['tank_id'] : '';
$device_id = isset($_POST['device_id']) ? $_POST['device_id'] : '';
$records = isset($_POST['RECORDS']) ? $_POST['RECORDS'] : 'LATEST';

// Initialize response array
$response = [
    'tankStatus' => []
];

// Connect to database
$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL);
if (!$conn) {
    $response['error'] = 'Database connection error: ' . mysqli_connect_error();
    echo json_encode($response);
    exit();
}

// Fetch Tank Status based on record type
switch (strtoupper($records)) {
    case "LATEST":
        $sql_tank = "SELECT * FROM tanks_status_history WHERE tank_id = ? ORDER BY date_time DESC LIMIT 20";
        $stmt = mysqli_prepare($conn, $sql_tank);
        mysqli_stmt_bind_param($stmt, 's', $tank_id);
        break;
        
    case "ADD":
        if (isset($_POST['DATE_TIME']) && !empty($_POST['DATE_TIME'])) {
            $date = trim($_POST['DATE_TIME']);
            // Handle potential different date formats
            $date_formatted = date('Y-m-d H:i:s', strtotime($date));
            
            $sql_tank = "SELECT * FROM tanks_status_history WHERE tank_id = ? AND date_time < ? ORDER BY date_time DESC LIMIT 200";
            $stmt = mysqli_prepare($conn, $sql_tank);
            mysqli_stmt_bind_param($stmt, 'ss', $tank_id, $date_formatted);
        } else {
            $response['error'] = 'Records not found. Empty date_time parameter sent.';
            echo json_encode($response);
            mysqli_close($conn);
            exit();
        }
        break;
        
    case "DATE":
        if (isset($_POST['DATE']) && !empty($_POST['DATE'])) {
            $date = trim($_POST['DATE']);
            $date_formatted = date('Y-m-d', strtotime($date));
            
            $sql_tank = "SELECT * FROM tanks_status_history WHERE tank_id = ? AND DATE(date_time) = ? ORDER BY date_time DESC LIMIT 200";
            $stmt = mysqli_prepare($conn, $sql_tank);
            mysqli_stmt_bind_param($stmt, 'ss', $tank_id, $date_formatted);
        } else {
            $response['error'] = 'Records not found. Empty date parameter sent.';
            echo json_encode($response);
            mysqli_close($conn);
            exit();
        }
        break;
        
    default:
        $response['error'] = 'Invalid record type specified.';
        echo json_encode($response);
        mysqli_close($conn);
        exit();
}

// Execute the query
mysqli_stmt_execute($stmt);
$result_tank = mysqli_stmt_get_result($stmt);

// Process results
if ($result_tank && mysqli_num_rows($result_tank) > 0) {
    while ($row = mysqli_fetch_assoc($result_tank)) {
        $response['tankStatus'][] = [
            'tank_id' => $row['tank_id'],
            'tank_status' => $row['tank_status'],
            'valve_status' => $row['valve_status'],
            'current_status' => $row['current_status'],
            'flow_rate' => $row['flow_rate'],
            'estimated_time' => $row['estimated_time'],
            'consumed_time' => $row['consumed_time'],
            'consumed_water' => $row['comsumed_water'], // Note: Column is misspelled in database as "comsumed_water"
            'voltage_1' => $row['voltage_1'],
            'voltage_2' => $row['voltage_2'],
            'gateway_id' => $row['gateway_id'],
            'date_time' => $row['date_time']
        ];
    }
} else {
    $response['error'] = 'No records found.';
}

// Close database connection and return response
mysqli_stmt_close($stmt);
mysqli_close($conn);
echo json_encode($response);
?>