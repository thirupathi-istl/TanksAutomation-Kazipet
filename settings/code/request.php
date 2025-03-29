<?php
date_default_timezone_set('Asia/Kolkata');

/*define('HOST', 'localhost');
define('USERNAME', 'root');
define('PASSWORD', '123456');
define('DB1', 'motoruserdb');
$dbAll="motor_pumps";
$live = 0;*/

//========================================================//
define('HOST', '103.101.59.93');
define('USERNAME', 'istlabsonline_db_user');
define('PASSWORD', 'istlabsonline_db_pass');

define('DB1', 'motoruserdb');
define('DB2', 'notifications_db');
$live = 1;

$dbAll="motor_pumps";

if (isset($_POST['REQ']))
{
    $data = $_POST['REQ'];

    //$data="MCMS_10;ACK;TANKS_PRIORITY";
    //$data="CCMS_1;ALERT;SUPPLY;3;234;235;236;23;24;25;20/09/29 12:23:34";
    //$data="CCMS_3;SUPDATE;";
    //$data="CCMS_3;";//;CALIB;CLEAR";   //UPDATETIME, LOADCLEAR, MINMAXSET,ONOFFLIGHT ,HYSTVAL
    //$data="MCMS_4;ALERT;CURRENT;111;250;256;267;12;13;14;23/01/11 12:11:14;";
    //$data="MCMS_4;EVENTS;IP_POWER;0;4158;23/01/23 15:21:14;";
    //$data="MCMS_4;EVENTS;IP_POWER_SMPS;2;4158;23/01/23 15:21:14;";
    //$data="MCMS_4;EVENTS;ONOFF;3;25/01/23 15:21:14;";
   // $data="MCMS_4;ACK;DRY_RUN;";
   //$data="MCMS_4;ACK;SERIALNO;";            //DEVID, ECLR,   WIFI, SERIALNO
    if ($data != "")
    {
        $response = "";
        $array_data = explode(';', $data);
        if ($GLOBALS['live'] == 1)
        {
            update($array_data[0]);
        }

        $db = strtolower($array_data[0]);
        $conn = mysqli_connect(HOST, USERNAME, PASSWORD, $db);
        if (!$conn)
        {
            die("Connection failed");
        }
        else
        {

            update_ping($array_data[0], $conn);

            if (trim($array_data[1]) == "ACK")
            {
                $status = 0;

                $condtion = $array_data[2];

                switch ($condtion)
                {
                    case "ONOFF":
                    clear_ack("ONOFF", $conn);
                    break;

                    case "VOLTAGE":
                    clear_ack("VOLTAGE", $conn);
                    break;

                    case "CURRENT":
                    clear_ack("CURRENT", $conn);
                    break;

                    case "DRY_RUN":
                    clear_ack("DRY_RUN", $conn);
                    break;

                    case "UPDATETIME":
                    clear_ack("FRAME_TIME", $conn);
                    break;

                    case "HYSTVAL":
                    clear_ack("HYSTERESIS", $conn);
                    break;

                    case "CALIB":
                    clear_ack("CALIB_VALUES", $conn);
                    break;

                    case "DEVID":
                    clear_ack("ID_CHANGE", $conn);
                    break;

                    case "ECLR":
                    clear_ack("ENERGY_RESET", $conn);
                    break;

                    case "WIFI":
                    clear_ack("WIFI_CREDENTIALS", $conn);
                    break;

                    case "ANGLE":
                    clear_ack("ANGLE", $conn);
                    break;

                    case "SERIALNO":
                    clear_ack("SERIAL_ID", $conn);
                    break;

                    case "TANKS_PRIORITY":
                    clear_ack("TANKS_PRIORITY", $conn);
                    break;

                    case "SUPDATE":

                    date_default_timezone_set('Asia/Kolkata');
                    $date = date("Y-m-d H:i:s");
                    mysqli_query($conn, "INSERT INTO `software_update_status` (`status_1`, `status_2`, `date_time`) VALUES ('$array_data[3]', '$array_data[4]', '$date')");

                    clear_ack("SOFTWARE", $conn);
                    break;

                    default:
                    $response = "ERROR";
                }
            }
            else if ($array_data[1] == 'ALERT')
            {
                $phase = $array_data[3];
                switch ($phase)
                {
                    case "000":
                    $response = voltage_current_alerts($array_data[0], $array_data[2], "Resumed", "1", $conn, "0", $array_data[4], $array_data[5], $array_data[6], $array_data[7], $array_data[8], $array_data[9], $array_data[10]);
                    break;

                    case "001":
                    $response = voltage_current_alerts($array_data[0], $array_data[2], "B", "R & Y", $conn, "1", $array_data[4], $array_data[5], $array_data[6], $array_data[7], $array_data[8], $array_data[9], $array_data[10]);
                    break;

                    case "010":
                    $response = voltage_current_alerts($array_data[0], $array_data[2], "Y", "R & B", $conn, "1", $array_data[4], $array_data[5], $array_data[6], $array_data[7], $array_data[8], $array_data[9], $array_data[10]);
                    break;

                    case "011":
                    $response = voltage_current_alerts($array_data[0], $array_data[2], "Y & B", "R", $conn, "1", $array_data[4], $array_data[5], $array_data[6], $array_data[7], $array_data[8], $array_data[9], $array_data[10]);
                    break;

                    case "100":
                    $response = voltage_current_alerts($array_data[0], $array_data[2], "R", "Y & B", $conn, "1", $array_data[4], $array_data[5], $array_data[6], $array_data[7], $array_data[8], $array_data[9], $array_data[10]);
                    break;

                    case "101":
                    $response = voltage_current_alerts($array_data[0], $array_data[2], "R & B", "Y", $conn, "1", $array_data[4], $array_data[5], $array_data[6], $array_data[7], $array_data[8], $array_data[9], $array_data[10]);
                    break;

                    case "110":
                    $response = voltage_current_alerts($array_data[0], $array_data[2], "R & Y", "B", $conn, "1", $array_data[4], $array_data[5], $array_data[6], $array_data[7], $array_data[8], $array_data[9], $array_data[10]);
                    break;

                    case "111":
                    $response = voltage_current_alerts($array_data[0], $array_data[2], "R, Y & B", "0", $conn, "1", $array_data[4], $array_data[5], $array_data[6], $array_data[7], $array_data[8], $array_data[9], $array_data[10]);
                    break;

                    default:
                    $response = "ERROR";
                }
            }
            else if ($array_data[1] == 'EVENTS')
            {
                $command = $array_data[2];
                switch ($command)
                {
                    case "ONOFF":
                    onoff_update($conn, $array_data[0], $array_data[3], $array_data[4]);
                    break;

                    case "IP_POWER_SMPS":
                    ip_power_smps_status_update($conn, $array_data[0], $array_data[3], $array_data[4], $array_data[5]);
                    break;

                    default:
                    $response = "ERROR EVENT";
                }
            }
            else if ($array_data[1] == 'SUPDATE')
            {
                $sql = "SELECT software FROM software_update ORDER BY id DESC LIMIT 1";
                if (mysqli_query($conn, $sql))
                {
                    $result = mysqli_query($conn, $sql);
                    if (mysqli_num_rows($result) > 0)
                    {
                        while ($r = mysqli_fetch_assoc($result))
                        {
                            $response = $r['software'];

                        }
                    }
                    else
                    {
                        $response = "000;No data";
                    }
                }
            }
            else if ($array_data[1] == 'OFFLINE' || $array_data[1] == 'DEFAULT')
            {
                $sql = "INSERT INTO `loaded_settings` ( `frame`) VALUES ('$data')";
                mysqli_query($conn, $sql);
                clear_ack("DEFAULT", $conn);
            }
            else if ($array_data[1] == 'REPORT')
            {
                try
                {
                    date_default_timezone_set('Asia/Kolkata');
                    $date_time = date("Y-m-d H:i:s");

                    $command = $array_data[2];
                    if (mysqli_query($conn, "SELECT * FROM device_check_report WHERE field ='$command'"))
                    {
                        $exist_row = mysqli_query($conn, "SELECT * FROM device_check_report WHERE field ='$command'");

                        if (mysqli_num_rows($exist_row) > 0)
                        {
                            mysqli_query($conn, "UPDATE device_check_report SET status='$array_data[3]', date_time = '$date_time' WHERE field = '$command' ");
                        }
                        else
                        {
                            mysqli_query($conn, "INSERT INTO `device_check_report` ( `field`, `status`, `date_time`) VALUES ( '$array_data[2]', '$array_data[3]', '$date_time')");
                        }

                        if ($array_data[2] == "SIMCOM_OFF")
                        {
                            mysqli_query($conn, "INSERT INTO `sim_module_communication` ( `date_time`, `server_time`) VALUES ( '$array_data[3]', '$date_time');");
                        }

                        if ($array_data[2] == "SIMCOMSTATUS")
                        {
                            mysqli_query($conn, "INSERT INTO `simcom_status` (`status`, `status_code`, `date_time`,`server_date_time`) VALUES ('$array_data[3]', '$array_data[4]', '$array_data[5]','$date_time');");
                        }

                        if ($array_data[2] == "SYSTEMSTATUS")
                        {
                            $array_data[3] = trim($array_data[3]);
                            $s_status = "";
                            $s_id = 0;

                            $sql = "SELECT * FROM system_status ORDER BY id DESC LIMIT 1";
                            if (mysqli_query($conn, $sql))
                            {
                                $result = mysqli_query($conn, $sql);
                                if (mysqli_num_rows($result) > 0)
                                {
                                    $r = mysqli_fetch_assoc($result);
                                    $s_status = trim($r['status']);
                                    $s_id = trim($r['id']);
                                }
                            }
                            $upd_status_flag = 0;

                            if ($s_status == trim($array_data[3]))
                            {
                                mysqli_query($conn, "UPDATE `system_status`  SET `date_time`='$array_data[4]' WHERE id='$s_id'");
                            }
                            else
                            {
                                mysqli_query($conn, "INSERT INTO `system_status` ( `status`, `date_time`, `prev_date_time` , `server_date_time`) VALUES ( '$array_data[3]', '$array_data[4]', '$array_data[4]', '$date_time');");
                                $upd_status_flag = 1;
                            }
                        }
                    }
                    else
                    {
                        $result = mysqli_query($conn, "SHOW TABLES LIKE 'device_check_report'");
                        if (mysqli_num_rows($result) <= 0)
                        {
                            $sql = "CREATE TABLE `device_check_report` (`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `field` varchar(255) NOT NULL, `status` varchar(255) NOT NULL, `date_time` datetime NOT NULL ) ENGINE=MyISAM DEFAULT CHARSET=latin1";
                            mysqli_query($conn, $sql);
                            mysqli_query($conn, "INSERT INTO `device_check_report` ( `field`, `status`, `date_time`) VALUES ( '$array_data[2]', '$array_data[3]', '$date_time')");
                        }
                    }
                }
                catch(Exception $e)
                {

                }
            }

            //$response = mainfunction($data, $conn);

            http_response_code(201);
            if ($response != null || $response != "")
            {
                echo $response;
            }
            else
            {
                echo $response = mainfunction($data, $conn);
            }
            mysqli_close($conn);
        }
    }
    else
    {
        echo "Empty Data";
    }
}
else if (isset($_POST['TANK']))
{

    $data=$_POST['TANK'];
    
    $response = "";
    $array_data = explode(';', $data);


    $db = strtolower($array_data[0]);
    $conn = mysqli_connect(HOST, USERNAME, PASSWORD);
    if (!$conn)
    {
        die("Connection failed");
    }
    else
    {
        if (trim($array_data[1]) == "ACK")
        {
            $status = 0;

            $condtion = $array_data[2];

            switch ($condtion)
            {
                case "VALVE_ONOFF":
                clearTanksAck("VALVE_ONOFF", $conn, $array_data[0]);
                break;
                case "CAPACITY":
                clearTanksAck("TANK_CAPACITY", $conn, $array_data[0]);
                break;

                case "SUPDATE":

                date_default_timezone_set('Asia/Kolkata');
                $date = date("Y-m-d H:i:s");
                mysqli_query($conn, "INSERT INTO `$dbAll`.`software_update_status` (`device_id`, `status`, `status_code`, `date_time`) VALUES ('$array_data[0]','$array_data[3]', '$array_data[4]', '$date')");

                clearTanksAck("SOFTWARE", $conn, $array_data[0]);
                break;

                default:
                $response = "ERROR";
            }
        }
        else if ($array_data[1] == 'SUPDATE')
        {
            $sql = "SELECT software FROM `$dbAll`.software_update WHERE device_id='$array_data[0]' ORDER BY id DESC LIMIT 1";
            if (mysqli_query($conn, $sql))
            {
               
                $result = mysqli_query($conn, $sql);
                if (mysqli_num_rows($result) > 0)
                {
                    while ($r = mysqli_fetch_assoc($result))
                    {
                       echo $response = $r['software'];

                    }
                }
                else
                {
                    $response = "000;No data";
                }
            }
        }

       // $response = tanksUpdates($data, $conn);

        http_response_code(201);
        if ($response != null || $response != "")
        {
            echo $response;
        }
        else
        {
            echo $response = tanksUpdates($data, $conn);
        }
        mysqli_close($conn);

    }

}
else{
    echo "Bad request";
}


