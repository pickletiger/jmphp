<?php
	require("../../conn.php");
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
	$ret_data = '';
	$ret_data["success"] = 'success';
	$orderNumber = isset($_POST["orderNumber"])?$_POST["orderNumber"] : '';
	$importPerson = isset($_POST["importPerson"])?$_POST["importPerson"] : '';
	$importTime = isset($_POST["importTime"])?$_POST["importTime"] : '';
	$checkPerson = isset($_POST["checkPerson"])?$_POST["checkPerson"] : '';
        $time = time();
		$id = $_POST["id"];
		$sql ="INSERT INTO audit_order (ordernumber,importperson,checkperson,importtime,pdf) VALUES ('$orderNumber','$importPerson','$checkPerson','$importTime','$pdf')";
		$res = $conn->query($sql);
    $conn->close();
	$json=json_encode($ret_data);
	echo $json
?>