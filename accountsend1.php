<?php
require_once('config.php');
require_once('functions.php');


function accountsend(){
    global $mysqlserver;
    global $sqluser;
    global $sqlpass;
    global $sqldatabase;
    $con=mysqli_connect($mysqlserver,$sqluser,$sqlpass,$sqldatabase);
    if (mysqli_connect_errno($con))
    {
        return 0;
    }else{
        // $sql="update access_token set expire_date=9 where id=1";
        // $result=mysqli_query($con,$sql);
        // mysqli_free_result($result);
        $sql="select openid from account_send_list where num=1";
        $result=mysqli_query($con,$sql);
        while($row = mysqli_fetch_array($result)){
            $openidgroup[] = $row['openid'];
        }
        mysqli_free_result($result);
        mysqli_close($con);
        $tmsg1 = '恭喜您获得马爹利名士法式派对锋潮限量版一瓶，请向现场工作人员兑换奖品！';
        $tmsg2 = '很遗憾您没有获奖，感谢您的参与，请继续关注我们的其他活动。';
        $n = count($openidgroup);
        $m = 1;
        if($n < 1) {
            exit;
        }
        if($n <= $m){
            foreach ($openidgroup as $openid) {
                //echo $openid.'<br />';
                sendmsg($openid,$tmsg1);
            }
            exit;
        }else{
            $temp = array();
            while(count($temp)<$m){
                $a=rand(0,$n-1);
                if(!in_array($a, $temp)){
                    $temp[] = $a;
                }
            }
            for ($i=0; $i < $n; $i++) {
                if(in_array($i,$temp)){
                    //echo $openidgroup[$i].'//yes<br />';
                    sendmsg($openidgroup[$i],$tmsg1);
                }else{
                    //echo $openidgroup[$i].'//no<br />';
                    sendmsg($openidgroup[$i],$tmsg2);
                }
            }
        }
    }  
}

// function sendmsg($openid,$msg){
//     $data['touser'] = (string)$openid;
//     $data['msgtype'] = 'text';
//     $data['text'] = array('content'=>$msg);
//     $data_string = json_encode($data,JSON_UNESCAPED_UNICODE);

//     $ch = curl_init('https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.get_accessToken());
//     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
//     curl_setopt($ch, CURLOPT_POSTFIELDS,$data_string);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
//     curl_setopt($ch, CURLOPT_HTTPHEADER, array(
//         'Content-Type: application/json',
//         'Content-Length: ' . strlen($data_string))
//     );
     
//     $result = curl_exec($ch);
//     curl_close($ch);
// }

accountsend();


?>