////////////////////////////////////    main Function   ////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////
function mainfunction($data, $conn)
{
    $array_data = explode(';', $data);
    $sql = "";
    $response = "";
    $condtion = "NA";
    $check = array();
    $length = 0;

    $sql = "SELECT *from (SELECT * FROM device_settings WHERE setting_type IN ('ONOFF', 'VOLTAGE', 'CURRENT', 'DRY_RUN', 'FRAME_TIME', 'HYSTERESIS', 'CALIB_VALUES', 'SOFTWARE', 'RESET', 'ID_CHANGE', 'ENERGY_RESET', 'SERIAL_ID', 'WIFI_CREDENTIALS', 'TANKS_PRIORITY') ORDER BY setting_type DESC) a UNION ALL (SELECT * FROM device_settings WHERE setting_type='DEFAULT' LIMIT 1)";

    if (mysqli_query($conn, $sql))
    {
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0)
        {
            while ($r = mysqli_fetch_assoc($result))
            {
                $check[] = array(
                   'type' => $r['setting_type'],
                   'flag' => $r['setting_flag']
               );
            }
        }
        else
        {
            $response = "000;No data";
        }
    }

    $values = json_encode($check);
    $set_list = json_decode($values);
    foreach ($set_list as $key => $value)
    {
        $condtion = $value->type;

        $set_flag = $value->flag;

        if ($set_flag == 1 || $set_flag == 2)
        {
            break;
        }
        else
        {
            $condtion = "NA";
        }
    }
    switch ($condtion)
    {
        case "VOLTAGE":
        $sql = "UPDATE device_settings SET setting_flag='2' WHERE setting_type='$condtion'";
        if (mysqli_query($conn, $sql))
        {
            $set_sql = "SELECT * FROM limit_voltage ORDER BY id DESC LIMIT 1";
            if (mysqli_query($conn, $set_sql))
            {
                $result = mysqli_query($conn, $set_sql);
                if (mysqli_num_rows($result) > 0)
                {
                    $r = mysqli_fetch_assoc($result);

                    $min_1 = (int)$r['min_1'];
                    $min_2 = (int)$r['min_2'];
                    $min_3 = (int)$r['min_3'];
                    $max_1 = (int)$r['max_1'];
                    $max_2 = (int)$r['max_2'];
                    $max_3 = (int)$r['max_3'];

                    $MIN = $min_1 . ";" . $min_2 . ";" . $min_3;
                    $MAX = $max_1 . ";" . $max_2 . ";" . $max_3;

                    $data = "V_MIN=" . $MIN . ";V_MAX=" . $MAX . ";";

                    $len = strlen($data);
                    $length = str_pad($len, 3, '0', STR_PAD_LEFT);
                    $data = $length . ";" . $data;

                    $response = "RES=" . $array_data[0] . ";" . $data;
                }
            }
        }
        break;

        case "CURRENT":
        $sql = "UPDATE device_settings SET setting_flag='2' WHERE setting_type='$condtion'";
        if (mysqli_query($conn, $sql))
        {
            $set_sql = "SELECT * FROM limit_current ORDER BY id DESC LIMIT 1";
            if (mysqli_query($conn, $set_sql))
            {
                $result = mysqli_query($conn, $set_sql);
                if (mysqli_num_rows($result) > 0)
                {
                    $r = mysqli_fetch_assoc($result);

                    $max_1 = (int)$r['max_1'];
                    $max_2 = (int)$r['max_2'];
                    $max_3 = (int)$r['max_3'];

                    $MAX = $max_1 . ";" . $max_2 . ";" . $max_3;

                    $data = "I_MAX=" . $MAX . ";";

                    $len = strlen($data);
                    $length = str_pad($len, 3, '0', STR_PAD_LEFT);
                    $data = $length . ";" . $data;

                    $response = "RES=" . $array_data[0] . ";" . $data;
                }
            }
        }
        break;

        case "DRY_RUN":
        $sql = "UPDATE device_settings SET setting_flag='2' WHERE setting_type='$condtion'";
        if (mysqli_query($conn, $sql))
        {
            $set_sql = "SELECT * FROM limit_dry_run ORDER BY id DESC LIMIT 1";
            if (mysqli_query($conn, $set_sql))
            {
                $result = mysqli_query($conn, $set_sql);
                if (mysqli_num_rows($result) > 0)
                {
                    $r = mysqli_fetch_assoc($result);

                    $limit = (int)$r['dry_run_limit'];

                    $data = "I_MIN=" . $limit . ";";

                    $len = strlen($data);
                    $length = str_pad($len, 3, '0', STR_PAD_LEFT);
                    $data = $length . ";" . $data;

                    $response = "RES=" . $array_data[0] . ";" . $data;
                }
            }
        }
        break;

        case "ONOFF":
        $sql = "UPDATE device_settings SET setting_flag='2' WHERE setting_type='$condtion'";
        if (mysqli_query($conn, $sql))
        {
            $set_sql = "SELECT * FROM on_off_activity ORDER BY id DESC LIMIT 1";
            if (mysqli_query($conn, $set_sql))
            {
                $result = mysqli_query($conn, $set_sql);
                if (mysqli_num_rows($result) > 0)
                {
                    $r = mysqli_fetch_assoc($result);

                    $switch = $r['on_off'];
                    $time = $r['time'];

                    $data = "SWITCH" . $switch . ";" . $time . ";";
                    $len = strlen($data);
                    $length = str_pad($len, 3, '0', STR_PAD_LEFT);
                    $data = $length . ";" . $data;
                    $response = "RES=" . $array_data[0] . ";" . $data;
                }
            }
        }
        break;

        case "FRAME_TIME":
        $sql = "UPDATE device_settings SET setting_flag='2' WHERE setting_type='$condtion'";
        if (mysqli_query($conn, $sql))
        {
            $set_sql = "SELECT * FROM update_time_interval ORDER BY id DESC LIMIT 1";
            if (mysqli_query($conn, $set_sql))
            {
                $result = mysqli_query($conn, $set_sql);
                if (mysqli_num_rows($result) > 0)
                {
                    $r = mysqli_fetch_assoc($result);

                    $update_interval = (int)$r['time'];

                    $data = "FRAMETIME=" . $update_interval . ";";
                    $len = strlen($data);
                    $length = str_pad($len, 3, '0', STR_PAD_LEFT);
                    $data = $length . ";" . $data;

                    $response = "RES=" . $array_data[0] . ";" . $data;
                }
            }
        }
        break;

        case "HYSTERESIS":
        $sql = "UPDATE device_settings SET setting_flag='2' WHERE setting_type='$condtion'";
        if (mysqli_query($conn, $sql))
        {
            $set_sql = "SELECT * FROM hysteresis_history ORDER BY id DESC LIMIT 1";
            if (mysqli_query($conn, $set_sql))
            {
                $result = mysqli_query($conn, $set_sql);
                if (mysqli_num_rows($result) > 0)
                {
                    $r = mysqli_fetch_assoc($result);

                    $hysteresis = (int)$r['set_value'];
                    $data = "HYST=" . $hysteresis . ";";
                    $len = strlen($data);
                    $length = str_pad($len, 3, '0', STR_PAD_LEFT);
                    $data = $length . ";" . $data;

                    $response = "RES=" . $array_data[0] . ";" . $data;
                }
            }
        }
        break;

        case "CALIB_VALUES":
        $sql = "UPDATE device_settings SET setting_flag='2' WHERE setting_type='$condtion'";
        if (mysqli_query($conn, $sql))
        {
            $set_sql = "SELECT * FROM calib_settings ORDER BY id DESC LIMIT 1";
            if (mysqli_query($conn, $set_sql))
            {
                $result = mysqli_query($conn, $set_sql);
                if (mysqli_num_rows($result) > 0)
                {
                    $r = mysqli_fetch_assoc($result);

                    $calib_val = $r['frame'];
                    $data = "CALIB:" . $calib_val;
                    $len = strlen($data);
                    $length = str_pad($len, 3, '0', STR_PAD_LEFT);
                    $data = $length . ";" . $data;

                    $response = "RES=" . $array_data[0] . ";" . $data;
                }
            }
        }
        break;

        case "SOFTWARE":
        $sql = "UPDATE device_settings SET setting_flag='2' WHERE setting_type='$condtion'";
        if (mysqli_query($conn, $sql))
        {
            $data = "S_UPDATE;";
            $len = strlen($data);
            $length = str_pad($len, 3, '0', STR_PAD_LEFT);
            $data = $length . ";" . $data;

            $response = "RES=" . $array_data[0] . ";" . $data;
        }

        break;

        case "RESET":

        $sql = "UPDATE device_settings SET setting_flag='0' WHERE setting_type='$condtion'";
        if (mysqli_query($conn, $sql))
        {
            $data = "RESET=0;";

            $len = strlen($data);
            $length = str_pad($len, 3, '0', STR_PAD_LEFT);
            $data = $length . ";" . $data;

            $response = "RES=" . $array_data[0] . ";" . $data;
        }

        break;

        case "DEFAULT":

        $sql = "UPDATE device_settings SET setting_flag='0' WHERE setting_type='$condtion'";
        if (mysqli_query($conn, $sql))
        {
            $data = "DEFAULT;";

            $len = strlen($data);
            $length = str_pad($len, 3, '0', STR_PAD_LEFT);
            $data = $length . ";" . $data;

            $response = "RES=" . $array_data[0] . ";" . $data;
        }
        break;

        case "ID_CHANGE":
        $sql = "UPDATE device_settings SET setting_flag='0' WHERE setting_type='$condtion'";
        if (mysqli_query($conn, $sql))
        {
            $set_sql = "SELECT * FROM history_device_id_change ORDER BY id DESC LIMIT 1";
            if (mysqli_query($conn, $set_sql))
            {
                $result = mysqli_query($conn, $set_sql);
                if (mysqli_num_rows($result) > 0)
                {
                    $r = mysqli_fetch_assoc($result);

                    $send_data = $r['new_id'];

                    $data = "DEVID=" . $send_data.";";

                    $len = strlen($data);
                    $length = str_pad($len, 3, '0', STR_PAD_LEFT);
                    $data = $length . ";" . $data;

                    $response = "RES=" . $array_data[0] . ";" . $data;
                }
            }
        }
        break;


        case "SERIAL_ID":
        $sql = "UPDATE device_settings SET setting_flag='2' WHERE setting_type='$condtion'";
        if (mysqli_query($conn, $sql))
        {
            $set_sql = "SELECT * FROM history_serial_id_change ORDER BY id DESC LIMIT 1";
            if (mysqli_query($conn, $set_sql))
            {
                $result = mysqli_query($conn, $set_sql);
                if (mysqli_num_rows($result) > 0)
                {
                    $r = mysqli_fetch_assoc($result);

                    $send_data = $r['serial_id'];

                    $data = "SNO=" . $send_data.";";

                    $len = strlen($data);
                    $length = str_pad($len, 3, '0', STR_PAD_LEFT);
                    $data = $length . ";" . $data;

                    $response = "RES=" . $array_data[0] . ";" . $data;
                }
            }
        }
        break;

        case "ENERGY_RESET":
        $sql = "UPDATE device_settings SET setting_flag='2' WHERE setting_type='$condtion'";
        if (mysqli_query($conn, $sql))
        {
            $set_sql = "SELECT * FROM history_energy_reset ORDER BY id DESC LIMIT 1";
            if (mysqli_query($conn, $set_sql))
            {
                $result = mysqli_query($conn, $set_sql);
                if (mysqli_num_rows($result) > 0)
                {
                    $r = mysqli_fetch_assoc($result);

                    $kwh = $r['kwh'];  
                    $kvah = $r['kvah'];

                    $data = "ECLR=" . $kwh.";". $kvah.";";

                    $len = strlen($data);
                    $length = str_pad($len, 3, '0', STR_PAD_LEFT);
                    $data = $length . ";" . $data;

                    $response = "RES=" . $array_data[0] . ";" . $data;
                }
            }
        }
        break;

        case "UNBALANCED_ERROR":
        $sql = "UPDATE device_settings SET setting_flag='2' WHERE setting_type='$condtion'";
        if (mysqli_query($conn, $sql))
        {
            $set_sql = "SELECT * FROM history_unbalanced_load_percentage ORDER BY id DESC LIMIT 1";
            if (mysqli_query($conn, $set_sql))
            {
                $result = mysqli_query($conn, $set_sql);
                if (mysqli_num_rows($result) > 0)
                {
                    $r = mysqli_fetch_assoc($result);

                    $deviation = $r['deviation'];  
                    $r_kva = $r['r_kva'];

                    $data = "UNBL=" . $deviation.";". $r_kva.";";

                    $len = strlen($data);
                    $length = str_pad($len, 3, '0', STR_PAD_LEFT);
                    $data = $length . ";" . $data;

                    $response = "RES=" . $array_data[0] . ";" . $data;
                }
            }
        }
        break;

        case "WIFI_CREDENTIALS":
        $sql = "UPDATE device_settings SET setting_flag='2' WHERE setting_type='$condtion'";
        if (mysqli_query($conn, $sql))
        {
            $set_sql = "SELECT * FROM history_wifi_credentials ORDER BY id DESC LIMIT 1";
            if (mysqli_query($conn, $set_sql))
            {
                $result = mysqli_query($conn, $set_sql);
                if (mysqli_num_rows($result) > 0)
                {
                    $r = mysqli_fetch_assoc($result);

                    $access_point_name = $r['access_point_name'];  
                    $pwd = $r['password'];

                    $data = "WIFI=" . $access_point_name.";". $pwd.";";

                    $len = strlen($data);
                    $length = str_pad($len, 3, '0', STR_PAD_LEFT);
                    $data = $length . ";" . $data;

                    $response = "RES=" . $array_data[0] . ";" . $data;
                }
            }
        }
        break;

        case "ANGLE":

        $sql = "UPDATE device_settings SET setting_flag='0' WHERE setting_type='$condtion'";
        if (mysqli_query($conn, $sql))
        {
            $sql = "UPDATE device_settings SET setting_flag='2' WHERE setting_type='$condtion'";
            if (mysqli_query($conn, $sql))
            {
                $set_sql = "SELECT * FROM history_angle_values ORDER BY id DESC LIMIT 1";
                if (mysqli_query($conn, $set_sql))
                {
                    $result = mysqli_query($conn, $set_sql);
                    if (mysqli_num_rows($result) > 0)
                    {
                        $r = mysqli_fetch_assoc($result);

                        $value_b_1 = (int)$r['angle_below_r'];
                        $value_b_2 = (int)$r['angle_below_y'];
                        $value_b_3 = (int)$r['angle_below_b'];
                        $value_a_1 = (int)$r['angle_above_r'];
                        $value_a_2 = (int)$r['angle_above_y'];
                        $value_a_3 = (int)$r['angle_above_b'];

                        $value_b = $value_b_1 . ";" . $value_b_2 . ";" . $value_b_3;
                        $value_a = $value_a_1 . ";" . $value_a_2 . ";" . $value_a_3;

                        $data = "ANGLE=" . $value_b . ";" . $value_a . ";";

                        $len = strlen($data);
                        $length = str_pad($len, 3, '0', STR_PAD_LEFT);
                        $data = $length . ";" . $data;

                        $response = "RES=" . $array_data[0] . ";" . $data;
                    }
                }
            }
        }
        break;

        case "TANKS_PRIORITY":

        $sql = "UPDATE device_settings SET setting_flag='0' WHERE setting_type='$condtion'";
        if (mysqli_query($conn, $sql))
        {
            $sql = "UPDATE device_settings SET setting_flag='2' WHERE setting_type='$condtion'";
            if (mysqli_query($conn, $sql))
            {


                $conn_tanks = mysqli_connect(HOST, USERNAME, PASSWORD);
                $motor_id=$array_data[0];
                $set_sql = "SELECT tank_id, priority FROM `motor_pumps`.`assigned_motor_tanks` WHERE motor_id='$motor_id' ORDER BY priority ASC";
                if (mysqli_query($conn_tanks, $set_sql))
                {
                    $result = mysqli_query($conn_tanks, $set_sql);
                    if (mysqli_num_rows($result) > 0)
                    {
                        $tanks_priority="";
                        while($r = mysqli_fetch_assoc($result))
                        {
                            $tanks_priority.=$r['tank_id'].":".$r['priority'].";";
                        }
                        $data = "TANKS_PRIORITY=" . $tanks_priority;

                        $len = strlen($data);
                        $length = str_pad($len, 3, '0', STR_PAD_LEFT);
                        $data = $length . ";" . $data;

                        $response = "RES=" . $array_data[0] . ";" . $data;
                    }
                }
                mysqli_close($conn_tanks);
            }
        }
        break;

        case "NA":

        $response = "No Data";

        break;

        default:
        $response = "No Data";

    }
    return $response;
}

