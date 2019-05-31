<?php
require ("../conn.php");
	$flag = $_POST["flag"];
	$time = date("Y-m-d h:i:s");
//$flag = "5";

switch ($flag) {
	case '0' :
		$id = $_POST["id"];
		$pid = $_POST["pid"];
		$modid = $_POST["modid"];
		$routeid = $_POST["routeid"];

//					$id = '7406';
//					$pid = "17";
//					$modid = "1000634968";
//					$routeid = "18959";

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

		$sql1 = "SELECT route FROM route WHERE modid='" . $modid . "' AND pid='" . $pid . "' AND isfinish='0' ORDER by id LIMIT 1 ";
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
	case '1' :
		// 获取isfinish状态
		$modid = $_POST["modid"];
		$pid = $_POST["pid"];

		//			$modid = '1000629924';
		//			$pid = '8';
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
	case '2' :
		// 更新isfinish状态在建
		$modid = $_POST["modid"];
		$pid = $_POST["pid"];
		$routeid = $_POST["routeid"];
		$isfinish = $_POST["isfinish"];
		$route = $_POST["route"];
		$station = $_POST["station"];
		$name = $_POST["name"];
		$count = $_POST["count"];
		
//		$modid = '1000634968';
//		$pid = '17';
//		$routeid = '18959';
//		$isfinish = '0';
//		$route = '$_POST["route"]';
//		$station = '$_POST["station"]';
//		$name = '$_POST["name"]';
//		$count = '4';
		
		$workstate = '就工';
		$message = $name . "的" . $route . "的" . $station . "已就工！";
		$writtenBy = $_POST["writtenBy"];
		//正常情况
//		if ($isfinish == "0") {
			$sql = "UPDATE workshop_k SET isfinish='2' ,route='$route' ,name='$name' ,todocount='$count' ,stime='" . date("Y-m-d") . "' WHERE modid='" . $modid . "' and routeid='" . $routeid . "' AND isfinish='0' ORDER by id LIMIT 1";
			$conn -> query($sql);
			// 更新route路线中（在建）
			$sql2 = "UPDATE route SET isfinish='2' where modid='" . $modid . "' and id='" . $routeid . "' ORDER by id LIMIT 1 ";
			$conn -> query($sql2);
//		} else if ($isfinish == "0"){
//			$sql3 = "UPDATE workshop_k SET isfinish='2' WHERE modid='" . $modid . "' and routeid='" . $routeid . "' and isfinish='4' ORDER by id LIMIT 1";
//			$conn -> query($sql3);
//			//				die();
//		}
		// 更新message
		$sql4 = "INSERT INTO message (content,time,department,state,workstate,route,station,cuser) VALUES ('" . $message . "','" . $time . "','销售部','0','" . $workstate . "','" . $route . "','" . $station . "','" . $writtenBy . "')";
		$res = $conn -> query($sql4);
		$messageid = $conn -> insert_id;
		$data['messageid'] = $messageid;
		$json = json_encode($data);
		echo $json;
		break;
	case '3' :
		// 更新isfinish状态完成
		$modid = $_POST["modid"];
		$pid = $_POST["pid"];
		$routeid = $_POST["routeid"];
		$sql = "UPDATE workshop_k SET isfinish='1' where modid='" . $modid . "' and routeid='" . $routeid . "' ORDER by id LIMIT 1 ";
		$conn -> query($sql);

		// 循环检测是否所有工序完成
		$sql2 = "SELECT isfinish from workshop_k where modid='" . $modid . "' ";
		$res = $conn -> query($sql2);
		if ($res -> num_rows > 0) {
			while ($row = $res -> fetch_assoc()) {
				if ($row['isfinish'] != '1') {
					// 检测如果还有未完成则终止脚本
					die();
				}
			}

			// 更新route进度为完成状态
			$sql3 = "SELECT id,isfinish from route where pid='" . $pid . "' and modid='" . $modid . "'";
			$res2 = $conn -> query($sql3);
			if ($res2 -> num_rows > 0) {
				while ($row2 = $res2 -> fetch_assoc()) {
					$routeid = $row2['id'];
					if ($row2['isfinish'] == '2') {
						$sql4 = "UPDATE route SET isfinish='1' where id='" . $routeid . "' ";
						$conn -> query($sql4);
						die();
					}
				}
			}
		}
		break;
	case '4' :
		// 获取isfinish状态
//		$modid = $_POST["modid"];
//		$routeid = $_POST["routeid"];
		
		$modid = '1000634968';
		$routeid = '18959';
		$sql = "SELECT id,routeid,isfinish FROM workshop_k WHERE modid='" . $modid . "' AND routeid='" . $routeid . "' AND isfinish!='3' ORDER by id LIMIT 1";
		$res = $conn -> query($sql);
		if ($res -> num_rows > 0) {
			$i = 0;
			while ($row = $res -> fetch_assoc()) {
				$arr[$i]['isfinish'] = $row['isfinish'];
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
		$todocount = $_POST["todocount"];
		$finishcount = $_POST["finishcount"];

//		$modid = '1000634968';
//		$pid = '17';
//		$routeid = '18959';
//		$isfinish = '0';
//		$route = '$_POST["route"]';
//		$station = '$_POST["station"]';
//		$name = '$_POST["name"]';
//		$todocount = '1';
//		$finishcount = '1';
//		$messageid = '$_POST["messageid"]';
		
		$workstate = '完工';
		$message = $name . "的" . $route . "的" . $station . "已完工！";
		$writtenBy = '$_POST["writtenBy"]';

		if ($todocount === $finishcount) {
			$todocount = $todocount - $finishcount;
			$sql = "UPDATE workshop_k SET isfinish='1' ,todocount='" . $todocount . "' ,inspectcount=inspectcount + '" . $finishcount . "' ,ftime='" . $time . "' where modid='" . $modid . "' and routeid='" . $routeid . "' ORDER by id LIMIT 1 ";
			$conn -> query($sql);

		} else {
			$todocount = $todocount - $finishcount;
			$sql5 = "UPDATE workshop_k SET todocount='" . $todocount . "' ,inspectcount=inspectcount + '" . $finishcount . "' where modid='" . $modid . "' and routeid='" . $routeid . "'  ORDER by id LIMIT 1 ";
			$conn -> query($sql5);
			
			
//			$sql4 = "SELECT finishcount from workshop_k where modid='" . $modid . "' and routeid='" . $routeid . "' ORDER by id LIMIT 1 ";
//			$res = $conn -> query($sql4);
//			$row = $res -> fetch_assoc();
//			echo $row["finishcount"];
//			$sql3 = "UPDATE workshop_k SET finishcount='$finishcount', count='$count' where modid='" . $modid . "' and routeid='" . $routeid . "' and isfinish='2' ORDER by id LIMIT 1 ";
//			$conn -> query($sql3);
		}
		//更新message
//		$sql1 = "INSERT INTO message (content,time,department,state,workstate,route,station,cuser) VALUES ('" . $message . "','" . date("Y-m-d H:i:s") . "','计划部','0','" . $workstate . "','" . $route . "','" . $station . "','" . $writtenBy . "')";
//		$conn -> query($sql1);
//		$sql2 = "UPDATE message SET state='1' where id='" . $messageid . "' ";
//		$conn -> query($sql2);

		break;
	case '6' :   // 检验改变状态
		$modid = $_POST["modid"];
//		$modid = '1000616927';
		$pid = $_POST["pid"];
		$routeid = $_POST["routeid"];
//		$routeid = '19067';
		$route = $_POST["route"];
		$station = $_POST["station"];
		$messageid = $_POST["messageid"];
		$name = $_POST["name"];
		$figure_number = $_POST["figure_number"];
		$inspectcount = $_POST["inspectcount"];
		$finishcount = $_POST["finishcount"];
		$writtenBy = $_POST["writtenBy"];
		$inspect = $_POST["inspect"];
		$remark = $_POST["remark"];
		$workstate = '检验';
		$message = $name . "的" . $route . "的" . $station . "已检验！";

		if ($inspect === "3") {
			//
			if ($inspectcount === $finishcount) {
				$inspectcount = $inspectcount - $finishcount;
				$sql = "UPDATE workshop_k SET remark='" . $remark . "' ,utime='" . $time . "' ,inspectcount='" . $inspectcount . "' WHERE modid='" . $modid . "' and routeid='" . $routeid . "' ORDER by id LIMIT 1";
				$conn -> query($sql);
	//			// 更新message
				$sql1 = "UPDATE message SET state='1' where id='" . $messageid . "' ORDER by id LIMIT 1 ";
				$conn -> query($sql1);
				//将检验信息更新到消息通知
				$sql2 = "INSERT INTO message (content,time,department,state,workstate,route,station,cuser) VALUES ('" . $message . "','" . date("Y-m-d H:i:s") . "','计划部','0','" . $workstate . "','" . $route . "','" . $station . "','" . $writtenBy . "')";
				$conn -> query($sql2);
				// 循环检测是否所有零件完成
				$sql3 = "SELECT todocount ,reviews from workshop_k where modid='" . $modid . "' and routeid='" . $routeid . "'  ";
				$res = $conn -> query($sql3);
				if ($res -> num_rows > 0) {
					while ($row = $res -> fetch_assoc()) {
						if ($row['todocount'] == '0'  && $row['reviews'] == '0') {
							$sql4 = "UPDATE workshop_k SET isfinish='3'  WHERE modid='" . $modid . "' and routeid='" . $routeid . "' ORDER by id LIMIT 1";
							$conn -> query($sql4);
						}
					}
				}
			} else {
				$inspectcount = $inspectcount - $finishcount;
				$sql13 = "UPDATE workshop_k SET remark='" . $remark . "' ,utime='" . $time . "' ,inspectcount='" . $inspectcount . "' WHERE modid='" . $modid . "' and routeid='" . $routeid . "' ORDER by id LIMIT 1";
				$conn -> query($sql13);
			}
			
			
		}
		//返工记录次数，默认变为未完成
		else if ($inspect === "4") {
			if ($inspectcount === $finishcount) {
				$sql14 = "UPDATE workshop_k SET  isfinish='2' ,todocount=todocount +  '" . $finishcount . "',notNum=notNum+1 WHERE modid='" . $modid . "' and routeid='" . $routeid . "'  ORDER by id LIMIT 1";
				$conn -> query($sql14);
			} else {
				$inspectcount = $inspectcount - $finishcount;
				$sql5 = "UPDATE workshop_k  SET isfinish='2' ,notNum=notNum+1 ,todocount=todocount +  '" . $finishcount . "',inspectcount='" . $inspectcount . "' WHERE modid='" . $modid . "' and routeid='" . $routeid . "' ORDER by id LIMIT 1";
				$conn -> query($sql5);
			}
		} 
		//进入评审，等待评审后才能继续流程
		else if ($inspect === "5") {
			$inspectcount = $inspectcount - $finishcount;
			$sql6 = "UPDATE workshop_k SET reviews=reviews + '" . $finishcount . "' ,inspectcount='" . $inspectcount . "'  WHERE modid='" . $modid . "' and routeid='" . $routeid . "' ORDER by id LIMIT 1";
			$conn -> query($sql6);
//			//保存数据进review
			$sql7 = "INSERT INTO review (modid,routeid,name,figure_number,reviews,route,isfinish,uuser) VALUES ('".$modid."','".$routeid."','".$name."','".$figure_number."','".$finishcount."','".$route."','5','".$writtenBy."')";
			$conn -> query($sql7);
		} 
		//报废，默认不改变完成数量，记录检查数量作为报废数量
		else if ($inspect === "6") {
			$inspectcount = $inspectcount - $finishcount;
			$sql8 = "UPDATE workshop_k SET isfinish='2' ,inspectcount='".$inspectcount."' ,unqualified=unqualified + '".$finishcount."' WHERE modid='" . $modid . "' and routeid='" . $routeid . "' ORDER by id LIMIT 1";
			$conn -> query($sql8);
//			$sql19 = "INSERT INTO scrap (modid,routeid,scrapNum) VALUES ('".$modid."','".$routeid."','".$inspectcount."')";
//			$conn -> query($sql19);
		}
		// 循环检测是否所有工序完成
		$sql9 = "SELECT isfinish from workshop_k where modid='" . $modid . "' and routeid='" . $routeid . "' ";
		$res1 = $conn -> query($sql9);
		if ($res1 -> num_rows > 0) {
			while ($row1 = $res1 -> fetch_assoc()) {
				if ($row1['isfinish'] != '3') {
					// 检测如果还有未完成则终止脚本
					die();
				}
			}
			// 更新route进度为完成状态
			$sql10 = "UPDATE route SET isfinish='1' where modid='" . $modid . "' and id='" . $routeid . "' ORDER by id LIMIT 1 ";
			$conn -> query($sql10);

			// 循环检测是否所有车间完成
			$sql11 = "SELECT isfinish from route where modid='" . $modid . "' and pid='" . $pid . "' ";
			$res2 = $conn -> query($sql11);
			if ($res2 -> num_rows > 0) {
				while ($row2 = $res2 -> fetch_assoc()) {
					if ($row2 ['isfinish'] != '1') {
						// 检测如果还有未完成则终止脚本
						die();
					}
				}
				// 更新part进度为完成状态
				$sql12 = "UPDATE part SET isfinish='1' where modid='" . $modid . "' and fid='" . $pid . "' ORDER by id LIMIT 1 ";
				$res2 = $conn -> query($sql12);
			}
		}
		break;
	case '7' :
		$id = $_POST["id"];
		$pid = $_POST["pid"];
		$modid = $_POST["modid"];
		$routeid = $_POST["routeid"];

//					$id = '7406';
//					$pid = "17";
//					$modid = "1000634968";
//					$routeid = "18959";

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

		$sql2 = "SELECT station,remark,todocount FROM workshop_k WHERE modid='" . $modid . "' AND routeid='" . $routeid . "' ORDER by id LIMIT 1 ";
		$res2 = $conn -> query($sql2);
		if ($res2 -> num_rows > 0) {

			$i = 0;
			while ($row2 = $res2 -> fetch_assoc()) {
				$arr[$i]['station'] = $row2['station'];
				$arr[$i]['remark'] = $row2['remark'];
				$arr[$i]['todocount'] = $row2['todocount'];
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
		
	
	case '8' :   //获取检验的信息	
		$id = $_POST["id"];
		$pid = $_POST["pid"];
		$modid = $_POST["modid"];
		$routeid = $_POST["routeid"];

//					$id = '7406';
//					$pid = "17";
//					$modid = "1000634968";
//					$routeid = "18959";

		//			$sql = "SELECT A.modid,A.figure_number,A.name,A.count,A.child_material,A.remark,B.id AS routeid,C.route,C.id,C.notNum,C.station FROM part A,route B,workshop_k C WHERE C.isfinish = '0' AND A.modid = B.modid AND B.id = C.routeid AND B.modid = C.modid ORDER BY id LIMIT 1";
		$sql = "select name,figure_number,child_material,quantity,modid from part where id='" . $id . "' ";
		$res = $conn -> query($sql);
		if ($res -> num_rows > 0) {

			$i = 0;
			while ($row = $res -> fetch_assoc()) {
				$arr[$i]['name'] = $row['name'];
				$arr[$i]['figure_number'] = $row['figure_number'];
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

		$sql2 = "SELECT station,remark,inspectcount FROM workshop_k WHERE modid='" . $modid . "' AND routeid='" . $routeid . "' AND isfinish!='3' ORDER by id LIMIT 1 ";
		$res2 = $conn -> query($sql2);
		if ($res2 -> num_rows > 0) {

			$i = 0;
			while ($row2 = $res2 -> fetch_assoc()) {
				$arr[$i]['station'] = $row2['station'];
				$arr[$i]['remark'] = $row2['remark'];
				$arr[$i]['inspectcount'] = $row2['inspectcount'];
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