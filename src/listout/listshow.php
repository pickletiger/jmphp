<?php
	require("../../conn.php");
	$ret_data = '';
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
//	$send_json = @file_get_contents('php://input');  // body 传值获取
//	$id = isset($_POST["id"]) ? $_POST["id"] : '11111';
//	$ret_data["id"] = $send_json;
//		echo $_POST["id"];
		$sql = "select * from list";
		$res = $conn->query($sql);
		if($res->num_rows>0) {
			$i=0;
			$ret_data["success"]= 'success';
			while($row = $res->fetch_assoc()){
				$ret_data["data"][$i]["number"] = $i+1;
				$ret_data["data"][$i]["id"] = $row["id"];
				$ret_data["data"][$i]["listname"] = $row["listname"];
				$ret_data["data"][$i]["description"] = $row["description"];
				$i++;
			}
		}
		$conn->close();
	$json = json_encode($ret_data);
	echo $json;
?>