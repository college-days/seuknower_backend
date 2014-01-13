<?php
// 本类由系统自动生成，仅供测试用途
class EventAction extends Action {
	public function _initialize(){
		if(isset($_SESSION['userId'])){	
		}
		else{
			//password is md5 value
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
				//是否是社团或者组织
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
    	$tag = I("param.tag");
		$time = I("param.time");
		if(!$tag) $tag = "全部";
		if(!$time) $time = "全部";
		$type = $tag;
		
		$Model = M();	
		$hots = $Model->table('seu_event event')->field('event.id,event.poster,event.title')->order('join_count desc')->limit(10)->select();

		$this->assign("hots", $hots);

		if($tag != "全部"){
			//$sql .= " AND event.tag LIKE '%".$tag."%' ";
			$sql = "event.tag = '".$tag."'";
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
			$pageCount = ceil($count/5);
			if(I('param.id')){
				$page = I('param.id');
				if($page > $pageCount) $page = $pageCount;
			}
			else{
				$page = 1;
			}
			//$start = ($page-1)*10;	
			$start = ($page-1)*5;
			
			//$events = $Model->table('seu_event event, seu_user user')->field('event.id,event.u_id, event.title, event.start_time, event.end_time, event.cost, event.location, event.join_count, event.interest_count, event.poster, user.is_group, user.name as organizer')->order('event.create_time desc')->limit($start.',10')->where($sql)->select();

			$events = $Model->table('seu_event event, seu_user user')->field('event.id,event.u_id, event.title, event.start_time, event.end_time, event.cost, event.location, event.join_count, event.interest_count, event.poster, user.is_group, user.name as organizer')->order('event.create_time desc')->limit($start.',5')->where($sql)->select();
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
		
		for($i=0; $i<count($events); $i++){
			$filename = $events[$i]["poster"];
			//必须得是绝对路径
			$path = "E:/wamp/www";
			$dest = $path.$filename;
			if(!file_exists($dest)){
				$events[$i]["poster"] = "__IMAGE__/event/act5.jpg";
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

    	$this->display('index');
    }
}
?>