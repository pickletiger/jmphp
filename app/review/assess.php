<?php
require("../../conn.php");

$time = date("Y-m-d h:i:s");
//$flag = $_POST["flag"];
$flag = '2';

switch ($flag) {
		case '0' : 

			$sql = "SELECT id,pid,name,figure_number,modid,routeid FROM review WHERE reviews != '0'";
			$res = $conn->query($sql);
			if($res -> num_rows > 0) {
				
				$i = 0;
				while($row = $res->fetch_assoc()) {
					$data[$i]['rid'] = $row['id'];
					$data[$i]['pid'] = $row['pid'];
					$data[$i]['name'] = $row['name'];
                    $data[$i]['figure_number'] = $row['figure_number'];
					$data[$i]['routeid'] = $row['routeid'];
					$data[$i]['modid'] = $row['modid'];
					$i++;
				}
			} else{
				//若workshop_k无数据跳出循环
				die();
			}
			
			
			$data['row'] = $i;
			$json = json_encode($data);
			echo $json;
			break;
			
		case '1' : 
			$rid = $_POST["rid"];
			$sql = "SELECT id,name,figure_number,reviews,route FROM review WHERE id = '".$rid."'";
			$res = $conn->query($sql);
			if($res -> num_rows > 0) {
				
				$i = 0;
				while($row = $res->fetch_assoc()) {
					$data[$i]['id'] = $row['id'];
					$data[$i]['name'] = $row['name'];
                    $data[$i]['figure_number'] = $row['figure_number'];
					$data[$i]['reviews'] = $row['reviews'];
					$data[$i]['route'] = $row['route'];
					$i++;
				}
			} else{
				//若workshop_k无数据跳出循环
				die();
			}
			
			
			$data['row'] = $i;
			$json = json_encode($data);
			echo $json;
			break;
		case '2' :   
		$modid = $_POST["modid"];
//		$modid = '1000616927';
		$rid = $_POST["rid"];
		$routeid = $_POST["routeid"];
//		$routeid = '19067';
//		$route = $_POST["route"];
//		$station = $_POST["station"];
//		$messageid = $_POST["messageid"];
//		$name = $_POST["name"];
//		$figure_number = $_POST["figure_number"];
		$reviews = $_POST["reviews"];
		$finishcount = $_POST["finishcount"];
//		$writtenBy = $_POST["writtenBy"];
		$inspect = $_POST["inspect"];
//		$remark = $_POST["remark"];
//		$workstate = '检验';
//		$message = $name . "的" . $route . "的" . $station . "已检验！";

		//让步接收
		if ($inspect === "8") {
			if ($reviews === $finishcount) {
				//全部合格
				$reviews = $reviews - $finishcount;
				$sql = "UPDATE workshop_k SET reviews='".$reviews."' ,utime='".$time."' WHERE modid='" . $modid . "' and routeid='" . $routeid . "' ORDER by id LIMIT 1";
				$conn -> query($sql);
				//更新review
				$sql16 = "UPDATE review SET isfinish='3' WHERE modid='".$modid."' and routeid='".$routeid."' ORDER by id LIMIT 1";
				$conn -> query($sql16);
//	//			// 更新message
//				$sql1 = "UPDATE message SET state='1' where id='" . $messageid . "' ORDER by id LIMIT 1 ";
//				$conn -> query($sql1);
//				//将检验信息更新到消息通知
//				$sql2 = "INSERT INTO message (content,time,department,state,workstate,route,station,cuser) VALUES ('" . $message . "','" . date("Y-m-d H:i:s") . "','计划部','0','" . $workstate . "','" . $route . "','" . $station . "','" . $writtenBy . "')";
//				$conn -> query($sql2);
				// 循环检测是否所有零件完成
				$sql3 = "SELECT todocount ,reviews ,inspectcount from workshop_k where modid='".$modid."' and routeid='".$routeid."'  ";
				$res = $conn -> query($sql3);
				if ($res -> num_rows > 0) {
					while ($row = $res -> fetch_assoc()) {
						if ($row['todocount'] == '0'  && $row['reviews'] == '0' && $row['inspectcount'] == '0') {
							$sql4 = "UPDATE workshop_k SET isfinish='3'  WHERE modid='" . $modid . "' and routeid='" . $routeid . "' ORDER by id LIMIT 1";
							$conn -> query($sql4);
						}
					}
				}
			} else {
				//部分合格
				$reviews = $reviews - $finishcount;
				$sql13 = "UPDATE review SET reviews='".$reviews."' ,utime='" . $time . "' WHERE modid='" . $modid . "' and routeid='" . $routeid . "' and id='" . $rid . "' ORDER by id LIMIT 1";
				$conn -> query($sql13);
				
				$sql7 = "UPDATE workshop_k SET reviews=reviews - '".$reviews."' ,utime='".$time."' WHERE modid='" . $modid . "' and routeid='" . $routeid . "' ORDER by id LIMIT 1";
				$conn -> query($sql7);
			}
			
			
		}
		//返工返修记录次数，默认变为未完成
		else if ($inspect === "7") {
			if ($reviews === $finishcount) {
				$reviews = $reviews - $finishcount;
				$sql14 = "UPDATE workshop_k SET isfinish='2' ,reviews='".$reviews."' ,todocount=todocount + '".$finishcount."',notNum=notNum+1 WHERE modid='".$modid."' and routeid='".$routeid."' ORDER by id LIMIT 1";
				$conn -> query($sql14);
				$sql16 = "UPDATE review SET isfinish='3' ,reviews='".$reviews."'  WHERE modid='".$modid."' and routeid='".$routeid."' ORDER by id LIMIT 1";
				$conn -> query($sql16);
			} else {
				$reviews = $reviews - $finishcount;
				$sql5 = "UPDATE workshop_k SET isfinish='2' ,reviews='".$reviews."' ,todocount=todocount + '".$finishcount."' ,notNum=notNum+1  WHERE modid='" . $modid . "' and routeid='" . $routeid . "' AND isfinish='1' ORDER by id LIMIT 1";
				$conn -> query($sql5);
				$sql17 = "UPDATE review SET isfinish='3' ,reviews='".$reviews."'  WHERE modid='".$modid."' and routeid='".$routeid."' ORDER by id LIMIT 1";
				$conn -> query($sql17);
			}
		} 
		//报废，默认不改变完成数量，记录检查数量作为报废数量
		else if ($inspect === "6") {
			$reviews = $reviews - $finishcount;
			$sql8 = "UPDATE workshop_k SET isfinish='2' ,inspectcount='" . $reviews . "' ,reviews='".$reviews."' ,unqualified=unqualified + '".$finishcount."' WHERE modid='" . $modid . "' and routeid='" . $routeid . "' ORDER by id LIMIT 1";
			$conn -> query($sql8);
			$sql19 = "UPDATE review SET reviews='".$reviews."'  WHERE modid='".$modid."' and routeid='".$routeid."' ORDER by id LIMIT 1";
			$conn -> query($sql19);
//			$sql19 = "UPDATE scrap SET scrapNum=scrapNum + '" . $reviews . "'  WHERE modid='" . $modid . "' and routeid='" . $routeid . "' ORDER by id LIMIT 1";
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
}
?>