<?php
import("@.Action.CommonUtil");
class EventAction extends Action {
	public function _initialize(){
		$util = new CommonUtil();
		$util->autologin();
	}
	
    public function index(){
    	$tag = I("param.tag");
		$time = I("param.time");
		if(!$tag) $tag = "全部";
		if(!$time) $time = "全部";
		$type = $tag;
		
		$Model = M();	
		$hots = $Model->table('seu_event event')->field('event.id,event.poster,event.title')->order('join_count desc')->limit(5)->select();

		$this->assign("hots", $hots);

		if($tag != "全部"){
			//$sql .= " AND event.tag LIKE '%".$tag."%' ";
			// $sql = "event.tag = '".$tag."'";
			$sql = "event.category = '".$tag."'";
		}else{
			$sql = "";
		}

		if($time == "今天") {
			$sql .=  ' AND event.start_time < '.strtotime('tomorrow').' AND ';
			$sql .=  ' event.end_time >='.strtotime('today');
		}
		elseif($time == "明天"){
			$sql .=  ' AND event.start_time < '.(strtotime('tomorrow')+86400).' AND ';
			$sql .=  'event.end_time >='.(strtotime('today')+86400);
		}
		elseif($time == "本周"){
			$sql .=  ' AND event.start_time < '.(strtotime('next sunday')+86400).' AND ';
			$sql .=  'event.end_time >='.(strtotime('today'));
		}

		$count = $Model->table('seu_event as event')->where($sql)->count();
		// 查询满足要求的总记录数 $map表示查询条件
		if($count){
			if($sql){
				$sql .= ' AND event.u_id = user.id ';
			}
			else{
				$sql = 'event.u_id = user.id'; 
			}
			
			//$pageCount = ceil($count/10);
			// $pageCount = ceil($count/5);
			$pageCount = ceil($count/6);
			if(I('param.id')){
				$page = I('param.id');
				if($page > $pageCount) $page = $pageCount;
			}
			else{
				$page = 1;
			}
			//$start = ($page-1)*10;	
			$start = ($page-1)*6;
			
			//$events = $Model->table('seu_event event, seu_user user')->field('event.id,event.u_id, event.title, event.start_time, event.end_time, event.cost, event.location, event.join_count, event.interest_count, event.poster, user.is_group, user.name as organizer')->order('event.create_time desc')->limit($start.',10')->where($sql)->select();

			$events = $Model->table('seu_event event, seu_user user')->field('event.id,event.u_id, event.title, event.start_time, event.end_time, event.cost, event.location, event.join_count, event.interest_count, event.poster, user.is_group, user.name as organizer')->order('event.create_time desc')->limit($start.',6')->where($sql)->select();
			for($i=0; $i<count($events); $i++){
				$startTime = explode(" ",date("Y年m月d日 H:i:s",$events[$i]['start_time']));	
				$endTime = explode(" ",date("Y年m月d日 H:i:s",$events[$i]['end_time']));
				unset($events[$i]['start_time']);
				unset($events[$i]['end_time']);
				if($startTime[0] == $endTime[0]){
					$events[$i]['time'] = substr($startTime[0],7)." ".substr($startTime[1],0,5)."-".substr($endTime[1],0,5);
				}
				else{
					$events[$i]['time'] = substr($startTime[0],7)."~".substr($endTime[0],7);
				}
			}
		}
		
		// 获取校园十大热门活动
		$nowTime = time();
		$eventTop10 = $Model->table('seu_event event')->where('status=1 AND end_time >'.$nowTime)->field('event.id,event.poster,event.title')->order('join_count desc')->limit(9)->select();
		// 获取校园网址列表-固定部分
		$SchoolUrl = M('SchoolUrl');
		$fixedUrlList = $SchoolUrl->where('status=1')->order('create_time ASC')->select();
		// 可变部分
		$uid = $this->_session('id');
		$map['status'] = 0;
		$map['u_id'] = $uid;
		$varUrlList = $SchoolUrl->where($map)->order('create_time ASC')->select();
		// 获取最新问题列表
		$qListNum = 6;
		$Question = M('Question');
		$qRecentList = $Question->order('create_time desc')->limit($qListNum)->select();
		
		$util = new CommonUtil();

		for($i=0; $i<count($events); $i++){
			if(!$util->exists_file($events[$i]["poster"])){
				$events[$i]["poster"] = "notexists";
			}
		}

		$this->assign('event_top10', $eventTop10);
		$this->assign('fixed_url_list', $fixedUrlList);
		$this->assign('var_url_list', $varUrlList);		
		$this->assign('question', $qRecentList);
		
		$this->assign('events', $events);
		$this->assign('eventscount', count($events));
		$this->assign('count', $count);
		
		$this->assign('curr_page', $page);
		$this->assign('page_count', $pageCount);
		$this->assign('type', $type);
		$this->assign('tag', $tag);
		$this->assign('time', $time);

		switch (count($events)) {
			case 6:
				$this->assign('leftevents', array($events[0], $events[1]));
				$this->assign('middleevents', array($events[2], $events[3]));
				$this->assign('rightevents', array($events[4], $events[5]));
				break;
			case 5:
				$this->assign('leftevents', array($events[0], $events[1]));
				$this->assign('middleevents', array($events[2], $events[3]));
				$this->assign('rightevents', array($events[4]));
				break;
			case 4:
				$this->assign('leftevents', array($events[0], $events[1]));
				$this->assign('middleevents', array($events[2], $events[3]));
				$this->assign('rightevents', array());
				break;
			case 3:
				$this->assign('leftevents', array($events[0], $events[1]));
				$this->assign('middleevents', array($events[2]));
				$this->assign('rightevents', array());
				break;
			case 2:
				$this->assign('leftevents', array($events[0], $events[1]));
				$this->assign('middleevents', array());
				$this->assign('rightevents', array());
				break;
			case 1:
				$this->assign('leftevents', array($events[0]));
				$this->assign('middleevents', array());
				$this->assign('rightevents', array());
				break;
			default:
				# code...
				break;
		}

    	$userId = session('userId');
    	$User = M('User');
    	$userInfo = $User->find($userId);

    	$util = new CommonUtil();
		$userInfo["sex"] = $util->filter_sex($userInfo["sex"]);

    	$this->assign('currentUser', $userInfo);

    	$map['u_id'] = $userId;
    	$Event = M('Event');
		$EventInterest = M('InterestEvent');
    	$interestEventIds = $EventInterest->field('e_id')->where($map)->select();
    	$interestEvents = array();
    	for($i=0; $i<count($interestEventIds); $i++){
    		$interestEvent = $Event->where('id='.$interestEventIds[$i]['e_id'])->find();
    		if(!empty($interestEvent)){
    			array_push($interestEvents, $interestEvent);
    		}
    	}

    	$EventJoin = M('JoinEvent');
    	$joinEventIds = $EventJoin->field('e_id')->where($map)->select();
    	$joinEvents = array();
    	for($i=0; $i<count($joinEventIds); $i++){
    		$joinEvent = $Event->where('id='.$joinEventIds[$i]['e_id'])->find();
    		if(!empty($joinEvent)){
    			array_push($joinEvents, $joinEvent);
    		}
    	}

		$this->assign('interestCount', count($interestEvents));
		$this->assign('joinCount', count($joinEvents));

		//for notify message
		//有时候会出现奇怪的bug，所以先把这两个session中的变量清空，其实之前不是奇怪的bug，是查询语句写错了，反正下面两个写着也不碍事就放着吧
		/*session('messageResult', null);
		session('messageCount', 0);*/
		//查询是否有新的消息，并放入session中便于header_b.html中的元素使用，放在此处就不用每次登陆才会看到消息提示了，而是下次进入网站的时候就可以看到消息提示
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

    	//$this->display('index');
    	$this->display('index_waterflow');
    }

