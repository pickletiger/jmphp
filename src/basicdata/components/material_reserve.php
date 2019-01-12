<?php
	require("../../../conn.php");
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
	$ret_data = '';
	$ret_data["success"] = 'success';
	$mNum = isset($_POST["mNum"])?$_POST["mNum"] : '';
	$name = isset($_POST["name"])?$_POST["name"] : '';
	$specifications = isset($_POST["specifications"])?$_POST["specifications"] : '';
	$amount = isset($_POST["amount"])?$_POST["amount"] : '';
	if(isset($_POST["id"])){
        $time = time();
		$id = $_POST["id"];
		$sql ="UPDATE material SET material_num='$mNum', material_name='$name',specifications='$specifications',amount='$amount',utime='$time' where id='$id'";
		$res = $conn->query($sql);
	}else {
        $time = time();
		$sql = "INSERT INTO material (material_num,material_name,specifications,amount,ctime) VALUES ('$mNum','$name','$specifications','$amount','$time')";
		$res = $conn->query($sql);
	}
    $conn->close();
	$json=json_encode($ret_data);
	echo $json
?>