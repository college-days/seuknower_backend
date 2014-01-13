<?php
// 本类由系统自动生成，仅供测试用途
class UserAction extends Action {
	public function _initialize(){
		if(isset($_SESSION['userId'])){	
		}
		else{
			//password is md5 value
			$account = cookie('account');
			$password = cookie('password');
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
				//是否是社团或者组织
				if($result['is_group']){
					session('isGroup',1);
				}
				else{
					session('isGroup',0);
				}
			}
		}
	}

    public function index(){
    	$u_id = I('param.id');
    	$User = M('User');
    	$userInfo = $User->find($u_id);
    	$this->assign('user', $userInfo);
    	$this->display('user_center');
    }
}
?>