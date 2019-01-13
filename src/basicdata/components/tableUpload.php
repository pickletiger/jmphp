<?php 
  require('../../../conn.php');
  header("Access-Control-Allow-Origin: *");
  require_once '../../../PHPExcel/IOFactory.php'; 
  require_once '../../../PHPExcel/Shared/Date.php';
  // 初始化上传限时和大小
  set_time_limit(0);
  ini_set("memory_limit","1024M");

  $objPHPExcel = PHPExcel_IOFactory::load($_FILES['file']['tmp_name']); // 读取xlsx文件
  // global $getDate;
  // global $startDate; // 缓存实际开始时间
  // $A = $_POST['K'];
  // $B = $_POST['T-焊前'];
  // $C = $_POST['I组焊'];
  // $D = $_POST['T装配'];
  // $E = $_POST['F'];
  // $F = $_POST['W'];
  // $G = $_POST['D装配'];
  // $H = $_POST['G'];
  // $I = $_POST['L组焊'];
  // $J = $_POST['I/L装配'];
  // $K = $_POST['外协轨道立柱/塔架/金字架架/转盘'];
  // $L = $_POST['厂内轨道立柱/塔架'];
  // $M = $_POST['座舱/车体/船体'];
  // $getDate = array($A,$B,$C,$D,$E,$F,$G,$H,$I,$J,$K,$L,$M);
  $flag1 = false; // 遍历到表头，开始导入
  $flag2 = false; // 遍历到最后一行结束
  $flag3 = 1; // 判断每一行的工号是否为空
  $arr = []; // 表格每行的值
  $Arr = []; // 表格所有行的值

  // 遍历表
  foreach ($objPHPExcel->getWorksheetIterator() as $worksheet){   
    // echo 'Worksheet -' ,$worksheet->getTitle() , PHP_EOL; 

    //遍历行
    foreach ($worksheet->getRowIterator() as $row){ 

      $cellIterator=$row->getCellIterator();   //得到所有列
      $cellIterator->setIterateOnlyExistingCells(false);
      $flag3 = 1; 
      // 遍历列
      foreach ($cellIterator as $cell){
        // echo $cell->getCoordinate(),'-',$cell->getCalculatedValue(),PHP_EOL;
        if($flag3==3) {
          //工号一列是否为空，空即停止遍历
          $flag2 = $cell->getCalculatedValue(); 
        }
        // 判断是否遍历到表头
        if(strpos($cell->getCalculatedValue(),'序号')!==false&&!$flag1) {
          $flag1 = true;
          break;
        }
        // else if($flag1) {
        //   // push每列
        //   array_push($arr, changeDate($cell,$flag3)); 
        // }
        $flag3++;
      }
      
      if(!$flag2) {
        break; // 遍历到最后一行,退出循环
      }else if($flag1) {
        array_push($Arr, $arr); // push每行
        $arr = []; // 清空行
      }
    }
    break;
  }

  // 查询数据库所有产品的名称和编号 
  $sql2 = "SELECT name,job,gNum,department,phone_number FROM user ";
  $result2 = $conn->query($sql2);
  $selectArr = []; 
  $delete = [];
  while($row = $result2->fetch_assoc()) {
    array_push($selectArr, array('name' => $row['name'], 'job' => $row['job'] ,'gNum' => $row['gNum'] ,  'department' => $row['department'], 'phone_number' => $row['phone_number']));
  }

  // 遍历导入的数据是否与已有数据重复
  array_shift($Arr);
  foreach ($Arr as $key1 => $value1) {
    foreach ($selectArr as $key2 => $value2) {
      if($value1[1]==$value2['name'] && $value1[3]==$value2['gNum']) {
        // 重复数据
        array_push($delete, $value2['gNum']);
        break;
      }
    }
  }

  // 删除重复的数据
  $result1 = 1;
  if(count($delete)) {
    $val1 = '';
    foreach ($delete as $key => $value) {
      $val1.=$value.','; 
    }
    $val1 = substr($val1, 0, -1);// 删除字符串末尾的逗号
    $sql1 = 'DELETE FROM plan_table WHERE id in (';
    $sql1.=$val1.')';
    $result1 = $conn->query($sql1);
  }

  // 添加创建时间


  if($result1) {
    // 拼接需要插入的数据
    // $val2 = ''; 
    // for($i=0; $i<count($Arr); $i++) {
    //   for($j=0; $j<38; $j++) {
    //     if($j==0) {
    //       $val2.='("'.$Arr[$i][$j].'",';
    //     }else if($j==37) {
    //       $val2.='"'.$Arr[$i][$j].'"),';
    //     }else {
    //       $val2.='"'.$Arr[$i][$j].'",';
    //     }
    //   }
    // }

    $json = '';
    $val2 = substr($val2, 0, -1);// 删除字符串末尾的逗号
    $sql2 = "INSERT INTO user (name,job,gNum,department,phone_number) VALUE ".$val2;
    $result2 = $conn->query($sql2);
    $json = '{"result":"'.$sql2.'"}';
  }else {
    $json = '{"result":"'.false.'"}';
  }
  echo $json;

  // 将日期转换为Y/M/D格式
  // function changeDate ($cell,$flag) {
  //   $val = $cell->getCalculatedValue();
  //   // 需要转为时间格式的字段
  //   if($flag>18) {  
  //     // 是否为工艺完成时间
  //     if(25<$flag && $flag<39) {
  //       // 如果有实际计划开始时间则处理工艺时间
  //       if(is_float($GLOBALS["startDate"])) {
          
  //         $val = date("Y/n/j",PHPExcel_Shared_Date::ExcelToPHP($GLOBALS["startDate"])+$GLOBALS["getDate"][$flag-26]*24*3600); 
  //       }else {
  //         $val = is_float($val)?$cell->getFormattedValue() : $val;
  //       }
  //     }else {

  //       // 如果遍历到实际开始时间则缓存
  //       if($flag == 23){
  //         $GLOBALS["startDate"] = $val;
  //       }
  //       $val = is_float($val)? $cell->getFormattedValue() : $val; // 如果数据为浮点数则转换为时间
  //     }
  //   }else {}
  //   // var_dump($val);
  //   return $val;
  // } 
?>