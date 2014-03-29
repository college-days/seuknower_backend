<?php
import("@.Action.CommonUtil");
class AccountAction extends Action{
	public function _initialize(){
		$util = new CommonUtil();
		$util->autologin();
	}

	public function _empty(){
		$this->display("Public:404");
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
			$this->ajaxReturn('', '查询数据库出错!', 0);
		}
		elseif($result === null){
			$this->ajaxReturn('', '帐号错误!', 0);
		}
		elseif($result['status'] == 0){
			$this->ajaxReturn('', '帐号未激活!', 0);
		}
		elseif($result['pwd'] != md5($password)){
			$this->ajaxReturn('', '密码错误!', 0);
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
			$this->ajaxReturn('', '', 1);
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
			if($info['name'] != null){
				session('dept',$info['dept']);
				session('major',$info['major']);
				session('stuNum',$info['stuNum']);
				session('stuId',$info['stuId']);
				session('name',$info['name']);

				$this->ajaxReturn($info, '', 1);
			}else{
				$this->ajaxReturn('', '请输入正确的一卡通号', 0);
			}
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
		$getUser =  $User->where(array('account' => $account))->find();
		if($getUser){
			if($getUser['status'] == 0){
				$result = $getUser['id'];
				$activeCode = $getUser['active_code'];
				$body = "请点击此<a href='http://www.seuknower.com/account/active_user/$result/$activeCode' target='_blank'>链接</a>来激活用户";
				// $body = "请点击此<a href='http://localhost/account/active_user/$result/$activeCode' target='_blank'>链接</a>来激活用户";

				$info = think_send_mail($account, session('name'), "用户激活", $body);
				if($info === true){
					$this->ajaxReturn($info, '', 1);
				}
				else{
					$this->ajaxReturn($info, '注册失败', 0);
				}
			}else{
				$this->ajaxReturn('', '你已经注册过了', 0);
			}
		}else{
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
			$body = "请点击此<a href='http://www.seuknower.com/account/active_user/$result/$activeCode' target='_blank'>链接</a>来激活用户";
			// $body = "请点击此<a href='http://localhost/account/active_user/$result/$activeCode' target='_blank'>链接</a>来激活用户";

			$info = think_send_mail($account, session('name'), "用户激活", $body);
			if($info === true){
				$this->ajaxReturn($info, '', 1);
			}
			else{
				$this->ajaxReturn($info, '注册失败', 0);
			}
		}
		
		//$url =  "http://www.seuknower.com/account/active_user/$result/$activeCode";
		// $url = "http://localhost/account/active_user/$result/$activeCode";
		// $body = "请将此链接 $url 复制到浏览器中激活用户";
    }

    public function reSendActiveEmail(){
    	$account = session('account');
    	$User = M('User');
		$getUser =  $User->where(array('account' => $account))->find();
		if($getUser){
			if($getUser['status'] == 0){
				$result = $getUser['id'];
				$activeCode = $getUser['active_code'];
				$body = "请点击此<a href='http://www.seuknower.com/account/active_user/$result/$activeCode' target='_blank'>链接</a>来激活用户";
				// $body = "请点击此<a href='http://localhost/account/active_user/$result/$activeCode' target='_blank'>链接</a>来激活用户";

				$info = think_send_mail($account, session('name'), "用户激活", $body);
				if($info === true){
					$this->ajaxReturn($info, '', 1);
				}
				else{
					$this->ajaxReturn($info, '发送失败', 0);
				}
			}else{
				$this->ajaxReturn('', '你已经注册过了', 0);
			}
		}else{
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
			$body = "请点击此<a href='http://www.seuknower.com/account/active_user/$result/$activeCode' target='_blank'>链接</a>来激活用户";
			// $body = "请点击此<a href='http://localhost/account/active_user/$result/$activeCode' target='_blank'>链接</a>来激活用户";
			
			$info = think_send_mail($account, session('name'), "用户激活", $body);
			if($info === true){
				$this->ajaxReturn($info, '', 1);
			}
			else{
				$this->ajaxReturn($info, '发送失败', 0);
			}
		}
    }

