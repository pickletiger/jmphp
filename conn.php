<?php	
    header("Access-Control-Allow-Origin:*");
//连接数据库
	$servername = "localhost:3306";
	$username = "root";
	$password = "123456";
	$dbname = "jmmes";	
	$conn = new mysqli($servername, $username, $password, $dbname);	
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}else{
		//echo "Connected successfully";
	}	
?> 