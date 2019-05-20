<?php
	require("../../conn.php");
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
	set_time_limit(0); //使无响应时间限制
	$ret_data = '';
		
	$ret_data["ftype"] = isset($_POST["ftype"])?$_POST["ftype"] : '';
	
	if($ret_data["ftype"] == 'application/vnd.ms-excel'){
		//加载PHPExcel类库
		require_once("../../PHPExcel.php");
		require_once("../../PHPExcel/IOFactory.php");
		require_once("../../PHPExcel/Reader/Excel5.php");
	
		//xls格式为2003格式 即Excel5,此处为03格式
		$objReader = PHPExcel_IOFactory::createReader('Excel5'); 
		//xlsx格式为2007格式 即excel2007
//		$objReader = PHPExcel_IOFactory::createReader('excel2007'); 
		
//		$_FILES['file'] 获取前端上传文件信息
//		$_FILES['file']['tmp_name'] 缓存的文件的路径
		$Excel = $_FILES['file'];
		$filetmp = $Excel['tmp_name'];
		//		$ret_data['file'] = $Excel;
		//		$ret_data['fname'] = $filetmp;
		
		$objReader->setReadDataOnly(true);//当数据格式有特殊字符时，使用该方法读取相应的单元格的数据，忽略任何格式的信息。
		
		$objPHPExcel = $objReader->load($filetmp);
		$sheet = $objPHPExcel->getSheet(0);  // 读取第一个sheet表
		$highestRow = $sheet->getHighestRow(); // 取得总行数 
   	 	$utime = date('Y-m-d H:i:s');
    	
		// $highestColumn = $sheet->getHighestColumn(); // 取得总列数
	    //循环读取excel表格,读取一条,插入一条
	    //j表示从哪一行开始读取  从第二行开始读取，因为第一行是标题不保存
	    //$a表示列号
		for($j=2;$j<=$highestRow;$j++)  
	    {
	        $a = $objPHPExcel->getActiveSheet()->getCell("A".$j)->getValue();//获取E(所属部件)列的值
	        $b = $objPHPExcel->getActiveSheet()->getCell("B".$j)->getValue();//获取I(图号)列的值
	        
	        //去除读取的数据头尾的空格
	        $a = trim(html_entity_decode($a),chr(0xc2).chr(0xa0));
	        $b = trim(html_entity_decode($b),chr(0xc2).chr(0xa0));
			
	       
		    $sql = "UPDATE part SET radio = '1',utime = '$utime' WHERE pNumber = '$a' AND figure_number = '$b'"; 
		    $res= $conn->query($sql);
	        
      }
//		$conn->close();
		$ret_data["success"]="success";
	}else {
		$conn->close();
		$ret_data["success"] = "error";
	}
	
	
	$json = json_encode($ret_data);
	echo $json;
?>