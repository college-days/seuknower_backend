<?php
// 本类由系统自动生成，仅供测试用途
class MarketAction extends Action {
	public function _initialize(){
		if(isset($_SESSION['userId'])){	
		}
		else{
			$account = cookie('account');
			$password = cookie('password');
			if(!empty($account) && !empty($password)){
				$User = M('User');
				$map['account'] = $account;
				$map['password'] = $password;
				$result = $User->where($map)->find();
				cookie('account',$result['account'],864000);
				cookie('password',$result['pwd'],864000);
				session('userId',$result['id']);
				session('account',$result['account']);
				session('userName',$result['name']);
				session('icon',$result['icon']);
				if($result['is_group']){
					session('isGroup',1);
				}
				else{
					session('isGroup',0);
				}
			}
		}
	}

    public function index(){
    	$category = I('param.cate');

		if(!$category){
			$category = "全部";
		}
		
		$Commodity = M('Commodity');
		$map['onsale'] = 1;
		if($category != "全部"){
			$map['category'] = $category;
		}
		
		// 查询满足要求的总记录数 $map表示查询条件	
		$count = $Commodity->where($map)->count();
		$pageCount = ceil($count/16);
		if(I('param.id')){
			$page = I('param.id');
			if($page > $pageCount) $page = $pageCount;
		}
		else{
			$page = 1;
		}
		$start = ($page-1)*16;
		
		$commodityInfo = $Commodity->where($map)->order('create_time desc')->limit($start.',16')->select();

		if(count($commodityInfo) > 0){
			$User = M('User');
			for($i=0; $i<count($commodityInfo); $i++){
				$filename = $commodityInfo[$i]["picture"];
				//必须得是绝对路径
				$path = "E:/wamp/www";
				$dest = $path.$filename;
				if(!file_exists($dest)){
					$commodityInfo[$i]["picture"] = "__IMAGE__/market/goods_01.jpg";
				}
				$result = $User->find($commodityInfo['u_id']);
				if($result['sex'] == '男'){
					$commodityInfo[$i]['u_sex'] = "male";
				}else if($result['sex'] == '女'){
					$commodityInfo[$i]['u_sex'] = "female";
				}else{
					$commodityInfo[$i]['u_sex'] = "none";
				}
				$commodityInfo[$i]["u_name"] = $result["name"];
				$commodityInfo[$i]["u_icon"] = $result["icon"];
				$commodityTitle = $commodityInfo[$i]["title"];
				if(mb_strlen($commodityTitle, 'utf-8') > 10){
					//$commodityInfo[$i]["title"] = 
					$commodityTitle = mb_substr($commodityTitle, 0, 15, 'utf-8');
					$commodityInfo[$i]["title"] = $commodityTitle."...";
				}
				$commodityIntro = $commodityInfo[$i]["intro"];
				if(mb_strlen($commodityIntro, 'utf-8') > 10){
					//$commodityInfo[$i]["title"] = 
					$commodityIntro = mb_substr($commodityIntro, 0, 15, 'utf-8');
					$commodityInfo[$i]["intro"] = $commodityIntro."...";
				}	
			}
		}

		if(count($commodityInfo) == 16){
			$isFull = 1;
		} 
		else{
			$isFull = 0;	
		} 
		
		$this->assign('commoditys',$commodityInfo);
		$this->assign('count',$count);
		$this->assign('is_full',$isFull);
		$this->assign('curr_page',$page);
		$this->assign('page_count',$pageCount);
		$this->assign('category',$category);
		
    	$this->display('index');
    }
}
?>