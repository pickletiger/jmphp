<?php
	require("../conn.php");
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
	$ret_data='';
	$flag = isset($_POST["flag"])?$_POST["flag"]:'';
	$id = isset($_POST["id"])?$_POST["id"]:'';
	
	if($flag == 'part') {
		$sql = "SELECT figure_number,name,count,standard,modid,remark FROM part WHERE id = '$id'";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			while($row=$res->fetch_assoc()){
				$ret_data["figure_number"] = $row["figure_number"];
				$ret_data["name"] = $row["name"];
				$ret_data["count"] = $row["count"];
				$ret_data["standard"] = $row["standard"];
				$ret_data["modid"] = $row["modid"];
				$ret_data["remark"] = $row["remark"];
				$ret_data["id"] = $id;
				$modid = $row["modid"];
			}
			$ret_data["success"] = 'success';
		}
		$mysql = "SELECT id,route, isfinish FROM route WHERE modid = '$modid' ORDER BY id ASC";
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
	}else if($flag == 'partsch'){
		$sql = "SELECT figure_number,name FROM part WHERE id = '$id'";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			while($row=$res->fetch_assoc()){
				$figure_number = $row["figure_number"];
				$name = $row["name"];
			}
		}
		
		$str = $figure_number.'&'.$name;
		
		$asql = "SELECT id,modid,name FROM part  WHERE belong_part = '$str' ";
		$ares=$conn->query($asql);
		//判断belong_part字段是否是&拼接的,如不是则执行if
		if($ares->num_rows>0){ 
			$ret_data["success"]="success";
			$i=0;
			while($arow=$ares->fetch_assoc()){
				$modid = $arow["modid"];
				$ret_data["item"][$i]["id"]=$arow["id"];
				$bsql="SELECT id,route,isfinish FROM route WHERE modid = '$modid' ORDER BY id ASC";
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
		}else {
			$csql = "SELECT id,modid,name FROM part  WHERE belong_part = '$figure_number' ";
			$cres=$conn->query($csql);
			//判断belong_part字段是否是&拼接的,如不是则执行if
			if($cres->num_rows>0){ 
				$ret_data["success"]="success";
				$i=0;
				while($crow=$cres->fetch_assoc()){
					$modid = $crow["modid"];
					$ret_data["item"][$i]["id"]=$crow["id"];
					$dsql="SELECT id,route,isfinish FROM route WHERE modid = '$modid' ORDER BY id ASC";
					$dres=$conn->query($dsql);
					if($dres->num_rows>0){
						$x=0;
						$y=0;
						$z=0;
						while($drow=$dres->fetch_assoc()){
			//				$ret_data["e"] = $row["isfinish"];
							$ret_data["item"][$i]["name"]=$crow["name"];
							switch($drow["isfinish"]){
								case 0:
								$ret_data["item"][$i]["unfinished"][$x]["route"] = $drow["route"];
								$ret_data["item"][$i]["unfinished"][$x]["id"] = $drow["id"];
								$x++;
								break;
								case 1:
								$ret_data["item"][$i]["finished"][$y]["route"] = $drow["route"];
								$ret_data["item"][$i]["finished"][$y]["id"] = $drow["id"];
								$y++;
								break;
								case 2:
								$ret_data["item"][$i]["bulid"][$z]["route"] = $drow["route"];
								$ret_data["item"][$i]["bulid"][$z]["id"] = $drow["id"];
								$z++;
								break;
							}
						}
						$i++;
					}
				}
			}	
		}
	}
	else if($flag == 'back'){
		//查找其所属项目并拼接处其所属部件项目名
		$sql = "SELECT id,name,number FROM project WHERE id = (SELECT fid FROM part WHERE id='$id')";
		$res = $conn->query($sql);
		if($res->num_rows>0){
			while($row=$res->fetch_assoc()){
				$name = $row["name"];
				$number = $row["number"];
				$fid = $row["id"];
			}
		}
		$str = explode("#",$number);
		$projectname = $name.$str[1];
		//判断是否项目下一级部件的部件
		$asql = "SELECT belong_part FROM part WHERE id='$id' AND belong_part<>'$projectname' AND belong_part<>''";
		$ares=$conn->query($asql);
		if($ares->num_rows>0){
			while($arow=$ares->fetch_assoc()){
				//判断是否是由图号和名字拼接形成的所属部件字段
				if(strstr($arow["belong_part"],'&')){
					$belong_str = explode("&",$arow["belong_part"]);
					$bsql = "SELECT id FROM part WHERE fid='$fid' AND figure_number='$belong_str[0]' AND name='$belong_str[1]'";
					$bres = $conn->query($bsql);
					if($bres->num_rows>0){
						while($brow=$bres->fetch_assoc()){
							$ret_data["id"] = $brow["id"];
							$ret_data["success"] = 'success';
						}
					}
				}else {
					$belong_str = $arow["belong_part"];
					$bsql = "SELECT id FROM part WHERE fid='$fid' AND figure_number='$belong_str' AND name='$belong_str'";
					$bres = $conn->query($bsql);
					if($bres->num_rows>0){
						while($brow=$bres->fetch_assoc()){
							$ret_data["id"] = $brow["id"];
							$ret_data["success"] = 'success';
						}
					}
				}
			}
		}else {
			$ret_data["success"] = 'error';
		}
	}
	//新建子部件
	else if($flag=='addpart'){
		$name = isset($_POST["name"])?$_POST["name"]:'';
		$figure_number = isset($_POST["figure_number"])?$_POST["figure_number"]:'';
		$standard = isset($_POST["standard"])?$_POST["standard"]:'';
		$count = isset($_POST["count"])?$_POST["count"]:'';
		$modid = isset($_POST["modid"])?$_POST["modid"]:'';
		$remark = isset($_POST["remark"])?$_POST["remark"]:'';
		$routel = isset($_POST["routel"])?$_POST["routel"]:'';
		$sql = "SELECT fid,figure_number,name from part WHERE id = '$id'";
		$res = $conn->query($sql);
		if($res->num_rows>0){
			while($row=$res->fetch_assoc()){
				if($row["figure_number"] == $row["name"]){
					$belong_part = $row["figure_number"];
				}else {
					$belong_part = $row["figure_number"].'&'.$row["name"];
				}
				$fid = $row["fid"];
			}
		}
		$asql = "INSERT INTO part (name,fid,belong_part,figure_number,standard,count,modid,remark,isfinish)  VALUES('$name','$fid','$belong_part','$figure_number','$standard','$count','$modid','$remark','0')";
		$ares = $conn->query($asql);
		$ret_data["success"]='success';
		if($routel){
			$route_arr = explode('→',$routel);
				$length = count($route_arr);
				for($route_i=1;$route_i<$length;$route_i++){
					$bsql = "INSERT INTO route VALUES(null,'$fid','$modid','$route_arr[$route_i]','$route_i','$routel','0')";
					$bres = $conn->query($bsql);
				}
		}
	}
	//保存部件信息
	else if($flag=="savepart"){
		$name = isset($_POST["name"])?$_POST["name"]:'';
		$standard = isset($_POST["standard"])?$_POST["standard"]:'';
		$count = isset($_POST["count"])?$_POST["count"]:'';
		$remark = isset($_POST["remark"])?$_POST["remark"]:'';
		$sql = "UPDATE part SET name='$name',standard='$standard',count='$count',remark='$remark' WHERE id = '$id'";
		$res=$conn->query($sql);
		$ret_data["success"] = 'success';
	}
	$conn->close();
	$json = json_encode($ret_data);
	echo $json;
?>