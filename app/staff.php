<?php
	require("../conn.php");
	$id = $_POST["id"];
	$sql = "select name from user where id='".$id."' ";
	$res = $conn->query($sql);
	if($res -> num_rows > 0) {
		$i = 0;
		while($row = $res->fetch_assoc()) {
			$arr[$i]['name'] = $row['name'];
			$i++;
		}
	}
	$json = json_encode($arr);
	echo $json;
	$conn->close();
?>