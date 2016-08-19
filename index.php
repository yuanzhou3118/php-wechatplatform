<?php
//define your token
require_once('config.php');
require_once('functions.php');

$wechatObj = new wechatCallbackapiTest();

//$wechatObj->valid();
$wechatObj->assign();

class wechatCallbackapiTest
{

    public function assign(){
        $xmlResult = simplexml_load_string(file_get_contents('php://input'),'SimpleXMLElement', LIBXML_NOCDATA);
        // $st = (array)$xmlResult;
        // file_put_contents('log/a.html', json_encode($st));
        if(strtolower($xmlResult->MsgType) == 'event'){
            if($xmlResult->Event == 'subscribe'){
                echo "success";
                recordsub($xmlResult->FromUserName);
//                responseMsgGen($xmlResult->FromUserName,$xmlResult->ToUserName,'欧洲杯，你属于哪一“派”？','“派”不同，不相为谋','http://www.dangdaimingshi.com/ddmswechat/images/welcome0629.jpg','http://lftapl.dangdaimingshi.com/quiz/?utm_source=wechat&utm_medium=wmsg&utm_campaign=quiz&openid='.$xmlResult->FromUserName);
                $content = array();
                $content[] = array("Title"=>'欧洲杯，你属于哪一“派”？', "Description"=>'“派”不同，不相为谋', "PicUrl"=>"http://www.dangdaimingshi.com/ddmswechat/images/welcome0629.jpg", "Url" =>'http://lftapl.dangdaimingshi.com/quiz/?utm_source=wechat&utm_medium=wmsg&utm_campaign=quiz&openid='.$xmlResult->FromUserName);
                $content[] = array("Title"=>'探寻奥秘，今晚是否够“绚”', "Description"=>"参与互动，赢取免费马爹利名士", "PicUrl"=>"http://www.dangdaimingshi.com/ddmswechat/images/txam.jpg", "Url" =>"http://mnbq1pro.gypserver.com/act/index.php/Home/Consumer");
                responseMoreMsgGen($xmlResult->FromUserName,$xmlResult->ToUserName,$content);
                exit;
            }

            if($xmlResult->Event == 'unsubscribe'){
                recordunsub($xmlResult->FromUserName);
                exit;
            }

            if(strtolower($xmlResult->Event) == 'click'){
                if(strtolower($xmlResult->EventKey) == 'ddms_wdlq'){
                    responseMsgText($xmlResult->FromUserName,$xmlResult->ToUserName,'请输入您的手机号!');
                    exit;
                }

                if(strtolower($xmlResult->EventKey) == 'ddms_vrh5'){
                    responseMsgGen($xmlResult->FromUserName,$xmlResult->ToUserName,'还没玩过VR派对？友谊的小船说翻就翻!','还没玩过VR派对？友谊的小船说翻就翻！','http://www.dangdaimingshi.com/ddmswechat/images/vrwelcome.jpg','http://lftapl.dangdaimingshi.com/vr/?openid='.$xmlResult->FromUserName);
                    exit;
                }
                if(strtolower($xmlResult->EventKey) == 'ddms_quizh5'){
                    responseMsgGen($xmlResult->FromUserName,$xmlResult->ToUserName,'欧洲杯，你属于哪一“派”？','“派”不同，不相为谋','http://www.dangdaimingshi.com/ddmswechat/images/welcome0629.jpg','http://lftapl.dangdaimingshi.com/quiz/?utm_source=wechat&utm_medium=menu&utm_campaign=quiz&openid='.$xmlResult->FromUserName);
                    exit;
                }
            }

            if(strtolower($xmlResult->Event) == 'view' && $xmlResult->EventKey == 'http://tesco.championmkt.com/tesco/zsf/martell/game2.jsp'){
                addtoaccountsendlist($xmlResult->FromUserName,1);
                exit;
            }
        }

        if($xmlResult->MsgType == 'text'){
            $text = strtolower(trim(json_decode('"'.$xmlResult->Content.'"')));
            if($text == '出色出彩'){
               responseMsgGen($xmlResult->FromUserName,$xmlResult->ToUserName,'欧洲杯，你属于哪一“派”？','“派”不同，不相为谋','http://www.dangdaimingshi.com/ddmswechat/images/welcome0629.jpg','http://lftapl.dangdaimingshi.com/quiz/?utm_source=wechat&utm_medium=wmsg&utm_campaign=quiz&openid='.$xmlResult->FromUserName);
               exit;
            }
            if($text == '派对能量'){
               responseMsgForBarParty2($xmlResult->FromUserName,$xmlResult->ToUserName);
               exit;
            }
            if($text == '锋潮之旅' || $text == 'virtual journey'){
               responseMsgImage($xmlResult->FromUserName,$xmlResult->ToUserName,'nRnUD3tV_i1JHHH2vQm2kOZsAMuZpqK8fNlal7NvyPU');
               exit;
            }
            if(preg_match('/^1[34578]\d{9}$/',$text)){
                echo "success";
                $type = getvrwintype($text);
                if($type == '23'){
                    sendmsg($xmlResult->FromUserName,'您获得的奖品已被记录，活动结束后会寄到您活动时留下的地址，请耐心等待！');
                }else if($type == '4'){
                    sendmsg($xmlResult->FromUserName,'领取礼券地址：http://www.wemart.cn/v2/weimao/index.html?shopId=shop000201603099411#gc/1883/6');
                }else if($type == '5'){
                    sendmsg($xmlResult->FromUserName,'领取礼券地址：http://www.wemart.cn/v2/weimao/index.html?shopId=shop000201603099411#gc/1875/6');
                }else{
                    sendmsg($xmlResult->FromUserName,'马爹利名士邀您加入VR派对，获取丰厚奖品！http://lftapl.dangdaimingshi.com/vr/?openid='.$xmlResult->FromUserName);
                }
                exit;
            }
        }
    }

    public function valid()
    {
        $echoStr = $_GET["echostr"];
        //echo 2;exit;
        //valid signature , option
        if($this->checkSignature()){
            echo $echoStr;
            exit;
        }
    }

        
    private function checkSignature()
    {
        // you must define TOKEN by yourself
        if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }
        
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
                
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        
        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }
}

?>