function clear_ack($key, $conn)
{
    mysqli_query($conn, "UPDATE device_settings SET setting_flag='0' WHERE setting_type='$key' and setting_flag='2'");
}

function onoff_update($conn, $deviceid, $status, $date_time)
{
    $device_name = $deviceid;

    $device_name = get_name($deviceid, $conn);

    $deviceid_for_msg = "";
    if ($deviceid != $device_name)
    {
        $deviceid_for_msg = $device_name;
    }
    else
    {
        $deviceid_for_msg = $deviceid;
    }
    $on_command = "";
    if ($status == 1 || $status == 3 || $status == 4 || $status == 5)
    {
        if ($status == 1)
        {
            $on_command = "ON";
        }
        else if ($status == 3)
        {
            $on_command = "ON from SERVER";
        }
        else if ($status == 4)
        {
            $on_command = "ON from WIFI APP";
        }
        else if ($status == 5)
        {
            $on_command = "ON MANUALLY";
        }

        $msg = "ID:$deviceid_for_msg Motor Switched $on_command ";
    }
    else
    {
        $on_command = "OFF";
        $msg = "ID:$deviceid_for_msg Motor Switched OFF";
    }
    mysqli_query($conn, "INSERT INTO `on_off_events_log` (`device_id`, `event`, `date_time`) VALUES ( 'deviceid', '$on_command', '$date_time');");

    send_message($msg, $conn, $deviceid);

}

