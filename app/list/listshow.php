<?php
	require("../../conn.php");
	$flag = $_POST["flag"];
	// $flag = '1';
	switch ($flag) {
		//0为查询数据
		case '0' : 
			$sql = "SELECT * FROM list";
			$res = $conn->query($sql);
			if($res -> num_rows > 0) {
				$i = 0;
				while($row = $res->fetch_assoc()) {
					$arr[$i]['listname'] = $row['listname'];
					$arr[$i]['description'] = $row['description'];
					$i++;
				}
			}
			$json = json_encode($arr);
			echo $json;
			break;
		case '1':
		    $listid=$_POST['listid'];
			// $listid='7';
			$sql_search="SELECT * FROM list where id='".$listid."'";
			$result = $conn->query($sql_search);
			if($result -> num_rows > 0) {
				while($row = $result->fetch_assoc()) {
					$arr['listname'] = $row['listname'];
				}
			}
			$arr['state']="nodata";
			$sql_all="SELECT * FROM listshowall where listid='".$listid."'";
			$res = $conn->query($sql_all);
			if($res -> num_rows > 0) {
				$arr['state']="data";
				$i = 0;
				while($row = $res->fetch_assoc()) {
					$arr[$i]['name'] = $row['name'];
					$arr[$i]['count'] = $row['count'];
					$arr[$i]['modid'] = $row['modid'];
					$arr[$i]['figure_number'] = $row['figure_number'];
					$arr[$i]['child_material'] = $row['child_material'];
					$i++;
				}
			}	
			$json = json_encode($arr);
			echo $json;
			break;
			//shanchu
			case '2':
			    $data['state']="error";
			    $modid=$_POST['modid'];
				$listname=$_POST['listname'];
				$sql_del="DELETE FROM listout WHERE modid='".$modid."' and listname='".$listname."'";
				$res = $conn->query($sql_del);
				if($res){
					$data['state']="success";
				}
				$json = json_encode($data);
				echo $json;
				break;
				
	}
	$conn->close();
?>