    public function startActiveUser(){
    	$userAccount = session('account');
    	$this->assign('account', $userAccount);
    	$this->display('startactive');
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
			//$this->redirect('User/profile', array('id' => $id));
			$this->assign('name', $result['name']);
			$this->assign('id', $id);
			$this->display('finishactive');
		}
		else{
			$this->error('激活码不正确！');
		}
	}

	public function checkMessage(){
		$QuestionMessage = M('QuestionMessage');
		$EventMessage = M('EventMessage');
		$CommodityMessage = M('CommodityMessage');
		$currentUserId = session('userId');
		$User = M('User');
		$EventAt = M('EventAt');
		$CommodityAt = M('CommodityAt');
		$AnswerMessage = M('AnswerMessage');
		$AnswerAt = M('AnswerAt');
		$AgreeMessage = M('AgreeMessage');
		$CommoditywantMessage = M('CommoditywantMessage');
		$CommoditywantAt = M('CommoditywantAt');

		$messageMap['u_id'] = $currentUserId;
		$questionResult = $QuestionMessage->where($messageMap)->select();
		$eventResult = $EventMessage->where($messageMap)->select();
		$commodityResult = $CommodityMessage->where($messageMap)->select();
		$eventAtResult = $EventAt->where($messageMap)->select();
		$commodityAtResult = $CommodityAt->where($messageMap)->select();
		$answerResult = $AnswerMessage->where($messageMap)->select();
		$answerAtResult = $AnswerAt->where($messageMap)->select();
		$agreeResult = $AgreeMessage->where($messageMap)->select();
		$commodityWantResult = $CommoditywantMessage->where($messageMap)->select();
		$commodityWantAtResult = $CommoditywantAt->where($messageMap)->select();

		for($i=0; $i<count($questionResult); $i++){
			$questionResult[$i]['type'] = 'question';
			if($questionResult[$i]['from_id'] == -3){
				$questionResult[$i]['title'] = "有人匿名回答了你提出的问题";
			}else{
				$from_id = $questionResult[$i]['from_id'];
				$from_user = $User->where('id='.$from_id)->find();
				$from_name = $from_user['name'];
				$questionResult[$i]['title'] = $from_name."回答了你提出的问题";
			}
		}

		for($i=0; $i<count($eventResult); $i++){
			$eventResult[$i]['type'] = 'event';
			$from_id = $eventResult[$i]['from_id'];
			$from_user = $User->where('id='.$from_id)->find();
			$from_name = $from_user['name'];
			$eventResult[$i]['title'] = $from_name."回复了你发起的活动";
		}

		for($i=0; $i<count($commodityResult); $i++){
			$commodityResult[$i]['type'] = 'commodity';
			$from_id = $commodityResult[$i]['from_id'];
			$from_user = $User->where('id='.$from_id)->find();
			$from_name = $from_user['name'];
			$commodityResult[$i]['title'] = $from_name."评论了你发布的商品";
		}

		for($i=0; $i<count($eventAtResult); $i++){
			$eventAtResult[$i]['type'] = 'event';
			$from_id = $eventAtResult[$i]['from_id'];
			$from_user = $User->where('id='.$from_id)->find();
			$from_name = $from_user['name'];
			$eventAtResult[$i]['title'] = $from_name."@了你";
		}

		for($i=0; $i<count($commodityAtResult); $i++){
			$commodityAtResult[$i]['type'] = 'commodity';
			$from_id = $commodityAtResult[$i]['from_id'];
			$from_user = $User->where('id='.$from_id)->find();
			$from_name = $from_user['name'];
			$commodityAtResult[$i]['title'] = $from_name."@了你";
		}

		for($i=0; $i<count($answerResult); $i++){
			$answerResult[$i]['type'] = 'answer';
			$from_id = $answerResult[$i]['from_id'];
			$from_user = $User->where('id='.$from_id)->find();
			$from_name = $from_user['name'];
			$answerResult[$i]['title'] = $from_name."回复了你的回答";
		}

		for($i=0; $i<count($answerAtResult); $i++){
			$answerAtResult[$i]['type'] = 'answer';
			$from_id = $answerAtResult[$i]['from_id'];
			$from_user = $User->where('id='.$from_id)->find();
			$from_name = $from_user['name'];
			$answerAtResult[$i]['title'] = $from_name."@了你";
		}

		for($i=0; $i<count($agreeResult); $i++){
			$agreeResult[$i]['type'] = 'question';
			$from_id = $agreeResult[$i]['from_id'];
			$from_user = $User->where('id='.$from_id)->find();
			$from_name = $from_user['name'];
			$agreeResult[$i]['title'] = $from_name."赞同了你的回答";
		}

		for($i=0; $i<count($commodityWantResult); $i++){
			$commodityWantResult[$i]['type'] = 'want';
			$from_id = $commodityWantResult[$i]['from_id'];
			$from_user = $User->where('id='.$from_id)->find();
			$from_name = $from_user['name'];
			$commodityWantResult[$i]['title'] = $from_name."回复了你的求购";
		}

		for($i=0; $i<count($commodityWantAtResult); $i++){
			$commodityWantAtResult[$i]['type'] = 'want';
			$from_id = $commodityWantAtResult[$i]['from_id'];
			$from_user = $User->where('id='.$from_id)->find();
			$from_name = $from_user['name'];
			$commodityWantAtResult[$i]['title'] = $from_name."@了你";
		}

		if(!$questionResult){
			$questionResult = array();
		}

		if(!$eventResult){
			$eventResult = array();
		}

		if(!$commodityResult){
			$commodityResult = array();
		}

		if(!$eventAtResult){
			$eventAtResult = array();
		}

		if(!$commodityAtResult){
			$commodityAtResult = array();
		}

		if(!$answerResult){
			$answerResult = array();
		}

		if(!$answerAtResult){
			$answerAtResult = array();
		}

		if(!$agreeResult){
			$agreeResult = array();
		}

		if(!$commodityWantResult){
			$commodityWantResult = array();
		}

		if(!$commodityWantAtResult){
			$commodityWantAtResult = array();
		}

		$finalResult = array_merge($questionResult, $eventResult, $commodityResult, $eventAtResult, $commodityAtResult, $answerResult, $answerAtResult, $agreeResult, $commodityWantResult, $commodityWantAtResult);

		session('messagecount', count($finalResult));
		session('messages', $finalResult);

		if(count($finalResult) == 0){
			$this->ajaxReturn('', '', 0);
		}else{
			$this->ajaxReturn($finalResult, '', 1);
		}
	}

	public function startChangePassword(){
		$this->display('startchangepassword');
	}

	public function changePasswordSession(){
		$account = I('param.account');
		$verify = I('param.verify');
		if(session('verify') != md5($verify)) {
			$this->ajaxReturn('', '验证码错误！', 0);
		}
		
		$User = M('User');
		$map['account'] = $account;
		$map['status'] = 1;
		
		$user = $User->where($map)->find();
		if($user){
			$id = $user['id'];
			$data['id'] = $id;
			$data['active_code'] = createKey(32);
			$data['status'] = 0;
			
			$activeCode = $data['active_code'];
			$User->save($data);

			$body = "请点击此<a href='http://www.seuknower.com/account/change_password/$id/$activeCode' target='_blank'>链接</a>来完成修改密码";
			// $body = "请点击此<a href='http://localhost/account/change_password/$id/$activeCode' target='_blank'>链接</a>来完成修改密码";

			$info = think_send_mail($account,$user['name'], "修改账户密码", $body);
		
			if($info === true){
				$this->ajaxReturn($info, '', 1);
			}
			else{
				$this->ajaxReturn($info, '邮件发送失败', 0);
			}
		}
		else{
			$this->ajaxReturn('', '用户不存在或未激活', 0);
		}
	}

	public function changePassword(){
		$id = I('param.id');
		$code = I('param.code');
		$User = M('User');
		$map['id'] = $id;
		$result = $User->field('active_code')->where($map)->find();
		if($result === false){
			$this->error('激活时查询数据库出错！');
		}
		elseif($result === null){
			$this->error('激活时用户不存在！');
		}
		elseif($result['active_code'] == $code){
			$data['id'] = $id;
			$data['status'] = 1;
			$User->save($data);
			$this->display('changepassword');
		}
		else{
			$this->error('激活码不正确！');
		}
	}

	public function savePassword(){
		$account = I('param.account');
		$password = I('param.password');
		$verify = I('param.verify');
		if(session('verify') != md5($verify)) {
			$this ->ajaxReturn('', '验证码错误！', 0);
		}
		
		$User = M('User');
		$map['account'] = $account;
		$map['status'] = 1;
		
		$user = $User->where($map)->find();
		if($user){
			$id = $user['id'];
			$data['id'] = $id;
			$data['pwd'] = md5($password);
			
			$result = $User->save($data);

			if(!$result){
				if(md5($password) != $user['pwd']){
					$this->ajaxReturn('', '密码修改失败', 0);
				}else{
					session('userId', $id);
					session('account', $account);
					session('userName', $user['name']);
					session('icon', $user['icon']);
					$this->ajaxReturn('', '', 1);
				}
			}else{
				session('userId', $id);
				session('account', $account);
				session('userName', $user['name']);
				session('icon', $user['icon']);
				$this->ajaxReturn('', '', 1);
			}
		}
		else{
			$this->ajaxReturn('', '用户不存在或未激活', 0);
		}
	}

	public function activePassword(){
		$this->assign('name', session('userName'));
		$this->assign('id', session('userId'));
		$this->display('finishchange');
	}

	public function checkInvite(){
		$inviteCode = I('param.invitecode');
		$Invite = M('Invite');
		$result = $Invite->where("content='".$inviteCode."'")->delete();
		if(!$result){
			$this->ajaxReturn('', '', 0);
		}else{
			$User = M('User');
			$userId = session('userId');
			$data['invited'] = 1;
			$userresult = $User->where('id='.$userId)->save($data);
			if(!$userresult){
				$this->ajaxReturn('', '', -1);
			}else{
				$this->ajaxReturn('', '', 1);
			}
		}
	}
}
?>