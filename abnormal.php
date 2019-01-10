<?php
 require("conn.php");
 $sqldata='';
 $sql="SELECT * FROM `abnormal` ORDER BY `classnumber`";
 $result = $conn->query($sql);
 while ($row = $result->fetch_assoc()) {
			$sqldata=$sqldata.'{
				"id":"'.$row["id"].'",
				"type":"'.$row["type"].'",
				"name":"'.$row["partname"].'",
				"Serial":"'.$row["classnumber"].'",
				"time":"'.$row["reworktime"].'",
				"Remarks":"'.$row["remarks"].'"
			},';
		}
 $jsonresult = 'true';
$otherdate = '{"success":"'.$jsonresult.'"
		      }';
$json = '['.$sqldata.$otherdate.']';
echo $json;
?>