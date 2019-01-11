<?php 
  require('../../conn.php');
  header("Access-Control-Allow-Origin: *");
  
  $pageSize = $_GET['pageSize']; // 每页条数
  $current = $_GET['current']; // 当前页
  $startPage = ($current-1)*$pageSize;

  $sql = 'SELECT * FROM plan_table LIMIT '.$startPage.','.$pageSize;
  $sql2 = 'SELECT COUNT(*) FROM plan_table';

  $result = $conn->query($sql);
  $result2 = $conn->query($sql2); 

  $data = '';
  $count = '';

  // 总条数
  if($result2->num_rows){ 

   $rs = $result2->fetch_assoc();
   $count = $rs['COUNT(*)'];
 
  }else{ 
    $count = 0;
  }

  // 获取数据
  if($result->num_rows) {
    while($row = $result->fetch_assoc()) {

      $data = $data.'{"序号":"'.$row["serial_number"].'",
        "产品名称":"'.$row["product_name"].'","编号":"'.$row["numbering"].'",
        "工单":"'.$row["work_order"].'","安装场地":"'.$row["installation_site"].'","外观":"'.$row["exterior"].'","颜色":"'.$row["colour"].'","拦河":"'.$row["barrage"].'","控制室":"'.$row["control_room"].'","基础预埋件":"'.$row["basic_embedded_parts"].'","站台/自动门":"'.$row["platform_automatic_door"].'","车体/座舱":"'.$row["car_body_cockpit"].'","备注":"'.$row["remarks"].'","优先级":"'.$row["priority"].'","玻璃钢预埋件状态":"'.$row["frp_embedded_parts"].'","排产工艺清单":"'.$row["scheduling_process_list"].'","重点产品":"'.$row["important_product"].'","重要材料信息":"'.$row["important_material_information"].'","交付时间":"'.$row["due_time"].'","进厂时间":"'.$row["arrival_time"].'","初始计划开始时间":"'.$row["initial_planning_start_time"].'","初始计划完成时间":"'.$row["initial_plan_completion_time"].'","实际计划开始时间":"'.$row["actual_planned_start_time"].'","实际计划完成时间":"'.$row["actual_planned_completion_time"].'","计划排产时间":"'.$row["planned_scheduling_time"].'","K":"'.$row["K"].'","T-焊前":"'.$row["T_before_welding"].'","T组焊":"'.$row["T_welding"].'","T装配":"'.$row["T_assembly"].'","F":"'.$row["F"].'","W":"'.$row["W"].'","D装配":"'.$row["D_assembly"].'","G":"'.$row["G"].'","L组焊":"'.$row["L_welding"].'","I/L装配":"'.$row["IL_assembly"].'","外协轨道立柱/塔架/金字架架/转盘":"'.$row["outer_track_column"].'","厂内轨道立柱/塔架":"'.$row["in_plant_track_column"].'","座舱/车体/船体":"'.$row["cockpit"].'"
      },';
    }
    $json = '{"ret":"'.true.'","count":"'.$count.'","data":['.substr($data, 0, -1).']}';  

  }else {
    $json = '{"ret":"'.false.'"}';
  }

  echo $json;
?>