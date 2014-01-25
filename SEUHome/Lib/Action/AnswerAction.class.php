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
			$data['u_id'] = $userId;
			$data['a_id'] = I('param.id');
			$data['create_time'] = time();
			$SupportAnswer = M('SupportAnswer');
			$SupportAnswer->add($data);
			
			$Answer = M('Answer');
			$add['id'] = I('param.id');
			$add['support_count'] = array('exp','support_count+1');
			$Answer->save($add);
			$this->ajaxReturn('','',1);
		}
		else{
			//should login first should comeout a view
			$this->ajaxReturn('','',0);
		}
	}
	
	public function addObject(){
		$userId=session('userId');
		if(isset($userId)){
			$map['u_id'] = $userId;
			$map['a_id'] = I('param.id');
			$SupportAnswer = M('SupportAnswer');
			$SupportAnswer->where($map)->delete();

			$Answer = M('Answer');
			$data['id'] = I('param.id');
			$data['nonsupport_count'] = array('exp','nonsupport_count+1');
			$Answer->save($data);
			$this->ajaxReturn('','',1);
		}
		else{
			$this->ajaxReturn('','',0);
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
					/*$model = new Model();
					$newresult = $model->query('select u_id, title from seu_question where id='.$qid);
					$uidforqid = $newresult[0]['u_id'];
					$titleforqid = $newresult[0]['title'];
					$messageResult = $model->query('select * from seu_question_message where u_id='.$uidforqid.' and q_id='.$qid);
					if($messageResult == null){
						$messageData['q_id'] = $qid;
						$messageData['u_id'] = $uidforqid;
						$messageData['title'] = $titleforqid;
						$messageData['answer_count'] = array('exp','answer_count+1');
						$messageModel = M('Question_message');
						$addResult = $messageModel->add($messageData);
					}else{
						//需要主键才可以= =
						$messageData['answer_count'] = array('exp','answer_count+1');
						$messageModel = M('Question_message');
						$saveResult = $messageModel->where('q_id='.$qid.' and u_id='.$uidforqid)->save($messageData);
					}*/

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
				/*$model = new Model();
				$result = $model->query('select u_id, title from seu_question where id='.$qid);
				$uidforqid = $result[0]['u_id'];
				$titleforqid = $result[0]['title'];
				$messageResult = $model->query('select * from seu_question_message where u_id='.$uidforqid.' and q_id='.$qid);
				if($messageResult == null){
					$messageData['q_id'] = $qid;
					$messageData['u_id'] = $uidforqid;
					$messageData['title'] = $titleforqid;
					$messageData['answer_count'] = array('exp','answer_count+1');
					$messageModel = M('Question_message');
					$addResult = $messageModel->add($messageData);
				}else{
					//需要主键才可以= =
					$messageData['answer_count'] = array('exp','answer_count+1');
					$messageModel = M('Question_message');
					$saveResult = $messageModel->where('q_id='.$qid.' and u_id='.$uidforqid)->save($messageData);
				}*/

				$this->ajaxReturn('', '', 1);
			}

		}
		//未登陆
		else{
			$this->ajaxReturn('', '', -1);
		}
	}
}
?>