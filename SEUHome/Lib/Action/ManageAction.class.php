<?php
class ManageAction extends Action {
	public function _initialize(){
		$userId = session("userId");
		if($userId != 7){
			$this->redirect("/");
		}
	}

	public function _empty(){
		$this->display("Public:404");
	}

	public function event(){
		$Event = M('Event');
		$events = $Event->order("create_time desc")->select();
		$this->assign("events", $events);
		$this->assign("current", "event");
		$this->display("event");
	}

	public function commodity(){
		$Commodity = M('Commodity');
		$commodities = $Commodity->order("create_time desc")->select();
		$this->assign("commodities", $commodities);
		$this->assign("current", "commodity");
		$this->display("commodity");
	}

	public function question(){
		$Question = M('Question');
		$questions = $Question->order("create_time desc")->select();
		$this->assign("questions", $questions);
		$this->assign("current", "question");
		$this->display("question");
	}

	public function answer(){
		$Answer = M('Answer');
		$answers = $Answer->order("create_time desc")->select();
		for($i=0; $i<count($answers); $i++){
			$answers[$i]['content'] = htmlspecialchars_decode($answers[$i]['content']);
		}
		$this->assign("answers", $answers);
		$this->assign("current", "answer");
		$this->display("answer");
	}

	public function eventComment(){
		$EventComment = M('EventComment');
		$eventComments = $EventComment->order("create_time desc")->select();
		for($i=0; $i<count($eventComments); $i++){
			$eventComments[$i]['content'] = htmlspecialchars_decode($eventComments[$i]['content']);
		}
		$this->assign("eventcomments", $eventComments);
		$this->assign("current", "eventcomment");
		$this->display("eventcomment");
	}

	public function commodityComment(){
		$CommodityComment = M('CommodityComment');
		$commodityComments = $CommodityComment->order("create_time desc")->select();
		for($i=0; $i<count($commodityComments); $i++){
			$commodityComments[$i]['content'] = htmlspecialchars_decode($commodityComments[$i]['content']);
		}
		$this->assign("commoditycomments", $commodityComments);
		$this->assign("current", "commoditycomment");
		$this->display("commoditycomment");
	}

	public function answerReply(){
		$AnswerReply = M('AnswerReply');
		$answerReplys = $AnswerReply->order("create_time desc")->select();
		for($i=0; $i<count($answerReplys); $i++){
			$answerReplys[$i]['content'] = htmlspecialchars_decode($answerReplys[$i]['content']);
		}
		$this->assign("answerreplys", $answerReplys);
		$this->assign("current", "answerreply");
		$this->display("answerreply");
	}

	public function deleteEvent(){
		$eids = I('param.eids');
		$Event = M('Event');
		for($i=0; $i<count($eids); $i++){
			$result = $Event->where('id='.$eids[$i])->delete();
			if(!$result){
				$this->ajaxReturn('', '', 0);
			}
		}
		$this->ajaxReturn('', '', 1);
	}

	public function deleteCommodity(){
		$cids = I('param.cids');
		$Commodity = M('Commodity');
		for($i=0; $i<count($cids); $i++){
			$result = $Commodity->where('id='.$cids[$i])->delete();
			if(!$result){
				$this->ajaxReturn('', '', 0);
			}
		}
		$this->ajaxReturn('', '', 1);
	}

	public function deleteQuestion(){
		$qids = I('param.qids');
		$Question = M('Question');
		for($i=0; $i<count($qids); $i++){
			$result = $Question->where('id='.$qids[$i])->delete();
			if(!$result){
				$this->ajaxReturn('', '', 0);
			}
		}
		$this->ajaxReturn('', '', 1);
	}

	public function deleteAnswer(){
		$aids = I('param.aids');
		$Answer = M('Answer');
		$Question = M('Question');
		for($i=0; $i<count($aids); $i++){
			$answer = $Answer->where('id='.$aids[$i])->find();
			$delete['id'] = $answer['q_id'];
			$delete['answer_count'] = array('exp','answer_count-1');
			$Question->save($delete);
			$result = $Answer->where('id='.$aids[$i])->delete();
			if(!$result){
				$this->ajaxReturn('', '', 0);
			}
		}
		$this->ajaxReturn('', '', 1);
	}

	public function deleteEventComment(){
		$ecids = I('param.ecids');
		$EventComment = M('EventComment');
		for($i=0; $i<count($ecids); $i++){
			$result = $EventComment->where('id='.$ecids[$i])->delete();
			if(!$result){
				$this->ajaxReturn('', '', 0);
			}
		}
		$this->ajaxReturn('', '', 1);
	}

	public function deleteCommodityComment(){
		$ccids = I('param.ccids');
		$CommodityComment = M('CommodityComment');
		for($i=0; $i<count($ccids); $i++){
			$result = $CommodityComment->where('id='.$ccids[$i])->delete();
			if(!$result){
				$this->ajaxReturn('', '', 0);
			}
		}
		$this->ajaxReturn('', '', 1);
	}

	public function deleteAnswerReply(){
		$rids = I('param.rids');
		$AnswerReply = M('AnswerReply');
		for($i=0; $i<count($rids); $i++){
			$result = $AnswerReply->where('id='.$rids[$i])->delete();
			if(!$result){
				$this->ajaxReturn('', '', 0);
			}
		}
		$this->ajaxReturn('', '', 1);
	}

