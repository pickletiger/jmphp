<?php
    require("../../../conn.php");
    $ret_data = '';
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
    $sql = "select id,name,job,gNum,phone_number,department,terminal from user";
    $res = $conn->query($sql);
    if($res->num_rows>0){
        $i = 0;
        $ret_data["success"]= 'success';
        while($row = $res->fetch_assoc()){
            $ret_data["data"][$i]["id"] = $row["id"];
            $ret_data["data"][$i]["name"] = $row["name"];
            $ret_data["data"][$i]["gNum"] = $row["gNum"];
            $ret_data["data"][$i]["phone"] = $row["phone_number"];
            $ret_data["data"][$i]["position"] = $row["job"];
            $ret_data["data"][$i]["department"] = $row["department"];
            $i++;
        }
    }
    $conn->close();
    $json = json_encode($ret_data);
	echo $json;
?>