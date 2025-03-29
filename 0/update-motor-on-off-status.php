<?php
require_once '../config_db/config.php';


if(isset($_POST['ID']))
{
    $id=$_POST['ID'];
    $status=$_POST['STATUS'];


    $update_flad=0;
    $response="";

    $update_user="tank-application";

    date_default_timezone_set('Asia/Kolkata');
    $date=date("Y-m-d H:i:s");

    $db=strtolower($id);
    require_once '../config_db/config.php';
    $conn = mysqli_connect(HOST,USERNAME,PASSWORD);
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    else
    {
       $d_id= trim(strtolower($id));
        //$d_id= trim(strtolower("MCMS_10"));
        $sql="INSERT INTO `$d_id`.`on_off_activity` ( `on_off`, `time`, `user`, `role`, `date_time`) VALUES ('$status', '0', '$update_user', '', '$date');";
        mysqli_query($conn, $sql);

        mysqli_query($conn,"INSERT INTO `$d_id`.device_settings ( `setting_type`, `setting_flag`) VALUES('ONOFF', '1') ON DUPLICATE KEY UPDATE setting_type='ONOFF', setting_flag='1'");

        $response="Successfully Saved..!!";


        mysqli_close($conn);
    }

    echo $response;
}
else
{
    echo "Somthing Went Wrong..!";
}

?>