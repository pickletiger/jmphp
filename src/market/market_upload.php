<?php
	require("../../conn.php");
	require_once '../../sdk.class.php';
	require_once '../../util/oss_util.class.php';
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
	set_time_limit(0); //使无响应时间限制
	$ret_data = '';
	
	$access_id = "LTAIIkOiJmMiAZ3V";
	$access_key = "NsVQ9HgzGuDXcHz0buas4lWzotCw9G";
	$end_point = "oss-cn-shenzhen.aliyuncs.com";
	$client = new ALIOSS($access_id,$access_key,$end_point);
	$bucket_name = "jmmes";
	$client->set_enable_domain_style(true);

	$ret_data["ftype"] = isset($_POST["ftype"])?$_POST["ftype"] : '';
	$ret_data["orderNumber"] = isset($_POST["orderNumber"])?$_POST["orderNumber"] : '';
	$ret_data["importPerson"] = isset($_POST["importPerson"])?$_POST["importPerson"] : '';
	$ret_data["checkPerson"] = isset($_POST["checkPerson"])?$_POST["checkPerson"] : '';
	$ret_data["importTime"] = isset($_POST["importTime"])?$_POST["importTime"] : '';
	
	$orderNumber = $ret_data["orderNumber"];
	$importPerson = $ret_data["importPerson"];
	$checkPerson = $ret_data["checkPerson"];
	$importTime = $ret_data["importTime"];
	$pdf = 'jmmes.oss-cn-shenzhen.aliyuncs.com/orderUpload/'.$_FILES["file"]["name"];

//	move_uploaded_file($_FILES["file"]["tmp_name"], "uploadfiles/" . $_FILES["file"]["name"]);

	//查询数据库，检查是否已存在该项目
//	$asql = "SELECT id FROM audit_order WHERE ordernumber = '$orderNumber'";
//	$ares = $conn->query($asql);

	if($orderNumber){
		$sql1 = "INSERT INTO audit_order (ordernumber,importperson,checkperson,importtime,pdf) VALUES ('$orderNumber','$importPerson','$checkPerson','$importTime','$pdf')";
		$res= $conn->query($sql1);
		$file_path = $_FILES["file"]["tmp_name"];//要上传文件的路径
		$object_name = 'orderUpload/'.$_FILES["file"]["name"];//上传到oss的文件路径
		$options = array(
			ALIOSS::OSS_HEADERS => array(
				'Expires' => '',
				'Content-Type'=>$_FILES["file"]["type"],
				'x-oss-server-side-encryption' => 'AES256',
			),
		);
		$response = $client->upload_file_by_file($bucket_name,$object_name,$file_path,$options);
//		move_uploaded_file($_FILES["file"]["tmp_name"], "orderUpload/" . $_FILES["file"]["name"]);
		
		$ret_data["success"] = "success";
	}else{
		$conn->close();
		$ret_data["success"] = "error";
	}
//		$sql1 = "INSERT INTO audit_order (ordernumber,importperson,checkperson,importtime,pdf) VALUES ('$orderNumber','$importPerson','$checkPerson','$importTime','$pdf')";
//		$res= $conn->query($sql1);
		
//		move_uploaded_file($_FILES["file"]["tmp_name"], "uploadfiles/" . $_FILES["file"]["name"]);
//	echo $sql1;


	$json = json_encode($ret_data);
	echo $json;


//	print_r($_FILES);
?>