    /*gcc*/
	public function load(){
		$tag = I("param.tag");
		$time = I("param.time");
		if(!$tag) $tag = "全部";
		if(!$time) $time = "全部";
		$type = $tag;
		
		$Model = M();	

		if($tag != "全部"){
			$sql = "event.category = '".$tag."'";
		}else{
			$sql = "";
		}

		if($time == "今天") {
			$sql .=  ' AND event.start_time < '.strtotime('tomorrow').' AND ';
			$sql .=  ' event.end_time >='.strtotime('today');
		}
		elseif($time == "明天"){
			$sql .=  ' AND event.start_time < '.(strtotime('tomorrow')+86400).' AND ';
			$sql .=  'event.end_time >='.(strtotime('today')+86400);
		}
		elseif($time == "本周"){
			$sql .=  ' AND event.start_time < '.(strtotime('next sunday')+86400).' AND ';
			$sql .=  'event.end_time >='.(strtotime('today'));
		}

		$count = $Model->table('seu_event as event')->where($sql)->count();
		if($count){
			if($sql){
				$sql .= ' AND event.u_id = user.id ';
			}
			else{
				$sql = 'event.u_id = user.id'; 
			}
			$pageCount = ceil($count/6);
			if(I('param.id')){
				$page = I('param.id');
				if($page > $pageCount) $page = $pageCount;
			}
			else{
				$page = 1;
			}
			$start = ($page-1)*6;

			$events = $Model->table('seu_event event, seu_user user')->field('event.id,event.u_id, event.title, event.start_time, event.end_time, event.cost, event.location, event.join_count, event.interest_count, event.poster, user.is_group, user.name as organizer')->order('event.create_time desc')->limit($start.',6')->where($sql)->select();
			
			for($i=0; $i<count($events); $i++){
				$startTime = explode(" ",date("Y年m月d日 H:i:s",$events[$i]['start_time']));	
				$endTime = explode(" ",date("Y年m月d日 H:i:s",$events[$i]['end_time']));
				unset($events[$i]['start_time']);
				unset($events[$i]['end_time']);
				if($startTime[0] == $endTime[0]){
					$events[$i]['time'] = substr($startTime[0],7)." ".substr($startTime[1],0,5)."-".substr($endTime[1],0,5);
				}
				else{
					$events[$i]['time'] = substr($startTime[0],7)."~".substr($endTime[0],7);
				}
			}
		}
		
		$util = new CommonUtil();
		for($i=0; $i<count($events); $i++){
			if(!$util->exists_file($events[$i]["poster"])){
				$events[$i]["poster"] = "notexists";
			}
		}
		//dump($events);
		if($events){
			$this->ajaxReturn($events,"",1);
		}
		else{
			$this->ajaxReturn("","",0);
		}
	}

