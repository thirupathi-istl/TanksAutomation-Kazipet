<?php

$ip_address = $_SERVER['REMOTE_ADDR'];

if($ip_address=="::1")
{
	define('HOST','localhost');
	define('USERNAME', 'root');
	define('PASSWORD','123456');
	
	define('DB_USER', 'motor_tanks_users');
	define('DB_ALL', 'motor_pumps');

}
else
{
	 define('HOST','103.101.59.93');
	 define('USERNAME', 'istlabsonline_db_user');
	 define('PASSWORD','istlabsonline_db_pass');
	 define('DB_USER', 'motor_tanks_users');
	 define('DB_ALL', 'motor_pumps');
}
$central_db=DB_ALL;
$users_db=DB_USER;

?>