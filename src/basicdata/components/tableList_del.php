<?php
	require("../../../conn.php");
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
	$ret_data = '';
	$ret_data["success"] = 'success';
	$id = isset($_POST["id"])?$_POST["id"] : '';
	$uuser = isset($_POST["uusername"])?$_POST["uusername"] : '';
	$sql = "DELETE  FROM user WHERE id='$id'";
	$res = $conn->query($sql);
	$sql1 = "INSERT INTO daily_record (username,time,type,thing) VALUES ('$uuser',NOW(),'删除','删除账号')";
	$conn->query($sql1);
    $conn->close();
    $ret_data["success"] = $sql;
	$json=json_encode($ret_data);
	echo $json
?>