<?php 
  require('../../../conn.php');
  header("Access-Control-Allow-Origin: *");
  require_once '../../../PHPExcel/IOFactory.php'; 
  require_once '../../../PHPExcel/Shared/Date.php';
  // 初始化上传限时和大小
  set_time_limit(0);
  ini_set("memory_limit","1024M");
  $objReader = PHPExcel_IOFactory::createReader('Excel5');//use excel2007 for 2007 format
  $objPHPExcel = PHPExcel_IOFactory::load($_FILES['file']['tmp_name']); // 读取xlsx文件
  $sheet = $objPHPExcel->getSheet(0); 
  $highestRow = $sheet->getHighestRow(); // 取得总行数 
  $highestColumn = $sheet->getHighestColumn(); // 取得总列数
  $k = 0;
  //循环读取excel文件,读取一条,插入一条
  //j表示从哪一行开始读取
  //$a表示列号
  $time = time();
  for($j=2;$j<=$highestRow;$j++)
  {
      $a = $objPHPExcel->getActiveSheet()->getCell("A".$j)->getValue();//获取A列的值,名字name
      $b = $objPHPExcel->getActiveSheet()->getCell("B".$j)->getValue();//获取B列的值,职位job
      $c = $objPHPExcel->getActiveSheet()->getCell("C".$j)->getValue();//获取C列的值,工号gNum
      $d = $objPHPExcel->getActiveSheet()->getCell("D".$j)->getValue();//获取D列的值,部门department
      $e = $objPHPExcel->getActiveSheet()->getCell("E".$j)->getValue();//获取E列的值,手机phone
      $f = $objPHPExcel->getActiveSheet()->getCell("F".$j)->getValue();//获取F列的值,终端terminal
      $g = $objPHPExcel->getActiveSheet()->getCell("G".$j)->getValue();//获取G列的值,车间workShop
      //搜索工号相同的数据
      $sqlNum = "SELECT * from user where gNum='$c'";
      $result = $conn->query($sqlNum);
      if ($result->num_rows > 0) {
        $ret_data["error"]=$c;
        $json=json_encode($ret_data);
        echo $json;
      }else{
        //插入数据
        $sql = "INSERT INTO user (gNum,name,phone_number,job,department,terminal,workShop,ctime) VALUES ('$c','$a','$e','$b','$d','$f','$g',$time)";
        $res = $conn->query($sql);
      }
      
  }
  $json = '{"result":"success"}';
  echo $json;
?>