function voltage_current_alerts($deviceid, $type, $phase, $normal_phases, $conn, $status, $v1, $v2, $v3, $c1, $c2, $c3, $date)
{
    $string = str_replace('/', '-', $date);
    $date_new = date_create($string);
    $date = @date_format($date_new, "Y/m/d H:i:s");

    if (!strlen($date))
    {
        date_default_timezone_set('Asia/Kolkata');
        $date = date("Y-m-d H:i:s");
    }

    $date_sms = date_format(date_create($date) , "H:i:s d/m/Y ");

    $response = "";

    $device_name = $deviceid;
    $device_name = get_name($deviceid, $conn);

    $deviceid_for_msg = "";
    if ($deviceid != $device_name)
    {
        $deviceid_for_msg = $device_name;
    }
    else
    {
        $deviceid_for_msg = $deviceid;
    }

    switch ($type)
    {
        case "PHASEFAIL":

        if ($normal_phases == "1")
        {
            $phase = "All Phases Normal";
        }
        else if ($normal_phases == "0")
        {
            $phase = $phase;
        }
        else
        {
            $phase = $phase . " (" . $normal_phases . " Normal)";
        }

        $sql = "INSERT INTO `phase_alerts` (`device_id`, `status`, `phases`, `v_ph1`, `v_ph2`, `v_ph3`, `i_ph1`, `i_ph2`, `i_ph3`, `date_time`) VALUES ( '$deviceid', '$type', '$phase', '$v1', '$v2', '$v3', '$c1', '$c2', '$c3', '$date')";

        mysqli_query($conn, $sql);

        if ($status == 1)
        {
            $msg = "ID:" . $deviceid_for_msg . " Power failure in " . $phase . ", Voltages(V): R=" . $v1 . ", Y=" . $v2 . ", Y=" . $v3 . " TIME:" . $date_sms . "";
            return send_message($msg, $conn, $deviceid);

        }
        else
        {
            $msg = "ID:" . $deviceid_for_msg . " Power Resumed in all phases, Voltages(V): R=" . $v1 . ", Y=" . $v2 . ", B=" . $v3 . " TIME:" . $date_sms . "";
            return send_message($msg, $conn, $deviceid);

        }

        break;

        case "CURRENT":

        if ($normal_phases == "1")
        {
            $phase = "ALL Phases Normal";
        }
        else if ($normal_phases == "0")
        {
            $phase = $phase;
        }
        else
        {
            $phase = $phase . " (" . $normal_phases . " Normal)";
        }

        $sql = "INSERT INTO `phase_alerts` (`device_id`, `status`, `phases`, `v_ph1`, `v_ph2`, `v_ph3`, `i_ph1`, `i_ph2`, `i_ph3`, `date_time`) VALUES ( '$deviceid', 'OVER CURRENT', '$phase', '$v1', '$v2', '$v3', '$c1', '$c2', '$c3', '$date')";
        mysqli_query($conn, $sql);

        if ($status == 1)
        {
            $msg = "ID:" . $deviceid_for_msg . "  Overload = " . $phase . ", Current(A): R=" . $c1 . ", Y=" . $c2 . ", B=" . $c3 . " TIME:" . $date_sms . "";
            return send_message($msg, $conn, $deviceid);

        }
        else
        {
            $msg = "ID:" . $deviceid_for_msg . " Phases Load Normal, Current(A): R=" . $c1 . ", Y=" . $c2 . ", B=" . $c3 . " TIME:" . $date_sms . "";
            return send_message($msg, $conn, $deviceid);

        }

        break;

        case "HIGH":

        if ($normal_phases == "1")
        {
            $phase = "All Phases Normal";
        }
        else if ($normal_phases == "0")
        {
            $phase = $phase;
        }
        else
        {
            $phase = $phase . " (" . $normal_phases . " Normal)";
        }

        $sql = "INSERT INTO `phase_alerts` (`device_id`, `status`, `phases`, `v_ph1`, `v_ph2`, `v_ph3`, `i_ph1`, `i_ph2`, `i_ph3`, `date_time`) VALUES ( '$deviceid', '$type Voltage', '$phase', '$v1', '$v2', '$v3', '$c1', '$c2', '$c3', '$date')";
        mysqli_query($conn, $sql);

        if ($status == 1)
        {
            $msg = "ID:" . $deviceid_for_msg . "  High Voltage= " . $phase . ", Voltages(V): R=" . $v1 . ", Y=" . $v2 . ", B=" . $v3 . " TIME:" . $date_sms . "";
            return send_message($msg, $conn, $deviceid);
        }
        else
        {
            $msg = "ID:" . $deviceid_for_msg . " Phases Voltage Normal, Voltages(V): R=" . $v1 . ", Y=" . $v2 . ", B=" . $v3 . " TIME:" . $date_sms . "";
            return send_message($msg, $conn, $deviceid);

        }

        break;

        case "LOW":

        if ($normal_phases == "1")
        {
            $phase = "ALL Phases Normal";
        }
        else if ($normal_phases == "0")
        {
            $phase = $phase;
        }
        else
        {
            $phase = $phase . " (" . $normal_phases . " Normal)";
        }
        $sql = "INSERT INTO `phase_alerts` (`device_id`, `status`, `phases`, `v_ph1`, `v_ph2`, `v_ph3`, `i_ph1`, `i_ph2`, `i_ph3`, `date_time`) VALUES ( '$deviceid', '$type Voltage', '$phase', '$v1', '$v2', '$v3', '$c1', '$c2', '$c3', '$date')";
        mysqli_query($conn, $sql);

        if ($status == 1)
        {
            if ($v1 > 0.9 || $v2 > 0.9 || $v3 > 0.9)
            {
                $msg = "ID:" . $deviceid_for_msg . " Low Voltage = " . $phase . ", Voltages(V): R=" . $v1 . ", Y=" . $v2 . ", B=" . $v3 . " TIME:" . $date_sms . "";
                return send_message($msg, $conn, $deviceid);
            }

        }
        else
        {
            $msg = "ID:" . $deviceid_for_msg . " Phases Voltage is Normal, Voltages(V): R=" . $v1 . ", Y=" . $v2 . ", B=" . $v3 . " TIME:" . $date_sms . "";
            return send_message($msg, $conn, $deviceid);

        }

        break;

        case "MCB":

        if ($normal_phases == "1")
        {
            $phase = " Motor Switched OFF (MANUAL / STARTER TRIPPED) ";
        }
        else if ($normal_phases == "0")
        {
            $phase = " Motor Switched OFF (MANUAL / STARTER TRIPPED)";
        }
        else
        {
            $phase = " Motor Switched OFF (MANUAL / STARTER TRIPPED)";

        }

        if ($status == 1)
        {
            $sql = "INSERT INTO `phase_alerts` (`device_id`, `status`, `phases`, `v_ph1`, `v_ph2`, `v_ph3`, `i_ph1`, `i_ph2`, `i_ph3`, `date_time`) VALUES ( '$deviceid', 'MOTOR OFF', '$phase', '$v1', '$v2', '$v3', '$c1', '$c2', '$c3', '$date')";
            mysqli_query($conn, $sql);
            $msg = "ID:" . $deviceid_for_msg . $phase . " TIME:" . $date_sms . "";

            return send_message($msg, $conn, $deviceid);
        }
        else
        {
            $sql = "INSERT INTO `phase_alerts` (`device_id`, `status`, `phases`, `v_ph1`, `v_ph2`, `v_ph3`, `i_ph1`, `i_ph2`, `i_ph3`, `date_time`) VALUES ('$deviceid', 'MOTOR ON', 'Motor Switched ON', '$v1', '$v2', '$v3', '$c1', '$c2', '$c3', '$date')";
            mysqli_query($conn, $sql);

            $msg = "ID:" . $deviceid_for_msg . " Motor Switched ON, TIME:" . $date_sms;

            return send_message($msg, $conn, $deviceid);

        }

        break;

        default:
        return "ERROR";
    }

}

    ///////////////////////////////////// INPUT POWER FAIL/ RESUME Alert ///////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////


