<?php

// function get_accessToken(){
//     global $appID;
//     global $appSecret;
//     global $mysqlserver;
//     global $sqluser;
//     global $sqlpass;
//     global $sqldatabase;

//     $getAccessToken = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appID.'&secret='.$appSecret;
//     //echo $getAccessToken;exit;

//     $con=mysqli_connect($mysqlserver,$sqluser,$sqlpass,$sqldatabase);
//     // Check connection
//     if (mysqli_connect_errno($con))
//     {
//         return 0;
//     }else{
//         $sql="select * from access_token where id=1";
//         $result=mysqli_query($con,$sql);
//         $arr=mysqli_fetch_array($result);
//         mysqli_free_result($result);
//         if(count($arr)){
//             if(time()>$arr['expire_date']){
//                 //need update
//                 $ch = curl_init();
//                 curl_setopt($ch, CURLOPT_URL, $getAccessToken);
//                 curl_setopt($ch, CURLOPT_HEADER, 0);
//                 curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//                 curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
//                 curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
//                 $r = curl_exec($ch);
//                 curl_close($ch);
//                 $jarr = json_decode($r,true);
//                 $access_token = $jarr['access_token'];
//                 $newtime = time()+60;
//                 $sql="update access_token set access_token='".$access_token."',expire_date='".$newtime."' where id=1";
//                 //file_put_contents('log/ccc.html', $sql);
//                 $result=mysqli_query($con,$sql);
//                 mysqli_free_result($result);
//                 mysqli_close($con);
//                 return $access_token;
//             }else{
//                 mysqli_close($con);
//                 return $arr['access_token'];
//             }
//         }else{
//             mysqli_close($con);
//             return 0;
//         }
//     }

// }

function decrypt($str){
    global $decodekey;
    $strBin = hex2bin($str);
    $td = mcrypt_module_open('des', '', 'ecb', '');
    mcrypt_generic_init($td, $decodekey, 0);
    $strRes = mdecrypt_generic($td,$strBin);
    return $strRes;
}

function get_accessToken(){
    global $appID;
    $r=file_get_contents('http://prapi.pernod-ricard-china.com:8080/wxWeb/apptoken?app_id='.$appID);
    $r = json_decode($r);
    if($r->ERRORMSG === '0' && isset($r->APP_TOKEN)){
        return trim(decrypt($r->APP_TOKEN));
    }else{
        return FALSE;
    }
}

//用于js分享配置的签名
function getjsticket(){
    global $mysqlserver;
    global $sqluser;
    global $sqlpass;
    global $sqldatabase;
    //echo $getAccessToken;exit;

    $con=mysqli_connect($mysqlserver,$sqluser,$sqlpass,$sqldatabase);
    // Check connection
    if (mysqli_connect_errno($con))
    {
        return 0;
    }else{
        $access_token = get_accessToken();
        $sql="select * from access_token where id=2";
        $result=mysqli_query($con,$sql);
        $arr=mysqli_fetch_array($result);
        mysqli_free_result($result);
        if(count($arr)){
            if(time()>$arr['expire_date']){
                //need update
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token.'&type=jsapi');
                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                $r = curl_exec($ch);
                curl_close($ch);
                $r = json_decode($r,true);
                if(!$r['errcode']){
                    $jsticket = $r['ticket'];
                    $newtime = time()+5000;
                    $sql="update access_token set access_token='".$jsticket."',expire_date='".$newtime."' where id=2";
                    $result=mysqli_query($con,$sql);
                    mysqli_free_result($result);
                    mysqli_close($con);
                    return $jsticket;
                }else{
                    return 0;
                }
            }else{
                mysqli_close($con);
                return $arr['access_token'];
            }
        }else{
            mysqli_close($con);
            return 0;
        }
    }
}

