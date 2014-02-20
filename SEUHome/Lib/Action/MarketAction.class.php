<?php
import("@.Action.CommonUtil");
class MarketAction extends Action {
	public function _initialize(){
		$util = new CommonUtil();
		$util->autologin();
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
				$commodityInfo[$i]["intro"] = $util->sub_string($commodityInfo[$i]["intro"], 16);

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

		//for message notify part
		/*$deleteModel = new Model();
		$deleteResult = $deleteModel->execute('delete from seu_commodity_message where c_id='.$id.' and u_id='.session('userId'));

		$model = new Model();
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

		$map['u_id'] = $result['u_id'];
		$relateCommodities = $Commodity->where($map)->select();

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
		$this->assign('picture',$picture);
		$this->assign('picture_count',count($picture));
		
		$Model = M();
		$count = $Model->table('seu_commodity_comment')->where("c_id = $id")->count();
		$comments = $Model->table('seu_commodity_comment comment,seu_user user')->field('comment.id,comment.u_id as user_id,comment.content,comment.create_time,user.name as user_name,user.icon as icon')->where("comment.c_id = $id AND comment.u_id = user.id")->order('comment.create_time')->limit(10)->select();
		
		for($i=0; $i<count($comments); $i++){
			$comments[$i]['content'] = htmlspecialchars_decode($comments[$i]['content']);
		}

		$this->assign('comments',$comments);
		$this->assign('comment_count',$count);

    	$this->display('detail');
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
			$commodityMsgModel = new Model('CommodityMessage');
			$messageResult = $commodityModel->query('select * from seu_commodity_comment where c_id='.I('param.commodity_id').' and u_id='.session('userId'));

			if($messageResult == null){
				$messageData['c_id'] = I('param.commodity_id');
				$messageData['u_id'] = $commodityuid;
				$messageData['title'] = $commodityTitle;
				$messageData['comment_count'] = array('exp', 'comment_count+1');
				$commodityMsgModel->add($messageData);
			}else{
				$messageData['comment_count'] = array('exp', 'comment_count+1');
				$commodityMsgModel->where('c_id='.I('param.commodity_id').' and u_id='.$commodityuid)->save($messageData);
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
    	$this->display('new');
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
		//$cId = $Commodity->add($data);
		//dump($data);
		$id = $data['id'];
		$Commodity->save($data);
		if(I('param.rawpath')){
			$Picture = M('CommodityPicture');
			$pdata['c_id'] = $cId;
			$pdata['create_time'] = time();
			$pdata['picture'] = I('param.rawpath');
			$Picture->add($pdata);
		}
		//dump($data);
		$this->redirect("/market/commodity/$id");
	}
}
?>