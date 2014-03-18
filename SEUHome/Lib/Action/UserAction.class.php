<?php
import("@.Action.CommonUtil");

class UserAction extends Action {
	public function _initialize(){
		$util = new CommonUtil();
        $util->autologin();
	}

    public function _empty(){
        $this->display("Public:404");
    }

	public function get_ask_question($u_id, $type){
		$Question = M('Question');
        if($type){
            $askQuestions = $Question->where("u_id=".$u_id." and type='".$type."'")->order('create_time desc')->select();
        }else{
            $askQuestions = $Question->where('u_id='.$u_id)->order('create_time desc')->select();
        }
		
		/*
    	for($i=0; $i<count($askQuestions); $i++){
    		$askQuestions[$i]["title"] = $util->sub_string($askQuestions[$i]["title"], 18);
    	}
    	*/

    	return $askQuestions;
	}

	public function get_answer_question($u_id, $type){
		$User = M('User');
		$Question = M('Question');
    	$Answer = M('Answer');
    	$answerQuestionIds = $Answer->field('q_id')->where('u_id='.$u_id)->order('create_time desc')->select();
    	$answerQuestions = array();
    	for($i=0; $i<count($answerQuestionIds); $i++){
            if($type){
                $answerQuestion = $Question->where("id=".$answerQuestionIds[$i]['q_id']." and type='".$type."'")->find();
            }else{
                $answerQuestion = $Question->where('id='.$answerQuestionIds[$i]['q_id'])->find();
            }
    		if(!empty($answerQuestion)){
    			$askUserInfo = $User->where('id='.$answerQuestion["u_id"])->find();
    			$answerQuestion["ask_name"] = $askUserInfo["name"];
    			array_push($answerQuestions, $answerQuestion);
    		}
    	}
    	
    	/*
    	for($i=0; $i<count($answerQuestions); $i++){
    		$answerQuestions[$i]["title"] = $util->sub_string($answerQuestions[$i]["title"], 18);
    	}
    	*/

    	return $answerQuestions;
	}

	public function get_join_event($u_id, $isdetail, $type){
		$Event = M('Event');
		$EventJoin = M('JoinEvent');
        $joinEventIds = $EventJoin->field('e_id')->where('u_id='.$u_id)->order('create_time desc')->select();
    	$joinEvents = array();
    	for($i=0; $i<count($joinEventIds); $i++){
            if($type){
                $joinEvent = $Event->where("id=".$joinEventIds[$i]['e_id']." and category='".$type."'")->find();
            }else{
                $joinEvent = $Event->where('id='.$joinEventIds[$i]['e_id'])->find();
            }
    		if(!empty($joinEvent)){
    			array_push($joinEvents, $joinEvent);
    		}
    	}

    	$util = new CommonUtil();	
    	
    	for($i=0; $i<count($joinEvents); $i++){
			if(!$util->exists_file($joinEvents[$i]["poster"])){
				$joinEvents[$i]["poster"] = "__IMAGE__/event/act5.jpg";
			}	
			if($isdetail == 0){
				$joinEvents[$i]["title"] = $util->sub_string($joinEvents[$i]["title"], 6);
			}
    	}

    	return $joinEvents;
	}

	public function get_interest_event($u_id, $isdetail, $type){
		$Event = M('Event');
    	$EventInterest = M('InterestEvent');
    	$interestEventIds = $EventInterest->field('e_id')->where('u_id='.$u_id)->order('create_time desc')->select();
    	$interestEvents = array();
    	for($i=0; $i<count($interestEventIds); $i++){
            if($type){
                $interestEvent = $Event->where('id='.$interestEventIds[$i]['e_id']." and category='".$type."'")->find();
            }else{
                $interestEvent = $Event->where('id='.$interestEventIds[$i]['e_id'])->find();
            }
    		if(!empty($interestEvent)){
    			array_push($interestEvents, $interestEvent);
    		}
    	}

    	$util = new CommonUtil();
  
		for($i=0; $i<count($interestEvents); $i++){
			if(!$util->exists_file($interestEvents[$i]["poster"])){
				$interestEvents[$i]["poster"] = "__IMAGE__/event/act5.jpg";
			}	
			if($isdetail == 0){
				$interestEvents[$i]["title"] = $util->sub_string($interestEvents[$i]["title"], 6);
			}
    	}

    	return $interestEvents;
	}

