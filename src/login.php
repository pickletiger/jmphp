<?php
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
	require("../conn.php");
	$flag = isset($_POST["flag"])?$_POST["flag"]:'';
	if($flag == "Login"){
		$username = $_POST["username"];
		$password = $_POST["password"];
		$sql = "SELECT password,department FROM user WHERE account = '$username' and terminal = '0' ";
		$result = $conn->query($sql);
		$row = $result->fetch_assoc();
		if(sha1($password)==$row["password"])	{
			$data['status']='success';
			$data['department'] = $row['department'];
		}else{
			$data='error';
		}
		
	}
	$json = json_encode($data);
	echo $json;
	$conn->close();	
?>