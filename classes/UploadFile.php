<?php 
/*
 * 上传文件保存
 * */


class UploadFile{
//	protected $fileName;//
//	protected $maxSize;//文件大小限制
//	protected $allowMime;//允许的文件类型
//	protected $allowExt;//文件后缀限制
	protected $uploadPath;//保存的路径
//	protected $imgFlag;//是否为真实图片
	protected $fileInfo;//文件主体 
	protected $error;
	protected $ext;
	protected $fileSaveName;
	/**
	 * @param string $fileName
	 * @param string $uploadPath
	 * @param string $imgFlag
	 * @param number $maxSize
	 * @param array $allowExt
	 * @param array $allowMime
	 */
//	public function __construct($fileName='myFile',$uploadPath='./uploads',$imgFlag=true,$maxSize=5242880,$allowExt=array('jpeg','jpg','png','gif'),$allowMime=array('image/jpeg','image/png','image/gif')){
	public function __construct($fileinfo,$uploadPath='../uploadfiles',$fileSaveName){
//		$this->fileName=$fileName;
//		$this->maxSize=$maxSize;
//		$this->allowMime=$allowMime;
//		$this->allowExt=$allowExt;
		$this->uploadPath=$uploadPath;
//		$this->imgFlag=$imgFlag;
		
		$this->fileInfo=$fileinfo;
		$this->fileSaveName = $fileSaveName;
	}
	/**
	 * 检测上传文件是否出错
	 * @return boolean
	 */
	protected function checkError(){
		if(!is_null($this->fileInfo)){
			if($this->fileInfo['error']>0){
				switch($this->fileInfo['error']){
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
	 * 检测上传文件的大小
	 * @return boolean
	 */
	protected function checkSize(){
		if($this->fileInfo['size']>$this->maxSize){
			$this->error='上传文件过大';
			return false;
		}
		return true;
	}
	/**
	 * 检测扩展名
	 * @return boolean
	 */
	protected function checkExt(){
		$this->ext=strtolower(pathinfo($this->fileInfo['name'],PATHINFO_EXTENSION));
		if(!in_array($this->ext,$this->allowExt)){
			$this->error='不允许的扩展名';
			return false;
		}
		return true;
	}
	/**
	 * 检测文件的类型
	 * @return boolean
	 */
	protected function checkMime(){
		if(!in_array($this->fileInfo['type'],$this->allowMime)){
			$this->error='不允许的文件类型';
			return false;
		}
		return true;
	}
	/**
	 * 检测是否是真实图片
	 * @return boolean
	 */
	protected function checkTrueImg(){
		if($this->imgFlag){
			if(!@getimagesize($this->fileInfo['tmp_name'])){
				$this->error='不是真实图片';
				return false;
			}
			return true;
		}
	}
	/**
	 * 检测是否通过HTTP POST方式上传上来的
	 * @return boolean
	 */
	protected function checkHTTPPost(){
		if(!is_uploaded_file($this->fileInfo['tmp_name'])){
			$this->error='文件不是通过HTTP POST方式上传上来的';
			return false;
		}
		return true;
	}
	/**
	 *显示错误 
	 */
	protected function showError(){
		exit('<span style="color:red">'.$this->error.'</span>');
	}
	/**
	 * 检测目录不存在则创建
	 */
	protected function checkUploadPath(){
		$uploadpath_gbk = iconv("UTF-8", "GB2312", $this->uploadPath);
		if(!file_exists($uploadpath_gbk)){
			mkdir($uploadpath_gbk,0777,true);
		}
	}
	/**
	 * 产生唯一字符串
	 * @return string
	 */
	protected function getUniName(){
		return md5(uniqid(microtime(true),true));
	}
	/**
	 * 上传文件
	 * @return string
	 */
	public function uploadFile(){
//		if($this->checkError()&&$this->checkSize()&&$this->checkExt()&&$this->checkMime()&&$this->checkTrueImg()&&$this->checkHTTPPost()){
		if($this->checkError()){
			$this->checkUploadPath();//检测保存目录是否存在
			$this->ext = strtolower(pathinfo($this->fileInfo['name'],PATHINFO_EXTENSION));//获取文件后缀
			if(isset($this->fileSaveName)){
				$this->destination = $this->uploadPath.'/'.$this->fileSaveName.'.'.$this->ext;
				$this->destination_gbk = $this->uploadPath.'/'.$this->fileSaveName.'.'.$this->ext;				
			}else{
				$this->uniName = pathinfo($this->fileInfo['name'],PATHINFO_FILENAME);//重新命名问价
				$this->destination = $this->uploadPath.'/'.$this->uniName.'.'.$this->ext;//组建完整问价路径
				$this->destination_gbk = iconv("UTF-8", "GB2312", $this->destination);
			}				
			
			if(@move_uploaded_file($this->fileInfo['tmp_name'], $this->destination_gbk)){
				return  $this->destination;//返回文件完整目录
			}else{
				$this->error='文件移动失败';
				$this->showError();
			}
		}else{
			$this->showError();
		}
	}
}