function responseMsgGen($fromUsername,$toUsername,$title,$desc,$picurl,$url){
    $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>
                    <ArticleCount>1</ArticleCount>
                    <Articles>
                    <item>
                    <Title><![CDATA[%s]]></Title> 
                    <Description><![CDATA[%s]]></Description>
                    <PicUrl><![CDATA[%s]]></PicUrl>
                    <Url><![CDATA[%s]]></Url>
                    </item>
                    </Articles>
                    </xml>";
    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(),$title,$desc,$picurl,$url);
    echo $resultStr;
}

function responseMoreMsgGen($fromUsername,$toUsername,$newsArray){
    $itemTpl = "<item>
                    <Title><![CDATA[%s]]></Title>
                    <Description><![CDATA[%s]]></Description>
                    <PicUrl><![CDATA[%s]]></PicUrl>
                    <Url><![CDATA[%s]]></Url>
                </item>";
    $item_str = "";
    foreach ($newsArray as $item){
        $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
    }
    $xmlTpl =  "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[news]]></MsgType>
                <ArticleCount>%s</ArticleCount>
                <Articles>
                $item_str
                </Articles>
                </xml>";

    $resultStr = sprintf($xmlTpl, $fromUsername, $toUsername, time(), count($newsArray));
    echo $resultStr;
}

function responseMsgText($fromUsername,$toUsername,$text){
    $textTpl = "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[text]]></MsgType>
                <Content><![CDATA[%s]]></Content>
                </xml>";
    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(),$text);
    echo $resultStr;
}

function responseMsgImage($fromUsername,$toUsername,$mediaid){
    $textTpl = "<xml>
                <ToUserName><![CDATA[%s]]></ToUserName>
                <FromUserName><![CDATA[%s]]></FromUserName>
                <CreateTime>%s</CreateTime>
                <MsgType><![CDATA[image]]></MsgType>
                <Image>
                <MediaId><![CDATA[%s]]></MediaId>
                </Image>
                </xml>";
    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(),$mediaid);
    echo $resultStr;
}

