<?php
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
	require("../conn.php");
	$ret_data='';
	$flag = isset($_POST["flag"])?$_POST["flag"]:'';
	$id = isset($_POST["id"])?$_POST["id"]:'';
	
//	$ret_data["flag"] = $flag;
//	$ret_data["id"] = $id;
	
	//获取项目信息
	if($flag == 'project') {
		$sql = "SELECT * FROM project WHERE id = '$id'";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			while($row=$res->fetch_assoc()){
				$ret_data["name"] = $row["name"];
				$ret_data["type"] = $row["type"];
				$ret_data["number"] = $row["number"];
				$ret_data["date"] = $row["end_date"];
				$ret_data["remark"] = $row["remark"];
				$ret_data["id"]=$id;
				$modid = $row["modid"];
			}
			$ret_data["success"] = 'success';
		}
		$mysql = "SELECT id,route, isfinish FROM route WHERE pid <> '0'	AND  modid = '$modid' ORDER BY id ASC";
		$myres = $conn->query($mysql);
		if($myres->num_rows>0){
			$x=0;
			$y=0;
			$z=0;
			while($myrow=$myres->fetch_assoc()){
//				$ret_data["e"] = $row["isfinish"];
				switch($myrow["isfinish"]){
					case 0:
					$ret_data["unfinished"][$x]["route"] = $myrow["route"];
					$ret_data["unfinished"][$x]["id"] = $myrow["id"];
					$x++;
					break;
					case 1:
					$ret_data["finished"][$y]["route"] = $myrow["route"];
					$ret_data["finished"][$y]["id"] = $myrow["id"];
					$y++;
					break;
					case 2:
					$ret_data["bulid"][$z]["route"] = $myrow["route"];
					$ret_data["bulid"][$z]["id"] = $myrow["id"];
					$z++;
					break;
				}
			}
		}
	}
	//项目工艺路线
	else if($flag == 'prosch'){
		$sql = "SELECT * FROM project WHERE id = '$id'";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			while($row=$res->fetch_assoc()){
				$name = $row["name"];
				$number = $row["number"];
			}
		}
		$str = explode("#",$number);
		$projectname = $name.$str[1];
		$ret_data["project"]=$projectname;
		$asql = "SELECT modid,name FROM part  WHERE fid = '$id' AND (belong_part=''||belong_part='$projectname')";
		$ares=$conn->query($asql);
		if($ares->num_rows>0){
			$ret_data["success"]="success";
			$i=0;
			while($arow=$ares->fetch_assoc()){
				$modid = $arow["modid"];
				$bsql="SELECT id,route,isfinish FROM route WHERE pid <> '0' AND modid = '$modid' ORDER BY id ASC";
				$bres=$conn->query($bsql);
				if($bres->num_rows>0){
					$x=0;
					$y=0;
					$z=0;
					while($brow=$bres->fetch_assoc()){
		//				$ret_data["e"] = $row["isfinish"];
						$ret_data["item"][$i]["name"]=$arow["name"];
						switch($brow["isfinish"]){
							case 0:
							$ret_data["item"][$i]["unfinished"][$x]["route"] = $brow["route"];
							$ret_data["item"][$i]["unfinished"][$x]["id"] = $brow["id"];
							$x++;
							break;
							case 1:
							$ret_data["item"][$i]["finished"][$y]["route"] = $brow["route"];
							$ret_data["item"][$i]["finished"][$y]["id"] = $brow["id"];
							$y++;
							break;
							case 2:
							$ret_data["item"][$i]["bulid"][$z]["route"] = $brow["route"];
							$ret_data["item"][$i]["bulid"][$z]["id"] = $brow["id"];
							$z++;
							break;
						}
					}
					$i++;
				}
			}
		}
	}
	//项目信息修改 
	else if($flag=='savepro'){
		$date = isset($_POST["date"])?$_POST["date"]:'';
		$remark = isset($_POST["remark"])?$_POST["remark"]:'';
		$sql = "UPDATE project SET end_date='$date',remark='$remark' WHERE id = '$id'";
		$res = $conn->query($sql);
		$ret_data["success"] = "success";
	}
	//增加项目下子部件
	else if($flag == "addmpart") {
		$name = isset($_POST["name"])?$_POST["name"]:'';
		$figure_number = isset($_POST["figure_number"])?$_POST["figure_number"]:'';
		$standard = isset($_POST["standard"])?$_POST["standard"]:'';
		$count = isset($_POST["count"])?$_POST["count"]:'';
		$modid = isset($_POST["modid"])?$_POST["modid"]:'';
		$remark = isset($_POST["remark"])?$_POST["remark"]:'';
		$routel = isset($_POST["routel"])?$_POST["routel"]:'';
		$sql = "INSERT INTO part (name,fid,belong_part,figure_number,standard,count,modid,remark,isfinish)  VALUES('$name','$id','','$figure_number','$standard','$count','$modid','$remark','0')";
		$res = $conn->query($sql);
		$ret_data["success"]='success';
		if($routel){
			$route_arr = explode('→',$routel);
				$length = count($route_arr);
				for($route_i=1;$route_i<$length;$route_i++){
					$bsql = "INSERT INTO route VALUES(null,'$id','$modid','$route_arr[$route_i]','$route_i','$routel','0')";
					$bres = $conn->query($bsql);
				}
		}
	}
	//删除项目
	else if($flag == "delpro"){
		//删除项目
		$sql = "DELETE FROM project WHERE id='$id'";
		$res=$conn->query($sql);
		//将所属部件假删除
		$asql = "DELETE FROM part WHERE fid='$id'";
		$ares=$conn->query($asql);
		//将所属部件的工艺路线假删除
		$bsql = "DELETE FROM route WHERE pid='$id'";
		$bres=$conn->query($bsql);
		$ret_data["success"] = "success";
		
	}
	//项目审核
	else if($flag == "proreview") {
		$id = isset($_POST["id"])?$_POST["id"]:'';
		$date = isset($_POST["date"])?$_POST["date"]:'';
		$remark = ($_POST["remark"])?$_POST["remark"]:'';
		$sql = "UPDATE project SET end_date='$date',remark='$remark',isfinish='0' WHERE id = '$id'";
		$res=$conn->query($sql);
		$ret_data["success"]='success';
	}
	
	$conn->close();
	$json = json_encode($ret_data);
	echo $json;
?>