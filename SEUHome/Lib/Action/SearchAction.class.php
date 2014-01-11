<?php
// 本类由系统自动生成，仅供测试用途
class SearchAction extends Action {
    public function index(){
    	//echo I('param.query');
    	//echo $_GET['query'];
    	$query = $_GET['query'];
    	$this->assign('query', $query);
    	$this->display('index');
    }
}
?>