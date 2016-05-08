<?php
/*
*	A PHP class for WeChat integration. 
*
*	Supports
*	1. Account validation methods
*	2. Message - get and reply to WeChat messages
*	3. Authenticate users
*	4. Get user information from user Id.
*	
*
*	This class was built by the specification and sample classes at:
*	Chinese version: http://mp.weixin.qq.com/wiki/home/index.html
*	English Version: http://admin.wechat.com/wiki/index.php?title=Main_Page
*
*
*	!!!!!! Important !!!!!!!
* 	Though most of the functions are the same, some functionality only available for the Chinese version.
*	Also, some of the URL's might be different.
*
*
*
*	Nitzan Brumer
*/


class WeChat
{
	
	// 	The token is used for server to Server authentication and for messaging.
	private $token; 
	//	AESKey is the token for encrypting messages. this is not mandatory, but recommanded.
	// 	English doc: http://admin.wechat.com/wiki/index.php?title=Implementation_Guide
	// 	Chinese doc: 
	private $AESKey;

	//	Each Official channel should have an AppId and AppSectet. These are used for all Authentication methods.
	private $appId;
	private $appSecret;
	

	public function __construct($token,$AESKey, $appId, $appSecret) {
	    $this->token = $token;
	    $this->AESKey = $AESKey;
	    $this->appId = $appId;
	    $this->appSecret = $appSecret;
	}

	/**
	*	This function is required for first authentication in the wechat admin
	*	gets the echostr param, and echo a response based on the token and timestamp.	
	*/
	public function validateAccount()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if($this->checkSignature()){
        	echo $echoStr;
        	exit;
        }
    }

    private function checkSignature()
	{
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];	
        		
		
		$tmpArr = array($this->token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			return true;
		}else{
			return false;
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
                $time = time();

                $str = "A new message was recived at: " .  date('r', $time) . "\r\n";
                $str .= "From: " .  $fromUsername . "\r\n";
                $str .= "Content: " .   $keyword ;

                sendMail($str);

                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";             
				if(!empty( $keyword ))
                {
              		$msgType = "text";                   	             	
                   	if($keyword == 'send demo')
                        $contentStr = "Check here: http://wechat.twodiv.com/demo.php";   
                    else 
                    	$contentStr = "Up And Running" ;  
                	$resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);

                /*
				 *	The WeCHat API states that a response should be Either a formatted XML message that will be sent to the client
				 *	Or a blank response (Empty string) in order to not send anything to the client.				 *	
                 */
                	//Use the empty echo for no response
                	if($keyword == 'send demo')
                		echo $resultStr;
                	else
                		echo "";
                	//Return the response:
                	// echo $resultStr;
                }else{
                	echo "Input something...";
                }

        }else {
        	echo "";
        	exit;
        }
    }

    public function authenticateUser()
    {
    	$AuthUrl = 'https://api.wechat.com/sns/oauth2/access_token?appid='.$this->appId.'&secret='.$this->appSecret.'&code='.$_GET['code'].'&grant_type=authorization_code';
		$response = $this->httpGet($AuthUrl);
		$obj = json_decode($response);
		$info_URL = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$obj->access_token.'&openid='.$obj->openid.'&lang=zh_CN';
		$info = $this->httpGet($info_URL);
		return $info;
    }

	public function getSignPackage() 
	{
	    $jsapiTicket = $this->getJsApiTicket();

	    // Do not hardcode the URL
	    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
	    $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

	    $timestamp = time();
	    $nonceStr = $this->createNonceStr();

	    // sorting all parameters used for signature in the ordered by each field names’ ASCII code from small to large (lexicographical order)
	    $string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

	    $signature = sha1($string);

	    $signPackage = array(
	      "appId"     => $this->appId,
	      "nonceStr"  => $nonceStr,
	      "timestamp" => $timestamp,
	      "url"       => $url,
	      "signature" => $signature,
	      "rawString" => $string
	    );
	    return $signPackage; 
  	}

  	private function createNonceStr($length = 16) 
  	{
	    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	    $str = "";
	    for ($i = 0; $i < $length; $i++) {
	      	$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
	    }
	    return $str;
	}


  private function getJsApiTicket() {
    // jsapi_ticket should be updated and cached globally. The following code uses files as global storage.
    $data = json_decode(file_get_contents("jsapi_ticket.json"));
    if ($data->expire_time < time()) 
    {
	  $accessToken = $this->getAccessToken();
	  $url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
	  $res = json_decode($this->httpGet($url));
	  $ticket = $res->ticket;
	  if ($ticket) 
		{
			$data->expire_time = time() + 7000;
		    $data->jsapi_ticket = $ticket;
		    $fp = fopen("jsapi_ticket.json", "w");
		    fwrite($fp, json_encode($data));
		    fclose($fp);
		}
    } 
    else 
    {
      $ticket = $data->jsapi_ticket;
    }

    return $ticket;
  }

  private function getAccessToken() {
    // access_token should be updated and cached globally. The following code uses files as global storage.
    $data = json_decode(file_get_contents("access_token.json"));
    if ($data->expire_time < time()) {
      $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
      $res = json_decode($this->httpGet($url));
      $access_token = $res->access_token;
      if ($access_token) {
        $data->expire_time = time() + 7000;
        $data->access_token = $access_token;
        $fp = fopen("access_token.json", "w");
        fwrite($fp, json_encode($data));
        fclose($fp);
      }
    } else {
      $access_token = $data->access_token;
    }
    return $access_token;
  }

  private function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);

    $res = curl_exec($curl);
    curl_close($curl);

    return $res;
  }


}