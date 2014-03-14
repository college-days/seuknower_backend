<?php
import("@.Action.CommonUtil");
class MarketAction extends Action {
	public function _initialize(){
		$util = new CommonUtil();
		$util->autologin();
		$this->assign('current', 'market');
	}

    public function index(){
    	$category = I('param.category');

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

		$util = new CommonUtil();

		if(count($commodityInfo) > 0){
			$User = M('User');
			for($i=0; $i<count($commodityInfo); $i++){
				if(!$util->exists_file($commodityInfo[$i]["picture"])){
					$commodityInfo[$i]["picture"] = "__IMAGE__/market/goods_01.jpg";
				}	

				$result = $User->find($commodityInfo[$i]['u_id']);
				$commodityInfo[$i]["u_sex"] = $util->filter_sex($result["sex"]);

				$commodityInfo[$i]["u_name"] = $result["name"];
				$commodityInfo[$i]["u_icon"] = $result["icon"];
				$commodityInfo[$i]["title"] = $util->sub_string($commodityInfo[$i]["title"], 15);
				$commodityInfo[$i]["intro"] = $util->sub_string(htmlspecialchars_decode($commodityInfo[$i]["intro"]), 16);

			}
		}

		if(count($commodityInfo) == 16){
			$isFull = 1;
		} 
		else{
			$isFull = 0;	
		} 
		
		$selledmap['onsale'] = 0;
		$selledcount = $Commodity->where($selledmap)->count();

		$this->assign('selledcount', $selledcount);
		$this->assign('commoditys',$commodityInfo);
		$this->assign('commodityscount', count($commodityInfo));
		$this->assign('count',$count);
		$this->assign('is_full',$isFull);
		$this->assign('curr_page',$page);
		$this->assign('page_count',$pageCount);
		$this->assign('category',$category);
		
    	$this->display('index');
    }

    public function detail(){
		$id = I('param.id');
		$userId = session('userId');
		$Commodity = M('Commodity');

		//增加浏览数
		$add['id'] = $id;
		$add['click_count'] = array('exp','click_count+1');
		$Commodity->save($add);

		//for message notify part，浏览过之后就要把和当前session有关的message都从db中clear掉啦
		$deleteModel = new Model();
		$deleteResult = $deleteModel->execute('delete from seu_commodity_message where c_id='.$id.' and u_id='.session('userId'));
		$CommodityAt = M('CommodityAt');
		$CommodityAt->where('c_id='.$id.' and u_id='.session('userId'))->delete();
		$CommodityAt->where('c_id='.$id.' and u_id=0')->delete();

		//现在不用存在session里了，所以不用再次查询更新了
		/*$model = new Model();
		$questionMessageResult = $model->query('select * from seu_question_message where u_id='.session('userId'));
		$questionMessageCount = count($questionMessageResult);

		$eventMessageResult = $model->query('select * from seu_event_message where u_id='.session('userId'));
		$eventMessageCount = count($eventMessageResult);

		$commodityMessageResult = $model->query('select * from seu_commodity_message where u_id='.session('userId'));
		$commodityMessageCount = count($commodityMessageResult);

		$messageCount = $questionMessageCount + $eventMessageCount + $commodityMessageCount;

		session('messageCount', $messageCount);

		session('questionMessageResult', $questionMessageResult);
		session('eventMessageResult', $eventMessageResult);
		session('commodityMessageResult', $commodityMessageResult);*/

		$result = $Commodity->find($id);

    	if($_SESSION['userId'] == $result['u_id']){
    		$this->assign('me', '1');
    	}else{
    		$this->assign('me', '0');
    	}

		//prevent xss
		$result['intro'] = htmlspecialchars_decode($result['intro']);

		//判断当前用户是不是已经点过赞了
		$map['c_id'] = $id;
		$map['u_id'] = $userId;
		$CommodityLike = M('CommodityLike');
		if($CommodityLike->where($map)->find()){
			$result['currentUserLike'] = 1;
		}
		else{
			$result['currentUserLike'] = 0;
		}

		$relateCommodities = $Commodity->where("u_id=".$result['u_id']." and id!=".$id)->select();

		$this->assign('relatecommodities', $relateCommodities);
		$this->assign('relatecommoditycount', count($relateCommodities));

		$User = M('User');
		$info = $User->find($result['u_id']);
		$result['u_name'] = $info['name'];
		$result['u_intro'] = $info['intro'];
		$result['u_icon'] = $info['icon'];
		
		$util = new CommonUtil();
		$result["u_sex"] = $util->filter_sex($info["sex"]);

		$filename = $result["picture"];
		
		if(!$util->exists_file($result["picture"])){
			$result["picture"] = "__IMAGE__/market/goods_01.jpg";
		}

		if(!$result['phone']){
			$result['phone'] = $info['phone'];
		}
		$Picture = M('CommodityPicture');
		$picture = $Picture->field('picture')->where("c_id = $id")->select();
		$this->assign('commodity',$result);
		$this->assign('pictures',$picture);
		$this->assign('picture_count',count($picture));
		
		$Model = M();
		$count = $Model->table('seu_commodity_comment')->where("c_id = $id")->count();
		$comments = $Model->table('seu_commodity_comment comment,seu_user user')->field('comment.id,comment.u_id as user_id,comment.content,comment.create_time,user.name as user_name,user.icon as icon')->where("comment.c_id = $id AND comment.u_id = user.id")->order('comment.create_time')->select();
		
		for($i=0; $i<count($comments); $i++){
			$comments[$i]['content'] = htmlspecialchars_decode($comments[$i]['content']);
		}

		$this->assign('comments',$comments);
		$this->assign('comment_count',$count);

    	$this->display('detail_new');
    }

