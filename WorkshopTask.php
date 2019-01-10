<?php
 require("conn.php");
 $sqldata='';
 $sql="SELECT * from `workshop_task`";
 $result = $conn->query($sql);
 while ($row = $result->fetch_assoc()) {
			$sqldata=$sqldata.'{
				"id":"'.$row["id"].'",
				"Serial":"'.$row["shopnumber"].'",
				"name":"'.$row["workname"].'",
				"Group":"'.$row["classnumber"].'",
				"bzzname":"'.$row["monitorname"].'",
				"time":"'.$row["plantime"].'",
				"finished":"'.$row["finally"].'",
				"Remarks":"'.$row["remarks"].'"
			},';
		}
 $jsonresult = 'true';
$otherdate = '{"success":"'.$jsonresult.'"
		      }';
$json = '['.$sqldata.$otherdate.']';
echo $json;
?>