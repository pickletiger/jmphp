<?php
	require("../../conn.php");
	$ret_data = '';
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
//	$send_json = @file_get_contents('php://input');  // body 传值获取
//	$id = isset($_POST["id"]) ? $_POST["id"] : '11111';
//	$ret_data["id"] = $send_json;
	if(isset($_POST["id"])) {
		$id = $_POST["id"];
		$sql = "select a.number,a.name,b.add_time,b.checkperson,b.checkresult from equipment a,equipmentcheck b where b.fid=a.id and a.id='$id'";
		$res = $conn->query($sql);
		if($res->num_rows>0){
			$i = 0;
			$ret_data["success"]= 'success';
			while($row = $res->fetch_assoc()){
				$ret_data["data"][$i]["name"] = $row["name"];
				$ret_data["data"][$i]["number"] = $row["number"];
				$ret_data["data"][$i]["checkresult"] = $row["checkresult"];
				$ret_data["data"][$i]["checkdate"] = $row["add_time"];
				$ret_data["data"][$i]["checkperson"] = $row["checkperson"];
				$i++;
			}
		}
		$conn->close();		
	}else {
//		echo $_POST["id"];
		$sql = "select * from equipment";
		$res = $conn->query($sql);
		if($res->num_rows>0) {
			$i=0;
			$ret_data["success"]= 'success';
			while($row = $res->fetch_assoc()){
				$ret_data["data"][$i]["id"] = $row["id"];
				$ret_data["data"][$i]["number"] = $row["number"];
				$ret_data["data"][$i]["name"] = $row["name"];
				$ret_data["data"][$i]["state"] = $row["state"];
				$ret_data["data"][$i]["workcenter"] = $row["workcenter"];
				$ret_data["data"][$i]["checkrequest"] = $row["checkrequest"];
				$ret_data["data"][$i]["tallyposition"] = $row["tallyposition"];
				$ret_data["data"][$i]["tallycycle"] = $row["tallycycle"];
				$ret_data["data"][$i]["terminal"] = $row["terminal"];
				$i++;
			}
		}
		$conn->close();
	}
	$json = json_encode($ret_data);
	echo $json;
?>