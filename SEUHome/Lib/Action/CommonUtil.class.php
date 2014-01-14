<?php
class CommonUtil{
	public function exists_file($filepath){
		$path = "E:/wamp/www";
		$dest = $path.$filepath;
		return file_exists($dest);
	}

	public function sub_string($string, $length){
		if(mb_strlen($string, 'utf-8') > $length){
			return mb_substr($string, 0, $length-1, 'utf-8')."...";
		}else{
			return $string;
		}
	}

	public function filter_sex($sex){
		if($sex == '男'){
			return "male";
		}else if($sex == '女'){
			return "female";
		}else{
			return "none";
		}
	}
}
?>