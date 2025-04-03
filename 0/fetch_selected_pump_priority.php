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

if (!isset($input['database'])) {
    die(json_encode(["status" => "error", "message" => "Invalid input received"]));
}

$database = $conn->real_escape_string($input['database']);

// Select the database
if (!$conn->select_db($database)) {
    die(json_encode(["status" => "error", "message" => "Database selection failed", "sql_error" => $conn->error]));
}

// Fetch pump data
$pumpQuery = "SELECT pump_id FROM `$database`.pumps ORDER BY id DESC LIMIT 1";
$pumpResult = $conn->query($pumpQuery);
$pumpName = "Not Selected";

if ($pumpResult && $pumpResult->num_rows > 0) {
    $pumpData = $pumpResult->fetch_assoc();
    $pumpName = $pumpData['pump_id'];
}

// Fetch priority data
$priorityQuery = "SELECT priority_id FROM `$database`.priority_mode ORDER BY id DESC LIMIT 1";
$priorityResult = $conn->query($priorityQuery);
$priorityLevel = "Not Selected";

if ($priorityResult && $priorityResult->num_rows > 0) {
    $priorityData = $priorityResult->fetch_assoc();
    $priorityLevel = $priorityData['priority_id'];
}

// Close connection
$conn->close();

// Return data as JSON
echo json_encode([
    "status" => "success",
    "selected_pump" => $pumpName,
    "selected_priority" => $priorityLevel
]);
?>
