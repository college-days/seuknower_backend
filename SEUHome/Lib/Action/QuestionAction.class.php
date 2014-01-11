<?php
// 本类由系统自动生成，仅供测试用途
class QuestionAction extends Action {
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
    	$Question = M('Question');
		$count = $Question->count();// 查询满足要求的总记录数 $map表示查询条件
		$pageCount = ceil($count/15);
		if(I('param.id')){
			$page = I('param.id');
			if($page > $pageCount) $page = $pageCount;
		}
		else{
			$page = 1;
		}
		$start = ($page-1)*15;
		
		$questions = $Question->order('create_time desc')->limit($start.',15')->select();
		$hotQuestions = $Question->order('click_count desc')->limit(10)->select();
		$this->assign('hots',$hotQuestions);
		$this->assign('questions',$questions);
		$this->assign('count',$count);
		
		$this->assign('curr_page',$page);
		$this->assign('page_count',$pageCount);

		$this->display('index');
    }
}
?>