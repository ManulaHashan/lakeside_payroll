<?php
error_reporting(0);
session_start();
include '../DB/DB.php';
$DB = new Database();
date_default_timezone_set('Asia/Colombo');

//search 
if (isset($_REQUEST["request"])) {
    $out;
    if ($_REQUEST["request"] == "setSession") {

        $_SESSION["exportdata"] = $_POST["data"];

        echo $_SESSION["exportdata"];
    } else if ($_REQUEST["request"] == "getreq") {

        $out = "<table class='table table-striped table-bordered'><thead class='thead-dark' style='position : sticky; top : 0;  z-index: 0; background-color: #ffffff;'>
        <tr>
        <th>Change Request</th>
        <th></th>
        </tr></thead>";

        $res = Search("select a.raid,a.request_type,a.req_date,a.request_status,b.fname as subjectuser,c.fname as requesteduser,d.fname as approveduser from add_employee_request_approve a left join user b on a.subject_user = b.uid left join user c on a.requested_user = c.uid left join user d on a.app_user = d.uid where a.req_date between '" . $_REQUEST["fromDate"] . "' and '" . $_REQUEST["toDate"] . "' and request_status = '" . $_REQUEST["des"] . "'");
        while ($result = mysqli_fetch_assoc($res)) {
            if ($result["request_type"] == "1") {
                $reqtyp = "Update " . $result["subjectuser"] . "'s details";
            } else if ($result["request_type"] == "2") {
                $reqtyp = "Terminate " . $result["subjectuser"] . " in the system";
            } else if ($result["request_type"] == "3") {
                $reqtyp = "Add " . $result["subjectuser"] . "'s Login Details";
            } else if ($result["request_type"] == "4") {
                $reqtyp = "Add  " . $result["subjectuser"] . "'s Skills Details";
            } else if ($result["request_type"] == "5") {
                $reqtyp = "Manage " . $result["subjectuser"] . "'s Time Profile";
            }


            if ($result["request_status"] == "0") {

                $out .= "<tr>";
                $out .= "<td style='background-color: #DAA520; color: black;'><b>" . $result["requesteduser"] . "</b> wants to <b>" . $reqtyp . "</b>. This request has been requested on <b>" . $result["req_date"] . "</b> | Decision : <b>Pending</b></td>";
                $out .= "<td><input type='button' class='btn btn-success' style='margin-top: 10px; width: 150px;' id='" . $result["raid"] . "' onclick='loadDataForApprove(id)' class='btn btn-default submit' style='width: 100px' value='Approve'>&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' class='btn btn-danger' style='margin-top: 10px; width: 150px;' id='" . $result["raid"] . "' onclick='loadDataForDecline(id)' class='btn btn-default submit' style='width: 100px' value='Decline'></td>";
            } else if ($result["request_status"] == "1") {
                $out .= "<tr>";
                $out .= "<td style='background-color: #47b833; color: black;'><b>" . $result["requesteduser"] . "</b> wants to <b>" . $reqtyp . "</b>. This request has been requested on <b>" . $result["req_date"] . "</b> | Decision : <b>Approved</b></td>";
                $out .= "<td>Request approved by <b>" . $result["approveduser"] . "</b></td>";
            } else if ($result["request_status"] == "2") {
                $out .= "<tr>";
                $out .= "<td style='background-color: #ff0000; color: black;'><b>" . $result["requesteduser"] . "</b> wants to <b>" . $reqtyp . "</b>. This request has been requested on <b>" . $result["req_date"] . "</b> | Decision : <b>Declined</b></td>";
                $out .= "<td>Request declined by <b>" . $result["approveduser"] . "</b></td>";
            } else if ($result["request_status"] == "3") {
                $out .= "<tr>";
                $out .= "<td style='color: black;'><b>" . $result["requesteduser"] . "</b> wants to <b>" . $reqtyp . "</b>. This request has been requested on <b>" . $result["req_date"] . "</b> | Decision : <b>Task Completed</b></td>";
                $out .= "<td>Request approved by <b>" . $result["approveduser"] . "</b></td>";
            }

            $out .= "</tr>";
        }

        $out .= "</table>";

        echo $out;
    } else if ($_REQUEST["request"] == "getApproveData") {
        $querys = "update add_employee_request_approve set request_status='1',app_user='" . $_SESSION["uid"] . "' where raid='" . $_REQUEST["reqid"] . "'";

        $ret = SUD($querys);

        if ($ret == 1) {
            echo "1";
        } else {
            echo "0";
        }
    } else if ($_REQUEST["request"] == "getDeclineData") {

        $querys = "update add_employee_request_approve set request_status='2',app_user='" . $_SESSION["uid"] . "' where raid='" . $_REQUEST["reqid"] . "'";

        $ret = SUD($querys);

        if ($ret == 1) {
            echo "1";
        } else {
            echo "0";
        }
    }
}


// function sendSMS($leave_req_ID) 
// {
//     $username = "derana_lab";
//     $password = "DRn76Lab";
//     $src = "DeranaLab";
//     $delivery = "1";

//     $resLeave_emp_name = Search("select elr.fromdate,elr.leave_type,elr.app_status,u.uid,u.mname,u.tpno from emp_leave_request elr, user u where u.uid = elr.empid and elr.elrid = '".$leave_req_ID."'");
//     if ($resultLeave_emp_name = mysqli_fetch_assoc($resLeave_emp_name)) 
//     {
//         $EMPName = $resultLeave_emp_name["mname"];
//         $EMPTPNO = $resultLeave_emp_name["tpno"];
//         $EMPReqDate = $resultLeave_emp_name["fromdate"];
//         $EMPLType = $resultLeave_emp_name["leave_type"];
//         $EMPAppStatus = $resultLeave_emp_name["app_status"];
//         $EMPID = $resultLeave_emp_name["uid"];
//     }
//     else
//     {
//         $EMPName = "Derana HRIS";
//         $EMPTPNO = "";
//         $EMPReqDate = "";
//         $EMPLType = "";
//         $EMPAppStatus = "";
//         $EMPID = 0;
//     }


//     if ($EMPAppStatus == "1") 
//     {
//       $decision = "approved";
//     }
//     else if ($EMPAppStatus == "2") 
//     {
//       $decision = "rejected";
//     }

//     $msg = "Dear ".$EMPName.",\n\n";
//     $msg .= "The ".$EMPLType.", Leave Request applied by you for ".$EMPReqDate." has been ".$decision.".\n\n";
//     $msg .= "Thank you! (Derana HRIS)"; 

//     $url = "https://sms.textware.lk:5001/sms/send_sms.php?username=" . $username . "&password=" . $password . "&src=" . $src . "&dst=" . $EMPTPNO . "&msg=" . $msg . "&dr=" . $delivery;

//     $url = str_replace(" ", "+", $url);
//     $url = str_replace("\n", "%0A", $url);
//     $result = file_get_contents($url);

//     if ($result === false) 
//     {
//       $res_msg = "0";
//     } 
//     else 
//     {
//       $res_log = SUD("insert into smslog(tpno, uid, operation_msg, action) values('" . $EMPTPNO . "','" . $EMPID . "','" . $result . "','Employee-Leave-SMS')");
//       $res_msg = "1";
//     }

//     return $res_msg;  
// }    