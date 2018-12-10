<?php
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
	
	require("../../conn.php");
	
	$flag = isset($_REQUEST["flag"]) ? $_REQUEST["flag"]:'';
	
	$ret_data= array(
		"success"=>"success",
		"msg"=>"",
		"data"=>array()
	);
	
	
	switch($flag){
		case "Sidebar":
			$account = isset($_POST["account"]) ? $_POST["account"] :"";
			
			$tempArray = array();//根据`seeModule`的值查出相关信息并拼凑出与前端Json格式类似的数组
			
			$sql = "SELECT seeModule from user WHERE account = '$account' ";
			$res=$conn->query($sql);
			if($res->num_rows>0){
				while($row=$res->fetch_assoc()){
					$seeModule = $row["seeModule"];
					
					if(strlen($seeModule) > 0){
						$sql1 = "SELECT `id`,`icon`,`index`,`title`,`subs`,`sindex`,`stitle` FROM moudle_tree WHERE FIND_IN_SET(id,'".$seeModule."')";//FIND_IN_SET自己百度
						$res1=$conn->query($sql1);
						if($res1->num_rows > 0){
							while($row1 =$res1->fetch_assoc()){
								if(!array_key_exists($row1["index"], $tempArray)){//优先判断大模块是否已经拼凑
									$tempArray[$row1["index"]]["icon"] = $row1["icon"];
									$tempArray[$row1["index"]]["index"] = $row1["index"];
									$tempArray[$row1["index"]]["title"] = $row1["title"];
								}
								if($row1["subs"] == "1"){//判断是否存在小模块
									if(!empty($row1["sindex"]) && !empty($row1["stitle"])){//判断小模块是否为空
										$tempArray[$row1["index"]]["subs"][$row1["id"]]["index"] = $row1["sindex"];
										$tempArray[$row1["index"]]["subs"][$row1["id"]]["title"] = $row1["stitle"];
									}
								}
							}
						}
					}				
					
				}
				
			}
			/*根据拼凑的数组再次重新组成与前端一样的Json格式字符串*/
			if(count($tempArray) > 0){
				$jsonString = ""; //返回给前端一样的Json格式字符串
				
				foreach($tempArray as $ky => $datainfo){
					$jsonString .= ',{"icon":"'.$datainfo["icon"].'","index":"'.$datainfo["index"].'","title":"'.$datainfo["title"].'"';
					if(array_key_exists("subs", $datainfo)){
						if(count($datainfo["subs"]) > 0){
							$jsonString_subs = "";
							foreach($datainfo["subs"] as $ky2 => $datainfo2){
								$jsonString_subs .= ',{"index":"'.$datainfo2["index"].'","title":"'.$datainfo2["title"].'"}';
							}
							$jsonString_subs = substr($jsonString_subs, 1);
							$jsonString .= ',"subs":['.$jsonString_subs.']';
						}
						$jsonString .= '}';
					}else{
						$jsonString .= '}';
					}
				}
				$jsonString = '['.substr($jsonString, 1).']';
				
				$ret_data["data"] = $jsonString;
			}
			
			$json = json_encode($ret_data);
			echo $json;
			break;
		default :
			echo '{"success":"failure","msg":"没有对应的标志"}';
	}
	
	$conn->close();
?>