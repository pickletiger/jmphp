<?php
	require("../conn.php");
	$flag = $_POST["flag"];
	
	switch ($flag) {
		case '0' : 
			$id = $_POST["id"];
			$sql = "select name,child_material,quantity,modid from part where id='".$id."' ";
			$res = $conn->query($sql);
			if($res -> num_rows > 0) {
				$i = 0;
				while($row = $res->fetch_assoc()) {
					$arr[$i]['name'] = $row['name'];
					$arr[$i]['child_material'] = $row['child_material'];
					$arr[$i]['quantity'] = $row['quantity'];
					$i++;
				}
			}
			$json = json_encode($arr);
			echo $json;
			break;
		case '1' : 
			// 获取isfinish状态
			$modid = $_POST["modid"];
			$pid = $_POST["pid"];
			$sql = "SELECT isfinish,id FROM route WHERE modid='".$modid."' AND pid='".$pid."' AND isfinish!='1' ORDER by id LIMIT 1";
			$res = $conn->query($sql);
			if($res -> num_rows > 0) {
				$i = 0;
				while($row = $res->fetch_assoc()) {
					$arr[$i]['isfinish'] = $row['isfinish'];
					$arr[$i]['routeid'] = $row['id'];
					$i++;
				}
			}
			$json = json_encode($arr);
			echo $json;
			break;
		case '2' :
			// 更新isfinish状态在建
			$modid = $_POST["modid"];
			$pid = $_POST["pid"];
			$routeid = $_POST["routeid"];
			$isfinish = $_POST["isfinish"];
			if($isfinish == "4"){
				$sql3 = "UPDATE workshop_k SET isfinish='1' WHERE modid='".$modid."' and routeid='".$routeid."' and isfinish='4' ";
				$conn->query($sql3);
				
//				$sql5 = "UPDATE workshop_k SET isfinish='2' where modid='".$modid."' and routeid='".$routeid."' AND isfinish='0' ORDER by id LIMIT 1 ";
//				$conn->query($sql5);
				die();
			}
			$sql = "UPDATE workshop_k SET isfinish='2' WHERE modid='".$modid."' and routeid='".$routeid."' ORDER by id LIMIT 1";
			$conn->query($sql);
			// 更新route路线中（在建）
			$sql2 = "UPDATE route SET isfinish='2' where modid='".$modid."' and id='".$routeid."' ORDER by id LIMIT 1 ";
			$conn->query($sql2);
			break;
		case '3' :
			// 更新isfinish状态完成
			$modid = $_POST["modid"];
			$pid = $_POST["pid"];
			$routeid = $_POST["routeid"];
			$sql = "UPDATE workshop_k SET isfinish='1' where modid='".$modid."' and routeid='".$routeid."' ORDER by id LIMIT 1 ";
			$conn->query($sql);
			
			// 循环检测是否所有工序完成
			$sql2 = "SELECT isfinish from workshop_k where modid='".$modid."' ";
			$res = $conn->query($sql2);
			if ($res->num_rows > 0) {
				while ($row = $res->fetch_assoc()) {
					if($row['isfinish'] != '1') {
						// 检测如果还有未完成则终止脚本
						die();
					}
				}
				
				// 更新route进度为完成状态
				$sql3 = "SELECT id,isfinish from route where pid='".$pid."' and modid='".$modid."'";
				$res2 = $conn->query($sql3);
				if ($res2->num_rows > 0) {
					while ($row2 = $res2->fetch_assoc()) {
						$routeid = $row2['id'];
						if ($row2['isfinish'] == '2') {
							$sql4 = "UPDATE route SET isfinish='1' where id='".$routeid."' ";
							$conn->query($sql4);
							die();
						} 
					}
				}
			}
			break;
		case '4' : 
			// 获取isfinish状态
			$modid = $_POST["modid"];
			$routeid = $_POST["routeid"];
			$sql = "SELECT id,isfinish FROM workshop_k WHERE modid='".$modid."' AND routeid='".$routeid."' AND isfinish!='3' ORDER by id LIMIT 1";
			$res = $conn->query($sql);
			if($res -> num_rows > 0) {
				$i = 0;
				while($row = $res->fetch_assoc()) {
					$arr[$i]['isfinish'] = $row['isfinish'];
					$arr[$i]['routeid'] = $row['id'];
					$i++;
				}
			} else {
				$i = 0;
				//已完工
				$arr[$i]['isfinish'] = "1";
			}
			$json = json_encode($arr);
			echo $json;
			break;
		case '5' :
			// 更新isfinish状态完成
			$modid = $_POST["modid"];
			$pid = $_POST["pid"];
			$routeid = $_POST["routeid"];
			$sql = "UPDATE workshop_k SET isfinish='1' where modid='".$modid."' and routeid='".$routeid."' and isfinish='2' ORDER by id LIMIT 1 ";
			$conn->query($sql);
			
			break;
		case '6' :
			// 检验是否合格
			$modid = $_POST["modid"];
			$pid = $_POST["pid"];
			$routeid = $_POST["routeid"];
			$inspect = $_POST["inspect"];
			$sql = "UPDATE workshop_k SET isfinish='".$inspect."' WHERE modid='".$modid."' and routeid='".$routeid."' AND isfinish='1' ORDER by id LIMIT 1";
			$conn->query($sql);
			if($inspect == "3"){
				$sql2 = "UPDATE workshop_k SET isfinish='2' WHERE modid='".$modid."' and routeid='".$routeid."' AND isfinish='0' ORDER by id LIMIT 1";
				$conn->query($sql2);
			}
			
			// 循环检测是否所有工序完成
			$sql3 = "SELECT isfinish from workshop_k where modid='".$modid."' and routeid='".$routeid."' ";
			$res = $conn->query($sql3);
			if ($res->num_rows > 0) {
				while ($row = $res->fetch_assoc()) {
					if($row['isfinish'] != '3') {
						// 检测如果还有未完成则终止脚本
						die();
					}
				}
				
//				// 更新route进度为完成状态
				$sql4 = "UPDATE route SET isfinish='1' where modid='".$modid."' and id='".$routeid."' ORDER by id LIMIT 1 ";
				$conn->query($sql4);
				
				// 循环检测是否所有车间完成
				$sql5 = "SELECT isfinish from route where modid='".$modid."' and pid='".$pid."' ";
				$res2 = $conn->query($sql5);
				if ($res2->num_rows > 0) {
					while ($row1 = $res2->fetch_assoc()) {
						if($row1['isfinish'] != '1') {
							// 检测如果还有未完成则终止脚本
							die();
						}
					}
					// 更新part进度为完成状态
					$sql3 = "UPDATE part SET isfinish='1' where modid='".$modid."' and fid='".$pid."' ORDER by id LIMIT 1 ";
					$res2 = $conn->query($sql3);
				}
			}
			break;
	}
	$conn->close();
?>