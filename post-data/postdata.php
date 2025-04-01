<?php
ignore_user_abort(true);
set_time_limit(0);

ob_start();

date_default_timezone_set('Asia/Kolkata');
$live=0;
$response="";
$data= "";
$motor_data= "";
$ip_address = $_SERVER['REMOTE_ADDR'];
define('DB','motor_pumps');
if($ip_address=="::1")
{
	define('HOST','localhost');
	define('USERNAME', 'root');
	define('PASSWORD','123456');



	/*INSERT INTO `tanks_status_history` (`tank_id`, `flow_rate`, `valve_status`, `estimated_time`, `consumed_time`, `tank_status`, `current_status`, `comsumed_water`, `voltage_1`, `voltage_2`, `date_time`) VALUES ('TANK_1', '150', '1', '200', '50', '0', 'full', '2565', '248', '250', current_timestamp());*/

	$date_update=date("y/m/d H:i:s");
	
	//$data= "TANK_1;150;1;200;50;0;2565;248;250;TANK;".$date_update."#TANK_2;150;0;200;50;1;2565;248;250;MOTOR;".$date_update."@
	//0;6000";

	//$data= "MTMS_1;246.02;242.79;245.31;0.01;0.01;0.01;0.01;0.000;0.000;0.000;1061.8;1076.8;0.00;101;1;24/1/23 16:0:10@RPTR_102;57830";


	$motor_data = "MCMS_10;1;2;3;4;5;6;7;8;9;10;11;12;13;14;15;16;17;18;19;20;21;22;23;24;25;26;27;28;29;30;31;32;33;34;35;36;37;38;39;40;41;" . $date_update . ";49933";
}
else
{
	$live=1;
	define('HOST','103.101.59.93');
	define('USERNAME', 'istlabsonline_db_user');
	define('PASSWORD', 'istlabsonline_db_pass');


	if (isset($_POST['a'])) {
		$motor_data = $_POST['a'];
	}
	if (isset($_POST['b'])) {
		$data = $_POST['b'];
	}
	

}


