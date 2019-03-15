<?php
	require("../conn.php");
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
	class Alteration{  
	    public $name;  
	    public $value;  
	}  
	$data = array();
	$sql = "select notNum,count(name) as count from workshop_k where notNum != '0' group by notNum ";
	$res=$conn->query($sql);
	if($res->num_rows>0){
		$i=0;
		while($row=$res->fetch_assoc()){
			$alter = new Alteration();
			$alter->name = $row['notNum']."次";
			$alter->value = intval($row['count']);  
			$data[] = $alter;
		}
	}
	echo json_encode($data);
?>