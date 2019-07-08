<?php
 	require("../../conn.php");
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
 	$ret_data='';
	$arr = $_POST["arr"];
 	$sql="SELECT route,name,figure_number,schedule_date,otime,todocount from `workshop_k` where isfinish = '2' and route in $arr ORDER BY id DESC limit 10";
 	$result = $conn->query($sql);
 	while ($row = $result->fetch_assoc()) {
 		$ret_data["Serial"] = $row["route"];
 		$ret_data["name"] = $row["name"];
 		$ret_data["figure_number"] = $row["figure_number"];
 		$ret_data["time"] = $row["schedule_date"];
 		$ret_data["finished"] = $row["otime"];
 		$ret_data["todocount"] = $row["todocount"];
	}
 	$ret_data["success"] = 'success';
	$conn->close();
	$json = json_encode($ret_data);
	echo $json;
?>