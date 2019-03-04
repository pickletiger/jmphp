<?php
	require ("../../conn.php");
	header("Access-Control-Allow-Origin: *");
	// 允许任意域名发起的跨域请求
	$ret_data = '';
	$sql = "select username,time,type,thing from daily_record order by time desc";
	$res = $conn -> query($sql);
	if ($res -> num_rows > 0) {
		$i = 0;
		while ($row = $res -> fetch_assoc()) {
			$ret_data["data"][$i]["username"] = $row["username"];
			$ret_data["data"][$i]["time"] = $row["time"];
			$ret_data["data"][$i]["thing"] = $row["thing"];
			$ret_data["data"][$i]["type"] = $row["type"];
			$i++;
		}
		$ret_data["success"] = 'success';
	}
	
	$conn -> close();
	$json = json_encode($ret_data);
	echo $json;
?>