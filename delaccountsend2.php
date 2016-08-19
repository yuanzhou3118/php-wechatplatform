<?php
require_once('config.php');
require_once('functions.php');


function delaccountsend(){
    global $mysqlserver;
    global $sqluser;
    global $sqlpass;
    global $sqldatabase;
    $con=mysqli_connect($mysqlserver,$sqluser,$sqlpass,$sqldatabase);
    if (mysqli_connect_errno($con))
    {
        return 0;
    }else{
        $sql="delete from account_send_list where num=2";
        $result=mysqli_query($con,$sql);
        mysqli_free_result($result);
        mysqli_close($con);
    }
    
}

delaccountsend();


?>