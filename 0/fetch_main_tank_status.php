<?php
require_once '../config_db/config.php';

$conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB_ALL);
if (!$conn) {
    die('Database connection error: ' . mysqli_connect_error());
}
$response=array();
$sql_status = "SELECT  *FROM tanks_status where tank_id in (select tank_id from assigned_motor_tanks where motor_id=(SELECT motor_id from motors_group where group_list='kazipet-1' AND flow='IN'))";
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
