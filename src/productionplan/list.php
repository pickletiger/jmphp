<?php
  header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
  require("../../conn.php");
  // 获取列表数据
  $sql = "SELECT A.modid,A.figure_number,A.name,A.standard,A.count,A.child_material,A.remark,B.id as routeid,C.name as product_name,C.number FROM part A,route B,project C  WHERE B.isfinish='0' and A.modid=B.modid and  B.pid=C.id";
  $res = $conn->query($sql);
  if($res->num_rows > 0 ){
    $i = 0;
    while($row = $res->fetch_assoc()){
      $arr[$i]['modid'] = $row['modid']; 
      $arr[$i]['figure_number'] = $row['figure_number']; 
      $arr[$i]['name'] = $row['name'];
      $arr[$i]['standard'] = $row['standard'];
      $arr[$i]['count'] = $row['count'];
      $arr[$i]['child_material'] = $row['child_material'];
      $number = explode("#",$row['number']);
      $arr[$i]['number'] = $number[0] . "#";
      $arr[$i]['product_name'] = $number[1] . $row['product_name'];
      $arr[$i]['remark'] = $row['remark'];
      $arr[$i]['routeid'] = $row['routeid'];
      $i++;
    }
  }
  // 过滤重复作为下拉checkbox数据
  $sql2 = "SELECT DISTINCT name from part";
  $res2 = $conn -> query($sql2);
  if($res2->num_rows > 0) {
    $i = 0;
    while($row2 = $res2 -> fetch_assoc()) {
      $arr2[$i]['f4'] = $row2['name'];
      $i++;
    }
  }

  $sql3 = "SELECT DISTINCT figure_number from part";
  $res3 = $conn -> query($sql3);
  if($res3->num_rows > 0) {
    $i = 0;
    while($row3 = $res3 -> fetch_assoc()) {
      $arr3[$i]['f3'] = $row3['figure_number'];
      $i++;
    }
  }

  // 未排产
  $list_data = json_encode($arr);
  $fName = json_encode($arr2);
  $fFigure_number = json_encode($arr3);  
  $json = '{"success":true,"rows":'.$list_data.',"fName":'.$fName.',"fFigure_number":'.$fFigure_number.'}';

  // 已排产数据列表
  $sql4 = "SELECT A.id,A.modid,A.figure_number,A.name,A.standard,A.count,A.child_material,A.remark,C. NAME AS product_name,C.number,D.station,D.schedule_date FROM part A,route B,project C,workshop_k D WHERE B.id = D.routeid AND A.fid = C.id AND A.modid = D.modid";
  $res4 = $conn->query($sql4);
  if($res4->num_rows > 0 ){
    $i = 0;
    while($row4 = $res4->fetch_assoc()){
      $arr4[$i]['modid'] = $row4['modid']; 
      $arr4[$i]['figure_number'] = $row4['figure_number']; 
      $arr4[$i]['name'] = $row4['name'];
      $arr4[$i]['standard'] = $row4['standard'];
      $arr4[$i]['count'] = $row4['count'];
      $arr4[$i]['child_material'] = $row4['child_material'];
      $number4 = explode("#",$row4['number']);
      $arr4[$i]['number'] = $number4[0] . "#";
      $arr4[$i]['product_name'] = $number4[1] . $row4['product_name'];
      $arr4[$i]['remark'] = $row4['remark'];
      $arr4[$i]['station'] = $row4['station'];
      $arr4[$i]['schedule_date'] = $row4['schedule_date'];
      $i++;
    }
    // 已排产
    $list_data2 = json_encode($arr4);
    $json = '{"success":true,"rows":'.$list_data.',"fName":'.$fName.',"fFigure_number":'.$fFigure_number.',"rows2":'.$list_data2.'}';
  }
  
  echo $json;
  $conn->close();
?>