<?php
	header("Access-Control-Allow-Origin: *");
	require_once '../../conn.php';
	
	/*毫秒级的时间戳*/
	function getMillisecond() {
		list($t1, $t2) = explode(' ', microtime());
		return (float)sprintf('%.0f',(floatval($t1)+floatval($t2))*1000);
	}
	
	$flag = isset($_REQUEST["flag"]) ? $_REQUEST["flag"] : "";
	switch($flag){
		case "heattreatmentDataOne" ://热处理工艺技术要求及检验记录表信息保存
			$treeId = isset($_POST["treeId"]) ? $_POST["treeId"] :"";//表【craftsmanshiptree】的id
			$heattreatmentTableHeader = isset($_POST["heattreatmentTableHeader"]) ? json_decode($_POST["heattreatmentTableHeader"],TRUE) : array();
			$model = isset($_REQUEST["model"]) ? $_REQUEST["model"] : "";
			//返回数据
			$returnData = array(
				"state" => "success",
				"message" => "",
				"sql" => ""				
			);
			//保存单一信息
			if(count($heattreatmentTableHeader) > 0){
				$sql  = "INSERT INTO `heattreatment`(weldingtree_id,model,productName,ownPartName,partsName,productDrawingNumber,ownPartDrawingNumber";
				$sql .= ",partsDrawingNumber,ctime) VALUES('".$treeId."','".$model."','".$heattreatmentTableHeader["productName"]."','".$heattreatmentTableHeader["ownPartName"]."'";
				$sql .= ",'".$heattreatmentTableHeader["partsName"]."','".$heattreatmentTableHeader["productDrawingNumber"]."','".$heattreatmentTableHeader["ownPartDrawingNumber"]."'";
				$sql .= ",'".$heattreatmentTableHeader["partsDrawingNumber"]."','".time()."')";
				$conn->query($sql);
			}else{
				$returnData["state"] = "failure";
				$returnData["message"] = "主要数据为空";
			}
			
			$json = json_encode($returnData);
			echo $json;
			break;
		//获取热处理信息	
		case "getHeattreatmentInfoData":
			//接收数据
			$contactId = isset($_GET["contactID"]) ? $_GET["contactID"] : "";

			//返回数据
			$returnData = array(
				"state" => "success",
				"message" => "",
				"data" => array(
					"craftsmanshipTableHeader" => array()
				)
			);

			//主表信息
			$sql = "SELECT * FROM `heattreatment` WHERE `id`='".$contactId."'";
			$result = $conn->query($sql);
			if($result->num_rows > 0){
				while($row = $result->fetch_assoc()){
					//头部信息
					$returnData["data"]["craftsmanshipTableHeader"]["contactId"] = $row["id"];
					$returnData["data"]["craftsmanshipTableHeader"]["productName"] = $row["productName"];
					$returnData["data"]["craftsmanshipTableHeader"]["ownPartName"] = $row["ownPartName"];
					$returnData["data"]["craftsmanshipTableHeader"]["partsName"] = $row["partsName"];
					$returnData["data"]["craftsmanshipTableHeader"]["productDrawingNumber"] = $row["productDrawingNumber"];
					$returnData["data"]["craftsmanshipTableHeader"]["ownPartDrawingNumber"] = $row["ownPartDrawingNumber"];
					$returnData["data"]["craftsmanshipTableHeader"]["partsDrawingNumber"] = $row["partsDrawingNumber"];
					$returnData["data"]["value"] = $row["model"];

				}
			}
			$json = json_encode($returnData);
			echo $json;
			break;
		//复制
		case "copyHeattreatment":
			//接收数据
			$relateId = isset($_GET["contactId"]) ? $_GET["contactId"] : "";//表【weldingtree】的id
			//返回给前端的数据
			$returnData = array(
				"state" => "success",
				"message" => ""
			);
			//获取旧时间，复制修改函数REPLACE需要定值修改
			$sql = "select `weldingtree_id`,`ctime` from `heattreatment` where id='".$relateId."'";
			$result = $conn->query($sql);
			$row = $result->fetch_assoc();
			$oldTime = $row["ctime"];
			//复制首表信息，返回自增id
			$sql = "INSERT INTO heattreatment ( weldingtree_id,model,productName,ownPartName,partsName,productDrawingNumber,ownPartDrawingNumber,partsDrawingNumber,ctime )";
			$sql .= "SELECT weldingtree_id,model,productName,ownPartName,partsName,productDrawingNumber,ownPartDrawingNumber,partsDrawingNumber,REPLACE(`ctime`,'".$oldTime."','".time()."') FROM heattreatment WHERE `id` = '".$relateId."'";
			$autoIncrementId = $conn->query($sql) ? $conn->insert_id : "";//获取成功插入后的id
			
			if(!empty($autoIncrementId)){
				$returnData["message"] = "保存成功";
			}else{
				$returnData["state"] = "failure";
				$returnData["message"] = "自增值ID为空";
			}
			$json = json_encode($returnData);
			echo $json;
			break;
		//删除
		case "deleteHeattreatment":
			//接收数据
			$contactId = isset($_GET["contactId"]) ? $_GET["contactId"] : "";
			
			//返回数据
			$returnData = array(
				"state" => "success",
				"message" => ""				
			);

			//删除表头表尾
			$sql = "DELETE FROM `heattreatment` WHERE `id`='".$contactId."'";
			if(!$conn->query($sql)){
				$returnData["state"] = "fail";
				$returnData["message"] .= "表头记录删除失败";
			}
			$json = json_encode($returnData);
			echo $json;
			break;
	}
?>