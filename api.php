<?php 
require_once('config.php');
require_once('functions.php');
header("Access-Control-Allow-Origin: *");

$action = trim($_REQUEST['action']);
if(!isset($action) || empty($action)) {
    $msg['haserror'] = 1;
    $msg['errormsg'] = 'action is required';
    echo json_encode($msg);
    exit;
}

switch($action){
    case 'getUserinfoByOpenId' :
        $openid = trim($_GET['openid']);
        if(!isset($openid) || empty($openid)) {
            $msg['haserror'] = 1;
            $msg['errormsg'] = 'openid is required';
            echo json_encode($msg);
            exit;
        }
        if($userinfo = getUserinfoByOpenId($openid)){
            $msg['haserror'] = 0;
            $msg['content'] = $userinfo;
            echo json_encode($msg);
        }else{
            $msg['haserror'] = 1;
            $msg['errormsg'] = 'get user info error';
            echo json_encode($msg);
            exit;
        }
        break;
    case 'getac' :
        echo get_accessToken();
        break;
}

?>