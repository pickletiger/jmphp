<?php
	require("../conn.php");
	$ret_data='';
	$flag = isset($_POST["flag"])?$_POST["flag"]:'';
	$id = isset($_POST["id"])?$_POST["id"]:'';
	
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
	}else if($flag == 'prosch'){
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
		}
	}
	$conn->close();
	$json = json_encode($ret_data);
	echo $json;
?>