<?php
  header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
  require("../../conn.php");
  $arr = '';
  $flag = isset($_POST["flag"])?$_POST["flag"]:'';
  if($flag == ''){
	  // 获取列表数据
	  $sql = "SELECT A.modid,A.figure_number,A.name,A.standard,A.count,A.child_material,A.remark,B.id as routeid,C.name as product_name,C.number,B.route_line FROM part A,route B,project C  WHERE B.isfinish='3' and A.modid=B.modid and  B.pid=C.id";
	  $res = $conn->query($sql);
	  if($res->num_rows > 0 ){
	    $i = 0;
	    while($row = $res->fetch_assoc()){
	      $arr[$i]['modid'] = $row['modid']; 
	      $arr[$i]['figure_number'] = $row['figure_number']; 
	      $arr[$i]['name'] = $row['name'];
	      $arr[$i]['standard'] = $row['standard'];
		  	$arr[$i]['route'] = $row['route_line'];
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
	  $sql2 = "SELECT DISTINCT standard from part";
	  $res2 = $conn -> query($sql2);
	  if($res2->num_rows > 0) {
	    $i = 0;
	    while($row2 = $res2 -> fetch_assoc()) {
	      // 开料尺寸
	      $arr2[$i]['f6'] = $row2['standard'];
	      $i++;
	    }
	  }
	
	  $sql3 = "SELECT DISTINCT child_material from part";
	  $res3 = $conn -> query($sql3);
	  if($res3->num_rows > 0) {
	    $i = 0;
	    while($row3 = $res3 -> fetch_assoc()) {
	      // 规格
	      $arr3[$i]['f5'] = $row3['child_material'];
	      $i++;
	    }
	  }
	
	  // 未排产
	  $list_data = json_encode($arr);
	  $fStandard = json_encode($arr2);
	  $fChild_material = json_encode($arr3);  
	  $json = '{"success":true,"rows":'.$list_data.',"fStandard":'.$fStandard.',"fChild_material":'.$fChild_material.'}';
	
	  // 已排产数据列表
	  $sql4 = "SELECT A.id,A.modid,A.fid,A.figure_number,A.name,A.standard,A.count,A.child_material,A.remark,C. NAME AS product_name,C.number,D.station,D.schedule_date,B.route_line FROM part A,route B,project C,workshop_k D WHERE B.id = D.routeid AND A.fid = C.id AND A.modid = D.modid AND D.isfinish = 0";
	  $res4 = $conn->query($sql4);
	  if($res4->num_rows > 0 ){
	    $i = 0;
	    while($row4 = $res4->fetch_assoc()){
	      $arr4[$i]['partid'] = $row4['id'];
	      $arr4[$i]['fid'] = $row4['fid'];  
	      $arr4[$i]['modid'] = $row4['modid']; 
	      $arr4[$i]['figure_number'] = $row4['figure_number']; 
	      $arr4[$i]['name'] = $row4['name'];
	      $arr4[$i]['standard'] = $row4['standard'];
	      $arr4[$i]['count'] = $row4['count'];
		  	$arr4[$i]['route_line'] = $row4['route_line'];
	      $arr4[$i]['child_material'] = $row4['child_material'];
	      $number4 = explode("#",$row4['number']);
	      $arr4[$i]['number'] = $number4[0] . "#";
	      $arr4[$i]['product_name'] = $number4[1] . $row4['product_name'];
	      $arr4[$i]['remark'] = $row4['remark'];
	      $arr4[$i]['station'] = $row4['station'];
	      $arr4[$i]['schedule_date'] = $row4['schedule_date'];
	      $i++;
	    }
	
	    // 规格下拉筛选数据
	    $sql5 = "SELECT DISTINCT child_material FROM part A,route B,project C,workshop_k D WHERE B.id = D.routeid AND A.fid = C.id AND A.modid = D.modid";
	    $res5 = $conn->query($sql5);
	    if($res5->num_rows > 0) {
	      $i = 0;
	      while($row5 = $res5->fetch_assoc()) {
	        $arr5[$i]['F5'] = $row5['child_material'];
	        $i++;
	      }
	    }
	
	    // 开料尺寸下拉筛选数据
	    $sql6 = "SELECT DISTINCT standard FROM part A,route B,project C,workshop_k D WHERE B.id = D.routeid AND A.fid = C.id AND A.modid = D.modid";
	    $res6 = $conn->query($sql6);
	    if($res6->num_rows > 0) {
	      $i = 0;
	      while($row6 = $res6->fetch_assoc()) {
	        $arr6[$i]['F6'] = $row6['standard'];
	        $i++;
	      }
	    }
	
	    // 已排产
	    $list_data2 = json_encode($arr4);
	    $FChild_material = json_encode($arr5);
	    $FStandard = json_encode($arr6);
	    $json = '{"success":true,"rows":'.$list_data.',"fStandard":'.$fStandard.',"fChild_material":'.$fChild_material.',"rows2":'.$list_data2.',"FStandard":'.$FStandard.',"FChild_material":'.$FChild_material.'}';
	  }
	  
	  
	  //生产中数据列表
	  $sql7 = "Select * from projectIng";
	  $res7 = $conn->query($sql7);
	  if($res7->num_rows > 0 ){
	    $i = 0;
	    while($row7 = $res7->fetch_assoc()){
	      $arr7[$i]['partid'] = $row7['id'];
	      $arr7[$i]['fid'] = $row7['fid'];  
	      $arr7[$i]['modid'] = $row7['modid']; 
	      $arr7[$i]['figure_number'] = $row7['figure_number']; 
	      $arr7[$i]['name'] = $row7['name'];
	      $arr7[$i]['standard'] = $row7['standard'];
	      $arr7[$i]['count'] = $row7['count'];
	      $arr7[$i]['child_material'] = $row7['child_material'];
	      $number7 = explode("#",$row7['number']);
	      $arr7[$i]['number'] = $number7[0] . "#";
	      $arr7[$i]['product_name'] = $number7[1] . $row7['product_name'];
	      $arr7[$i]['remark'] = $row7['remark'];
	      $arr7[$i]['station'] = $row7['station'];
	      $arr7[$i]['schedule_date'] = $row7['schedule_date'];
	      $i++;
	    }
		   // 规格下拉筛选数据
	    $sql8 = "SELECT DISTINCT child_material FROM part A,route B,project C,workshop_k D WHERE B.id = D.routeid AND A.fid = C.id AND A.modid = D.modid";
	    $res8 = $conn->query($sql8);
	    if($res8->num_rows > 0) {
	      $i = 0;
	      while($row8 = $res8->fetch_assoc()) {
	        $arr8[$i]['F5'] = $row8['child_material'];
	        $i++;
	      }
	    }
	
	    // 开料尺寸下拉筛选数据
	    $sql9 = "SELECT DISTINCT standard FROM part A,route B,project C,workshop_k D WHERE B.id = D.routeid AND A.fid = C.id AND A.modid = D.modid";
	    $res9 = $conn->query($sql9);
	    if($res9->num_rows > 0) {
	      $i = 0;
	      while($row9 = $res9->fetch_assoc()) {
	        $arr9[$i]['F6'] = $row9['standard'];
	        $i++;
	      }
	    }
	
	    // 生产中
	    $list_data3 = json_encode($arr7);
	    $FChild_material3 = json_encode($arr8);
	    $FStandard3 = json_encode($arr9);
	    $json = '{"success":true,"rows":'.$list_data.',"fStandard":'.$fStandard.',"fChild_material":'.$fChild_material.',"rows2":'.$list_data2.',"FStandard":'.$FStandard.',"FChild_material":'.$FChild_material.',"rows3":'.$list_data3.'}';
	  }
  }else{ //分车间显示
  	
	  // 获取列表数据
	  $sql = "SELECT A.modid,A.figure_number,A.name,A.standard,A.count,A.child_material,A.remark,B.id as routeid,C.name as product_name,C.number,B.route_line FROM part A,route B,project C  WHERE B.isfinish='3' and A.modid=B.modid and  B.pid=C.id and B.route like '%$flag%'";
	  $res = $conn->query($sql);
	  if($res->num_rows > 0 ){
	    $i = 0;
	    while($row = $res->fetch_assoc()){
	      $arr[$i]['modid'] = $row['modid']; 
	      $arr[$i]['figure_number'] = $row['figure_number']; 
	      $arr[$i]['name'] = $row['name'];
	      $arr[$i]['standard'] = $row['standard'];
		  	$arr[$i]['route'] = $row['route_line'];
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
	  $sql2 = "SELECT DISTINCT standard from part";
	  $res2 = $conn -> query($sql2);
	  if($res2->num_rows > 0) {
	    $i = 0;
	    while($row2 = $res2 -> fetch_assoc()) {
	      // 开料尺寸
	      $arr2[$i]['f6'] = $row2['standard'];
	      $i++;
	    }
	  }
	
	  $sql3 = "SELECT DISTINCT child_material from part";
	  $res3 = $conn -> query($sql3);
	  if($res3->num_rows > 0) {
	    $i = 0;
	    while($row3 = $res3 -> fetch_assoc()) {
	      // 规格
	      $arr3[$i]['f5'] = $row3['child_material'];
	      $i++;
	    }
	  }
	
	  // 未排产
	  $list_data = json_encode($arr);
	  $fStandard = json_encode($arr2);
	  $fChild_material = json_encode($arr3);  
	  $json = '{"success":true,"rows":'.$list_data.',"fStandard":'.$fStandard.',"fChild_material":'.$fChild_material.'}';
	
	  // 已排产数据列表
	  $sql4 = "SELECT A.id,A.modid,A.fid,A.figure_number,A.name,A.standard,A.count,A.child_material,A.remark,C. NAME AS product_name,C.number,D.station,D.schedule_date,B.route_line FROM part A,route B,project C,workshop_k D WHERE B.id = D.routeid AND A.fid = C.id AND A.modid = D.modid AND D.isfinish = '0'";
	  $res4 = $conn->query($sql4);
	  if($res4->num_rows > 0 ){
	    $i = 0;
	    while($row4 = $res4->fetch_assoc()){
	      $arr4[$i]['partid'] = $row4['id'];
	      $arr4[$i]['fid'] = $row4['fid'];  
	      $arr4[$i]['modid'] = $row4['modid']; 
	      $arr4[$i]['figure_number'] = $row4['figure_number']; 
	      $arr4[$i]['name'] = $row4['name'];
	      $arr4[$i]['standard'] = $row4['standard'];
	      $arr4[$i]['count'] = $row4['count'];
		  	$arr4[$i]['route_line'] = $row4['route_line'];
	      $arr4[$i]['child_material'] = $row4['child_material'];
	      $number4 = explode("#",$row4['number']);
	      $arr4[$i]['number'] = $number4[0] . "#";
	      $arr4[$i]['product_name'] = $number4[1] . $row4['product_name'];
	      $arr4[$i]['remark'] = $row4['remark'];
	      $arr4[$i]['station'] = $row4['station'];
	      $arr4[$i]['schedule_date'] = $row4['schedule_date'];
	      $i++;
	    }
	
	    // 规格下拉筛选数据
	    $sql5 = "SELECT DISTINCT child_material FROM part A,route B,project C,workshop_k D WHERE B.id = D.routeid AND A.fid = C.id AND A.modid = D.modid";
	    $res5 = $conn->query($sql5);
	    if($res5->num_rows > 0) {
	      $i = 0;
	      while($row5 = $res5->fetch_assoc()) {
	        $arr5[$i]['F5'] = $row5['child_material'];
	        $i++;
	      }
	    }
	
	    // 开料尺寸下拉筛选数据
	    $sql6 = "SELECT DISTINCT standard FROM part A,route B,project C,workshop_k D WHERE B.id = D.routeid AND A.fid = C.id AND A.modid = D.modid";
	    $res6 = $conn->query($sql6);
	    if($res6->num_rows > 0) {
	      $i = 0;
	      while($row6 = $res6->fetch_assoc()) {
	        $arr6[$i]['F6'] = $row6['standard'];
	        $i++;
	      }
	    }
	
	    // 已排产
	    $list_data2 = json_encode($arr4);
	    $FChild_material = json_encode($arr5);
	    $FStandard = json_encode($arr6);
	    $json = '{"success":true,"rows":'.$list_data.',"fStandard":'.$fStandard.',"fChild_material":'.$fChild_material.',"rows2":'.$list_data2.',"FStandard":'.$FStandard.',"FChild_material":'.$FChild_material.'}';
	  }
	  
	  
	  //生产中数据列表
	  $sql7 = "Select * from projectIng";
	  $res7 = $conn->query($sql7);
	  if($res7->num_rows > 0 ){
	    $i = 0;
	    while($row7 = $res7->fetch_assoc()){
	      $arr7[$i]['partid'] = $row7['id'];
	      $arr7[$i]['fid'] = $row7['fid'];  
	      $arr7[$i]['modid'] = $row7['modid']; 
	      $arr7[$i]['figure_number'] = $row7['figure_number']; 
	      $arr7[$i]['name'] = $row7['name'];
	      $arr7[$i]['standard'] = $row7['standard'];
	      $arr7[$i]['count'] = $row7['count'];
	      $arr7[$i]['child_material'] = $row7['child_material'];
	      $number7 = explode("#",$row7['number']);
	      $arr7[$i]['number'] = $number7[0] . "#";
	      $arr7[$i]['product_name'] = $number7[1] . $row7['product_name'];
	      $arr7[$i]['remark'] = $row7['remark'];
	      $arr7[$i]['station'] = $row7['station'];
	      $arr7[$i]['schedule_date'] = $row7['schedule_date'];
	      $i++;
	    }
		   // 规格下拉筛选数据
	    $sql8 = "SELECT DISTINCT child_material FROM part A,route B,project C,workshop_k D WHERE B.id = D.routeid AND A.fid = C.id AND A.modid = D.modid";
	    $res8 = $conn->query($sql8);
	    if($res8->num_rows > 0) {
	      $i = 0;
	      while($row8 = $res8->fetch_assoc()) {
	        $arr8[$i]['F5'] = $row8['child_material'];
	        $i++;
	      }
	    }
	
	    // 开料尺寸下拉筛选数据
	    $sql9 = "SELECT DISTINCT standard FROM part A,route B,project C,workshop_k D WHERE B.id = D.routeid AND A.fid = C.id AND A.modid = D.modid";
	    $res9 = $conn->query($sql9);
	    if($res9->num_rows > 0) {
	      $i = 0;
	      while($row9 = $res9->fetch_assoc()) {
	        $arr9[$i]['F6'] = $row9['standard'];
	        $i++;
	      }
	    }
	
	    // 生产中
	    $list_data3 = json_encode($arr7);
	    $FChild_material3 = json_encode($arr8);
	    $FStandard3 = json_encode($arr9);
	    $json = '{"success":true,"rows":'.$list_data.',"fStandard":'.$fStandard.',"fChild_material":'.$fChild_material.',"rows2":'.$list_data2.',"FStandard":'.$FStandard.',"FChild_material":'.$FChild_material.',"rows3":'.$list_data3.'}';
	  }
  
  }
  echo $json;
  $conn->close();
?>