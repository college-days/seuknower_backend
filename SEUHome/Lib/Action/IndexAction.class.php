<?php
import("@.Action.CommonUtil");
class IndexAction extends Action {
	public function _initialize(){
		$util = new CommonUtil();
		$util->autologin();
	}

    public function index(){
    	$this->display('index');
    }
}
?>