<?php
  require("../../conn.php");
  header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
  header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
  header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE');

  $modid = $_POST["modid"];
  $routeid = $_POST["routeid"];
  $station = $_POST["checkList"];
  $schedule = $_POST["schedule"];


  $modidArr = explode(",",$modid);
  $stationArr = explode(",",$station);
  $routeidArr = explode(",",$routeid);

  $mod_length = count($modidArr);
  $station_length = count($stationArr);

 
  for($i = 0; $i < $mod_length; $i++) {
    for($j = 0; $j < $station_length; $j++) {
      echo $stationArr[$j] . "||";
      echo $schedule . "||";
      $sql = "INSERT INTO workshop_k (modid, routeid, station, schedule_date, isfinish) VALUES ('$modidArr[$i]', '$routeidArr[$i]', '$stationArr[$j]', '$schedule', '0')";
      $conn->query($sql);    
    }
  } 
  
?>