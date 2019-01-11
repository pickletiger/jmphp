<?php
	require("../../conn.php");
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
 	$sqldata='';
 	$sql="SELECT * FROM `working_condition` ORDER BY `classnumber`";
 	$result = $conn->query($sql);
 	while ($row = $result->fetch_assoc()) {
		$sqldata=$sqldata.'{
			"id":"'.$row["id"].'",
			"Serial":"'.$row["classnumber"].'",
			"name":"'.$row["partname"].'",
			"type":"'.$row["producttype"].'",
			"time":"'.$row["finallytime"].'",
			"schedule":"'.$row["progress"].'",
			"Remarks":"'.$row["remarks"].'"
		},';
	}
 	$jsonresult = 'true';
	$otherdate = '{"success":"'.$jsonresult.'"
		      }';
	$json = '['.$sqldata.$otherdate.']';
	echo $json;
?>