    public function detail(){
    	$id = I('param.id');
    	$Event = M('Event');
    	$User = M('User');

		//about notify message
		$deleteModel = new Model();
		$deleteResult = $deleteModel->execute('delete from seu_event_message where e_id='.$id.' and u_id='.session('userId'));
		$EventAt = M('EventAt');
		$EventAt->where('e_id='.$id.' and u_id='.session('userId'))->delete();
		$EventAt->where('e_id='.$id.' and u_id=0')->delete();

    	//活动的点击数增加
    	$add['id'] = $id;
		$add['click_count'] = array('exp','click_count+1');
		$Event->save($add);

		$currentEvent = $Event->find($id);
		$user = $User->find($currentEvent['u_id']);
		$currentEvent['organizer'] = $user['name'];

		$startTime = explode(" ",date("Y年m月d日 H:i:s",$currentEvent['start_time']));	
		$endTime = explode(" ",date("Y年m月d日 H:i:s",$currentEvent['end_time']));
		//unset撤销对象之后就会将其置为null
		unset($currentEvent['start_time']);
		unset($currentEvent['end_time']);
		if($startTime[0] == $endTime[0]){
			$currentEvent['time'] = substr($startTime[0],7)." ".substr($startTime[1],0,5)."-".substr($endTime[1],0,5);
		}
		else{
			$currentEvent['time'] = substr($startTime[0],7)."~".substr($endTime[0],7);
		}

		//prevent xss
		$currentEvent['intro'] = htmlspecialchars_decode($currentEvent['intro']);

		$Model = M();
		$count = $Model->table('seu_event_comment')->where("e_id = ".$id)->count();
		//表联结
		$comments = $Model->table('seu_event_comment comment,seu_user user')->field('comment.id,comment.u_id as user_id,comment.content,comment.create_time,user.name as user_name,user.icon as icon')->where("comment.e_id = $id AND comment.u_id = user.id")->order('comment.create_time')->select();
		
		$util = new CommonUtil();

		if(!$util->exists_file($currentEvent["poster"])){
			$currentEvent["poster"] = "__IMAGE__/event/act5.jpg";
		}

		for($i=0; $i<count($comments); $i++){
			$comments[$i]['content'] = htmlspecialchars_decode($comments[$i]['content']);
		}

		$this->assign('event', $currentEvent);
		$this->assign('comments', $comments);
		$this->assign('comment_count', $count);

		//u_id是当前登录用户的id
		$map['u_id'] = session('userId');
		//e_id是活动的id
		$map['e_id'] = $id;
		$JoinEvent = M('JoinEvent');
		//判断当前登录用户是否参与了该活动
		if($JoinEvent->where($map)->find()){
			$this->assign('join',1);
		}
		else{
			$this->assign('join',0);
		}
		//判断当前登录用户是否关注了该活动
		$InterestEvent = M('InterestEvent');
		if($InterestEvent->where($map)->find()){
			$this->assign('interest',1);
		}
		else{
			$this->assign('interest',0);
		}

		$relatedEvents = $Event->where("category='".$currentEvent['category']."'")->order("create_time desc")->limit(10)->select();
		$this->assign('relatedevents', $relatedEvents);

    	$this->display('detail');
    }

