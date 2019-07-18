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
	}
?>