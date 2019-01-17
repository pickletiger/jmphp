<?php
	require("../../conn.php");
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
	set_time_limit(0); //使无响应时间限制
	$ret_data = '';
	
	$ret_data["ftype"] = isset($_POST["ftype"])?$_POST["ftype"] : '';
	$ret_data["orderNumber"] = isset($_POST["orderNumber"])?$_POST["orderNumber"] : '';
	$ret_data["importPerson"] = isset($_POST["importPerson"])?$_POST["importPerson"] : '';
	$ret_data["checkPerson"] = isset($_POST["checkPerson"])?$_POST["checkPerson"] : '';
	$ret_data["importTime"] = isset($_POST["importTime"])?$_POST["importTime"] : '';
	
	$orderNumber = $ret_data["orderNumber"];
	$importPerson = $ret_data["importPerson"];
	$checkPerson = $ret_data["checkPerson"];
	$importTime = $ret_data["importTime"];
	$pdf = '${this.baseURL}/market/uploadfiles/'.$_FILES["file"]["name"];

	move_uploaded_file($_FILES["file"]["tmp_name"], "uploadfiles/" . $_FILES["file"]["name"]);
	
	//查询数据库，检查是否已存在该项目
	$asql = "SELECT id FROM audit_order WHERE ordernumber = '$orderNumber'";
	$ares = $conn->query($asql);
	
	if(($ares->num_rows==0)&&($_FILES["file"]["type"] == 'application/pdf')){
		$sql1 = "INSERT INTO audit_order (ordernumber,importperson,checkperson,importtime,pdf) VALUES ('$orderNumber','$importPerson','$checkPerson','$importTime','$pdf')";
		$res= $conn->query($sql1);
		
		move_uploaded_file($_FILES["file"]["tmp_name"], "uploadfiles/" . $_FILES["file"]["name"]);
		
		$ret_data["success"] = "success";
	}else{
		$conn->close();
		$ret_data["success"] = "error";
	}



	$json = json_encode($ret_data);
	echo $json;


//	print_r($_FILES);
?>