	public function get_sell_commodity($u_id, $isdetail, $type){
		$Commodity = M('Commodity');
        if($type){
            $sellCommodities = $Commodity->where("u_id=".$u_id." and category='".$type."'")->order('create_time desc')->select();
        }else{
            $sellCommodities = $Commodity->where('u_id='.$u_id)->order('create_time desc')->select();
        }
    	$util = new CommonUtil();

    	for($i=0; $i<count($sellCommodities); $i++){
    		if(!$util->exists_file($sellCommodities[$i]["picture"])){
    			$sellCommodities[$i]["picture"] = "__IMAGE__/user_center/goods_01.jpg";
    		}
    		//待测试 现在用的两个账号一个没有商品，还有一个商品的title都很短
    		if($isdetail == 0){
    			$util->sub_string($sellCommodities[$i]["title"], 8);
    		}
    	}	

    	return $sellCommodities;
	}

	public function get_sell_commodity_on($u_id, $type){
		$sellCommodities = $this->get_sell_commodity($u_id, 1, $type);
		$sellons = array();
		for($i=0; $i<count($sellCommodities); $i++){
			if($sellCommodities[$i]['onsale'] == 1){
                $sellCommodities[$i]['title'] = htmlspecialchars_decode($sellCommodities[$i]['title']);
                $sellCommodities[$i]['intro'] = htmlspecialchars_decode($sellCommodities[$i]['intro']);
				array_push($sellons, $sellCommodities[$i]);
			}
		}

		return $sellons;
	}

	public function get_sell_commodity_done($u_id, $type){
		$sellCommodities = $this->get_sell_commodity($u_id, 1, $type);
		$selldones = array();
		for($i=0; $i<count($sellCommodities); $i++){
			if($sellCommodities[$i]['onsale'] == 0){
                $sellCommodities[$i]['title'] = htmlspecialchars_decode($sellCommodities[$i]['title']);
                $sellCommodities[$i]['intro'] = htmlspecialchars_decode($sellCommodities[$i]['intro']);
				array_push($selldones, $sellCommodities[$i]);
			}
		}

		return $selldones;
	}

    public function index(){
    	$u_id = I('param.id');
    	$User = M('User');
    	$userInfo = $User->find($u_id);

    	$askQuestions = $this->get_ask_question($u_id);
    	$answerQuestions = $this->get_answer_question($u_id);
    	
    	$joinEvents = $this->get_join_event($u_id, 0);
        $interestEvents = $this->get_interest_event($u_id, 0);

		$sellCommodities = $this->get_sell_commodity_on($u_id, 0);

    	if($_SESSION['userId'] == $u_id){
    		$this->assign('me', '1');
    	}else{
    		$this->assign('me', '0');
    	}

    	session('askcount', count($askQuestions));
    	session('answercount', count($answerQuestions));
    	session('sellcommoditycount', count($sellCommodities));
    	session('joineventcount', count($joinEvents));
    	session('interesteventcount', count($interestEvents));

        $this->footPrint($u_id);

    	$this->assign('asks', $askQuestions);
    	$this->assign('askcount', count($askQuestions));
    	$this->assign('answers', $answerQuestions);
    	$this->assign('answercount', count($answerQuestions));
    	$this->assign('interestevents', $interestEvents);
    	$this->assign('interesteventcount', count($interestEvents));
    	$this->assign('joinevents', $joinEvents);
    	$this->assign('joineventcount', count($joinEvents));
    	$this->assign('sellcommodities', $sellCommodities);
    	$this->assign('sellcommoditycount', count($sellCommodities));
    	$this->assign('user', $userInfo);
    	$this->assign('u_id', $u_id);
    	$this->display('user_center');
    }

    public function ask_question(){
    	$u_id = I('param.id');
    	$User = M('User');
        $type = I('param.type');
    	$userInfo = $User->find($u_id);
    	
    	$askQuestions = $this->get_ask_question($u_id, $type);

    	if($_SESSION['userId'] == $u_id){
    		$this->assign('me', '1');
    	}else{
    		$this->assign('me', '0');
    	}

        $this->footPrint($u_id);

    	$this->assign('asks', $askQuestions);
    	$this->assign('askcount', count($askQuestions));
    	$this->assign('answercount', $_SESSION['answercount']);
    	$this->assign('sellcommoditycount', $_SESSION['sellcommoditycount']);
    	$this->assign('user', $userInfo);
    	$this->assign('u_id', $u_id);
        $this->assign('type', $type);
    	$this->display('ask_question');
    }

