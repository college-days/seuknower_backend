<?php
import("@.Action.CommonUtil");
class SearchAction extends Action {
	public function _initialize(){
		$util = new CommonUtil();
        $util->autologin();
	}

    public function index(){
    	$query = $_GET['query'];
    	$this->assign('query', $query);
    	$this->display('index');
    }
}
?>