<?php

if(isset($_GET['echostr']) && !empty($_GET['echostr']))
    $wechatObj->validateAccount();
else
    $wechatObj->responseMsg();