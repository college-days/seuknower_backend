<?php
import("@.Action.CommonUtil");
class IndexAction extends Action {
	public function _initialize(){
		$util = new CommonUtil();
		$util->autologin();
	}

    public function index(){
    	$util = new CommonUtil();
    	$Question = M('Question');
    	$recommendQuestions = $Question->where("recommended=1")->select();
    	if(!$recommendQuestions){
    		$hotQuestions = $Question->order('click_count desc')->limit(4)->select();
    	}elseif(count($recommendQuestions) == 4){
    		$hotQuestions = $recommendQuestions;
    	}else{
    		$rest = 4-count($recommendQuestions);
    		$questions = $Question->order('click_count desc')->limit($rest)->select();
    		$hotQuestions = array_merge($recommendQuestions, $questions);
    	}
		
		for($i=0; $i<count($hotQuestions); $i++){
			$hotQuestions[$i]['title'] = $util->sub_string($hotQuestions[$i]['title'], 15);
		}

    	$Model = M();
    	$User = M('User');
    	$Event = M('Event');
    	$recommendEvents = $Event->where("recommended=1")->select();
    	if(!$recommendEvents){
    		$hotEvents = $Model->table('seu_event event')->order('click_count desc')->limit(4)->select();  		
    	}elseif(count($recommendEvents) == 4){
    		$hotEvents = $recommendEvents;
    	}else{
    		$rest = 4-count($recommendEvents);
    		$events = $Model->table('seu_event event')->order('click_count desc')->limit($rest)->select();
    		$hotEvents = array_merge($recommendEvents, $events);
    	}
    	
    	for($i=0; $i<count($hotEvents); $i++){
			$startTime = explode(" ",date("Y年m月d日 H:i:s",$hotEvents[$i]['start_time']));	
			$endTime = explode(" ",date("Y年m月d日 H:i:s",$hotEvents[$i]['end_time']));
			unset($hotEvents[$i]['start_time']);
			unset($hotEvents[$i]['end_time']);
			if($startTime[0] == $endTime[0]){
				$hotEvents[$i]['time'] = substr($startTime[0],7)." ".substr($startTime[1],0,5)."-".substr($endTime[1],0,5);
			}
			else{
				$hotEvents[$i]['time'] = substr($startTime[0],7)."~".substr($endTime[0],7);
			}

			$organizer = $User->where('id='.$hotEvents[$i]['u_id'])->find();
			$hotEvents[$i]['organizer'] = $organizer['name'];
		}
		
		for($i=0; $i<count($hotEvents); $i++){
			if(!$util->exists_file($hotEvents[$i]["poster"])){
				$hotEvents[$i]["poster"] = "__IMAGE__/event/act5.jpg";
			}
		}
		$this->assign('hotquestions', $hotQuestions);
		$this->assign('hotevents', $hotEvents);
    	$this->display('index');
    }
}
?>