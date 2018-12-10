<?php
	require("../conn.php");
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
	$name=$_POST["name"];
	$utime = time();
	$account=$_POST["account"];
	$postword=$_POST["postword"];
	$sql = "select account from user where account='$account'";
	$res=$conn->query($sql);
	if($res->num_rows>0){
		echo "该用户已经存在";
	}else{
		$sql1 = "INSERT INTO `user`(account,password,name,ctime) VALUES('$account','$postword','$name','$utime')";
		$res1=$conn->query($sql1);
		if($res1->num_rows>0){
			echo "添加用户成功";
		}else{
			echo "添加失败";
		}
	}
?>