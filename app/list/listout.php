<?php
	require("../../conn.php");
	$flag = $_POST["flag"];
	// $flag = '';
	
	switch ($flag) {
		//0为查询数据
		case '0' : 
			$id = $_POST["id"];
			$pid = $_POST["pid"];
			$modid = $_POST["modid"];
			// $id = "10281";
			// $modid = $_POST["modid"];
			$sql = "SELECT name,figure_number,child_material,count FROM part WHERE id='".$id."' or modid='".$modid."'";
			$res = $conn->query($sql);
			$data= array();
			$data["status"]="error";
		   if($res->num_rows > 0)
			{
				while($row = $res->fetch_assoc())
				{
					$data['name'] = $row['name'];
					$data['count'] = $row['count'];
					$data['figure_number'] = $row['figure_number'];
					$data['child_material'] = $row['child_material'];
				}
				$data["status"]="success";
			 }
			$json = json_encode($data);
			echo $json;
			break;
			//1为装配出仓
		case '1' : 
			$pid = $_POST["pid"];
			$modid = $_POST["modid"];
			$operator = $_POST["operator"];
			$listname=$_POST["listname"];
			// $pid = '11';
			// $modid = '1000616933';
			// $operator = '12';
			// $listname='456';
			$sql_sea="select * from listout where modid='".$modid."' and listname='".$listname."'";
			$result_sea=$conn->query($sql_sea);
			$data='error';
			if($result_sea->num_rows>0){
				$data='error';
			}
			else{
				$sql = "INSERT INTO listout (pid,modid,operator,time,listname) VALUES ('".$pid."','".$modid."','".$operator."','".date("Y-m-d")."','".$listname."' )";
				$result=$conn->query($sql);
				if($result){
				$data='success';
				}
			}
			$json = json_encode($data);
			echo $json;
			break;
	}
	$conn->close();
?>