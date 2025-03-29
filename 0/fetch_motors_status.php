<?php

require_once '../config_db/config.php';
$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL);

if (!$conn) {
    die('Database connection error: ' . mysqli_connect_error());
}
$device_id = $_POST['device_id'];
$response=array();
//$sql_status = "SELECT *FROM motor_status_update where group_list='kazipet-1' AND flow='IN' UNION SELECT *FROM motor_status_update where group_list='kazipet-1' AND motor_id='$device_id'";
$sql_status = "SELECT * FROM motor_status_update WHERE group_list = 'kazipet-1' AND (flow = 'IN' OR motor_id = '$device_id');";
if(mysqli_query($conn, $sql_status))
{
    $result = mysqli_query($conn, $sql_status);
    if(mysqli_num_rows($result)>0)
    {
        while ($rl = mysqli_fetch_assoc( $result ))
        {
            $response[]=$rl;
        }

    }

} else {
    echo 'Error: ' . mysqli_error($conn);
}
mysqli_close($conn);

echo json_encode($response);
?>