    public function answer_question(){
    	$u_id = I('param.id');
    	$User = M('User');
        $type = I('param.type');
    	$userInfo = $User->find($u_id);
		
		$answerQuestions = $this->get_answer_question($u_id, $type);

		if($_SESSION['userId'] == $u_id){
    		$this->assign('me', '1');
    	}else{
    		$this->assign('me', '0');
    	}

        $this->footPrint($u_id);

    	$this->assign('answers', $answerQuestions);
    	$this->assign('answercount', count($answerQuestions));
    	$this->assign('askcount', $_SESSION['askcount']);
    	$this->assign('sellcommoditycount', $_SESSION['sellcommoditycount']);
    	$this->assign('user', $userInfo);
    	$this->assign('u_id', $u_id);
        $this->assign('type', $type);
    	$this->display('answer_question');
    }

    public function join_event(){
    	$u_id = I('param.id');
    	$User = M('User');
        $type = I('param.type');
    	$userInfo = $User->find($u_id);

    	$joinEvents = $this->get_join_event($u_id, 1, $type);  

    	if($_SESSION['userId'] == $u_id){
    		$this->assign('me', '1');
    	}else{
    		$this->assign('me', '0');
    	}

        $this->footPrint($u_id);

    	$this->assign('answercount', $_SESSION['answercount']);
    	$this->assign('askcount', $_SESSION['askcount']);
    	$this->assign('sellcommoditycount', $_SESSION['sellcommoditycount']);
    	$this->assign('joineventcount', $_SESSION['joineventcount']);
    	$this->assign('interesteventcount', $_SESSION['interesteventcount']);
    	$this->assign('joinevents', $joinEvents);
    	$this->assign('user', $userInfo);
    	$this->assign('u_id', $u_id);
        $this->assign('type', $type);
    	$this->display('join_event');
    }

    public function interest_event(){
    	$u_id = I('param.id');
    	$User = M('User');
        $type = I('param.type');
    	$userInfo = $User->find($u_id);

    	$interestEvents = $this->get_interest_event($u_id, 1, $type);

    	if($_SESSION['userId'] == $u_id){
    		$this->assign('me', '1');
    	}else{
    		$this->assign('me', '0');
    	}

        $this->footPrint($u_id);

    	$this->assign('answercount', $_SESSION['answercount']);
    	$this->assign('askcount', $_SESSION['askcount']);
    	$this->assign('sellcommoditycount', $_SESSION['sellcommoditycount']);
    	$this->assign('joineventcount', $_SESSION['joineventcount']);
    	$this->assign('interesteventcount', $_SESSION['interesteventcount']);
    	$this->assign('interestevents', $interestEvents);
    	$this->assign('user', $userInfo);
    	$this->assign('u_id', $u_id);
        $this->assign('type', $type);
    	$this->display('interest_event');
    }

    public function sell_commodity_on(){
    	$u_id = I('param.id');
    	$User = M('User');
        $type = I('param.type');
    	$userInfo = $User->find($u_id);
    	
    	$sellons = $this->get_sell_commodity_on($u_id, $type);
    	$selldones = $this->get_sell_commodity_done($u_id, $type);

    	if($_SESSION['userId'] == $u_id){
    		$this->assign('me', '1');
    	}else{
    		$this->assign('me', '0');
    	}

        $this->footPrint($u_id);

    	$this->assign('answercount', $_SESSION['answercount']);
    	$this->assign('askcount', $_SESSION['askcount']);
    	$this->assign('sellcommoditycount', $_SESSION['sellcommoditycount']);
    	$this->assign('sellons', $sellons);
    	$this->assign('selloncount', count($sellons));
    	$this->assign('selldonecount', count($selldones));
    	$this->assign('user', $userInfo);
    	$this->assign('u_id', $u_id);
        $this->assign('type', $type);
    	$this->display('sell_commodity_on');
    }

