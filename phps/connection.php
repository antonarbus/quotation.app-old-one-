<?php
	$mysql_host = 'localhost';   # you can also write '127.0.0.1'
	$mysql_user = 'sherbsherb'; // root for test machine
	$mysql_password = '3007aPPl@'; // for test machine
	#$mysql_db_name= $_POST['DB']; //'db_name';	
	$con = mysqli_connect($mysql_host, $mysql_user, $mysql_password);
	mysqli_set_charset($con, "utf8");
	
	if (mysqli_connect_errno())
	{	}
	else
	{	}
?>