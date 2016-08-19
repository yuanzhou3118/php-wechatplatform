<?php 
require_once('config.php');
$code = $_GET['code'];
$stype = $_COOKIE['stype'];
if(!isset($code)){
    echo 'wc code error';
    exit;
}

$ch = curl_init('https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appID.'&secret='.$appSecret.'&code='.$code.'&grant_type=authorization_code');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    // 要求结果为字符串且输出到屏幕上
curl_setopt($ch, CURLOPT_HEADER, 0); // 不要http header 加快效率
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 

$result = curl_exec($ch);
$result = json_decode($result,true);
//var_dump(json_decode($result,true));
curl_close($ch);

if($result['errcode']){
    $result['haserror'] = 1;
    $result['errormsg'] = 'use code get access_token fail.';
    echo json_encode($result);
    // $t = time();
    // file_put_contents('log/aaa.html', json_encode($result).'///'.date("D F d Y",$t));
    exit;
}

if($stype=='base'){
    $arr = json_decode($_COOKIE['reurl'],true);
    $href = $arr['reurl'];
    unset($arr['reurl']);
    $arr['openid'] = $result['openid'];
    $para = http_build_query($arr);
    $tourl = 'location.href = "'.$href.'?'.$para.'"';
    echo '<script type="text/javascript">';
    echo $tourl;
    echo '</script>';
    exit;
}

$ch = curl_init('http://glp.nurunci.com/wc/getuserinfo.php?scope=user&openid='.$result['openid'].'&access_token='.$result['access_token'].'&refresh_token='.$result['refresh_token']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    // 要求结果为字符串且输出到屏幕上
curl_setopt($ch, CURLOPT_HEADER, 0); // 不要http header 加快效率
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 

$result = curl_exec($ch);
$result = json_decode($result,true);
//var_dump(json_decode($result,true));
curl_close($ch);

if($result['errcode']){
    $result['haserror'] = 1;
    $result['errormsg'] = 'get user info fail.';
    echo json_encode($result);
    exit;
}

$arr = json_decode($_COOKIE['reurl'],true);
$href = $arr['reurl'];
unset($arr['reurl']);
$arr['openid'] = $result['openid'];
if($result['scope'] != 'snsapi_base'){
    $arr['nickname'] = urlencode($result['nickname']);
    $arr['headimgurl'] = $result['headimgurl'];
}
$para = http_build_query($arr);


$tourl = 'location.href = "'.$href.'?'.$para.'"';

echo '<script type="text/javascript">';
echo $tourl;
echo '</script>';
exit;

?>