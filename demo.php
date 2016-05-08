<?php


define('APPID', 'wx09cdd5cd75ef694f');
define('SECRET', '5f70747455db954ed58e7f4f88ec4286');
define('TESTURL', 'http://wechat.twodiv.com/auth');



?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
  <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.js"></script>
  
</head>
<body>
  
<h1>Test Url</h1>
<a href="https://open.weixin.qq.com/connect/oauth2/authorize?appid=<?php echo APPID;?>&redirect_uri=<?php echo urlencode(TESTURL);?>&response_type=code&scope=snsapi_userinfo&state=454545#wechat_redirect">Test Me - snsapi_userinfo</a>

<button id="ajaxSend">ajax Test</button>

<script type="text/javascript">
  var url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=<?php echo APPID;?>&redirect_uri=<?php echo urlencode(TESTURL);?>&response_type=code&scope=snsapi_userinfo&state=454545#wechat_redirect";
  $('#ajaxSend').click(function(){
    $.get( url, function( data ) {
      alert( data );

  });
  })
    </script>
</body>

</html>
