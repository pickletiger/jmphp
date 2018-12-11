<?php
	require("../../conn.php");
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
	$ret_data='';
	$flag = isset($_POST["flag"])?$_POST["flag"]:'';

	if($flag == "Select"){
		$sql = "select name,gNum,phone_number,job from user";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			$i = 0;
			while($row=$res->fetch_assoc()){
				
				$ret_data["data"][$i]["specification"] = $row["gNum"];
				$ret_data["data"][$i]["size"] = $row["name"];
				$ret_data["data"][$i]["place"] = $row["phone_number"];
				$ret_data["data"][$i]["date"] = $row["job"];
				$i++;
			}
			$ret_data["success"] = 'success';
		}
	}else if($flag == "Save"){
		$gNum = $_POST["gNum"]; //工号
		$size = $_POST["size"]; //姓名
		$place = $_POST["place"]; //手机号码
		$date  = $_POST["date"]; //职位
		if(strlen($gNum) > 0){
			$sql = "UPDATE user SET gNum = '$gNum',name = '$size',phone_number = '$place',job = '$date' WHERE gNum = '$gNum' or name = '$size'";
		}else{}
		
		$res=$conn->query($sql);
		if($res==true){
			$ret_data["success"] = 'success';
		}
	}else if($flag =="Seemodule"){
		$seeModule = $_POST["seeModule"];
		$gNum = $_POST["gNum"];
		$sql = "UPDATE user SET seeModule = '$seeModule' where  gNum = '$gNum'";
		$res=$conn->query($sql);
		if($res==true){
			$ret_data["success"] = 'success';
		}
	}else{
		
	}
		
	
	$conn->close();
	$json = json_encode($ret_data);
	echo $json;
?>