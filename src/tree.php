<?php
	require("../conn.php");
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
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
	}
	//未完成部分
	else if($flag == 'type'){
		$sql = "SELECT type from project where isfinish='0' GROUP BY type";
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
	}else if($flag=='project'){
		$type = isset($_POST["type"])?$_POST["type"]:'';
//		$ret_data["type"] = $type;
		$sql = "SELECT id,name,number FROM project WHERE isfinish='0' AND type = '$type'";
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
	}
	//已完成部分
	if($flag == 'finished_type'){
		$sql = "SELECT type from project where isfinish='1' GROUP BY type";
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
	}else if($flag=='finished_project'){
		$type = isset($_POST["type"])?$_POST["type"]:'';
//		$ret_data["type"] = $type;
		$sql = "SELECT id,name,number FROM project WHERE isfinish='1' AND type = '$type'";
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
	}
	else if($flag=='mpart'){  //项目下一级部件
		$id = isset($_POST["id"])?$_POST["id"]:'';
		$name = isset($_POST["name"])?$_POST["name"]:'';
		$number = isset($_POST["number"])?$_POST["number"]:'';
		$str = explode("#",$number);
		$projectname = $name.$str[1];
		$key = isset($_POST["key"])?$_POST["key"]:'';
//		$ret_data["type"] = $type;
		$sql = "SELECT id,name,modid,figure_number FROM part  WHERE fid = '$id' AND (belong_part=''||belong_part='$projectname') and radio = '$key'";
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
	}else if($flag=='part'){ // 部件
		$modid = isset($_POST["modid"])?$_POST["modid"]:'';
		$pid = isset($_POST["pid"])?$_POST["pid"]:'';
		$name = isset($_POST["name"])?$_POST["name"]:'';
		$figure_number = isset($_POST["figure_number"])?$_POST["figure_number"]:'';
		$level = isset($_POST["level"])?$_POST["level"]:'';
		$key = isset($_POST["key"])?$_POST["key"]:'';
		$ret_data["level"] = $figure_number.'&'.$name;
		if($level == 5) {
			$sql = "SELECT id,name,modid,figure_number FROM part  WHERE fid = '$pid' AND belong_part='$name' and radio = '$key'";
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
				$sql = "SELECT id,name,modid,figure_number FROM part  WHERE fid = '$pid' AND belong_part='$bpart' and radio = '$key'";
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
	} else if($flag == 'treefilter'){
		$modid = isset($_POST["modid"])?$_POST["modid"]:'';
		$state = isset($_POST["state"])?$_POST["state"]:'';
		if($modid) {
			$sql = "SELECT id,name,number FROM project WHERE isfinish='$state' AND modid = '$modid'";
			$res=$conn->query($sql);
			if($res->num_rows>0){
				while($row=$res->fetch_assoc()){
					$ret_data["data"][0]["id"] = $row["id"];
					$ret_data["data"][0]["name"] = $row["number"].$row["name"];
					$ret_data["data"][0]["number"] = $row["number"];
					$ret_data["data"][0]["zhname"] = $row["name"];
					$ret_data["data"][0]["lx"] = 'xm';
					$ret_data["data"][0]["leaf"] = true;
				}
				$ret_data["success"] = 'success';
			}else {
				$asql = "SELECT id,fid,name,modid,figure_number FROM part  WHERE  modid='$modid'";
				$ares=$conn->query($asql);
				if($ares->num_rows>0){
					while($arow=$ares->fetch_assoc()){
						$ret_data["data"][0]["id"] = $arow["id"];
						$ret_data["data"][0]["pid"] = $arow["fid"];  //项目id
						$ret_data["data"][0]["lx"] = 'bj';
						$ret_data["data"][0]["name"] = $arow["name"];
						$ret_data["data"][0]["figure_number"] = $arow["figure_number"];
						$ret_data["data"][0]["modid"] = $arow["modid"];
						$ret_data["data"][0]["leaf"] = true;
					}
					$ret_data["success"] = 'success';
				}else {
					$ret_data["success"] = 'error';
				}
			}
		}
	}
	
	$conn->close();
	$json=json_encode($ret_data);
	echo $json;
?>