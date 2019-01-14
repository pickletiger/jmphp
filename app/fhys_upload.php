<?php
require ("../conn.php");
$sjc = $_POST["sjc"];
//$xmid = $_POST["xmid"];
//$gclb = $_POST["gclb"];
//$mc = $_POST["mc"];
//$sclb = $_POST["sclb"]; 
//$checkId = $_POST["checkId"];
$filenames1 = explode( "~*^*~",$filenames);
$sql = "select testphoto from workshop_k where testphototime = '".$sjc."' ";
$result = $conn->query($sql);
$count = mysqli_num_rows($result);
if($count){
	for ($i = 1; $i < count($filenames1); $i++) {
        $sql2 = "UPDATE `workshop_k` SET `testphoto` = '$filenames1[$i]' WHERE `testphototime` = '" . $sjc . "'";
        if ($conn -> query($sql2) === TRUE) { 
	      	$jsonresult = 'success';
  	    } else {
		    $jsonresult = 'error';
		}
    }
}
else{
	for ($i = 1; $i < count($filenames1); $i++) {
	    $sql3 = "INSERT INTO `workshop_k` (`testphoto`,`testphototime`) VALUE ('$filenames1[$i]','$sjc')";
	    if ($conn -> query($sql3) === TRUE) { 
	      	$jsonresult = 'success';
  	    } else {
		    $jsonresult = 'error';
		}
	}
}


$json = '{"result":"' . $jsonresult . '"		
			}';
echo $json;
//echo $filenames1[1];
$conn -> close();
?>