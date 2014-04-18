<?php
class RenrenAction extends Action {
	public function index(){
		$this->display('index');
	}

	public function getProvinces(){
		$Province = M('Province');
		$provinces = $Province->select();
		$this->ajaxReturn($provinces, '', 1);
	}

	public function getCollege(){
		$pid = I('param.pid');
		$College = M('College');
		$colleges = $College->where("PROID=".$pid)->select();
		$this->ajaxReturn($colleges, '', 1);
	}

	public function getDepartment(){
		$cid = I('param.cid');
		$Department = M('Department');
		$departments = $Department->where("COLID=".$cid)->select();
		$this->ajaxReturn($departments, '', 1);
	}

	public function queryTotal(){
		$pid = I('param.pid');
		$cid = I('param.cid');
		$did = I('param.did');
		$Province = M('Province');
		$College = M('College');
		$Department = M('Department');

		$pResult = $Province->where('PROID='.$pid)->find();
		$pName = $pResult['PRONAME'];

		$cResult = $College->where('COLID='.$cid)->find();
		$cName = $cResult['COLNAME'];

		$dResult = $Department->where('id='.$did)->find();
		$dName = $dResult['DEPNAME'];

		$result = getRenrenQuery($cName, $dName, $cid);
		$this->ajaxReturn($result, '', 1);
	}
}
?>