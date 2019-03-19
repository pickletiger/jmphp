<?php
	require ("../conn.php");
	header("Access-Control-Allow-Origin: *");
	$ret_data = '';
	$flag = isset($_POST["flag"]) ? $_POST["flag"] : '';
	$number = isset($_POST["number"]) ? $_POST["number"] : '';
//	$number = "1000634971";
	$gNum = array();
	$sql = "select figure_number,name,modid,number,count ,part_url from sending where modid = '$number'";
	$res=$conn->query($sql);
	if($res->num_rows>0){
		$i = 0;
		while($row=$res->fetch_assoc()){
			$ret_data["data"][$i]["figure_number"] = $row["figure_number"];
			$ret_data["data"][$i]["name"] = $row["name"];
			$ret_data["data"][$i]["number"] = $row["modid"];
			$gNum = explode("#",$row["number"]);
			$ret_data["data"][$i]["belong_part"] = $gNum[0].'#';
			$ret_data["data"][$i]["count"] = $row["count"];
			$arr = array();
			$arr=explode(',',$row["part_url"]);
			$base = "http://jmmes.oss-cn-shenzhen.aliyuncs.com/partUpload/";
			foreach($arr as $key => $url){
				$arr[$key] = $base .$url;
			}	
			$ret_data["data"][$i]["photourl"] = $arr;
			
			$i++;
		}
		$ret_data["success"] = 'success';
	}else{
		$ret_data["success"] = 'error';
	}
	$conn->close();
	$json = json_encode($ret_data);
	echo $json;
?>