function ip_power_smps_status_update($conn, $deviceid, $status, $voltage, $date)
{
    date_default_timezone_set('Asia/Kolkata');
    $string = str_replace('/', '-', $date);
    $date_new = date_create($string);
    $date = @date_format($date_new, "Y/m/d H:i:s");

    $BSF_alert = "";

    $device_name = $deviceid;

    $device_name = get_name($deviceid, $conn);

    $deviceid_for_msg = "";
    if ($deviceid != $device_name)
    {
        $deviceid_for_msg = $device_name;
    }
    else
    {
        $deviceid_for_msg = $deviceid;
    }

    $ps_query = mysqli_query($conn, "SELECT * FROM `alert_power_supply_check` ORDER BY id DESC lIMIT 1");

    if (mysqli_num_rows($ps_query) > 0)
    {
        $r = mysqli_fetch_assoc($ps_query);

        $pf_status = $r['ps_status'];

    }

    $send_mail = false;
    $msg = "";
    if ($status == "0")
    {
        $send_mail = true;

        if ($pf_status === "ON")
        {
            mysqli_query($conn, "INSERT INTO `alert_power_supply` (`device_id`, `mobile_number`, `message`, `date_time`) VALUES ('$deviceid', '', 'INPUT_FAIL', '$date')");
        }

        $update_sql = "INSERT INTO `alert_power_supply_check` ( `ps_status`, `battery_voltage`, `date_time`) VALUES ( 'OFF', '$voltage', '$date');";
        mysqli_query($conn, $update_sql);
        $msg = "ID:" . $deviceid_for_msg . ", Power Failure. TIME:" . $date . "";
        send_message($msg, $conn, $deviceid);
    }

    else if ($status == "1")
    {
        $send_mail = true;
        if ($pf_status === "OFF")
        {
            mysqli_query($conn, "INSERT INTO `alert_power_supply` (`device_id`, `mobile_number`, `message`, `date_time`) VALUES ('$deviceid', '', 'INPUT_RESUME', '$date')");
        }
        $update_sql = "INSERT INTO `alert_power_supply_check` ( `ps_status`, `battery_voltage`, `date_time`) VALUES ( 'ON', '$voltage', '$date');";
        mysqli_query($conn, $update_sql);

        $msg = "ID:" . $deviceid_for_msg . ", Power Resumed. TIME:" . $date . "";
        send_message($msg, $conn, $deviceid);

    }
    else if ($status == "2")
    {
        $send_mail = true;
        $send_alert_msg = " SMPS-1 Fail(" . $voltage . " mV)";
        $msg = "SMPS-1 Fail in the Device ID : " . $deviceid;

        mysqli_query($conn, "INSERT INTO `smps_status`(`device_id`, `smps`, `smps_status`, `date_time`) VALUES ('$deviceid', 'SMPS_1', 'FAIL', '$date')");

    }
    else if ($status == "3")
    {
        $send_mail = true;
        $send_alert_msg = " SMPS-1 Resumed(" . $voltage . " mV)";
        $msg = "SMPS-1 Resumed in the Device ID : " . $deviceid;
        mysqli_query($conn, "INSERT INTO `smps_status`(`device_id`, `smps`, `smps_status`, `date_time`) VALUES ('$deviceid', 'SMPS_1', 'RESUMED', '$date')");

    }
    else if ($status == "4")
    {
        $send_mail = true;
        $send_alert_msg = " SMPS-2 Fail(" . $voltage . " mV)";
        $msg = "SMPS-1 Fail in the Device ID : " . $deviceid;
        mysqli_query($conn, "INSERT INTO `smps_status`(`device_id`, `smps`, `smps_status`, `date_time`) VALUES ('$deviceid', 'SMPS_2', 'FAIL', '$date')");
    }
    else if ($status == "5")
    {
        $send_mail = true;
        $send_alert_msg = " SMPS-2 Resumed(" . $voltage . " mV)";
        $msg = "SMPS-2 Resumed in the Device ID : " . $deviceid;

        mysqli_query($conn, "INSERT INTO `smps_status`(`device_id`, `smps`, `smps_status`, `date_time`) VALUES ('$deviceid', 'SMPS_2', 'RESUMED', '$date')");

    }
    else if ($status == "6")
    {
        $send_mail = true;
        $send_alert_msg = " SMPS-1 & SMPS-2 Fail(" . $voltage . " mV)";
        $msg = "SMPS-1 & SMPS-2 Fail in the Device ID : " . $deviceid;

        mysqli_query($conn, "INSERT INTO `smps_status`(`device_id`, `smps`, `smps_status`, `date_time`) VALUES ('$deviceid', 'SMPS_1', 'FAIL', '$date')");
        mysqli_query($conn, "INSERT INTO `smps_status`(`device_id`, `smps`, `smps_status`, `date_time`) VALUES ('$deviceid', 'SMPS_2', 'FAIL', '$date')");

    }
    else if ($status == "7")
    {
        $send_mail = true;
        $send_alert_msg = " SMPS-1 & SMPS-2 Resumed(" . $voltage . " mV)";
        $msg = "SMPS-1 & SMPS-2 Resumed in the Device ID : " . $deviceid;

        mysqli_query($conn, "INSERT INTO `smps_status`(`device_id`, `smps`, `smps_status`, `date_time`) VALUES ('$deviceid', 'SMPS_1', 'RESUMED', '$date')");
        mysqli_query($conn, "INSERT INTO `smps_status`(`device_id`, `smps`, `smps_status`, `date_time`) VALUES ('$deviceid', 'SMPS_2', 'RESUMED', '$date')");

    }

    if ($send_mail && $msg != "")
    {
        $enable_disable = 0;
        $sql = "SELECT * FROM `user_notification_settings` WHERE alert_type='SMPS' ORDER BY id DESC LIMIT 1";
        if (mysqli_query($conn, $sql))
        {
            $result = mysqli_query($conn, $sql);
            if (mysqli_num_rows($result) > 0)
            {
                while ($r = mysqli_fetch_assoc($result))
                {
                    $enable_disable = $r['alert_en_dis'];
                    $send_interval = $r['alert_time_interval'];
                }
            }
        }
        if ($enable_disable == 1)
        {
            $SUBJECT = "SMPS Alert of " . $deviceid;
            $emial_send = "";
            if ($GLOBALS['live'] == 1)
            {
                $emial_send = "swamy@istlabs.in,sampath@istlabs.in,ananth@istlabs.in";
            }
            else
            {
                $emial_send = "thirupathi@istlabs.in";
                send_message($msg, $conn, $deviceid);
            }

            //send_email_alert($emial_send, $msg, $SUBJECT);
        }
    }

}

    /////////////////////////////////// SMS Sending  message      ///////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////


