<?php
	require("../conn.php");
	$id = $_POST["id"];
	$flag = $_POST["flag"];
	$modid = $_POST["modid"];
	switch ($flag) {
		case '0' : 
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
			$sql = "SELECT isfinish from workshop_k where modid='".$modid."' ";
			$res = $conn->query($sql);
			if($res -> num_rows > 0) {
				$i = 0;
				while($row = $res->fetch_assoc()) {
					$isfinish = $row['isfinish'];
					$i++;
				}
			}
			$json = json_encode($isfinish);
			echo $json
			break;
		case '2' :
			// 更新isfinish状态在建
			$sql = "UPDATE workshop_k SET isfinish='2' where modid='".$modid."' ";
			$conn->query($sql);
			break;
		case '3' :
			// 更新isfinish状态完成
			$sql = "UPDATE workshop_k SET isfinish='1' where modid='".$modid."' ";
			$conn->query($sql);
			break;
	}
	$conn->close();
?>