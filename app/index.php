<?php
require ("../conn.php");
	$flag = $_POST["flag"];
	$time = date("Y-m-d h:i:s");
//$flag = "5";

switch ($flag) {

	case '0' :
		// 获取isfinish状态
		$modid = $_POST["modid"];
		$pid = $_POST["pid"];

//		$modid = '1000634968';
//		$pid = '17';
		$sql = "SELECT isfinish,id FROM route WHERE modid='" . $modid . "' AND pid='" . $pid . "' AND isfinish!='1' ORDER by id LIMIT 1";
		$res = $conn -> query($sql);
		if ($res -> num_rows > 0) {
			$i = 0;
			while ($row = $res -> fetch_assoc()) {
				$arr[$i]['isfinish'] = $row['isfinish'];
				$arr[$i]['routeid'] = $row['id'];
				$i++;
			}
		}
		$json = json_encode($arr);
		//			echo gettype($json);
		echo $json;
		break;
	case '1' :
		// 获取isfinish状态
		$modid = $_POST["modid"];
		$routeid = $_POST["routeid"];
		
//		$modid = '1000634968';
//		$routeid = '18959';
		$sql = "SELECT id,routeid,todocount,inspectcount,isfinish FROM workshop_k WHERE modid='" . $modid . "' AND routeid='" . $routeid . "' AND isfinish!='3' ORDER by id LIMIT 1";
		$res = $conn -> query($sql);
		if ($res -> num_rows > 0) {
			$i = 0;
			while ($row = $res -> fetch_assoc()) {
				$arr[$i]['todocount'] = $row['todocount'];
				$arr[$i]['isfinish'] = $row['isfinish'];
				$arr[$i]['inspectcount'] = $row['inspectcount'];
				$arr[$i]['routeid'] = $row['routeid'];
				$arr[$i]['wid'] = $row['id'];
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
		$route = $_POST["route"];
		$messageid = $_POST["messageid"];
		$station = $_POST["station"];
		$name = $_POST["name"];
		$count = $_POST["count"];
		$finishcount = $_POST["finishcount"];
		$workstate = '完工';
		$message = $name . "的" . $route . "的" . $station . "已完工！";
		$writtenBy = $_POST["writtenBy"];

		if ($count == $finishcount) {
			$sql = "UPDATE workshop_k SET isfinish='1' ,ftime='$time' where modid='" . $modid . "' and routeid='" . $routeid . "' and isfinish='2' ORDER by id LIMIT 1 ";
			$conn -> query($sql);

		} else {
			$sql4 = "SELECT finishcount from workshop_k where modid='1000564425' and routeid='7487' ORDER by id LIMIT 1 ";
			$res = $conn -> query($sql4);
			$row = $res -> fetch_assoc();
			echo $row["finishcount"];
			$sql3 = "UPDATE workshop_k SET finishcount='$finishcount', count='$count' where modid='" . $modid . "' and routeid='" . $routeid . "' and isfinish='2' ORDER by id LIMIT 1 ";
			$conn -> query($sql3);
		}
		//更新message
		$sql1 = "INSERT INTO message (content,time,department,state,workstate,route,station,cuser) VALUES ('" . $message . "','" . date("Y-m-d H:i:s") . "','计划部','0','" . $workstate . "','" . $route . "','" . $station . "','" . $writtenBy . "')";
		$conn -> query($sql1);
		$sql2 = "UPDATE message SET state='1' where id='" . $messageid . "' ";
		$conn -> query($sql2);

		break;
	case '6' :
		// 检验是否合格
		$modid = $_POST["modid"];
		$pid = $_POST["pid"];
		$routeid = $_POST["routeid"];
		$inspect = $_POST["inspect"];
		$route = $_POST["route"];
		$station = $_POST["station"];
		$messageid = $_POST["messageid"];
		$name = $_POST["name"];
		$workstate = '检验';
		$message = $name . "的" . $route . "的" . $station . "已检验！";
		$remark = $_POST["remark"];
		$count = $_POST["count"];
		$inspectcount = $_POST["inspectcount"];
		

		if ($inspect === "3") {
			//待完成数量
			$todocount = $count - $inspectcount;
			$sql = "UPDATE workshop_k SET isfinish='" . $inspect . "' ,remark='" . $remark . "' ,utime='" . $time . "' ,todocount='" . $todocount . "' WHERE modid='" . $modid . "' and routeid='" . $routeid . "' AND isfinish='1' ORDER by id LIMIT 1";
			$conn -> query($sql);
//			// 更新message
			$sql7 = "UPDATE message SET state='1' where id='" . $messageid . "' ORDER by id LIMIT 1 ";
			$conn -> query($sql7);
			//将检验信息更新到消息通知
			$sql2 = "INSERT INTO message (content,time,department,state,workstate,route,station,cuser) VALUES ('" . $message . "','" . date("Y-m-d H:i:s") . "','计划部','0','" . $workstate . "','" . $route . "','" . $station . "','" . $writtenBy . "')";
			$conn -> query($sql2);
		}
		//返工记录次数，默认变为未完成
		else if ($inspect === "4") {
			$sql4 = "UPDATE workshop_k SET isfinish='0',notNum=notNum+1 WHERE modid='" . $modid . "' and routeid='" . $routeid . "' AND isfinish='4' ORDER by id LIMIT 1";
			$conn -> query($sql4);
		
		} 
		//进入评审，等待评审后才能继续流程
		else if ($inspect === "5") {
			$sql5 = "UPDATE workshop_k SET isfinish='".$inspect."' WHERE modid='" . $modid . "' and routeid='" . $routeid . "' AND isfinish='1' ORDER by id LIMIT 1";
			$conn -> query($sql5);
		} 
		//报废，默认不改变完成数量，记录检查数量
		else if ($inspect === "6") {
			$sql6 = "UPDATE workshop_k SET isfinish='0' ,inspectcount='".$inspectcount."' WHERE modid='" . $modid . "' and routeid='" . $routeid . "' AND isfinish='1' ORDER by id LIMIT 1";
			$conn -> query($sql6);
		}
		// 循环检测是否所有工序完成
		$sql4 = "SELECT isfinish from workshop_k where modid='" . $modid . "' and routeid='" . $routeid . "' ";
		$res = $conn -> query($sql4);
		if ($res -> num_rows > 0) {
			while ($row = $res -> fetch_assoc()) {
				if ($row['isfinish'] != '3') {
					// 检测如果还有未完成则终止脚本
					die();
				}
			}
			// 更新route进度为完成状态
			$sql5 = "UPDATE route SET isfinish='1' where modid='" . $modid . "' and id='" . $routeid . "' ORDER by id LIMIT 1 ";
			$conn -> query($sql5);

			// 循环检测是否所有车间完成
			$sql6 = "SELECT isfinish from route where modid='" . $modid . "' and pid='" . $pid . "' ";
			$res2 = $conn -> query($sql6);
			if ($res2 -> num_rows > 0) {
				while ($row1 = $res2 -> fetch_assoc()) {
					if ($row1['isfinish'] != '1') {
						// 检测如果还有未完成则终止脚本
						die();
					}
				}
				// 更新part进度为完成状态
				$sql7 = "UPDATE part SET isfinish='1' where modid='" . $modid . "' and fid='" . $pid . "' ORDER by id LIMIT 1 ";
				$res2 = $conn -> query($sql7);
			}
		}
		break;
	case '7' :
		$id = $_POST["id"];
		$pid = $_POST["pid"];
		$modid = $_POST["modid"];
		$routeid = $_POST["routeid"];

		//			$id = '2279';
		//			$pid = "8";
		//			$modid = "1000634982";
		//			$routeid = "7513";

		//			$sql = "SELECT A.modid,A.figure_number,A.name,A.count,A.child_material,A.remark,B.id AS routeid,C.route,C.id,C.notNum,C.station FROM part A,route B,workshop_k C WHERE C.isfinish = '0' AND A.modid = B.modid AND B.id = C.routeid AND B.modid = C.modid ORDER BY id LIMIT 1";
		$sql = "select name,figure_number,child_material,quantity,modid from part where id='" . $id . "' ";
		$res = $conn -> query($sql);
		if ($res -> num_rows > 0) {

			$i = 0;
			while ($row = $res -> fetch_assoc()) {
				$arr[$i]['name'] = $row['name'];
				$arr[$i]['figure_number'] = $row['figure_number'];
				//					$arr[$i]['count'] = $row['count'];
				$arr[$i]['child_material'] = $row['child_material'];
				$arr[$i]['quantity'] = $row['quantity'];
				$i++;
			}
		}

		$sql1 = "SELECT route FROM route WHERE modid='" . $modid . "' AND pid='" . $pid . "' AND isfinish='2' ORDER by id LIMIT 1 ";
		$res1 = $conn -> query($sql1);
		if ($res1 -> num_rows > 0) {

			$i = 0;
			while ($row1 = $res1 -> fetch_assoc()) {
				$arr[$i]['route'] = $row1['route'];
				//					$arr[$i]['count'] = $row1['count'];
				$i++;
			}
		}

		$sql2 = "SELECT station,remark,count FROM workshop_k WHERE modid='" . $modid . "' AND routeid='" . $routeid . "' AND isfinish='2' ORDER by id LIMIT 1 ";
		$res2 = $conn -> query($sql2);
		if ($res2 -> num_rows > 0) {

			$i = 0;
			while ($row2 = $res2 -> fetch_assoc()) {
				$arr[$i]['station'] = $row2['station'];
				$arr[$i]['remark'] = $row2['remark'];
				$arr[$i]['count'] = $row['count'];
				$i++;
			}
		} else {
			//若workshop_k无数据跳出循环
			die();
		}

		$sql3 = "SELECT notNum FROM workshop_k WHERE modid='" . $modid . "' AND routeid='" . $routeid . "' AND isfinish!='3' ORDER by id LIMIT 1 ";
		$res3 = $conn -> query($sql3);
		if ($res3 -> num_rows > 0) {

			$i = 0;
			while ($row3 = $res3 -> fetch_assoc()) {
				$arr[$i]['notNum'] = $row3['notNum'];
				$i++;
			}
		} else {
			//若workshop_k无数据跳出循环
			die();
		}

		$json = json_encode($arr);
		echo $json;
		break;
	case '8' :
		$id = $_POST["id"];
		$pid = $_POST["pid"];
		$modid = $_POST["modid"];
		$routeid = $_POST["routeid"];

		//			$id = '2279';
		//			$pid = "8";
		//			$modid = "1000634982";
		//			$routeid = "7513";

		//			$sql = "SELECT A.modid,A.figure_number,A.name,A.count,A.child_material,A.remark,B.id AS routeid,C.route,C.id,C.notNum,C.station FROM part A,route B,workshop_k C WHERE C.isfinish = '0' AND A.modid = B.modid AND B.id = C.routeid AND B.modid = C.modid ORDER BY id LIMIT 1";
		$sql = "select name,figure_number,count,child_material,quantity,modid from part where id='" . $id . "' ";
		$res = $conn -> query($sql);
		if ($res -> num_rows > 0) {

			$i = 0;
			while ($row = $res -> fetch_assoc()) {
				$arr[$i]['name'] = $row['name'];
				$arr[$i]['figure_number'] = $row['figure_number'];
				$arr[$i]['count'] = $row['count'];
				$arr[$i]['child_material'] = $row['child_material'];
				$arr[$i]['quantity'] = $row['quantity'];
				$i++;
			}
		}

		$sql1 = "SELECT route FROM route WHERE modid='" . $modid . "' AND pid='" . $pid . "' AND isfinish='2' ORDER by id desc LIMIT 1 ";
		$res1 = $conn -> query($sql1);
		if ($res1 -> num_rows > 0) {

			$i = 0;
			while ($row1 = $res1 -> fetch_assoc()) {
				$arr[$i]['route'] = $row1['route'];
				$i++;
			}
		}

		$sql2 = "SELECT station,remark FROM workshop_k WHERE modid='" . $modid . "' AND routeid='" . $routeid . "' AND isfinish!='3' ORDER by id LIMIT 1 ";
		$res2 = $conn -> query($sql2);
		if ($res2 -> num_rows > 0) {

			$i = 0;
			while ($row2 = $res2 -> fetch_assoc()) {
				$arr[$i]['station'] = $row2['station'];
				$arr[$i]['remark'] = $row2['remark'];
				$i++;
			}
		} else {
			//若workshop_k无数据跳出循环
			die();
		}

		$sql3 = "SELECT notNum FROM workshop_k WHERE modid='" . $modid . "' AND routeid='" . $routeid . "' AND isfinish!='3' ORDER by id LIMIT 1 ";
		$res3 = $conn -> query($sql3);
		if ($res3 -> num_rows > 0) {

			$i = 0;
			while ($row3 = $res3 -> fetch_assoc()) {
				$arr[$i]['notNum'] = $row3['notNum'];
				$i++;
			}
		} else {
			//若workshop_k无数据跳出循环
			die();
		}

		$json = json_encode($arr);
		echo $json;
		break;
}
$conn -> close();
?>