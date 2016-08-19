<?php 
require_once('config.php');
require_once('functions.php');
header("Access-Control-Allow-Origin: *");

if(!$_GET['signurl']){
    $msg['haserror'] = 1;
    $msg['errormsg'] = 'signurl is required';
    echo json_encode($msg);
    exit;
}

function getRandChar($length){
   $str = null;
   $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
   $max = strlen($strPol)-1;

   for($i=0;$i<$length;$i++){
    $str.=$strPol[rand(0,$max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
   }

   return $str;
}

$jsticket = getjsticket();
if($jsticket){
    $noncestr = getRandChar(16);
    $timestamp = time();
    //$url=urldecode($_GET['signurl']);
    $url=$_GET['signurl'];
    $str = 'jsapi_ticket='.$jsticket.'&noncestr='.$noncestr.'&timestamp='.$timestamp.'&url='.$url;
    $msg['haserror'] = 0;
    $msg['appId'] = $appID;
    $msg['signature'] = sha1($str);
    $msg['timestamp'] = $timestamp;
    $msg['nonceStr'] = $noncestr;
    $msg['url'] = $url;
    $msg['jsticket'] = $jsticket;
    echo json_encode($msg);
    exit;
}else{
    $msg['haserror'] = 1;
    $msg['errormsg'] = 'get jsticket error';
    echo json_encode($msg);
    exit;
}


?>