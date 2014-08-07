<?php
class CommonUtil{
	public function autologin(){
		//var_dump($_SESSION);
		//var_dump($_COOKIE);
		if(isset($_SESSION['userId'])){	
		}
		else{
			$account = cookie('account');
			$password = cookie('password');
			//每登入一次再延长cookie的有效时间
			if(!empty($account) && !empty($password)){
				$User = M('User');
				$map['account'] = $account;
				$map['password'] = $password;
				$result = $User->where($map)->find();
				cookie('account',$result['account'],864000);
				cookie('password',$result['pwd'],864000);
				session('userId',$result['id']);
				session('account',$result['account']);
				session('userName',$result['name']);
				session('icon',$result['icon']);
				if($result['is_group']){
					session('isGroup',1);
				}
				else{
					session('isGroup',0);
				}
			}
		}
	}

	public function exists_file($filepath){
		$path = "D:/Program Files/Apache Software Foundation/Apache2.2/htdocs";
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