	public function invite(){
		$this->assign("current", "invite");
		$Invite = M('Invite');

		$invites = $Invite->where("invited=0")->select();
		$this->assign("invites", $invites);
		$this->display("invite");
	}

	public function createInvite(){
		$count = I('param.count');
		$Invite = M('Invite');

		for($i=0; $i<$count; $i++){
			$data['content'] = createKey(32);
			$data['invited'] = 0;
			$result = $Invite->add($data);
			if(!$result){
				$this->ajaxReturn('', '', 0);
			}
		}
		$this->ajaxReturn('', '', 1);
	}

	public function deleteInvite(){
		$iids = I('param.iids');
		$Invite = M('Invite');
		$data['invited'] = 1;
		for($i=0; $i<count($iids); $i++){
			$result = $Invite->where('id='.$iids[$i])->save($data);
			if(!$result){
				$this->ajaxReturn('', '', 0);
			}
		}
		$this->ajaxReturn('', '', 1);
	}

	public function recommendEvent(){
		$Event = M('Event');
		$EventRecommend = M('EventRecommend');
		$events = $Event->where('recommended=0')->order("create_time desc")->select();
		$recommendEvents = $EventRecommend->select();
		$this->assign("events", $events);
		$this->assign("recommendevents", $recommendEvents);
		$this->assign("recommendcount", count($recommendEvents));
		$this->assign('current', 'recommendevent');
		$this->display("recommendevent");
	}

	public function addRecommendEvent(){
		$eids = I('param.eids');
		$Event = M('Event');
		$EventRecommend = M('EventRecommend');
		$recommendEvents = $EventRecommend->select();
		if(count($eids) > 4){
			$this->ajaxReturn('', '', -1);
		}
		if(count($recommendEvents) >= 4){
			$this->ajaxReturn('', '', -1);
		}
		if(count($eids)+count($recommendEvents) > 4){
			$this->ajaxReturn('', '', -1);
		}
		for($i=0; $i<count($eids); $i++){
			$updateData['recommended'] = 1;
			$Event->where('id='.$eids[$i])->save($updateData);
			$event = $Event->where('id='.$eids[$i])->find();
			$addData['e_id'] = $eids[$i];
			$addData['e_title'] = $event['title'];
			$result = $EventRecommend->add($addData);
			if(!$result){
				$this->ajaxReturn('', '', 0);
			}
		}
		$this->ajaxReturn('', '', 1);
	}

	public function deleteRecommendEvent(){
		$eids = I('param.eids');
		$Event = M('Event');
		$EventRecommend = M('EventRecommend');
		for($i=0; $i<count($eids); $i++){
			$updateData['recommended'] = 0;
			$Event->where('id='.$eids[$i])->save($updateData);
			$result = $EventRecommend->where('e_id='.$eids[$i])->delete();
			if(!$result){
				$this->ajaxReturn('', '', 0);
			}
		}
		$this->ajaxReturn('', '', 1);
	}

	public function recommendQuestion(){
		$Question = M('Question');
		$QuestionRecommend = M('QuestionRecommend');
		$questions = $Question->where('recommended=0')->order("create_time desc")->select();
		$recommendQuestions = $QuestionRecommend->select();
		$this->assign("questions", $questions);
		$this->assign("recommendquestions", $recommendQuestions);
		$this->assign("recommendcount", count($recommendQuestions));
		$this->assign('current', 'recommendquestion');
		$this->display("recommendquestion");
	}

	public function addRecommendQuestion(){
		$qids = I('param.qids');
		$Question = M('Question');
		$QuestionRecommend = M('QuestionRecommend');
		$recommendQuestions = $QuestionRecommend->select();
		if(count($qids) > 4){
			$this->ajaxReturn('', '', -1);
		}
		if(count($recommendQuestions) >= 4){
			$this->ajaxReturn('', '', -1);
		}
		if(count($qids)+count($recommendQuestions) > 4){
			$this->ajaxReturn('', '', -1);
		}
		for($i=0; $i<count($qids); $i++){
			$updateData['recommended'] = 1;
			$Question->where('id='.$qids[$i])->save($updateData);
			$question = $Question->where('id='.$qids[$i])->find();
			$addData['q_id'] = $qids[$i];
			$addData['q_title'] = $question['title'];
			$result = $QuestionRecommend->add($addData);
			if(!$result){
				$this->ajaxReturn('', '', 0);
			}
		}
		$this->ajaxReturn('', '', 1);
	}

	public function deleteRecommendQuestion(){
		$qids = I('param.qids');
		$Question = M('Question');
		$QuestionRecommend = M('QuestionRecommend');
		for($i=0; $i<count($qids); $i++){
			$updateData['recommended'] = 0;
			$Question->where('id='.$qids[$i])->save($updateData);
			$result = $QuestionRecommend->where('q_id='.$qids[$i])->delete();
			if(!$result){
				$this->ajaxReturn('', '', 0);
			}
		}
		$this->ajaxReturn('', '', 1);
	}

	public function user(){
		$User = M('User');
		$users = $User->order("id desc")->select();
		$this->assign("users", $users);
		$this->assign('current', 'user');
		$this->display("user");
	}

	public function deleteUser(){
		$uids = I('param.uids');
		$User = M('User');
		for($i=0; $i<count($uids); $i++){
			$result = $User->where('id='.$uids[$i])->delete();
			if(!$result){
				$this->ajaxReturn('', '', 0);
			}
		}
		$this->ajaxReturn('', '', 1);
	}
}
?>