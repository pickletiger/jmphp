<?php
	require("../conn.php");
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
	$ret_data='';
	$flag = isset($_POST["flag"])?$_POST["flag"]:'';

	if($flag == "Unread"){
		$department = $_POST["department"]; 
		$sql = "SELECT content,time,id FROM message WHERE state='0' AND department='".$department."' ";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			$i = 0;
			while($row=$res->fetch_assoc()){
				
				$ret_data["data"][$i]["title"] = $row["content"];
				$ret_data["data"][$i]["date"] = $row["time"];
				$ret_data["data"][$i]["id"] = $row["id"];
				$i++;
			}
			$ret_data["success"] = 'success';
		}
	}else if($flag == "Read"){
		$department = $_POST["department"]; 
		$sql = "SELECT content,time,id FROM message WHERE state='1' AND department='".$department."' ";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			$i = 0;
			while($row=$res->fetch_assoc()){
				
				$ret_data["data"][$i]["title"] = $row["content"];
				$ret_data["data"][$i]["date"] = $row["time"];
				$ret_data["data"][$i]["id"] = $row["id"];
				$i++;
			}
			$ret_data["success"] = 'success';
		}
	}else if($flag == "Recycle"){
		$sql = "SELECT content,time,id FROM message where state='1' or state='0' ";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			$i = 0;
			while($row=$res->fetch_assoc()){
				
				$ret_data["data"][$i]["title"] = $row["content"];
				$ret_data["data"][$i]["date"] = $row["time"];
				$ret_data["data"][$i]["id"] = $row["id"];
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