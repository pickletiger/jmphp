<?php
	require("../conn.php");
	$ret_data='';
	$flag = isset($_POST["flag"])?$_POST["flag"]:'';
	
	//未审核部分
	if($flag == 'unreview_type'){
		$sql = "SELECT type from project where isfinish='2' GROUP BY type";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			$i = 0;
			while($row=$res->fetch_assoc()){
				$ret_data["data"][$i]["name"] = $row["type"];
				$ret_data["data"][$i]["leaf"] = false;
				$i++;
			}
			$ret_data["success"] = 'success';
		}
	}else if($flag=='unreview_project'){
		$type = isset($_POST["type"])?$_POST["type"]:'';
//		$ret_data["type"] = $type;
		$sql = "SELECT id,name,number FROM project WHERE isfinish='2' AND type = '$type'";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			$i = 0;
			while($row=$res->fetch_assoc()){
				$ret_data["data"][$i]["id"] = $row["id"];
				$ret_data["data"][$i]["name"] = $row["number"].$row["name"];
				$ret_data["data"][$i]["number"] = $row["number"];
				$ret_data["data"][$i]["zhname"] = $row["name"];
				$ret_data["data"][$i]["lx"] = 'xm';
				$ret_data["data"][$i]["leaf"] = false;
				$i++;
			}
			$ret_data["success"] = 'success';
		}
	}else if($flag=='unreview_mpart'){
		$id = isset($_POST["id"])?$_POST["id"]:'';
		$name = isset($_POST["name"])?$_POST["name"]:'';
		$number = isset($_POST["number"])?$_POST["number"]:'';
		$str = explode("#",$number);
		$projectname = $name.$str[1];
//		$ret_data["type"] = $type;
		$sql = "SELECT id,name,modid,figure_number FROM part  WHERE fid = '$id' AND (belong_part=''||belong_part='$projectname')";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			$i = 0;
			while($row=$res->fetch_assoc()){
				$ret_data["data"][$i]["id"] = $row["id"];
				$ret_data["data"][$i]["pid"] = $id;  //项目id
				$ret_data["data"][$i]["lx"] = 'bj';
				$ret_data["data"][$i]["name"] = $row["name"];
				$ret_data["data"][$i]["figure_number"] = $row["figure_number"];
				$ret_data["data"][$i]["modid"] = $row["modid"];
				$ret_data["data"][$i]["leaf"] = false;
				$i++;
			}
			$ret_data["success"] = 'success';
		}
	}else if($flag=='unreview_part'){
		$modid = isset($_POST["modid"])?$_POST["modid"]:'';
		$pid = isset($_POST["pid"])?$_POST["pid"]:'';
		$name = isset($_POST["name"])?$_POST["name"]:'';
		$figure_number = isset($_POST["figure_number"])?$_POST["figure_number"]:'';
		$level = isset($_POST["level"])?$_POST["level"]:'';
		$ret_data["level"] = $figure_number.'&'.$name;
		if($level == 5) {
			$sql = "SELECT id,name,modid,figure_number FROM part  WHERE fid = '$pid' AND belong_part='$name'";
			$res=$conn->query($sql);
			if($res->num_rows>0){
				$i = 0;
				while($row=$res->fetch_assoc()){
					$ret_data["data"][$i]["id"] = $row["id"];
					$ret_data["data"][$i]["pid"] = $pid;  //项目id
					$ret_data["data"][$i]["lx"] = 'bj';
					$ret_data["data"][$i]["name"] = $row["name"];
					$ret_data["data"][$i]["figure_number"] = $row["figure_number"];
					$ret_data["data"][$i]["modid"] = $row["modid"];
					$ret_data["data"][$i]["leaf"] = true;
					$i++;
				}
				$ret_data["success"] = 'success';
			}else {
				$bpart = $figure_number.'&'.$name;
				$sql = "SELECT id,name,modid,figure_number FROM part  WHERE fid = '$pid' AND belong_part='$bpart'";
				$res=$conn->query($sql);
				if($res->num_rows>0){
					$i = 0;
					while($row=$res->fetch_assoc()){
						$ret_data["data"][$i]["id"] = $row["id"];
						$ret_data["data"][$i]["pid"] = $pid;  //项目id
						$ret_data["data"][$i]["lx"] = 'bj';
						$ret_data["data"][$i]["name"] = $row["name"];
						$ret_data["data"][$i]["figure_number"] = $row["figure_number"];
						$ret_data["data"][$i]["modid"] = $row["modid"];
						$ret_data["data"][$i]["leaf"] = true;
						$i++;
					}
					$ret_data["success"] = 'success';
				}
			}
		
		}else {
			$sql = "SELECT id,name,modid,figure_number FROM part  WHERE fid = '$pid' AND belong_part='$name'";
			$res=$conn->query($sql);
			if($res->num_rows>0){
				$i = 0;
				while($row=$res->fetch_assoc()){
					$ret_data["data"][$i]["id"] = $row["id"];
					$ret_data["data"][$i]["pid"] = $pid;  //项目id
					$ret_data["data"][$i]["lx"] = 'bj';
					$ret_data["data"][$i]["name"] = $row["name"];
					$ret_data["data"][$i]["figure_number"] = $row["figure_number"];
					$ret_data["data"][$i]["modid"] = $row["modid"];
					$ret_data["data"][$i]["leaf"] = false;
					$i++;
				}
				$ret_data["success"] = 'success';
			}else {
				$bpart = $figure_number.'&'.$name;
				$sql = "SELECT id,name,modid,figure_number FROM part  WHERE fid = '$pid' AND belong_part='$bpart'";
				$res=$conn->query($sql);
				if($res->num_rows>0){
					$i = 0;
					while($row=$res->fetch_assoc()){
						$ret_data["data"][$i]["id"] = $row["id"];
						$ret_data["data"][$i]["pid"] = $pid;  //项目id
						$ret_data["data"][$i]["lx"] = 'bj';
						$ret_data["data"][$i]["name"] = $row["name"];
						$ret_data["data"][$i]["figure_number"] = $row["figure_number"];
						$ret_data["data"][$i]["modid"] = $row["modid"];
						$ret_data["data"][$i]["leaf"] = false;
						$i++;
					}
					$ret_data["success"] = 'success';
				}
			}
		}
	}
	
	$conn->close();
	$json=json_encode($ret_data);
	echo $json;
?>