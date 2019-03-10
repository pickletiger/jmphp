<?php
    require("../../conn.php");
	require_once '../../phpExcel/Classes/PHPExcel.php';
    header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
    header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
    header('Access-Control-Allow-Methods: GET, POST, PUT,DELETE');
    $name = $_POST["name"];
	// 首先创建一个新的对象  PHPExcel object  
	$objPHPExcel = new PHPExcel();  
	  
	// 设置文件的一些属性，在xls文件——>属性——>详细信息里可以看到这些值，xml表格里是没有这些值的  
	$objPHPExcel  
	      ->getProperties()  //获得文件属性对象，给下文提供设置资源  
	      ->setCreator("shun")                 //设置文件的创建者  
	      ->setLastModifiedBy( "shun")          //设置最后修改者  
	      ->setTitle( "bt" )    //设置标题  
	      ->setSubject( "zt" );  //设置主题  
	//    ->setDescription( "Test document for Office 2007 XLSX, generated using PHP classes.") //设置备注  
	//    ->setKeywords( "office 2007 openxml php")        //设置标记  
	//    ->setCategory( "Test result file");                //设置类别  
	// 位置aaa  *为下文代码位置提供锚  
	// 设置表头
	$objPHPExcel->setActiveSheetIndex(0)             
	            ->setCellValue( 'A1', '序号' )        
	            ->setCellValue( 'B1', '产品名称' )    
	            ->setCellValue( 'C1', '工单')         
	            ->setCellValue( 'D1', '零件图号')         
	            ->setCellValue( 'E1', '名称' )
				->setCellValue( 'F1', '规格' )
				->setCellValue( 'G1', '开料尺寸' )
				->setCellValue( 'H1', '加工工艺路线' )
				->setCellValue( 'I1', '数量' );         
	//设置样式：  
	$objPHPExcel->getActiveSheet()->getStyle('A1:I1')->getFont()->setBold(true); //多个单元格 加粗 
	
		//写入内容
	  $sql = "SELECT A.modid,A.figure_number,A.name,A.standard,A.count,A.child_material,A.remark,B.id as routeid,C.name as product_name,C.number,B.route FROM part A,route B,project C  WHERE B.isfinish='3' and A.modid=B.modid and  B.pid=C.id ";
	  $res = $conn->query($sql);
	  if($res->num_rows > 0 ){
	    $i = 2;
	    while($row = $res->fetch_assoc()){
	      $number = explode("#",$row['number']);
		  $objPHPExcel->setActiveSheetIndex(0)  
	        ->setCellValue('A'.$i, $i-1)
			->setCellValue('B'.$i, $number[1] . $row['product_name'])
			->setCellValue('C'.$i, $number[0] . "#")
			->setCellValue('D'.$i, $row['figure_number'])
			->setCellValue('E'.$i, $row['name'])
			->setCellValue('F'.$i, $row['child_material'])
			->setCellValue('G'.$i, $row['standard'])
			->setCellValue('H'.$i, $row['route'])
			->setCellValue('I'.$i, $row['count']);  
	      $i++;
	    }
	  }
	//得到当前活动的表,注意下文教程中会经常用到$objActSheet  
	$objActSheet = $objPHPExcel->getActiveSheet();  
	// 位置bbb  *为下文代码位置提供锚  
	// 给当前活动的表设置名称  
	$objActSheet->setTitle('用户表');  
	//为文档命名
	$excelName = "export.xlsx";
	// 保存Excel 2007格式文件，保存路径为当前路径，名字为export.xlsx
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	$objWriter->save($excelName);
	
	$data["excelUrl"] = $excelName;
    echo json_encode($excelName);
    exit;  
?>