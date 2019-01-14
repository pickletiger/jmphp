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
      $a = $objPHPExcel->getActiveSheet()->getCell("A".$j)->getValue();//获取A列的值,物料编码material_num
      $b = $objPHPExcel->getActiveSheet()->getCell("B".$j)->getValue();//获取B列的值,物料名称material_name
      $c = $objPHPExcel->getActiveSheet()->getCell("C".$j)->getValue();//获取C列的值,规格specifications
      $d = $objPHPExcel->getActiveSheet()->getCell("D".$j)->getValue();//获取D列的值,数量amount
      //搜索工号相同的数据
      $sqlNum = "SELECT * from material where material_num='$a'";
      $result = $conn->query($sqlNum);
      if ($result->num_rows > 0) {
        $ret_data["error"]=$a;
        $json=json_encode($ret_data);
        echo $json;
      }else{
        //插入数据
        $sql = "INSERT INTO material(material_num,material_name,specifications,amount,ctime) VALUES ('$a','$b','$c','$d',$time)";
        $res = $conn->query($sql);
      }
      
  }
  $json = '{"result":"success"}';
  echo $json;
?>