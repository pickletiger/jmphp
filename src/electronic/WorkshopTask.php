<?php
 	require("../../conn.php");
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
 	$sqldata='';
 	$sql="SELECT * from `test` where isfinish = '0'";
 	$result = $conn->query($sql);
 	while ($row = $result->fetch_assoc()) {
		$sqldata=$sqldata.'{
			"Serial":"'.$row["route"].'",
			"name":"'.$row["name"].'",
			"Group":"'.$row["station"].'",
			"time":"'.$row["ctime"].'",
			"finished":"'.$row["otime"].'",
			"Remarks":"'.$row["remark"].'"
		},';
	}
 	$jsonresult = 'true';
	$otherdate = '{"success":"'.$jsonresult.'"
		      }';
	$json = '['.$sqldata.$otherdate.']';
	echo $json;
?>