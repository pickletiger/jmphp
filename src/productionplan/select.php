<?php
	header("Access-Control-Allow-Origin: *");
	// 允许任意域名发起的跨域请求
	require ("../../conn.php");
	$arr = '';
	$flag = isset($_POST["flag"]) ? $_POST["flag"] : '';
	if ($flag == "Select") {
		$isfinish = isset($_POST["isfinish"]) ? $_POST["isfinish"] : '';
		$list = isset($_POST["list"]) ? $_POST["list"] : '';
		if ($isfinish == '3') {
			$sql = "select modid,figure_number,name,standard,route,count,child_material,number,product_name,remark,routeid,backMark,reason from productionplan WHERE isfinish='3' and Pisfinish='0' and route in $list  ORDER BY backMark DESC,routeid";
			$res = $conn -> query($sql);
			if ($res -> num_rows == TRUE) {
				$i = 0;
				while ($row = $res -> fetch_assoc()) {
					$arr[$i]['modid'] = $row['modid'];
					$arr[$i]['figure_number'] = $row['figure_number'];
					//零件图号
					$arr[$i]['name'] = $row['name'];
					//名称
					$arr[$i]['standard'] = $row['standard'];
					//开料尺寸
					$arr[$i]['route'] = $row['route'];
					//加工工艺路线
					$arr[$i]['count'] = $row['count'];
					//数量
					$arr[$i]['child_material'] = $row['child_material'];
					//规格
					$number = explode("#", $row['number']);
					$arr[$i]['number'] = $number[0] . "#";
					//工单
					$arr[$i]['product_name'] = $number[1] . $row['product_name'];
					//产品名称
					$arr[$i]['remark'] = $row['remark'];
					$arr[$i]['routeid'] = $row['routeid'];
					if ($row['backMark'] == "1") {
						$arr[$i]['backMark'] = "是";
					} else {
						$arr[$i]['backMark'] = "否";
					}
	
					$arr[$i]['reason'] = $row['reason'];
					$i++;
				}
			}
			// 未排产
			if($arr!=''){
				$list_data = json_encode($arr);
				$json = '{"success":"true","rows":' . $list_data . '}';
			}else{
				$list_data = json_encode($arr);
				$json = '{"success":"error","rows":' . $list_data . '}';
			}
		} else if ($isfinish == '0') {
			
			$arr4 = '';
			// 已排产数据列表
			$sql4 = "SELECT id,modid,fid,figure_number,name,standard,count,child_material,remark,routeid,product_name,number,station,schedule_date,route FROM Delivered WHERE Wisfinish = 0  and route in $list ";
			$res4 = $conn -> query($sql4);
			if ($res4 -> num_rows > 0) {
				$i = 0;
				while ($row4 = $res4 -> fetch_assoc()) {
					$arr4[$i]['partid'] = $row4['id'];
					$arr4[$i]['fid'] = $row4['fid'];
					$arr4[$i]['modid'] = $row4['modid'];
					$arr4[$i]['routeid'] = $row4['routeid'];
					$arr4[$i]['figure_number'] = $row4['figure_number'];
					$arr4[$i]['name'] = $row4['name'];
					$arr4[$i]['standard'] = $row4['standard'];
					$arr4[$i]['count'] = $row4['count'];
					$arr4[$i]['route'] = $row4['route'];
					$arr4[$i]['child_material'] = $row4['child_material'];
					$number4 = explode("#", $row4['number']);
					$arr4[$i]['number'] = $number4[0] . "#";
					$arr4[$i]['product_name'] = $number4[1] . $row4['product_name'];
					$arr4[$i]['remark'] = $row4['remark'];
					$arr4[$i]['station'] = $row4['station'];
					$arr4[$i]['schedule_date'] = $row4['schedule_date'];
					$i++;
				}
			}
			// 已排产
			if($arr4!=''){
				$list_data2 = json_encode($arr4);
				$json = '{"success":"true","rows2":'.$list_data2.'}';
			}else{
				$list_data2 = json_encode($arr4);
				$json = '{"success":"error","rows2":'.$list_data2.'}';
			}
			
		} else {
			
			$arr7 = '';	
			//生产中数据列表
			$sql7 = "Select * from projectIng  and route in $list ";
			$res7 = $conn -> query($sql7);
			if ($res7 -> num_rows > 0) {
				$i = 0;
				while ($row7 = $res7 -> fetch_assoc()) {
					$arr7[$i]['partid'] = $row7['id'];
					$arr7[$i]['fid'] = $row7['fid'];
					$arr7[$i]['modid'] = $row7['modid'];
					$arr7[$i]['figure_number'] = $row7['figure_number'];
					$arr7[$i]['name'] = $row7['name'];
					$arr7[$i]['standard'] = $row7['standard'];
					$arr7[$i]['count'] = $row7['count'];
					$arr7[$i]['child_material'] = $row7['child_material'];
					$number7 = explode("#", $row7['number']);
					$arr7[$i]['number'] = $number7[0] . "#";
					$arr7[$i]['product_name'] = $number7[1] . $row7['product_name'];
					$arr7[$i]['remark'] = $row7['remark'];
					$arr7[$i]['station'] = $row7['station'];
					$arr7[$i]['schedule_date'] = $row7['schedule_date'];
					$i++;
				}
	
			}
	
			// 生产中
			if($arr7!=''){
				$list_data3 = json_encode($arr7);
				$json = '{"success":"true","rows3":'.$list_data3.'}';
			}else{
				$list_data3 = json_encode($arr7);
				$json = '{"success":"error","rows3":'.$list_data3.'}';
			}
		}
	}else{
		$isfinish = isset($_POST["isfinish"]) ? $_POST["isfinish"] : '';
		$searchValue = isset($_POST["searchValue"]) ? $_POST["searchValue"] : '';
		$searchCondition = isset($_POST["searchCondition"]) ? $_POST["searchCondition"] : '';
		if ($isfinish == '3') {
			$sql = "select modid,figure_number,name,standard,route,count,child_material,number,product_name,remark,routeid,backMark,reason from productionplan WHERE isfinish='3' and $searchCondition LIKE '%$searchValue%' ORDER BY backMark DESC,routeid";
			$res = $conn -> query($sql);
			if ($res -> num_rows == TRUE) {
				$i = 0;
				while ($row = $res -> fetch_assoc()) {
					$arr[$i]['modid'] = $row['modid'];
					$arr[$i]['figure_number'] = $row['figure_number'];
					//零件图号
					$arr[$i]['name'] = $row['name'];
					//名称
					$arr[$i]['standard'] = $row['standard'];
					//开料尺寸
					$arr[$i]['route'] = $row['route'];
					//加工工艺路线
					$arr[$i]['count'] = $row['count'];
					//数量
					$arr[$i]['child_material'] = $row['child_material'];
					//规格
					$number = explode("#", $row['number']);
					$arr[$i]['number'] = $number[0] . "#";
					//工单
					$arr[$i]['product_name'] = $number[1] . $row['product_name'];
					//产品名称
					$arr[$i]['remark'] = $row['remark'];
					$arr[$i]['routeid'] = $row['routeid'];
					if ($row['backMark'] == "1") {
						$arr[$i]['backMark'] = "是";
					} else {
						$arr[$i]['backMark'] = "否";
					}
	
					$arr[$i]['reason'] = $row['reason'];
					$i++;
				}
			}
			// 未排产
			if($arr!=''){
				$list_data = json_encode($arr);
				$json = '{"success":"true","rows":' . $list_data . '}';
			}else{
				$list_data = json_encode($arr);
				$json = '{"success":"error","rows":' . $list_data . '}';
			}
		} else if ($isfinish == '0') {
			
			$arr4 = '';
			// 已排产数据列表
			$sql4 = "SELECT id,modid,fid,figure_number,name,standard,count,child_material,remark,routeid,product_name,number,station,schedule_date,route FROM Delivered WHERE Wisfinish = 0  and $searchCondition LIKE '%$searchValue%'";
			$res4 = $conn -> query($sql4);
			if ($res4 -> num_rows > 0) {
				$i = 0;
				while ($row4 = $res4 -> fetch_assoc()) {
					$arr4[$i]['partid'] = $row4['id'];
					$arr4[$i]['fid'] = $row4['fid'];
					$arr4[$i]['modid'] = $row4['modid'];
					$arr4[$i]['routeid'] = $row4['routeid'];
					$arr4[$i]['figure_number'] = $row4['figure_number'];
					$arr4[$i]['name'] = $row4['name'];
					$arr4[$i]['standard'] = $row4['standard'];
					$arr4[$i]['count'] = $row4['count'];
					$arr4[$i]['route'] = $row4['route'];
					$arr4[$i]['child_material'] = $row4['child_material'];
					$number4 = explode("#", $row4['number']);
					$arr4[$i]['number'] = $number4[0] . "#";
					$arr4[$i]['product_name'] = $number4[1] . $row4['product_name'];
					$arr4[$i]['remark'] = $row4['remark'];
					$arr4[$i]['station'] = $row4['station'];
					$arr4[$i]['schedule_date'] = $row4['schedule_date'];
					$i++;
				}
			}
			// 已排产
			if($arr4!=''){
				$list_data2 = json_encode($arr4);
				$json = '{"success":"true","rows2":'.$list_data2.'}';
			}else{
				$list_data2 = json_encode($arr4);
				$json = '{"success":"error","rows2":'.$list_data2.'}';
			}
			
		} else {
			
			$arr7 = '';	
			//生产中数据列表
			$sql7 = "Select * from projectIng   and $searchCondition LIKE '%$searchValue%'";
			$res7 = $conn -> query($sql7);
			if ($res7 -> num_rows > 0) {
				$i = 0;
				while ($row7 = $res7 -> fetch_assoc()) {
					$arr7[$i]['partid'] = $row7['id'];
					$arr7[$i]['fid'] = $row7['fid'];
					$arr7[$i]['modid'] = $row7['modid'];
					$arr7[$i]['figure_number'] = $row7['figure_number'];
					$arr7[$i]['name'] = $row7['name'];
					$arr7[$i]['standard'] = $row7['standard'];
					$arr7[$i]['count'] = $row7['count'];
					$arr7[$i]['child_material'] = $row7['child_material'];
					$number7 = explode("#", $row7['number']);
					$arr7[$i]['number'] = $number7[0] . "#";
					$arr7[$i]['product_name'] = $number7[1] . $row7['product_name'];
					$arr7[$i]['remark'] = $row7['remark'];
					$arr7[$i]['station'] = $row7['station'];
					$arr7[$i]['schedule_date'] = $row7['schedule_date'];
					$i++;
				}
			}
			// 生产中
			if($arr7!=''){
				$list_data3 = json_encode($arr7);
				$json = '{"success":"true","rows3":'.$list_data3.'}';
			}else{
				$list_data3 = json_encode($arr7);
				$json = '{"success":"error","rows3":'.$list_data3.'}';
			}
		}
	}
//	echo $sql;
	echo $json;
	$conn -> close();
?>