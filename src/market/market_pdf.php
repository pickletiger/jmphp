<?php
	require("../../conn.php");
	$ret_data = '';
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
	$ordernumber = $_POST["ordernumber"];
	$sql="select pdf from audit_order where ordernumber='$ordernumber'";
	$res = $conn->query($sql);
 	while($row = $res->fetch_assoc()){
 		$ret_data["data"]["pdf"] = $row["pdf"];
 	}
	$json = json_encode($ret_data);
	echo $json;
?>