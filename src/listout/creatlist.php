<?php
	require("../../conn.php");
	header("Access-Control-Allow-Origin: *"); // 允许任意域名发起的跨域请求
	$ret_data = '';
	$flag=$_POST['flag'];
	// $flag='update';
	$ret_data["state"] = 'error';
	switch($flag){
		//编辑清单
		case "update":
		$id=$_POST['id'];
		$listname=$_POST['listname'];
		$description=$_POST['description'];
		$sql_1="SELECT * FROM list WHERE id='".$id."'";
		//为了保证清单名称是唯一的
		//编辑时分不更改和更改的情况,若更改是要检验更改后的结果表是否有
		$res_1=$conn->query($sql_1);
		if($res_1->num_rows>0){
			while($row = $res_1->fetch_assoc()){
				//第一种情况不更改任何东西
				 if($listname==$row["listname"]&&$description==$row["description"]){
					 //没有更改
					 $ret_data["state"] = 'nothing';
				 }
				 //第二种情况是名字不改,说明改
				 else if($listname== $row["listname"]&&$description!=$row["description"]){
					 $sql="UPDATE list SET listname='".$listname."',description='".$description."' WHERE id='".$id."'";
					 $res = $conn->query($sql);
					 if($res){
						$ret_data["state"] = 'success';
					 }
				}
				//其他
				else{
					$sql_have="SELECT * FROM list WHERE listname='".$listname."'";
					 $res_have=$conn->query($sql_have);
					 if($res_have->num_rows>0){
						$ret_data["state"] = 'exist';
					 }
					 else{
						 $sql="UPDATE list SET listname='".$listname."',description='".$description."' WHERE id='".$id."'";
						 $sql_out="UPDATE listout SET listname='".$listname."' WHERE listname='".$row["listname"]."'";
						 $res = $conn->query($sql);
						 $res_out = $conn->query($sql_out);
						 if($res&&$res_out){
							$ret_data["state"] = 'success';
						}
					}
				}
		    }
		}
		$json=json_encode($ret_data);
		echo $json;
		break;
		//新建清单
		case "new":
		$listname=$_POST['listname'];
		$description=$_POST['description'];
		$sql_have="SELECT * FROM list WHERE listname='".$listname."'";
		$res_have=$conn->query($sql_have);
		if($res_have->num_rows>0){
			$ret_data["exist"] = 'yes';
		}
		else{
			$sql = "INSERT INTO list(listname,description) VALUES ('".$listname."','".$description."')";
			$res = $conn->query($sql);
			if($res){
				$ret_data["state"] = 'success';
			}
		}
		$json=json_encode($ret_data);
		echo $json;
		break;
		default :
		echo '{"state":"error","message":"没有对应的标志"}';
	}
	$conn->close();
?>