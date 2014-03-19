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
	                	$contentStr = "欢迎关注东大通";
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
}
?>