    public function newEvent(){
    	if(isset($_SESSION['userId'])){
    		$this->display('new');
    	}else{
    		$this->redirect("/login");
    	}
    }

    public function addJoin(){
		$userId=session('userId');
		//判断是否登陆
		if(isset($userId)){
			$data['u_id'] = $userId;
			$data['e_id'] = I('param.e_id');
			$data['create_time'] = time();
			$JoinEvent = M('JoinEvent');
			$JoinEvent->add($data);
			$Event = M('Event');
			$add['id'] = I('param.e_id');
			$add['join_count'] = array('exp','join_count+1');
			$Event->save($add);
			$this->ajaxReturn('', '', 1);
		}
		else{
			$this->ajaxReturn('', '', 0);
		}
	}
	
	public function cancelJoin(){
		$userId=session('userId');
		if(isset($userId)){
			$map['u_id'] = $userId;
			$map['e_id'] = I('param.e_id');
			
			$JoinEvent = M('JoinEvent');
			$JoinEvent->where($map)->delete();
			$Event = M('Event');
			$data['id'] = I('param.e_id');
			$data['join_count'] = array('exp','join_count-1');
			$Event->save($data);
			$this->ajaxReturn('', '', 1);
		}
		else{
			$this->ajaxReturn('', '', 0);
		}
	}
	
	public function addInterest(){
		$userId=session('userId');
		if(isset($userId)){
			$data['u_id'] = $userId;
			$data['e_id'] = I('param.e_id');
			$data['create_time'] = time();
			$InterestEvent = M('InterestEvent');
			$InterestEvent->add($data);
			$Event = M('Event');
			$add['id'] = I('param.e_id');
			$add['interest_count'] = array('exp','interest_count+1');
			$Event->save($add);
			$this->ajaxReturn('', '', 1);
		}
		else{
			$this->ajaxReturn('', '', 0);
		}
	}
	
	public function cancelInterest(){
		$userId=session('userId');
		if(isset($userId)){
			$map['u_id'] = $userId;
			$map['e_id'] = I('param.e_id');
			
			$InterestEvent = M('InterestEvent');
			$InterestEvent->where($map)->delete();
			$Event = M('Event');
			$data['id'] = I('param.e_id');
			$data['interest_count'] = array('exp','interest_count-1');
			$Event->save($data);
			$this->ajaxReturn('', '', 1);
		}
		else{
			$this->ajaxReturn('', '', 0);
		}
	}

