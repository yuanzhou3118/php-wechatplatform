<?php 

require_once('config.php');
require_once('functions.php');



// $data = array('type'=>'image','offset'=>0,'count'=>20);

// $data = json_encode($data);

$currentopenid = '';

$t = 1;

while ($t<11) {
    $t++;
    $ch = curl_init('https://api.weixin.qq.com/cgi-bin/user/get?&access_token='.get_accessToken().'&next_openid='.$currentopenid);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
    $res = curl_exec($ch);
    curl_close($ch);
    //var_dump($r);
    $r = json_decode($res);
    $currentopenid = $r->next_openid;
    $a = $r->data->openid;
    $fp = fopen("log/openid.csv","a");
    foreach ($a as $b) {
        $p=fwrite($fp,$b."\r\n");
    }
    fclose($fp);
    $total = $r->total;
}

var_dump($total);
exit;



?>