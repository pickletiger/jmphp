<?php	
    header("Access-Control-Allow-Origin:*");
//连接数据库
	$servername = "192.168.0.136";
	$username = "admin";
	$password = "root";
	$dbname = "jmmes";	
	$conn = new mysqli($servername, $username, $password, $dbname);	
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}else{
//		echo "Connected successfully";
	}	
?> 