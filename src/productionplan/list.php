<?php
  header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
  require("../../conn.php");
  // 获取列表数据
  $sql = "SELECT A.modid,A.figure_number,A.name,A.standard,A.count,A.child_material,A.remark,B.id as routeid,C.name as product_name,C.number FROM part A,route B,project C  WHERE B.isfinish='2' and A.modid=B.modid and  B.pid=C.id";
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
  $list_data = json_encode($arr);
  $fName = json_encode($arr2);
  $fFigure_number = json_encode($arr3);
  $json = '{"success":true,"rows":'.$list_data.',"fName":'.$fName.',"fFigure_number":'.$fFigure_number.'}';
  echo $json;
  $conn->close();
?>