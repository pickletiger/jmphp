<?php
	require ("../conn.php");
	header("Access-Control-Allow-Origin: *");
	// 允许任意域名发起的跨域请求
	$ret_data = '';
	$flag = isset($_POST["flag"]) ? $_POST["flag"] : '';
	if($flag == "Select"){
		$sql = "select Wmodid,station,name from test where isfinish = '1'";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			$i = 0;
			while($row=$res->fetch_assoc()){
				$ret_data["data"][$i]["number"] = $row["Wmodid"];
				$ret_data["data"][$i]["partName"] = $row["name"];
				$ret_data["data"][$i]["processName"] = $row["station"];
				$i++;
			}
			$ret_data["success"] = 'success';
		}
	}else if($flag == "Test"){
		$result = $_POST["result"];
		$Number = $_POST["Number"];
		$person = $_POST["person"];
		$type   = $_POST["type"];
		$sql = "UPDATE workshop_k SET isfinish='".$result."',uuser = '".$person."',test_type = '".$type."' WHERE isfinish = '1' and modid='".$Number."'";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			
			}
		$ret_data["success"] = 'success';
	}else{
		$state = $_POST["state"];
		$sql = "select Wmodid,station,name,utime,photourl from test where isfinish = '".$state."'";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			$i = 0;
			while($row=$res->fetch_assoc()){
				$ret_data["data"][$i]["number"] = $row["Wmodid"];
				$ret_data["data"][$i]["partName"] = $row["name"];
				$ret_data["data"][$i]["processName"] = $row["station"];
				$ret_data["data"][$i]["checkDate"] = $row["utime"];
				$ret_data["data"][$i]["photourl"] = $row["photourl"];
				$i++;
			}
			$ret_data["success"] = 'success';
		}
	}
	$conn->close();
	$json = json_encode($ret_data);
	echo $json;
?>