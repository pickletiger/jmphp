<?php
	require("../../../conn.php");
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
	$ret_data = '';
	$ret_data["success"] = 'success';
	$gNum = isset($_POST["gNum"])?$_POST["gNum"] : '';
	$name = isset($_POST["name"])?$_POST["name"] : '';
	$phone = isset($_POST["phone"])?$_POST["phone"] : '';
	$position = isset($_POST["position"])?$_POST["position"] : '';
	$department = isset($_POST["department"])?$_POST["department"] : '';
	$terminal = isset($_POST["terminal"])?$_POST["terminal"] : '';
	if(isset($_POST["id"])){
		$id = $_POST["id"];
		$sql ="UPDATE user SET gNum='$gNum', name='$name',phone_number='$phone',job='$position',department='$department',terminal='$terminal',utime=NOW() where id='$id'";
		$res = $conn->query($sql);
	}else {
		$sqlNum = "SELECT * from user where gNum='$gNum'";
		$result = $conn->query($sqlNum);
		if ($result->num_rows > 0) {
			$ret_data["success"]='该工号已被注册，请更换!';
			$json=json_encode($ret_data);
			echo $json;
			exit;
		}
		$sqlNum = "SELECT * from user where phone_number='$phone'";
		$result = $conn->query($sqlNum);
		if ($result->num_rows > 0) {
			$ret_data["success"]='该手机已被注册，请更换!';
			$json=json_encode($ret_data);
			echo $json;
			exit;
		}
        $time = time();
		$sql = "INSERT INTO user (gNum,name,phone_number,job,department,terminal,ctime) VALUES ('$gNum','$name','$phone','$position','$department','$terminal',$time)";
		$res = $conn->query($sql);
	}
    $conn->close();
	$json=json_encode($ret_data);
	echo $json
?>