<?php
	require("../../conn.php");
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
 	$sqldata='';
 	$sql="SELECT * FROM `abnormal`";
 	$result = $conn->query($sql);
 	while ($row = $result->fetch_assoc()) {
 		$time = date("Y-m-d H:i:s",$row["createtime"]);
		$sqldata=$sqldata.'{
			"type":"'.$row["type"].'",
			"name":"'.$row["partname"].'",
			"time":"'.$time.'",
			"Remarks":"'.$row["remarks"].'"
		},';
	}
 	$jsonresult = 'true';
	$otherdate = '{"success":"'.$jsonresult.'"
			      }';
	$json = '['.$sqldata.$otherdate.']';
	echo $json;
?>