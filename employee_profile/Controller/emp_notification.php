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
    } else if ($_REQUEST["request"] == "getleave") {      // 👇️ Attendance Log Data Table

        $out = "<table class='table table-striped table-bordered nowrap'><thead style='position : sticky; top : 0;  z-index: 0;'>
        <tr>
        <th>Date</th>
        <th>Time</th>
        <th>Message</th>
        <th>Seen Status</th>
        </tr></thead><tbody>";

        $query;

        $query = "select * from notification where n_user='" . $_SESSION["uid"] . "' and n_date between '" . $_REQUEST["frmdate"] . "' and '" . $_REQUEST["tod"] . "' order by n_date DESC,n_time ";


        $res = Search($query);
        while ($result = mysqli_fetch_assoc($res)) {

            if ($result["n_status"] == "1") {
                $out .= "<tr style='background-color:#ffffff; cursor: pointer;'>";

                $out .= "<td><center>" . $result["n_date"] . "<center></td>
                         <td><center>" . $result["n_time"] . "<center></td>";

                $querys = "select app_status,app_date from emp_leave_request where elrid = '" . $result["erid"] . "'";

                $ress = Search($querys);
                if ($results = mysqli_fetch_assoc($ress)) {

                    if ($results["app_status"] == "1") {
                        $out .= "<td>Your leave request accepted in " . $results["app_date"] . "</td>";
                    }
                } else {
                    $out .= "<td></td>";
                }

                $out .= "<td><center><img src='../img/correct.png' width='30px'></center></td>";

                $out .= "</tr>";
            } else if ($result["n_status"] == "2") {
                $out .= "<tr style='background-color:#d6d6c2; cursor: pointer;' onclick='ChangeStatus(" . $result["nid"] . ")'>";

                $out .= "<td><center>" . $result["n_date"] . "<center></td>
                         <td><center>" . $result["n_time"] . "<center></td>";

                $querys = "select app_status,app_date from emp_leave_request where elrid = '" . $result["erid"] . "'";

                $ress = Search($querys);
                if ($results = mysqli_fetch_assoc($ress)) {

                    if ($results["app_status"] == "2") {
                        $out .= "<td>Your leave request declined in " . $results["app_date"] . "</td>";
                    }
                } else {
                    $out .= "<td></td>";
                }

                $out .= "<td><center><img src='../img/pending.png' width='30px'></center></td>";

                $out .= "</tr>";
            } else if ($result["n_status"] == "3") {
                $out .= "<tr style='background-color:#ffffff; cursor: pointer;'>";

                $out .= "<td><center>" . $result["n_date"] . "<center></td>
                         <td><center>" . $result["n_time"] . "<center></td>";

                $querys = "select app_status,app_date from emp_leave_request where elrid = '" . $result["erid"] . "'";

                $ress = Search($querys);
                if ($results = mysqli_fetch_assoc($ress)) {

                    if ($results["app_status"] == "2") {
                        $out .= "<td>Your leave request declined in " . $results["app_date"] . "</td>";
                    }
                } else {
                    $out .= "<td></td>";
                }

                $out .= "<td><center><img src='../img/reject.png' width='30px'></center></td>";

                $out .= "</tr>";
            } else {
                $out .= "<tr style='background-color:#d6d6c2; cursor: pointer;' onclick='ChangeStatus(" . $result["nid"] . ")'>";

                $out .= "<td><center>" . $result["n_date"] . "<center></td>
                         <td><center>" . $result["n_time"] . "<center></td>";

                $querys = "select app_status,app_date from emp_leave_request where elrid = '" . $result["erid"] . "'";

                $ress = Search($querys);
                if ($results = mysqli_fetch_assoc($ress)) {

                    if ($results["app_status"] == "1") {
                        $out .= "<td>Your leave request accepted in " . $results["app_date"] . "</td>";
                    }
                } else {
                    $out .= "<td></td>";
                }

                $out .= "<td><center><img src='../img/pending.png' width='30px'></center></td>";

                $out .= "</tr>";
            }
        }

        $out .= "</tbody></table>";

        echo $out;
    } else if ($_REQUEST["request"] == "leaverequest") {
        $query = "select empid from emp_leave_request where empid = '" . $_SESSION["uid"] . "' and fromdate= '" . $_REQUEST["from"] . "' and leave_type = '" . $_REQUEST["leavetype"] . "'";
        $res = Search($query);
        if ($result = mysqli_fetch_assoc($res)) {
            echo "1";
        } else {

            //insert request

            $insertQuery = "insert into emp_leave_request(empid, fromdate, days, leave_type, reqstatus, request_date, request_time, app_status)"
                . " values('" . $_SESSION["uid"] . "','" . $_REQUEST["from"] . "','" . $_REQUEST["day"] . "','" . $_REQUEST["leavetype"] . "','0','" . date("Y-m-d") . "','" . date("H:i:s") . "','0')";
            $return = SUD($insertQuery);

            if ($return == "1") {
                echo "2";
            } else {
                echo "3";
            }
        }
    } else if ($_REQUEST["request"] == "getnotification") {

        $out = "<table class='table table-striped'>";

        $query;

        $query = "select * from notification where n_user = '" . $_SESSION["uid"] . "' order by n_date DESC,n_time DESC limit 4";

        $res = Search($query);
        while ($result = mysqli_fetch_assoc($res)) {

            if ($result["n_status"] == "0") {

                $out .= "<tr id='" . $result["nid"] . "' style='background-color:#d6d6c2; cursor: pointer;' onclick='getrowid(id)'>";
                $out .= "<td><img src='../img/pending.png' width='30px'>&nbsp; Your request accepted in " . $result["n_date"] . "</td>";
                $out .= "</tr>";
            } else if ($result["n_status"] == "2") {
                $out .= "<tr id='" . $result["nid"] . "' style='background-color:#d6d6c2; cursor: pointer;' onclick='getrowid(id)'>";
                $out .= "<td><img src='../img/pending.png' width='30px'>&nbsp; Your request declined in " . $result["n_date"] . "</td>";
                $out .= "</tr>";
            } else if ($result["n_status"] == "3") {
                $out .= "<tr style='background-color:#ffffff; cursor: pointer;'>";
                $out .= "<td><img src='../img/reject.png' width='30px'>&nbsp; Your Request declined in " . $result["n_date"] . "</td>";
                $out .= "</tr>";
            } else {
                $out .= "<tr style='background-color:#ffffff; cursor: pointer;'>";
                $out .= "<td><img src='../img/correct.png' width='30px'>&nbsp; Your Request accepted in " . $result["n_date"] . "</td>";
                $out .= "</tr>";
            }
        }

        $out .= "</table>";

        echo $out;
    } else if ($_REQUEST["request"] == "viewnotification") {

        $query = "select * from notification where nid='" . $_REQUEST["notid"] . "'";
        $res = Search($query);
        if ($result = mysqli_fetch_assoc($res)) {

            if ($result["n_status"] == "2") {
                $querys = "Update notification set n_status='3' where nid='" . $_REQUEST["notid"] . "'";

                $ret = SUD($querys);

                if ($ret == 1) {
                    echo "1";
                } else {
                    echo "0";
                }
            } else {
                $querys = "Update notification set n_status='1' where nid='" . $_REQUEST["notid"] . "'";
                $ret = SUD($querys);

                if ($ret == 1) {
                    echo "1";
                } else {
                    echo "0";
                }
            }
        }
        echo $out;
    } else if ($_REQUEST["request"] == "getnotificationcount") {

        $query = "select count(nid) as noticount from notification where n_user = '" . $_SESSION["uid"] . "' and (n_status = '0' or n_status = '2')";
        $res = Search($query);
        if ($result = mysqli_fetch_assoc($res)) {

            $notic = $result["noticount"];
        } else {

            $notic = "0";
        }
        echo $notic;
    } else if ($_REQUEST["request"] == "getEmpsbyID") {

        $query = "select emp_img_URL from user where uid = '" . $_SESSION["uid"] . "'";
        $res = Search($query);
        if ($result = mysqli_fetch_assoc($res)) {

            if ($result["emp_img_URL"] == "") {

                $imgurl = "0";
            } else {
                $imgurl = substr($result["emp_img_URL"], 3);
            }
        }
        echo $imgurl;
    } else if ($_REQUEST["request"] == "changestat") {

        $res_nid_upd = SUD("update notification set n_status = '1' where nid = '" . $_REQUEST["nid"] . "'");

        echo $res_nid_upd;
    }
    //other methods
}