if($data!="")
{
	$response="";
	$crc_string= strrchr( $data, ';' ); 
	$crc_compare=trim($crc_string,";"); 
	$crc_len=strlen($crc_string)-1; 
	$data_crc=substr($data, 0, -$crc_len); 
	$data_crc="b=".$data_crc; 
	$for_array=substr($data, 0, -strlen($crc_string)); 
	
	

	$get_gateway_id=explode('@', $for_array);
	$array_frame_data = explode('#', $get_gateway_id[0]);



	//$array_frame_data = explode('#', $for_array);   
	$crc = 0xFFFF;
	for ($i = 0; $i < strlen($data_crc); $i++)
	{
		$crc ^=ord($data_crc[$i]);
		for ($j = 8; $j !=0; $j--)
		{
			if (($crc & 0x0001) !=0)
			{
				$crc >>= 1;
				$crc ^= 0xA001;
			}
			else
				$crc >>= 1;
		}
	}

	try{

		$conn =  mysqli_connect(HOST,USERNAME,PASSWORD,DB);
		if (!$conn) 
		{
			die("Connection failed: " . mysqli_connect_error());
		}
		else
		{

			if($crc== $crc_compare)
			{
				if(count($get_gateway_id)==2)
				{
					//$response= mainfunction($get_gateway_id[1], $conn) ;
					//$response=$get_gateway_id[1];
					$response= "";
				}
				else
				{
					$response="Process will continue "; 
				}
				//echo $response;
				/*http_response_code(202);
				header('Connection: close');
				header('Content-Length: '.ob_get_length());
				ob_end_flush();
				ob_flush();
				flush();*/

				for($k=0; $k<count($array_frame_data);$k++)
				{


					$array_data = explode(';', $array_frame_data[$k]);
					$insertdata="";


					$value_status=0;
					for ($i=0; $i < count($array_data); $i++) 
					{ 

						if($i==3||$i==4)
						{
							$minutes=$array_data[$i];
							$hours = floor($minutes / 60).':'.($minutes -   floor($minutes / 60) * 60);

							$array_data[$i]=$hours;
						}

						if($i==2)
						{
							$value_status=$array_data[2];
							if($array_data[2]==0){

								$array_data[2]="Closed";
							}
							elseif($array_data[2]==1){

								$array_data[2]="Open";
							}

						}
						if($i==6)
						{
							if($value_status==1)
							{
								$add_sts="Filling";
								$insertdata=$insertdata."'".$add_sts."',";
							}
							elseif($value_status==0)
							{
								$add_sts="Full";
								$insertdata=$insertdata."'".$add_sts."',";
							}

						}
						if($i==5)
						{
							if($array_data[$i]==0)
							{
								$array_data[$i]="Empty";
							}
							elseif($array_data[$i]==1)
							{
								$array_data[$i]="Full";
							}

						}

						$insertdata=$insertdata."'".$array_data[$i]."',";
						
					}

					$insertdata=substr($insertdata, 0, -1);

					$sql  = "";

					$sql  = "INSERT IGNORE INTO `tanks_status_history` (`tank_id`, `flow_rate`, `valve_status`, `estimated_time`, `consumed_time`, `tank_status`, `current_status`, `comsumed_water`, `voltage_1`, `voltage_2`, `gateway_id`, `date_time`) VALUES (".$insertdata.")";
					

					if (mysqli_query($conn, $sql)) 
					{

						list($tank_id, $flow_rate, $valve_status, $estimated_time, $consumed_time, $tank_status, $current_status, $comsumed_water, $voltage_1, $voltage_2, $gateway_id, $date_time) = explode(",", $insertdata);


						$sql_update = " INSERT INTO `tanks_status`  (`tank_id`, `flow_rate`, `valve_status`, `estimated_time`, `consumed_time`, `tank_status`, `current_status`, `comsumed_water`, `voltage_1`, `voltage_2`, `gateway_id`, `date_time`)  VALUES  ($tank_id, $flow_rate, $valve_status, $estimated_time, $consumed_time, $tank_status, $current_status, $comsumed_water, $voltage_1, $voltage_2, $gateway_id, $date_time)  ON DUPLICATE KEY UPDATE  `flow_rate` = VALUES(`flow_rate`),  `valve_status` = VALUES(`valve_status`),  `estimated_time` = VALUES(`estimated_time`),  `consumed_time` = VALUES(`consumed_time`),  `tank_status` = VALUES(`tank_status`),  `current_status` = VALUES(`current_status`),  `comsumed_water` = VALUES(`comsumed_water`),  `voltage_1` = VALUES(`voltage_1`),  `voltage_2` = VALUES(`voltage_2`),  `gateway_id` = VALUES(`gateway_id`),  `date_time` = VALUES(`date_time`)";

						mysqli_query($conn, $sql_update);

					}
				}
				http_response_code(202);
				
			}
			else
			{
				http_response_code(202);
			} 
			mysqli_close($conn); 
		}


	}
	catch(exception $ex){
	//	$response="No Data";
	}

	/*if($response=="")
	{
		$response="No Data";
	}
	echo $response;*/
}
if($motor_data!="")
{

	$data=$motor_data;
    $crc_string = strrchr($data, ';'); // get the crc word from data
    $crc_compare = trim($crc_string, ";"); // getting crc value from the data
    $crc_len = strlen($crc_string) - 1; // getting the length of the crc word
    $data_crc = substr($data, 0, -$crc_len); // removing last crc word
    $data_crc = "a=" . $data_crc;
    $for_array = substr($data, 0, -strlen($crc_string)); // removing array crc from data
    $array_data = explode(';', $for_array); //converting data to array
    $crc = 0xFFFF;
    for ($i = 0;$i < strlen($data_crc);$i++)
    {
    	$crc ^= ord($data_crc[$i]);

    	for ($j = 8;$j != 0;$j--)
    	{
    		if (($crc & 0x0001) != 0)
    		{
    			$crc >>= 1;
    			$crc ^= 0xA001;
    		}
    		else $crc >>= 1;
    	}
    }

    $db = strtolower($array_data[0]);

    /*if ($GLOBALS['live'] == 1)
    {
    	update($array_data[0]);
    }*/

    $conn = mysqli_connect(HOST, USERNAME, PASSWORD, $db);
    if (!$conn)
    {
    	die("Connection failed: " . mysqli_connect_error());
    }
    else
    {

        /////////////////////////////  IOT Communication break check ////////////////////////////////
    	try 
    	{
    		date_default_timezone_set("Asia/Kolkata");
    		$server_time = date("Y-m-d H:i:s");
    		$frame_date_time = $array_data[count($array_data) - 1];
    		$ping_date_time = $server_time;
    		$prev_frame_date_time = "";
    		$device_break_down = 0;
    		$pres_frame_date = "";
    		try 
    		{
    			$sql_frame_time = "SELECT Date_Time FROM frame_data WHERE Date_Time NOT LIKE '%0000-00-00 00:00:00%'  ORDER BY Date_Time DESC LIMIT 1";
    			if (mysqli_query($conn, $sql_frame_time)) {
    				$result_frame_time = mysqli_query($conn, $sql_frame_time);
    				$count_frame_time = mysqli_num_rows($result_frame_time);
    				if ($count_frame_time > 0) {
    					$r_fr = mysqli_fetch_assoc($result_frame_time);
    					$prev_frame_date_time = $r_fr['Date_Time'];
    				}
    			}
    			if ($prev_frame_date_time != "") 
    			{
    				$string_date = str_replace('/', '-', $array_data[count($array_data) - 1]);

                            // $date_new_string = date_create($string_date);
                            // $pres_frame_date = date("Y-m-d H:i:s", $date_new_string);

    				$date = new DateTime($string_date);
    				$pres_frame_date = $date->format('Y-m-d H:i:s');

    				$current_time = date("Y-m-d H:i:s", strtotime($server_time) + 300);
    				$aa = strtotime($pres_frame_date);
    				$bb = strtotime($current_time);
    				$cc = strtotime($prev_frame_date_time);

    				if ($aa < $bb && $aa > $cc) 
    				{
    					$aa = (int)($aa / 60);
    					$cc = (int)($cc / 60);
    					$device_break_down = $aa - $cc;
    					if ($device_break_down <= 2) 
    					{
    						$device_break_down = 0;
    					}
    				}
    			}
    		}
    		catch(Exception $e) {
    		}
    		$sql = "SELECT id, ping_date_time FROM device_communication_break ORDER BY id DESC LIMIT 1";
    		if (mysqli_query($conn, $sql)) 
    		{
    			$result = mysqli_query($conn, $sql);
    			$count = mysqli_num_rows($result);
    			if ($count >= 1) {
    				$r = mysqli_fetch_assoc($result);
    				$date_time = $r['ping_date_time'];
    				$s_id = $r['id'];
                            //$main_min= strtotime($frame_date_time);
    				$server_min = (int)(strtotime($server_time) / 60);
    				$old_server_min = (int)(strtotime($date_time) / 60);
    				$ping_min = (int)(strtotime($ping_date_time) / 60);
    				$server_diff = $server_min - $old_server_min;
    				$ping_diff = $server_min - $ping_min;

    				if ($server_diff > 2) 
    				{
    					mysqli_query($conn, "INSERT INTO `device_communication_break` ( `previous_server_date_time`, `present_server_date_time`, `frame_date_time`, `ping_date_time`, `break_time`,`device_break_down_time`) VALUES ( '$date_time', '$server_time', '$frame_date_time','$server_time' , '$server_diff','$device_break_down');");

    				} 
    				else if ($device_break_down > 2 && $server_diff <3) {

    					$pre_d_time = 0;
    					$_update_s_no = 0;
    					$sql_prev_time_to_update = "SELECT `device_break_down_time`, `id` FROM `device_communication_break`  WHERE present_server_date_time >='$pres_frame_date' or present_server_date_time <=(SELECT present_server_date_time FROM `device_communication_break`  WHERE present_server_date_time <='$pres_frame_date' ORDER BY `id` DESC limit 1) limit 1";

    					if (mysqli_query($conn, $sql_prev_time_to_update)) 
    					{
    						$result_down_time_update = mysqli_query($conn, $sql_prev_time_to_update);
    						$down_time_update_time = mysqli_num_rows($result_down_time_update);
    						if ($down_time_update_time > 0) 
    						{
    							$r_update = mysqli_fetch_assoc($result_down_time_update);

    							$_update_s_no = $r_update["id"];
    							$pre_d_time = $r_update["device_break_down_time"];
    						}

    					}           
    					$device_break_down = $device_break_down + $pre_d_time;

    					$up_sql="UPDATE `device_communication_break` SET `device_break_down_time` = '$device_break_down' WHERE `id`='$_update_s_no' ";
    					mysqli_query($conn, $up_sql);
    				}
    				else 
    				{
    					mysqli_query($conn, "UPDATE device_communication_break SET ping_date_time ='$server_time' WHERE id='$s_id'");
    				}

    			} else {
    				$ins_sql="INSERT INTO `device_communication_break` (`previous_server_date_time`, `present_server_date_time`, `frame_date_time`, `ping_date_time`, `break_time`, `device_break_down_time`) VALUES ( '$server_time', '$server_time', '$frame_date_time','$server_time' , '0', '0');";
    				mysqli_query($conn, $ins_sql);
    			}
    		}
    	}
    	catch(Exception $e) {
    	}

        ////////////////////////////////////////////////////////////////////////////////////////
    	$device_id = $array_data[0]; 
    	$running_status = "Stopped"; 
    	$flow_rate = 0; 
    	$date_time = date("Y-m-d H:i:s");
   		$v_r=$array_data[2];
   		$v_y=$array_data[3];
   		$v_b=$array_data[4];
   		$i_r=$array_data[5];
   		$i_y=$array_data[6];
   		$i_b=$array_data[7];

    	update_ping($array_data[0], $conn);
    	if ($crc == $crc_compare)
    	{
    		$insertdata = "";
    		for ($i = 0;$i < count($array_data);$i++)
    		{
    			if ($i == 31)
    			{
    				if ($array_data[5] > 1 && $array_data[6] > 1 && $array_data[7] > 1)
    				{
    					$array_data[$i] = 1;
    				}

    			}

    			$insertdata = $insertdata . "'" . $array_data[$i] . "',";
    		}

    		if($array_data[31]==1)
    		{
    			$running_status = "Running"; 
    		}


    		$server_date_time = date("Y-m-d H:i:s");
    		$insertdata = $insertdata . "'" . $server_date_time . "'";
    		$sql = "";

    		$sql = "INSERT IGNORE INTO `ccms_data_live` (`device_id`,  `board_temperature`, `voltage_ph1`, `voltage_ph2`, `voltage_ph3`, `current_ph1`, `current_ph2`, `current_ph3`, `energy_kwh_ph1`, `energy_kwh_ph2`, `energy_kwh_ph3`, `energy_kwh_total`, `energy_kvah_ph1`, `energy_kvah_ph2`, `energy_kvah_ph3`, `energy_kvah_total`, `lag_kvarh_ph1`, `lag_kvarh_ph2`, `lag_kvarh_ph3`, `lag_kvarh_total`, `lead_kvarh_ph1`, `lead_kvarh_ph2`, `lead_kvarh_ph3`, `lead_kvarh_total`, `frequency_ph1`, `frequency_ph2`, `frequency_ph3`, `powerfactor_ph1`, `powerfactor_ph2`, `powerfactor_ph3`, `on_off_status`, `contactor_status`, `kw_1`, `kw_2`, `kw_3`, `kw_total`, `kva_1`, `kva_2`, `kva_3`, `kva_total`, `location`, `signal_level`, `date_time`, `server_date_time`) VALUES  (" . $insertdata . ")";

    		if (mysqli_query($conn, $sql))
    		{
    			try {
    				
    				
    				$conn_all = mysqli_connect(HOST, USERNAME, PASSWORD);
    				$sql = "INSERT INTO `motor_pumps`.`motors_status` (device_id, running_status, flow_rate, ph_r_v, ph_y_v, ph_b_v, ph_r_i, ph_y_i, ph_b_i, date_time) VALUES ('$device_id', '$running_status', $flow_rate, '$v_r', '$v_y', '$v_b', '$i_r', '$i_y', '$i_b', '$date_time') ON DUPLICATE KEY UPDATE     running_status = VALUES(running_status), flow_rate = VALUES(flow_rate), ph_r_v = VALUES(ph_r_v), ph_y_v = VALUES(ph_y_v), ph_b_v = VALUES(ph_b_v), ph_r_i = VALUES(ph_r_i), ph_y_i = VALUES(ph_y_i), ph_b_i = VALUES(ph_b_i), date_time = VALUES(date_time)";

					mysqli_query($conn_all, $sql);
    				mysqli_close($conn_all);
    			} catch (Exception $e) {
    				
    			}

    			http_response_code(202);
    			$response = mainfunction($array_data[0], $conn);
    			echo $response;
    		}
    		else
    		{
    			$response = mainfunction($array_data[0]);
    			echo $response;
    		}

    		mysqli_close($conn);

    	}
    	else
    	{
    		http_response_code(202);
    		$response = mainfunction($array_data[0], $conn);
    		echo $response;

    	}
    }


}