	public function addComment(){
		if(isset($_SESSION['userId'])){
			$Comment = M('EventComment');
			$data['e_id'] = I('param.e_id');
			//此处的u_id是目前登录用户的u_id并非活动发起人的u_id
			$data['u_id'] = session('userId');
			$data['content'] = I('param.content');
			$data['create_time'] = time();

			$eventModel = new Model();
			$eventResult = $eventModel->query('select u_id, title from seu_event where id='.I('param.e_id'));
			//发布活动的u_id，因为要做消息机制，需要提示活动发布者，这个u_id是活动发布者的u_id，而不是评论活动的人的id
			$eventuid = $eventResult[0]['u_id'];
			$eventTitle = $eventResult[0]['title'];
			$eventMsgModel = new Model('EventMessage');
			$messageResult = $eventModel->query('select * from seu_event_message where e_id='.I('param.e_id').' and u_id='.$eventuid.' and from_id='.session('userId'));
			if($messageResult == null){
				//不存在就insert
				$messageData['e_id'] = I('param.e_id');
				$messageData['u_id'] = $eventuid;
				$messageData['from_id'] = session("userId");
				$messageData['title'] = $eventTitle;
				// $messageData['comment_count'] = array('exp', 'comment_count+1');
				$messageData['comment_count'] = 1;
				$eventMsgModel->add($messageData);
			}else{
				//如果已经存在就update
				$messageData['comment_count'] = array('exp', 'comment_count+1');
				$eventMsgModel->where('e_id='.I('param.e_id').' and u_id='.$eventuid.' and from_id='.session('userId'))->save($messageData);
			}

			$eventAtModel = new Model();
			$EventAt = M('EventAt');
			$atMessageResult = $eventAtModel->query('select * from seu_event_at where e_id='.I('param.e_id').' and u_id='.I('param.at_id').' and from_id='.session('userId'));
			if($atMessageResult == null){
				//不存在就insert
				$atMessageData['e_id'] = I('param.e_id');
				$atMessageData['u_id'] = I('param.at_id');
				$atMessageData['from_id'] = session("userId");
				$EventAt->add($atMessageData);
			}
			
			$result = $Comment->add($data);
			if ($result < 1) {
				$this->ajaxReturn('', '', 0);
			}
			$result['user_id'] = session('userId');
			$result['user_name'] = session('userName');
			$result['icon'] = session('icon');
			$result['content'] = I('param.content');
			$this->ajaxReturn($result, 'success', 1);
		}
		else{
			$this->ajaxReturn('', '请登录', -1);
		}
		
	}

	//上传海报
	public function uploadPoster(){
		import('@.ORG.UploadFile');

		$upload = new UploadFile();								 	// 实例化上传类
		$upload->maxSize  = 3145728 ;							 	// 设置附件上传大小
		$upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');	// 设置附件上传类型
		$upload->savePath =  './Uploads/Images/Event/Poster/Raw/';	// 设置附件上传目录	
		$upload->saveRule= "uniqid";								//文件保存规则
		$upload->thumb = true; 										//设置需要生成缩略图，仅对图像文件有效
		$upload->thumbPrefix = 'r_';  							//设置需要生成缩略图的文件后缀
        $upload->imageClassPath = '@.ORG.Image';					
        $upload->thumbMaxWidth = '300'; 						//设置缩略图最大宽度 
        $upload->thumbMaxHeight = '300';						//设置缩略图最大高度 
		$upload->thumbRemoveOrigin = true; 
		 
		if(!$upload->upload()) {// 上传错误提示错误信息
			$this->error($upload->getErrorMsg());
		}else{// 上传成功
			$info=$upload->getUploadFileInfo();
			$data['path'] = $info[0]['savepath'].'r_'.$info[0]['savename'];
			$imageSize = getimagesize($data['path']);
			$data['width'] = $imageSize[0];
			$data['height'] = $imageSize[1];
			$this->ajaxReturn($data, 'success', 1);
		}
	}

	//裁剪海报并生成缩略图
	public function thumbPoster(){
		$rawPath = I('param.imagepath');
		$thumbPath = str_replace('Raw','Thumb',$rawPath);
		$thumbPath = str_replace('r_','t_',$thumbPath);
		thumb($rawPath,$thumbPath,I('param.width'),I('param.height'),I('param.iconx'),I('param.icony'));
		$data['rawpath'] = $rawPath;
		$data['thumbpath'] = $thumbPath;
		$this->ajaxReturn($data, 'success', 1);
	}

	//将活动信息添加到数据库
	public function addEvent(){
		$startTime = I('param.startdate')." ".I('param.starttime').":00";
		$endTime = I('param.enddate')." ".I('param.endtime').":00";
		$data['title'] = I('param.title');
		$data['create_time'] = time();
		$data['start_time'] = strtotime($startTime);
		$data['end_time'] = strtotime($endTime);
		$data['location'] = I('param.location');
		$data['intro'] = I('param.intro');
		$data['u_id'] = session('userId');
		$data['category'] = I('param.tag_cate');
		if(I('param.rawpath') && I('param.thumbpath')){
			$data['raw_poster'] = I('param.rawpath');
			$data['poster'] = I('param.thumbpath');
		}
		
		if(I('param.iscost')=="yes"){
			$data['cost'] = I('param.cost');
		}else{
			$data['cost'] = "免费";
		}
		
		$Event = M('Event');
		
		if(session('userId') == 1){
			$data['status'] = 1;
			$saveid = $Event->add($data);
			$this->redirect('/event/'+$saveid);
		}else{
			$saveid = $Event->add($data);
			$this->redirect('/event/'+$saveid);
		}
	}

}
?>