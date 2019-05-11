<?php
	header("Access-Control-Allow-Origin: *");
	require_once '../../conn.php';
	require_once '../../classes/UploadFile.php';
	
	/*毫秒级的时间戳*/
	function getMillisecond() {
		list($t1, $t2) = explode(' ', microtime());
		return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
	}
	
	$flag = isset($_REQUEST["flag"]) ? $_REQUEST["flag"] : "";

	
	switch($flag){
		case "getTreeListData": //----------------获取树节点信息----------------------
			//返回给前端的数据
			$returnData = array(
				"state" => "success",
				"message" => "",
				"data" => array(
					0 => array(
						"label" => "转马类",
						"children" => array()
					),
					1 => array(
						"label" => "滑行类",
						"children" => array()
					),
					2 => array(
						"label" => "陀螺类",
						"children" => array()
					),
					3 => array(
						"label" => "飞行塔类",
						"children" => array()
					),
					4 => array(
						"label" => "赛车类",
						"children" => array()
					),
					5 => array(
						"label" => "自控飞机类",
						"children" => array()
					),
					6 => array(
						"label" => "观览车类",
						"children" => array()
					),
					7 => array(
						"label" => "小火车类",
						"children" => array()
					),
					8 => array(
						"label" => "架空游览车类",
						"children" => array()
					),
					9 => array(
						"label" => "水上游乐设施",
						"children" => array()
					),
					10 => array(
						"label" => "碰碰车类",
						"children" => array()
					),
					11 => array(
						"label" => "电池车类",
						"children" => array()
					),
					12 => array(
						"label" => "摇摆类",
						"children" => array()
					),
					13 => array(
						"label" => "回旋类",
						"children" => array()
					),
					14 => array(
						"label" => "其他类",
						"children" => array()
					),
					15 => array(
						"label" => "科技娱乐类",
						"children" => array()
					)
				)
			);
			
			//查询大类树节点,0-15分别对应存货分类的16类
			for($j=0;$j<16;$j++){
				$sql = "SELECT `id`,`proname` FROM `weldingtree` where category='".$j."'";
				$result = $conn->query($sql);
				if($result->num_rows > 0){
					$i = 0;
					while($row = $result->fetch_assoc()){
						//通过时间戳获取同类表id
						$sql1 = "SELECT b.`id` FROM `weldingtree` a,`craftsmanshiptree` b WHERE a.`id`='".$row["id"]."' and a.ctime=b.ctime";
						$result1 = $conn->query($sql1);
						$row1 = $result1->fetch_assoc();
						$returnData["data"][$j]["children"][$i]["tableFlag"] = 1;//用于判断第二层树
						$returnData["data"][$j]["children"][$i]["label"] = $row["proname"];
						$returnData["data"][$j]["children"][$i]["relateId"] = $row["id"];
						$returnData["data"][$j]["children"][$i]["children"][0]["label"] = "焊接工艺及检验记录";
						$returnData["data"][$j]["children"][$i]["children"][0]["thereFlag"] = 1;
						$returnData["data"][$j]["children"][$i]["children"][0]["thereId"] = $row["id"];
						$returnData["data"][$j]["children"][$i]["children"][1]["label"] = "机械制造工艺及检验表";
						$returnData["data"][$j]["children"][$i]["children"][1]["thereFlag"] = 2;
						$returnData["data"][$j]["children"][$i]["children"][1]["thereId"] = $row1["id"];
						$i++;
					}
				}
			}
			
			
			$json = json_encode($returnData);
			echo $json;
			break;
		
		case "saveTreeListData": //--------------------保存新建树节点------------------------
			//接收数据
			$tableFlag = isset($_POST["tableFlag"]) ? $_POST["tableFlag"] :"";//判断保存到焊接还是工艺的表:1为焊接，2为工艺
			$proname = isset($_POST["proname"]) ? $_POST["proname"] :"";
			$procode = isset($_POST["procode"]) ? $_POST["procode"] :"";
			
			//返回给前端的数据
			$returnData = array(
				"state" => "success",
				"message" => ""				
			);
			
			$sql = "INSERT INTO `weldingtree`(`proname`,`procode`,`category`,`ctime`) VALUES('".$proname."','".$procode."','".$tableFlag."','".time()."')";
			$result = $conn->query($sql);
			$sql1 = "INSERT INTO `craftsmanshiptree`(`proname`,`procode`,`category`,`ctime`) VALUES('".$proname."','".$procode."','".$tableFlag."','".time()."')";
			$result1 = $conn->query($sql1);
			if(!$result&&!$result1){
				$returnData["state"] = "fail";
				$returnData["message"] = "服务器错误";
			}
			
			

			$json = json_encode($returnData);
			echo $json;
			break;
		
		case "insertData" ://------------插入新的焊接数据------------------
			//接收数据
			$treeId = isset($_POST["treeId"]) ? $_POST["treeId"] :"";//表【weldingtree】的id
			$weldingtable = isset($_POST["weldingtable"]) ? json_decode($_POST["weldingtable"],TRUE) : array();
			$weldingtableone = isset($_POST["weldingtableone"]) ? json_decode($_POST["weldingtableone"],TRUE) : array();
			$weldingtabletwo = isset($_POST["weldingtabletwo"]) ? json_decode($_POST["weldingtabletwo"],TRUE) : array();
			$weldingtablethree = isset($_POST["weldingtablethree"]) ? json_decode($_POST["weldingtablethree"],TRUE) : array();
			$weldingtablefour = isset($_POST["weldingtablefour"]) ? json_decode($_POST["weldingtablefour"],TRUE) : array();
			
			//返回给前端的数据
			$returnData = array(
				"state" => "success",
				"message" => ""
			);
			
			//保存图片，返回保存路径
			$fileSaveSql = '';//保存的路径，在src目录下
			if(count($_FILES) > 0){
				$fileSaveDir = "../uploadfiles";//文件存放目录
				$fileSaveName = getMillisecond();//无后缀的文件名
				$uploadfileclass = new UploadFile($_FILES["myfile"],$fileSaveDir,$fileSaveName);
				$fileSaveSql = $uploadfileclass->uploadFile();
				$fileSaveSql = substr($fileSaveSql, 3);
			}
			
			//保存首表信息，返回自增id
			$sql = "INSERT INTO `weldingtable`(`weldingtree_id`,`processnumber`,`quantity`,`workpiecenumber`,`workshop`,`workordernumber`,`producname`,`productcode`,`partname`,`partdrawingnumber`,`finallyresult`,`inspectorsingnature`,`finallydate`,`weldingsequence`,`weldingnumbermap`,`ctime`) VALUES(";
			$sql .= "'".$treeId."','".$weldingtable["processNumber"]."','".$weldingtable["quantity"]."','".$weldingtable["workpieceNumber"]."','".$weldingtable["workshop"]."','".$weldingtable["workOrderNumber"]."','".$weldingtable["productName"]."','".$weldingtable["productCode"]."','".$weldingtable["partName"]."','".$weldingtable["partDrawingNumber"]."','".$weldingtable["finalInspectionResult"]."','".$weldingtable["inspectorSingnature"]."','".$weldingtable["date"]."','".$weldingtable["weldingSequence"]."','".$fileSaveSql."','".time()."')";
			$autoIncrementId = $conn->query($sql) ? $conn->insert_id : "";//获取成功插入后的id
			
			if(!empty($autoIncrementId)){
				//保存第一个表信息
				foreach($weldingtableone as $ky => $datainfo){
					$sql = "INSERT INTO `weldingtableone`(`weldingtable_id`,`weldingnumber`,`materialfirst`,`specificationsfirst`,`materialsecond`,`specificationssecond`,`weldingmethod`,`grooveform`,`consumables`,`specifications`,`weldinglayer`,`weldingtrack`,`gas`,`current`,`actualcurrentfirst`,`actualcurrentsecond`,`voltage`,`actualvoltagefirst`,`actualvoltagesecond`,`specificationnumber`,`ratingnumber`,`flawdetection`,`steelstamp`,`ctime`) VALUES(";
					$sql .= "'".$autoIncrementId."','".$datainfo["weldNumber"]."','".$datainfo["materialAndSpecifications_1"]."','".$datainfo["materialAndSpecifications_1_thickness"]."','".$datainfo["materialAndSpecifications_2"]."','".$datainfo["materialAndSpecifications_2_thickness"]."','".$datainfo["weldingMethod"]."','".$datainfo["grooveForm"]."','".$datainfo["consumables"]."','".$datainfo["specifications"]."','".$datainfo["weldingLevel_numberOfLayers"]."','".$datainfo["weldingLevel_numberOftracks"]."','".$datainfo["protectiveGas"]."','".$datainfo["weldingCurrent"]."','".$datainfo["actualCurrent_1"]."','".$datainfo["actualCurrent_2"]."','".$datainfo["weldingVoltage"]."','".$datainfo["actualVoltage_1"]."','".$datainfo["actualVoltage_2"]."','".$datainfo["specificationNumber"]."','".$datainfo["ratingNumber"]."','".$datainfo["flawDetectionRequirements"]."','".$datainfo["steelStamp"]."','".time()."')";
					$conn->query($sql);
				}
				
				//保存第二个表信息
				foreach($weldingtabletwo as $ky => $datainfo){
					$sql = "INSERT INTO `weldingtabletwo`(`weldingtable_id`,`serialnumber`,`checkcontent`,`processrequirement`,`testresult`,`singnature`,`ctime`) VALUES(";
					$sql .= "'".$autoIncrementId."','".$datainfo["serialNumber"]."','".$datainfo["checkContent"]."','".$datainfo["processRequirements"]."','".$datainfo["testResult"]."','".$datainfo["inspectorSingnature"]."','".time()."')";
					$conn->query($sql);
				}
				
				//保存第三个表信息
				foreach($weldingtablethree as $ky => $datainfo){
					$sql = "INSERT INTO `weldingtablethree`(`weldingtable_id`,`weldingnumber`,`requirementone`,`testresultone`,`singnatureone`,`requirementtwo`,`testresultonetwo`,`singnatureonetwo`,`requirementthree`,`testresultonethree`,`singnatureonethree`,`ctime`) VALUES(";
					$sql .= "'".$autoIncrementId."','".$datainfo["weldNumber"]."','".$datainfo["processRequirements_1"]."','".$datainfo["testResult_1"]."','".$datainfo["inspectorSingnature_1"]."','".$datainfo["processRequirements_2"]."','".$datainfo["testResult_2"]."','".$datainfo["inspectorSingnature_2"]."','".$datainfo["processRequirements_3"]."','".$datainfo["testResult_3"]."','".$datainfo["inspectorSingnature_3"]."','".time()."')";
					$conn->query($sql);
				}
				
				//保存第四个表信息
				foreach($weldingtablefour as $ky => $datainfo){
					$sql = "INSERT INTO `weldingtablefour`(`weldingtable_id`,`weldingnumber`,`requirementone`,`testresultone`,`singnatureone`,`requirementtwo`,`testresultonetwo`,`singnatureonetwo`,`requirementthree`,`testresultonethree`,`singnatureonethree`,`ctime`) VALUES(";
					$sql .= "'".$autoIncrementId."','".$datainfo["weldNumber"]."','".$datainfo["processRequirements_1"]."','".$datainfo["testResult_1"]."','".$datainfo["inspectorSingnature_1"]."','".$datainfo["processRequirements_2"]."','".$datainfo["testResult_2"]."','".$datainfo["inspectorSingnature_2"]."','".$datainfo["processRequirements_3"]."','".$datainfo["testResult_3"]."','".$datainfo["inspectorSingnature_3"]."','".time()."')";
					$conn->query($sql);
				}
				$returnData["message"] = "保存成功";
			}else{
				$returnData["state"] = "failure";
				$returnData["message"] = "自增值ID为空";
			}
			
			$json = json_encode($returnData);
			echo $json;
			break;
		
		case "updateData" ://------------更新新的焊接数据------------------
			//接收数据
			$weldingtable = isset($_POST["weldingtable"]) ? json_decode($_POST["weldingtable"],TRUE) : array();
			$weldingtableone = isset($_POST["weldingtableone"]) ? json_decode($_POST["weldingtableone"],TRUE) : array();
			$weldingtabletwo = isset($_POST["weldingtabletwo"]) ? json_decode($_POST["weldingtabletwo"],TRUE) : array();
			$weldingtablethree = isset($_POST["weldingtablethree"]) ? json_decode($_POST["weldingtablethree"],TRUE) : array();
			$weldingtablefour = isset($_POST["weldingtablefour"]) ? json_decode($_POST["weldingtablefour"],TRUE) : array();
			
			//返回给前端的数据
			$returnData = array(
				"state" => "success",
				"message" => "",
				"sql" => "",
				"error" => ""
			);
			
			//保存图片，返回保存路径
			$fileSaveSql = '';//保存的路径，在src目录下
			if(count($_FILES) > 0){
				//删除原有文件
				$sql = "SELECT `weldingnumbermap` FROM `weldingtable` WHERE id='".$weldingtable["contactId"]."'";
				$result = $conn->query($sql);
				if($result->num_rows > 0){
					while($row = $result->fetch_row()){
						$filePath = $row[0];
						$filePath = "../".$filePath;
						if(file_exists($filePath)){
							@unlink($filePath);
						}
					}
				}				
				$fileSaveDir = "../uploadfiles";//文件存放目录
				$fileSaveName = getMillisecond();//无后缀的文件名
				$uploadfileclass = new UploadFile($_FILES["myfile"],$fileSaveDir,$fileSaveName);
				$fileSaveSql = $uploadfileclass->uploadFile();
				$fileSaveSql = substr($fileSaveSql, 3);
			}
			
			//保存首表信息			
			$sql = "UPDATE `weldingtable` SET `processnumber`='".$weldingtable["processNumber"]."',`quantity`='".$weldingtable["quantity"]."',`workpiecenumber`='".$weldingtable["workpieceNumber"]."',`workshop`='".$weldingtable["workshop"]."'";
			$sql .= ",`workordernumber`='".$weldingtable["workOrderNumber"]."',`producname`='".$weldingtable["productName"]."',`productcode`='".$weldingtable["productCode"]."',`partname`='".$weldingtable["partName"]."',`partdrawingnumber`='".$weldingtable["partDrawingNumber"]."'";
			$sql .= ",`finallyresult`='".$weldingtable["finalInspectionResult"]."',`inspectorsingnature`='".$weldingtable["inspectorSingnature"]."',`finallydate`='".$weldingtable["date"]."',`weldingsequence`='".$weldingtable["weldingSequence"]."'";
			if(!empty($fileSaveSql)){
				$sql .= ",`weldingnumbermap`='".$fileSaveSql."'";
			}
			$sql .= " WHERE id='".$weldingtable["contactId"]."'";
			$conn->query($sql);			
			$autoIncrementId = $weldingtable["contactId"];//主表id
			
			if(!empty($autoIncrementId)){
				//删除所有子表信息再重新保存
				$sql = "DELETE FROM `weldingtableone` WHERE `weldingtable_id`='".$autoIncrementId."';";
				$sql2 = "DELETE FROM `weldingtabletwo` WHERE `weldingtable_id`='".$autoIncrementId."';";
				$sql3 = "DELETE FROM `weldingtablethree` WHERE `weldingtable_id`='".$autoIncrementId."';";
				$sql4 = "DELETE FROM `weldingtablefour` WHERE `weldingtable_id`='".$autoIncrementId."';";
				
				
				if($conn->query($sql) && $conn->query($sql2) && $conn->query($sql3) && $conn->query($sql4)){
					//保存第一个表信息
					foreach($weldingtableone as $ky => $datainfo){
						$sql = "INSERT INTO `weldingtableone`(`weldingtable_id`,`weldingnumber`,`materialfirst`,`specificationsfirst`,`materialsecond`,`specificationssecond`,`weldingmethod`,`grooveform`,`consumables`,`specifications`,`weldinglayer`,`weldingtrack`,`gas`,`current`,`actualcurrentfirst`,`actualcurrentsecond`,`voltage`,`actualvoltagefirst`,`actualvoltagesecond`,`specificationnumber`,`ratingnumber`,`flawdetection`,`steelstamp`,`ctime`) VALUES(";
						$sql .= "'".$autoIncrementId."','".$datainfo["weldNumber"]."','".$datainfo["materialAndSpecifications_1"]."','".$datainfo["materialAndSpecifications_1_thickness"]."','".$datainfo["materialAndSpecifications_2"]."','".$datainfo["materialAndSpecifications_2_thickness"]."','".$datainfo["weldingMethod"]."','".$datainfo["grooveForm"]."','".$datainfo["consumables"]."','".$datainfo["specifications"]."','".$datainfo["weldingLevel_numberOfLayers"]."','".$datainfo["weldingLevel_numberOftracks"]."','".$datainfo["protectiveGas"]."','".$datainfo["weldingCurrent"]."','".$datainfo["actualCurrent_1"]."','".$datainfo["actualCurrent_2"]."','".$datainfo["weldingVoltage"]."','".$datainfo["actualVoltage_1"]."','".$datainfo["actualVoltage_2"]."','".$datainfo["specificationNumber"]."','".$datainfo["ratingNumber"]."','".$datainfo["flawDetectionRequirements"]."','".$datainfo["steelStamp"]."','".time()."')";
						$conn->query($sql);
					}
					
					//保存第二个表信息
					foreach($weldingtabletwo as $ky => $datainfo){
						$sql = "INSERT INTO `weldingtabletwo`(`weldingtable_id`,`serialnumber`,`checkcontent`,`processrequirement`,`testresult`,`singnature`,`ctime`) VALUES(";
						$sql .= "'".$autoIncrementId."','".$datainfo["serialNumber"]."','".$datainfo["checkContent"]."','".$datainfo["processRequirements"]."','".$datainfo["testResult"]."','".$datainfo["inspectorSingnature"]."','".time()."')";
						$conn->query($sql);
					}
					
					//保存第三个表信息
					foreach($weldingtablethree as $ky => $datainfo){
						$sql = "INSERT INTO `weldingtablethree`(`weldingtable_id`,`weldingnumber`,`requirementone`,`testresultone`,`singnatureone`,`requirementtwo`,`testresultonetwo`,`singnatureonetwo`,`requirementthree`,`testresultonethree`,`singnatureonethree`,`ctime`) VALUES(";
						$sql .= "'".$autoIncrementId."','".$datainfo["weldNumber"]."','".$datainfo["processRequirements_1"]."','".$datainfo["testResult_1"]."','".$datainfo["inspectorSingnature_1"]."','".$datainfo["processRequirements_2"]."','".$datainfo["testResult_2"]."','".$datainfo["inspectorSingnature_2"]."','".$datainfo["processRequirements_3"]."','".$datainfo["testResult_3"]."','".$datainfo["inspectorSingnature_3"]."','".time()."')";
						$conn->query($sql);
					}
					
					//保存第四个表信息
					foreach($weldingtablefour as $ky => $datainfo){
						$sql = "INSERT INTO `weldingtablefour`(`weldingtable_id`,`weldingnumber`,`requirementone`,`testresultone`,`singnatureone`,`requirementtwo`,`testresultonetwo`,`singnatureonetwo`,`requirementthree`,`testresultonethree`,`singnatureonethree`,`ctime`) VALUES(";
						$sql .= "'".$autoIncrementId."','".$datainfo["weldNumber"]."','".$datainfo["processRequirements_1"]."','".$datainfo["testResult_1"]."','".$datainfo["inspectorSingnature_1"]."','".$datainfo["processRequirements_2"]."','".$datainfo["testResult_2"]."','".$datainfo["inspectorSingnature_2"]."','".$datainfo["processRequirements_3"]."','".$datainfo["testResult_3"]."','".$datainfo["inspectorSingnature_3"]."','".time()."')";
						$conn->query($sql);
					}
					$returnData["message"] = "保存成功";
				}else{
					$returnData["sql"] = $sql;
				}
				
				
			}else{
				$returnData["state"] = "failure";
				$returnData["message"] = "自增值ID为空";
			}
			
			$json = json_encode($returnData);
			echo $json;
			break;
		
		case "getTableListData" ://--------------查询焊接与制作工艺表格列表信息------------------
			//接收数据
			$tableFlag = isset($_GET["tableFlag"]) ? $_GET["tableFlag"] :"";//1为焊接，2为制造
			$relateId = isset($_GET["relateId"]) ? $_GET["relateId"] :"";
			
			//返回信息
			$returnData = array(
				"state" => "success",
				"message" => "",
				"data" => array()
			);
			
			switch($tableFlag){
				case "1":
					//装载数据-焊接信息
					$sql = "SELECT `id` AS `contactId`,`productcode`,`processnumber`,`producname`,`partname`,FROM_UNIXTIME(`ctime`,'%Y-%m-%d %H:%i:%s') AS ctime,'welding' AS diff FROM `weldingtable` WHERE `weldingtree_id`='".$relateId."'";
					$result = $conn->query($sql);
					if($result->num_rows > 0){
						$returnData["message"] = "获取成功";
						$i = 0;
						while($row = $result->fetch_assoc()){
							$returnData["data"][$i] = $row;
							$i++;
						}
					}else{
						$returnData["message"] = "没有数据";
					}
					break;
				case "2":
					//装载数据-制造信息
					$sql = "SELECT `id` AS `contactId`,`productdrawnumber` AS `productcode`,`ownpartdrawnumber` AS `processnumber`,productname AS `producname`,`partname`,FROM_UNIXTIME(`ctime`,'%Y-%m-%d %H:%i:%s') AS ctime,'craftsmanship' AS diff FROM `craftsmanshiptable` WHERE `craftsmanshiptree_id`='".$relateId."'";
					$result = $conn->query($sql);
					if($result->num_rows > 0){
						$returnData["message"] = "获取成功";
						$i = 0;
						while($row = $result->fetch_assoc()){
							$returnData["data"][$i] = $row;
							$i++;
						}
					}else{
						$returnData["message"] = "没有数据";
					}
					break;
				default :
					
			}
			
			
			
			$json = json_encode($returnData);
			echo $json;
			break;
		
		case "getWeldingInfoData" ://----------------根据ID获取焊接相应的数据--------------------------
			//接收数据
			$contactId = isset($_GET["contactID"]) ? $_GET["contactID"] : "";
			
			//返回数据
			$returnData = array(
				"state" => "success",
				"message" => "",
				"data" => array(
					"weldingTableOne" => array(),
					"weldingTableTwo_1" => array(),
					"weldingTableTwo_2" => array(),
					"weldingTableThree_1" => array(),
					"weldingTableThree_2" => array(),
					"weldingTableThree_3" => array(),
					"weldngTableFour" => array()
				)
			);
			
			//查询数据
			if(!empty($contactId)){
				//查询首表信息
				$sql = "SELECT `processnumber`,`quantity`,`workpiecenumber`,`workshop`,`workordernumber`,`producname`,`productcode`,`partname`,`partdrawingnumber`,`finallyresult`,`inspectorsingnature`,`finallydate`,`weldingsequence`,`weldingnumbermap` FROM `weldingtable` WHERE `id`='".$contactId."'";
				$result = $conn->query($sql);
				if($result->num_rows > 0){
					while($row = $result->fetch_assoc()){
						$returnData["data"]["weldingTableOne"]["contactId"] = $contactId;
						$returnData["data"]["weldingTableOne"]["processNumber"] = $row["processnumber"];
						$returnData["data"]["weldingTableOne"]["quantity"] = $row["quantity"];
						$returnData["data"]["weldingTableOne"]["workpieceNumber"] = $row["workpiecenumber"];
						$returnData["data"]["weldingTableOne"]["workshop"] = $row["workshop"];
						$returnData["data"]["weldingTableOne"]["workOrderNumber"] = $row["workordernumber"];
						$returnData["data"]["weldingTableOne"]["productName"] = $row["producname"];
						$returnData["data"]["weldingTableOne"]["productCode"] = $row["productcode"];
						$returnData["data"]["weldingTableOne"]["partName"] = $row["partname"];
						$returnData["data"]["weldingTableOne"]["partDrawingNumber"] = $row["partdrawingnumber"];
						
						$returnData["data"]["weldingTableThree_3"]["finalInspectionResult"] = $row["finallyresult"];
						$returnData["data"]["weldingTableThree_3"]["inspectorSingnature"] = $row["inspectorsingnature"];
						$returnData["data"]["weldingTableThree_3"]["date"] = $row["finallydate"];
						
						$returnData["data"]["weldngTableFour"]["weldingSequence"] = $row["weldingsequence"];
						$returnData["data"]["weldngTableFour"]["weldNumberMap"] = $row["weldingnumbermap"];
						$returnData["data"]["weldngTableFour"]["imgHtm"] = "";
						
					}
				}
				//查询第一个表
				$sql = "SELECT `weldingnumber`,`materialfirst`,`specificationsfirst`,`materialsecond`,`specificationssecond`,`weldingmethod`,`grooveform`,`consumables`,`specifications`,`weldinglayer`,`weldingtrack`,`gas`,`current`,`actualcurrentfirst`,`actualcurrentsecond`,`voltage`,`actualvoltagefirst`,`actualvoltagesecond`,`specificationnumber`,`ratingnumber`,`flawdetection`,`steelstamp` FROM `weldingtableone` WHERE `weldingtable_id`='".$contactId."' ORDER BY `id`";
				$result = $conn->query($sql);
				if($result->num_rows > 0){
					$i = 0;
					while($row = $result->fetch_assoc()){
						$returnData["data"]["weldingTableTwo_1"][$i]["weldNumber"] = $row["weldingnumber"];
						$returnData["data"]["weldingTableTwo_1"][$i]["materialAndSpecifications_1"] = $row["materialfirst"];
						$returnData["data"]["weldingTableTwo_1"][$i]["materialAndSpecifications_1_thickness"] = $row["specificationsfirst"];
						$returnData["data"]["weldingTableTwo_1"][$i]["materialAndSpecifications_2"] = $row["materialsecond"];
						$returnData["data"]["weldingTableTwo_1"][$i]["materialAndSpecifications_2_thickness"] = $row["specificationssecond"];
						$returnData["data"]["weldingTableTwo_1"][$i]["weldingMethod"] = $row["weldingmethod"];
						$returnData["data"]["weldingTableTwo_1"][$i]["grooveForm"] = $row["grooveform"];
						$returnData["data"]["weldingTableTwo_1"][$i]["consumables"] = $row["consumables"];
						$returnData["data"]["weldingTableTwo_1"][$i]["specifications"] = $row["specifications"];
						$returnData["data"]["weldingTableTwo_1"][$i]["weldingLevel_numberOfLayers"] = $row["weldinglayer"];
						$returnData["data"]["weldingTableTwo_1"][$i]["weldingLevel_numberOftracks"] = $row["weldingtrack"];
						$returnData["data"]["weldingTableTwo_1"][$i]["protectiveGas"] = $row["gas"];
						$returnData["data"]["weldingTableTwo_1"][$i]["weldingCurrent"] = $row["current"];
						$returnData["data"]["weldingTableTwo_1"][$i]["actualCurrent_1"] = $row["actualcurrentfirst"];
						$returnData["data"]["weldingTableTwo_1"][$i]["actualCurrent_2"] = $row["actualcurrentsecond"];
						$returnData["data"]["weldingTableTwo_1"][$i]["weldingVoltage"] = $row["voltage"];
						$returnData["data"]["weldingTableTwo_1"][$i]["actualVoltage_1"] = $row["actualvoltagefirst"];
						$returnData["data"]["weldingTableTwo_1"][$i]["actualVoltage_2"] = $row["actualvoltagesecond"];
						$returnData["data"]["weldingTableTwo_1"][$i]["specificationNumber"] = $row["specificationnumber"];
						$returnData["data"]["weldingTableTwo_1"][$i]["ratingNumber"] = $row["ratingnumber"];
						$returnData["data"]["weldingTableTwo_1"][$i]["flawDetectionRequirements"] = $row["flawdetection"];
						$returnData["data"]["weldingTableTwo_1"][$i]["steelStamp"] = $row["steelstamp"];
						
						$i++;
					}
				}
				//查询第二个表
				$sql = "SELECT `serialnumber`,`checkcontent`,`processrequirement`,`testresult`,`singnature` FROM `weldingtabletwo` WHERE `weldingtable_id`='".$contactId."' ORDER BY `id`";
				$result = $conn->query($sql);
				if($result->num_rows > 0){
					$i = 0;
					while($row = $result->fetch_assoc()){
						$returnData["data"]["weldingTableTwo_2"][$i]["serialNumber"] = $row["serialnumber"];
						$returnData["data"]["weldingTableTwo_2"][$i]["checkContent"] = $row["checkcontent"];
						$returnData["data"]["weldingTableTwo_2"][$i]["processRequirements"] = $row["processrequirement"];
						$returnData["data"]["weldingTableTwo_2"][$i]["testResult"] = $row["testresult"];
						$returnData["data"]["weldingTableTwo_2"][$i]["inspectorSingnature"] = $row["singnature"];
						
						$i++;
					}
				}
				//查询第三个表
				$sql = "SELECT `weldingnumber`,`requirementone`,`testresultone`,`singnatureone`,`requirementtwo`,`testresultonetwo`,`singnatureonetwo`,`requirementthree`,`testresultonethree`,`singnatureonethree` FROM `weldingtablethree` WHERE `weldingtable_id`='".$contactId."' ORDER BY `id`";
				$result = $conn->query($sql);
				if($result->num_rows > 0){
					$i = 0;
					while($row = $result->fetch_assoc()){
						$returnData["data"]["weldingTableThree_1"][$i]["weldNumber"] = $row["weldingnumber"];
						$returnData["data"]["weldingTableThree_1"][$i]["processRequirements_1"] = $row["requirementone"];
						$returnData["data"]["weldingTableThree_1"][$i]["testResult_1"] = $row["testresultone"];
						$returnData["data"]["weldingTableThree_1"][$i]["inspectorSingnature_1"] = $row["singnatureone"];
						$returnData["data"]["weldingTableThree_1"][$i]["processRequirements_2"] = $row["requirementtwo"];
						$returnData["data"]["weldingTableThree_1"][$i]["testResult_2"] = $row["testresultonetwo"];
						$returnData["data"]["weldingTableThree_1"][$i]["inspectorSingnature_2"] = $row["singnatureonetwo"];
						$returnData["data"]["weldingTableThree_1"][$i]["processRequirements_3"] = $row["requirementthree"];
						$returnData["data"]["weldingTableThree_1"][$i]["testResult_3"] = $row["testresultonethree"];
						$returnData["data"]["weldingTableThree_1"][$i]["inspectorSingnature_3"] = $row["singnatureonethree"];
						
						$i++;
					}
				}
				//查询第四个表
				$sql = "SELECT `weldingnumber`,`requirementone`,`testresultone`,`singnatureone`,`requirementtwo`,`testresultonetwo`,`singnatureonetwo`,`requirementthree`,`testresultonethree`,`singnatureonethree` FROM `weldingtablefour` WHERE `weldingtable_id`='".$contactId."' ORDER BY `id`";
				$result = $conn->query($sql);
				if($result->num_rows > 0){
					$i = 0;
					while($row = $result->fetch_assoc()){
						$returnData["data"]["weldingTableThree_2"][$i]["weldNumber"] = $row["weldingnumber"];
						$returnData["data"]["weldingTableThree_2"][$i]["processRequirements_1"] = $row["requirementone"];
						$returnData["data"]["weldingTableThree_2"][$i]["testResult_1"] = $row["testresultone"];
						$returnData["data"]["weldingTableThree_2"][$i]["inspectorSingnature_1"] = $row["singnatureone"];
						$returnData["data"]["weldingTableThree_2"][$i]["processRequirements_2"] = $row["requirementtwo"];
						$returnData["data"]["weldingTableThree_2"][$i]["testResult_2"] = $row["testresultonetwo"];
						$returnData["data"]["weldingTableThree_2"][$i]["inspectorSingnature_2"] = $row["singnatureonetwo"];
						$returnData["data"]["weldingTableThree_2"][$i]["processRequirements_3"] = $row["requirementthree"];
						$returnData["data"]["weldingTableThree_2"][$i]["testResult_3"] = $row["testresultonethree"];
						$returnData["data"]["weldingTableThree_2"][$i]["inspectorSingnature_3"] = $row["singnatureonethree"];
						
						$i++;
					}
				}
			}else{
				$returnData["state"] = "failure";
				$returnData["message"] = "无数据";
			}
			
			
			$json = json_encode($returnData);
			echo $json;
			break;
		
		case "DeleteWelding": //--------------------------------根据ID删除焊接信息----------------------------------------
			//接收数据
			$contactId = isset($_GET["contactId"]) ? $_GET["contactId"] : "";
			
			//返回数据
			$returnData = array(
				"state" => "success",
				"message" => ""				
			);
			//删除表【weldingtable】的图片
			$sql = "SELECT `weldingnumbermap` FROM `weldingtable` WHERE `id`='".$contactId."'";
			$result = $conn->query($sql);
			if($result->num_rows > 0){
				while($row = $result->fetch_assoc()){
					if(!empty($row["weldingnumbermap"])){
						$savePath = "../".$row["weldingnumbermap"];
						if(file_exists($savePath)){
							@unlink($savePath);
						}
					}
				}
			}
			//删除表【weldingtable】的数据
			$sql = "DELETE FROM `weldingtable` WHERE `id`='".$contactId."'";
			if(!$conn->query($sql)){
				$returnData["state"] = "fail";
				$returnData["message"] .= "表weldingtable记录删除失败；";
			}
			//删除表【weldingtableone】的数据
			$sql = "DELETE FROM `weldingtableone` WHERE `weldingtable_id`='".$contactId."'";
			if(!$conn->query($sql)){
				$returnData["state"] = "fail";
				$returnData["message"] .= "表weldingtableone记录删除失败；";
			}
			//删除表【weldingtabletwo】的数据
			$sql = "DELETE FROM `weldingtabletwo` WHERE `weldingtable_id`='".$contactId."'";
			if(!$conn->query($sql)){
				$returnData["state"] = "fail";
				$returnData["message"] .= "表weldingtabletwo记录删除失败；";
			}
			//删除表【weldingtablethree】的数据
			$sql = "DELETE FROM `weldingtablethree` WHERE `weldingtable_id`='".$contactId."'";
			if(!$conn->query($sql)){
				$returnData["state"] = "fail";
				$returnData["message"] .= "表weldingtablethree记录删除失败；";
			}
			//删除表【weldingtablefour】的数据
			$sql = "DELETE FROM `weldingtablefour` WHERE `weldingtable_id`='".$contactId."'";
			if(!$conn->query($sql)){
				$returnData["state"] = "fail";
				$returnData["message"] .= "表weldingtablefour记录删除失败；";
			}
			
			$json = json_encode($returnData);
			echo $json;
			break;
			
		case "craftsmanshipInsertDataOne" ://产品制作工艺技术要求及检验记录表信息保存【插入】【模板一】
			//接收数据
			$treeId = isset($_POST["treeId"]) ? $_POST["treeId"] :"";//表【craftsmanshiptree】的id
			$craftsmanshipTableHeader = isset($_POST["craftsmanshipTableHeader"]) ? json_decode($_POST["craftsmanshipTableHeader"],TRUE) : array();
			$craftsmanshipTableBody_1 = isset($_POST["craftsmanshipTableBody_1"]) ? json_decode($_POST["craftsmanshipTableBody_1"],TRUE) : array();
			$craftsmanshipTableBodyResult = isset($_POST["craftsmanshipTableBodyResult"]) ? json_decode($_POST["craftsmanshipTableBodyResult"],TRUE) : array();
			$craftsmanshipTableFooter = isset($_POST["craftsmanshipTableFooter"]) ? json_decode($_POST["craftsmanshipTableFooter"],TRUE) : array();
			//返回数据
			$returnData = array(
				"state" => "success",
				"message" => "",
				"sql" => ""				
			);
			//保存文件并返回相应的保存路径
			$fileSaveSql = "";//保存的路径，在src目录下
			if(count($_FILES) > 0){
				$fileSaveDir = "../uploadfiles";//文件存放目录				
				//第一张
				$fileSaveName = getMillisecond();//无后缀的文件名
				if(isset($_FILES["myfile"])){
					$uploadfileclass = new UploadFile($_FILES["myfile"],$fileSaveDir,$fileSaveName);
					$fileSaveSql_tmp = $uploadfileclass->uploadFile();
					$fileSaveSql = substr($fileSaveSql_tmp, 3);
				}
					
			}
			//保存单一信息
			if(count($craftsmanshipTableHeader) > 0){
				$sql = "INSERT INTO `craftsmanshiptable`(`craftsmanshiptree_id`,`model`,`productname`,`ownpartname`,`partname`,`workpiecenumber`,`productdrawnumber`,`ownpartdrawnumber`,`partdrawnumber`";
				$sql .= ",`quantity`,`finalconclusion`,`inspector`,`inspectionaudit`,`mark`,`numberofplaces`,`changethefilenumber`,`signature`,`date`,`establishment`,`review`,`conclusion`,`inconsistentconfirmation`,`firstfive`,`ctime`) VALUES(";
				$sql .= "'".$treeId."','1'";
				$sql .= ",'".$craftsmanshipTableHeader["productName"]."','".$craftsmanshipTableHeader["ownPartName"]."','".$craftsmanshipTableHeader["partsName"]."','".$craftsmanshipTableHeader["workpieceNumber"]."'";
				$sql .= ",'".$craftsmanshipTableHeader["productDrawingNumber"]."','".$craftsmanshipTableHeader["ownPartDrawingNumber"]."','".$craftsmanshipTableHeader["partsDrawingNumber"]."','".$craftsmanshipTableHeader["quantity"]."'";
				$sql .= ",'".$craftsmanshipTableFooter["finalConclusion"]."','".$craftsmanshipTableFooter["inspector"]."','".$craftsmanshipTableFooter["inspectionAudit"]."','".$craftsmanshipTableFooter["mark"]."','".$craftsmanshipTableFooter["numberOfPlaces"]."'";
				$sql .= ",'".$craftsmanshipTableFooter["changeTheFileNumber"]."','".$craftsmanshipTableFooter["signature"]."','".$craftsmanshipTableFooter["date"]."','".$craftsmanshipTableFooter["establishment"]."','".$craftsmanshipTableFooter["review"]."'";
				$sql .= ",'".$craftsmanshipTableBodyResult["conclusion"]."','".$craftsmanshipTableBodyResult["inconsistentConfirmation"]."','".$fileSaveSql."','".time()."')";
				echo $sql;
				$autoIncrementId = $conn->query($sql) ? $conn->insert_id : "";//获取成功插入后的id
				
				if(!empty($autoIncrementId)){
					//保存可遍历的信息
					foreach($craftsmanshipTableBody_1 as $index => $datainfo){
						$sql = "INSERT INTO `craftsmanshiptableone`(`craftsmanship_id`,`serialnumber`,`processflow`,`inspectioncontent`,`skillsrequirement`,`selftest_13`,`selftest_14`,`selftest_15`,`selftest_16`";
						$sql .= ",`signature_1`,`qualityinspection_13`,`qualityinspection_14`,`qualityinspection_15`,`qualityinspection_16`,`signature_2`) VALUES(";
						$sql .= "'".$autoIncrementId."','".$datainfo["serialNumber"]."','".$datainfo["processFlow"]."','".$datainfo["inspectionContent"]."','".$datainfo["skillsRequirement"]."'";
						$sql .= ",'".$datainfo["selfTest_13"]."','".$datainfo["selfTest_14"]."','".$datainfo["selfTest_15"]."','".$datainfo["selfTest_16"]."'";
						$sql .= ",'".$datainfo["signature_1"]."','".$datainfo["qualityInspection_13"]."','".$datainfo["qualityInspection_14"]."','".$datainfo["qualityInspection_15"]."'";
						$sql .= ",'".$datainfo["qualityInspection_16"]."','".$datainfo["signatture_2"]."'";
						$sql .= ")";
						$conn->query($sql);
					}
				}else{
					$returnData["state"] = "failure";
					$returnData["message"] = "主要数据保存失败";
				}
			}else{
				$returnData["state"] = "failure";
				$returnData["message"] = "主要数据为空";
			}
  			
  			$json = json_encode($returnData);
			echo $json;
			break;
		
		case "craftsmanshipUpdateDataOne"://产品制作工艺技术要求及检验记录表信息保存【更新】【模板一】
			//接收数据
			$craftsmanshipTableHeader = isset($_POST["craftsmanshipTableHeader"]) ? json_decode($_POST["craftsmanshipTableHeader"],TRUE) : array();
			$craftsmanshipTableBody_1 = isset($_POST["craftsmanshipTableBody_1"]) ? json_decode($_POST["craftsmanshipTableBody_1"],TRUE) : array();
			$craftsmanshipTableBodyResult = isset($_POST["craftsmanshipTableBodyResult"]) ? json_decode($_POST["craftsmanshipTableBodyResult"],TRUE) : array();
			$craftsmanshipTableFooter = isset($_POST["craftsmanshipTableFooter"]) ? json_decode($_POST["craftsmanshipTableFooter"],TRUE) : array();
			
			//返回数据
			$returnData = array(
				"state" => "success",
				"message" => "",
				"sql" => ""				
			);
			
			//保存单一信息
			if(count($craftsmanshipTableHeader) > 0){
				
				$sql = "UPDATE `craftsmanshiptable` SET `productname`='".$craftsmanshipTableHeader["productName"]."',`ownpartname`='".$craftsmanshipTableHeader["ownPartName"]."',`partname`='".$craftsmanshipTableHeader["partsName"]."'";
				$sql .= ",`workpiecenumber`='".$craftsmanshipTableHeader["workpieceNumber"]."',`productdrawnumber`='".$craftsmanshipTableHeader["productDrawingNumber"]."',`ownpartdrawnumber`='".$craftsmanshipTableHeader["ownPartDrawingNumber"]."',`partdrawnumber`='".$craftsmanshipTableHeader["partsDrawingNumber"]."'";
				$sql .= ",`quantity`='".$craftsmanshipTableHeader["quantity"]."',`finalconclusion`='".$craftsmanshipTableFooter["finalConclusion"]."',`inspector`='".$craftsmanshipTableFooter["inspector"]."'";
				$sql .= ",`inspectionaudit`='".$craftsmanshipTableFooter["inspectionAudit"]."',`mark`='".$craftsmanshipTableFooter["mark"]."',`numberofplaces`='".$craftsmanshipTableFooter["numberOfPlaces"]."'";
				$sql .= ",`changethefilenumber`='".$craftsmanshipTableFooter["changeTheFileNumber"]."',`signature`='".$craftsmanshipTableFooter["signature"]."',`date`='".$craftsmanshipTableFooter["date"]."',`establishment`='".$craftsmanshipTableFooter["establishment"]."'";
				$sql .= ",`review`='".$craftsmanshipTableFooter["review"]."',`conclusion`='".$craftsmanshipTableBodyResult["conclusion"]."',`inconsistentconfirmation`='".$craftsmanshipTableBodyResult["inconsistentConfirmation"]."'";
				$sql .= " WHERE id='".$craftsmanshipTableHeader["contactId"]."'";
				
				$returnData["sql"] = $sql;
				$autoIncrementId = $conn->query($sql) ? $craftsmanshipTableHeader["contactId"] : "";//主表`craftsmanshiptable`的id
				
				if(!empty($autoIncrementId)){
					//先删除子表所有数据
					$sql = "DELETE FROM `craftsmanshiptableone` WHERE `craftsmanship_id`='".$autoIncrementId."'";
					if($conn->query($sql)){
						//保存可遍历的信息
						foreach($craftsmanshipTableBody_1 as $index => $datainfo){
							$sql = "INSERT INTO `craftsmanshiptableone`(`craftsmanship_id`,`serialnumber`,`processflow`,`inspectioncontent`,`skillsrequirement`,`selftest_13`,`selftest_14`,`selftest_15`,`selftest_16`";
							$sql .= ",`signature_1`,`qualityinspection_13`,`qualityinspection_14`,`qualityinspection_15`,`qualityinspection_16`,`signature_2`) VALUES(";
							$sql .= "'".$autoIncrementId."','".$datainfo["serialNumber"]."','".$datainfo["processFlow"]."','".$datainfo["inspectionContent"]."','".$datainfo["skillsRequirement"]."'";
							$sql .= ",'".$datainfo["selfTest_13"]."','".$datainfo["selfTest_14"]."','".$datainfo["selfTest_15"]."','".$datainfo["selfTest_16"]."'";
							$sql .= ",'".$datainfo["signature_1"]."','".$datainfo["qualityInspection_13"]."','".$datainfo["qualityInspection_14"]."','".$datainfo["qualityInspection_15"]."'";
							$sql .= ",'".$datainfo["qualityInspection_16"]."','".$datainfo["signatture_2"]."'";
							$sql .= ")";
							$conn->query($sql);
							$returnData["sql"] = $sql;
						}
					}
					
				}else{
					$returnData["state"] = "failure";
					$returnData["message"] = "主要数据保存失败";
				}
			}else{
				$returnData["state"] = "failure";
				$returnData["message"] = "主要数据为空";
			}
  			
  			$json = json_encode($returnData);
			echo $json;
			break;
		
		case "craftsmanshipInsertDataTwo": //产品制作工艺技术要求及检验记录表信息保存【插入】【模板二】
			//接收数据
			$treeId = isset($_POST["treeId"]) ? $_POST["treeId"] :"";//表【craftsmanshiptree】的id
			$craftsmanshipTableHeader = isset($_POST["craftsmanshipTableHeader"]) ? json_decode($_POST["craftsmanshipTableHeader"],TRUE) : array();
			$craftsmanshipTableBody_2 = isset($_POST["craftsmanshipTableBody_2"]) ? json_decode($_POST["craftsmanshipTableBody_2"],TRUE) : array();
			$craftsmanshipTableBodyResult = isset($_POST["craftsmanshipTableBodyResult"]) ? json_decode($_POST["craftsmanshipTableBodyResult"],TRUE) : array();
			$craftsmanshipTableFooter = isset($_POST["craftsmanshipTableFooter"]) ? json_decode($_POST["craftsmanshipTableFooter"],TRUE) : array();
			
			//返回数据
			$returnData = array(
				"state" => "success",
				"message" => "",
				"sql" => ""				
			);
			
			//保存文件并返回相应的保存路径
			$fileSaveSql = array("","","");//保存的路径，在src目录下
			if(count($_FILES) > 0){
				$fileSaveDir = "../uploadfiles";//文件存放目录				
				//第一张
				$fileSaveName = getMillisecond();//无后缀的文件名
				if(isset($_FILES["myfileone"])){
					$uploadfileclass = new UploadFile($_FILES["myfileone"],$fileSaveDir,$fileSaveName);
					$fileSaveSql_tmp = $uploadfileclass->uploadFile();
					$fileSaveSql[0] = substr($fileSaveSql_tmp, 3);
				}
				//第二张
				$fileSaveName = getMillisecond();//无后缀的文件名
				if(isset($_FILES["myfiletwo"])){
					$uploadfileclass = new UploadFile($_FILES["myfiletwo"],$fileSaveDir,$fileSaveName);
					$fileSaveSql_tmp = $uploadfileclass->uploadFile();
					$fileSaveSql[1] = substr($fileSaveSql_tmp, 3);
				}
				//第三张
				$fileSaveName = getMillisecond();//无后缀的文件名
				if(isset($_FILES["myfilethree"])){
					$uploadfileclass = new UploadFile($_FILES["myfilethree"],$fileSaveDir,$fileSaveName);
					$fileSaveSql_tmp = $uploadfileclass->uploadFile();
					$fileSaveSql[2] = substr($fileSaveSql_tmp, 3);
				}	
			}
			
			//保存单一信息
			if(count($craftsmanshipTableHeader) > 0){
				$sql = "INSERT INTO `craftsmanshiptable`(`craftsmanshiptree_id`,`model`,`productname`,`ownpartname`,`partname`,`workpiecenumber`,`productdrawnumber`,`ownpartdrawnumber`,`partdrawnumber`";
				$sql .= ",`quantity`,`finalconclusion`,`inspector`,`inspectionaudit`,`mark`,`numberofplaces`,`changethefilenumber`,`signature`,`date`,`establishment`,`review`,`conclusion`,`inconsistentconfirmation`,`secondmodelimageone`,`secondmodelimagetwo`,`secondmodelimagethree`,`ctime`) VALUES(";
				$sql .= "'".$treeId."','2'";
				$sql .= ",'".$craftsmanshipTableHeader["productName"]."','".$craftsmanshipTableHeader["ownPartName"]."','".$craftsmanshipTableHeader["partsName"]."','".$craftsmanshipTableHeader["workpieceNumber"]."'";
				$sql .= ",'".$craftsmanshipTableHeader["productDrawingNumber"]."','".$craftsmanshipTableHeader["ownPartDrawingNumber"]."','".$craftsmanshipTableHeader["partsDrawingNumber"]."','".$craftsmanshipTableHeader["quantity"]."'";
				$sql .= ",'".$craftsmanshipTableFooter["finalConclusion"]."','".$craftsmanshipTableFooter["inspector"]."','".$craftsmanshipTableFooter["inspectionAudit"]."','".$craftsmanshipTableFooter["mark"]."','".$craftsmanshipTableFooter["numberOfPlaces"]."'";
				$sql .= ",'".$craftsmanshipTableFooter["changeTheFileNumber"]."','".$craftsmanshipTableFooter["signature"]."','".$craftsmanshipTableFooter["date"]."','".$craftsmanshipTableFooter["establishment"]."','".$craftsmanshipTableFooter["review"]."'";
				$sql .= ",'".$craftsmanshipTableBodyResult["conclusion"]."','".$craftsmanshipTableBodyResult["inconsistentConfirmation"]."','".$fileSaveSql[0]."','".$fileSaveSql[1]."','".$fileSaveSql[2]."','".time()."')";
				
				$returnData["sql"] = $sql;
				$autoIncrementId = $conn->query($sql) ? $conn->insert_id : "";//获取成功插入后的id
				
				if(!empty($autoIncrementId)){
					//保存可遍历的信息
					foreach($craftsmanshipTableBody_2 as $index => $datainfo){
						$sql = "INSERT INTO `craftsmanshiptabletwo`(`craftsmanship_id`,`serialnumber`,`processflow`,`inspectioncontent`,`skillsrequirement`,`selftest`,`signature`,`ctime`) VALUES(";
						$sql .= "'".$autoIncrementId."','".$datainfo["serialNumber"]."','".$datainfo["processFlow"]."','".$datainfo["inspectionContent"]."','".$datainfo["skillsRequirement"]."'";
						$sql .= ",'".$datainfo["selfTest"]."','".$datainfo["signature"]."','".time()."'";
						$sql .= ")";
						$conn->query($sql);
					}
				}else{
					$returnData["state"] = "failure";
					$returnData["message"] = "主要数据保存失败";
				}
			}else{
				$returnData["state"] = "failure";
				$returnData["message"] = "主要数据为空";
			}
  			
  			$json = json_encode($returnData);
			echo $json;
			break;
		
		case "craftsmanshipUpdateDataOne"://产品制作工艺技术要求及检验记录表信息保存【更新】【模板二】
			//接收数据
			$craftsmanshipTableHeader = isset($_POST["craftsmanshipTableHeader"]) ? json_decode($_POST["craftsmanshipTableHeader"],TRUE) : array();
			$craftsmanshipTableBody_2 = isset($_POST["craftsmanshipTableBody_2"]) ? json_decode($_POST["craftsmanshipTableBody_2"],TRUE) : array();
			$craftsmanshipTableBodyResult = isset($_POST["craftsmanshipTableBodyResult"]) ? json_decode($_POST["craftsmanshipTableBodyResult"],TRUE) : array();
			$craftsmanshipTableFooter = isset($_POST["craftsmanshipTableFooter"]) ? json_decode($_POST["craftsmanshipTableFooter"],TRUE) : array();
			
			//返回数据
			$returnData = array(
				"state" => "success",
				"message" => "",
				"sql" => ""				
			);
			
			//保存文件并返回相应的保存路径
			
			if(count($_FILES) > 0){
				$fileSaveDir = "../uploadfiles";//文件存放目录				
				//第一张
				$fileSaveName = getMillisecond();//无后缀的文件名
				if(isset($_FILES["myfileone"])){
					//删除原有文件
					$sql = "SELECT `secondmodelimageone` FROM `craftsmanshiptable` WHERE id='".$craftsmanshipTableHeader["contactId"]."'";
					$result = $conn->query($sql);
					if($result->num_rows > 0){
						while($row = $result->fetch_row()){
							$filePath = $row[0];
							$filePath = "../".$filePath;
							if(file_exists($filePath)){
								@unlink($filePath);
							}
						}
					}
					$uploadfileclass = new UploadFile($_FILES["myfileone"],$fileSaveDir,$fileSaveName);
					$fileSaveSql_tmp = $uploadfileclass->uploadFile();
					$fileSaveSql = substr($fileSaveSql_tmp, 3);
					$sql = "UPDATE `craftsmanshiptable` SET `secondmodelimageone`='".$fileSaveSql."' WHERE `id`='".$craftsmanshipTableHeader["contactId"]."'";
					$conn->query($sql);
				}
				//第二张
				$fileSaveName = getMillisecond();//无后缀的文件名
				if(isset($_FILES["myfiletwo"])){
					//删除原有文件
					$sql = "SELECT `secondmodelimagetwo` FROM `craftsmanshiptable` WHERE id='".$craftsmanshipTableHeader["contactId"]."'";
					$result = $conn->query($sql);
					if($result->num_rows > 0){
						while($row = $result->fetch_row()){
							$filePath = $row[0];
							$filePath = "../".$filePath;
							if(file_exists($filePath)){
								@unlink($filePath);
							}
						}
					}
					$uploadfileclass = new UploadFile($_FILES["myfiletwo"],$fileSaveDir,$fileSaveName);
					$fileSaveSql_tmp = $uploadfileclass->uploadFile();
					$fileSaveSql = substr($fileSaveSql_tmp, 3);
					$sql = "UPDATE `craftsmanshiptable` SET `secondmodelimagetwo`='".$fileSaveSql."' WHERE `id`='".$craftsmanshipTableHeader["contactId"]."'";
					$conn->query($sql);
				}
				//第三张
				$fileSaveName = getMillisecond();//无后缀的文件名
				if(isset($_FILES["myfilethree"])){
					//删除原有文件
					$sql = "SELECT `secondmodelimagethree` FROM `craftsmanshiptable` WHERE id='".$craftsmanshipTableHeader["contactId"]."'";
					$result = $conn->query($sql);
					if($result->num_rows > 0){
						while($row = $result->fetch_row()){
							$filePath = $row[0];
							$filePath = "../".$filePath;
							if(file_exists($filePath)){
								@unlink($filePath);
							}
						}
					}
					$uploadfileclass = new UploadFile($_FILES["myfilethree"],$fileSaveDir,$fileSaveName);
					$fileSaveSql_tmp = $uploadfileclass->uploadFile();
					$fileSaveSql = substr($fileSaveSql_tmp, 3);
					$sql = "UPDATE `craftsmanshiptable` SET `secondmodelimagethree`='".$fileSaveSql."' WHERE `id`='".$craftsmanshipTableHeader["contactId"]."'";
					$conn->query($sql);
				}	
			}
			
			//保存单一信息
			if(count($craftsmanshipTableHeader) > 0){
				
				$sql = "UPDATE `craftsmanshiptable` SET `productname`='".$craftsmanshipTableHeader["productName"]."',`ownpartname`='".$craftsmanshipTableHeader["ownPartName"]."',`partname`='".$craftsmanshipTableHeader["partsName"]."'";
				$sql .= ",`workpiecenumber`='".$craftsmanshipTableHeader["workpieceNumber"]."',`productdrawnumber`='".$craftsmanshipTableHeader["productDrawingNumber"]."',`ownpartdrawnumber`='".$craftsmanshipTableHeader["ownPartDrawingNumber"]."',`partdrawnumber`='".$craftsmanshipTableHeader["partsDrawingNumber"]."'";
				$sql .= ",`quantity`='".$craftsmanshipTableHeader["quantity"]."',`finalconclusion`='".$craftsmanshipTableFooter["finalConclusion"]."',`inspector`='".$craftsmanshipTableFooter["inspector"]."'";
				$sql .= ",`inspectionaudit`='".$craftsmanshipTableFooter["inspectionAudit"]."',`mark`='".$craftsmanshipTableFooter["mark"]."',`numberofplaces`='".$craftsmanshipTableFooter["numberOfPlaces"]."'";
				$sql .= ",`changethefilenumber`='".$craftsmanshipTableFooter["changeTheFileNumber"]."',`signature`='".$craftsmanshipTableFooter["signature"]."',`date`='".$craftsmanshipTableFooter["date"]."',`establishment`='".$craftsmanshipTableFooter["establishment"]."'";
				$sql .= ",`review`='".$craftsmanshipTableFooter["review"]."',`conclusion`='".$craftsmanshipTableBodyResult["conclusion"]."',`inconsistentconfirmation`='".$craftsmanshipTableBodyResult["inconsistentConfirmation"]."'";
				$sql .= " WHERE id='".$craftsmanshipTableHeader["contactId"]."'";
				
				$returnData["sql"] = $sql;
				$autoIncrementId = $conn->query($sql) ? $craftsmanshipTableHeader["contactId"] : "";//主表`craftsmanshiptable`的id
				
				if(!empty($autoIncrementId)){
					//先删除子表所有数据
					$sql = "DELETE FROM `craftsmanshiptabletwo` WHERE `craftsmanship_id`='".$autoIncrementId."'";
					if($conn->query($sql)){
						//保存可遍历的信息
						//保存可遍历的信息
						foreach($craftsmanshipTableBody_2 as $index => $datainfo){
							$sql = "INSERT INTO `craftsmanshiptabletwo`(`craftsmanship_id`,`serialnumber`,`processflow`,`inspectioncontent`,`skillsrequirement`,`selftest`,`signature`,`ctime`) VALUES(";
							$sql .= "'".$autoIncrementId."','".$datainfo["serialNumber"]."','".$datainfo["processFlow"]."','".$datainfo["inspectionContent"]."','".$datainfo["skillsRequirement"]."'";
							$sql .= ",'".$datainfo["selfTest"]."','".$datainfo["signature"]."','".time()."'";
							$sql .= ")";
							$conn->query($sql);
						}
					}
					
				}else{
					$returnData["state"] = "failure";
					$returnData["message"] = "主要数据保存失败";
				}
			}else{
				$returnData["state"] = "failure";
				$returnData["message"] = "主要数据为空";
			}
  			
  			$json = json_encode($returnData);
			echo $json;
			break;
		
		case "craftsmanshipInsertDataThree"://产品制作工艺技术要求及检验记录表信息保存【插入】【模板三】
			//接收数据
			$treeId = isset($_POST["treeId"]) ? $_POST["treeId"] :"";//表【craftsmanshiptree】的id
			$craftsmanshipTableHeader = isset($_POST["craftsmanshipTableHeader"]) ? json_decode($_POST["craftsmanshipTableHeader"],TRUE) : array();
			$craftsmanshipTableFooter = isset($_POST["craftsmanshipTableFooter"]) ? json_decode($_POST["craftsmanshipTableFooter"],TRUE) : array();
			
			//返回数据
			$returnData = array(
				"state" => "success",
				"message" => "",
				"sql" => ""				
			);
			
			//保存文件并返回相应的保存路径
			$fileSaveSql = "";//保存的路径，在src目录下
			if(count($_FILES) > 0){
				$fileSaveDir = "../uploadfiles";//文件存放目录				
				//第一张
				$fileSaveName = getMillisecond();//无后缀的文件名
				if(isset($_FILES["myfile"])){
					$uploadfileclass = new UploadFile($_FILES["myfile"],$fileSaveDir,$fileSaveName);
					$fileSaveSql_tmp = $uploadfileclass->uploadFile();
					$fileSaveSql = substr($fileSaveSql_tmp, 3);
				}
					
			}
			
			//保存单一信息
			if(count($craftsmanshipTableHeader) > 0){
				$sql = "INSERT INTO `craftsmanshiptable`(`craftsmanshiptree_id`,`model`,`productname`,`ownpartname`,`partname`,`workpiecenumber`,`productdrawnumber`,`ownpartdrawnumber`,`partdrawnumber`";
				$sql .= ",`quantity`,`finalconclusion`,`inspector`,`inspectionaudit`,`mark`,`numberofplaces`,`changethefilenumber`,`signature`,`date`,`establishment`,`review`,`thirdmodelimage`,`ctime`) VALUES(";
				$sql .= "'".$treeId."','3'";
				$sql .= ",'".$craftsmanshipTableHeader["productName"]."','".$craftsmanshipTableHeader["ownPartName"]."','".$craftsmanshipTableHeader["partsName"]."','".$craftsmanshipTableHeader["workpieceNumber"]."'";
				$sql .= ",'".$craftsmanshipTableHeader["productDrawingNumber"]."','".$craftsmanshipTableHeader["ownPartDrawingNumber"]."','".$craftsmanshipTableHeader["partsDrawingNumber"]."','".$craftsmanshipTableHeader["quantity"]."'";
				$sql .= ",'".$craftsmanshipTableFooter["finalConclusion"]."','".$craftsmanshipTableFooter["inspector"]."','".$craftsmanshipTableFooter["inspectionAudit"]."','".$craftsmanshipTableFooter["mark"]."','".$craftsmanshipTableFooter["numberOfPlaces"]."'";
				$sql .= ",'".$craftsmanshipTableFooter["changeTheFileNumber"]."','".$craftsmanshipTableFooter["signature"]."','".$craftsmanshipTableFooter["date"]."','".$craftsmanshipTableFooter["establishment"]."','".$craftsmanshipTableFooter["review"]."'";
				$sql .= ",'".$fileSaveSql."','".time()."')";
				
				$returnData["sql"] = $sql;
				if(!$conn->query($sql)){
					$returnData["state"] = "failure";
					$returnData["message"] = "主要数据保存失败";
				}
			}else{
				$returnData["state"] = "failure";
				$returnData["message"] = "主要数据为空";
			}
  			
  			$json = json_encode($returnData);
			echo $json;
			break;
		
		case "craftsmanshipUpdateDataThree"://产品制作工艺技术要求及检验记录表信息保存【更新】【模板三】
			//接收数据
			$craftsmanshipTableHeader = isset($_POST["craftsmanshipTableHeader"]) ? json_decode($_POST["craftsmanshipTableHeader"],TRUE) : array();
			$craftsmanshipTableFooter = isset($_POST["craftsmanshipTableFooter"]) ? json_decode($_POST["craftsmanshipTableFooter"],TRUE) : array();
			
			//返回数据
			$returnData = array(
				"state" => "success",
				"message" => "",
				"sql" => ""				
			);
			
			//保存文件并返回相应的保存路径
			$fileSaveSql = "";//保存的路径，在src目录下
			if(count($_FILES) > 0){
				//第一张
				$fileSaveName = getMillisecond();//无后缀的文件名
				if(isset($_FILES["myfile"])){
					//先删除
					$sql = "SELECT `thirdmodelimage` FROM `craftsmanshiptable` WHERE id='".$craftsmanshipTableHeader["contactId"]."'";
					$result = $conn->query($sql);
					if($result->num_rows > 0){
						while($row = $result->fetch_row()){
							$filePath = $row[0];
							$filePath = "../".$filePath;
							if(file_exists($filePath)){
								@unlink($filePath);
							}
						}
					}
					//再保存
					$fileSaveDir = "../uploadfiles";//文件存放目录
					$uploadfileclass = new UploadFile($_FILES["myfile"],$fileSaveDir,$fileSaveName);
					$fileSaveSql_tmp = $uploadfileclass->uploadFile();
					$fileSaveSql = substr($fileSaveSql_tmp, 3);
					$sql = "UPDATE `craftsmanshiptable` SET `thirdmodelimage`='".$fileSaveSql."' WHERE `id`='".$craftsmanshipTableHeader["contactId"]."'";
					$conn->query($sql);
				}
					
			}
			
			//保存单一信息
			if(count($craftsmanshipTableHeader) > 0){
				
				$sql = "UPDATE `craftsmanshiptable` SET `productname`='".$craftsmanshipTableHeader["productName"]."',`ownpartname`='".$craftsmanshipTableHeader["ownPartName"]."',`partname`='".$craftsmanshipTableHeader["partsName"]."'";
				$sql .= ",`workpiecenumber`='".$craftsmanshipTableHeader["workpieceNumber"]."',`productdrawnumber`='".$craftsmanshipTableHeader["productDrawingNumber"]."',`ownpartdrawnumber`='".$craftsmanshipTableHeader["ownPartDrawingNumber"]."',`partdrawnumber`='".$craftsmanshipTableHeader["partsDrawingNumber"]."'";
				$sql .= ",`quantity`='".$craftsmanshipTableHeader["quantity"]."',`finalconclusion`='".$craftsmanshipTableFooter["finalConclusion"]."',`inspector`='".$craftsmanshipTableFooter["inspector"]."'";
				$sql .= ",`inspectionaudit`='".$craftsmanshipTableFooter["inspectionAudit"]."',`mark`='".$craftsmanshipTableFooter["mark"]."',`numberofplaces`='".$craftsmanshipTableFooter["numberOfPlaces"]."'";
				$sql .= ",`changethefilenumber`='".$craftsmanshipTableFooter["changeTheFileNumber"]."',`signature`='".$craftsmanshipTableFooter["signature"]."',`date`='".$craftsmanshipTableFooter["date"]."',`establishment`='".$craftsmanshipTableFooter["establishment"]."'";
				$sql .= ",`review`='".$craftsmanshipTableFooter["review"]."'";
				$sql .= " WHERE id='".$craftsmanshipTableHeader["contactId"]."'";
				
				$returnData["sql"] = $sql;
				$autoIncrementId = $conn->query($sql) ? $craftsmanshipTableHeader["contactId"] : "";//主表`craftsmanshiptable`的id
				
			}else{
				$returnData["state"] = "failure";
				$returnData["message"] = "主要数据为空";
			}
  			
  			$json = json_encode($returnData);
			echo $json;
			break;
		
		case "getCraftsmanshipInfoData"://获取制造工艺的详情信息【所有模板】
			//接收数据
			$contactId = isset($_GET["contactID"]) ? $_GET["contactID"] : "";
			
			//返回数据
			$returnData = array(
				"state" => "success",
				"message" => "",
				"data" => array(
					"model" => "",
					"craftsmanshipTableHeader" => array(),
					"craftsmanshipTableBody_1" => array(),//模板一
					"craftsmanshipTableBody_2" => array(),//模板二
					"craftsmanshipTableBody_3" => array(),//模板三
					"craftsmanshipTableBodyResult" => array(),//模板一、二
					"craftsmanshipTableFooter" => array()
				)
			);
			
			//主表信息
			$sql = "SELECT * FROM `craftsmanshiptable` WHERE `id`='".$contactId."'";
			$result = $conn->query($sql);
			if($result->num_rows > 0){
				while($row = $result->fetch_assoc()){
					$returnData["data"]["model"] = $row["model"];//返回模型值
					//头部信息
					$returnData["data"]["craftsmanshipTableHeader"]["contactId"] = $row["id"];
					$returnData["data"]["craftsmanshipTableHeader"]["productName"] = $row["productname"];
					$returnData["data"]["craftsmanshipTableHeader"]["ownPartName"] = $row["ownpartname"];
					$returnData["data"]["craftsmanshipTableHeader"]["partsName"] = $row["partname"];
					$returnData["data"]["craftsmanshipTableHeader"]["workpieceNumber"] = $row["workpiecenumber"];
					$returnData["data"]["craftsmanshipTableHeader"]["productDrawingNumber"] = $row["productdrawnumber"];
					$returnData["data"]["craftsmanshipTableHeader"]["ownPartDrawingNumber"] = $row["ownpartdrawnumber"];
					$returnData["data"]["craftsmanshipTableHeader"]["partsDrawingNumber"] = $row["partdrawnumber"];
					$returnData["data"]["craftsmanshipTableHeader"]["quantity"] = $row["quantity"];
					
					//尾部信息
					$returnData["data"]["craftsmanshipTableFooter"]["finalConclusion"] = $row["finalconclusion"];
					$returnData["data"]["craftsmanshipTableFooter"]["inspector"] = $row["inspector"];
					$returnData["data"]["craftsmanshipTableFooter"]["inspectionAudit"] = $row["inspectionaudit"];
					$returnData["data"]["craftsmanshipTableFooter"]["mark"] = $row["mark"];
					$returnData["data"]["craftsmanshipTableFooter"]["numberOfPlaces"] = $row["numberofplaces"];
					$returnData["data"]["craftsmanshipTableFooter"]["changeTheFileNumber"] = $row["changethefilenumber"];
					$returnData["data"]["craftsmanshipTableFooter"]["signature"] = $row["signature"];
					$returnData["data"]["craftsmanshipTableFooter"]["date"] = $row["date"];
					$returnData["data"]["craftsmanshipTableFooter"]["establishment"] = $row["establishment"];
					$returnData["data"]["craftsmanshipTableFooter"]["review"] = $row["review"];
					switch($row["model"]){//根据模型编号返回相应的信息
						case "1":														
							//结论与不符合确定【模板一、二】
							$returnData["data"]["craftsmanshipTableBodyResult"]["conclusion"] = $row["conclusion"];
							$returnData["data"]["craftsmanshipTableBodyResult"]["inconsistentConfirmation"] = $row["inconsistentconfirmation"];
							
							$returnData["data"]["craftsmanshipTableBody_1"]["fileOne"] = $row["firstfive"];
							$returnData["data"]["craftsmanshipTableBody_1"]["imgHtml"] = "";
							//模板一可遍历的数据
							$sql = "SELECT * FROM `craftsmanshiptableone` WHERE `craftsmanship_id`='".$row["id"]."' ORDER BY `id`";
							$result_2 = $conn->query($sql);
							if($result_2->num_rows > 0){
								$i = 0;	
								while($row_2 = $result_2->fetch_assoc()){
									$returnData["data"]["craftsmanshipTableBody_1"]["rowsData"][$i]["serialNumber"] = $row_2["serialnumber"];
									$returnData["data"]["craftsmanshipTableBody_1"]["rowsData"][$i]["processFlow"] = $row_2["processflow"];
									$returnData["data"]["craftsmanshipTableBody_1"]["rowsData"][$i]["inspectionContent"] = $row_2["inspectioncontent"];
									$returnData["data"]["craftsmanshipTableBody_1"]["rowsData"][$i]["skillsRequirement"] = $row_2["skillsrequirement"];
									$returnData["data"]["craftsmanshipTableBody_1"]["rowsData"][$i]["selfTest_13"] = $row_2["selftest_13"];
									$returnData["data"]["craftsmanshipTableBody_1"]["rowsData"][$i]["selfTest_14"] = $row_2["selftest_14"];
									$returnData["data"]["craftsmanshipTableBody_1"]["rowsData"][$i]["selfTest_15"] = $row_2["selftest_15"];
									$returnData["data"]["craftsmanshipTableBody_1"]["rowsData"][$i]["selfTest_16"] = $row_2["selftest_16"];
									$returnData["data"]["craftsmanshipTableBody_1"]["rowsData"][$i]["signature_1"] = $row_2["signature_1"];
									$returnData["data"]["craftsmanshipTableBody_1"]["rowsData"][$i]["qualityInspection_13"] = $row_2["qualityinspection_13"];
									$returnData["data"]["craftsmanshipTableBody_1"]["rowsData"][$i]["qualityInspection_14"] = $row_2["qualityinspection_14"];
									$returnData["data"]["craftsmanshipTableBody_1"]["rowsData"][$i]["qualityInspection_15"] = $row_2["qualityinspection_15"];
									$returnData["data"]["craftsmanshipTableBody_1"]["rowsData"][$i]["qualityInspection_16"] = $row_2["qualityinspection_16"];
									$returnData["data"]["craftsmanshipTableBody_1"]["rowsData"][$i]["signatture_2"] = $row_2["signature_2"];
									
									$i++;
								}
							}							
							break;
						case "2":
							//结论与不符合确定【模板一、二】
							$returnData["data"]["craftsmanshipTableBodyResult"]["conclusion"] = $row["conclusion"];
							$returnData["data"]["craftsmanshipTableBodyResult"]["inconsistentConfirmation"] = $row["inconsistentconfirmation"];
							//图片信息
							$returnData["data"]["craftsmanshipTableBody_2"]["fileOne"] = $row["secondmodelimageone"];
							$returnData["data"]["craftsmanshipTableBody_2"]["fileTwo"] = $row["secondmodelimagetwo"];
							$returnData["data"]["craftsmanshipTableBody_2"]["fileThree"] = $row["secondmodelimagethree"];
							//html容器
							$returnData["data"]["craftsmanshipTableBody_2"]["imgHtmlOne"] = "";
							$returnData["data"]["craftsmanshipTableBody_2"]["imgHtmlTwo"] = "";
							$returnData["data"]["craftsmanshipTableBody_2"]["imgHtmlTherr"] = "";
							
							//模板二可遍历的数据
							$sql = "SELECT * FROM `craftsmanshiptabletwo` WHERE `craftsmanship_id`='".$row["id"]."' ORDER BY `id`";
							$result_2 = $conn->query($sql);
							if($result_2->num_rows > 0){
								$i = 0;	
								while($row_2 = $result_2->fetch_assoc()){
									$returnData["data"]["craftsmanshipTableBody_2"]["rowsData"][$i]["serialNumber"] = $row_2["serialnumber"];
									$returnData["data"]["craftsmanshipTableBody_2"]["rowsData"][$i]["processFlow"] = $row_2["processflow"];
									$returnData["data"]["craftsmanshipTableBody_2"]["rowsData"][$i]["inspectionContent"] = $row_2["inspectioncontent"];
									$returnData["data"]["craftsmanshipTableBody_2"]["rowsData"][$i]["skillsRequirement"] = $row_2["skillsrequirement"];
									$returnData["data"]["craftsmanshipTableBody_2"]["rowsData"][$i]["selfTest"] = $row_2["selftest"];
									$returnData["data"]["craftsmanshipTableBody_2"]["rowsData"][$i]["signature"] = $row_2["signature"];
									
									$i++;
								}
							}
							break;
						case "3":
							$returnData["data"]["craftsmanshipTableBody_3"]["fileOne"] = $row["thirdmodelimage"];
							$returnData["data"]["craftsmanshipTableBody_3"]["imgHtml"] = "";
							break;
						default:
							$returnData["state"] = "failure";
							$returnData["message"] = "模型参数无效";					
					}
				}
			}

			$json = json_encode($returnData);
			echo $json;
			break;
		case "deleteCraftsmanship": //----------------------------------根据【craftsmanshiptable】ID删除制造工艺信息--------------------------------------------------
			//接收数据
			$contactId = isset($_GET["contactId"]) ? $_GET["contactId"] : "";
			
			//返回数据
			$returnData = array(
				"state" => "success",
				"message" => ""				
			);
			
			//删除【craftsmanshiptable】的模板二的图，模板三的图
			$sql = "SELECT `secondmodelimageone`,`secondmodelimagetwo`,`secondmodelimagethree`,`thirdmodelimage` FROM `craftsmanshiptable` WHERE `id`='".$contactId."'";
			$result = $conn->query($sql);
			if($result->num_rows > 0){
				$savePathArr = array();
				while($row = $result->fetch_assoc()){
					$savePathArr[0] = $row["secondmodelimageone"];
					$savePathArr[1] = $row["secondmodelimagetwo"];
					$savePathArr[2] = $row["secondmodelimagethree"];
					$savePathArr[3] = $row["thirdmodelimage"];
				}				
				foreach($savePathArr as $index => $savePath){
					if(!empty($savePath)){
						$realsavePath = "../".$savePath;
						if(file_exists($realsavePath)){
							@unlink($realsavePath);
						}
					}
				}
			}
			//删除【craftsmanshiptable】表记录
			$sql = "DELETE FROM `craftsmanshiptable` WHERE `id`='".$contactId."' ";
			if(!$conn->query($sql)){
				$returnData["state"] = "fail";
				$returnData["message"] .= "表craftsmanshiptable记录删除失败";
			}
			//删除【craftsmanshiptableone】表记录
			$sql = "DELETE FROM `craftsmanshiptableone` WHERE `craftsmanship_id`='".$contactId."' ";
			if(!$conn->query($sql)){
				$returnData["state"] = "fail";
				$returnData["message"] .= "表craftsmanshiptableone记录删除失败";
			}
			//删除【craftsmanshiptabletwo】表记录
			$sql = "DELETE FROM `craftsmanshiptabletwo` WHERE `craftsmanship_id`='".$contactId."' ";
			if(!$conn->query($sql)){
				$returnData["state"] = "fail";
				$returnData["message"] .= "表craftsmanshiptabletwo记录删除失败";
			}
			
			$json = json_encode($returnData);
			echo $json;
			break;
		
		case "getWeldingPrintData"://----------------------------焊接工艺及检验记录打印--------------------------------------
			//接收数据
			$relateId = isset($_GET["relateId"]) ? $_GET["relateId"] : "";//表【weldingtree】的id
			
			//返回数据
			$ret_data = array(
				"state" => "success",
				"message" => "",
				"data" => array(
					"detailList" => array(),
					"weldingInfo" => array()
				)
			);
			
			$weldingtableIdArr = array();//用于查询焊接的各部位的详细信息
			
			//初始化明细表
			for($i = 0;$i <= 18;$i++ ){
				$ret_data["data"]["detailList"][$i]["serial"] = $i+1;//序号				
			}			
			//装载明细表数据
			$sql = "SELECT `id`,`processnumber`,`partname`,`partdrawingnumber` FROM weldingtable WHERE `weldingtree_id`='".$relateId."'";
			$result = $conn->query($sql);
			if($result->num_rows > 0){
				$i = 0;
				while($row = $result->fetch_assoc()){
					$weldingtableIdArr[] = $row["id"];
					
					$ret_data["data"]["detailList"][$i]["processnumber"] = $row["processnumber"];
					$ret_data["data"]["detailList"][$i]["partname"] = $row["partname"];
					$ret_data["data"]["detailList"][$i]["partdrawingnumber"] = $row["partdrawingnumber"];
					$ret_data["data"]["detailList"][$i]["pageNum"] = 3;
					$ret_data["data"]["detailList"][$i]["level"] = "A";
					$i++;
				}
			}
			
			//查询焊接的各部位的详细信息
			foreach($weldingtableIdArr as $indexNum => $contactId){
				if(!empty($contactId)){
					//查询首表信息
					$sql = "SELECT `processnumber`,`quantity`,`workpiecenumber`,`workshop`,`workordernumber`,`producname`,`productcode`,`partname`,`partdrawingnumber`,`finallyresult`,`inspectorsingnature`,`finallydate`,`weldingsequence`,`weldingnumbermap` FROM `weldingtable` WHERE `id`='".$contactId."'";
					$result = $conn->query($sql);
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableOne"]["contactId"] = $contactId;
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableOne"]["processNumber"] = $row["processnumber"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableOne"]["quantity"] = $row["quantity"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableOne"]["workpieceNumber"] = $row["workpiecenumber"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableOne"]["workshop"] = $row["workshop"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableOne"]["workOrderNumber"] = $row["workordernumber"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableOne"]["productName"] = $row["producname"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableOne"]["productCode"] = $row["productcode"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableOne"]["partName"] = $row["partname"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableOne"]["partDrawingNumber"] = $row["partdrawingnumber"];
							
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableThree_3"]["finalInspectionResult"] = $row["finallyresult"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableThree_3"]["inspectorSingnature"] = $row["inspectorsingnature"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableThree_3"]["date"] = $row["finallydate"];
							
							$ret_data["data"]["weldingInfo"][$indexNum]["weldngTableFour"]["weldingSequence"] = $row["weldingsequence"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldngTableFour"]["weldNumberMap"] = $row["weldingnumbermap"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldngTableFour"]["imgHtm"] = "";
							
						}
					}
					//查询第一个表
					$sql = "SELECT `weldingnumber`,`materialfirst`,`specificationsfirst`,`materialsecond`,`specificationssecond`,`weldingmethod`,`grooveform`,`consumables`,`specifications`,`weldinglayer`,`weldingtrack`,`gas`,`current`,`actualcurrentfirst`,`actualcurrentsecond`,`voltage`,`actualvoltagefirst`,`actualvoltagesecond`,`specificationnumber`,`ratingnumber`,`flawdetection`,`steelstamp` FROM `weldingtableone` WHERE `weldingtable_id`='".$contactId."' ORDER BY `id`";
					$result = $conn->query($sql);
					if($result->num_rows > 0){
						$i = 0;
						while($row = $result->fetch_assoc()){
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableTwo_1"][$i]["weldNumber"] = $row["weldingnumber"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableTwo_1"][$i]["materialAndSpecifications_1"] = $row["materialfirst"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableTwo_1"][$i]["materialAndSpecifications_1_thickness"] = $row["specificationsfirst"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableTwo_1"][$i]["materialAndSpecifications_2"] = $row["materialsecond"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableTwo_1"][$i]["materialAndSpecifications_2_thickness"] = $row["specificationssecond"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableTwo_1"][$i]["weldingMethod"] = $row["weldingmethod"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableTwo_1"][$i]["grooveForm"] = $row["grooveform"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableTwo_1"][$i]["consumables"] = $row["consumables"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableTwo_1"][$i]["specifications"] = $row["specifications"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableTwo_1"][$i]["weldingLevel_numberOfLayers"] = $row["weldinglayer"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableTwo_1"][$i]["weldingLevel_numberOftracks"] = $row["weldingtrack"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableTwo_1"][$i]["protectiveGas"] = $row["gas"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableTwo_1"][$i]["weldingCurrent"] = $row["current"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableTwo_1"][$i]["actualCurrent_1"] = $row["actualcurrentfirst"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableTwo_1"][$i]["actualCurrent_2"] = $row["actualcurrentsecond"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableTwo_1"][$i]["weldingVoltage"] = $row["voltage"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableTwo_1"][$i]["actualVoltage_1"] = $row["actualvoltagefirst"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableTwo_1"][$i]["actualVoltage_2"] = $row["actualvoltagesecond"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableTwo_1"][$i]["specificationNumber"] = $row["specificationnumber"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableTwo_1"][$i]["ratingNumber"] = $row["ratingnumber"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableTwo_1"][$i]["flawDetectionRequirements"] = $row["flawdetection"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableTwo_1"][$i]["steelStamp"] = $row["steelstamp"];
							
							$i++;
						}
					}
					//查询第二个表
					$sql = "SELECT `serialnumber`,`checkcontent`,`processrequirement`,`testresult`,`singnature` FROM `weldingtabletwo` WHERE `weldingtable_id`='".$contactId."' ORDER BY `id`";
					$result = $conn->query($sql);
					if($result->num_rows > 0){
						$i = 0;
						while($row = $result->fetch_assoc()){
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableTwo_2"][$i]["serialNumber"] = $row["serialnumber"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableTwo_2"][$i]["checkContent"] = $row["checkcontent"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableTwo_2"][$i]["processRequirements"] = $row["processrequirement"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableTwo_2"][$i]["testResult"] = $row["testresult"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableTwo_2"][$i]["inspectorSingnature"] = $row["singnature"];
							
							$i++;
						}
					}
					//查询第三个表
					$sql = "SELECT `weldingnumber`,`requirementone`,`testresultone`,`singnatureone`,`requirementtwo`,`testresultonetwo`,`singnatureonetwo`,`requirementthree`,`testresultonethree`,`singnatureonethree` FROM `weldingtablethree` WHERE `weldingtable_id`='".$contactId."' ORDER BY `id`";
					$result = $conn->query($sql);
					if($result->num_rows > 0){
						$i = 0;
						while($row = $result->fetch_assoc()){
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableThree_1"][$i]["weldNumber"] = $row["weldingnumber"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableThree_1"][$i]["processRequirements_1"] = $row["requirementone"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableThree_1"][$i]["testResult_1"] = $row["testresultone"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableThree_1"][$i]["inspectorSingnature_1"] = $row["singnatureone"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableThree_1"][$i]["processRequirements_2"] = $row["requirementtwo"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableThree_1"][$i]["testResult_2"] = $row["testresultonetwo"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableThree_1"][$i]["inspectorSingnature_2"] = $row["singnatureonetwo"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableThree_1"][$i]["processRequirements_3"] = $row["requirementthree"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableThree_1"][$i]["testResult_3"] = $row["testresultonethree"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableThree_1"][$i]["inspectorSingnature_3"] = $row["singnatureonethree"];
							
							$i++;
						}
					}
					//查询第四个表
					$sql = "SELECT `weldingnumber`,`requirementone`,`testresultone`,`singnatureone`,`requirementtwo`,`testresultonetwo`,`singnatureonetwo`,`requirementthree`,`testresultonethree`,`singnatureonethree` FROM `weldingtablefour` WHERE `weldingtable_id`='".$contactId."' ORDER BY `id`";
					$result = $conn->query($sql);
					if($result->num_rows > 0){
						$i = 0;
						while($row = $result->fetch_assoc()){
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableThree_2"][$i]["weldNumber"] = $row["weldingnumber"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableThree_2"][$i]["processRequirements_1"] = $row["requirementone"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableThree_2"][$i]["testResult_1"] = $row["testresultone"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableThree_2"][$i]["inspectorSingnature_1"] = $row["singnatureone"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableThree_2"][$i]["processRequirements_2"] = $row["requirementtwo"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableThree_2"][$i]["testResult_2"] = $row["testresultonetwo"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableThree_2"][$i]["inspectorSingnature_2"] = $row["singnatureonetwo"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableThree_2"][$i]["processRequirements_3"] = $row["requirementthree"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableThree_2"][$i]["testResult_3"] = $row["testresultonethree"];
							$ret_data["data"]["weldingInfo"][$indexNum]["weldingTableThree_2"][$i]["inspectorSingnature_3"] = $row["singnatureonethree"];
							
							$i++;
						}
					}
				}
			}
			
			$json = json_encode($ret_data);
			echo $json;
			break;
		
		case "getCraftsmanshipPrintData"://----------------------------------制造工艺及检验表打印------------------------------------------
			//接收数据
			$relateId = isset($_GET["relateId"]) ? $_GET["relateId"] : "";//表【craftsmanshiptree】的id
			
			//返回数据
			$returnData = array(
				"state" => "success",
				"message" => "",
				"data" => array()
			);
			
			$craftsmanshipIdArr = array();
			//初始化数据形式
			$sql = "SELECT `id` FROM `craftsmanshiptable` WHERE `craftsmanshiptree_id`='".$relateId."'";
			$result = $conn->query($sql);
			if($result->num_rows > 0){
				$i = 0;
				while($row = $result->fetch_assoc()){
					$craftsmanshipIdArr[$i] = $row["id"];
					$returnData["data"][$i]["model"] = "";
					$returnData["data"][$i]["craftsmanshipTableHeader"] = array();
					$returnData["data"][$i]["craftsmanshipTableBody_1"] = array();//模板一
					$returnData["data"][$i]["craftsmanshipTableBody_2"] = array();//模板二
					$returnData["data"][$i]["craftsmanshipTableBody_3"] = array();//模板三
					$returnData["data"][$i]["craftsmanshipTableBodyResult"] = array();//模板一、二
					$returnData["data"][$i]["craftsmanshipTableFooter"] = array();
					
					$i++;
				}
			}
			
			foreach($craftsmanshipIdArr as $indexcraftsmanship => $contactId){
				if(!empty($contactId)){
					//主表信息
					$sql = "SELECT * FROM `craftsmanshiptable` WHERE `id`='".$contactId."'";
					$result = $conn->query($sql);
					if($result->num_rows > 0){
						while($row = $result->fetch_assoc()){
							$returnData["data"][$indexcraftsmanship]["model"] = $row["model"];//返回模型值
							//头部信息
							$returnData["data"][$indexcraftsmanship]["craftsmanshipTableHeader"]["contactId"] = $row["id"];
							$returnData["data"][$indexcraftsmanship]["craftsmanshipTableHeader"]["productName"] = $row["productname"];
							$returnData["data"][$indexcraftsmanship]["craftsmanshipTableHeader"]["ownPartName"] = $row["ownpartname"];
							$returnData["data"][$indexcraftsmanship]["craftsmanshipTableHeader"]["partsName"] = $row["partname"];
							$returnData["data"][$indexcraftsmanship]["craftsmanshipTableHeader"]["workpieceNumber"] = $row["workpiecenumber"];
							$returnData["data"][$indexcraftsmanship]["craftsmanshipTableHeader"]["productDrawingNumber"] = $row["productdrawnumber"];
							$returnData["data"][$indexcraftsmanship]["craftsmanshipTableHeader"]["ownPartDrawingNumber"] = $row["ownpartdrawnumber"];
							$returnData["data"][$indexcraftsmanship]["craftsmanshipTableHeader"]["partsDrawingNumber"] = $row["partdrawnumber"];
							$returnData["data"][$indexcraftsmanship]["craftsmanshipTableHeader"]["quantity"] = $row["quantity"];
							
							//尾部信息
							$returnData["data"][$indexcraftsmanship]["craftsmanshipTableFooter"]["finalConclusion"] = $row["finalconclusion"];
							$returnData["data"][$indexcraftsmanship]["craftsmanshipTableFooter"]["inspector"] = $row["inspector"];
							$returnData["data"][$indexcraftsmanship]["craftsmanshipTableFooter"]["inspectionAudit"] = $row["inspectionaudit"];
							$returnData["data"][$indexcraftsmanship]["craftsmanshipTableFooter"]["mark"] = $row["mark"];
							$returnData["data"][$indexcraftsmanship]["craftsmanshipTableFooter"]["numberOfPlaces"] = $row["numberofplaces"];
							$returnData["data"][$indexcraftsmanship]["craftsmanshipTableFooter"]["changeTheFileNumber"] = $row["changethefilenumber"];
							$returnData["data"][$indexcraftsmanship]["craftsmanshipTableFooter"]["signature"] = $row["signature"];
							$returnData["data"][$indexcraftsmanship]["craftsmanshipTableFooter"]["date"] = $row["date"];
							$returnData["data"][$indexcraftsmanship]["craftsmanshipTableFooter"]["establishment"] = $row["establishment"];
							$returnData["data"][$indexcraftsmanship]["craftsmanshipTableFooter"]["review"] = $row["review"];
							switch($row["model"]){//根据模型编号返回相应的信息
								case "1":														
									//结论与不符合确定【模板一、二】
									$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBodyResult"]["conclusion"] = $row["conclusion"];
									$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBodyResult"]["inconsistentConfirmation"] = $row["inconsistentconfirmation"];
									
									$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_1"]["fileOne"] = $row["firstfive"];
									$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_1"]["imgHtml"] = "";
									//模板一可遍历的数据
									$sql = "SELECT * FROM `craftsmanshiptableone` WHERE `craftsmanship_id`='".$row["id"]."' ORDER BY `id`";
									$result_2 = $conn->query($sql);
									if($result_2->num_rows > 0){
										$i = 0;	
										while($row_2 = $result_2->fetch_assoc()){
											$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_1"]["rowsData"][$i]["serialNumber"] = $row_2["serialnumber"];
											$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_1"]["rowsData"][$i]["processFlow"] = $row_2["processflow"];
											$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_1"]["rowsData"][$i]["inspectionContent"] = $row_2["inspectioncontent"];
											$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_1"]["rowsData"][$i]["skillsRequirement"] = $row_2["skillsrequirement"];
											$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_1"]["rowsData"][$i]["selfTest_13"] = $row_2["selftest_13"];
											$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_1"]["rowsData"][$i]["selfTest_14"] = $row_2["selftest_14"];
											$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_1"]["rowsData"][$i]["selfTest_15"] = $row_2["selftest_15"];
											$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_1"]["rowsData"][$i]["selfTest_16"] = $row_2["selftest_16"];
											$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_1"]["rowsData"][$i]["signature_1"] = $row_2["signature_1"];
											$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_1"]["rowsData"][$i]["qualityInspection_13"] = $row_2["qualityinspection_13"];
											$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_1"]["rowsData"][$i]["qualityInspection_14"] = $row_2["qualityinspection_14"];
											$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_1"]["rowsData"][$i]["qualityInspection_15"] = $row_2["qualityinspection_15"];
											$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_1"]["rowsData"][$i]["qualityInspection_16"] = $row_2["qualityinspection_16"];
											$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_1"]["rowsData"][$i]["signatture_2"] = $row_2["signature_2"];
											
											$i++;
										}
									}							
									break;
								case "2":
									//结论与不符合确定【模板一、二】
									$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBodyResult"]["conclusion"] = $row["conclusion"];
									$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBodyResult"]["inconsistentConfirmation"] = $row["inconsistentconfirmation"];
									//图片信息
									$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_2"]["fileOne"] = $row["secondmodelimageone"];
									$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_2"]["fileTwo"] = $row["secondmodelimagetwo"];
									$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_2"]["fileThree"] = $row["secondmodelimagethree"];
									//html容器
									$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_2"]["imgHtmlOne"] = "";
									$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_2"]["imgHtmlTwo"] = "";
									$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_2"]["imgHtmlTherr"] = "";
									
									//模板二可遍历的数据
									$sql = "SELECT * FROM `craftsmanshiptabletwo` WHERE `craftsmanship_id`='".$row["id"]."' ORDER BY `id`";
									$result_2 = $conn->query($sql);
									if($result_2->num_rows > 0){
										$i = 0;	
										while($row_2 = $result_2->fetch_assoc()){
											$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_2"]["rowsData"][$i]["serialNumber"] = $row_2["serialnumber"];
											$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_2"]["rowsData"][$i]["processFlow"] = $row_2["processflow"];
											$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_2"]["rowsData"][$i]["inspectionContent"] = $row_2["inspectioncontent"];
											$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_2"]["rowsData"][$i]["skillsRequirement"] = $row_2["skillsrequirement"];
											$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_2"]["rowsData"][$i]["selfTest"] = $row_2["selftest"];
											$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_2"]["rowsData"][$i]["signature"] = $row_2["signature"];
											
											$i++;
										}
									}
									break;
								case "3":
									$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_3"]["fileOne"] = $row["thirdmodelimage"];
									$returnData["data"][$indexcraftsmanship]["craftsmanshipTableBody_3"]["imgHtml"] = "";
									break;
								default:
									$returnData["state"] = "failure";
									$returnData["message"] = "模型参数无效";					
							}
						}
					}
				}
			}

			$json = json_encode($returnData);
			echo $json;
			break;
		case "deleteTreeNodeWelding": //-------------------------------删除焊接树节点--------------------------------------------------
			//接收数据
			$relateId = isset($_GET["relateId"]) ? $_GET["relateId"] : "";//表【weldingtree】的id
			
			//通过时间戳获取同类表id
			$sql = "SELECT b.`id` FROM `weldingtree` a,`craftsmanshiptree` b WHERE a.`id`='".$relateId."' and a.ctime=b.ctime";
			$result = $conn->query($sql);
			$row = $result->fetch_assoc();
			$relateId1 = $row["id"];
			//返回数据
			$ret_data = array(
				"state" => "success",
				"message" => ""				
			);
			
			//根据ID删除表【weldingtree】的记录
			$sql = "DELETE FROM `weldingtree` WHERE `id`='".$relateId."'";
			if(!$conn->query($sql)){
				$ret_data["state"] = "fail";
				$ret_data["message"] .= "【weldingtree】删除失败;";
			}
			
			$contactIdArr = array();//表【weldingtable】的id
			$sql = "SELECT `id` FROM `weldingtable` WHERE `weldingtree_id`='".$relateId."'";
			$result = $conn->query($sql);
			if($result->num_rows > 0){
				while($row = $result->fetch_assoc()){
					$contactIdArr[] = $row["id"];
				}
			}
			
			foreach($contactIdArr as $index => $contactId){
				//删除表【weldingtable】的图片
				$sql = "SELECT `weldingnumbermap` FROM `weldingtable` WHERE `id`='".$contactId."'";
				$result = $conn->query($sql);
				if($result->num_rows > 0){
					while($row = $result->fetch_assoc()){
						if(!empty($row["weldingnumbermap"])){
							$savePath = "../".$row["weldingnumbermap"];
							if(file_exists($savePath)){
								@unlink($savePath);
							}
						}
					}
				}
				//删除表【weldingtable】的数据
				$sql = "DELETE FROM `weldingtable` WHERE `id`='".$contactId."'";
				if(!$conn->query($sql)){
					$ret_data["state"] = "fail";
					$ret_data["message"] .= "表weldingtable记录删除失败；";
				}
				//删除表【weldingtableone】的数据
				$sql = "DELETE FROM `weldingtableone` WHERE `weldingtable_id`='".$contactId."'";
				if(!$conn->query($sql)){
					$ret_data["state"] = "fail";
					$ret_data["message"] .= "表weldingtableone记录删除失败；";
				}
				//删除表【weldingtabletwo】的数据
				$sql = "DELETE FROM `weldingtabletwo` WHERE `weldingtable_id`='".$contactId."'";
				if(!$conn->query($sql)){
					$ret_data["state"] = "fail";
					$ret_data["message"] .= "表weldingtabletwo记录删除失败；";
				}
				//删除表【weldingtablethree】的数据
				$sql = "DELETE FROM `weldingtablethree` WHERE `weldingtable_id`='".$contactId."'";
				if(!$conn->query($sql)){
					$ret_data["state"] = "fail";
					$ret_data["message"] .= "表weldingtablethree记录删除失败；";
				}
				//删除表【weldingtablefour】的数据
				$sql = "DELETE FROM `weldingtablefour` WHERE `weldingtable_id`='".$contactId."'";
				if(!$conn->query($sql)){
					$ret_data["state"] = "fail";
					$ret_data["message"] .= "表weldingtablefour记录删除失败；";
				}
			}

			//表1查询出的id
			$relateId = $relateId1;			
			
			//返回数据
			$returnData = array(
				"state" => "success",
				"message" => ""				
			);
			
			//根据ID删除表【craftsmanshiptree】的记录
			$sql = "DELETE FROM `craftsmanshiptree` WHERE `id`='".$relateId."'";
			if(!$conn->query($sql)){
				$returnData["state"] = "fail";
				$returnData["message"] .= "【craftsmanshiptree】删除失败;";
			}
			
			$contactIdArr = array();//表【craftsmanshiptable】的id
			$sql = "SELECT `id` FROM `craftsmanshiptable` WHERE `craftsmanshiptree_id`='".$relateId."'";
			$result = $conn->query($sql);
			if($result->num_rows > 0){
				while($row = $result->fetch_assoc()){
					$contactIdArr[] = $row["id"];
				}
			}
			
			foreach($contactIdArr as $index => $contactId){
				//删除【craftsmanshiptable】的模板二的图，模板三的图
				$sql = "SELECT `secondmodelimageone`,`secondmodelimagetwo`,`secondmodelimagethree`,`thirdmodelimage` FROM `craftsmanshiptable` WHERE `id`='".$contactId."'";
				$result = $conn->query($sql);
				if($result->num_rows > 0){
					$savePathArr = array();
					while($row = $result->fetch_assoc()){
						$savePathArr[0] = $row["secondmodelimageone"];
						$savePathArr[1] = $row["secondmodelimagetwo"];
						$savePathArr[2] = $row["secondmodelimagethree"];
						$savePathArr[3] = $row["thirdmodelimage"];
					}				
					foreach($savePathArr as $index => $savePath){
						if(!empty($savePath)){
							$realsavePath = "../".$savePath;
							if(file_exists($realsavePath)){
								@unlink($realsavePath);
							}
						}
					}
				}
				//删除【craftsmanshiptable】表记录
				$sql = "DELETE FROM `craftsmanshiptable` WHERE `id`='".$contactId."' ";
				if(!$conn->query($sql)){
					$returnData["state"] = "fail";
					$returnData["message"] .= "表craftsmanshiptable记录删除失败";
				}
				//删除【craftsmanshiptableone】表记录
				$sql = "DELETE FROM `craftsmanshiptableone` WHERE `craftsmanship_id`='".$contactId."' ";
				if(!$conn->query($sql)){
					$returnData["state"] = "fail";
					$returnData["message"] .= "表craftsmanshiptableone记录删除失败";
				}
				//删除【craftsmanshiptabletwo】表记录
				$sql = "DELETE FROM `craftsmanshiptabletwo` WHERE `craftsmanship_id`='".$contactId."' ";
				if(!$conn->query($sql)){
					$returnData["state"] = "fail";
					$returnData["message"] .= "表craftsmanshiptabletwo记录删除失败";
				}
			}
			
			$json = json_encode($returnData);
			echo $json;
			break;
		//	焊接工艺卡复制
		case "copyWelding":
			//接收数据
			$relateId = isset($_GET["contactId"]) ? $_GET["contactId"] : "";//表【weldingtree】的id
			//返回给前端的数据
			$returnData = array(
				"state" => "success",
				"message" => ""
			);
			//获取旧时间，复制修改函数REPLACE需要定值修改
			$sql = "select `weldingtree_id`,`ctime` from `weldingtable` where id='".$relateId."'";
			$result = $conn->query($sql);
			$row = $result->fetch_assoc();
			$oldTime = $row["ctime"];
			//复制首表信息，返回自增id
			$sql = "INSERT INTO `weldingtable`(`weldingtree_id`,`processnumber`,`quantity`,`workpiecenumber`,`workshop`,`workordernumber`,`producname`,`productcode`,`partname`,`partdrawingnumber`,`finallyresult`,`inspectorsingnature`,`finallydate`,`weldingsequence`,`weldingnumbermap`,`ctime`)";
			$sql .= "select `weldingtree_id`,`processnumber`,`quantity`,`workpiecenumber`,`workshop`,`workordernumber`,`producname`,`productcode`,`partname`,`partdrawingnumber`,`finallyresult`,`inspectorsingnature`,`finallydate`,`weldingsequence`,`weldingnumbermap`,REPLACE(`ctime`,'".$oldTime."','".time()."') from `weldingtable` where `id` = '".$relateId."'";
			$autoIncrementId = $conn->query($sql) ? $conn->insert_id : "";//获取成功插入后的id
			
			if(!empty($autoIncrementId)){
				//复制第一个表信息
				$sql = "INSERT INTO `weldingtableone`(`weldingtable_id`,`weldingnumber`,`materialfirst`,`specificationsfirst`,`materialsecond`,`specificationssecond`,`weldingmethod`,`grooveform`,`consumables`,`specifications`,`weldinglayer`,`weldingtrack`,`gas`,`current`,`actualcurrentfirst`,`actualcurrentsecond`,`voltage`,`actualvoltagefirst`,`actualvoltagesecond`,`specificationnumber`,`ratingnumber`,`flawdetection`,`steelstamp`,`ctime`)";
				$sql .= "select REPLACE(`weldingtable_id`,'".$relateId."','".$autoIncrementId."'),`weldingnumber`,`materialfirst`,`specificationsfirst`,`materialsecond`,`specificationssecond`,`weldingmethod`,`grooveform`,`consumables`,`specifications`,`weldinglayer`,`weldingtrack`,`gas`,`current`,`actualcurrentfirst`,`actualcurrentsecond`,`voltage`,`actualvoltagefirst`,`actualvoltagesecond`,`specificationnumber`,`ratingnumber`,`flawdetection`,`steelstamp`,REPLACE(`ctime`,'".$oldTime."','".time()."') from `weldingtableone` where `weldingtable_id` = '".$relateId."'";
				$conn->query($sql);
				
				//复制第二个表信息
				$sql = "INSERT INTO `weldingtabletwo`(`weldingtable_id`,`serialnumber`,`checkcontent`,`processrequirement`,`testresult`,`singnature`,`ctime`)";
				$sql .= "select REPLACE(`weldingtable_id`,'".$relateId."','".$autoIncrementId."'),`serialnumber`,`checkcontent`,`processrequirement`,`testresult`,`singnature`,REPLACE(`ctime`,'".$oldTime."','".time()."') from `weldingtabletwo` where `weldingtable_id` = '".$relateId."'";
				$conn->query($sql);
				
				//复制第三个表信息
				$sql = "INSERT INTO `weldingtablethree`(`weldingtable_id`,`weldingnumber`,`requirementone`,`testresultone`,`singnatureone`,`requirementtwo`,`testresultonetwo`,`singnatureonetwo`,`requirementthree`,`testresultonethree`,`singnatureonethree`,`ctime`)";
				$sql .= "select REPLACE(`weldingtable_id`,'".$relateId."','".$autoIncrementId."'),`weldingnumber`,`requirementone`,`testresultone`,`singnatureone`,`requirementtwo`,`testresultonetwo`,`singnatureonetwo`,`requirementthree`,`testresultonethree`,`singnatureonethree`,REPLACE(`ctime`,'".$oldTime."','".time()."') from `weldingtablethree` where `weldingtable_id` = '".$relateId."'";
				$conn->query($sql);
				
				//复制第四个表信息
				$sql = "INSERT INTO `weldingtablefour`(`weldingtable_id`,`weldingnumber`,`requirementone`,`testresultone`,`singnatureone`,`requirementtwo`,`testresultonetwo`,`singnatureonetwo`,`requirementthree`,`testresultonethree`,`singnatureonethree`,`ctime`)";
				$sql .= "select REPLACE(`weldingtable_id`,'".$relateId."','".$autoIncrementId."'),`weldingnumber`,`requirementone`,`testresultone`,`singnatureone`,`requirementtwo`,`testresultonetwo`,`singnatureonetwo`,`requirementthree`,`testresultonethree`,`singnatureonethree`,REPLACE(`ctime`,'".$oldTime."','".time()."') from `weldingtablefour` where `weldingtable_id` = '".$relateId."'";
				$conn->query($sql);
				$returnData["message"] = "保存成功";
			}else{
				$returnData["state"] = "failure";
				$returnData["message"] = "自增值ID为空";
			}
			
			$json = json_encode($returnData);
			echo $json;
			break;
		//制造工艺卡复制	
		case "copyCraftsmanship":
			//接收数据
			$relateId = isset($_GET["contactId"]) ? $_GET["contactId"] : "";//表【weldingtree】的id
			//返回给前端的数据
			$returnData = array(
				"state" => "success",
				"message" => ""
			);
			//获取旧时间，复制修改函数REPLACE需要定值修改
			$sql = "select `craftsmanshiptree_id`,`ctime` from `craftsmanshiptable` where id='".$relateId."'";
			$result = $conn->query($sql);
			$row = $result->fetch_assoc();
			$oldTime = $row["ctime"];
			//复制首表信息，也是模板三的表，返回自增id
			$sql = "INSERT INTO `craftsmanshiptable`(`craftsmanshiptree_id`,`model`,`productname`,`ownpartname`,`partname`,`workpiecenumber`,`productdrawnumber`,`ownpartdrawnumber`,`partdrawnumber`,`quantity`,`finalconclusion`,`inspector`,`inspectionaudit`,`mark`,`numberofplaces`,`changethefilenumber`,`signature`,`date`,`establishment`,`review`,`conclusion`,`inconsistentconfirmation`,`firstfive`,`ctime`)";
			$sql .= "select `craftsmanshiptree_id`,`model`,`productname`,`ownpartname`,`partname`,`workpiecenumber`,`productdrawnumber`,`ownpartdrawnumber`,`partdrawnumber`,`quantity`,`finalconclusion`,`inspector`,`inspectionaudit`,`mark`,`numberofplaces`,`changethefilenumber`,`signature`,`date`,`establishment`,`review`,`conclusion`,`inconsistentconfirmation`,`firstfive`,REPLACE(`ctime`,'".$oldTime."','".time()."') from `craftsmanshiptable` where `id` = '".$relateId."'";
			$autoIncrementId = $conn->query($sql) ? $conn->insert_id : "";//获取成功插入后的id
			
			if(!empty($autoIncrementId)){
				//复制模板一表信息
				$sql = "INSERT INTO `craftsmanshiptableone`(`craftsmanship_id`,`serialnumber`,`processflow`,`inspectioncontent`,`skillsrequirement`,`selftest_13`,`selftest_14`,`selftest_15`,`selftest_16`,`signature_1`,`qualityinspection_13`,`qualityinspection_14`,`qualityinspection_15`,`qualityinspection_16`,`signature_2`)";
				$sql .= "select REPLACE(`craftsmanship_id`,'".$relateId."','".$autoIncrementId."'),`serialnumber`,`processflow`,`inspectioncontent`,`skillsrequirement`,`selftest_13`,`selftest_14`,`selftest_15`,`selftest_16`,`signature_1`,`qualityinspection_13`,`qualityinspection_14`,`qualityinspection_15`,`qualityinspection_16`,`signature_2` from `craftsmanshiptableone` where `craftsmanship_id` = '".$relateId."'";
				$conn->query($sql);
				
				//复制模板二表信息
				$sql = "INSERT INTO `craftsmanshiptabletwo`(`craftsmanship_id`,`serialnumber`,`processflow`,`inspectioncontent`,`skillsrequirement`,`selftest`,`signature`,`ctime`)";
				$sql .= "select REPLACE(`craftsmanship_id`,'".$relateId."','".$autoIncrementId."'),`serialnumber`,`processflow`,`inspectioncontent`,`skillsrequirement`,`selftest`,`signature`,REPLACE(`ctime`,'".$oldTime."','".time()."') from `craftsmanshiptabletwo` where `craftsmanship_id` = '".$relateId."'";
				$conn->query($sql);
				$returnData["message"] = "保存成功";
			}else{
				$returnData["state"] = "failure";
				$returnData["message"] = "自增值ID为空";
			}
			
			$json = json_encode($returnData);
			echo $json;
			break;
		default :
			echo '{"state":"failure","message":"没有对应的标志"}';
	}
	
	$conn->close();
?>