    public function addLike(){
		$userId = session('userId');
		if(isset($userId)){
			$data['u_id'] = $userId;
			$data['c_id'] = I('param.id');
			$data['create_time'] = time();
			$CommodityLike = M('CommodityLike');
			$CommodityLike->add($data);
			
			$Commodity = M('Commodity');
			$add['id'] = I('param.id');
			$add['like_count'] = array('exp','like_count+1');
			$Commodity->save($add);
			$this->ajaxReturn('', '', 1);
		}
		else{
			//should login first should comeout a view
			$this->ajaxReturn('', '', 0);
		}
    }

    public function cancelLike(){
		$userId=session('userId');
		if(isset($userId)){
			$map['u_id'] = $userId;
			$map['c_id'] = I('param.id');
			
			$CommodityLike = M('CommodityLike');
			$CommodityLike->where($map)->delete();
			$Commodity = M('Commodity');
			$data['id'] = I('param.id');
			$data['like_count'] = array('exp','like_count-1');
			$Commodity->save($data);
			$this->ajaxReturn('', '', 1);
		}
		else{
			$this->ajaxReturn('', '', 0);
		}
	}

	public function addComment(){
		if(isset($_SESSION['userId'])){
			$Comment = M('CommodityComment');
			$data['c_id'] = I('param.commodity_id');
			$data['u_id'] = session('userId');
			$data['content'] = I('param.content');
			$data['create_time'] = time();

			$commodityModel = new Model();
			$commodityResult = $commodityModel->query('select u_id, title from seu_commodity where id='.I('param.commodity_id'));
			$commodityuid = $commodityResult[0]['u_id'];
			$commodityTitle = $commodityResult[0]['title'];

			//评论商品的消息提示
			$commodityMsgModel = new Model('CommodityMessage');
			//$messageResult = $commodityModel->query('select * from seu_commodity_comment where c_id='.I('param.commodity_id').' and u_id='.session('userId'));
			$messageResult = $commodityMsgModel->where('c_id='.I('param.commodity_id').' and from_id='.session('userId'))->select();

			if($messageResult == null){
				$messageData['c_id'] = I('param.commodity_id');
				$messageData['u_id'] = $commodityuid;
				$messageData['from_id'] = session("userId");
				$messageData['title'] = $commodityTitle;
				$messageData['comment_count'] = array('exp', 'comment_count+1');
				$commodityMsgModel->add($messageData);
			}else{
				$messageData['comment_count'] = array('exp', 'comment_count+1');
				$commodityMsgModel->where('c_id='.I('param.commodity_id').' and u_id='.$commodityuid.' and from_id='.session('userId'))->save($messageData);
			}

			$CommodityAt = M('CommodityAt');
			$atMessageResult = $CommodityAt->where('c_id='.I('param.commodity_id').' and u_id='.I('param.at_id').' and from_id='.session('userId'))->select();
			if($atMessageResult == null){
				$atMessageData['c_id'] = I('param.commodity_id');
				$atMessageData['u_id'] = I('param.at_id');
				$atMessageData['from_id'] = session("userId");
				$CommodityAt->add($atMessageData);
			}

			$Commodity = M('Commodity');
			$addCommentData['id'] = I('param.commodity_id');
			$addCommentData['comment_count'] = array('exp','comment_count+1');
			$Commodity->save($addCommentData);

			$result = $Comment->add($data);
			if ($result < 1) {
				$this->ajaxReturn('', '', 0);
			}

			$result['user_id'] = session('userId');
			$result['user_name'] = session('userName');
			$result['icon'] = session('icon');
			$result['content'] = I('param.content');
			$this -> ajaxReturn($result, 'success', 1);
		}
		else{
			$this -> ajaxReturn('', '请登录', -1);
		}
		
	}

