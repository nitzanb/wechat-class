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
</head>
<body>
  
<h1>Test Url</h1>
<a href="https://open.weixin.qq.com/connect/oauth2/authorize?appid=<?php echo APPID;?>&redirect_uri=<?php echo urlencode(TESTURL);?>&response_type=code&scope=snsapi_userinfo&state=454545#wechat_redirect">Test Me - snsapi_userinfo</a>


</body>

</html>