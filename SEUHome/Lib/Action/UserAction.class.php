<?php
// 本类由系统自动生成，仅供测试用途
import("@.Action.CommonUtil");

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

    	$Question = M('Question');
    	$askQuestions = $Question->where('u_id='.$u_id)->order('create_time desc')->select();

    	$Answer = M('Answer');
    	$answerQuestionIds = $Answer->field('q_id')->where('u_id='.$u_id)->order('create_time desc')->select();
    	$answerQuestions = array();
    	for($i=0; $i<count($answerQuestionIds); $i++){
    		$answerQuestion = $Question->where('id='.$answerQuestionIds[$i]['q_id'])->find();
    		if(!empty($answerQuestion)){
    			$askUserInfo = $User->where('id='.$answerQuestion["u_id"])->find();
    			$answerQuestion["ask_name"] = $askUserInfo["name"];
    			array_push($answerQuestions, $answerQuestion);
    		}
    	}

    	/*
    	for($i=0; $i<count($askQuestions); $i++){
    		$askQuestions[$i]["title"] = $util->sub_string($askQuestions[$i]["title"], 18);
    	}

    	for($i=0; $i<count($answerQuestions); $i++){
    		$answerQuestions[$i]["title"] = $util->sub_string($answerQuestions[$i]["title"], 18);
    	}
    	*/

        $Event = M('Event');
    	$EventInterest = M('InterestEvent');
    	$interestEventIds = $EventInterest->field('e_id')->where('u_id='.$u_id)->order('create_time desc')->select();
    	$interestEvents = array();
    	for($i=0; $i<count($interestEventIds); $i++){
    		$interestEvent = $Event->where('id='.$interestEventIds[$i]['e_id'])->find();
    		if(!empty($interestEvent)){
    			array_push($interestEvents, $interestEvent);
    		}
    	}

    	$EventJoin = M('JoinEvent');
    	$joinEventIds = $EventJoin->field('e_id')->where('u_id='.$u_id)->order('create_time desc')->select();
    	$joinEvents = array();
    	for($i=0; $i<count($joinEventIds); $i++){
    		$joinEvent = $Event->where('id='.$joinEventIds[$i]['e_id'])->find();
    		if(!empty($joinEvent)){
    			array_push($joinEvents, $joinEvent);
    		}
    	}

    	$util = new CommonUtil();	
    	
    	for($i=0; $i<count($joinEvents); $i++){
			if(!$util->exists_file($joinEvents[$i]["poster"])){
				$joinEvents[$i]["poster"] = "__IMAGE__/event/act5.jpg";
			}	
			$joinEvents[$i]["title"] = $util->sub_string($joinEvents[$i]["title"], 6);
    	}

		for($i=0; $i<count($interestEvents); $i++){
			if(!$util->exists_file($interestEvents[$i]["poster"])){
				$interestEvents[$i]["poster"] = "__IMAGE__/event/act5.jpg";
			}	
			$interestEvents[$i]["title"] = $util->sub_string($interestEvents[$i]["title"], 6);
    	}

		$Commodity = M('Commodity');
    	$sellCommodities = $Commodity->where('u_id='.$u_id)->order('create_time desc')->select();

    	for($i=0; $i<count($sellCommodities); $i++){
    		if(!$util->exists_file($sellCommodities[$i]["picture"])){
    			$sellCommodities[$i]["picture"] = "__IMAGE__/user_center/goods_01.jpg";
    		}
    		//待测试 现在用的两个账号一个没有商品，还有一个商品的title都很短
    		$util->sub_string($sellCommodities[$i]["title"], 8);
    	}

    	if($_SESSION['userId'] == $u_id){
    		$this->assign('me', '1');
    	}else{
    		$this->assign('me', '0');
    	}

    	$this->assign('asks', $askQuestions);
    	$this->assign('askcount', count($askQuestions));
    	$this->assign('answers', $answerQuestions);
    	$this->assign('answercount', count($answerQuestions));
    	$this->assign('interestevents', $interestEvents);
    	$this->assign('interesteventcount', count($interestEvents));
    	$this->assign('joinevents', $joinEvents);
    	$this->assign('joineventcount', count($joinEvents));
    	$this->assign('sellcommodities', $sellCommodities);
    	$this->assign('sellcommoditycount', count($sellCommodities));
    	$this->assign('user', $userInfo);
    	$this->assign('u_id', $u_id);
    	$this->display('user_center');
    }

    public function ask_question(){
    	$u_id = I('param.id');
    	$User = M('User');
    	$userInfo = $User->find($u_id);
    	$this->assign('user', $userInfo);
    	$this->assign('u_id', $u_id);
    	$this->display('ask_question');
    }

    public function answer_question(){
    	$u_id = I('param.id');
    	$User = M('User');
    	$userInfo = $User->find($u_id);
    	$this->assign('user', $userInfo);
    	$this->assign('u_id', $u_id);
    	$this->display('answer_question');
    }
}
?>