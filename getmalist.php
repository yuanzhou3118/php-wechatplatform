<?php
require_once('config.php');
require_once('functions.php'); 


$data['type'] = 'image';
$data['offset'] = 0;
$data['count'] = 20;
$data_string = json_encode($data);

$ch = curl_init('https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token='.get_accessToken());
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data_string))
);
 
$result = curl_exec($ch);
//file_put_contents('log/ccc.html', $result);
//var_dump($result);
curl_close($ch);
echo '<script>';
echo 'window.aaa = '.$result.';';
echo '</script>';


?>