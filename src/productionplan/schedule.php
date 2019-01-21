<?php
  require("../../conn.php");
  header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
  header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
  header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE');
  
  $flag = isset($_POST["flag"])?$_POST["flag"]:'';
	if($flag == "Schedule"){
		  $modid = $_POST["modid"];
		  $routeid = $_POST["routeid"];
		  $station = $_POST["checkList"];
		  $schedule = $_POST["schedule"];
		
		
		  $modidArr = explode(",",$modid);
		  $stationArr = explode(",",$station);
		  $routeidArr = explode(",",$routeid);
		
		  $mod_length = count($modidArr);
		  $station_length = count($stationArr);
		
		  // 插入排产数据
		  for($i = 0; $i < $mod_length; $i++) {
		    for($j = 0; $j < $station_length; $j++) {
	    		date_default_timezone_set("Asia/Shanghai");  //获取当前时间为上海时间
					$time = date("Y-m-d h:i");//获取当前时间
		      $sql = "INSERT INTO workshop_k (modid, routeid, station, schedule_date, isfinish,ctime) VALUES ('$modidArr[$i]', '$routeidArr[$i]', '$stationArr[$j]', '$schedule', '0','$time')";
		      $conn->query($sql);   
			  	// 更新路线route状态
				  $sql2 = "UPDATE route SET isfinish='0' where modid='$modidArr[$i]' ";
				  $conn->query($sql2); 
		    }
		  } 
	}else if($flag == "Back"){
			
		  $modid = $_POST["modid"];
		  $routeid = $_POST["routeid"];
		
		
		  $modidArr = explode(",",$modid);
		
		  $mod_length = count($modidArr);
		  
		  for($i = 0; $i < $mod_length; $i++) {
		  	$sql= "DELETE FROM workshop_k WHERE modid = '$modidArr[$i]' and isfinish = '0'";
	      $conn->query($sql);
		  } 
		
		  // 更新路线route状态
		  $sql2 = "UPDATE route SET isfinish='3' where id='$routeid' ";
		  $conn->query($sql2);
	
	}else{
		
	}


  $conn->close();
?>