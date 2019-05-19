<?php
	require("../../conn.php");
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
	set_time_limit(0); //使无响应时间限制
	$ret_data = '';
		
	$ret_data["ftype"] = isset($_POST["ftype"])?$_POST["ftype"] : '';
	$ret_data["name"] = isset($_POST["name"])?$_POST["name"] : '';
	$ret_data["number"] = isset($_POST["number"])?$_POST["number"] : '';
	$ret_data["type"] = isset($_POST["type"])?$_POST["type"] : '';
	$ret_data["date"] = isset($_POST["date"])?$_POST["date"] : '';
		
	$name = $ret_data["name"];
	$number = $ret_data["number"];
	$type = $ret_data["type"];
	$date = $ret_data["date"];
	//查询数据库，检查是否已存在该项目
	$asql = "SELECT id FROM project WHERE number = '$number'";
	$ares = $conn->query($asql);
	
	if($ret_data["ftype"] == 'application/vnd.ms-excel'&& $ares->num_rows==0){
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
   	 	$arr = explode('#',$number);
   	 	$pNumber = $arr[0]."#";
   	 	$ctime = date('Y-m-d H:i:s');
	 	$bsql = "INSERT INTO project (name,type,number,pNumber,end_date,isfinish,ctime)VALUES('$name','$type','$number','$pNumber','$date','2','$ctime')";
		$bres = $conn->query($bsql);
	  	$csql = "SELECT id FROM project WHERE number = '$number'";
	 	$cres = $conn->query($csql);
	  	if($cres->num_rows>0){
	 		while($crow = $cres->fetch_assoc()){
	 			$id = $crow["id"];
	  		}
	 	}
    	//拆拼接字符串，为下面比对插入数据库做准备
    	$str = explode("#",$number);
    	//项目名
    	$projectname = $name.$str[1];
//  	$ret_data["projectname"] =$projectname;
    	
		// $highestColumn = $sheet->getHighestColumn(); // 取得总列数
	    //循环读取excel表格,读取一条,插入一条
	    //j表示从哪一行开始读取  从第二行开始读取，因为第一行是标题不保存
	    //$a表示列号
		for($j=2;$j<=$highestRow;$j++)  
	    {
	        $e = $objPHPExcel->getActiveSheet()->getCell("E".$j)->getValue();//获取E(所属部件)列的值
	        $i = $objPHPExcel->getActiveSheet()->getCell("I".$j)->getValue();//获取I(图号)列的值
	        $partname = $objPHPExcel->getActiveSheet()->getCell("J".$j)->getValue();//获取J(名称)列的值
	        $k = $objPHPExcel->getActiveSheet()->getCell("K".$j)->getValue();//获取K(材料)列的值
	        $l = $objPHPExcel->getActiveSheet()->getCell("L".$j)->getValue();//获取L(子件物料名称)列的值
	        $m = $objPHPExcel->getActiveSheet()->getCell("M".$j)->getValue();//获取M(规格型号)列的值
	        $n = $objPHPExcel->getActiveSheet()->getCell("N".$j)->getValue();//获取N(ABC分类)列的值
	        $o = $objPHPExcel->getActiveSheet()->getCell("O".$j)->getValue();//获取O(定额数量)列的值
	        $p = $objPHPExcel->getActiveSheet()->getCell("P".$j)->getValue();//获取P(辅助单位)列的值
	        $q = $objPHPExcel->getActiveSheet()->getCell("Q".$j)->getValue();//获取Q(工艺路线)列的值
	        $r = $objPHPExcel->getActiveSheet()->getCell("R".$j)->getValue();//获取R(备注)列的值
	        $s = $objPHPExcel->getActiveSheet()->getCell("S".$j)->getValue();//获取S(数量)列的值
	        $t = $objPHPExcel->getActiveSheet()->getCell("T".$j)->getValue();//获取T(MoDId)列的值
	        $u = $objPHPExcel->getActiveSheet()->getCell("U".$j)->getValue();//获取U(子件物料编码)列的值
	        $y = $objPHPExcel->getActiveSheet()->getCell("Y".$j)->getValue();//获取Y(子件单位)列的值
	        
	        //去除读取的数据头尾的空格
	        $e = trim(html_entity_decode($e),chr(0xc2).chr(0xa0));
	        $i = trim(html_entity_decode($i),chr(0xc2).chr(0xa0));
	        $partname = trim(html_entity_decode($partname),chr(0xc2).chr(0xa0));
	        $k = trim(html_entity_decode($k),chr(0xc2).chr(0xa0));
	        $l = trim(html_entity_decode($l),chr(0xc2).chr(0xa0));
	        $m = trim(html_entity_decode($m),chr(0xc2).chr(0xa0));
	        $n = trim(html_entity_decode($n),chr(0xc2).chr(0xa0));
	        $o = trim(html_entity_decode($o),chr(0xc2).chr(0xa0));
	        $p = trim(html_entity_decode($p),chr(0xc2).chr(0xa0));
	        $q = trim(html_entity_decode($q),chr(0xc2).chr(0xa0));
	        $r = trim(html_entity_decode($r),chr(0xc2).chr(0xa0));
	        $s = trim(html_entity_decode($s),chr(0xc2).chr(0xa0));
	        $t = trim(html_entity_decode($t),chr(0xc2).chr(0xa0));
	        $u = trim(html_entity_decode($u),chr(0xc2).chr(0xa0));
	        $y = trim(html_entity_decode($y),chr(0xc2).chr(0xa0));
	        
	        if($t==''){
	        	break;
	        }
//	        echo $e;
			if($q) {
				$ret_data["route"] = $q;
				$route_arr = explode('→',$q);
				$length = count($route_arr);
				for($route_i=1;$route_i<$length;$route_i++){
					$dsql = "INSERT INTO route (pid,modid,route,listid,route_line,isfinish,pNumber)VALUES('$id','$t','$route_arr[$route_i]','$route_i','$q','3','$pNumber')";
					$dres = $conn->query($dsql);
					
				}
			}
			
	       
	        if($partname!=$projectname){
		    	$sql = "INSERT INTO part (fid,belong_part,pNumber,figure_number,name,material,child_material,standard,radio,category,quantity,unit,count,modid,child_number,child_unit,remark,isfinish) VALUES('$id','$e','$pNumber','$i','$partname','$k','$l','$m','2','$n','$o','$p','$s','$t','$u','$y','$r','3')"; //null 为主键id，自增可用null表示自动添加
		    	$res= $conn->query($sql);
	        }else {
	        	$sql = "UPDATE project SET modid='$t' WHERE id = '$id'"; //为项目添加modid
		    	$res= $conn->query($sql);
	        }
	        
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