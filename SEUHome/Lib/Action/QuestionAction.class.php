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
		$pageCount = ceil($count/23);
		if(I('param.id')){
			$page = I('param.id');
			if($page > $pageCount) $page = $pageCount;
		}
		else{
			$page = 1;
		}
		$start = ($page-1)*23;
		
		$questions = $Question->order('create_time desc')->limit($start.',23')->select();
		$hotQuestions = $Question->order('click_count desc')->limit(10)->select();
		$this->assign('hots',$hotQuestions);
		$this->assign('questions',$questions);
		$this->assign('count',$count);
		
		$this->assign('curr_page',$page);
		$this->assign('page_count',$pageCount);

		$this->display('index');
    }

    public function detail(){
		$id = I('param.id');
		//$userId = session('userId');
		$Question = M('Question');

		//$deleteModel = new Model();
		//$deleteResult = $deleteModel->execute('delete from seu_question_message where q_id='.$id.' and u_id='.$userId);
	
		$model = new Model();
		$questionMessageResult = $model->query('select * from seu_question_message where u_id='.session('userId'));
		$questionMessageCount = count($questionMessageResult);

		$eventMessageResult = $model->query('select * from seu_event_message where u_id='.session('userId'));
		$eventMessageCount = count($eventMessageResult);

		$commodityMessageResult = $model->query('select * from seu_commodity_message where u_id='.session('userId'));
		$commodityMessageCount = count($commodityMessageResult);

		$messageCount = $questionMessageCount + $eventMessageCount + $commodityMessageCount;

		session('messageCount', $messageCount);

		session('questionMessageResult', $questionMessageResult);
		session('eventMessageResult', $eventMessageResult);
		session('commodityMessageResult', $commodityMessageResult);

		//获取问题编号，然后更新问题的浏览数
		$add['id'] = $id;
		$add['click_count'] = array('exp','click_count+1');
		$Question->save($add);
		
		//获取问题编号之后获取问题信息，再获取问题的提问者编号
		$questionInfo = $Question->find($id);
		$User = M('User');
		$result = $User->field('id')->find($questionInfo['u_id']);
		$questionInfo['user_id'] = $result['id'];
		$this->assign('question',$questionInfo);
		$Model = M();
		//多表查询
		$AnswerInfo = $Model->table('seu_answer answer, seu_user user')->field('answer.*,user.name as u_name, user.icon as icon')->where("answer.q_id = $id AND answer.u_id = user.id")->order('answer.support_count desc')->select();
		$AnonymousInfo = $Model->query('select * from seu_answer where q_id='.$id.' and anonymous=1;');

		for($i=0; $i<count($AnswerInfo); $i++){
			$AnswerInfo[$i]['content'] = htmlspecialchars_decode($AnswerInfo[$i]['content']);//nl2br()

			$Support = M('SupportAnswer');
			if($Support->where("u_id = $userId AND a_id =".$AnswerInfo[$i]['id'])->find()){
				$AnswerInfo[$i]['support'] = 1;

			}
			else{
				$AnswerInfo[$i]['support'] = 0;
			}

			$Nonsupport = M('SupportAnswer');
			if($Nonsupport->where("u_id = $userId AND a_id =".$AnswerInfo[$i]['id'])->find()){
				$AnswerInfo[$i]['nonsupport'] = 1;
			}
			else{
				$AnswerInfo[$i]['nonsupport'] = 0;
			}
		}

		if($AnswerInfo == null && $AnonymousInfo != null){
			$UltimateInfo = $AnonymousInfo;
		}
		if($AnswerInfo != null && $AnonymousInfo == null){
			$UltimateInfo = $AnswerInfo;
		}
		if($AnswerInfo != null && $AnonymousInfo != null){
			$UltimateInfo = array_merge($AnswerInfo, $AnonymousInfo);
		}

		$this->assign('answer', $UltimateInfo);
		$map['u_id'] = $userId;
		$map['q_id'] = $id;
		$Focus = M('FocusQuestion');
		if($Focus->where($map)->find()){
			$this->assign('focus',1);
		}
		else{
			$this->assign('focus',0);
		}

		$this->display('detail_b');
	}
}
?>