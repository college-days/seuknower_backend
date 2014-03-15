<?php
import("@.Action.CommonUtil");
class AnswerAction extends Action {
	public function _initialize(){
		$util = new CommonUtil();
		$util->autologin();
	}

	public function addAgree(){
		$userId = session('userId');
		if(isset($userId)){
			$agreedata['u_id'] = $userId;
			$agreedata['a_id'] = I('param.id');
			$agreedata['create_time'] = time();
			$SupportAnswer = M('SupportAnswer');
			$SupportAnswer->add($agreedata);
			
			$Answer = M('Answer');
			$add['id'] = I('param.id');
			$add['support_count'] = array('exp','support_count+1');
			$Answer->save($add);

			$objectdata['u_id'] = $userId;
			$objectdata['a_id'] = I('param.id');
			$NonsupportAnswer = M('NonsupportAnswer');
			$NonsupportAnswer->where($objectdata)->delete();

			$Answer = M('Answer');
			$data['id'] = I('param.id');
			$data['nonsupport_count'] = array('exp','nonsupport_count-1');
			$Answer->save($data);

			$this->ajaxReturn('', '', 1);
		}
		else{
			//should login first should comeout a view
			$this->ajaxReturn('', '', 0);
		}
	}
	
	public function cancelAgree(){
		$userId=session('userId');
		if(isset($userId)){
			$map['u_id'] = $userId;
			$map['a_id'] = I('param.id');
			
			$SupportAnswer = M('SupportAnswer');
			$SupportAnswer->where($map)->delete();
			$Answer = M('Answer');
			$data['id'] = I('param.id');
			$data['support_count'] = array('exp','support_count-1');
			$Answer->save($data);
			$this->ajaxReturn('', '', 1);
		}
		else{
			$this->ajaxReturn('', '', 0);
		}
	}

	public function addObject(){
		$userId=session('userId');
		if(isset($userId)){
			$objectdata['u_id'] = $userId;
			$objectdata['a_id'] = I('param.id');
			$objectdata['create_time'] = time();
			$NonsupportAnswer = M('NonsupportAnswer');
			$NonsupportAnswer->add($objectdata);
			
			$Answer = M('Answer');
			$add['id'] = I('param.id');
			$add['nonsupport_count'] = array('exp','nonsupport_count+1');
			$Answer->save($add);

			$agreedata['u_id'] = $userId;
			$agreedata['a_id'] = I('param.id');
			$SupportAnswer = M('SupportAnswer');
			$SupportAnswer->where($agreedata)->delete();

			$Answer = M('Answer');
			$data['id'] = I('param.id');
			$data['support_count'] = array('exp','support_count-1');
			$Answer->save($data);

			$this->ajaxReturn('', '', 1);
		}
		else{
			$this->ajaxReturn('', '', 0);
		}
	}

	public function cancelObject(){
		$userId=session('userId');
		if(isset($userId)){
			$map['u_id'] = $userId;
			$map['a_id'] = I('param.id');
			
			$NonsupportAnswer = M('NonsupportAnswer');
			$NonsupportAnswer->where($map)->delete();
			$Answer = M('Answer');
			$data['id'] = I('param.id');
			$data['nonsupport_count'] = array('exp','nonsupport_count-1');
			$Answer->save($data);
			$this->ajaxReturn('', '', 1);
		}
		else{
			$this->ajaxReturn('', '', 0);
		}
	}

