<?php
	require("../../conn.php");
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
	set_time_limit(0); //使无响应时间限制
//	$ret_data = '';
//	
//	$ret_data["ftype"] = isset($_POST["ftype"])?$_POST["ftype"] : '';
//	$ret_data["name"] = isset($_POST["name"])?$_POST["name"] : '';
//	$ret_data["number"] = isset($_POST["number"])?$_POST["number"] : '';
//	$ret_data["type"] = isset($_POST["type"])?$_POST["type"] : '';
//	$ret_data["date"] = isset($_POST["date"])?$_POST["date"] : '';
//	
//	$name = $ret_data["name"];
//	$number = $ret_data["number"];
//	$type = $ret_data["type"];
//	$date = $ret_data["date"];
//	
//	echo $name;
	print_r($_FILES);
?>