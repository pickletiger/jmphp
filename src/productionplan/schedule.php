<?php
  require("../../conn.php");
  header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
  header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
  header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE');
  $data="1";
  $flag = isset($_POST["flag"])?$_POST["flag"]:'';
	if($flag == "Schedule"){
		  $modid = $_POST["modid"];
		  $routeid = $_POST["routeid"];
		  $station = $_POST["checkList"];
		  $schedule = $_POST["schedule"];
		  $overdata = $_POST["overdata"];
			$cuser = $_POST["cuser"];
		
		  $modidArr = explode(",",$modid);
		  $stationArr = explode(",",$station);
			$routeidArr = explode(",",$routeid);
			sort($routeidArr);//从小到大排序
			sort($modidArr);
			
		  $mod_length = count($modidArr);
		  $station_length = count($stationArr);
		
		  // 插入排产数据
		  for($i = 0; $i < $mod_length; $i++) {
				$sqlc = "SELECT * from route where isfinish = '3' AND modid = '$modidArr[$i]' ORDER BY id";
				$resc=$conn->query($sqlc);
				if($resc->num_rows>0){
					$rowc=$resc->fetch_assoc();
					if($routeidArr[$i]==$rowc["id"]){
						// 更新路线route状态
						$sql2 = "UPDATE route SET isfinish='0' where id='$routeidArr[$i]' ";
						$conn->query($sql2); 
					}else{
						for($i = 0; $i < $mod_length; $i++){
							// 更新路线route状态
							$sql2 = "UPDATE route SET isfinish='3' where id='$routeidArr[$i]' ";
							$conn->query($sql2); 
						}
						$data="0";
						echo json_encode($data);
						exit;
					}
				}
		    for($j = 0; $j < $station_length; $j++) {
	    		date_default_timezone_set("Asia/Shanghai");  //获取当前时间为上海时间
					$time = date("Y-m-d h:i");//获取当前时间
		      $sql = "INSERT INTO workshop_k (modid, routeid, station, schedule_date, isfinish,ctime,otime,cuser) VALUES ('$modidArr[$i]', '$routeidArr[$i]', '$stationArr[$j]', '$schedule', '0','$time','$overdata','$cuser')";
		      $conn->query($sql);   
			  	// 更新路线route状态
				  $sql2 = "UPDATE route SET isfinish='0' where id='$routeidArr[$i]' ";
				  $conn->query($sql2); 
		    }
			} 
			echo json_encode($data);
	}else if($flag == "Back"){
			
		  $modid = $_POST["modid"];
			$routeid = $_POST["routeid"];
			$reason = $_POST["reason"];
			echo $routeid;
		
		  $modidArr = explode(",",$modid);
			$routeidArr = explode(",",$routeid);
		  $mod_length = count($modidArr);
		  
		  for($i = 0; $i < $mod_length; $i++) {
		  	$sql= "DELETE FROM workshop_k WHERE modid = '$modidArr[$i]' and isfinish = '0'";
	      $conn->query($sql);
		  } 
		
			// 更新路线route状态
			for($i = 0; $i < count($routeidArr); $i++){
				$sql2 = "UPDATE route SET isfinish='3',backMark='1',reason='".$reason."' where id='$routeidArr[$i]' ";
				$conn->query($sql2);
			}
	}else{
		  $Wid = $_POST["Wid"];
		  $schedule = $_POST["scheduleScrap"];
		  $overdata = $_POST["overdataScrap"];
			$cuser = $_POST["cuser"];
		  $WidArr = explode(",",$Wid);
		  $Wid_length = count($WidArr);
		
		  // 插入排产数据
		  for($i = 0; $i < $Wid_length; $i++) {
				$sqlc = "SELECT routeid,modid,todocount,unqualified from workshop_k where id = '$WidArr[$i]' ORDER BY id";
				$resc=$conn->query($sqlc);
				if($resc->num_rows>0){
					$rowc=$resc->fetch_assoc();
					$todocount = $rowc["todocount"] + $rowc["unqualified"];
					$routeid = $rowc["routeid"];
					$modid = $rowc["modid"];
					$sql = "UPDATE workshop_k SET todocount='$todocount',unqualified='0',cuser='$cuser',stime='$schedule',otime='$overdata' where id='$WidArr[$i]'";
					$conn->query($sql);
					$sql1 = "UPDATE workshop_k SET isfinish ='0' where modid='$modid'";
					$conn->query($sql1);
					$sql2 = "UPDATE route SET isfinish ='2' where modid='$modid' and isfinish='1'";
					$conn->query($sql2);
				} 
			} 
			echo json_encode($data);
	}


  $conn->close();
?>