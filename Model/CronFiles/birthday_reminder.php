<?php
date_default_timezone_set('Asia/Colombo');
include '../../DB/DB.php';

$DaY = date("d");
$Month = date("m");

$resBday = Search("select uid from user where uid !='2' and MONTH(dob) = '" . $Month . "' and DAY(dob) = '" . $DaY . "' and isactive='1'");
while ($resultBday = mysqli_fetch_assoc($resBday)) 
{
    $userid = $resultBday["uid"];
    sendSMS($userid);
}



function sendSMS($userID) 
{
    $username = "derana_lab";
    $password = "DRn76Lab";
    $src = "DeranaLab";
    $delivery = "1";

    $resBday_user = Search("select mname,tpno from user where uid='".$userID."'");
    if ($resultBday_user = mysqli_fetch_assoc($resBday_user)) 
    {
        $UserName = $resultBday_user["mname"];
        $UserTPNO = $resultBday_user["tpno"]; 
    }
    else
    {
        $UserName = "Derana HRIS";
        $UserTPNO = "";
    }

    $msg = "Dear ".$UserName.",\n\n";
    $msg .= "We appreciate all of your hard work in the past year. Wishing you a happy and relaxing birthday!\n\n";
    $msg .= "- Derana HRIS System -";

    $url = "https://sms.textware.lk:5001/sms/send_sms.php?username=" . $username . "&password=" . $password . "&src=" . $src . "&dst=" . $UserTPNO . "&msg=" . $msg . "&dr=" . $delivery;

    $url = str_replace(" ", "+", $url);
    $url = str_replace("\n", "%0A", $url);

    // Send the SMS
    $result = file_get_contents($url);

    //Check if SMS was sent successfully
    if ($result === false) 
    {
      return "Failed to send SMS";
    } 
    else 
    {
      $res_log = SUD("insert into smslog(tpno, uid, operation_msg, action) values('" . $UserTPNO . "','" . $userID . "','" . $result . "','Birthday-SMS')");
    }
}

?>