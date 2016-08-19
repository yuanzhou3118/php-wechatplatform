<?php 
require_once('config.php');
function anzUrl($reurl){
    if(strpos($reurl,'?')){
        $t = explode('?',$reurl);
        $parr['reurl'] = $t[0];
        $t = explode('&', $t[1]);
        foreach($t as $param){
            $c = explode('=', $param);
            $parr[$c[0]] = $c[1];
        }
    }else{
        $parr['reurl'] = $reurl;
    }
    return $parr;
}

$type = $_GET['type'];
$reurl = $_GET['reurl'];
if(!isset($reurl)){
    echo 'reurl is required.';
    exit;
}
if(!isset($type)){
    echo 'type is required.';
    exit;
}
if($type != 'userinfo' && $type != 'base'){
    echo 'type is incorrect.';
    exit;
}

$reurl = urldecode($reurl);
$parr = anzUrl($reurl);
setcookie('reurl',json_encode($parr));

//$callback = urldecode($callback);
//echo $callback;
echo '<script type="text/javascript">';
if($type == 'userinfo'){
    setcookie('stype','userinfo');
    echo 'location.href = "https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appID.'&redirect_uri='.urlencode('http://www.dangdaimingshi.com/ddmswechat/skip2.php').'&response_type=code&scope=snsapi_userinfo&state=v1#wechat_redirect"';
}else{
    setcookie('stype','base');
    echo 'location.href = "https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$appID.'&redirect_uri='.urlencode('http://www.dangdaimingshi.com/ddmswechat/skip2.php').'&response_type=code&scope=snsapi_base&state=v1#wechat_redirect"';
}

echo '</script>';
exit;    


?>