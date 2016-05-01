<?php
//	For debugging
//error_reporting( E_ALL );
//ini_set('display_errors', 1);

/** Absolute path to the Root directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');


include(ABSPATH.'/config.php');
include(ABSPATH.'/include/weChat.class.php');
include(ABSPATH.'/include/helpers.class.php');

$wechatObj = new WeChat(TOKEN,AESKey, appId, appSecret);

$url = trim($_SERVER['REQUEST_URI'] , '/');

if($url == '/' || empty($url))
	$handler = '404';
else
{
	
	$parms = explode( '/', $url);
	$handler = explode('?',$parms[0]);	


} 		
include(ABSPATH.'/handler/'.$handler[0].'.php');