    public function newCommodity(){
		if(isset($_SESSION['userId'])){
    		$this->display('new');
    	}else{
    		$this->redirect("/login");
    	}
    }

	//上传图片
	public function uploadPicture(){
		import('@.ORG.UploadFile');
		$upload = new UploadFile();								 	// 实例化上传类
		$upload->maxSize  = 3145728 ;							 	// 设置附件上传大小
		$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');	// 设置附件上传类型
		$upload->savePath =  './Uploads/Images/Market/Picture/Raw/';	// 设置附件上传目录	
		$upload->saveRule= "uniqid";								//文件保存规则
		$upload->thumb = true; 										//设置需要生成缩略图，仅对图像文件有效
		$upload->thumbPrefix = 'r_';  							//设置需要生成缩略图的文件后缀
        $upload->imageClassPath = '@.ORG.Image';					
        $upload->thumbMaxWidth = '390'; 						//设置缩略图最大宽度 
        $upload->thumbMaxHeight = '260';						//设置缩略图最大高度 
		$upload->thumbRemoveOrigin = true; 
		 
		if(!$upload->upload()) {// 上传错误提示错误信息
			$this->error($upload->getErrorMsg());
		}else{// 上传成功
			$info = $upload->getUploadFileInfo();
			$data['path'] = $info[0]['savepath'].'r_'.$info[0]['savename'];
			$imageSize = getimagesize($data['path']);
			$data['width'] = $imageSize[0];
			$data['height'] = $imageSize[1];
			$this->ajaxReturn($data, 'success', 1);
		}
	}

	//裁剪图片并生成缩略图
	public function thumbPicture(){
		$rawPath = I('param.imagepath');
		$thumbPath = str_replace('Raw','Thumb',$rawPath);
		$thumbPath = str_replace('r_','t_',$thumbPath);
		thumb($rawPath,$thumbPath,I('param.width'),I('param.height'),I('param.iconx'),I('param.icony'));
		$data['rawpath'] = $rawPath;
		$data['thumbpath'] = $thumbPath;
		$this->ajaxReturn($data, 'success', 1);
	}

	public function addCommodity(){
		$Commodity = M('Commodity');
		$data['title'] = I('param.title');
		$data['create_time'] = time();
		$data['cost'] = I('param.cost');
		$data['location'] = I('param.location');
		$data['intro'] = I('param.intro');
		$data['u_id'] = session('userId');
		$data['phone'] = I('param.phone');
		$data['status'] = I('param.status');
		$getittime = I('param.gettime');
		if(!$getittime){
			$data['getittime'] = "0";
		}else{
			$data['getittime'] = strtotime(I('param.gettime')." 00:00:00");
		}
		
		$User = M('User');
		$result = $User->find(session('userId'));
		if(!$result['phone']){
			$update['id'] = session('userId');
			$update['phone'] = I('param.phone');
			$User->save($update);
		}
		
		if(I('param.thumbpath')){
			$data['picture'] = I('param.thumbpath');
		}
		if(I('param.tag_cate')){
			$data['category'] = I('param.tag_cate');
			$data['tag'] = I('param.tag_cate');
		}
		if(I('param.catalog')){
			$data['tag'] = I('param.catalog');
		}
		$cId = $Commodity->add($data);
		
		if(I('param.rawpath')){
			$Picture = M('CommodityPicture');
			$pdata['c_id'] = $cId;
			$pdata['create_time'] = time();
			$pdata['picture'] = I('param.rawpath');
			$Picture->add($pdata);
		}
		
		$this->redirect("/market/more_picture/$cId");
		
	}

	public function morePicture(){
		$id = I('param.id');
		$Commodity = M('Commodity');
		$result = $Commodity->find($id);
		$User = M('User');
		$info = $User->find($result['u_id']);
		$result['u_name'] = $info['name'];
		$result['phone'] = $info['phone'];
		$Picture = M('CommodityPicture');
		$map['c_id'] = $id;
		$picture = $Picture->where($map)->find();
		$result['picture'] = $picture['picture'];
		$this->assign('commodity',$result);
		
		$this->display('morepicture');
	}	

