<?php
class WeixinAction extends Action {
	public function index(){
		if($this->isGet()){
			$echoStr = $_GET["echostr"];
	        if($this->checkSignature()){
	        	echo $echoStr;
	        	exit;
	        }
		}
		if($this->isPost()){
			$this->responseMsg();
		}
	}

	public function postMenu(){
		$this->creatMenu();
		echo 'cleantha';
	}

	public function wxaccess(){
		$accessToken = $this->getAccessToken();
		echo $accessToken;
	}

	public function responseMsg()
    {
		//get post data, May be due to the different environments
		$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

      	//extract post data
		if (!empty($postStr)){
                
              	$postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
                $fromUsername = $postObj->FromUserName;
                $toUsername = $postObj->ToUserName;
                $keyword = trim($postObj->Content);
                $msgType = $postObj->MsgType;
                $event = $postObj->Event;
                $eventKey = $postObj->EventKey;
                $time = time();
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";

				if($msgType == "event"){
					if($event == "subscribe"){
						$msgType = "text";
						$contentStr = "欢迎关注东大通！东大通（seuknower.com）是东大专属的校园生活服务网站，活动召集、校园问答、二手市场，一网打尽。我们存在的意义就是创造让东大同学尖叫的功能，如果你有什么让人好的建议直接回复给我们，一旦被采纳将获得我们的小礼物。让咱们一起用互联网便捷东大人的生活吧";
						$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
						echo $resultStr;
					}
					if($event == "CLICK"){
						if($eventKey == "paocao"){
							$msgType = "text";
							$currentHour = (int)date('H');
              				$currentMinite = (int)date('i');
              				$currentWeekday = date('D');
              				if($currentWeekday == "Sun" || $currentWeekday == "Sat"){
              					$contentStr = "这是我们送给各位通友的第一个福利，当别人早上艰难的刷人人找跑操信息的时候，你只要优雅的点击一下“是否跑操”立刻就能知道跑操与否，每天早上6：25定时更新当日信息。亲，今天是周末不跑操，继续睡吧。。。";
              				}else{
              					if($currentHour < 6 || ($currentHour == 6 && $currentMinite < 25)){
	              					$contentStr = "这是我们送给各位通友的第一个福利，当别人早上艰难的刷人人找跑操信息的时候，你只要优雅的点击一下“是否跑操”立刻就能知道跑操与否，每天早上6：25定时更新当日信息。现在还没到时间，可以在睡一会哦";
	              				}else{
		              				$contentStr = getPaocaoMessage();
	              				}
              				}

							$year = date('Y');
							$month = date('m');
							$day = date('d');
							$dateStr = $year.'年'.$month.'月'.$day.'日';
							$contentStr = $contentStr.' '.$dateStr;

              				$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
	                		echo $resultStr;
						}
					}
					
				}else{
					if(!empty( $keyword ))
	                {
	              		$msgType = "text";
	              		if(preg_match("/跑操/i", $keyword, $matches)){
	              			if($keyword == "是否跑操"){
	              				$currentHour = (int)date('H');
	              				$currentMinite = (int)date('i');
	              				$currentWeekday = date('D');
	              				if($currentWeekday == "Sun" || $currentWeekday == "Sat"){
	              					$contentStr = "这是我们送给各位通友的第一个福利，当别人早上艰难的刷人人找跑操信息的时候，你只要优雅的点击一下“是否跑操”立刻就能知道跑操与否，每天早上6：25定时更新当日信息。亲，今天是周末不跑操，继续睡吧。。。";
	              				}else{
	              					if($currentHour < 6 || ($currentHour == 6 && $currentMinite < 25)){
		              					$contentStr = "这是我们送给各位通友的第一个福利，当别人早上艰难的刷人人找跑操信息的时候，你只要优雅的点击一下“是否跑操”立刻就能得到知道跑操与否。每天早上6：25定时更新当日信息，现在还没到时间，可以在睡一会哦";
		              				}else{
			              				$contentStr = getPaocaoMessage();
		              				}
	              				}
	              			}else{
							    $contentStr = "每个人的建议都有可能是我们下次给通友的福利哦!告诉你怎么更容易猜对：仔细观察自己和身边的朋友的生活有哪些难题，想想如果推出哪个功能会让你尖叫，咱们要做的就是能解决你生活中难题的功能。";
	              			}
						} else {
						    $contentStr = "每个人的建议都有可能是我们下次给通友的福利哦!告诉你怎么更容易猜对：仔细观察自己和身边的朋友的生活有哪些难题，想想如果推出哪个功能会让你尖叫，咱们要做的就是能解决你生活中难题的功能。";
						}
	              		
	              		$year = date('Y');
						$month = date('m');
						$day = date('d');
						$dateStr = $year.'年'.$month.'月'.$day.'日';
						$contentStr = $contentStr.' '.$dateStr;

	                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
	                	echo $resultStr;
	                }else{
	                	echo "请输入内容";
	                }
				}
        }else {
        	echo "";
        	exit;
        }
    }

	private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
        		
		$token = "cleantha";
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
		}
	}

	//创建菜单
	//获取access_token
	//将菜单结构体POST给微信服务器
	public function creatMenu(){
		$accessToken = $this->getAccessToken();
		$menuPostString = '{
	 		"button":[
	 		{
	 			"type":"view",
	 			"name":"跳蚤市场",
	 			"url":"http://www.seuknower.com/market/intro"
	 		},
	 		{
				"type":"click",
				"name":"是否跑操",
				"key":"paocao"
      		}]	
	 	}';
	 	$menuPostUrl = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$accessToken;
	 	$menu = $this->dataPost($menuPostString, $menuPostUrl);
	}

	//获取access_token
	//通过自定义函数getCurl得到https的内容
	//转为数组
	//获取access_token
	private function getAccessToken(){
	 	$AppId = 'wx358c79f5c4e52937';
	 	$AppSecret = '006ad901b4022d656bd52da7991e4bae';
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$AppId."&secret=".$AppSecret;
		$data = $this->getCurl($url);
		$resultArr = json_decode($data, true);
		return $resultArr["access_token"];
	}

	//get https的内容
	private function getCurl($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	//POST方式提交数据
	private function dataPost($post_string, $url){
		$context = array ('http' => array ('method' => "POST", 'header' => "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US) \r\n Accept: */*", 'content' => $post_string ) );
		$stream_context = stream_context_create ( $context );
		$data = file_get_contents ( $url, FALSE, $stream_context );
		// return $data;
	}
}
?>