function mainfunction($data, $conn)
{
	$array_data = explode(';', $data);
	$sql = "";
	$response = "";
	$condtion = "NA";
	$check = array();
	$length = 0;

	$sql = "SELECT *from (SELECT * FROM device_settings WHERE setting_type IN ('ONOFF','VOLTAGE','CURRENT','FRAME_TIME','HYSTERESIS', 'CALIB_VALUES', 'SOFTWARE', 'RESET') ORDER BY setting_type DESC) a UNION ALL (SELECT * FROM device_settings WHERE setting_type='DEFAULT' LIMIT 1)";

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
            //$sql="UPDATE device_settings SET setting_flag='2' WHERE setting_type='$condtion'" ;
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

		case "NA":

		$response = "No Data";

		break;

		default:
		$response = "No Data";

	}
	return $response;
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

			mysqli_query($log_conn, "INSERT INTO devices ( `device`, `status`) VALUES('$deviceid', '1') ON DUPLICATE KEY UPDATE device='$deviceid', status='1'");

			$sql = "INSERT INTO device_logs ( `device_id`, `time`, `date`, `date_time`, `notification_flag`) VALUES('$deviceid', '$time', '$date', '$date_time', '0') ON DUPLICATE KEY UPDATE device_id='$deviceid', `time`='$time', `date`='$date', `date_time` = '$date_time', `notification_flag`='0'";
			mysqli_query($log_conn, $sql);

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

		mysqli_query($conn, "INSERT INTO `check_main_frame` ( `frame_type`,`frame`, `date_time`) VALUES ('MAIN','$data', '$date')");
	}
	catch(Exception $e)
	{

	}
}


?>