<?php
	require ("../conn.php");
	header("Access-Control-Allow-Origin: *");
	// 允许任意域名发起的跨域请求
	$ret_data = '';
	$flag = isset($_POST["flag"]) ? $_POST["flag"] : '';
	if($flag == "Select"){
		$sql = "select Wmodid,station,name,route,count,figure_number,radio,photourl,inspectcount from test where inspectcount !='0' ORDER BY ftime desc ";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			$i = 0;
			while($row=$res->fetch_assoc()){
				$ret_data["data"][$i]["number"] = $row["Wmodid"];
				$ret_data["data"][$i]["partName"] = $row["name"];
				$ret_data["data"][$i]["processName"] = $row["station"];
				$ret_data["data"][$i]["route"] = $row["route"];
				$ret_data["data"][$i]["count"] = $row["inspectcount"];
				$ret_data["data"][$i]["figure_number"] = $row["figure_number"];
				$partdrawnumber = $row["figure_number"];
				
				//使用部件图号查询制造工艺卡信息
				$sql1 = "select craftsmanshiptree_id,id from craftsmanshiptable where partdrawnumber = '$partdrawnumber'";//使用部件图号查询制造工艺卡信息
				$res1 = $conn ->query($sql1);
				$row1 = $res1 -> fetch_assoc();
				$ret_data["data"][$i]["contactId"] = isset( $row1["id"]) ?  $row1["id"] : '';
				$ret_data["data"][$i]["selectedTreeNode"] = $row1["craftsmanshiptree_id"];
				if($ret_data["data"][$i]["contactId"]==''){
					$ret_data["data"][$i]["show_btn"] = false;
				}else{
					$ret_data["data"][$i]["show_btn"] = true;
				}
				//使用部件图号查询焊接工艺卡信息
				$sql2 = "select id from weldingtable where partdrawingnumber = '$partdrawnumber'";//使用部件图号查询制造工艺卡信息
				$res2 = $conn ->query($sql2);
				$row2 = $res2 -> fetch_assoc();
				$ret_data["data"][$i]["weldingcontactId"] = isset( $row2["id"]) ? $row2["id"] : '';
				if($ret_data["data"][$i]["contactId"]==''){
					$ret_data["data"][$i]["show_btn1"] = false;
				}else{
					$ret_data["data"][$i]["show_btn1"] = true;
				}
				
				$ret_data["data"][$i]["photourl"] = $row["photourl"];
				if($row["radio"]==2){
					$ret_data["data"][$i]["radio"] = "非关键零部件";
				}else{
					$ret_data["data"][$i]["radio"] = "关键零部件";
				}
				$i++;
			}
			$ret_data["success"] = 'success';
		}
	}else if($flag == "Test"){
		$result = $_POST["result"];
		$Number = $_POST["Number"];
		$person = $_POST["person"];
		$type   = $_POST["type"];
		$sql = "UPDATE workshop_k SET isfinish='".$result."',uuser = '".$person."',test_type = '".$type."' WHERE isfinish = '1' and modid='".$Number."'";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			
			}
		$ret_data["success"] = 'success';
	}else{
//		$state = 3;
		$state = $_POST["state"];
		$sql = "select Wmodid,station,name,utime,photourl,route,count,figure_number,radio from test where isfinish = '".$state."'";
		//  $sql = "select Wmodid,station,name,utime,photourl,route,count,figure_number,radio,inspectcount from test where inspectcount !='0'";
		$res=$conn->query($sql);
		if($res->num_rows>0){
			$i = 0;
			while($row=$res->fetch_assoc()){
				$ret_data["data"][$i]["number"] = $row["Wmodid"];
				$ret_data["data"][$i]["partName"] = $row["name"];
				$ret_data["data"][$i]["checkDate"] = $row["utime"];
				$ret_data["data"][$i]["route"] = $row["route"];
				$ret_data["data"][$i]["count"] = $row["count"];
				$ret_data["data"][$i]["figure_number"] = $row["figure_number"];
				$partdrawnumber = $row["figure_number"];
				//使用部件图号查询制造工艺卡信息
				$sql1 = "select craftsmanshiptree_id,id from craftsmanshiptable where partdrawnumber = '$partdrawnumber'";//使用部件图号查询制造工艺卡信息
				$res1 = $conn ->query($sql1);
				$row1 = $res1 -> fetch_assoc();
				$ret_data["data"][$i]["contactId"] = isset( $row1["id"]) ?  $row1["id"] : '';
				$ret_data["data"][$i]["selectedTreeNode"] = $row1["craftsmanshiptree_id"];
				if($ret_data["data"][$i]["contactId"]==''){
					$ret_data["data"][$i]["show_btn"] = false;
				}else{
					$ret_data["data"][$i]["show_btn"] = true;
				}
				//使用部件图号查询焊接工艺卡信息
				$sql2 = "select id from weldingtable where partdrawingnumber = '$partdrawnumber'";//使用部件图号查询制造工艺卡信息
				$res2 = $conn ->query($sql2);
				$row2 = $res2 -> fetch_assoc();
				$ret_data["data"][$i]["weldingcontactId"] = isset( $row2["id"]) ? $row2["id"] : '';
				if($ret_data["data"][$i]["contactId"]==''){
					$ret_data["data"][$i]["show_btn1"] = false;
				}else{
					$ret_data["data"][$i]["show_btn1"] = true;
				}
				$ret_data["data"][$i]["photourl"] = $row["photourl"];
				if($row["radio"]==2){
					$ret_data["data"][$i]["radio"] = "非关键零部件";
				}else{
					$ret_data["data"][$i]["radio"] = "关键零部件";
				}
				$i++;
			}
			$ret_data["success"] = 'success';
		}
	}
	$conn->close();
	$json = json_encode($ret_data);
	echo $json;
?>