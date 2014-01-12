<?php
// 本类由系统自动生成，仅供测试用途
class UserAction extends Action {
    public function index(){
    	$u_id = I('param.id');
    	$User = M('User');
    	$userInfo = $User->find($u_id);
    	$this->assign('user', $userInfo);
    	$this->display('user_center');
    }
}
?>