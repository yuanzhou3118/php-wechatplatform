<?php
require_once('config.php');
require_once('functions.php');
//include('full/qrlib.php');


// $qrid = $_GET['qrid'];
// if(!$qrid || $qrid < 1 || $qrid > 100000){
//     $msg['haserror'] = 1;
//     $msg['errormsg'] = 'qrid is uncorrect';
//     echo json_encode($msg);
//     exit;
// }



// $tempDir = "eventqr/";
// $codeContents = 'http://martell.doloplay.com/Mnb/?imgid='.$qrid;   
// $fileName = 'event_'.$qrid.'.png'; 
// $pngAbsoluteFilePath = $tempDir.$fileName;
// if(!file_exists($pngAbsoluteFilePath)) {
//     QRcode::png($codeContents, $pngAbsoluteFilePath, QR_ECLEVEL_Q,10);
//     //img_water_mark($pngAbsoluteFilePath, 'logo.png', $savepath=$tempDir, $savename=$fileName, $positon=3, $alpha=100);
//     $msg['haserror'] = 0;
//     $msg['qrurl'] = 'http://'.$_SERVER['HTTP_HOST'].'/ddmswechat/eventqr/event_'.$qrid.'.png';
//     echo json_encode($msg);
//     exit;
// }else{
//     $msg['haserror'] = 0;
//     $msg['qrurl'] = 'http://'.$_SERVER['HTTP_HOST'].'/ddmswechat/eventqr/event_'.$qrid.'.png';
//     echo json_encode($msg);
//     exit;
// }

// $data = array("expire_seconds" => "86400", "action_name" => "QR_SCENE", "action_info" => array("scene" => array("scene_id"=>$qrid)));
// $data_string = json_encode($data);

// $ch = curl_init('https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.get_accessToken());
// curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
// curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
// curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//     'Content-Type: application/json',
//     'Content-Length: ' . strlen($data_string))
// );
// $result = curl_exec($ch);
// curl_close($ch);
// $ticket = json_decode($result,true);
// if($ticket['errcode']){
//     $msg['haserror'] = 1;
//     $msg['errormsg'] = json_encode($ticket);
//     echo json_encode($msg);
//     exit;
// }
// $tstr = urlencode($ticket["ticket"]);
// $url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$tstr;
// $ch = curl_init();
// curl_setopt($ch, CURLOPT_URL, $url);
// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    // 要求结果为字符串且输出到屏幕上
// curl_setopt($ch, CURLOPT_HEADER, 0); // 不要http header 加快效率
// curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
// curl_setopt($ch, CURLOPT_TIMEOUT, 15);
// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
// curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 

// $output = curl_exec($ch);
// curl_close($ch);

// if($output){
//     file_put_contents('eventqr/event_'.$qrid.'.jpg', $output);
//     if(file_exists('eventqr/event_'.$qrid.'.jpg')){
//         $msg['haserror'] = 0;
//         $msg['qrurl'] = 'http://'.$_SERVER['HTTP_HOST'].'/ddmswechat/eventqr/event_'.$qrid.'.jpg';
//     }else{
//         $msg['haserror'] = 1;
//         $msg['errormsg'] = 'genrate qr image fail.';
//     }
//     echo json_encode($msg);
//     exit;
// }else{
//     $msg['haserror'] = 1;
//     $msg['errormsg'] = 'get qr image from wc fail.';
//     echo json_encode($msg);
//     exit;
// }

$data = array("action_name" => "QR_LIMIT_STR_SCENE", "action_info" => array("scene" => array("scene_str"=>"cny2016")));
$data_string = json_encode($data);

$ch = curl_init('https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.get_accessToken());
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data_string))
);
 
$result = curl_exec($ch);
curl_close($ch);

$ticket = json_decode($result,true);
$tstr = urlencode($ticket["ticket"]);
$url = "https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$tstr;
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    // 要求结果为字符串且输出到屏幕上
curl_setopt($ch, CURLOPT_HEADER, 0); // 不要http header 加快效率
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 

$output = curl_exec($ch);
curl_close($ch);
//echo $output;

file_put_contents('log/cny2016.jpg', $output);


?>