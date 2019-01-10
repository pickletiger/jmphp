<?php
 require("conn.php");
 $sqldata='';
 $sql="SELECT * FROM `working_condition` ORDER BY `classnumber`";
 $result = $conn->query($sql);
 while ($row = $result->fetch_assoc()) {
			$sqldata=$sqldata.'{
				"id":"'.$row["id"].'",
				"Serial":"'.$row["classnumber"].'",
				"name":"'.$row["partname"].'",
				"type":"'.$row["producttype"].'",
				"time":"'.$row["finallytime"].'",
				"schedule":"'.$row["progress"].'",
				"Remarks":"'.$row["remarks"].'"
			},';
		}
 $jsonresult = 'true';
$otherdate = '{"success":"'.$jsonresult.'"
		      }';
$json = '['.$sqldata.$otherdate.']';
echo $json;
?>