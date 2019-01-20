<?php
require ("../conn.php");
//require ("fileupload.php");
//$id = $_POST["id"];
//$modid = $_POST["modid"];
//$routeid = $_POST["routeid"];
$id = "340";
$modid = "1000479741";
$routeid = "1398";
$url = $destination;
$filenames1 = explode( ",",$filenames);
$sql = "select testphoto from workshop_k where id = '".$id."' ";
$result = $conn->query($sql);
$count = mysqli_num_rows($result);
if($count){
	for ($i = 1; $i < count($filenames1); $i++) {
        $sql2 = "UPDATE `workshop_k` SET `testphoto` = '$filenames1[$i]' WHERE `id` = '" . $id . "'";
        if ($conn -> query($sql2) === TRUE) { 
	      	$jsonresult = 'success';
  	    } else {
		    $jsonresult = 'error';
		}
    }
}
else{
	for ($i = 1; $i < count($filenames1); $i++) {
	    $sql3 = "INSERT INTO `workshop_k` (`testphoto`,) VALUE ('$filenames1[$i]')";
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