<?php
require_once '../config_db/config.php';

header("Content-Type: application/json");

// Connect to MySQL server
$conn = new mysqli(HOST, USERNAME, PASSWORD);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed", "sql_error" => $conn->connect_error]));
}

// Read JSON input
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['database']) || !isset($input['pump_id'])) {
    die(json_encode(["status" => "error", "message" => "Invalid input received"]));
}

$db_name = trim(strtolower($input['database']));
$pump_id = $input['pump_id'];

error_log("Using database: " . $db_name);

// Validate database name (prevent SQL injection)
if (!preg_match('/^[a-zA-Z0-9_]+$/', $db_name)) {
    die(json_encode(["status" => "error", "message" => "Invalid database name"]));
}

// Prepare the SQL statement
$stmt = $conn->prepare("INSERT INTO `$db_name`.pumps (pump_id) VALUES (?)");

if (!$stmt) {
    die(json_encode(["status" => "error", "message" => "Statement preparation failed", "sql_error" => $conn->error]));
}

$stmt->bind_param("s", $pump_id);

if ($stmt->execute()) {

    if (mysqli_query($conn, "INSERT INTO `$db_name`.`device_settings` (`setting_type`, `setting_flag`) VALUES('PUMP_SELECTION', '1') ON DUPLICATE KEY UPDATE setting_flag='1'")) {
        echo json_encode(["status" => "success", "message" => "Pump switching saved successfully"]);
    }
    else
    {
        echo json_encode(["status" => "error", "message" => "Please try again..!"]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "Failed to insert Pump ID", "sql_error" => $stmt->error]);
}

$stmt->close();
$conn->close();
?>
