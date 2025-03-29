<?php
require_once '../config_db/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $location = $_POST['location'] ?? '';
    $motorId = $_POST['motorId'] ?? '';
    $tanks = json_decode($_POST['tanks'] ?? '[]', true);

    // Database connection
    $conn = mysqli_connect('localhost', 'root', '123456', 'motor_pumps');

    if (!$conn) {
        die('Database connection error: ' . mysqli_connect_error());
    }

    // Insert Location and Motor Details
    $locationQuery = "INSERT INTO locations (location_name, motor_id) VALUES ('$location', '$motorId')";
    if (mysqli_query($conn, $locationQuery)) {
        $locationId = mysqli_insert_id($conn);

        // Insert Tanks and Priorities
        foreach ($tanks as $tank) {
            $tankId = $tank['id'];
            $capacity = $tank['capacity'];
            $priority = $tank['priority'];

            $tankQuery = "INSERT INTO tanks (location_id, motor_id, tank_identifier, capacity, priority) 
            VALUES ('$locationId', '$motorId', '$tankId', '$capacity', '$priority')";
            mysqli_query($conn, $tankQuery);

        }

        echo 'Data saved successfully!';
    } else {
        echo 'Error: ' . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>
