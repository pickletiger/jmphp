<?php
	// header("Access-Control-Allow-Origin: *");
	require_once '../../conn.php';
	require_once '../../classes/UploadFile.php';

	/*毫秒级的时间戳*/
	function getMillisecond() {
		list($t1, $t2) = explode(' ', microtime());
		return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
	}
	
	$flag = isset($_REQUEST["flag"]) ? $_REQUEST["flag"] : "";

	switch ($flag) {
		case 'machingInserData':
			//接收数据
			$treeId = isset($_POST["treeId"]) ? $_POST["treeId"] :"";//表【craftsmanshiptree】的id
			$machiningTableHeader = isset($_POST["machiningTableHeader"]) ? json_decode($_POST["machiningTableHeader"],TRUE) : array();
			$machiningTableBody = isset($_POST["machiningTableBody"]) ? json_decode($_POST["machiningTableBody"],TRUE) : array();
			$machiningTableFooter = isset($_POST["machiningTableFooter"]) ? json_decode($_POST["machiningTableFooter"],TRUE) : array();
			
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
				if(isset($_FILES["myfileone"])){
					$uploadfileclass = new UploadFile($_FILES["myfileone"],$fileSaveDir,$fileSaveName);
					$fileSaveSql_tmp = $uploadfileclass->uploadFile();
					$fileSaveSql = substr($fileSaveSql_tmp, 3);
				}
					
			}

			//保存信息
			if(count($machiningTableHeader) > 0){
				$sql = "INSERT INTO machiningtable ( craftsmanshiptree_id, hendnumber, productname, ownpartname, partname, workpiecenumber, productdrawnumber, ownpartdrawnumber, partdrawnumber, quantity, bottonimage, authorizedname, auditor, ctime ) VALUES(";
				$sql .="'".$treeId."','".$machiningTableHeader["hendNumber"]."','".$machiningTableHeader["productName"]."','".$machiningTableHeader["ownPartName"]."','".$machiningTableHeader["partsName"]."','".$machiningTableHeader["workpieceNumber"]."'";
				$sql .=",'".$machiningTableHeader["productDrawingNumber"]."','".$machiningTableHeader["ownPartDrawingNumber"]."','".$machiningTableHeader["partsDrawingNumber"]."','".$machiningTableHeader["quantity"]."','".$fileSaveSql."','".$machiningTableFooter["name1"]."','".$machiningTableFooter["name2"]."','".time()."')";
				$autoIncrementId = $conn->query($sql) ? $conn->insert_id : "";//获取成功插入后的id
				
				if(!empty($autoIncrementId)){
					//保存可遍历的信息
					foreach($machiningTableBody as $index => $datainfo){
						$sql = "INSERT INTO machingbody ( machingtable_id, serialnumber, process, workshop, processcontent, equipment, ctime ) VALUES(";
						$sql .= "'".$autoIncrementId."','".$datainfo["serialNumber"]."','".$datainfo["processFlow"]."','".$datainfo["workshop"]."','".$datainfo["skillsRequirement"]."','".$datainfo["equipment"]."','".time()."'";
						$sql .= ")";
						$returnData["sql"]=$sql;
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
		
		case 'machiningUpdateData':
			//接收数据
			$machiningTableHeader = isset($_POST["machiningTableHeader"]) ? json_decode($_POST["machiningTableHeader"],TRUE) : array();
			$machiningTableBody = isset($_POST["machiningTableBody"]) ? json_decode($_POST["machiningTableBody"],TRUE) : array();
			$machiningTableFooter = isset($_POST["machiningTableFooter"]) ? json_decode($_POST["machiningTableFooter"],TRUE) : array();
			
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
				if(isset($_FILES["myfileone"])){
					$uploadfileclass = new UploadFile($_FILES["myfileone"],$fileSaveDir,$fileSaveName);
					$fileSaveSql_tmp = $uploadfileclass->uploadFile();
					$fileSaveSql = substr($fileSaveSql_tmp, 3);
					$sql = "UPDATE `machiningtable` SET `bottonimage`='".$fileSaveSql."' WHERE `id`='".$machiningTableHeader["contactId"]."'";
					$conn->query($sql);
				}
				
			}

			break;
		case 'getMachiningInfoData':
			//接收数据
			$contactId = isset($_GET["contactID"]) ? $_GET["contactID"] : "";

			//返回数据
			$returnData = array(
				"state" => "success",
				"message" => "",
				"data" => array(
					"machiningTableHeader" => array(),
					"machiningTableBody" => array(),
					"machiningTableFooter" => array()
				)
			);

			//主表信息
			$sql = "SELECT * FROM `machiningtable` WHERE `id`='".$contactId."'";
			$result = $conn->query($sql);
			if($result->num_rows > 0){
				while($row = $result->fetch_assoc()){
					//头部信息
					$returnData["data"]["machiningTableHeader"]["contactId"] = $row["id"];
					$returnData["data"]["machiningTableHeader"]["hendNumber"] = $row["hendnumber"];
					$returnData["data"]["machiningTableHeader"]["productName"] = $row["productname"];
					$returnData["data"]["machiningTableHeader"]["ownPartName"] = $row["ownpartname"];
					$returnData["data"]["machiningTableHeader"]["partsName"] = $row["partname"];
					$returnData["data"]["machiningTableHeader"]["workpieceNumber"] = $row["workpiecenumber"];
					$returnData["data"]["machiningTableHeader"]["productDrawingNumber"] = $row["productdrawnumber"];
					$returnData["data"]["machiningTableHeader"]["ownPartDrawingNumber"] = $row["ownpartdrawnumber"];
					$returnData["data"]["machiningTableHeader"]["partsDrawingNumber"] = $row["partdrawnumber"];
					$returnData["data"]["machiningTableHeader"]["quantity"] = $row["quantity"];

					//尾部信息
					$returnData["data"]["machiningTableFooter"]["name1"] = $row["authorizedname"];
					$returnData["data"]["machiningTableFooter"]["name2"] = $row["auditor"];

					//中间内容
					$sql = "SELECT * FROM `machingbody` WHERE `machingtable_id`='".$row["id"]."' ORDER BY `id`";
					$result_2 = $conn->query($sql);
					if($result_2->num_rows > 0){
						$i = 0;	
						while($row_2 = $result_2->fetch_assoc()){
							$returnData["data"]["machiningTableBody"]["rowsData"][$i]["serialNumber"] = $row_2["serialnumber"];
							$returnData["data"]["machiningTableBody"]["rowsData"][$i]["processFlow"] = $row_2["process"];
							$returnData["data"]["machiningTableBody"]["rowsData"][$i]["workshop"] = $row_2["workshop"];
							$returnData["data"]["machiningTableBody"]["rowsData"][$i]["skillsRequirement"] = $row_2["processcontent"];
							$returnData["data"]["machiningTableBody"]["rowsData"][$i]["equipment"] = $row_2["equipment"];
							
							$i++;
						}
					}
					//图片
					$returnData["data"]["machiningTableBody"]["fileOne"] = $row["bottonimage"];
					$returnData["data"]["machiningTableBody"]["imgHtmlOne"] = "";
				}
			}
			$json = json_encode($returnData);
			echo $json;
			break;
		case 'copyMachining':
			//接收数据
			$relateId = isset($_GET["contactId"]) ? $_GET["contactId"] : "";//表【weldingtree】的id
			//返回给前端的数据
			$returnData = array(
				"state" => "success",
				"message" => ""
			);
			//获取旧时间，复制修改函数REPLACE需要定值修改
			$sql = "select `craftsmanshiptree_id`,`ctime` from `machiningtable` where id='".$relateId."'";
			$result = $conn->query($sql);
			$row = $result->fetch_assoc();
			$oldTime = $row["ctime"];
			//复制首表信息，返回自增id
			$sql = "INSERT INTO machiningtable ( craftsmanshiptree_id, hendnumber, productname, ownpartname, partname, workpiecenumber, productdrawnumber, ownpartdrawnumber, partdrawnumber, quantity, bottonimage, authorizedname, auditor, ctime )";
			$sql .= "select `craftsmanshiptree_id`,`hendnumber`,`productname`,`ownpartname`,`partname`,`workpiecenumber`,`productdrawnumber`,`ownpartdrawnumber`,`partdrawnumber`,`quantity`,`bottonimage`,`authorizedname`,`auditor`,REPLACE(`ctime`,'".$oldTime."','".time()."') from `machiningtable` where `id` = '".$relateId."'";
			$autoIncrementId = $conn->query($sql) ? $conn->insert_id : "";//获取成功插入后的id

			if(!empty($autoIncrementId)){
				$sql = "INSERT INTO machingbody ( machingtable_id, serialnumber, process, workshop, processcontent, equipment, ctime )";
				$sql .= "select REPLACE(`machingtable_id`,'".$relateId."','".$autoIncrementId."'),`serialnumber`,`process`,`workshop`,`processcontent`,`equipment`,REPLACE(`ctime`,'".$oldTime."','".time()."') from `machingbody` where `machingtable_id` = '".$relateId."' order by id";
				$conn->query($sql);
				
				$returnData["message"] = "保存成功";
			}else{
				$returnData["state"] = "failure";
				$returnData["message"] = "自增值ID为空";
			}
			
			$json = json_encode($returnData);
			echo $json;

			break;
		case 'deleteMachining':
			//接收数据
			$contactId = isset($_GET["contactId"]) ? $_GET["contactId"] : "";
			
			//返回数据
			$returnData = array(
				"state" => "success",
				"message" => ""				
			);

			//删除表头表尾
			$sql = "DELETE FROM `machiningtable` WHERE `id`='".$contactId."'";
			if(!$conn->query($sql)){
				$returnData["state"] = "fail";
				$returnData["message"] .= "表头记录删除失败";
			}
			//删除内容
			$sql = "DELETE FROM `machingbody` WHERE `machingtable_id`='".$contactId."'";
			if(!$conn->query($sql)){
				$returnData["state"] = "fail";
				$returnData["message"] .= "表内容记录删除失败";
			}
			$json = json_encode($returnData);
			echo $json;
			break;
	}
?>