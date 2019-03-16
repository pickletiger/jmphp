<?php
	//测试云平台交互的JSON数据格式
	header("Access-Control-Allow-Origin: *");
	class Test{
		public $conn;
		public $projectData = array();
		public $countNum = 0;
				
		public function __construct($conn){
			$this->conn = $conn;
		}
		
		//第一级树(project)
		public function main(){
			//1、查询project的信息
			$sql = "SELECT `id`,`modid`,`name`,`type`,`number`,end_date FROM `project`";
			$result = $this->conn->query($sql);
			if($result->num_rows>0){
				$i = 0;
				while($row = $result->fetch_assoc()){
					$this->projectData[$i] = $row;
										
					//2、根据project的id（=》fid）与name和number的#号后的组合（name+number[1]）作为第二级
					$number_array = explode("#", $row["number"]);
					$belong_part = $row["name"].end($number_array);
					$this->projectData[$i]["part"] = $this->getMpart($row["id"],$belong_part);
					
					$i++;
				}
			}
		}		
		
		//第二级树(mpart)
		public function getMpart($fid,$belong_part){
			$mpartData = array();
			$sql = "SELECT id,name,modid,figure_number,standard,count,radio,remark,part_url FROM part  WHERE fid = '".$fid."' AND (belong_part='' OR belong_part='".$belong_part."')";
			$result = $this->conn->query($sql);
			if($result->num_rows>0){
				$i = 0;
				while($row = $result->fetch_assoc()){
					$arr = array();
					$arr=explode(',',$row["part_url"]);
					$base = "http://jmmes.oss-cn-shenzhen.aliyuncs.com/partUpload/";
					foreach($arr as $key => $url){
						$arr[$key] = $base .$url;
					}		
					
					$mpartData[$i] = $row;
					$mpartData[$i]["part_url"] = $arr;
					
					//3、根据二级树part的fid(=>fid) 与 [figure_number.'&'.name;](=>belong_part)
					$belong_part_3_1 = $row["name"];
					$belong_part_3_2 = $row["figure_number"]."&".$row["name"];
					$retData = $this->getPart_3($fid,$belong_part_3_1,$belong_part_3_2);
					if(count($retData) > 0){
						$mpartData[$i]["part"] = $retData;
					}				
					
					
					$i++;																		
				}				 
			}
			return $mpartData;
		}
		
		//第三级树或之后(part)
		public function getPart_3($fid,$belong_part_3_1,$belong_part_3_2){
			$partData_3 = array();
			$sql = "SELECT id,name,modid,figure_number,standard,count,radio,remark,part_url FROM part  WHERE fid = '".$fid."' AND belong_part='".$belong_part_3_1."'";			
			$result = $this->conn->query($sql);
			if($result->num_rows>0){
//				echo $sql."<br /></hr/>";
				$i = 0;
				while($row = $result->fetch_assoc()){
					$arr = array();
					$arr=explode(',',$row["part_url"]);
					$base = "http://jmmes.oss-cn-shenzhen.aliyuncs.com/partUpload/";
					foreach($arr as $key => $url){
						$arr[$key] = $base .$url;
					}		
					
					$partData_3[$i] = $row;
					$partData_3[$i]["part_url"] = $arr;
//					4、根据三级树part的fid(=>fid) 与 [figure_number.'&'.name;](=>belong_part)
					$belong_part_more_1 = $row["name"];
					$belong_part_more_2 = $row["figure_number"]."&".$row["name"];
					$retData = $this->getPart_3($fid,$belong_part_more_1,$belong_part_more_2);
					if(count($retData) > 0){
						$partData_3[$i]["part"] = $this->getPart_3($fid,$belong_part_more_1,$belong_part_more_2);
					} 					
					
					
					$i++;																		
				}
				return $partData_3;				 
			}else{
				$sql = "SELECT id,name,modid,figure_number,standard,count,radio,remark,part_url FROM part  WHERE fid = '".$fid."' AND belong_part='".$belong_part_3_2."'";
				$result = $this->conn->query($sql);
				if($result->num_rows>0){
//					echo $sql."<br /></hr/>";
					$i = 0;
					while($row = $result->fetch_assoc()){
						$arr = array();
						$arr=explode(',',$row["part_url"]);
						$base = "http://jmmes.oss-cn-shenzhen.aliyuncs.com/partUpload/";
						foreach($arr as $key => $url){
							$arr[$key] = $base .$url;
						}		
						
						$partData_3[$i] = $row;
						$partData_3[$i]["part_url"] = $arr;
						
						//4、根据三级树part的fid(=>fid) 与 [figure_number.'&'.name;](=>belong_part)
						$belong_part_more_1 = $row["name"];
						$belong_part_more_2 = $row["figure_number"]."&".$row["name"];					
						$retData = $this->getPart_3($fid,$belong_part_more_1,$belong_part_more_2);
						if(count($retData) > 0){
							$partData_3[$i]["part"] = $this->getPart_3($fid,$belong_part_more_1,$belong_part_more_2);
						}
						
						$i++;																		
					}
					return $partData_3;				 
				}else{
					return $partData_3;
				}
			}
			
		}		
	}

//	header("charset:utf-8");
//	header("content-type:application/json");
	set_time_limit(0);//设置脚本最大执行时间
	ini_set('memory_limit','40960M');
//	require("../conn.php");
	$servername = "192.168.0.133:3306"; //将本地当做服务器，端口默认3306
	$username = "admin";  //连接对象
	$password = "123456";  //连接密码
	
//	$servername = "127.0.0.1:3306"; //将本地当做服务器，端口默认3306
//	$username = "root";  //连接对象
//	$password = "123456";  //连接密码
	$dbname = "jmmes";	 //数据库名称
	$conn = new mysqli($servername, $username, $password, $dbname);	
	if ($conn->connect_error) {
		die("Connection failed: " . $conn->connect_error);
	}else{
		// echo "Connected successfully";
	}
	
	$test = new Test($conn);
	$test->main();
	$json = json_encode($test->projectData,JSON_UNESCAPED_UNICODE);
//	echo $json;
//	$testData = $test->getPart_3(2,"吊钩组件","FY-48B.05.01-00&吊钩组件");
//	print_r($testData);

//	生成json文件
	$myfile = fopen("newfile0.json", "w") or die("Unable to open file!");
	fwrite($myfile, $json);
	fclose($myfile);

	
?>