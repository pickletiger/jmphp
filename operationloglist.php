<?php
 require("conn.php");
 $account=$_GET["account"];
   $sqldata='';
   $sql="SELECT * FROM operation_loglist WHERE `account`='".$account."'";
   $result = $conn->query($sql);
   while ($row = $result->fetch_assoc()) {
			$sqldata=$sqldata.'{
		"id":"'.$row["id"].'",
		"account":"'.$row["account"].'",
		"content":"'.$row["content"].'",
		"data":"'.$row["data"].'",
		"time":"'.$row["time"].'",
		"man":"'.$row["humanname"].'"
	},';
		}
   $jsonresult = 'true';
	$otherdate = '{"success":"'.$jsonresult.'"
			      }';
	$json = '['.$sqldata.$otherdate.']';
echo $json;
?>