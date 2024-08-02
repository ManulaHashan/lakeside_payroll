
<?php
date_default_timezone_set('Asia/Colombo');
include '../../DB/DB.php';

$Auth_arr = [4,3,5,7,6,8,21,35,37,48,33,31,22];

foreach ($Auth_arr as $auth_id) 
{
    $resLeave_Count = Search("select count(a.elrid) as reqCount from emp_leave_request a,user b where a.empid = b.uid and a.reqstatus='0' and (b.auth_person_id = '".$auth_id."' or b.sec_auth_person_id = '".$auth_id."')");
    if ($resultLeave_count = mysqli_fetch_assoc($resLeave_Count)) 
    {
         if ($resultLeave_count["reqCount"] >= "1") 
         {
            sendSMS($auth_id);
         }
         else
         {
            echo "No Reques in : ".$auth_id;
         }
    }
    else
    {
        echo "No Reques in : ".$auth_id;
    } 
}


  function sendSMS($authID) 
  {
      $username = "derana_lab";
      $password = "DRn76Lab";
      $src = "DeranaLab";
      $delivery = "1";

      $resLeave_auth_name = Search("select mname,tpno from user where uid='".$authID."'");
      if ($resultLeave_auth_name = mysqli_fetch_assoc($resLeave_auth_name)) 
      {
          $AuthName = $resultLeave_auth_name["mname"];
          $AuthTPNO = $resultLeave_auth_name["tpno"]; 
      }
      else
      {
          $AuthName = "Derana HRIS";
          $AuthTPNO = "";
      }

      $msg = "Dear ".$AuthName.",\n\n";
      $msg .= "The employees assigned to you today have applied for some leave requests. Please give your approval for it.\n\n";
      $msg .= "Thank you! (Derana HRIS)"; 

      $url = "https://sms.textware.lk:5001/sms/send_sms.php?username=" . $username . "&password=" . $password . "&src=" . $src . "&dst=" . $AuthTPNO . "&msg=" . $msg . "&dr=" . $delivery;

      $url = str_replace(" ", "+", $url);
      $url = str_replace("\n", "%0A", $url);

      // Send the SMS
      $result = file_get_contents($url);

      // Check if SMS was sent successfully
      if ($result === false) 
      {
        return "Failed to send SMS";
      } 
      else 
      {
        $res_log = SUD("insert into smslog(tpno, uid, operation_msg, action) values('" . $AuthTPNO . "','" . $authID . "','" . $result . "','Auth-SMS')");

        $res_upd = SUD("update emp_leave_request a,user b set a.conf_date='" . date("Y-m-d") . "',a.conf_time='" . date("H:i:s") . "',a.conf_by='" . $authID . "',a.conf_status='1' where a.empid = b.uid and a.reqstatus='0' and (b.auth_person_id = '" . $authID . "' or b.sec_auth_person_id = '" . $authID . "')");
        
        return "SMS sent successfully";
      }
  }

?>
