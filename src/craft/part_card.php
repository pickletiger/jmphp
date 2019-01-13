<?php
	require("../../conn.php");
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
	$ret_data='';
	$flag = isset($_POST["flag"])?$_POST["flag"]:'';
	$figure_number = isset($_POST["figure_number"])?$_POST["figure_number"]:'';
	switch($flag){
		case 'welding':
		$sql = "SELECT id FROM weldingtable WHERE partdrawingnumber = '$figure_number'";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			$ret_data["success"] = 'success';
			while($row=$res->fetch_assoc()){
				$ret_data["id"] = $row["id"];
			}
		}else {
			$ret_data["success"] = 'error';
		}
		break;
		case 'crafts':
		$sql = "SELECT id FROM craftsmanshiptable WHERE ownpartdrawnumber = '$figure_number';";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			$ret_data["success"] = 'success';
			while($row=$res->fetch_assoc()){
				$ret_data["id"] = $row["id"];
			}
		}else {
			$ret_data["success"] = 'error';
		}
		break;
	}
		
	
	
	$conn->close();
	$json = json_encode($ret_data);
	echo $json;
?>