    public function sell_commodity_done(){
    	$u_id = I('param.id');
    	$User = M('User');
        $type = I('param.type');
    	$userInfo = $User->find($u_id);

    	$sellons = $this->get_sell_commodity_on($u_id, $type);
    	$selldones = $this->get_sell_commodity_done($u_id, $type);

    	if($_SESSION['userId'] == $u_id){
    		$this->assign('me', '1');
    	}else{
    		$this->assign('me', '0');
    	}

        $this->footPrint($u_id);

    	$this->assign('answercount', $_SESSION['answercount']);
    	$this->assign('askcount', $_SESSION['askcount']);
    	$this->assign('sellcommoditycount', $_SESSION['sellcommoditycount']);
    	$this->assign('selldones', $selldones);
    	$this->assign('selloncount', count($sellons));
    	$this->assign('selldonecount', count($selldones));
    	$this->assign('user', $userInfo);
    	$this->assign('u_id', $u_id);
        $this->assign('type', $type);
    	$this->display('sell_commodity_done');
    }


    public function buy_commodity(){
    	$u_id = I('param.id');
    	$User = M('User');
    	$userInfo = $User->find($u_id);

		if($_SESSION['userId'] == $u_id){
    		$this->assign('me', '1');
    	}else{
    		$this->assign('me', '0');
    	}

        $this->footPrint($u_id);

    	$this->assign('answercount', $_SESSION['answercount']);
    	$this->assign('askcount', $_SESSION['askcount']);
    	$this->assign('sellcommoditycount', $_SESSION['sellcommoditycount']);
    	$this->assign('user', $userInfo);
    	$this->assign('u_id', $u_id);
    	$this->display('buy_commodity');
    }

    public function profile(){
    	$u_id = I('param.id');
        $modify_flag = I('param.modify');
        if(!$modify_flag){
            $this->assign('modify', 0);
        }else{
            $this->assign('modify', 1);
        }
    	$User = M('User');
    	$userInfo = $User->find($u_id);

        $util = new CommonUtil();

        $userInfo["sex"] = $util->filter_sex($userInfo["sex"]);

    	if($_SESSION['userId'] == $u_id){
            $this->assign('me', '1');
    	}else{
            $this->assign('me', '0'); 
    	}
        $this->footPrint($u_id);

        $this->assign('answercount', $_SESSION['answercount']);
        $this->assign('askcount', $_SESSION['askcount']);
        $this->assign('sellcommoditycount', $_SESSION['sellcommoditycount']);
        $this->assign('user', $userInfo);
        $this->assign('u_id', $u_id);
        $this->assign('isprofile', 1);
        $this->display('user_profile');
    }

    //上传头像
    public function uploadIcon(){
        import('ORG.Net.UploadFile');
        import('ORG.Util.Image');
        $upload = new UploadFile();                                 // 实例化上传类
        $upload->maxSize  = 3145728 ;                               // 设置附件上传大小
        $upload->allowExts  = array('jpg', 'gif', 'png', 'jpeg');   // 设置附件上传类型
        $upload->savePath =  './Uploads/Images/User/Icon/Raw/'; // 设置附件上传目录 
        $upload->saveRule= "time";                                  //文件保存规则
         
        if(!$upload->upload()) {// 上传错误提示错误信息
            $this->error($upload->getErrorMsg());
        }else{// 上传成功
            $info = $upload->getUploadFileInfo();
            $data['path'] = $info[0]['savepath'].$info[0]['savename'];
            $imageSize = getimagesize($data['path']);
            $data['width'] = $imageSize[0];
            $data['height'] = $imageSize[1];
            $this->ajaxReturn($data, 'success', 1);
        }
    }

    //裁剪头像并生成缩略图
    public function thumbIcon(){
        $srcPath=I('param.imagepath');
        $rawPath='./Uploads/Images/User/Icon/Raw/r'.time().'.jpg';
        thumb($srcPath,$rawPath,I('param.width'),I('param.height'),I('param.iconx'),I('param.icony'));
        
        import('ORG.Util.Image');
        $thumbPath='./Uploads/Images/User/Icon/t'.time().'.jpg';
        image::thumb($rawPath, $thumbPath, 'jpg', 48, 48);
        $result = unlink($rawPath);
        $data['rawpath'] = $srcPath;
        $data['thumbpath'] = $thumbPath;
        $this->ajaxReturn($data, 'success', $result);
    }

