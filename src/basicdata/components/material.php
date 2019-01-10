<?php
    require("../../../conn.php");
    $ret_data = '';
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
    $sql = "SELECT id,material_num,material_name,specifications,amount from material";
    $res = $conn->query($sql);
    if($res->num_rows>0){
        $i = 0;
        $ret_data["success"]= 'success';
        while($row = $res->fetch_assoc()){
            $ret_data["data"][$i]["id"] = $row["id"];
            $ret_data["data"][$i]["mNum"] = $row["material_num"];//物料编码
            $ret_data["data"][$i]["name"] = $row["material_name"];//物料名称
            $ret_data["data"][$i]["specifications"] = $row["specifications"];//规格
            $ret_data["data"][$i]["amount"] = $row["amount"];//数量
            $i++;
        }
    }
    $conn->close();
    $json = json_encode($ret_data);
	echo $json;
?>