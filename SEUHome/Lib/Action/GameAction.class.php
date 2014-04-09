<?php
import("@.Action.CommonUtil");
class GameAction extends Action {
	public function _initialize(){
		$util = new CommonUtil();
		$util->autologin();
	}

	public function doge(){
		$this->display('doge');
	}

	public function lottery(){
		$this->display('lottery');
	}
	
	public function login(){
		if(session('userId')){
			$User = M('User');
			$result = $User->where('id='.session('userId'))->find();
			if($result['lottery_count'] > 0){
				$delete['id'] = $result['id'];
				$delete['lottery_count'] = array('exp','lottery_count-1');
				$User->save($delete);
				$this->generateGuajiang();
				$this->display("guajiang");
			}else{
				//已经没有抽奖机会了
				echo "你已经没有抽奖机会了";
			}
		}else{
			$this->display("login");
		}
		// $this->display("login");
	}

	// 登录检测
	public function checkLogin() {
		$account = I('param.account');
		$password = I('param.password');
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
			cookie('account',$result['account'],864000);
			cookie('password',$result['pwd'],864000);

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
			if($result['lottery_count'] > 0){
				// $User = M('User');
				// $delete['id'] = $result['id'];
				// $delete['lottery_count'] = array('exp','lottery_count-1');
				// $User->save($delete);
				$this->ajaxReturn('', '', 1);
			}else{
				//已经没有抽奖机会了
				$this->ajaxReturn('', '', 3);
			}
		}
	}

	public function register(){
		$this->display("register");
	}

	public function generateGuajiang(){
		$lotteryresult = rand(0, 100);
		$User = M('User');
		$result = $User->where('id='.session('userId'))->find();
		if($lotteryresult >= 99){
			session('lotteryresult', '一等奖');
			$update['id'] = $result['id'];
			$update['lottery_price'] = 1;
			$User->save($update);
		}else if($lotteryresult >= 95){
			session('lotteryresult', '二等奖');
			$update['id'] = $result['id'];
			$update['lottery_price'] = 2;
			$User->save($update);
		}else if($lotteryresult >= 80){
			session('lotteryresult', '三等奖');
			$update['id'] = $result['id'];
			$update['lottery_price'] = 3;
			$User->save($update);
		}else{
			session('lotteryresult', '谢谢参与奖');
			$update['id'] = $result['id'];
			$update['lottery_price'] = 0;
			$User->save($update);
		}
	}

	public function guajiang(){
		if(session('userId')){
			$this->generateGuajiang();
			$User = M('User');
			$result = $User->where('id='.session('userId'))->find();
			if($result['lottery_count'] > 0){
				$delete['id'] = $result['id'];
				$delete['lottery_count'] = array('exp','lottery_count-1');
				$User->save($delete);
				$this->generateGuajiang();
				$this->display("guajiang");
			}else{
				$this->display("deprecate");
			}
		}else{
			$this->display("login");
		}
	}

	public function addLottery(){
		$userId = session("userId");
		$User = M('User');
		$result = $User->where('id='.$userId)->find();
		if($result['isshared'] == 0){
			$add['id'] = $result['id'];
			$add['isshared'] = 1;
			$add['lottery_count'] = array('exp','lottery_count+2');
			$User->save($add);
			$this->ajaxReturn('', '', 1);
		}else{
			$this->ajaxReturn('', '', 0);
		}
	}

	public function verifycode(){
		import('ORG.Util.Image');
		Image::buildImageVerify();
	}

	public function verifyForRegister(){
		$username = I('param.username');
		$password = I('param.password');

		$User = M('User');
		$result = $User->where("account=".$username+"@seu.edu.cn")->find();
		if($result){
			$this->ajaxReturn('', '你已经注册过了', 0);
		}

		$verify = I('param.verify');
		if(session('verify') != md5($verify)) {
			$this->ajaxReturn('', '验证码错误！', 0);
		}

		$ret = verifyFromMySeu($username, $password);
		if($ret == 1){
			$this->ajaxReturn('', '', 1);
		}else{
			$this->ajaxReturn('', 'myseu密码不正确', 0);
		}
	}

	public function logout(){
		$id = session('userId');
        if(isset($id)) {
			unset($_SESSION);
			session(null);
			session_destroy();
			cookie('account',null);
			cookie('password',null);
			$this->redirect('/game/login');
        }
	}

	public function lotteryManage(){
		$userName = session("userName");
		if($userName == "cleantha"){
			$User = M('User');
			$users = $User->where('status=1 and isgetprice=0')->order("id desc")->select();
			$this->assign("users", $users);
			$this->assign('current', 'lottery');
			$this->display("lottery");
		}else{
			$this->redirect("/");
		}
		
	}

	public function getPrice(){
		$uids = I('param.uids');
		$User = M('User');
		for($i=0; $i<count($uids); $i++){
			$update['id'] = $uids[$i];
			$update['isgetprice'] = 1;
			$result = $User->save($update);
			if(!$result){
				$this->ajaxReturn('', '', 0);
			}
		}
		$this->ajaxReturn('', '', 1);
	}

	public function addCleantha(){
		$User = M('User');
		$add['account'] = "333@seu.edu.cn";
		$add['pwd'] = "a2d871755fb54f91baff08f0c0070b8e";
		$add['name'] = "cleantha";
		$add['status'] = 1;
		$User->add($add);
		echo md5("cleantha");
	}

}
?>