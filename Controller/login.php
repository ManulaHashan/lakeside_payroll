<?php
error_reporting(0);
session_start();

include '../DB/DB.php';

if (isset($_REQUEST["submit"])) {
    // $query = "select uid,new_password from user where uid = (select user_uid from logindetails where username = '" . $_REQUEST["usr"] . "' and password='" . $_REQUEST["pwrd"] . "') and isactive = '1'";

    $query = "select a.uid,b.new_password from user a, logindetails b where a.uid=b.User_uid and b.username = '" . $_REQUEST["usr"] . "' and b.password='" . $_REQUEST["pwrd"] . "' and a.isactive = '1'";

    $res = Search($query);
    if ($Result = mysqli_fetch_assoc($res)) {
        if (empty($Result["new_password"])) {
            $_SESSION["uid"] = $Result["uid"];
            echo "SET";
        } else {
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
    if ($return == "1") {
        echo "OK";
    } else {
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

if ($_REQUEST["request"] == "getPreviouseleavecount") {

    $restDATA = Search("select uid from user where isactive = '1'");
    while ($resultsDATA = mysqli_fetch_assoc($restDATA)) {
        $queryYears = "select registerdDate from user where uid = '" . $resultsDATA["uid"] . "'";
        $resYears = Search($queryYears);

        if ($resultYears = mysqli_fetch_assoc($resYears)) {
            $joinDate = $resultYears["registerdDate"];
        }

        $YearDiff = date('Y-m-d') - $joinDate;
        $date1 = $joinDate;
        $date2 = date('Y-m-d');

        $diff = abs(strtotime($date2) - strtotime($date1));

        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

        if ($years >= "0" && $years < "2") {
            if ($years == "0" && $months >= "1" && $months <= "12") {

                $one_month_half = 0.5; // 1 half day
                $one_month_short = 0.5; // 2 short leaves

                $GetHalf = 0;
                $GetShort = 0;
                $Half_Available = 0;
                $Short_Available = 0;
                $Prev_Total_leave = 0;
                $Prev_Get_leave = 0;
                $Uniq_value = 0.5;
                $PreVTOTdata = 0;


                $reschkDataUpdate = Search("select pfylid from total_leave_data where uid='" . $resultsDATA["uid"] . "' and monthno = '" . date('m') . "'");
                if ($resultchkDataUpdate = mysqli_fetch_assoc($reschkDataUpdate)) {
                } else {

                    $resGETPREVTOTLEV = Search("select total_leave from total_leave_data where uid='" . $resultsDATA["uid"] . "'");
                    if ($resultGETPREVTOTLEV = mysqli_fetch_assoc($resGETPREVTOTLEV)) {
                        $Prev_Total_leave = $resultGETPREVTOTLEV["total_leave"];
                    }


                    //AND (type like 'Halfday Leave' or type like 'Leave')
                    $queryPreviouseHalf = "select sum(days) as previousehalf from employee_leave where uid = '" . $resultsDATA["uid"] . "' AND YEAR(date) = '" . date('Y') . "' AND MONTH(date) = '" . date('m', strtotime('-1 month')) . "' AND (type like 'Halfday Morning Leave' or type like 'Halfday Evening Leave')";
                    $resPreviouseHalf = Search($queryPreviouseHalf);

                    if ($resultPreviouseHalf = mysqli_fetch_assoc($resPreviouseHalf)) {
                        $Prev_Get_leave = $resultPreviouseHalf["previousehalf"];

                        if ($Prev_Get_leave == "") {
                            $Total_Value = $Prev_Total_leave + $Uniq_value;
                            $resCHKPreviouseHalf = Search("select pfylid from total_leave_data where uid='" . $resultsDATA["uid"] . "'");

                            if ($resultCHKPreviouseHalf = mysqli_fetch_assoc($resCHKPreviouseHalf)) {
                                $querysUpdate = "Update total_leave_data set total_leave='" . $Total_Value . "' where pfylid='" . $resultCHKPreviouseHalf["pfylid"] . "'";
                                $ret = SUD($querysUpdate);
                            } else {

                                $queryLeave = "insert into total_leave_data(total_leave, uid) values('" . $Total_Value . "','" . $resultsDATA["uid"] . "')";
                                SUD($queryLeave);
                            }
                        } else {
                            $PreVTOTdata = $Prev_Total_leave - $Prev_Get_leave;


                            if ($PreVTOTdata <= 0) {
                                $PreVdata = 0;
                            } else {
                                $PreVdata = $PreVTOTdata;
                            }

                            $Total_Value = $PreVdata + $Uniq_value;
                            $resCHKPreviouseHalf = Search("select pfylid from total_leave_data where uid='" . $resultsDATA["uid"] . "'");

                            if ($resultCHKPreviouseHalf = mysqli_fetch_assoc($resCHKPreviouseHalf)) {
                                $querysUpdate = "Update total_leave_data set total_leave='" . $Total_Value . "' where pfylid='" . $resultCHKPreviouseHalf["pfylid"] . "'";
                                $ret = SUD($querysUpdate);
                            } else {
                                $queryLeave = "insert into total_leave_data(total_leave, uid) values('" . $Total_Value . "','" . $resultsDATA["uid"] . "')";
                                SUD($queryLeave);
                            }
                        }
                    }

                    $querysDataUpdate = "Update total_leave_data set monthno = '" . date('m') . "' where uid='" . $resultsDATA["uid"] . "'";
                    $ret = SUD($querysDataUpdate);
                }
            }
        }
    }
}

session_write_close();