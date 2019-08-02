<?php
	require("../../conn.php");
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
	$ret_data = '';
	$id = $_POST["id"];
	$listname=$_POST["listname"];
	$sql = "DELETE  FROM list WHERE id='".$id."'";
	$sql_1 = "DELETE  FROM listout WHERE listname='".$listname."'";
	$res = $conn->query($sql);
	$res_1 = $conn->query($sql_1);
	if($res&&$res_1){
		$ret_data["state"] = 'success';
	}
	$conn->close();
	$json=json_encode($ret_data);
	echo $json
?>