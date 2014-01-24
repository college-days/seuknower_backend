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
}
?>