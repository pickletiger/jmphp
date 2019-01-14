<?php
	require("../../conn.php");
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
	$ret_data='';
	$flag = isset($_POST["flag"])?$_POST["flag"]:'';
	$id = isset($_POST["id"])?$_POST["id"]:'';
	
	switch($flag){
		// 获取点击工艺路线的模态框内容
		case 'show':
		$sql = "SELECT id,modid,station,schedule_date,isfinish FROM workshop_k WHERE routeid='$id'";
		$res = $conn->query($sql);
		if($res->num_rows>0){
			$i=0;
			while($row=$res->fetch_assoc()){
				$ret_data["item"][$i]["modid"] = $row["modid"];
				$ret_data["item"][$i]["station"] = $row["station"];
				$ret_data["item"][$i]["schedule_date"] = $row["schedule_date"];
				$ret_data["item"][$i]["id"] = $row["id"];
				switch($row["isfinish"]){
					case 0:
					$ret_data["item"][$i]["isfinish"]='未完成';
					break;
					case 1:
					$ret_data["item"][$i]["isfinish"]='完成';
					break;
					case 2:
					$ret_data["item"][$i]["isfinish"]='在建中';
					break;
					case 3:
					$ret_data["item"][$i]["isfinish"]='准备中';
					break;
				}	
				$i++;
			}
				$ret_data["success"] = 'success';
		}
		$asql = "SELECT pid,modid,listid,isfinish FROM route WHERE id = '$id'";
		$ares = $conn->query($asql);
		if($ares->num_rows>0){
			while($arow=$ares->fetch_assoc()){
				$ret_data["id"] = $id;
				$listid = (int)$arow["listid"];
				$isfinish = $arow["isfinish"];
				//下节点的id
				$nextid = (int)$arow["listid"]+1;
				$ret_data["nextid"] = (int)$arow["listid"]+1;
				$modid = $arow["modid"];
				$pid = $arow["pid"];
			}
		}
		// 判断该部件节点的数量
		$bsql = "SELECT isfinish FROM route WHERE modid = '$modid' AND pid = '$pid'";
		$bres = $conn->query($bsql);
		$ret_data["count"] = $bres->num_rows;
 		// 0状态代表该部件已经完成,不允许再继续添加节点
		// 1状态表示该节点的下一节点已经在进行,不允许再进行增加节点的操作
 		// 2状态表示该节点可以成功添加下一节点
 		
		// 判断是在最后一个节点添加,且最后一个节点是否处于未完成状态,假如完成则不允许继续添加节点
		if($ret_data["count"]==$listid&&$isfinish=='1'){
			$ret_data["state"] = '0';
		}
		// 判断不属于最后一个节点的节点的下一节点是否是处于未完成的状态，如是未完成状态允许添加节点 
		else if($listid<$ret_data["count"]) {
			$csql = "SELECT isfinish FROM route WHERE modid = '$modid' AND pid = '$pid' AND listid='$nextid'";
			$cres = $conn->query($csql);
			if($cres->num_rows>0){
				while($crow=$cres->fetch_assoc()){
					if($crow["isfinish"]==0) {
						$ret_data["state"] = 2;
					}else {
						$ret_data["state"] = 1;
					}
				}
			}
		}
		else {
			$ret_data["state"] = 2;
		}
		
		break;
		// 删除工艺路线内的工艺
		case 'del':
		$sql = "DELETE FROM workshop_k WHERE id='$id'";
		$res = $conn->query($sql);
		break;
		// 增加车间节点
		case 'addroute':
		$nextid = isset($_POST["nextid"])?$_POST["nextid"]:'';
		$nextid = (int)$nextid;   //将nextid强制转换为int型
		$route = isset($_POST["route"])?$_POST["route"]:'';
		$sql = "SELECT modid,pid FROM route WHERE id='$id'";
		$res = $conn->query($sql);
		if($res->num_rows>0){
			while($row=$res->fetch_assoc()){
				$modid = $row["modid"];
				$pid = $row["pid"];
			}
		}
		// 更新新增节点后的节点的listid 加一
		$asql = "UPDATE route set listid = listid+1 WHERE listid >= '$nextid' AND modid='$modid' AND pid='$pid'";
		$ares = $conn->query($asql);
		// 新增节点
		$bsql = "INSERT INTO route VALUES(null,'$pid','$modid','$route','$nextid',null,'0',null,null,null,null)";
		$bres = $conn->query($bsql);
		// 更新该部件的route_line 字段
		$ret_data["route_line"] = '';
		$csql = "SELECT route FROM route WHERE modid='$modid' AND pid='$pid' ORDER BY listid";
		$cres = $conn->query($csql);
		if($cres->num_rows>0){
			while($crow=$cres->fetch_assoc()){
				$ret_data["route_line"] = $ret_data["route_line"].'→'.$crow["route"];
			}
		}
		
		$routr_line = $ret_data["route_line"];
		$dsql = "UPDATE route SET route_line = '$routr_line' WHERE pid='$pid' AND modid='$modid'";
		$dres = $conn->query($dsql);
	}
	$conn->close();
	$json = json_encode($ret_data);
	echo $json;
	
?>