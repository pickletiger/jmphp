<?php
	require("../../conn.php");
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
	$ret_data='';
	$flag = isset($_POST["flag"])?$_POST["flag"]:'';

	if($flag == "partfile"){
		$id = isset($_POST["id"])?$_POST["id"]:'';
		$sql = "select route,route_line,notNum,remark,backMark,reason,otime,stime,utime,ctime,photourl from onfile where id = '$id' order by Rid ";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			$i = 0;
			while($row=$res->fetch_assoc()){
				$ret_data["data"][$i]["route"] = $row["route"];
				$ret_data["data"][$i]["route_line"] = $row["route_line"];
				$ret_data["data"][$i]["notNum"] = $row["notNum"];
				$ret_data["data"][$i]["remark"] = $row["remark"];
				if($row["backMark"]=="0"){
					$ret_data["data"][$i]["backMark"] = "否";
				}else{
					$ret_data["data"][$i]["backMark"] = "是";
				}
				$arr = array();
				$arr=explode(',',$row["photourl"]);
				$base = "http://jmmes.oss-cn-shenzhen.aliyuncs.com/partUpload/";
				foreach($arr as $key => $url){
					$arr[$key] = $base .$url;
				}	
				$ret_data["data"][$i]["photourl"] = $arr;
				
				$ret_data["data"][$i]["reason"] = $row["reason"];
				$ret_data["data"][$i]["otime"] = $row["otime"];
				$ret_data["data"][$i]["stime"] = $row["stime"];
				$ret_data["data"][$i]["utime"] = $row["utime"];
				$ret_data["data"][$i]["ctime"] = $row["ctime"];
				$i++;
			}
			$ret_data["success"] = 'success';
		}
	}else if($flag=="partdata"){
		$id = isset($_POST["id"])?$_POST["id"]:'';
		$sql = "SELECT figure_number,name,count,standard,radio,child_material,id,child_number,quantity,material,Pmodid FROM onfile WHERE id  = '$id'";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			$i = 0;
			while($row=$res->fetch_assoc()){
				$ret_data["data"]["id"] = $row["id"];
				$ret_data["data"]["figure_number"] = $row["figure_number"];
				$ret_data["data"]["name"] = $row["name"];
				$ret_data["data"]["count"] = $row["count"];
				$ret_data["data"]["standard"] = $row["standard"];
				if($row["radio"]=="1"){
					$ret_data["data"]["radio"] = "关键零部件";
				}else{
					$ret_data["data"]["radio"] = "非关键零部件";
				}
				
				$ret_data["data"]["child_material"] = $row["child_material"];
				$ret_data["data"]["child_number"] = $row["child_number"];
				$ret_data["data"]["quantity"] = $row["quantity"];
				$ret_data["data"]["material"] = $row["material"];
				$ret_data["data"]["Pmodid"] = $row["Pmodid"];
			}
			$ret_data["success"] = 'success';
		}
	}else{
		
	}
		
	
	$conn->close();
	$json = json_encode($ret_data);
	echo $json;
?>