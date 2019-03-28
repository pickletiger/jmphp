<?php
	require("../../conn.php");
	$flag = isset($_REQUEST["flag"]) ? $_REQUEST["flag"] : "";
	$ret_data = '';
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
	switch($flag){
		case 'updata':
			$ordernumber = isset($_POST["ordernumber"])?$_POST["ordernumber"]:'';
			$checkPerson = isset($_POST["checkPerson"])?$_POST["checkPerson"]:'';
			$sql = "UPDATE audit_order SET checkperson ='$checkPerson' WHERE ordernumber = '$ordernumber'";
			$res=$conn->query($sql);
			if($res===TRUE){
				$ret_data["success"] = 'success';
			}else {
				$ret_data["success"] = 'error';
			}
		break;
		case 'pdf':
			$ordernumber = $_POST["ordernumber"];
			$sql="select pdf from audit_order where ordernumber='$ordernumber'";
			$res = $conn->query($sql);
		 	while($row = $res->fetch_assoc()){
		 		$ret_data["data"]["pdf"] = $row["pdf"];
		 	}
			break;
		}
	
	$json = json_encode($ret_data);
	echo $json;
?>