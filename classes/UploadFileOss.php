<?php
require_once '../oss/sdk.class.php';
require_once '../oss/util/oss_util.class.php';


class UploadFileOss{
	protected $fileinfo;
	protected $access_id;
	protected $access_key;
	protected $end_point;
	protected $client;
	protected $bucket_name;
	public $error;
	
	public function __construct($fileinfo){
		$this->fileinfo = $fileinfo;
		
		$this->access_id = "LTAIIkOiJmMiAZ3V";
		$this->access_key = "NsVQ9HgzGuDXcHz0buas4lWzotCw9G";
		$this->end_point = "oss-cn-shenzhen.aliyuncs.com";
		$this->client = new ALIOSS($this->access_id,$this->access_key,$this->end_point);
		$this->bucket_name = "jmmes";
		$this->client->set_enable_domain_style(true);		
	}	
	
	//检查上传文件的的错误码
	protected function checkError(){
		if(!is_null($this->fileinfo)){
			if($this->fileinfo['error']>0){
				switch($this->fileinfo['error']){
					case 1:
						$this->error='超过了PHP配置文件中upload_max_filesize选项的值';
						break;
					case 2:
						$this->error='超过了表单中MAX_FILE_SIZE设置的值';
						break;
					case 3:
						$this->error='文件部分被上传';
						break;
					case 4:
						$this->error='没有选择上传文件';
						break;
					case 6:
						$this->error='没有找到临时目录';
						break;
					case 7:
						$this->error='文件不可写';
						break;
					case 8:
						$this->error='由于PHP的扩展程序中断文件上传';
						break;
						
				}
				return false;
			}else{
				return true;
			}
		}else{
			$this->error='文件上传出错';
			return false;
		}
	}

	/**
	 * 检测是否通过HTTP POST方式上传上来的
	 * @return boolean
	 */
	protected function checkHTTPPost(){
		if(!is_uploaded_file($this->fileinfo['tmp_name'])){
			$this->error='文件不是通过HTTP POST方式上传上来的';
			return false;
		}
		return true;
	}
	//对象转数组
	function objectToArray($e){
	    $e=(array)$e;
	    foreach($e as $k=>$v){
	        if( gettype($v)=='resource' ) return;
	        if( gettype($v)=='object' || gettype($v)=='array' )
	            $e[$k]=(array)$this->objectToArray($v);
	    }
	    return $e;
	}
	
	public function uploadpart(){
		$file_path = $this->fileinfo["tmp_name"];//要上传文件的路径
		$object_name = 'partUpload/'.$this->fileinfo["name"];//上传到oss的文件路径
		$options = array(
			ALIOSS::OSS_HEADERS => array(
				'Expires' => '',
				'Content-Type'=>$this->fileinfo["type"],
				'x-oss-server-side-encryption' => 'AES256',
			),
		);
		$response = $this->client->upload_file_by_file($this->bucket_name,$object_name,$file_path,$options);
		$a = $this->objectToArray($response);
		echo $a["header"]["_info"]["url"];
	}
	
	public function useclass(){
		if($this->checkHTTPPost() && $this->checkError()){
			$this->uploadpart();
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
}


?>