function send_message($msg, $conn, $device_id)
{
    date_default_timezone_set('Asia/Kolkata');
    $date = date("Y-m-d H:i:s");
    $chart_id = "";
    $sql = "INSERT INTO `message` (`device_id`, `mobile_number`, `message`, `date_time`) VALUES ( '$device_id','..','$msg','$date')";
    mysqli_query($conn, $sql);

    $conn1 = mysqli_connect(HOST, USERNAME, PASSWORD, DB1);
    if (!$conn1)
    {
        die("Connection failed");
    }
    else
    {
        $msg = str_replace("&", "and", $msg);
        $msg = str_replace("High", "high", $msg);
        $msg = str_replace("HIGH", "high", $msg);
        $msg = str_replace("Voltage", "voltage", $msg);

        $sql = "SELECT * FROM `telegram_groups` WHERE id in (SELECT group_id FROM `telegram_groups_devices` WHERE device_id='$device_id')";
        if (mysqli_query($conn1, $sql))
        {
            $result = mysqli_query($conn1, $sql);
            if (mysqli_num_rows($result) > 0)
            {
                while ($r = mysqli_fetch_assoc($result))
                {
                    $chart_id = $r['chat_id'];
                    $token = $r['token'];

                    if ($chart_id != "" && $token != "")
                    {
                        $TG_ALERT_URL = 'https://api.telegram.org/' . $token . '/sendMessage?chat_id=' . $chart_id . '&text=' . $msg;
                        file_get_contents($TG_ALERT_URL);
                    }
                }
            }
        }
    }
    mysqli_close($conn1);
}

