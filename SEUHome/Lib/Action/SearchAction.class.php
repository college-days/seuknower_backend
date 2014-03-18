<?php
import("@.Action.CommonUtil");
class SearchAction extends Action {
	public function _initialize(){
		$util = new CommonUtil();
        $util->autologin();
	}

	public function _empty(){
		$this->display("Public:404");
	}

    public function index(){
    	$query = $_GET['query'];
    	$this->assign('query', $query);
    	$this->display('index');
    }
}
?>