<?php
	$servername = "localhost:3306"; //将本地当做服务器，端口默认3306
	$username = "root";  //连接对象
	$password = "123456";  //连接密码
	$dbname = "jmmes";	 //数据库名称
	$conn = new mysqli($servername, $username, $password, $dbname);	
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}else{
		// echo "Connected successfully";
	}
?>