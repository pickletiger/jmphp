<?php
	require("../conn.php");
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
	$sql = "SELECT type,id FROM project";
	$res=$conn->query($sql);
	if($res->num_rows>0){
		$ret_data["success"] = 'success';
		$i = 0;
		while($row=$res->fetch_assoc()){
			$ret_data["data"][$i]["lable"] = $row["type"];
			$ret_data["data"][$i]["value"] = $row["id"];
			$i++;
		}
	}else {
		$ret_data["success"] = 'error';
	}
//	$flag = isset($_POST["flag"])?$_POST["flag"]:'';
//	switch($flag){
//		case 'Select':
//		$account = isset($_POST["account"])?$_POST["account"]:'';
//		$sql = "SELECT account,name FROM user WHERE account = '".$account."'";
//		$res=$conn->query($sql);
//		if($res->num_rows>0){
//			$ret_data["success"] = 'success';
//			while($row=$res->fetch_assoc()){
//				$ret_data["account"] = $row["account"];
//				$ret_data["name"] = $row["name"];
//			}
//		}else {
//			$ret_data["success"] = 'error';
//		}
//		break;
//		case 'Save':
//			$name=$_POST["name"];
//			$utime = time();
//			$account=$_POST["account"];
//			$postword=sha1($_POST["postword"]);
//			$sql = "UPDATE `user` SET `name` = '".$name."', `password` = '".$postword."' , utime = '".$utime."' WHERE account = '".$account."'";
//			$res=$conn->query($sql);
//			if($res==true){
//				$ret_data["success"] = 'success';
//			}else {
//				$ret_data["success"] = 'error';
//			}
//			break;
//		}
	$conn->close();
	$json = json_encode($ret_data);
	echo $json;
?>