    public function updateProfile(){
        $u_id = I('param.id');

        //无权修改他人的信息
        if($_SESSION['userId'] == $u_id){
            $nickname = $_POST['nickname'];
            $sex = $_POST['sex'];
            $dept = $_POST['dept'];
            $grade = $_POST['grade'];
            $campus = $_POST['campus'];
            $intro = $_POST['intro'];
            $qq = $_POST['qq'];
            $email = $_POST['email'];
            $weibo = $_POST['weibo'];

            //thinkphp 框架如果update的时候发现字段根本没有更新那么就会默认直接返回一个false，就算是用execute执行原生的sql语句貌似也是类似的情况
            $User = M('User');

            $userInfo = $User->find($u_id);

            $data['name'] = $nickname;
            $data['sex'] = $sex;
            $data['dept'] = $dept;
            $data['grade'] = $grade;
            $data['campus'] = $campus;
            if(empty($nickname)){
                $data['name'] = $userInfo['name'];
            }else{
                $findUser = $User->where("name='".$nickname."'")->find();
                if($findUser){
                    if((int)$findUser['id'] != (int)$_SESSION['userId']){
                        $this->ajaxReturn('', '', 3);
                    }
                }
            }
            if(empty($intro)){
                $data['intro'] = $userInfo['intro'];
            }else{
                $data['intro'] = $intro;
            }
            if(empty($qq)){
                $data['qq'] = $userInfo['qq'];
            }else{
                $data['qq'] = $qq;
            }
            if(empty($email)){
                $data['email'] = $userInfo['email'];
            }else{
                $data['email'] = $email;
            }
            if(empty($weibo)){
                $data['weibo'] = $userInfo['weibo'];
            }else{
                $data['weibo'] = $weibo;
            }

            if(I('param.rawpath') && I('param.thumbpath')){
                $data['raw_icon'] = I('param.rawpath');
                $data['icon'] = I('param.thumbpath');
            }

            // if($userInfo['sex'] == $data['sex'] && $userInfo['dept'] == $data['dept'] && $userInfo['grade'] == $data['grade'] && $userInfo['campus'] == $data['campus'] && $userInfo['intro'] == $data['intro'] && $userInfo['qq'] == $data['qq'] && $userInfo['email'] == $data['email'] && $userInfo['weibo'] == $data['weibo']){
            //     $this->ajaxReturn('啥也没更新', '', 1);
            // }

            $result = $User->where('id='.$u_id)->save($data);
            if(!$result){
                $this->ajaxReturn('', '', 1);
            }else{
                $this->ajaxReturn('', '', 1);
            }

        }else{
            $this->ajaxReturn($u_id, '', 0);
        }
        
    }

    public function footPrint($u_id){
        $Event = M('Event');
        $Question = M('Question');
        $Commodity = M('Commodity');
        $Answer = M('Answer');
        $EventComment = M('EventComment');
        $CommodityComment = M('CommodityComment');

        $recentEvents = $Event->where("u_id=".$u_id)->order('create_time desc')->limit(1)->select();
        $recentQuestions = $Question->where("u_id=".$u_id)->order('create_time desc')->limit(1)->select();
        $recentCommodities = $Commodity->where("u_id=".$u_id)->order('create_time desc')->limit(1)->select();
        $recentAnswers = $Answer->where("u_id=".$u_id)->order('create_time desc')->limit(1)->select();
        $recentEventComments = $EventComment->where("u_id=".$u_id)->order('create_time desc')->limit(1)->select();
        $recentCommodityComments = $CommodityComment->where("u_id=".$u_id)->order('create_time desc')->limit(1)->select();

        for($i=0; $i<count($recentAnswers); $i++){
            $targetQuestion = $Question->where("id=".$recentAnswers[$i]['q_id'])->find();
            $recentAnswers[$i]['q_title'] = $targetQuestion['title'];
        }

        for($i=0; $i<count($recentEventComments); $i++){
            $targetEvent = $Event->where("id=".$recentEventComments[$i]['e_id'])->find();
            $recentEventComments[$i]['e_title'] = $targetEvent['title'];
        }

        for($i=0; $i<count($recentCommodityComments); $i++){
            $targetCommodity = $Commodity->where("id=".$recentCommodityComments[$i]['c_id'])->find();
            $recentCommodityComments[$i]['c_title'] = $targetCommodity['title'];
        }

        $footCount = count($recentEvents)+count($recentQuestions)+count($recentCommodities)+count($recentAnswers)+count($recentEventComments)+count($recentCommodityComments);

        $this->assign('footcount', $footCount);
        $this->assign('recentevents', $recentEvents);
        $this->assign('recentquestions', $recentQuestions);
        $this->assign('recentcommodities', $recentCommodities);
        $this->assign('recentanswers', $recentAnswers);
        $this->assign('recenteventcomments', $recentEventComments);
        $this->assign('recentcommoditycomments', $recentCommodityComments);
    }
}
?>