	public function uploadify(){
		$verifyToken = md5($_POST['timestamp']);
		if (!empty($_FILES) && $_POST['token'] == $verifyToken) {
			import('@.ORG.UploadFile');
			$upload = new UploadFile();// 实例化上传类
			
			$upload->maxSize  = 3145728 ;							 	// 设置附件上传大小
			$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');	// 设置附件上传类型
			$upload->savePath =  './Uploads/Images/Market/Picture/Raw/';	// 设置附件上传目录	
			$upload->saveRule= "uniqid";								//文件保存规则
			$upload->thumb = true; 										//设置需要生成缩略图，仅对图像文件有效
			$upload->thumbPrefix = 'r_';  							//设置需要生成缩略图的文件后缀
	        $upload->imageClassPath = '@.ORG.Image';					
	        $upload->thumbMaxWidth = '390'; 						//设置缩略图最大宽度 
	        $upload->thumbMaxHeight = '260';						//设置缩略图最大高度 
			$upload->thumbRemoveOrigin = true; 
			if(!$upload->upload()) {// 上传错误提示错误信息
				echo $upload->getErrorMsg();
			}else{// 上传成功 获取上传文件信息
				$info =  $upload->getUploadFileInfo();
				echo $info[0]['savepath'].'r_'.$info[0]['savename'];
			}
		}
    }

    public function savePicture(){
		$id = I('param.id');
		$path = I('param.path');
		$Picture = M('CommodityPicture');
		for($i=0; $i<count($path); $i++){
			if($path[$i]){
				$data['c_id'] = $id;
				$data['picture'] = $path[$i];
				$data['create_time'] = time();
				$Picture->add($data);
			}
		}
		$this->ajaxReturn($id, '', 1);
	}

	public function getsamecate(){
		$id = I('param.id');
		$Commodity = M('Commodity');

		$result = $Commodity->find($id);
		$sameCateCommodities = $Commodity->where('id!='.$id." and category='".$result['category']."' and onsale=1")->field('id,picture,title')->order('create_time desc')->limit(5)->select();

		$util = new CommonUtil();

		for($i=0; $i<count($sameCateCommodities); $i++){
			$sameCateCommodities[$i]['title'] = $util->sub_string($sameCateCommodities[$i]['title'], 9);
		}

		$this->ajaxReturn($sameCateCommodities, '', 1);
	}

	public function modifyCommodity(){
		$id = I('param.id');
		$Commodity = M("Commodity");
		$commodityInfo = $Commodity->find($id);
		if($commodityInfo['u_id'] == session('userId')){
			$this->assign('commodity', $commodityInfo);
			$Picture = M('CommodityPicture');
			$map['c_id']=$id;
			$picture = $Picture->where($map)->select();
			$this->assign('picture',$picture);
			if(!$commodityInfo['phone']){
				$User = M('User');
				$user = $User->find(session('userId'));
				$this->assign('phone',$user['phone']);
			}
			else{
				$this->assign('phone',$commodityInfo['phone']);
			}
			$this->display('modify');
		}
		else{
			$this->error("您无操作权限");
		}
		
	}

	public function saveCommodity(){
		$Commodity = M('Commodity');
		$data['id'] = I('param.id');
		$data['title'] = I('param.title');
		//$data['create_time'] = time();
		$data['cost'] = I('param.cost');
		$data['location'] = I('param.location');
		$data['intro'] = I('param.intro');
		$data['phone'] = I('param.phone');
		$data['u_id'] = session('userId');
		$data['status'] = I('param.status');
		$getittime = I('param.gettime');
		if(!$getittime){
			$data['getittime'] = "0";
		}else{
			$data['getittime'] = strtotime(I('param.gettime')." 00:00:00");
		}

		$User = M('User');
		$result = $User->find(session('userId'));
		if(!$result['phone']){
			$update['id'] = session('userId');
			$update['phone'] = I('param.phone');
			$User->save($update);
		}

		if(I('param.thumbpath')){
			$data['picture'] = I('param.thumbpath');
		}
		if(I('param.tag_cate')){
			$data['category'] = I('param.tag_cate');
		}
		$id = $data['id'];
		$Commodity->save($data);
		if(I('param.rawpath')){
			$Picture = M('CommodityPicture');
			$pdata['c_id'] = $cId;
			$pdata['create_time'] = time();
			$pdata['picture'] = I('param.rawpath');
			$Picture->add($pdata);
		}
		$this->redirect("/market/commodity/$id");
	}

	public function deleteCommodity(){
		$cid = I('param.cid');
		$Commodity = M('Commodity');
		$map['onsale'] = 0;
		$result = $Commodity->where("id=".$cid)->save($map);
		if(!$result){
			$this->ajaxReturn('', '', 0);
		}else{
			$this->ajaxReturn('', '', 1);
		}
	}
}
?>