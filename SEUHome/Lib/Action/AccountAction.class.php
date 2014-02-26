<?php
import("@.Action.CommonUtil");
class AccountAction extends Action{
	public function _initialize(){
		$util = new CommonUtil();
		$util->autologin();
	}

	public function login() {
		$this->display('login');
	}

	// 登录检测
	public function checkLogin() {
		$account = I('param.account');
		$password = I('param.password');
		$rememberme = I('param.rememberme');
		if(empty($account)) {
			$this->error('帐号错误!');
		}elseif (empty($password)){
			$this->error('密码必须!');
		}
		$User = M('User');
        $map['account'] = $account;
		$result = $User->where($map)->find();
		if($result === false){
			$this ->ajaxReturn('', '查询数据库出错!', 0);
		}
		elseif($result === null){
			$this ->ajaxReturn('', '帐号错误!', 0);
		}
		elseif($result['status'] == 0){
			$this ->ajaxReturn('', '帐号未激活!', 0);
		}
		elseif($result['pwd'] != md5($password)){
			$this ->ajaxReturn('', '密码错误!', 0);
		}
		else{
			//将用户ID存入session

			if($rememberme){
				if(!$result['is_group']){
					cookie('account',$result['account'],864000);
					cookie('password',$result['pwd'],864000);
				}
				else{
					cookie('group_account',$result['account'],864000);
					cookie('group_password',$result['pwd'],864000);
				}
			}

			session('rememberme', $rememberme);
			session('userId',$result['id']);
			session('account',$result['account']);
			session('userName',$result['name']);
			session('icon',$result['icon']);

			//查询是否有新的消息，并放入session中便于header_b.html中的元素使用
			/*$model = new Model();
			$messageResult = $model->query('select * from seu_question_message where u_id='.$result['id']);
			$messageCount = count($messageResult);
			session('messageCount', $messageCount);
			session('messageResult', $messageResult);*/

			if($result['is_group']){
				session('isGroup',1);
			}
			else{
				session('isGroup',0);
			}
			$this ->ajaxReturn('', '', 1);
		}
	}

	public function logout()
    {
		$id = session('userId');
        if(isset($id)) {
			unset($_SESSION);
			session(null);
			session_destroy();
			cookie('account',null);
			cookie('password',null);
			$this -> redirect('/index');
        }
    }

    public function verifycode(){
    	import('ORG.Util.Image');
	    Image::buildImageVerify();
    }

    public function register(){
    	$this->display("register");
    }

    //从教务处加载用户信息
	public function loadStuInfo(){
		$id = I('param.id');
		session('account', $id);
		$str = explode('@', $id);
		$info = getNameById($str[0]);
		if($info !== null){
			session('dept',$info['dept']);
			session('major',$info['major']);
			session('stuNum',$info['stuNum']);
			session('stuId',$info['stuId']);
			session('name',$info['name']);
			$this->ajaxReturn($info, '', 1);
		}
		else{
			$this->ajaxReturn('', '网络出错', 0);
		}
	}

	//检查验证码是否正确
	public function checkVerify(){
		$verify = I('param.verify');
		if(session('verify') != md5($verify)) {
			$this->ajaxReturn('', '验证码错误！', 0);
		}else{
			$this->ajaxReturn('', '', 1);
		}
	}

	//检查用户注册
    public function checkRegister(){
		$account = I('param.account');
		$pwd = I('param.password');
		$verify = I('param.verify');
		//二次验证
		if(session('verify') != md5($verify)) {
			$this->ajaxReturn('', '验证码错误！', 0);
		}		
		
		$User = M('User');
		$data['account'] = $account;
		$data['pwd'] = md5($pwd);
		$data['name'] = session('name');
		$data['dept'] = session('dept');
		$data['degree'] = '20'.substr(session('stuId'),3,2);
		$data['stu_id'] = session('stuId');
		$data['stu_num'] = session('stuNum');
		$data['major'] = session('major');
		$data['reg_time'] = time();
		$data['login_time'] = time();
		$data['modify_time'] = time();
		$data['active_code'] = createKey(32);
		$activeCode = $data['active_code'];
		$result = $User->add($data);
		//$url =  "http://www.seuknower.com/account/active_user/$result/$activeCode";
		$url = "http://localhost/account/active_user/$result/$activeCode";
		$body = "请将此链接 $url 复制到浏览器中激活用户";
		
		$info = think_send_mail($account, session('name'), "用户激活", $body);
		if($info === true){
			$this->ajaxReturn($info, '', 1);
		}
		else{
			$this->ajaxReturn($info, '注册失败', 0);
		}
    }

    public function activeUser(){
		$id = I('param.id');
		$code = I('param.code');
		$User = M('User');
		$map['id'] = $id ;
		$result = $User->field('active_code')->where($map)->find();
		if($result === false){
			$this->error('激活时查询数据库出错！');
		}
		elseif($result === null){
			$this->error('激活时用户不存在！');
		}
		elseif($result['active_code'] == $code){
			$result = $User->find($id);
			session('userId',$id);
			session('account',$result['account']);
			session('userName',$result['name']);
			session('icon',$result['icon']);
			$data['id'] = $id;
			$data['status'] = 1;
			$User->save($data);			
			$this->redirect('/user/profile');
		}
		else{
			$this->error('激活码不正确！');
		}
	}

}
?>