	public function addAnswer(){
		if(isset($_SESSION['userId'])){
			$qid = I('param.q_id');
			$content = I('param.content');
			$uid = session('userId');
			$anonymous = I('param.anonymous');

			//实名回答
			if($anonymous == 0){
				$model = new Model();
				$result = $model->query('select * from seu_answer where q_id='.$qid.' and u_id='.$uid);
			    if($result == null){
	            	$data['q_id'] = I('param.q_id');
					$data['content'] = I('param.content');
					$data['u_id'] = session('userId');
					$data['anonymous'] = 0;
					$data['create_time'] = time();
					$Answer = M('Answer');
					$result = $Answer->add($data);
					if($result < 1){
						$this->ajaxReturn('', '', 0);
					}

					$Question = M('Question');
					$questiondata['id'] = I('param.q_id');
					//回答数加1
					$questiondata['answer_count'] = array('exp','answer_count+1');
					$questiondata['has_answer'] = 1;
					$Question->save($questiondata);

					//回答的消息提示
					$model = new Model();
					$newresult = $model->query('select u_id, title from seu_question where id='.$qid);
					$uidforqid = $newresult[0]['u_id'];
					$titleforqid = $newresult[0]['title'];
					$messageResult = $model->query('select * from seu_question_message where u_id='.$uidforqid.' and q_id='.$qid.' and from_id='.session('userId'));
					if($messageResult == null){
						$messageData['q_id'] = $qid;
						$messageData['u_id'] = $uidforqid;
						$messageData['from_id'] = session("userId");
						$messageData['title'] = $titleforqid;
						$messageData['answer_count'] = array('exp','answer_count+1');
						$messageModel = M('Question_message');
						$addResult = $messageModel->add($messageData);
					}else{
						//需要主键才可以= =
						$messageData['answer_count'] = array('exp','answer_count+1');
						$messageModel = M('Question_message');
						$saveResult = $messageModel->where('q_id='.$qid.' and u_id='.$uidforqid.' and from_id='.session('userId'))->save($messageData);
					}

					$this->ajaxReturn('', '', 1);
	       	 	}else{
	       	 		//回答次数限制
	            	$this->ajaxReturn('', '', 3);
	        	}

			}
			//匿名回答
			if($anonymous == 1){
				$data['q_id'] = I('param.q_id');
				$data['content'] = I('param.content');
				$data['anonymous'] = 1;
				$data['u_id'] = session('userId');
				$data['create_time'] = time();
				$Answer = M('Answer');
				$result = $Answer->add($data);
				if($result < 1){
					$this->ajaxReturn('', '', 0);
				}
				//$result['content'] = htmlspecialchars_decode($data['content']);

				$Question = M('Question');
				$questiondata['id'] = I('param.q_id');
				//回答数加1
				$questiondata['answer_count'] = array('exp','answer_count+1');
				$questiondata['has_answer'] = 1;
				$Question->save($questiondata);

				//回答的消息提示
				$model = new Model();
				$result = $model->query('select u_id, title from seu_question where id='.$qid);
				$uidforqid = $result[0]['u_id'];
				$titleforqid = $result[0]['title'];
				$messageResult = $model->query('select * from seu_question_message where u_id='.$uidforqid.' and q_id='.$qid);
				if($messageResult == null){
					$messageData['q_id'] = $qid;
					$messageData['u_id'] = $uidforqid;
					$messageData['from_id'] = -3;
					$messageData['title'] = $titleforqid;
					$messageData['answer_count'] = array('exp','answer_count+1');
					$messageModel = M('Question_message');
					$addResult = $messageModel->add($messageData);
				}else{
					//需要主键才可以= =
					$messageData['answer_count'] = array('exp','answer_count+1');
					$messageModel = M('Question_message');
					$saveResult = $messageModel->where('q_id='.$qid.' and u_id='.$uidforqid)->save($messageData);
				}

				$this->ajaxReturn('', '', 1);
			}

		}
		//未登陆
		else{
			$this->ajaxReturn('', '', -1);
		}
	}

	public function addReply(){
		$data['a_id'] = I('param.a_id');
		$data['u_id'] = session('userId');
		$data['content'] = I('param.msg');
		$data['create_time'] = time();
		$Answer = M('Answer');

		$targetAnswer = $Answer->where('id='.I('param.a_id'))->find();
		$AnswerMessage = M('AnswerMessage');
		$messageResult = $AnswerMessage->where('a_id='.I('param.a_id').' and u_id='.$targetAnswer['u_id'].' and from_id='.session('userId'))->select();
		if($messageResult == null){
			$messageData['q_id'] = I('param.q_id');
			$messageData['a_id'] = I('param.a_id');
			$messageData['u_id'] = $targetAnswer['u_id'];
			$messageData['from_id'] = session("userId");
			$AnswerMessage->add($messageData);
		}

		$AnswerAt = M('AnswerAt');
		$atMessageResult = $AnswerAt->where('a_id='.I('param.a_id').' and u_id='.I('param.at_id').' and from_id='.session('userId'))->select();
		if($atMessageResult == null){
			$atMessageData['q_id'] = I('param.q_id');
			$atMessageData['a_id'] = I('param.a_id');
			$atMessageData['u_id'] = I('param.at_id');
			$atMessageData['from_id'] = session("userId");
			$AnswerAt->add($atMessageData);
		}

		$AnswerReply = M('AnswerReply');
		$result = $AnswerReply->add($data);
		if($result < 1){
			$this->ajaxReturn('', '', 0);
		}else{
			$this->ajaxReturn('', '', 1);
		}
	}

	public function changeContent(){
		$aid = I('param.a_id');
		$content = I('param.content');
		// $pwd = I('param.pwd');

		$Answer = M('Answer');
		$User = M('User');

		$answer = $Answer->where("id=".$aid)->find();
		$user = $User->where("id=".$answer['u_id'])->find();

		$answerSaveData['content'] = $content;
		$result = $Answer->where('id='.$aid)->save($answerSaveData);
		if($result){
			$this->ajaxReturn('', '', 1);
		}else{
			$this->ajaxReturn('', '', 0);
		}

		/*if(md5($pwd) != $user['pwd']){
			//密码不正确
			$this->ajaxReturn('', '', -1);
		}else{
			$answerSaveData['content'] = $content;
			$result = $Answer->where('id='.$aid)->save($answerSaveData);
			if($result){
				$this->ajaxReturn('', '', 1);
			}else{
				$this->ajaxReturn('', '', 0);
			}
		}*/
	}
}
?>