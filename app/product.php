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
			$sql = "SELECT routeid,isfinish from workshop_k where modid='".$modid."' ";
			$res = $conn->query($sql);
			if($res -> num_rows > 0) {
				$i = 0;
				while($row = $res->fetch_assoc()) {
					$arr[$i]['isfinish'] = $row['isfinish'];
					$arr[$i]['routeid'] = $row['routeid'];
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
			$sql = "UPDATE workshop_k SET isfinish='2' where modid='".$modid."' ";
			$conn->query($sql);
			// 更新route路线中（在建）
			$sql2 = "UPDATE route SET isfinish='2' where id='".$routeid."' ";
			$conn->query($sql2);
			break;
		case '3' :
			// 更新isfinish状态完成
			$modid = $_POST["modid"];
			$pid = $_POST["pid"];
			$sql = "UPDATE workshop_k SET isfinish='1' where modid='".$modid."' ";
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
	}
	$conn->close();
?>