function send_email_alert($emial_send, $msg, $SUBJECT)
{
    ob_start();
    try
    {
        $bodyContent = '<h4>ITMS Alerts</h4>' . "\r\n";
        $bodyContent .= $msg;

        $SUBJECT = $SUBJECT;
        $HEADER_NAME = "ITMS Device Alerts";
        $BODY = $bodyContent;
        $CC_MAIL = "thirupathi@istlabs.in";
        $TO_MAIL = "";
        if ($GLOBALS['live'] == 1)
        {
            $TO_MAIL = $emial_send;
        }
        else
        {
            $TO_MAIL = "thirupathi@istlabs.in";
        }

        $mail = "SUBJECT=" . $SUBJECT . "&HEADER_NAME=" . $HEADER_NAME . "&BODY=" . $BODY . "&TO_MAIL=" . $TO_MAIL . "&CC_MAIL=" . $CC_MAIL;

        $ch = curl_init('https://istlabsonline.com/API_eMail/mail_api.php');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $mail);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_exec($ch);
        curl_close($ch);
    }
    catch(Exception $e)
    {

    }
    ob_end_clean();

}
function get_name($device_name, $conn)
{
    $sql = "SELECT * FROM `device_name` ORDER BY id DESC lIMIT 1";
    if (mysqli_query($conn, $sql))
    {
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0)
        {
            while ($r = mysqli_fetch_assoc($result))
            {
                $device_name = $r['device_name'];
            }
        }
    }
        //return $device_name = (strlen($device_name) > 15) ? substr($device_name, 0, 15) : $device_name;
    return $device_name;
}

