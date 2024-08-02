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
    } else if ($_REQUEST["request"] == "getleave") {

        $out = "<table class='table table-striped table-bordered'><thead class='thead-dark' style='position : sticky; top : 0;  z-index: 0; background-color: #ffffff;'>
        <tr>
        <th>Leave Request</th>
        <th></th>
        </tr></thead>";

        $query = "select a.* from emp_leave_request a,user b where a.empid = b.uid and a.reqstatus='0' and (b.auth_person_id = '" . $_SESSION["uid"] . "' or b.sec_auth_person_id = '" . $_SESSION["uid"] . "')";


        $res = Search($query);
        while ($result = mysqli_fetch_assoc($res)) {

            $querys = "select fname from user where uid ='" . $result["empid"] . "'";

            $ress = Search($querys);
            if ($results = mysqli_fetch_assoc($ress)) {
                $uname = $results["fname"];
            }

            if ($result["reqstatus"] == "0") {

                $out .= "<tr>";
                $out .= "<td style='background-color: #DAA520; color: black;'><b>Employee : </b> " . $uname . " | <b>Leave Type : </b>" . $result["leave_type"] . " |  <b>Date of Leave :</b> " . $result["fromdate"] . " | <b>Number of Days :</b> " . $result["days"] . "</br><b>Reason : </b>" . $result["reason"] . " | <b>Date The Request Was Sent :</b> " . $result["request_date"] . " |  <b>Decision : Pending</b></td>";
            }

            $out .= "<td><input type='button' class='btn btn-success' style='margin-top: 10px; width: 150px;' id='" . $result["elrid"] . "' onclick='loadDataForApprove(id)' class='btn btn-default submit' style='width: 100px' value='Approve'>&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' class='btn btn-danger' style='margin-top: 10px; width: 150px;' id='" . $result["elrid"] . "' onclick='loadDataForDecline(id)' class='btn btn-default submit' style='width: 100px' value='Decline'></td>";

            $out .= "</tr>";
        }

        $out .= "</table>";

        echo $out;
    } else if ($_REQUEST["request"] == "getApproveData") {

        $querys = "Update emp_leave_request set app_date='" . date("Y-m-d") . "',app_time='" . date("H:i:s") . "',app_by='" . $_REQUEST["Auth"] . "',app_status='1',reqstatus='1' where elrid='" . $_REQUEST["eid"] . "'";

        $ret = SUD($querys);

        if ($ret == 1) {
            $query = "select * from emp_leave_request where elrid='" . $_REQUEST["eid"] . "'";
            $res = Search($query);
            if ($result = mysqli_fetch_assoc($res)) {

                $E_id = $result["empid"];
                $E_ltype = $result["leave_type"];
                $E_date = $result["fromdate"];
                $E_days = $result["days"];
                $E_timeslot = $result["time_slot"];
                $E_reason = $result["reason"];

                $queryLeave = "insert into employee_leave(date, type, uid, days, time_slot, reason) values('" . $E_date . "','" . $E_ltype . "','" . $E_id . "','" . $E_days . "','" . $E_timeslot . "','" . $E_reason . "')";
                SUD($queryLeave);

                if ($E_ltype == "Halfday Morning Leave" || $E_ltype == "Halfday Evening Leave") {
                    $resCHKPreviouseHalf = Search("select pfylid,total_leave from total_leave_data where uid='" . $E_id . "'");
                    if ($resultCHKPreviouseHalf = mysqli_fetch_assoc($resCHKPreviouseHalf)) {
                        $Total_Value = $resultCHKPreviouseHalf["total_leave"] - $E_days;

                        $querysUpdate = "Update total_leave_data set total_leave='" . $Total_Value . "' where pfylid='" . $resultCHKPreviouseHalf["pfylid"] . "'";
                        $ret = SUD($querysUpdate);
                    }
                }

                $queryNotification = "insert into notification(n_date, n_time, n_status, n_user, erid) values('" . date("Y-m-d") . "','" . date("H:i:s") . "','0','" . $E_id . "','" . $_REQUEST["eid"] . "')";
                SUD($queryNotification);
                sendSMS($_REQUEST["eid"]);
            }

            echo "1";
        } else {
            echo "0";
        }
    } else if ($_REQUEST["request"] == "getDeclineData") {

        $querys = "Update emp_leave_request set app_date='" . date("Y-m-d") . "',app_time='" . date("H:i:s") . "',app_by='" . $_REQUEST["Auth"] . "',app_status='2',reqstatus='2' where elrid='" . $_REQUEST["eid"] . "'";

        $ret = SUD($querys);

        if ($ret == 1) {
            $query = "select * from emp_leave_request where elrid='" . $_REQUEST["eid"] . "'";
            $res = Search($query);
            if ($result = mysqli_fetch_assoc($res)) {

                $E_id = $result["empid"];

                $queryNotification = "insert into notification(n_date, n_time, n_status, n_user, erid) values('" . date("Y-m-d") . "','" . date("H:i:s") . "','2','" . $E_id . "','" . $_REQUEST["eid"] . "')";
                SUD($queryNotification);
                sendSMS($_REQUEST["eid"]);
            }

            echo "1";
        } else {
            echo "0";
        }
    }
}


function sendSMS($leave_req_ID)
{
    $username = "derana_lab";
    $password = "DRn76Lab";
    $src = "DeranaLab";
    $delivery = "1";

    $resLeave_emp_name = Search("select elr.fromdate,elr.leave_type,elr.app_status,u.uid,u.mname,u.tpno from emp_leave_request elr, user u where u.uid = elr.empid and elr.elrid = '" . $leave_req_ID . "'");
    if ($resultLeave_emp_name = mysqli_fetch_assoc($resLeave_emp_name)) {
        $EMPName = $resultLeave_emp_name["mname"];
        $EMPTPNO = $resultLeave_emp_name["tpno"];
        $EMPReqDate = $resultLeave_emp_name["fromdate"];
        $EMPLType = $resultLeave_emp_name["leave_type"];
        $EMPAppStatus = $resultLeave_emp_name["app_status"];
        $EMPID = $resultLeave_emp_name["uid"];
    } else {
        $EMPName = "Derana HRIS";
        $EMPTPNO = "";
        $EMPReqDate = "";
        $EMPLType = "";
        $EMPAppStatus = "";
        $EMPID = 0;
    }


    if ($EMPAppStatus == "1") {
        $decision = "approved";
    } else if ($EMPAppStatus == "2") {
        $decision = "rejected";
    }

    $msg = "Dear " . $EMPName . ",\n\n";
    $msg .= "The " . $EMPLType . ", Leave Request applied by you for " . $EMPReqDate . " has been " . $decision . ".\n\n";
    $msg .= "Thank you! (Derana HRIS)";

    $url = "https://sms.textware.lk:5001/sms/send_sms.php?username=" . $username . "&password=" . $password . "&src=" . $src . "&dst=" . $EMPTPNO . "&msg=" . $msg . "&dr=" . $delivery;

    $url = str_replace(" ", "+", $url);
    $url = str_replace("\n", "%0A", $url);
    $result = file_get_contents($url);

    if ($result === false) {
        $res_msg = "0";
    } else {
        $res_log = SUD("insert into smslog(tpno, uid, operation_msg, action) values('" . $EMPTPNO . "','" . $EMPID . "','" . $result . "','Employee-Leave-SMS')");
        $res_msg = "1";
    }

    return $res_msg;
}