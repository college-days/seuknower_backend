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
			$this->creatMenu();
			$this->responseMsg();
		}
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
					$msgType = "text";
	                	$contentStr = "欢迎关注“东大通”官方微信账号。东大通团队全部都是咱们东大的在校大学生，如果感觉东大通哪用得不爽欢迎直接拍砖，自己人不要客气，如果您有什么建议或者意见也欢迎直接回复我们，这是对我们最大的鼓励和支持，感谢亲爱的同学们！东大通，东大人自己的校园通！";
	                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
	                	echo $resultStr;
				}else{
					if(!empty( $keyword ))
	                {
	              		$msgType = "text";
	                	$contentStr = "东大通竭诚为您服务";
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
	 		"button":[{
	 			"type":"click",
	 			"name":"刮刮乐",
	 			"url":"http://www.seuknower.com/lottery"
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
		return $data;
	}
}
?>