function update_ping($deviceid, $conn)
{
    try
    {
        $date_time = date("Y-m-d H:i:s");
        mysqli_query($conn, "INSERT INTO `communication_check` ( `device_id`, `date_time`) VALUES('$deviceid', '$date_time') ON DUPLICATE KEY UPDATE device_id='$deviceid', date_time='$date_time'");
    }
    catch(exeption $e)
    {

    }

}

function update($deviceid)
{
    date_default_timezone_set('Asia/Kolkata');
    $date_time = date("Y-m-d H:i:s");
    $time = date("H:i:s");
    $date = date("Y-m-d ");

    try
    {
        $log_conn = mysqli_connect(HOST, USERNAME, PASSWORD, DB2);
        if (!$log_conn)
        {
            die("Connection failed: ");
        }
        else
        {
            $device_exist = mysqli_query($log_conn, "SELECT * FROM devices WHERE device ='$deviceid'");

            if (mysqli_num_rows($device_exist) > 0)
            {
                mysqli_query($log_conn, "UPDATE `devices` SET `status`='1' WHERE device = '$deviceid' ");
            }
            else
            {
                mysqli_query($log_conn, "INSERT INTO `devices` ( `device`, `status`) VALUES ( '$deviceid',  '1')");
            }

                /////////////////////////////////////////////////////////////
            $exist_row = mysqli_query($log_conn, "SELECT * FROM device_logs WHERE device_id ='$deviceid'");

            if (mysqli_num_rows($exist_row) > 0)
            {
                mysqli_query($log_conn, "UPDATE `device_logs` SET `time`='$time', `date`='$date', date_time = '$date_time', `notification_flag`='0' WHERE device_id = '$deviceid' ");
            }
            else
            {
                mysqli_query($log_conn, "INSERT INTO `device_logs` ( `device_id`, `time`, `date`, `date_time`, `notification_flag`) VALUES ( '$deviceid', '$time', '$date', '$date_time', '0')");
            }

            mysqli_close($log_conn);
        }
    }
    catch(exeption $e)
    {

    }
}

function store_data($data, $conn)
{
    try
    {
        date_default_timezone_set('Asia/Kolkata');
        $date = date("Y-m-d H:i:s");

        mysqli_query($conn, "INSERT INTO `check_main_frame` ( `frame_type`,`frame`, `date_time`) VALUES ('REQUEST','$data', '$date')");
    }
    catch(Exception $e)
    {

    }
}


function tanksUpdates($data, $conn)
{
    $array_data = explode(';', $data);
    $sql = "";
    $response = "";
    $condtion = "NA";
    $check = array();
    $length = 0;
    global $dbAll;

    $sql = "SELECT * FROM `$dbAll`.`tank_updates` WHERE tank_id='$array_data[0]' AND setting_type IN ('VALVE_ONOFF', 'TANK_CAPACITY', 'SOFTWARE') ORDER BY setting_type DESC";

    if (mysqli_query($conn, $sql))
    {
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0)
        {
            while ($r = mysqli_fetch_assoc($result))
            {
                $check[] = array(
                    'type' => $r['setting_type'],
                    'flag' => $r['setting_flag']
                );
            }
        }
        else
        {
            $response = "000;No data";
        }
    }

    $values = json_encode($check);
    $set_list = json_decode($values);
    foreach ($set_list as $key => $value)
    {
        $condtion = $value->type;

        $set_flag = $value->flag;

        if ($set_flag == 1 || $set_flag == 2)
        {
            break;
        }
        else
        {
            $condtion = "NA";
        }
    }
    switch ($condtion)
    {
        case "VALVE_ONOFF":
        $sql = "UPDATE `$dbAll`.`tank_updates` SET setting_flag='2' WHERE tank_id='$array_data[0]' AND setting_type='$condtion' ";
        if (mysqli_query($conn, $sql))
        {
            $set_sql = "SELECT * FROM `$dbAll`.`tank_value_updates` WHERE tank_id='$array_data[0]' ORDER BY id DESC LIMIT 1";
            if (mysqli_query($conn, $set_sql))
            {
                $result = mysqli_query($conn, $set_sql);
                if (mysqli_num_rows($result) > 0)
                {
                    $r = mysqli_fetch_assoc($result);

                    $status = (int)$r['valve_status'];

                    $data = "VALVE=" . $status . ";";

                    $len = strlen($data);
                    $length = str_pad($len, 3, '0', STR_PAD_LEFT);
                    $data = $length . ";" . $data;

                    $response = "RES=" . $array_data[0] . ";" . $data;
                }
            }
        }
        break;

        case "TANK_CAPACITY":
        $sql = "UPDATE `$dbAll`.`tank_updates` SET setting_flag='2' WHERE tank_id='$array_data[0]' AND setting_type='$condtion' ";
        if (mysqli_query($conn, $sql))
        {
            $set_sql = "SELECT * FROM `$dbAll`.`assigned_motor_tanks` WHERE tank_id='$array_data[0]' ORDER BY id DESC LIMIT 1";
            if (mysqli_query($conn, $set_sql))
            {
                $result = mysqli_query($conn, $set_sql);
                if (mysqli_num_rows($result) > 0)
                {
                    $r = mysqli_fetch_assoc($result);

                    $status = (int)$r['capacity'];

                    $data = "CAPACITY=" . $status . ";";

                    $len = strlen($data);
                    $length = str_pad($len, 3, '0', STR_PAD_LEFT);
                    $data = $length . ";" . $data;

                    $response = "RES=" . $array_data[0] . ";" . $data;
                }
            }
        }
        break;

        case "SOFTWARE":
        $sql = "UPDATE `$dbAll`.`tank_updates` SET setting_flag='2' WHERE tank_id='$array_data[0]' AND setting_type='$condtion'";
        if (mysqli_query($conn, $sql))
        {
            $data = "S_UPDATE;";
            $len = strlen($data);
            $length = str_pad($len, 3, '0', STR_PAD_LEFT);
            $data = $length . ";" . $data;

            $response = "RES=" . $array_data[0] . ";" . $data;
        }

        break;
        case "NA":
        $response = "No Data";
        break;

        default:
        $response = "No Data";

    }
    return $response;
}

function  clearTanksAck($key, $conn, $tank_id)
{
    global $dbAll;
    mysqli_query($conn, "UPDATE  `$dbAll`.`tank_updates` SET setting_flag='0' WHERE tank_id='$tank_id' AND setting_type='$key' AND setting_flag='2' ");
}


?>