function responseMsgLFTapl($fromUsername,$toUsername){
    $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>
                    <ArticleCount>1</ArticleCount>
                    <Articles>
                    <item>
                    <Title><![CDATA[畅游虚拟实境，就差一副眼镜！]]></Title> 
                    <Description><![畅游虚拟实境，就差一副眼镜！]]></Description>
                    <PicUrl><![CDATA[http://www.dangdaimingshi.com/ddmswechat/images/lftvr.jpg]]></PicUrl>
                    <Url><![CDATA[http://lftapl.dangdaimingshi.com/index.html?openid=%s]]></Url>
                    </item>
                    </Articles>
                    </xml>";
    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $fromUsername);
    echo $resultStr;
}

function responseMsgPaperwall($fromUsername,$toUsername){
    $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>
                    <ArticleCount>1</ArticleCount>
                    <Articles>
                    <item>
                    <Title><![CDATA[群猴新意，开启溢彩新禧]]></Title> 
                    <Description><![群猴新意，开启溢彩新禧]]></Description>
                    <PicUrl><![CDATA[http://www.dangdaimingshi.com/ddmswechat/images/paperwall.jpg]]></PicUrl>
                    <Url><![CDATA[http://lftapl.dangdaimingshi.com/cnypaperwall/index1.html]]></Url>
                    </item>
                    </Articles>
                    </xml>";
    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $fromUsername);
    echo $resultStr;
}

function responseMsgForBarParty($fromUsername,$toUsername){
    $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>
                    <ArticleCount>1</ArticleCount>
                    <Articles>
                    <item>
                    <Title><![CDATA[玩转绚丽数字夜，赢取马爹利名士]]></Title> 
                    <Description><![玩转绚丽数字夜，赢取马爹利名士]]></Description>
                    <PicUrl><![CDATA[http://www.dangdaimingshi.com/ddmswechat/images/barparty151010.jpg]]></PicUrl>
                    <Url><![CDATA[http://puredee.championmkt.com/tesco/zsf/martell/game2.jsp?openid=%s]]></Url>
                    </item>
                    </Articles>
                    </xml>";
    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $fromUsername);
    echo $resultStr;
}

function responseMsgForBarParty2($fromUsername,$toUsername){
    $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[news]]></MsgType>
                    <ArticleCount>1</ArticleCount>
                    <Articles>
                    <item>
                    <Title><![CDATA[畅游虚拟实境，开启派对能量！]]></Title> 
                    <Description><![畅游虚拟实境，开启派对能量！]]></Description>
                    <PicUrl><![CDATA[http://www.dangdaimingshi.com/ddmswechat/images/lftvr2.jpg]]></PicUrl>
                    <Url><![CDATA[http://lafrenchtouch.martell.com]]></Url>
                    </item>
                    </Articles>
                    </xml>";
    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, time(), $fromUsername);
    echo $resultStr;
}

function addtoaccountsendlist($openid,$num){
    global $mysqlserver;
    global $sqluser;
    global $sqlpass;
    global $sqldatabase;

    $con=mysqli_connect($mysqlserver,$sqluser,$sqlpass,$sqldatabase);
    // Check connection
    if (mysqli_connect_errno($con))
    {
        return 0;
    }else{
        $sql="select * from account_send_list where num='".$num."' and openid='".$openid."' limit 1";
        $result=mysqli_query($con,$sql);
        $arr=mysqli_fetch_array($result);
        mysqli_free_result($result);
        if(!count($arr)){
            $sql="insert account_send_list(openid,num) value('".$openid."','".$num."')";
            $result=mysqli_query($con,$sql);
            mysqli_free_result($result);
        }
        mysqli_close($con);
    }
}

function getUserinfoByOpenId($openid){
    $ch = curl_init('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.get_accessToken().'&openid='.$openid.'&lang=zh_CN');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    // 要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_HEADER, 0); // 不要http header 加快效率
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $result = curl_exec($ch);
    curl_close($ch);
    file_put_contents('log/c.html', $result);
    $r = json_decode($result,true);
    if(!$r['errcode']){
        return $r;
    }else{
        file_put_contents('log/ccc.html', $r['errmsg'].'///');
        return 0;
    }
}

function sendmsg($openid,$msg){
    $data['touser'] = (string)$openid;
    $data['msgtype'] = 'text';
    $data['text'] = array('content'=>$msg);
    $data_string = json_encode($data,JSON_UNESCAPED_UNICODE);
    //$data_string = json_encode_ex($data);

    $ch = curl_init('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.get_accessToken());
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data_string))
    );

    $result = curl_exec($ch);
    curl_close($ch);
}

function getvrwintype($mobile){
    $ch = curl_init('http://lftapl.dangdaimingshi.com/vr/p/info.php?mobile='.$mobile);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);    // 要求结果为字符串且输出到屏幕上
    curl_setopt($ch, CURLOPT_HEADER, 0); // 不要http header 加快效率
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $result = curl_exec($ch);
    curl_close($ch);
    $r = json_decode($result);
    return $r->type;

}

function recordunsub($openid){
    global $mysqlserver;
    global $sqluser;
    global $sqlpass;
    global $sqldatabase;

    $mysqli = new mysqli($mysqlserver, $sqluser, $sqlpass, $sqldatabase);
    $mysqli->query('SET NAMES UTF8');

    $sql = 'select * from fixusers where openid="'.$openid.'"';
    $result = $mysqli->query($sql);
    $infoarr = $result->fetch_row();
    $result->close();
    if(isset($infoarr)){
        $sql = 'insert into oldloss (openid) value ("'.$openid.'")';
        $mysqli->query($sql);
    }else{
        $sql = 'insert into newloss (openid) value ("'.$openid.'")';
        $mysqli->query($sql);
    }
    $mysqli->close();
}

function recordsub($openid){
    global $mysqlserver;
    global $sqluser;
    global $sqlpass;
    global $sqldatabase;

    $mysqli = new mysqli($mysqlserver, $sqluser, $sqlpass, $sqldatabase);
    $mysqli->query('SET NAMES UTF8');
    $sql = 'insert into newadd (openid) value ("'.$openid.'")';
    $mysqli->query($sql);
    $mysqli->close();
}


?>
