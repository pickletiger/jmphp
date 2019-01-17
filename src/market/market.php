<?php
	require("../../conn.php");
    $ret_data = '';
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
    $sql = "SELECT id,ordernumber,importperson,importtime,checkperson,pdf from audit_order";
    $res = $conn->query($sql);
    if($res->num_rows>0){
        $i = 0;
        $ret_data["success"]= 'success';
        while($row = $res->fetch_assoc()){
            $ret_data["data"][$i]["id"] = $row["id"];
            $ret_data["data"][$i]["orderName"] = $row["ordernumber"];//订单编号
            $ret_data["data"][$i]["importPerson"] = $row["importperson"];//导入人员
            $ret_data["data"][$i]["importTime"] = $row["importtime"];//导入时间
            $ret_data["data"][$i]["checkPerson"] = $row["checkperson"];//审核人
            $i++;
        }
    }
    $conn->close();
    $json = json_encode($ret_data);
	echo $json;
?>