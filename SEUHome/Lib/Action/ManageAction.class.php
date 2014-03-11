<?php
class ManageAction extends Action {
	public function _initialize(){
		$userId = session("userId");
		if($userId != 7){
			$this->redirect("/");
		}
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
		for($i=0; $i<count($aids); $i++){
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
}
?>