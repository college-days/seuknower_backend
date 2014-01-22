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
		$Commodity = M('Commodity');

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

		/*$add['id'] = $id;
		$add['click_count'] = array('exp','click_count+1');
		$Commodity->save($add);*/
		
		$result = $Commodity->find($id);
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
		
		$this->assign('comments',$comments);
		$this->assign('comment_count',$count);

    	$this->display('detail');
    }
}
?>