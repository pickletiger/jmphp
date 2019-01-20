<?php
	require("../conn.php");
	$flag = $_POST["flag"];
//	$flag = '1';
	
	switch ($flag) {
		case '0' : 
			$id = $_POST["id"];
			$equnumber = $_POST["equnumber"];
//			$id = '25';
//			$equnumber = '1';
			$sql = "SELECT name FROM equipment WHERE id='".$id."' AND number='".$equnumber."' ";
			$res = $conn->query($sql);
			if($res -> num_rows > 0) {
				$i = 0;
				while($row = $res->fetch_assoc()) {
					$arr[$i]['name'] = $row['name'];
					$i++;
				}
			}
			
			$json = json_encode($arr);
			echo $json;
			break;
		case '1' : 
			$id = $_POST["id"];
			$checkresult = $_POST["checkresult"];
			$checkperson = $_POST["checkperson"];
			$sql = "INSERT INTO equipmentcheck (fid,checkresult,checkperson,ctime) VALUES ('".$id."','".$checkresult."','".$checkperson."','".date("Y-m-d H:i:s")."' )";
			$conn->query($sql);
			break;
	}
	$conn->close();
?>