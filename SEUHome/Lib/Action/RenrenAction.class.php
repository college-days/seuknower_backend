<?php
class RenrenAction extends Action {
	public function index(){
		$pid = I('param.pid');
		$cid = I('param.cid');

		if(!$pid){
			$pid = 1;
		}
		if(!$cid){
			$cid = 1;
		}

		$Province = M('Province');
		$College = M('College');
		$Department = M('Department');
		$provinces = $Province->select();
		$colleges = $College->where("PROID=".$pid)->select();
		$this->assign('provinces', $provinces);
		$this->display('index');
	}
}
?>