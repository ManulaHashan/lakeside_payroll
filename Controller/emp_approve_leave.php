<?php
error_reporting(0);
session_start();
include '../DB/DB.php';
$DB = new Database();
date_default_timezone_set('Asia/Colombo');

//search 
if (isset($_REQUEST["request"])) {
    $out;
 if($_REQUEST["request"] == "setSession"){

        $_SESSION["exportdata"] = $_POST["data"];  

        echo $_SESSION["exportdata"];

    }
    else if ($_REQUEST["request"] == "getleave") {

        $out = "<table class='table table-striped table-bordered'><thead class='thead-dark' style='position : sticky; top : 0;  z-index: 0; background-color: #ffffff;'>
        <tr>
        <th>Leave Request</th>
        <th>Confirm Status</th>
        </tr></thead>";

        

        $query;

        if ($_REQUEST["decission"] == "NC") 
        {
            $query = "select * from emp_leave_request where reqstatus ='0' and conf_status ='0'";
        }
        elseif ($_REQUEST["decission"] == "0") 
        {
            $query = "select * from emp_leave_request where reqstatus='0' and conf_status = '1'";
        }
        else
        {
            $query = "select * from emp_leave_request where request_date between '".$_REQUEST["from"]."' and '".$_REQUEST["to"]."' and reqstatus='".$_REQUEST["decission"]."'";
        }
      
        
        $res = Search($query);
        while ($result = mysqli_fetch_assoc($res)) {

           $querys = "select fname,auth_person_id,sec_auth_person_id from user where uid ='".$result["empid"]."'";

           $ress = Search($querys);
           if ($results = mysqli_fetch_assoc($ress)) {
              $uname = $results["fname"];
              $Auth_ID = $results["auth_person_id"];
              $SecondAuth_ID = $results["sec_auth_person_id"];
           }

           
           $ressAPN = Search("select fname from user where uid ='".$Auth_ID."'");
           if ($resultAPN = mysqli_fetch_assoc($ressAPN)) {
              $Auth_P_Name = $resultAPN["fname"];
           }
           else
           {
              $Auth_P_Name = "None";
           }

           $ressSecondAPN = Search("select fname from user where uid ='".$SecondAuth_ID."'");
           if ($resultSecondAPN = mysqli_fetch_assoc($ressSecondAPN)) {
              $Second_Auth_P_Name = $resultSecondAPN["fname"];
           }
           else
           {
             $Second_Auth_P_Name = "None";
           }

            if ($result["reqstatus"] == "0" && $result["conf_status"] == "0") {
                
                $out .= "<tr id='".$result["elrid"]."' onclick='loadDataForFields(id)' style='cursor:pointer;'>";
                $out .= "<td style='background-color: #81b2c9; color: black;'><b>Employee : </b> ".$uname." | <b>Leave Type : </b>" . $result["leave_type"] . " |  <b>Date of Leave :</b> ".$result["fromdate"]." | <b>Number of Days :</b> ".$result["days"]."</br><b>Time Slot :</b>".$result["time_slot"]." | <b>Date The Request Was Sent :</b> ".$result["request_date"]." | <b>Authorized Person :</b> ".$Auth_P_Name." | <b>Second Authorized Person :</b> ".$Second_Auth_P_Name." |  <b>Decision : Not-Confirmed</b></td>";
            }
            else if ($result["reqstatus"] == "0" && $result["conf_status"] == "1") {
                
                $out .= "<tr id='".$result["elrid"]."' onclick='loadDataForFields(id)' style='cursor:pointer;'>";
                $out .= "<td style='background-color: #DAA520; color: black;'><b>Employee : </b> ".$uname." | <b>Leave Type : </b>" . $result["leave_type"] . " |  <b>Date of Leave :</b> ".$result["fromdate"]." | <b>Number of Days :</b> ".$result["days"]."</br><b>Time Slot :</b>".$result["time_slot"]." | <b>Date The Request Was Sent :</b> ".$result["request_date"]." | <b>Authorized Person :</b> ".$Auth_P_Name." | <b>Second Authorized Person :</b> ".$Second_Auth_P_Name." |  <b>Decision : Pending</b></td>";
            }
            else
            {
                $out .= "<tr>";
                $out .= "<td><b>Employee : </b> ".$uname." | <b>Leave Type : </b>" . $result["leave_type"] . " |  <b>Date of Leave :</b> ".$result["fromdate"]." | <b>Number of Days :</b> ".$result["days"]." </br><b>Time Slot :</b>".$result["time_slot"]." | <b>Date The Request Was Sent :</b> ".$result["request_date"]." | <b>Authorized Person :</b> ".$Auth_P_Name." | <b>Second Authorized Person :</b> ".$Second_Auth_P_Name."</td>";
            }

            if ($result["app_status"] == "0") {
                
                $out .= "<td></td>";
               
            }
            else if ($result["app_status"] == "2") 
            {
                $queryUserDecline = "select fname as usernames from user where uid='" . $result["app_by"] . "'";
                $resUserDecline = Search($queryUserDecline);
                if ($resultUserDecline = mysqli_fetch_assoc($resUserDecline)) {
                    
                   $UserDecline = $resultUserDecline["usernames"];

                }

                $out .= "<td style='background-color: #ff0000; color: black;'><b>Declined Date :</b> ".$result["app_date"]." |  <b>Declined Time :</b> ".$result["app_time"]." | <b>Declined By :</b> ".$UserDecline." |  <b>Decision : Decline</b></td>";
            }
            else
            {   
                $queryUserConfirm = "select fname as username from user where uid='" . $result["app_by"] . "'";
                $resUserConfirm = Search($queryUserConfirm);
                if ($resultUserConfirm = mysqli_fetch_assoc($resUserConfirm)) {
                    
                   $UserConfirm = $resultUserConfirm["username"];

                }

                 $out .= "<td style='background-color: #47b833; color: black;'><b>Approved Date :</b> ".$result["app_date"]." |  <b>Approved Time :</b> ".$result["app_time"]." | <b>Approved By :</b> ".$UserConfirm." |  <b>Decision : Confirm</b></td>";
            }
           
          $out .="</tr>";  
        }
        
        $out .= "</table>";

        echo $out;
    }
    else if ($_REQUEST["request"] == "LeaveEmail") 
    {
       $resLeave = Search("select empid,fromdate,app_date,app_by,leave_type from emp_leave_request where elrid='" . $_REQUEST["eid"] . "' and app_status='1'");
       if ($resultLeave = mysqli_fetch_assoc($resLeave)) 
       {
            $resLeavePerson = Search("select mname as callleave,epfno,fname as empfname from user where uid='" . $resultLeave["empid"] . "'");
            if ($resultLeavePerson = mysqli_fetch_assoc($resLeavePerson))
            {    
                 $LeavePersonFname = $resultLeavePerson["empfname"];
                 $LeavePerson = $resultLeavePerson["callleave"];
                 $LeavePersonEpf = $resultLeavePerson["epfno"];
            } 

            $resLeaveApprove = Search("select fname as callapp,mname as empcall from user where uid='" . $resultLeave["app_by"] . "'");
            if ($resultLeaveApprove = mysqli_fetch_assoc($resLeaveApprove))
            {
                $LeaveApprove = $resultLeaveApprove["callapp"];
                $LeaveApproveCall = $resultLeaveApprove["empcall"];
            }

            $LeaveDate = $resultLeave["fromdate"];
            $ApproveDate = $resultLeave["app_date"];
            $LeaveType = $resultLeave["leave_type"];

            // $out = "<table>
            //         <tr>
            //           <td>
            //             <p> Dear All,</p><br>

            //             <p> The Following Employee: Name <b>".$LeavePersonFname." (".$LeavePerson.")</b>,&nbsp;Emp No :&nbsp;<b>".$LeavePersonEpf."</b> will be on a <b> ".$LeaveType." </b> on <b> ".$LeaveDate." </b> and the requested <b> ".$LeaveType." </b> was approved by <b> ".$LeaveApprove." (".$LeaveApproveCall.") </b> on <b>".$ApproveDate."</b></p><br> 
            //           </td>  
            //         </tr>
            //         <tr>
            //            <td><p> Satlo Payroll</p></td>
            //         </tr>
            //         <tr>
            //            <td><p> Thank You  </p></td>
            //         </tr>
            //       </table>";


            $email_data = array(

                      "Email" => array(
                          array(
                              "Leave_Person_Fname" => $LeavePersonFname,
                              "Leave_Person" => $LeavePerson,
                              "Leave_Person_EPF" => $LeavePersonEpf,
                              "Leave_TYP" => $LeaveType,
                              "Leave_DATE" => $LeaveDate,
                              "Leave_Approve" => $LeaveApprove,
                              "Leave_Approve_Call" => $LeaveApproveCall,
                              "Approve_Date" => $ApproveDate
                          )
                      ) 
                  );

            $json_data = json_encode($email_data);

            echo $json_data;

       }
       
    }
    else if ($_REQUEST["request"] == "RequestToAuthorizedPerson") 
    {
       $resLeave = Search("select empid,fromdate,app_date,app_by,leave_type from emp_leave_request where elrid='" . $_REQUEST["eid"] . "' and conf_status='1'");
       if ($resultLeave = mysqli_fetch_assoc($resLeave)) 
       {
            $resLeavePerson = Search("select mname as callleave,epfno,fname as empfname,auth_person_id from user where uid='" . $resultLeave["empid"] . "'");
            if ($resultLeavePerson = mysqli_fetch_assoc($resLeavePerson))
            {    
                 $LeavePersonFname = $resultLeavePerson["empfname"];
                 $LeavePerson = $resultLeavePerson["callleave"];
                 $LeavePersonEpf = $resultLeavePerson["epfno"];
                 $LeaveAuthor = $resultLeavePerson["auth_person_id"];
            } 

            $resLeaveApprove = Search("select mname as empcall from user where uid='" . $LeaveAuthor . "'");
            if ($resultLeaveApprove = mysqli_fetch_assoc($resLeaveApprove))
            {
                $LeaveApproveCall = $resultLeaveApprove["empcall"];
            }

            $LeaveDate = $resultLeave["fromdate"];
            $LeaveType = $resultLeave["leave_type"];
            $LeaveEMP = $resultLeave["empid"];

            // $out = "<table>
            //         <tr>
            //           <td>
            //             <p> Dear ".$LeaveApproveCall.",</p><br>

            //             <p> The Following Employee: Name <b>".$LeavePersonFname." (".$LeavePerson.")</b>,&nbsp;Emp No :&nbsp;<b>".$LeavePersonEpf."</b> has requested <b> ".$LeaveType." </b> on <b> ".$LeaveDate." </b>.Please approve this leave request through the link below.</p><br> 
            //           </td>
            //         </tr>
            //         <tr>
            //            <td><p> Link : http://appexsl.com/satlo/Views/approve_author.php?UID=".$LeaveAuthor."&EMPID=".$LeaveEMP."</p></td>
            //         </tr>
            //         <tr>
            //            <td><p> Satlo Payroll</p></td>
            //         </tr>
            //         <tr>
            //            <td><p> Thank You  </p></td>
            //         </tr>
            //       </table>";

            

            $email_data = array(

                "Email" => array(
                    array(
                        "Auth_person" => $LeaveApproveCall,
                        "Leave_Person_Fname" => $LeavePersonFname,
                        "Leave_Person" => $LeavePerson,
                        "Leave_Person_EPF" => $LeavePersonEpf,
                        "Leave_TYP" => $LeaveType,
                        "Leave_DATE" => $LeaveDate,
                        "Leave_AUTH" => $LeaveAuthor,
                        "Leave_EMP" => $LeaveEMP
                    )
                ) 
            );

            $json_data = json_encode($email_data);

            echo $json_data."#@#".$LeaveAuthor;

       }
       
    }
    else if ($_REQUEST["request"] == "RequestToSecondAuthorizedPerson") 
    {
       $resLeave = Search("select empid,fromdate,app_date,app_by,leave_type from emp_leave_request where elrid='" . $_REQUEST["eid"] . "' and conf_status='1'");
       if ($resultLeave = mysqli_fetch_assoc($resLeave)) 
       {
            $resLeavePerson = Search("select mname as callleave,epfno,fname as empfname,sec_auth_person_id from user where uid='" . $resultLeave["empid"] . "'");
            if ($resultLeavePerson = mysqli_fetch_assoc($resLeavePerson))
            {    
                 $LeavePersonFname = $resultLeavePerson["empfname"];
                 $LeavePerson = $resultLeavePerson["callleave"];
                 $LeavePersonEpf = $resultLeavePerson["epfno"];
                 $LeaveSecondAuthor = $resultLeavePerson["sec_auth_person_id"];
            } 

            $resLeaveApprove = Search("select mname as empcall from user where uid='" . $LeaveSecondAuthor . "'");
            if ($resultLeaveApprove = mysqli_fetch_assoc($resLeaveApprove))
            {
                $LeaveApproveCall = $resultLeaveApprove["empcall"];
            }

            $LeaveDate = $resultLeave["fromdate"];
            $LeaveType = $resultLeave["leave_type"];
            $LeaveEMP = $resultLeave["empid"];

            // $out = "<table>
            //         <tr>
            //           <td>
            //             <p> Dear ".$LeaveApproveCall.",</p><br>

            //             <p> The Following Employee: Name <b>".$LeavePersonFname." (".$LeavePerson.")</b>,&nbsp;Emp No :&nbsp;<b>".$LeavePersonEpf."</b> has requested <b> ".$LeaveType." </b> on <b> ".$LeaveDate." </b>.Please approve this leave request through the link below.</p><br> 
            //           </td>
            //         </tr>
            //         <tr>
            //            <td><p> Link : http://appexsl.com/satlo/Views/approve_author.php?UID=".$LeaveSecondAuthor."&EMPID=".$LeaveEMP."</p></td>
            //         </tr>
            //         <tr>
            //            <td><p> Satlo Payroll</p></td>
            //         </tr>
            //         <tr>
            //            <td><p> Thank You  </p></td>
            //         </tr>
            //       </table>";

            $email_data = array(

                "Email" => array(
                    array(
                        "Auth_person" => $LeaveApproveCall,
                        "Leave_Person_Fname" => $LeavePersonFname,
                        "Leave_Person" => $LeavePerson,
                        "Leave_Person_EPF" => $LeavePersonEpf,
                        "Leave_TYP" => $LeaveType,
                        "Leave_DATE" => $LeaveDate,
                        "Leave_AUTH" => $LeaveSecondAuthor,
                        "Leave_EMP" => $LeaveEMP
                    )
                ) 
            );

            $json_data = json_encode($email_data);

            echo $json_data."#@#".$LeaveSecondAuthor;

       }
       
    }
    else if ($_REQUEST["request"] == "approveleave") {


       $queryYears = "select registerdDate from user where uid = '" . $_REQUEST["employeeID"] . "'";
       $resYears = Search($queryYears);

       if ($resultYears = mysqli_fetch_assoc($resYears)) 
       {
           $joinDate = $resultYears["registerdDate"];
       }

       $YearDiff = date('Y-m-d') - $joinDate;
       $date1 = $joinDate;
       $date2 = date('Y-m-d');

       $diff = abs(strtotime($date2) - strtotime($date1));

       $years = floor($diff / (365*60*60*24));
       $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
       $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));


       if ($_REQUEST["LeaveType"] == "Nopay Leave")
       {
          
            $querys = "Update emp_leave_request set app_date='" . date("Y-m-d") . "',app_time='" . date("H:i:s") . "',app_by='".$_SESSION["uid"]."',app_status='1',reqstatus='1' where elrid='" . $_REQUEST["eid"] . "'";
                         
            $ret = SUD($querys);

            if ($ret == 1) 
            {
                $query = "select * from emp_leave_request where elrid='" . $_REQUEST["eid"] . "'";
                $res = Search($query);
                if ($result = mysqli_fetch_assoc($res)) {
                    
                    $E_id = $result["empid"];
                    $E_ltype = $result["leave_type"];
                    $E_date = $result["fromdate"];
                    $E_days = $result["days"];
                    $E_timeslot = $result["time_slot"];

                    $queryLeave = "insert into employee_leave(date, type, uid, days, time_slot) values('" . $E_date . "','".$E_ltype."','".$E_id."','".$E_days."','".$E_timeslot."')";
                    SUD($queryLeave);

                    $queryNotification = "insert into notification(n_date, n_time, n_status, n_user, erid) values('" . date("Y-m-d") . "','".date("H:i:s")."','0','".$E_id."','".$_REQUEST["eid"]."')";
                    SUD($queryNotification);

                }

                echo "1"; 
            } 
            else 
            {
                echo "0";
            }
                  

       }
       else if ($_REQUEST["LeaveType"] == "Liue Leave")
       {
            $querys = "Update emp_leave_request set app_date='" . date("Y-m-d") . "',app_time='" . date("H:i:s") . "',app_by='".$_SESSION["uid"]."',app_status='1',reqstatus='1' where elrid='" . $_REQUEST["eid"] . "'";
                         
            $ret = SUD($querys);

            if ($ret == 1) 
            {
                $query = "select * from emp_leave_request where elrid='" . $_REQUEST["eid"] . "'";
                $res = Search($query);
                if ($result = mysqli_fetch_assoc($res)) {
                    
                    $E_id = $result["empid"];
                    $E_ltype = $result["leave_type"];
                    $E_date = $result["fromdate"];
                    $E_days = $result["days"];
                    $E_timeslot = $result["time_slot"];

                    $queryLeave = "insert into employee_leave(date, type, uid, days, time_slot) values('" . $E_date . "','".$E_ltype."','".$E_id."','".$E_days."','".$E_timeslot."')";
                    SUD($queryLeave);

                    $queryNotification = "insert into notification(n_date, n_time, n_status, n_user, erid) values('" . date("Y-m-d") . "','".date("H:i:s")."','0','".$E_id."','".$_REQUEST["eid"]."')";
                    SUD($queryNotification);

                }

                echo "1"; 
            } 
            else 
            {
                echo "0";
            }
       }
       else if ($_REQUEST["LeaveType"] == "Company Leave")
       {
            $querys = "Update emp_leave_request set app_date='" . date("Y-m-d") . "',app_time='" . date("H:i:s") . "',app_by='".$_SESSION["uid"]."',app_status='1',reqstatus='1' where elrid='" . $_REQUEST["eid"] . "'";
                         
            $ret = SUD($querys);

            if ($ret == 1) 
            {
                $query = "select * from emp_leave_request where elrid='" . $_REQUEST["eid"] . "'";
                $res = Search($query);
                if ($result = mysqli_fetch_assoc($res)) {
                    
                    $E_id = $result["empid"];
                    $E_ltype = $result["leave_type"];
                    $E_date = $result["fromdate"];
                    $E_days = $result["days"];
                    $E_timeslot = $result["time_slot"];

                    $queryLeave = "insert into employee_leave(date, type, uid, days, time_slot) values('" . $E_date . "','".$E_ltype."','".$E_id."','".$E_days."','".$E_timeslot."')";
                    SUD($queryLeave);

                    $queryNotification = "insert into notification(n_date, n_time, n_status, n_user, erid) values('" . date("Y-m-d") . "','".date("H:i:s")."','0','".$E_id."','".$_REQUEST["eid"]."')";
                    SUD($queryNotification);

                }

                echo "1"; 
            } 
            else 
            {
                echo "0";
            }
       }
       else
       {

           if ($years >= "0" && $years < "2") 
           {

              if ($years == "0" && $months == "0") 
              {

                  $GetHalf = 0;
                  $GetShort = 0;
                  $Half_Available = 0;
                  $Short_Available = 0;
                  $Total_leave = 0;
                  $CheckMonth = "Empty";

                  if ($Half_Available == "0" && $Short_Available == "0")
                  {
                      if ($_REQUEST["LeaveType"] == "Liue Leave") 
                      {
                          $querys = "Update emp_leave_request set app_date='" . date("Y-m-d") . "',app_time='" . date("H:i:s") . "',app_by='".$_SESSION["uid"]."',app_status='1',reqstatus='1' where elrid='" . $_REQUEST["eid"] . "'";
                             
                              $ret = SUD($querys);

                              if ($ret == 1) 
                              {
                                  $query = "select * from emp_leave_request where elrid='" . $_REQUEST["eid"] . "'";
                                  $res = Search($query);
                                  if ($result = mysqli_fetch_assoc($res)) {
                                      
                                      $E_id = $result["empid"];
                                      $E_ltype = $result["leave_type"];
                                      $E_date = $result["fromdate"];
                                      $E_days = $result["days"];
                                      $E_timeslot = $result["time_slot"];

                                      $queryLeave = "insert into employee_leave(date, type, uid, days, time_slot) values('" . $E_date . "','".$E_ltype."','".$E_id."','".$E_days."','".$E_timeslot."')";
                                      SUD($queryLeave);

                                      $queryNotification = "insert into notification(n_date, n_time, n_status, n_user, erid) values('" . date("Y-m-d") . "','".date("H:i:s")."','0','".$E_id."','".$_REQUEST["eid"]."')";
                                      SUD($queryNotification);

                                  }

                                  echo "1"; 
                              } 
                              else 
                              {
                                  echo "0";
                              }
                      }
                      else
                      {
                          $querys = "Update emp_leave_request set leave_type = 'Nopay Leave',days='1',time_slot='',app_date='" . date("Y-m-d") . "',app_time='" . date("H:i:s") . "',app_by='".$_SESSION["uid"]."',app_status='1',reqstatus='1' where elrid='" . $_REQUEST["eid"] . "'";
                             
                              $ret = SUD($querys);

                              if ($ret == 1) 
                              {
                                  $query = "select * from emp_leave_request where elrid='" . $_REQUEST["eid"] . "'";
                                  $res = Search($query);
                                  if ($result = mysqli_fetch_assoc($res)) {
                                      
                                      $E_date = $result["fromdate"];
                                      
                                      $queryLeave = "insert into employee_leave(date, type, uid, days, time_slot) values('" . $E_date . "','Nopay Leave','".$_REQUEST["employeeID"]."','1','')";
                                      SUD($queryLeave);

                                      $queryNotification = "insert into notification(n_date, n_time, n_status, n_user, erid) values('" . date("Y-m-d") . "','".date("H:i:s")."','0','".$_REQUEST["employeeID"]."','".$_REQUEST["eid"]."')";
                                      SUD($queryNotification);

                                  }

                                  echo "1"; 
                              } 
                              else 
                              {
                                  echo "0";
                              }
                      }
                  }
                  else
                  {
                      if ($_REQUEST["LeaveType"] == "Halfday Leave") 
                      {
                          if ($Half_Available == "0") 
                          {
                              echo "half";
                          }
                          else
                          {
                              $querys = "Update emp_leave_request set app_date='" . date("Y-m-d") . "',app_time='" . date("H:i:s") . "',app_by='".$_SESSION["uid"]."',app_status='1',reqstatus='1' where elrid='" . $_REQUEST["eid"] . "'";
                             
                              $ret = SUD($querys);

                              if ($ret == 1) 
                              {
                                  $query = "select * from emp_leave_request where elrid='" . $_REQUEST["eid"] . "'";
                                  $res = Search($query);
                                  if ($result = mysqli_fetch_assoc($res)) {
                                      
                                      $E_id = $result["empid"];
                                      $E_ltype = $result["leave_type"];
                                      $E_date = $result["fromdate"];
                                      $E_days = $result["days"];
                                      $E_timeslot = $result["time_slot"];

                                      $queryLeave = "insert into employee_leave(date, type, uid, days, time_slot) values('" . $E_date . "','".$E_ltype."','".$E_id."','".$E_days."','".$E_timeslot."')";
                                      SUD($queryLeave);

                                      $resCHKPreviouseHalf = Search("select pfylid,total_leave from total_leave_data where uid='".$E_id."'");
                                      if ($resultCHKPreviouseHalf = mysqli_fetch_assoc($resCHKPreviouseHalf)) 
                                      {
                                          $Total_Value = $resultCHKPreviouseHalf["total_leave"] - $E_days;

                                          $querysUpdate = "Update total_leave_data set total_leave='".$Total_Value."' where pfylid='" . $resultCHKPreviouseHalf["pfylid"] . "'";
                                          $ret = SUD($querysUpdate);
                                      }

                                      $queryNotification = "insert into notification(n_date, n_time, n_status, n_user, erid) values('" . date("Y-m-d") . "','".date("H:i:s")."','0','".$E_id."','".$_REQUEST["eid"]."')";
                                      SUD($queryNotification);

                                  }

                                  echo "1"; 
                              } 
                              else 
                              {
                                  echo "0";
                              }
                          }
                      }
                      else if ($_REQUEST["LeaveType"] == "Short Leave") 
                      {
                          if ($Short_Available == "0") 
                          {
                              echo "short";
                          }
                          else
                          {
                              $querys = "Update emp_leave_request set app_date='" . date("Y-m-d") . "',app_time='" . date("H:i:s") . "',app_by='".$_SESSION["uid"]."',app_status='1',reqstatus='1' where elrid='" . $_REQUEST["eid"] . "'";
                             
                              $ret = SUD($querys);

                              if ($ret == 1) 
                              {
                                  $query = "select * from emp_leave_request where elrid='" . $_REQUEST["eid"] . "'";
                                  $res = Search($query);
                                  if ($result = mysqli_fetch_assoc($res)) {
                                      
                                      $E_id = $result["empid"];
                                      $E_ltype = $result["leave_type"];
                                      $E_date = $result["fromdate"];
                                      $E_days = $result["days"];
                                      $E_timeslot = $result["time_slot"];

                                      $queryLeave = "insert into employee_leave(date, type, uid, days, time_slot) values('" . $E_date . "','".$E_ltype."','".$E_id."','".$E_days."','".$E_timeslot."')";
                                      SUD($queryLeave);

                                      $queryNotification = "insert into notification(n_date, n_time, n_status, n_user, erid) values('" . date("Y-m-d") . "','".date("H:i:s")."','0','".$E_id."','".$_REQUEST["eid"]."')";
                                      SUD($queryNotification);

                                  }

                                  echo "1"; 
                              } 
                              else 
                              {
                                  echo "0";
                              }
                          }
                      }
                      else if ($_REQUEST["LeaveType"] == "Liue Leave") 
                      {
                          $querys = "Update emp_leave_request set app_date='" . date("Y-m-d") . "',app_time='" . date("H:i:s") . "',app_by='".$_SESSION["uid"]."',app_status='1',reqstatus='1' where elrid='" . $_REQUEST["eid"] . "'";
                             
                              $ret = SUD($querys);

                              if ($ret == 1) 
                              {
                                  $query = "select * from emp_leave_request where elrid='" . $_REQUEST["eid"] . "'";
                                  $res = Search($query);
                                  if ($result = mysqli_fetch_assoc($res)) {
                                      
                                      $E_id = $result["empid"];
                                      $E_ltype = $result["leave_type"];
                                      $E_date = $result["fromdate"];
                                      $E_days = $result["days"];
                                      $E_timeslot = $result["time_slot"];

                                      $queryLeave = "insert into employee_leave(date, type, uid, days, time_slot) values('" . $E_date . "','".$E_ltype."','".$E_id."','".$E_days."','".$E_timeslot."')";
                                      SUD($queryLeave);

                                      $queryNotification = "insert into notification(n_date, n_time, n_status, n_user, erid) values('" . date("Y-m-d") . "','".date("H:i:s")."','0','".$E_id."','".$_REQUEST["eid"]."')";
                                      SUD($queryNotification);

                                  }

                                  echo "1"; 
                              } 
                              else 
                              {
                                  echo "0";
                              }
                      } 
                  }
              }
              else if ($years == "0" && $months >= "1" && $months <= "6") 
              {

                    $one_month_half = 0.5; // 1 half day
                    $one_month_short = 0.5; // 2 short leaves

                    $GetHalf = 0;
                    $GetShort = 0;
                    $Half_Available = 0;
                    $Short_Available = 0;
                    $Total_leave = 0;

                    $queryHalf = "select sum(days) as halfleave from employee_leave where uid = '" . $_REQUEST["employeeID"] . "' AND YEAR(date) = '".date('Y')."' AND MONTH(date) = '".date('m')."' AND (type like 'Halfday Leave' or type like 'Leave')";
                    $resHalf = Search($queryHalf);
                    
                    if ($resultHalf = mysqli_fetch_assoc($resHalf)) 
                    {
                        if ($resultHalf["halfleave"] == "") 
                        {
                            $resPreviouseHalf = Search("select total_leave as previousehalf from total_leave_data where uid = '" . $_REQUEST["employeeID"] . "'");
                    
                            if ($resultPreviouseHalf = mysqli_fetch_assoc($resPreviouseHalf)) 
                            {
                                $TOTAL = $resultPreviouseHalf["previousehalf"];

                                if ($resultPreviouseHalf["previousehalf"] == "") 
                                {
                                    
                                }
                                else
                                {
                                    $GetHalf = 0;
                                    $Half_Available = $resultPreviouseHalf["previousehalf"];
                                }
                                           
                            }

                        }
                        else
                        {
                            $resPreviouseHalf = Search("select total_leave as previousehalf from total_leave_data where uid = '" . $_REQUEST["employeeID"] . "'");
                    
                            if ($resultPreviouseHalf = mysqli_fetch_assoc($resPreviouseHalf)) 
                            {
                                $TOTAL = $resultPreviouseHalf["previousehalf"];

                                if ($resultPreviouseHalf["previousehalf"] == "") 
                                {
                                
                                }
                                else
                                {
                                    $GetHalf = $resultHalf["halfleave"];
                                    $Half_Available = $resultPreviouseHalf["previousehalf"];
                                }
                                           
                            }
                            
                        }
                                   
                    }
                    

                    $queryShort = "select sum(days) as shortleave from employee_leave where uid = '" . $_REQUEST["employeeID"] . "' AND YEAR(date) = '".date('Y')."' AND MONTH(date) = '".date('m')."' AND type like 'Short Leave'";
                    $resShort = Search($queryShort);
                    
                    if ($resultShort = mysqli_fetch_assoc($resShort)) 
                    {
                        if ($resultShort["shortleave"] == "") 
                        {
                            $GetShort = 0;
                            $Short_Available = $one_month_short - 0;
                        }
                        else
                        {
                            $GetShort = $resultShort["shortleave"];
                            $Short_Available = $one_month_short - $GetShort;
                        }
                        
                               
                    }

                    $queryNopay = "select sum(days) as Nopayleave from employee_leave where uid = '" . $_REQUEST["employeeID"] . "' AND YEAR(date) = '".date('Y')."' AND MONTH(date) = '".date('m')."' AND type like 'Nopay Leave'";
                    $resNopay = Search($queryNopay);
                    
                    if ($resultNopay = mysqli_fetch_assoc($resNopay)) 
                    {
                        if ($resultNopay["Nopayleave"] == "") 
                        {
                            $NOPAYDATA = 0;
                        }
                        else
                        {
                            $NOPAYDATA = $resultNopay["Nopayleave"];
                        }
                        
                               
                    }


                    $queryUserCheck = "select jobcode from user where uid = '" . $_REQUEST["employeeID"] . "' and (EmployeeType_etid like '5' or EmployeeType_etid like '2')";
                    $resUserCheck = Search($queryUserCheck);
                        
                    if ($resultUserCheck = mysqli_fetch_assoc($resUserCheck)) 
                    {
                        $CheckUser = "1";
                        $queryLeaveLeave = "select sum(days) as LeaveLeavetot from employee_leave where uid = '" . $_REQUEST["employeeID"] . "' AND YEAR(date) = '".date('Y')."' AND MONTH(date) = '".date('m')."' AND type like 'Liue Leave'";
                          $resLeaveLeave = Search($queryLeaveLeave);
                          
                          if ($resultLeaveLeave = mysqli_fetch_assoc($resLeaveLeave)) 
                          {
                              if ($resultLeaveLeave["LeaveLeavetot"] == "") 
                              {
                                  $LEAVE_LEAVE = 0;
                              }
                              else
                              {
                                  $LEAVE_LEAVE = $resultLeaveLeave["LeaveLeavetot"];
                              }
                              
                                     
                          }
                    }
                    else
                    {
                        $CheckUser = "0";
                        $LEAVE_LEAVE = "0";
                    }

                   $Total_leave = $TOTAL + $one_month_short;
                   $NOPAY = $NOPAYDATA - $LEAVE_LEAVE;

                   if ($NOPAY <= 0) {
                       $NOPAY = 0;
                   }

                   if ($Half_Available <= 0) {
                       $Half_Available = 0;
                   }




                  if ($Half_Available == "0" && $Short_Available == "0")
                  {
                      if ($_REQUEST["LeaveType"] == "Liue Leave") 
                      {
                          $querys = "Update emp_leave_request set app_date='" . date("Y-m-d") . "',app_time='" . date("H:i:s") . "',app_by='".$_SESSION["uid"]."',app_status='1',reqstatus='1' where elrid='" . $_REQUEST["eid"] . "'";
                             
                              $ret = SUD($querys);

                              if ($ret == 1) 
                              {
                                  $query = "select * from emp_leave_request where elrid='" . $_REQUEST["eid"] . "'";
                                  $res = Search($query);
                                  if ($result = mysqli_fetch_assoc($res)) {
                                      
                                      $E_id = $result["empid"];
                                      $E_ltype = $result["leave_type"];
                                      $E_date = $result["fromdate"];
                                      $E_days = $result["days"];
                                      $E_timeslot = $result["time_slot"];

                                      $queryLeave = "insert into employee_leave(date, type, uid, days, time_slot) values('" . $E_date . "','".$E_ltype."','".$E_id."','".$E_days."','".$E_timeslot."')";
                                      SUD($queryLeave);

                                      $queryNotification = "insert into notification(n_date, n_time, n_status, n_user, erid) values('" . date("Y-m-d") . "','".date("H:i:s")."','0','".$E_id."','".$_REQUEST["eid"]."')";
                                      SUD($queryNotification);

                                  }

                                  echo "1"; 
                              } 
                              else 
                              {
                                  echo "0";
                              }
                      }
                      else
                      {
                          $querys = "Update emp_leave_request set leave_type = 'Nopay Leave',days='1',time_slot='',app_date='" . date("Y-m-d") . "',app_time='" . date("H:i:s") . "',app_by='".$_SESSION["uid"]."',app_status='1',reqstatus='1' where elrid='" . $_REQUEST["eid"] . "'";
                             
                              $ret = SUD($querys);

                              if ($ret == 1) 
                              {
                                  $query = "select * from emp_leave_request where elrid='" . $_REQUEST["eid"] . "'";
                                  $res = Search($query);
                                  if ($result = mysqli_fetch_assoc($res)) {
                                      
                                      $E_date = $result["fromdate"];
                                      
                                      $queryLeave = "insert into employee_leave(date, type, uid, days, time_slot) values('" . $E_date . "','Nopay Leave','".$_REQUEST["employeeID"]."','1','')";
                                      SUD($queryLeave);

                                      $queryNotification = "insert into notification(n_date, n_time, n_status, n_user, erid) values('" . date("Y-m-d") . "','".date("H:i:s")."','0','".$_REQUEST["employeeID"]."','".$_REQUEST["eid"]."')";
                                      SUD($queryNotification);

                                  }

                                  echo "1"; 
                              } 
                              else 
                              {
                                  echo "0";
                              }
                      }
                  }
                  else
                  {
                      if ($_REQUEST["LeaveType"] == "Halfday Leave") 
                      {
                          if ($Half_Available == "0") 
                          {
                              echo "half";
                          }
                          else
                          {
                              $querys = "Update emp_leave_request set app_date='" . date("Y-m-d") . "',app_time='" . date("H:i:s") . "',app_by='".$_SESSION["uid"]."',app_status='1',reqstatus='1' where elrid='" . $_REQUEST["eid"] . "'";
                             
                              $ret = SUD($querys);

                              if ($ret == 1) 
                              {
                                  $query = "select * from emp_leave_request where elrid='" . $_REQUEST["eid"] . "'";
                                  $res = Search($query);
                                  if ($result = mysqli_fetch_assoc($res)) {
                                      
                                      $E_id = $result["empid"];
                                      $E_ltype = $result["leave_type"];
                                      $E_date = $result["fromdate"];
                                      $E_days = $result["days"];
                                      $E_timeslot = $result["time_slot"];

                                      $queryLeave = "insert into employee_leave(date, type, uid, days, time_slot) values('" . $E_date . "','".$E_ltype."','".$E_id."','".$E_days."','".$E_timeslot."')";
                                      SUD($queryLeave);

                                      $resCHKPreviouseHalf = Search("select pfylid,total_leave from total_leave_data where uid='".$E_id."'");
                                      if ($resultCHKPreviouseHalf = mysqli_fetch_assoc($resCHKPreviouseHalf)) 
                                      {
                                          $Total_Value = $resultCHKPreviouseHalf["total_leave"] - $E_days;

                                          $querysUpdate = "Update total_leave_data set total_leave='".$Total_Value."' where pfylid='" . $resultCHKPreviouseHalf["pfylid"] . "'";
                                          $ret = SUD($querysUpdate);
                                      }

                                      $queryNotification = "insert into notification(n_date, n_time, n_status, n_user, erid) values('" . date("Y-m-d") . "','".date("H:i:s")."','0','".$E_id."','".$_REQUEST["eid"]."')";
                                      SUD($queryNotification);

                                  }

                                  echo "1"; 
                              } 
                              else 
                              {
                                  echo "0";
                              }
                          }
                      }
                      else if ($_REQUEST["LeaveType"] == "Short Leave") 
                      {
                          if ($Short_Available == "0") 
                          {
                              echo "short";
                          }
                          else
                          {
                              $querys = "Update emp_leave_request set app_date='" . date("Y-m-d") . "',app_time='" . date("H:i:s") . "',app_by='".$_SESSION["uid"]."',app_status='1',reqstatus='1' where elrid='" . $_REQUEST["eid"] . "'";
                             
                              $ret = SUD($querys);

                              if ($ret == 1) 
                              {
                                  $query = "select * from emp_leave_request where elrid='" . $_REQUEST["eid"] . "'";
                                  $res = Search($query);
                                  if ($result = mysqli_fetch_assoc($res)) {
                                      
                                      $E_id = $result["empid"];
                                      $E_ltype = $result["leave_type"];
                                      $E_date = $result["fromdate"];
                                      $E_days = $result["days"];
                                      $E_timeslot = $result["time_slot"];

                                      $queryLeave = "insert into employee_leave(date, type, uid, days, time_slot) values('" . $E_date . "','".$E_ltype."','".$E_id."','".$E_days."','".$E_timeslot."')";
                                      SUD($queryLeave);

                                      $queryNotification = "insert into notification(n_date, n_time, n_status, n_user, erid) values('" . date("Y-m-d") . "','".date("H:i:s")."','0','".$E_id."','".$_REQUEST["eid"]."')";
                                      SUD($queryNotification);

                                  }

                                  echo "1"; 
                              } 
                              else 
                              {
                                  echo "0";
                              }
                          }
                      }
                      else if ($_REQUEST["LeaveType"] == "Liue Leave") 
                      {
                          $querys = "Update emp_leave_request set app_date='" . date("Y-m-d") . "',app_time='" . date("H:i:s") . "',app_by='".$_SESSION["uid"]."',app_status='1',reqstatus='1' where elrid='" . $_REQUEST["eid"] . "'";
                             
                              $ret = SUD($querys);

                              if ($ret == 1) 
                              {
                                  $query = "select * from emp_leave_request where elrid='" . $_REQUEST["eid"] . "'";
                                  $res = Search($query);
                                  if ($result = mysqli_fetch_assoc($res)) {
                                      
                                      $E_id = $result["empid"];
                                      $E_ltype = $result["leave_type"];
                                      $E_date = $result["fromdate"];
                                      $E_days = $result["days"];
                                      $E_timeslot = $result["time_slot"];

                                      $queryLeave = "insert into employee_leave(date, type, uid, days, time_slot) values('" . $E_date . "','".$E_ltype."','".$E_id."','".$E_days."','".$E_timeslot."')";
                                      SUD($queryLeave);

                                      $queryNotification = "insert into notification(n_date, n_time, n_status, n_user, erid) values('" . date("Y-m-d") . "','".date("H:i:s")."','0','".$E_id."','".$_REQUEST["eid"]."')";
                                      SUD($queryNotification);

                                  }

                                  echo "1"; 
                              } 
                              else 
                              {
                                  echo "0";
                              }
                      } 
                  }
              }
              else
              {
                   if (date('m', strtotime($joinDate)) == "01" || date('m', strtotime($joinDate)) == "02" || date('m', strtotime($joinDate)) == "03") 
                   {
                       $casual_leaves = 7;
                       $annual_leaves = 14;
                   }
                   else if (date('m', strtotime($joinDate)) == "04" || date('m', strtotime($joinDate)) == "05" || date('m', strtotime($joinDate)) == "06") 
                   {
                       $casual_leaves = 7;
                       $annual_leaves = 10;
                   }
                   else if (date('m', strtotime($joinDate)) == "07" || date('m', strtotime($joinDate)) == "08" || date('m', strtotime($joinDate)) == "09") 
                   {
                       $casual_leaves = 7;
                       $annual_leaves = 7;
                   }
                   else if (date('m', strtotime($joinDate)) == "10" || date('m', strtotime($joinDate)) == "11" || date('m', strtotime($joinDate)) == "12")
                   {
                       $casual_leaves = 7;
                       $annual_leaves = 4;
                   }
                   
                   $GetCasual = 0;
                   $GetAnnual = 0;
                   $Casual_Available = 0;
                   $Annual_Available = 0;
                   $Total_leave = 0;
                   $query = "select sum(days) as totalLeaves from employee_leave where uid = '" . $_REQUEST["employeeID"] . "' AND YEAR(date) = '".date('Y')."' and type != 'Short Leave' and type != 'Company Leave' and type != 'Liue Leave' and type != 'Nopay Leave' ";
                   $res = Search($query);

                   if ($result = mysqli_fetch_assoc($res)) {

                       if ($result["totalLeaves"] == "") 
                       {
                           $Casual_Available = $casual_leaves;
                           $Annual_Available = $annual_leaves;

                       }
                       else
                       {
                           if ($result["totalLeaves"] >= $casual_leaves) 
                           {
                               $Difference = $result["totalLeaves"] - $casual_leaves;

                               $Annual_Available = $annual_leaves - $Difference;
                               $GetAnnual = $annual_leaves - $Annual_Available;

                               if ($NewAnnual <= 0) {
                                   $NewAnnual = 0;
                               }

                               $Casual_Available = 0;
                               $GetCasual = $casual_leaves;
                           }
                           else
                           {
                               $Casual_Available = $casual_leaves - $result["totalLeaves"];
                               $GetCasual = $casual_leaves - $Casual_Available;

                               if ($GetCasual <= 0) {
                                   $GetCasual = 0;
                               }

                               $Annual_Available = $annual_leaves;
                               $GetAnnual = 0;
                           }
                       }

                       

                   }

                   $queryNopay = "select sum(days) as Nopayleave from employee_leave where uid = '" . $_REQUEST["employeeID"] . "' AND YEAR(date) = '".date('Y')."' AND type like 'Nopay Leave'";
                        $resNopay = Search($queryNopay);
                        
                        if ($resultNopay = mysqli_fetch_assoc($resNopay)) 
                        {
                            if ($resultNopay["Nopayleave"] == "") 
                            {
                                $NOPAYDATA = 0;
                            }
                            else
                            {
                                $NOPAYDATA = $resultNopay["Nopayleave"];
                            }
                            
                                   
                        }

                  $queryUserCheck = "select jobcode from user where uid = '" . $_REQUEST["employeeID"] . "' and (EmployeeType_etid like '5' or EmployeeType_etid like '2')";
                  $resUserCheck = Search($queryUserCheck);
                      
                  if ($resultUserCheck = mysqli_fetch_assoc($resUserCheck)) 
                  {
                      $CheckUser = "1";
                      $queryLeaveLeave = "select sum(days) as LeaveLeavetot from employee_leave where uid = '" . $_REQUEST["employeeID"] . "' AND YEAR(date) = '".date('Y')."' AND type like 'Liue Leave'";
                        $resLeaveLeave = Search($queryLeaveLeave);
                        
                        if ($resultLeaveLeave = mysqli_fetch_assoc($resLeaveLeave)) 
                        {
                            if ($resultLeaveLeave["LeaveLeavetot"] == "") 
                            {
                                $LEAVE_LEAVE = 0;
                            }
                            else
                            {
                                $LEAVE_LEAVE = $resultLeaveLeave["LeaveLeavetot"];
                            }
                            
                                   
                        }
                  }
                  else
                  {
                      $CheckUser = "0";
                      $LEAVE_LEAVE = "0";
                  }    

                            

                   $Total_leave = $annual_leaves + $casual_leaves;
                   $NOPAY = $NOPAYDATA - $LEAVE_LEAVE;

                     if ($NOPAY <= 0) {
                         $NOPAY = 0;
                     }

                  if ($Casual_Available == "0" && $Annual_Available == "0")
                  {
                      if ($_REQUEST["LeaveType"] == "Liue Leave") 
                      {
                          $querys = "Update emp_leave_request set app_date='" . date("Y-m-d") . "',app_time='" . date("H:i:s") . "',app_by='".$_SESSION["uid"]."',app_status='1',reqstatus='1' where elrid='" . $_REQUEST["eid"] . "'";
                             
                              $ret = SUD($querys);

                              if ($ret == 1) 
                              {
                                  $query = "select * from emp_leave_request where elrid='" . $_REQUEST["eid"] . "'";
                                  $res = Search($query);
                                  if ($result = mysqli_fetch_assoc($res)) {
                                      
                                      $E_id = $result["empid"];
                                      $E_ltype = $result["leave_type"];
                                      $E_date = $result["fromdate"];
                                      $E_days = $result["days"];
                                      $E_timeslot = $result["time_slot"];

                                      $queryLeave = "insert into employee_leave(date, type, uid, days, time_slot) values('" . $E_date . "','".$E_ltype."','".$E_id."','".$E_days."','".$E_timeslot."')";
                                      SUD($queryLeave);

                                      $queryNotification = "insert into notification(n_date, n_time, n_status, n_user, erid) values('" . date("Y-m-d") . "','".date("H:i:s")."','0','".$E_id."','".$_REQUEST["eid"]."')";
                                      SUD($queryNotification);

                                  }

                                  echo "1"; 
                              } 
                              else 
                              {
                                  echo "0";
                              }
                      }
                      else
                      {
                          $querys = "Update emp_leave_request set leave_type = 'Nopay Leave',days='1',time_slot='',app_date='" . date("Y-m-d") . "',app_time='" . date("H:i:s") . "',app_by='".$_SESSION["uid"]."',app_status='1',reqstatus='1' where elrid='" . $_REQUEST["eid"] . "'";
                             
                              $ret = SUD($querys);

                              if ($ret == 1) 
                              {
                                  $query = "select * from emp_leave_request where elrid='" . $_REQUEST["eid"] . "'";
                                  $res = Search($query);
                                  if ($result = mysqli_fetch_assoc($res)) {
                                      
                                      $E_date = $result["fromdate"];
                                      
                                      $queryLeave = "insert into employee_leave(date, type, uid, days, time_slot) values('" . $E_date . "','Nopay Leave','".$_REQUEST["employeeID"]."','1','')";
                                      SUD($queryLeave);

                                      $queryNotification = "insert into notification(n_date, n_time, n_status, n_user, erid) values('" . date("Y-m-d") . "','".date("H:i:s")."','0','".$_REQUEST["employeeID"]."','".$_REQUEST["eid"]."')";
                                      SUD($queryNotification);

                                  }

                                  echo "1"; 
                              } 
                              else 
                              {
                                  echo "0";
                              }
                      }
                  }
                  else
                  {
                      if ($_REQUEST["LeaveType"] == "Liue Leave") 
                      {
                          $querys = "Update emp_leave_request set app_date='" . date("Y-m-d") . "',app_time='" . date("H:i:s") . "',app_by='".$_SESSION["uid"]."',app_status='1',reqstatus='1' where elrid='" . $_REQUEST["eid"] . "'";
                             
                              $ret = SUD($querys);

                              if ($ret == 1) 
                              {
                                  $query = "select * from emp_leave_request where elrid='" . $_REQUEST["eid"] . "'";
                                  $res = Search($query);
                                  if ($result = mysqli_fetch_assoc($res)) {
                                      
                                      $E_id = $result["empid"];
                                      $E_ltype = $result["leave_type"];
                                      $E_date = $result["fromdate"];
                                      $E_days = $result["days"];
                                      $E_timeslot = $result["time_slot"];

                                      $queryLeave = "insert into employee_leave(date, type, uid, days, time_slot) values('" . $E_date . "','".$E_ltype."','".$E_id."','".$E_days."','".$E_timeslot."')";
                                      SUD($queryLeave);

                                      $queryNotification = "insert into notification(n_date, n_time, n_status, n_user, erid) values('" . date("Y-m-d") . "','".date("H:i:s")."','0','".$E_id."','".$_REQUEST["eid"]."')";
                                      SUD($queryNotification);

                                  }

                                  echo "1"; 
                              } 
                              else 
                              {
                                  echo "0";
                              }
                      }
                      else
                      {

                          $set_value = $GetAnnual + $_REQUEST["DaysValue"];

                          if ($set_value > $annual_leaves) 
                          {
                              $querys = "Update emp_leave_request set leave_type = 'Nopay Leave',days='1',time_slot='',app_date='" . date("Y-m-d") . "',app_time='" . date("H:i:s") . "',app_by='".$_SESSION["uid"]."',app_status='1',reqstatus='1' where elrid='" . $_REQUEST["eid"] . "'";
                             
                              $ret = SUD($querys);

                              if ($ret == 1) 
                              {
                                  $query = "select * from emp_leave_request where elrid='" . $_REQUEST["eid"] . "'";
                                  $res = Search($query);
                                  if ($result = mysqli_fetch_assoc($res)) {
                                      
                                      $E_date = $result["fromdate"];
                                      
                                      $queryLeave = "insert into employee_leave(date, type, uid, days, time_slot) values('" . $E_date . "','Nopay Leave','".$_REQUEST["employeeID"]."','1','')";
                                      SUD($queryLeave);

                                      $queryNotification = "insert into notification(n_date, n_time, n_status, n_user, erid) values('" . date("Y-m-d") . "','".date("H:i:s")."','0','".$_REQUEST["employeeID"]."','".$_REQUEST["eid"]."')";
                                      SUD($queryNotification);

                                  }

                                  echo "1"; 
                              } 
                              else 
                              {
                                  echo "0";
                              }
                          }
                          else
                          {
                              $querys = "Update emp_leave_request set app_date='" . date("Y-m-d") . "',app_time='" . date("H:i:s") . "',app_by='".$_SESSION["uid"]."',app_status='1',reqstatus='1' where elrid='" . $_REQUEST["eid"] . "'";
                             
                              $ret = SUD($querys);

                              if ($ret == 1) 
                              {
                                  $query = "select * from emp_leave_request where elrid='" . $_REQUEST["eid"] . "'";
                                  $res = Search($query);
                                  if ($result = mysqli_fetch_assoc($res)) {
                                      
                                      $E_id = $result["empid"];
                                      $E_ltype = $result["leave_type"];
                                      $E_date = $result["fromdate"];
                                      $E_days = $result["days"];
                                      $E_timeslot = $result["time_slot"];

                                      $queryLeave = "insert into employee_leave(date, type, uid, days, time_slot) values('" . $E_date . "','".$E_ltype."','".$E_id."','".$E_days."','".$E_timeslot."')";
                                      SUD($queryLeave);

                                      $queryNotification = "insert into notification(n_date, n_time, n_status, n_user, erid) values('" . date("Y-m-d") . "','".date("H:i:s")."','0','".$E_id."','".$_REQUEST["eid"]."')";
                                      SUD($queryNotification);

                                  }

                                  echo "1"; 
                              } 
                              else 
                              {
                                  echo "0";
                              }
                          }
        
                      } 
                  }
     
              }
      
           }
           else
           {
                $casual_leaves = 7;
               $annual_leaves = 14;
               $GetCasual = 0;
               $GetAnnual = 0;
               $Casual_Available = 0;
               $Annual_Available = 0;
               $Total_leave = 0;
               $query = "select sum(days) as totalLeaves from employee_leave where uid = '" . $_REQUEST["employeeID"] . "' AND YEAR(date) = '".date('Y')."' and type != 'Short Leave' and type != 'Company Leave' and type != 'Liue Leave' and type != 'Nopay Leave' ";
               $res = Search($query);

               if ($result = mysqli_fetch_assoc($res)) {

                   if ($result["totalLeaves"] == "") 
                   {
                       $Casual_Available = $casual_leaves;
                       $Annual_Available = $annual_leaves;

                   }
                   else
                   {
                       if ($result["totalLeaves"] >= $casual_leaves) 
                       {
                           $Difference = $result["totalLeaves"] - $casual_leaves;

                           $Annual_Available = $annual_leaves - $Difference;
                           $GetAnnual = $annual_leaves - $Annual_Available;

                           if ($NewAnnual <= 0) {
                               $NewAnnual = 0;
                           }

                           $Casual_Available = 0;
                           $GetCasual = $casual_leaves;
                       }
                       else
                       {
                           $Casual_Available = $casual_leaves - $result["totalLeaves"];
                           $GetCasual = $casual_leaves - $Casual_Available;

                           if ($GetCasual <= 0) {
                               $GetCasual = 0;
                           }

                           $Annual_Available = $annual_leaves;
                           $GetAnnual = 0;
                       }
                   }

                   

               }

               $queryNopay = "select sum(days) as Nopayleave from employee_leave where uid = '" . $_REQUEST["employeeID"] . "' AND YEAR(date) = '".date('Y')."' AND type like 'Nopay Leave'";
                    $resNopay = Search($queryNopay);
                    
                    if ($resultNopay = mysqli_fetch_assoc($resNopay)) 
                    {
                        if ($resultNopay["Nopayleave"] == "") 
                        {
                            $NOPAYDATA = 0;
                        }
                        else
                        {
                            $NOPAYDATA = $resultNopay["Nopayleave"];
                        }
                        
                               
                    }

              $queryUserCheck = "select jobcode from user where uid = '" . $_REQUEST["employeeID"] . "' and (EmployeeType_etid like '5' or EmployeeType_etid like '2')";
              $resUserCheck = Search($queryUserCheck);
                  
              if ($resultUserCheck = mysqli_fetch_assoc($resUserCheck)) 
              {
                  $CheckUser = "1";
                  $queryLeaveLeave = "select sum(days) as LeaveLeavetot from employee_leave where uid = '" . $_REQUEST["employeeID"] . "' AND YEAR(date) = '".date('Y')."' AND type like 'Liue Leave'";
                    $resLeaveLeave = Search($queryLeaveLeave);
                    
                    if ($resultLeaveLeave = mysqli_fetch_assoc($resLeaveLeave)) 
                    {
                        if ($resultLeaveLeave["LeaveLeavetot"] == "") 
                        {
                            $LEAVE_LEAVE = 0;
                        }
                        else
                        {
                            $LEAVE_LEAVE = $resultLeaveLeave["LeaveLeavetot"];
                        }
                        
                               
                    }
              }
              else
              {
                  $CheckUser = "0";
                  $LEAVE_LEAVE = "0";
              }    

                        

               $Total_leave = $annual_leaves + $casual_leaves;
               $NOPAY = $NOPAYDATA - $LEAVE_LEAVE;

               if ($NOPAY <= 0) {
                   $NOPAY = 0;
               }

              if ($Casual_Available == "0" && $Annual_Available == "0")
                {
                    if ($_REQUEST["LeaveType"] == "Liue Leave") 
                    {
                        $querys = "Update emp_leave_request set app_date='" . date("Y-m-d") . "',app_time='" . date("H:i:s") . "',app_by='".$_SESSION["uid"]."',app_status='1',reqstatus='1' where elrid='" . $_REQUEST["eid"] . "'";
                           
                            $ret = SUD($querys);

                            if ($ret == 1) 
                            {
                                $query = "select * from emp_leave_request where elrid='" . $_REQUEST["eid"] . "'";
                                $res = Search($query);
                                if ($result = mysqli_fetch_assoc($res)) {
                                    
                                    $E_id = $result["empid"];
                                    $E_ltype = $result["leave_type"];
                                    $E_date = $result["fromdate"];
                                    $E_days = $result["days"];
                                    $E_timeslot = $result["time_slot"];

                                    $queryLeave = "insert into employee_leave(date, type, uid, days, time_slot) values('" . $E_date . "','".$E_ltype."','".$E_id."','".$E_days."','".$E_timeslot."')";
                                    SUD($queryLeave);

                                    $queryNotification = "insert into notification(n_date, n_time, n_status, n_user, erid) values('" . date("Y-m-d") . "','".date("H:i:s")."','0','".$E_id."','".$_REQUEST["eid"]."')";
                                    SUD($queryNotification);

                                }

                                echo "1"; 
                            } 
                            else 
                            {
                                echo "0";
                            }
                    }
                    else
                    {
                        $querys = "Update emp_leave_request set leave_type = 'Nopay Leave',days='1',time_slot='',app_date='" . date("Y-m-d") . "',app_time='" . date("H:i:s") . "',app_by='".$_SESSION["uid"]."',app_status='1',reqstatus='1' where elrid='" . $_REQUEST["eid"] . "'";
                           
                            $ret = SUD($querys);

                            if ($ret == 1) 
                            {
                                $query = "select * from emp_leave_request where elrid='" . $_REQUEST["eid"] . "'";
                                $res = Search($query);
                                if ($result = mysqli_fetch_assoc($res)) {
                                    
                                    $E_date = $result["fromdate"];
                                    
                                    $queryLeave = "insert into employee_leave(date, type, uid, days, time_slot) values('" . $E_date . "','Nopay Leave','".$_REQUEST["employeeID"]."','1','')";
                                    SUD($queryLeave);

                                    $queryNotification = "insert into notification(n_date, n_time, n_status, n_user, erid) values('" . date("Y-m-d") . "','".date("H:i:s")."','0','".$_REQUEST["employeeID"]."','".$_REQUEST["eid"]."')";
                                    SUD($queryNotification);

                                }

                                echo "1"; 
                            } 
                            else 
                            {
                                echo "0";
                            }
                    }
                }
                else
                {
                    if ($_REQUEST["LeaveType"] == "Liue Leave") 
                    {
                        $querys = "Update emp_leave_request set app_date='" . date("Y-m-d") . "',app_time='" . date("H:i:s") . "',app_by='".$_SESSION["uid"]."',app_status='1',reqstatus='1' where elrid='" . $_REQUEST["eid"] . "'";
                           
                            $ret = SUD($querys);

                            if ($ret == 1) 
                            {
                                $query = "select * from emp_leave_request where elrid='" . $_REQUEST["eid"] . "'";
                                $res = Search($query);
                                if ($result = mysqli_fetch_assoc($res)) {
                                    
                                    $E_id = $result["empid"];
                                    $E_ltype = $result["leave_type"];
                                    $E_date = $result["fromdate"];
                                    $E_days = $result["days"];
                                    $E_timeslot = $result["time_slot"];

                                    $queryLeave = "insert into employee_leave(date, type, uid, days, time_slot) values('" . $E_date . "','".$E_ltype."','".$E_id."','".$E_days."','".$E_timeslot."')";
                                    SUD($queryLeave);

                                    $queryNotification = "insert into notification(n_date, n_time, n_status, n_user, erid) values('" . date("Y-m-d") . "','".date("H:i:s")."','0','".$E_id."','".$_REQUEST["eid"]."')";
                                    SUD($queryNotification);

                                }

                                echo "1"; 
                            } 
                            else 
                            {
                                echo "0";
                            }
                    }
                    else
                    {

                        $set_value = $GetAnnual + $_REQUEST["DaysValue"];

                        if ($set_value > $annual_leaves) 
                        {
                            $querys = "Update emp_leave_request set leave_type = 'Nopay Leave',days='1',time_slot='',app_date='" . date("Y-m-d") . "',app_time='" . date("H:i:s") . "',app_by='".$_SESSION["uid"]."',app_status='1',reqstatus='1' where elrid='" . $_REQUEST["eid"] . "'";
                           
                            $ret = SUD($querys);

                            if ($ret == 1) 
                            {
                                $query = "select * from emp_leave_request where elrid='" . $_REQUEST["eid"] . "'";
                                $res = Search($query);
                                if ($result = mysqli_fetch_assoc($res)) {
                                    
                                    $E_date = $result["fromdate"];
                                    
                                    $queryLeave = "insert into employee_leave(date, type, uid, days, time_slot) values('" . $E_date . "','Nopay Leave','".$_REQUEST["employeeID"]."','1','')";
                                    SUD($queryLeave);

                                    $queryNotification = "insert into notification(n_date, n_time, n_status, n_user, erid) values('" . date("Y-m-d") . "','".date("H:i:s")."','0','".$_REQUEST["employeeID"]."','".$_REQUEST["eid"]."')";
                                    SUD($queryNotification);

                                }

                                echo "1"; 
                            } 
                            else 
                            {
                                echo "0";
                            }
                        }
                        else
                        {
                            $querys = "Update emp_leave_request set app_date='" . date("Y-m-d") . "',app_time='" . date("H:i:s") . "',app_by='".$_SESSION["uid"]."',app_status='1',reqstatus='1' where elrid='" . $_REQUEST["eid"] . "'";
                           
                            $ret = SUD($querys);

                            if ($ret == 1) 
                            {
                                $query = "select * from emp_leave_request where elrid='" . $_REQUEST["eid"] . "'";
                                $res = Search($query);
                                if ($result = mysqli_fetch_assoc($res)) {
                                    
                                    $E_id = $result["empid"];
                                    $E_ltype = $result["leave_type"];
                                    $E_date = $result["fromdate"];
                                    $E_days = $result["days"];
                                    $E_timeslot = $result["time_slot"];

                                    $queryLeave = "insert into employee_leave(date, type, uid, days, time_slot) values('" . $E_date . "','".$E_ltype."','".$E_id."','".$E_days."','".$E_timeslot."')";
                                    SUD($queryLeave);

                                    $queryNotification = "insert into notification(n_date, n_time, n_status, n_user, erid) values('" . date("Y-m-d") . "','".date("H:i:s")."','0','".$E_id."','".$_REQUEST["eid"]."')";
                                    SUD($queryNotification);

                                }

                                echo "1"; 
                            } 
                            else 
                            {
                                echo "0";
                            }
                        }
      
                    } 
                }
           }  

        }
    }
    else if ($_REQUEST["request"] == "declineleave") {

        $querys = "Update emp_leave_request set app_date='" . date("Y-m-d") . "',app_time='" . date("H:i:s") . "',app_by='".$_SESSION["uid"]."',app_status='2',reqstatus='2' where elrid='" . $_REQUEST["eid"] . "'";
               
        $ret = SUD($querys);

        if ($ret == 1) 
        {
            $query = "select * from emp_leave_request where elrid='" . $_REQUEST["eid"] . "'";
            $res = Search($query);
            if ($result = mysqli_fetch_assoc($res)) {
                
                $E_id = $result["empid"];

                $queryNotification = "insert into notification(n_date, n_time, n_status, n_user, erid) values('" . date("Y-m-d") . "','".date("H:i:s")."','2','".$E_id."','".$_REQUEST["eid"]."')";
                SUD($queryNotification);

            }

            echo "1"; 
        } 
        else 
        {
            echo "0";
        }   
    }
    else if ($_REQUEST["request"] == "confrmleave") {

        $querys = "Update emp_leave_request set conf_date='" . date("Y-m-d") . "',conf_time='" . date("H:i:s") . "',conf_by='".$_SESSION["uid"]."',conf_status='1' where elrid='" . $_REQUEST["eid"] . "'";
               
        $ret = SUD($querys);

        if ($ret == 1) 
        {
            echo "1"; 
        } 
        else 
        {
            echo "0";
        }
  
    }
    else if ($_REQUEST["request"] == "getDataField") {

        $query = "select * from emp_leave_request where elrid='" . $_REQUEST["id"] . "'";

        $res = Search($query);
        if ($result = mysqli_fetch_assoc($res)) {

            $E_id = $result["empid"];
            $E_ltype = $result["leave_type"];
            $E_date = $result["fromdate"];
            $E_days = $result["days"];
            $E_time_slot = $result["time_slot"];
            $E_confrm_state = $result["conf_status"];

            $querys = "select fname from user where uid='" . $E_id . "'";

            $ress = Search($querys);
            if ($results = mysqli_fetch_assoc($ress)) {

               $E_name = $results["fname"];
                
            }
        }
        
      echo $E_id."#".$E_name."#".$E_ltype."#".$E_date."#".$E_days."#".$E_time_slot."#".$E_confrm_state;
    }
    else if ($_REQUEST["request"] == "updateleavedetails") {

      if ($_REQUEST["DAYValueToOther"] != "") 
      {
          $querys = "Update emp_leave_request set leave_type ='" . $_REQUEST["leavetype"] . "',days='".$_REQUEST["DAYValueToOther"]."',time_slot='".$_REQUEST["timeSlot"]."' where elrid='" . $_REQUEST["eid"] . "'";
                 
            $ret = SUD($querys);  
       
        
            if ($ret == 1) 
            {
                echo "1";
            } 
            else 
            {
                echo "0";
            }
      }
      else
      {

          $querys = "Update emp_leave_request set leave_type ='" . $_REQUEST["leavetype"] . "',days='".$_REQUEST["DAYValue"]."',time_slot='".$_REQUEST["timeSlot"]."' where elrid='" . $_REQUEST["eid"] . "'";
                 
          $ret = SUD($querys);  
     
      
          if ($ret == 1) 
          {
              echo "1";
          } 
          else 
          {
              echo "0";
          }
      }

    }
    else if ($_REQUEST["request"] == "getleavecount") {

        // $YearDiff = 0;
       $queryYears = "select registerdDate from user where uid = '" . $_REQUEST["userid"] . "'";
       $resYears = Search($queryYears);

       if ($resultYears = mysqli_fetch_assoc($resYears)) 
       {
           $joinDate = $resultYears["registerdDate"];
       }

       $YearDiff = date('Y-m-d') - $joinDate;
       $date1 = $joinDate;
       $date2 = date('Y-m-d');

       $diff = abs(strtotime($date2) - strtotime($date1));

       $years = floor($diff / (365*60*60*24));
       $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
       $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

       if ($years >= "0" && $years < "2") //Working Year
       {
          if ($years == "0" && $months == "0") //First Month
          {
             
              $Total_leave = 0;
              $CheckMonth = "Empty";
              
              echo 'A'."#".$GetHalf."#".$Half_Available."#".$GetShort."#".$Short_Available."#".$Total_leave."#".$NOPAY."#".$LEAVE_LEAVE."#".$CheckUser."#".$CheckMonth;

          }
          else if ($years == "0" && $months >= "1" && $months <= "6") //After First Month Less Than 6
          {
              $one_month_half = 0.5; // 1 half day
              $one_month_short = 0.5; // 2 short leaves

              $GetHalf = 0;
              $GetShort = 0;
              $Half_Available = 0;
              $Short_Available = 0;
              $Total_leave = 0;

              $queryHalf = "select sum(days) as halfleave from employee_leave where uid = '" . $_REQUEST["userid"] . "' AND YEAR(date) = '".date('Y')."' AND MONTH(date) = '".date('m')."' AND (type like 'Halfday Leave' or type like 'Leave')";
              $resHalf = Search($queryHalf);
              
              if ($resultHalf = mysqli_fetch_assoc($resHalf)) 
              {
                  if ($resultHalf["halfleave"] == "") 
                  {
                      $resPreviouseHalf = Search("select total_leave as previousehalf from total_leave_data where uid = '" . $_REQUEST["userid"] . "'");
              
                      if ($resultPreviouseHalf = mysqli_fetch_assoc($resPreviouseHalf)) 
                      {
                          $TOTAL = $resultPreviouseHalf["previousehalf"];

                          if ($resultPreviouseHalf["previousehalf"] == "") 
                          {
                              
                          }
                          else
                          {
                              $GetHalf = 0;
                              $Half_Available = $resultPreviouseHalf["previousehalf"];
                          }
                                     
                      }

                  }
                  else
                  {
                      $resPreviouseHalf = Search("select total_leave as previousehalf from total_leave_data where uid = '" . $_REQUEST["userid"] . "'");
              
                      if ($resultPreviouseHalf = mysqli_fetch_assoc($resPreviouseHalf)) 
                      {
                          $TOTAL = $resultPreviouseHalf["previousehalf"];

                          if ($resultPreviouseHalf["previousehalf"] == "") 
                          {
                          
                          }
                          else
                          {
                              $GetHalf = $resultHalf["halfleave"];
                              $Half_Available = $resultPreviouseHalf["previousehalf"];
                          }
                                     
                      }
                      
                  }
                             
              }
              

              $queryShort = "select sum(days) as shortleave from employee_leave where uid = '" . $_REQUEST["userid"] . "' AND YEAR(date) = '".date('Y')."' AND MONTH(date) = '".date('m')."' AND type like 'Short Leave'";
              $resShort = Search($queryShort);
              
              if ($resultShort = mysqli_fetch_assoc($resShort)) 
              {
                  if ($resultShort["shortleave"] == "") 
                  {
                      $GetShort = 0;
                      $Short_Available = $one_month_short - 0;
                  }
                  else
                  {
                      $GetShort = $resultShort["shortleave"];
                      $Short_Available = $one_month_short - $GetShort;
                  }
                  
                         
              }

              $queryNopay = "select sum(days) as Nopayleave from employee_leave where uid = '" . $_REQUEST["userid"] . "' AND YEAR(date) = '".date('Y')."' AND MONTH(date) = '".date('m')."' AND type like 'Nopay Leave'";
              $resNopay = Search($queryNopay);
              
              if ($resultNopay = mysqli_fetch_assoc($resNopay)) 
              {
                  if ($resultNopay["Nopayleave"] == "") 
                  {
                      $NOPAYDATA = 0;
                  }
                  else
                  {
                      $NOPAYDATA = $resultNopay["Nopayleave"];
                  }
                  
                         
              }


              $queryUserCheck = "select jobcode from user where uid = '" . $_REQUEST["userid"] . "' and (EmployeeType_etid like '5' or EmployeeType_etid like '2')";
              $resUserCheck = Search($queryUserCheck);
                  
              if ($resultUserCheck = mysqli_fetch_assoc($resUserCheck)) 
              {
                  $CheckUser = "1";
                  $queryLeaveLeave = "select sum(days) as LeaveLeavetot from employee_leave where uid = '" . $_REQUEST["userid"] . "' AND YEAR(date) = '".date('Y')."' AND MONTH(date) = '".date('m')."' AND type like 'Liue Leave'";
                    $resLeaveLeave = Search($queryLeaveLeave);
                    
                    if ($resultLeaveLeave = mysqli_fetch_assoc($resLeaveLeave)) 
                    {
                        if ($resultLeaveLeave["LeaveLeavetot"] == "") 
                        {
                            $LEAVE_LEAVE = 0;
                        }
                        else
                        {
                            $LEAVE_LEAVE = $resultLeaveLeave["LeaveLeavetot"];
                        }
                        
                               
                    }
              }
              else
              {
                  $CheckUser = "0";
                  $LEAVE_LEAVE = "0";
              }

             // $Total_leave = $one_month_half + $one_month_short;
             $Total_leave = $TOTAL + $one_month_short;
             $NOPAY = $NOPAYDATA - $LEAVE_LEAVE;

             if ($NOPAY <= 0) {
                 $NOPAY = 0;
             }

             echo 'A'."#".$GetHalf."#".$Half_Available."#".$GetShort."#".$Short_Available."#".$Total_leave."#".$NOPAY."#".$LEAVE_LEAVE."#".$CheckUser."#".$CheckMonth;


          }
          else //After 6 Months
          {
               if (date('m', strtotime($joinDate)) == "01" || date('m', strtotime($joinDate)) == "02" || date('m', strtotime($joinDate)) == "03") 
               {
                   $casual_leaves = 7;
                   $annual_leaves = 14;
               }
               else if (date('m', strtotime($joinDate)) == "04" || date('m', strtotime($joinDate)) == "05" || date('m', strtotime($joinDate)) == "06") 
               {
                   $casual_leaves = 7;
                   $annual_leaves = 10;
               }
               else if (date('m', strtotime($joinDate)) == "07" || date('m', strtotime($joinDate)) == "08" || date('m', strtotime($joinDate)) == "09") 
               {
                   $casual_leaves = 7;
                   $annual_leaves = 7;
               }
               else if (date('m', strtotime($joinDate)) == "10" || date('m', strtotime($joinDate)) == "11" || date('m', strtotime($joinDate)) == "12")
               {
                   $casual_leaves = 7;
                   $annual_leaves = 4;
               }
               
               $GetCasual = 0;
               $GetAnnual = 0;
               $Casual_Available = 0;
               $Annual_Available = 0;
               $Total_leave = 0;

               $one_month_short = 0.5; // 2 short leaves
               $GetShort = 0;
               $Short_Available = 0;

               $query = "select sum(days) as totalLeaves from employee_leave where uid = '" . $_REQUEST["userid"] . "' AND YEAR(date) = '".date('Y')."' and type != 'Short Leave' and type != 'Company Leave' and type != 'Liue Leave' and type != 'Nopay Leave' ";
               $res = Search($query);

               if ($result = mysqli_fetch_assoc($res)) {

                   if ($result["totalLeaves"] == "") 
                   {
                       $Casual_Available = $casual_leaves;
                       $Annual_Available = $annual_leaves;

                   }
                   else
                   {
                       if ($result["totalLeaves"] >= $casual_leaves) 
                       {
                           $Difference = $result["totalLeaves"] - $casual_leaves;

                           $Annual_Available = $annual_leaves - $Difference;
                           $GetAnnual = $annual_leaves - $Annual_Available;

                           if ($NewAnnual <= 0) {
                               $NewAnnual = 0;
                           }

                           $Casual_Available = 0;
                           $GetCasual = $casual_leaves;
                       }
                       else
                       {
                           $Casual_Available = $casual_leaves - $result["totalLeaves"];
                           $GetCasual = $casual_leaves - $Casual_Available;

                           if ($GetCasual <= 0) {
                               $GetCasual = 0;
                           }

                           $Annual_Available = $annual_leaves;
                           $GetAnnual = 0;
                       }
                   }
               }

               $queryShort = "select sum(days) as shortleave from employee_leave where uid = '" . $_REQUEST["userid"] . "' AND YEAR(date) = '".date('Y')."' AND MONTH(date) = '".date('m')."' AND type like 'Short Leave'";
                $resShort = Search($queryShort);
                
                if ($resultShort = mysqli_fetch_assoc($resShort)) 
                {
                    if ($resultShort["shortleave"] == "") 
                    {
                        $GetShort = 0;
                        $Short_Available = $one_month_short - 0;
                    }
                    else
                    {
                        $GetShort = $resultShort["shortleave"];
                        $Short_Available = $one_month_short - $GetShort;
                    }         
                }

               $queryNopay = "select sum(days) as Nopayleave from employee_leave where uid = '" . $_REQUEST["userid"] . "' AND YEAR(date) = '".date('Y')."' AND type like 'Nopay Leave'";
                    $resNopay = Search($queryNopay);
                    
                if ($resultNopay = mysqli_fetch_assoc($resNopay)) 
                {
                    if ($resultNopay["Nopayleave"] == "") 
                    {
                        $NOPAYDATA = 0;
                    }
                    else
                    {
                        $NOPAYDATA = $resultNopay["Nopayleave"];
                    }          
                }

              $queryUserCheck = "select jobcode from user where uid = '" . $_REQUEST["userid"] . "' and (EmployeeType_etid like '5' or EmployeeType_etid like '2')";
              $resUserCheck = Search($queryUserCheck);
                  
              if ($resultUserCheck = mysqli_fetch_assoc($resUserCheck)) 
              {
                  $CheckUser = "1";
                  $queryLeaveLeave = "select sum(days) as LeaveLeavetot from employee_leave where uid = '" . $_REQUEST["userid"] . "' AND YEAR(date) = '".date('Y')."' AND type like 'Liue Leave'";
                    $resLeaveLeave = Search($queryLeaveLeave);
                    
                    if ($resultLeaveLeave = mysqli_fetch_assoc($resLeaveLeave)) 
                    {
                        if ($resultLeaveLeave["LeaveLeavetot"] == "") 
                        {
                            $LEAVE_LEAVE = 0;
                        }
                        else
                        {
                            $LEAVE_LEAVE = $resultLeaveLeave["LeaveLeavetot"];
                        }
                        
                               
                    }
              }
              else
              {
                  $CheckUser = "0";
                  $LEAVE_LEAVE = "0";
              }    

                        

               $Total_leave = $annual_leaves + $casual_leaves;
               $NOPAY = $NOPAYDATA - $LEAVE_LEAVE;

                 if ($NOPAY <= 0) {
                     $NOPAY = 0;
                 }

              echo 'B'."#".$GetCasual."#".$Casual_Available."#".$GetAnnual."#".$Annual_Available."#".$Total_leave."#".$NOPAY."#".$LEAVE_LEAVE."#".$CheckUser."#".$GetShort."#".$Short_Available; 

          }

  
       }
       else //Full Leave
       {
             $casual_leaves = 7;
             $annual_leaves = 14;
             $GetCasual = 0;
             $GetAnnual = 0;
             $Casual_Available = 0;
             $Annual_Available = 0;
             $Total_leave = 0;

             $one_month_short = 0.5; // 2 short leaves
             $GetShort = 0;
             $Short_Available = 0;

             $query = "select sum(days) as totalLeaves from employee_leave where uid = '" . $_REQUEST["userid"] . "' AND YEAR(date) = '".date('Y')."' and type != 'Short Leave' and type != 'Company Leave' and type != 'Liue Leave' and type != 'Nopay Leave' ";
             $res = Search($query);

             if ($result = mysqli_fetch_assoc($res)) {

                 if ($result["totalLeaves"] == "") 
                 {
                     $Casual_Available = $casual_leaves;
                     $Annual_Available = $annual_leaves;

                 }
                 else
                 {
                     if ($result["totalLeaves"] >= $casual_leaves) 
                     {
                         $Difference = $result["totalLeaves"] - $casual_leaves;

                         $Annual_Available = $annual_leaves - $Difference;
                         $GetAnnual = $annual_leaves - $Annual_Available;

                         if ($NewAnnual <= 0) {
                             $NewAnnual = 0;
                         }

                         $Casual_Available = 0;
                         $GetCasual = $casual_leaves;
                     }
                     else
                     {
                         $Casual_Available = $casual_leaves - $result["totalLeaves"];
                         $GetCasual = $casual_leaves - $Casual_Available;

                         if ($GetCasual <= 0) {
                             $GetCasual = 0;
                         }

                         $Annual_Available = $annual_leaves;
                         $GetAnnual = 0;
                     }
                 }
             }

             $queryShort = "select sum(days) as shortleave from employee_leave where uid = '" . $_REQUEST["userid"] . "' AND YEAR(date) = '".date('Y')."' AND MONTH(date) = '".date('m')."' AND type like 'Short Leave'";
                $resShort = Search($queryShort);
                
              if ($resultShort = mysqli_fetch_assoc($resShort)) 
              {
                  if ($resultShort["shortleave"] == "") 
                  {
                      $GetShort = 0;
                      $Short_Available = $one_month_short - 0;
                  }
                  else
                  {
                      $GetShort = $resultShort["shortleave"];
                      $Short_Available = $one_month_short - $GetShort;
                  }         
              }

            $queryNopay = "select sum(days) as Nopayleave from employee_leave where uid = '" . $_REQUEST["userid"] . "' AND YEAR(date) = '".date('Y')."' AND type like 'Nopay Leave'";
                  $resNopay = Search($queryNopay);
                  
            if ($resultNopay = mysqli_fetch_assoc($resNopay)) 
            {
                if ($resultNopay["Nopayleave"] == "") 
                {
                    $NOPAYDATA = 0;
                }
                else
                {
                    $NOPAYDATA = $resultNopay["Nopayleave"];
                }          
            }

            $queryUserCheck = "select jobcode from user where uid = '" . $_REQUEST["userid"] . "' and (EmployeeType_etid like '5' or EmployeeType_etid like '2')";
            $resUserCheck = Search($queryUserCheck);
                
            if ($resultUserCheck = mysqli_fetch_assoc($resUserCheck)) 
            {
                $CheckUser = "1";
                $queryLeaveLeave = "select sum(days) as LeaveLeavetot from employee_leave where uid = '" . $_REQUEST["userid"] . "' AND YEAR(date) = '".date('Y')."' AND type like 'Liue Leave'";
                  $resLeaveLeave = Search($queryLeaveLeave);
                  
                  if ($resultLeaveLeave = mysqli_fetch_assoc($resLeaveLeave)) 
                  {
                      if ($resultLeaveLeave["LeaveLeavetot"] == "") 
                      {
                          $LEAVE_LEAVE = 0;
                      }
                      else
                      {
                          $LEAVE_LEAVE = $resultLeaveLeave["LeaveLeavetot"];
                      }
                      
                             
                  }
            }
            else
            {
                $CheckUser = "0";
                $LEAVE_LEAVE = "0";
            }    

                      

             $Total_leave = $annual_leaves + $casual_leaves;
             $NOPAY = $NOPAYDATA - $LEAVE_LEAVE;

             if ($NOPAY <= 0) {
                 $NOPAY = 0;
             }

            echo 'C'."#".$GetCasual."#".$Casual_Available."#".$GetAnnual."#".$Annual_Available."#".$Total_leave."#".$NOPAY."#".$LEAVE_LEAVE."#".$CheckUser."#".$GetShort."#".$Short_Available;  
       }


    }

//other methods
}
?>