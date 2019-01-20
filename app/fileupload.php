<?php
require ("../conn.php");
require ("../classes/UploadFileOss.php");

$flag = $_POST["flag"];
//$flag = "0";

switch ($flag) {
	case '0' :
		$nub = isset($_POST["nub"]) ? $_POST["nub"] : "";
		$modid = isset($_POST["modid"]) ? $_POST["modid"] : "";
		$pid = isset($_POST["pid"]) ? $_POST["pid"] : "";
		print_r($_FILES);

		for ($i = 0; $i < $nub; $i++) {
			$index = $i + 1;
			$upfile = $_FILES["upfile" . $index];
			//上传文件名
			$filepathsave = $upfile["name"];
			$uploadfileoss = new UploadFileOss($upfile);
			if ($uploadfileoss -> useclass()) {
				$sql = "select part_url from part where fid = '" . $pid . "' AND modid = '" . $modid . "' ";
				$result = $conn -> query($sql);
				$row = $result -> fetch_assoc();
				if (strlen($row["part_url"]) > 0) {
					$filepathsave1 = $row["part_url"] . "," . $filepathsave;
					//上传照片url
					$sql1 = "UPDATE part SET part_url = '" . $filepathsave1 . "' WHERE fid = '" . $pid . "' AND modid = '" . $modid . "' ";
					$result = $conn -> query($sql1);
				} else {
					//上传照片url
					$sql2 = "UPDATE part SET part_url = '" . $filepathsave . "' WHERE fid = '" . $pid . "' AND modid = '" . $modid . "' ";
					$result = $conn -> query($sql2);
				}

			} else {
				$returndata["msg"] .= $filepathsave . "上传失败！";
			}
		}

		break;
	case '1' :
		$nub = isset($_POST["nub"]) ? $_POST["nub"] : "";
		$modid = isset($_POST["modid"]) ? $_POST["modid"] : "";
		$wid = isset($_POST["wid"]) ? $_POST["wid"] : "";
		$pid = isset($_POST["pid"]) ? $_POST["pid"] : "";
		$routeid = isset($_POST["routeid"]) ? $_POST["routeid"] : "";
		print_r($_FILES);

		$returndata = array("state" => "1", "msg" => "");

		for ($i = 0; $i < $nub; $i++) {
			$index = $i + 1;
			$upfile = $_FILES["upfile" . $index];
			$filepathsave = $upfile["name"];
			$uploadfileoss = new UploadFileOss($upfile);
			if ($uploadfileoss -> useclass()) {
				//检验照片
				$sql = "select photourl from workshop_k where id = '" . $wid . "' ";
				$result = $conn -> query($sql);
				$row = $result -> fetch_assoc();
				if (strlen($row["photourl"]) > 0) {
					$filepathsave1 = $row["photourl"] . "," . $filepathsave;
					//上传照片url
					$sql1 = "UPDATE workshop_k SET photourl = '" . $filepathsave1 . "' WHERE id = '" . $wid . "' ";
					$result = $conn -> query($sql1);
					//同步part表
					$sql2 = "select part_url from part where fid = '" . $pid . "' AND modid = '" . $modid . "' ";
					$result = $conn -> query($sql2);
					$row = $result -> fetch_assoc();
					$filepathsave2 = $row["part_url"] . "," . $filepathsave;
					$sql3 = "UPDATE part SET part_url = '" . $filepathsave2 . "' WHERE fid = '" . $pid . "' AND modid = '" . $modid . "' ";
					$result = $conn -> query($sql3);
				} else {
					//上传照片url
					$sql = "UPDATE workshop_k SET photourl = '" . $filepathsave . "' WHERE id = '" . $wid . "' ";
					$result = $conn -> query($sql);
					//同步part表
					$sql1 = "select part_url from part where fid = '" . $pid . "' AND modid = '" . $modid . "' ";
					$result = $conn -> query($sql1);
					$row = $result -> fetch_assoc();
					$filepathsave2 = $row["part_url"] . "," . $filepathsave;
					$sql2 = "UPDATE part SET part_url = '" . $filepathsave2 . "' WHERE fid = '" . $pid . "' AND modid = '" . $modid . "' ";
					$result = $conn -> query($sql2);
				}

			} else {
				$returndata["msg"] .= $filepathsave . "上传失败！";
			}
		}
		break;
}
$conn -> close();
?>
