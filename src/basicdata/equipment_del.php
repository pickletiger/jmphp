<?php
	require("../../conn.php");
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
	$ret_data = '';
	$ret_data["success"] = 'success';
	$id = isset($_POST["id"])?$_POST["id"] : '';
	$sql = "DELETE  FROM equipment WHERE id='$id'";
	$mysql = "DELETE  FROM equipmentcheck WHERE fid='$id'";
	$res = $conn->query($sql);
	$myres = $conn->query($mysql);
	$conn->close();
	$json=json_encode($ret_data);
	echo $json
?>