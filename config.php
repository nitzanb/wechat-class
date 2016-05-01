<?php

if(file_exists(ABSPATH.'/devtest.php'))
	include(ABSPATH.'/config.php');
else
{
	define("TOKEN", "devTest");
	define("AESKey", 'f09sfd0s8d0f0sdudf0sdjf0s89f0usdf9s0dfu0sdf');
	define("appId", "");
	define("appSecret","");	
}





