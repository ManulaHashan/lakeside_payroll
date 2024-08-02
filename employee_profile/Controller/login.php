<?php

session_start();

include '../DB/DB.php';

if (isset($_REQUEST["submit"])) {
    // $query = "select uid,new_password from user where uid = (select user_uid from logindetails where username = '" . $_REQUEST["usr"] . "' and password='" . $_REQUEST["pwrd"] . "') and isactive = '1'";

    $query = "select u.uid,loging.new_password from user u, logindetails loging where u.uid=loging.User_uid and loging.username = '" . $_REQUEST["usr"] . "' and loging.password='" . $_REQUEST["pwrd"] . "' and u.isactive = '1'";
    
    $res = Search($query);
    if ($Result = mysqli_fetch_assoc($res)) 
    {   
        if (empty($Result["new_password"])) 
        {
            $_SESSION["uid"] = $Result["uid"];
            echo "SET";
        }
        else
        {
            $_SESSION["uid"] = $Result["uid"];
            echo "OK";
        }

    } else {
        echo "NO";
    }
}


if ($_REQUEST["request"] == "changePW") {

    $updateQuery = "update logindetails set new_password = '" . $_REQUEST["NEWPW"] . "', password = '" . $_REQUEST["NEWPW"] . "' where User_uid = '" . $_SESSION["uid"] . "'";
    $return = SUD($updateQuery);
    if ($return == "1") 
    {
        echo "OK";
    } 
    else 
    {
        echo "NO";
    }
}


if ($_REQUEST["request"] == "leavecount") {

    $querys = "select count(elrid) as notid from emp_leave_request where reqstatus = '0'";
    $rest = Search($querys);
    if ($results = mysqli_fetch_assoc($rest)) {
        $not_count = $results["notid"];
    }

    echo $not_count;
}

if ($_REQUEST["request"] == "sendetpnocode") {

    $TPNO = $_REQUEST["tpno"];
    $confirCode = $_REQUEST["fwdcode"];
    
    $querys = "select uid from user where tpno = '".$TPNO."'";
    $rest = Search($querys);
    if ($results = mysqli_fetch_assoc($rest)) 
    {
        $uid = $results["uid"]; 
        $querys = "Update logindetails set emp_resetcode='".$confirCode."' where User_uid='" . $uid . "'";
        $ret = SUD($querys);

        if ($ret == 1) 
        {
            $emailErr = sendSMS($uid,$_REQUEST["fwdcode"]);
        }
        else
        {
           $emailErr = '2'; 
        }
    }
    else
    {
        $emailErr = '0';
    }
    echo $emailErr;
}

if ($_REQUEST["request"] == "checkconfirmcode") {

    $querys = "select lgid from logindetails where emp_resetcode = '".$_REQUEST["code"]."'";
    $rest = Search($querys);
    if ($results = mysqli_fetch_assoc($rest)) {
        $logid = $results["lgid"];
    }
    else
    {
        $logid = "0";
    }

    echo $logid;
}

if ($_REQUEST["request"] == "resetpw") {

    $querys = "Update logindetails set password='".$_REQUEST["pass1"]."',new_password = '" . $_REQUEST["pass1"] . "' where lgid='" . $_REQUEST["lid"] . "'";
    $ret = SUD($querys);

    if ($ret == "1") 
    {
        $output = "1";
    }
    else
    {
        $output = "0";
    }


    echo $output;
}


function sendSMS($useID,$confirm_code) 
{
    $username = "derana_lab";
    $password = "DRn76Lab";
    $src = "DeranaLab";
    $delivery = "1";

    $resLeave_emp_name = Search("select mname,tpno from user where uid = '".$useID."'");
    if ($resultLeave_emp_name = mysqli_fetch_assoc($resLeave_emp_name)) 
    {
        $EMPName = $resultLeave_emp_name["mname"];
        $EMPTPNO = $resultLeave_emp_name["tpno"];
    }
    else
    {
        $EMPName = "Derana HRIS";
        $EMPTPNO = "0";
    } 

    $msg = "Dear ".$EMPName.",\n\n";
    $msg .= "Your Confirmation Code Is : ".$confirm_code.".\n\n";
    $msg .= "Thank you! (Derana HRIS)"; 

    $url = "https://sms.textware.lk:5001/sms/send_sms.php?username=" . $username . "&password=" . $password . "&src=" . $src . "&dst=" . $EMPTPNO . "&msg=" . $msg . "&dr=" . $delivery;

    $url = str_replace(" ", "+", $url);
    $url = str_replace("\n", "%0A", $url);
    $result = file_get_contents($url);

    if ($result === false) 
    {
      $res_msg = "2";
    } 
    else 
    {
      $res_log = SUD("insert into smslog(tpno, uid, operation_msg, action) values('" . $EMPTPNO . "','" . $EMPID . "','" . $result . "','Password-Reset-SMS')");
      $res_msg = "1";
    }

    return $res_msg;  
}

session_write_close();
?>