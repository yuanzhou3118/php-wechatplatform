<?php

require_once('config.php');
require_once('functions.php');


$data['button'] = array();
$data['button'][0] = array('name'=>'在线购买','sub_button'=>array(array('type'=>'view','name'=>'官方微店','url'=>'http://www.wemart.cn/v2/weimao/index.html?disableCache=true&shopId=shop000201603099411#gd/shop000201603099411/shop000201603099411/10'),array('type'=>'click','name'=>'微店礼券','key'=>'ddms_wdlq'),array('type'=>'view','name'=>'正品鉴别','url'=>'http://mp.weixin.qq.com/s?__biz=MjM5NzYyMTk5Mg==&mid=202414270&idx=1&sn=f83d1caa0f68d22dc551bdbb28643841&scene=18#rd')));


$data['button'][1] = array('name'=>'超趴未来','sub_button'=>array(array('type'=>'click','name'=>'主场派对','key'=>'ddms_quizh5'),array('type'=>'click','name'=>'VR派对','key'=>'ddms_vrh5')));

$data['button'][2] = array('name'=>'绚境之夜','sub_button'=>array(array('type'=>'view','name'=>'绚境指数','url'=>'http://mnbq1pro.gypserver.com/face'),array('type'=>'view','name'=>'定制影像','url'=>'http://tesco.championmkt.com/tesco/zsf/martell/game1.jsp'),array('type'=>'view','name'=>'互动赢好礼','url'=>'http://tesco.championmkt.com/tesco/zsf/martell/game2.jsp'),array('type'=>'view','name'=>'玩转绚境','url'=>'http://tesco.championmkt.com/tesco/zsf/martell/game3.jsp')));

$data = json_encode($data,JSON_UNESCAPED_UNICODE);

$ch = curl_init('https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.get_accessToken());
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data))
);
 
$result = curl_exec($ch);
curl_close($ch);
var_dump($result);
exit;


?>