<?php
	require("../conn.php");
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
	$ret_data='';
	$time=date("Y-m-d h:i:sa");
	$flag = isset($_POST["flag"])?$_POST["flag"]:'';
	// $flag = 'Overdue';

	if($flag == "Overdue"){
		$sql = "SELECT id,otime,name,route,station FROM workshop_k where odata = '0' ";
		
		$res=$conn->query($sql);
		if($res->num_rows>0){
			$i = 0;
			while($row=$res->fetch_assoc()){
				if(strtotime($row["otime"])<strtotime($time)){
					$message = $row["name"]."的".$row["route"]."的".$row["station"]."已逾期！";
					$sql1 = "INSERT INTO message (content,time,department,state,workstate,route,station) VALUES ('".$message."','".$time."','销售部','0','逾期','".$row["route"]."','".$row["station"]."')";
					$res1=$conn->query($sql1);
					$sql2 = "UPDATE workshop_k SET odata='1' WHERE id='".$row["id"]."' ";
					$res2=$conn->query($sql2);
		   		}else{
					echo “zero2早于zero1′;
		   };
				$i++;
			}
			$ret_data["success"] = 'success';
		}
		
	}else if($flag == "Unread"){
		$department = $_POST["department"]; 
		$sql = "SELECT content,time,id,station,workstate,route FROM message WHERE state='0' AND department='".$department."' ";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			$i = 0;
			while($row=$res->fetch_assoc()){
				
				$ret_data["data"][$i]["address"] = $row["content"];
				$ret_data["data"][$i]["date"] = $row["time"];
				$ret_data["data"][$i]["id"] = $row["id"];
				$ret_data["data"][$i]["tag"] = $row["station"];
				$ret_data["data"][$i]["state"] = $row["workstate"];
				$ret_data["data"][$i]["route"] = $row["route"];
				$i++;
			}
			$ret_data["success"] = 'success';
		}
	}else if($flag == "Read"){
		$department = $_POST["department"]; 
		$sql = "SELECT content,time,id,station,workstate,route FROM message WHERE state='1' AND department='".$department."' ";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			$i = 0;
			while($row=$res->fetch_assoc()){
				
				$ret_data["data"][$i]["address"] = $row["content"];
				$ret_data["data"][$i]["date"] = $row["time"];
				$ret_data["data"][$i]["id"] = $row["id"];
				$ret_data["data"][$i]["tag"] = $row["station"];
				$ret_data["data"][$i]["state"] = $row["workstate"];
				$ret_data["data"][$i]["route"] = $row["route"];
				$i++;
			}
			$ret_data["success"] = 'success';
		}
	}else if($flag == "Recycle"){
		$sql = "SELECT content,time,id,station,workstate,route FROM message where state='1' or state='0' ";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			$i = 0;
			while($row=$res->fetch_assoc()){
				
				$ret_data["data"][$i]["address"] = $row["content"];
				$ret_data["data"][$i]["date"] = $row["time"];
				$ret_data["data"][$i]["id"] = $row["id"];
				$ret_data["data"][$i]["tag"] = $row["station"];
				$ret_data["data"][$i]["state"] = $row["workstate"];
				$ret_data["data"][$i]["route"] = $row["route"];
				$i++;
			}
			$ret_data["success"] = 'success';
		}	
	}else if($flag == "ReadIn"){
		$id = $_POST["id"]; 
		$sql = "UPDATE message SET state='1' WHERE id='".$id."' ";
		$res=$conn->query($sql);
		
	}else if($flag == "allRead"){
		$id = $_POST["id"]; 
		$sql = "UPDATE message SET state='1' WHERE id='".$id."' ";
		$res=$conn->query($sql);
		
	}else if($flag == "RecycleIn"){
		$id = $_POST["id"]; 
		$sql = "UPDATE message SET state='2' WHERE id='".$id."' ";
		$res=$conn->query($sql);
		
	
	}else if($flag == "allDel"){
		$id = $_POST["id"]; 
		$sql = "UPDATE message SET state='2' WHERE id='".$id."' ";
		$res=$conn->query($sql);
		
	
	}else if($flag == "Reduction"){
		$id = $_POST["id"]; 
		$sql = "UPDATE message SET state='1' WHERE id='".$id."' ";
		$res=$conn->query($sql);
		
	}else {
		
	}
		
	
	$conn->close();
	$json = json_encode($ret_data);
	echo $json;
?>