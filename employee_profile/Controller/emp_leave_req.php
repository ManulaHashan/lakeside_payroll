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

        $out = "<table class='table table-striped table-bordered nowrap'><thead style='position : sticky; top : 0;  z-index: 0;'>
        <tr>
        <th>Leave Request</th>
        <th>Confirm Status</th>
        </tr></thead><tbody>";

        $query;

        if ($_REQUEST["decission"] == "0") {
            $query = "select * from emp_leave_request where empid='" . $_SESSION["uid"] . "' and reqstatus='0'";
        } else {
            $query = "select * from emp_leave_request where empid='" . $_SESSION["uid"] . "' and request_date between '" . $_REQUEST["frmdate"] . "' and '" . $_REQUEST["tod"] . "' and reqstatus='" . $_REQUEST["decission"] . "'";
        }




        $res = Search($query);
        while ($result = mysqli_fetch_assoc($res)) {

            $out .= "<tr>";

            if ($result["reqstatus"] == "0") {

                $out .= "<td style='background-color: #DAA520; color: black;'><b>Leave Type : </b>" . $result["leave_type"] . " |  <b>Date of Leave :</b> " . $result["fromdate"] . " | <b>Number of Days :</b> " . $result["days"] . "</br><b>Reason :</b> " . $result["reason"] . " |  <b>Date The Request Was Sent :</b> " . $result["request_date"] . " |  <b>Decision : Pending</b></td>";
            } else {
                $out .= "<td><b>Leave Type : </b>" . $result["leave_type"] . " |  <b>Date of Leave :</b> " . $result["fromdate"] . " | <b>Number of Days :</b> " . $result["days"] . " </br><b>Reason :</b> " . $result["reason"] . " |  <b>Date The Request Was Sent :</b> " . $result["request_date"] . "</td>";
            }

            if ($result["app_status"] == "0") {

                $out .= "<td></td>";
            } else if ($result["app_status"] == "2") {
                $queryUserDecline = "select fname as usernames from user where uid='" . $result["app_by"] . "'";
                $resUserDecline = Search($queryUserDecline);
                if ($resultUserDecline = mysqli_fetch_assoc($resUserDecline)) {

                    $UserDecline = $resultUserDecline["usernames"];
                }

                $out .= "<td style='background-color: #ff0000; color: black;'><b>Declined Date :</b> " . $result["app_date"] . " |  <b>Declined Time :</b> " . $result["app_time"] . " | <b>Declined By :</b> " . $UserDecline . " |  <b>Decision : Decline</b></td>";
            } else {
                $queryUserConfirm = "select fname as username from user where uid='" . $result["app_by"] . "'";
                $resUserConfirm = Search($queryUserConfirm);
                if ($resultUserConfirm = mysqli_fetch_assoc($resUserConfirm)) {

                    $UserConfirm = $resultUserConfirm["username"];
                }

                $out .= "<td style='background-color: #47b833; color: black;'><b>Approved Date :</b> " . $result["app_date"] . " |  <b>Approved Time :</b> " . $result["app_time"] . " | <b>Approved By :</b> " . $UserConfirm . " |  <b>Decision : Confirm</b></td>";
            }

            $out .= "</tr>";
        }

        $out .= "</tbody></table>";

        echo $out;
    } else if ($_REQUEST["request"] == "leaverequest") {
        $query = "select empid from emp_leave_request where empid = '" . $_SESSION["uid"] . "' and fromdate= '" . $_REQUEST["from"] . "' and leave_type = '" . $_REQUEST["leavetype"] . "' and reqstatus !='2' ";   //and reqstatus !='2' add this new part
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
    } else if ($_REQUEST["request"] == "getleavecount") {

        $queryYears = "select registerdDate,first_year_leave_end_date from user where uid = '" . $_SESSION["uid"] . "'";
        $resYears = Search($queryYears);

        if ($resultYears = mysqli_fetch_assoc($resYears)) {
            $joinDate = $resultYears["registerdDate"];
            $first_yr_end_Date = $resultYears["first_year_leave_end_date"];
        }

        $YearDiff = date('Y-m-d') - $joinDate;
        $date1 = $joinDate;
        $date2 = date('Y-m-d');

        $diff = abs(strtotime($date2) - strtotime($date1));

        $years = floor($diff / (365 * 60 * 60 * 24));
        $months = floor(($diff - $years * 365 * 60 * 60 * 24) / (30 * 60 * 60 * 24));
        $days = floor(($diff - $years * 365 * 60 * 60 * 24 - $months * 30 * 60 * 60 * 24) / (60 * 60 * 24));

        $Total_leave = 0;
        $Get_Leave_Half_OR_CASUAL = 0;
        $Available_Leave_Half_OR_CASUAL = 0;
        $Get_Leave_Short_OR_ANNUAL = 0;
        $Available_Leave_Short_OR_ANNUAL = 0;
        $Get_Leave_MEDICAL = 0;
        $Available_Leave_MEDICAL = 0;
        $NOPAY = 0;
        $DUTY  = 0;
        $MATERNITY = 0;
        $PARENTAL = 0;
        $LIUE_LEAVE = 0;

        $Get_Separate_Short = 0;
        $Available_Separate_Short = 0;

        $Get_Separate_Half = 0;
        $Available_Separate_Half = 0;

        $Flag = "FILL";


        if ($years >= "0" && $years < "2") {

            if ($years == "0" && $months == "0") {
                $Total_leave = 0;
                $Flag = "Empty";
                echo 'A' . "#" . $Get_Leave_Half_OR_CASUAL . "#" . $Available_Leave_Half_OR_CASUAL . "#" . $Get_Leave_Short_OR_ANNUAL . "#" . $Available_Leave_Short_OR_ANNUAL . "#" . $Get_Leave_MEDICAL . "#" . $Available_Leave_MEDICAL . "#" . $Total_leave . "#" . $Get_Separate_Short . "#" . $Available_Separate_Short . "#" . $Get_Separate_Half . "#" . $Available_Separate_Half . "#" . $NOPAY . "#" . $DUTY . "#" . $MATERNITY . "#" . $PARENTAL . "#" . $LIUE_LEAVE . "#" . $Flag;
            } else if ($years == "0" && $months >= "1" && $months <= "12") {
                //2024-06-22 NEW PART
                if (date('m', strtotime($joinDate)) == "07" || date('m', strtotime($joinDate)) == "08" || date('m', strtotime($joinDate)) == "09" || date('m', strtotime($joinDate)) == "10" || date('m', strtotime($joinDate)) == "11" || date('m', strtotime($joinDate)) == "12") {
                    if ($years == "0" && $months >= "6") {
                        if (date('m', strtotime($joinDate)) == "01" || date('m', strtotime($joinDate)) == "02" || date('m', strtotime($joinDate)) == "03") {
                            $casual_leaves = 7;
                            $annual_leaves = 14;
                            $medical_leaves = 7;
                        } else if (date('m', strtotime($joinDate)) == "04" || date('m', strtotime($joinDate)) == "05" || date('m', strtotime($joinDate)) == "06") {
                            $casual_leaves = 7;
                            $annual_leaves = 10;
                            $medical_leaves = 5;
                        } else if (date('m', strtotime($joinDate)) == "07" || date('m', strtotime($joinDate)) == "08" || date('m', strtotime($joinDate)) == "09") {
                            $casual_leaves = 7;
                            $annual_leaves = 7;
                            $medical_leaves = 3;
                        } else if (date('m', strtotime($joinDate)) == "10" || date('m', strtotime($joinDate)) == "11" || date('m', strtotime($joinDate)) == "12") {
                            $casual_leaves = 7;
                            $annual_leaves = 4;
                            $medical_leaves = 0;
                        }

                        $one_month_short = 0.5; // 2 short leaves

                        if (!empty($first_yr_end_Date) && $first_yr_end_Date != "0000-00-00") {
                            $Get_DATA = "AND date > '" . $first_yr_end_Date . "'";
                        } else {
                            $Get_DATA = "";
                        }

                        $resHalf = Search("select sum(days) as halfleave from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' AND (type like 'Halfday Morning Leave' or type like 'Halfday Evening Leave') " . $Get_DATA . "");

                        if ($resultHalf = mysqli_fetch_assoc($resHalf)) {
                            if ($resultHalf["halfleave"] == "") {
                                $Get_Separate_Half = 0;
                            } else {
                                $Get_Separate_Half = $resultHalf["halfleave"];
                            }
                        }

                        $res_casual = Search("select sum(days) as totalLeaves_casual from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' and type like 'Casual Leave'");
                        if ($result_casual = mysqli_fetch_assoc($res_casual)) {
                            if ($result_casual["totalLeaves_casual"] == "") {
                                $Get_Leave_Half_OR_CASUAL = 0 + $Get_Separate_Half;
                                $Available_Leave_Half_OR_CASUAL = $casual_leaves - $Get_Separate_Half;
                            } else {
                                $Get_Leave_Half_OR_CASUAL = $result_casual["totalLeaves_casual"];

                                if ($Get_Leave_Half_OR_CASUAL >= $casual_leaves) {
                                    $Available_Leave_Half_OR_CASUAL = 0;
                                    $Get_Leave_Half_OR_CASUAL = $casual_leaves;
                                } else {
                                    $Available_Leave_Half_OR_CASUAL = $casual_leaves - ($Get_Leave_Half_OR_CASUAL + $Get_Separate_Half);
                                    $Get_Leave_Half_OR_CASUAL = $Get_Leave_Half_OR_CASUAL + $Get_Separate_Half; // 2024-05-14 added
                                }
                            }
                        }


                        $res_annual = Search("select sum(days) as totalLeaves_anual from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' and type like 'Annual Leave'");
                        if ($result_annual = mysqli_fetch_assoc($res_annual)) {
                            if ($result_annual["totalLeaves_anual"] == "") {
                                $Get_Leave_Short_OR_ANNUAL = 0;
                                $Available_Leave_Short_OR_ANNUAL = $annual_leaves;
                            } else {
                                $Get_Leave_Short_OR_ANNUAL = $result_annual["totalLeaves_anual"];

                                if ($Get_Leave_Short_OR_ANNUAL >= $annual_leaves) {
                                    $Get_Leave_Short_OR_ANNUAL = $annual_leaves;
                                    $Available_Leave_Short_OR_ANNUAL = 0;
                                } else {
                                    $Available_Leave_Short_OR_ANNUAL = $annual_leaves - $Get_Leave_Short_OR_ANNUAL;
                                }
                            }
                        }

                        $res_medical = Search("select sum(days) as totalLeaves_medical from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' and type like 'Medical Leave'");
                        if ($result_medical = mysqli_fetch_assoc($res_medical)) {
                            if ($result_medical["totalLeaves_medical"] == "") {
                                $Get_Leave_MEDICAL = 0;
                                $Available_Leave_MEDICAL = $medical_leaves;
                            } else {
                                $Get_Leave_MEDICAL = $result_medical["totalLeaves_medical"];

                                if ($Get_Leave_MEDICAL >= $medical_leaves) {
                                    $Get_Leave_MEDICAL = $medical_leaves;
                                    $Available_Leave_MEDICAL = 0;
                                } else {
                                    $Available_Leave_MEDICAL = $medical_leaves - $Get_Leave_MEDICAL;
                                }
                            }
                        }

                        $queryShort = "select sum(days) as shortleave from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' AND MONTH(date) = '" . date('m') . "' AND (type like 'Short Morning Leave' or type like 'Short Evening Leave')";
                        $resShort = Search($queryShort);

                        if ($resultShort = mysqli_fetch_assoc($resShort)) {
                            if ($resultShort["shortleave"] == "") {
                                $Get_Separate_Short = 0;
                                $Available_Separate_Short = $one_month_short - 0;
                            } else {
                                $Get_Separate_Short = $resultShort["shortleave"];
                                $Available_Separate_Short = $one_month_short - $Get_Separate_Short;
                            }
                        }

                        $queryNopay = "select sum(days) as Nopayleave from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' AND (type like 'Nopay Full Day Leave' or type like 'Nopay Morning Leave' or type like 'Nopay Evening Leave')";
                        $resNopay = Search($queryNopay);

                        if ($resultNopay = mysqli_fetch_assoc($resNopay)) {
                            if ($resultNopay["Nopayleave"] == "") {
                                $NOPAYDATA = 0;
                            } else {
                                $NOPAYDATA = $resultNopay["Nopayleave"];
                            }
                        }


                        $resDuty = Search("select sum(days) as Dutyleave from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' AND (type like 'Duty Full Day Leave' or type like 'Duty Morning Leave' or type like 'Duty Evening Leave')");

                        if ($resultDuty = mysqli_fetch_assoc($resDuty)) {
                            if ($resultDuty["Dutyleave"] == "") {
                                $DUTY = 0;
                            } else {
                                $DUTY = $resultDuty["Dutyleave"];
                            }
                        }



                        $resMaternity = Search("select sum(days) as Maternityleave from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' AND type like 'Maternity Leave'");
                        if ($resultMaternity = mysqli_fetch_assoc($resMaternity)) {
                            if ($resultMaternity["Maternityleave"] == "") {
                                $MATERNITY = 0;
                            } else {
                                $MATERNITY = $resultMaternity["Maternityleave"];
                            }
                        }

                        $resParental = Search("select sum(days) as Parentalleave from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' AND type like 'Parental Leave'");

                        if ($resultParental = mysqli_fetch_assoc($resParental)) {
                            if ($resultParental["Parentalleave"] == "") {
                                $PARENTAL = 0;
                            } else {
                                $PARENTAL = $resultParental["Parentalleave"];
                            }
                        }


                        $queryLiue_Leave = "select sum(days) as liueleave from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' AND type like 'Lieu Leave'";
                        $resLiue_Leave = Search($queryLiue_Leave);

                        if ($resultLiue_Leave = mysqli_fetch_assoc($resLiue_Leave)) {
                            if ($resultLiue_Leave["liueleave"] == "") {
                                $LIUELEAVEDATA = 0;
                            } else {
                                $LIUELEAVEDATA = $resultLiue_Leave["liueleave"];
                            }
                        }

                        $NOPAY = $NOPAYDATA - $LIUELEAVEDATA;
                        $LIUE_LEAVE = $LIUELEAVEDATA;

                        if ($NOPAY <= 0) {
                            $NOPAY = 0;
                        }

                        $Total_leave = $annual_leaves + $casual_leaves;

                        echo 'B' . "#" . $Get_Leave_Half_OR_CASUAL . "#" . $Available_Leave_Half_OR_CASUAL . "#" . $Get_Leave_Short_OR_ANNUAL . "#" . $Available_Leave_Short_OR_ANNUAL . "#" . $Get_Leave_MEDICAL . "#" . $Available_Leave_MEDICAL . "#" . $Total_leave . "#" . $Get_Separate_Short . "#" . $Available_Separate_Short . "#" . $Get_Separate_Half . "#" . $Available_Separate_Half . "#" . $NOPAY . "#" . $DUTY . "#" . $MATERNITY . "#" . $PARENTAL . "#" . $LIUE_LEAVE . "#" . $Flag;
                    }
                }

                $one_month_half = 0.5; // 1 half day
                $one_month_short = 0.5; // 2 short leaves

                $queryHalf = "select sum(days) as halfleave from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' AND MONTH(date) = '" . date('m') . "' AND (type like 'Halfday Morning Leave' or type like 'Halfday Evening Leave')";
                $resHalf = Search($queryHalf);

                if ($resultHalf = mysqli_fetch_assoc($resHalf)) {
                    if ($resultHalf["halfleave"] == "") {
                        $resPreviouseHalf = Search("select total_leave as previousehalf from total_leave_data where uid = '" . $_SESSION["uid"] . "'");

                        if ($resultPreviouseHalf = mysqli_fetch_assoc($resPreviouseHalf)) {
                            $TOTAL = $resultPreviouseHalf["previousehalf"];

                            if ($resultPreviouseHalf["previousehalf"] == "") {
                                $Get_Leave_Half_OR_CASUAL = 0;
                                $Available_Leave_Half_OR_CASUAL = $one_month_half;
                            } else {
                                $Get_Leave_Half_OR_CASUAL = 0;
                                $Available_Leave_Half_OR_CASUAL = $resultPreviouseHalf["previousehalf"];
                            }
                        }
                    } else {
                        $resPreviouseHalf = Search("select total_leave as previousehalf from total_leave_data where uid = '" . $_SESSION["uid"] . "'");

                        if ($resultPreviouseHalf = mysqli_fetch_assoc($resPreviouseHalf)) {
                            $TOTAL = $resultPreviouseHalf["previousehalf"];

                            if ($resultPreviouseHalf["previousehalf"] == "") {
                                $Get_Leave_Half_OR_CASUAL = 0;
                                $Available_Leave_Half_OR_CASUAL = $one_month_half;
                            } else {
                                $Get_Leave_Half_OR_CASUAL = $resultHalf["halfleave"];
                                $Available_Leave_Half_OR_CASUAL = $resultPreviouseHalf["previousehalf"];
                            }
                        }
                    }
                }


                $queryShort = "select sum(days) as shortleave from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' AND MONTH(date) = '" . date('m') . "' AND (type like 'Short Morning Leave' or type like 'Short Evening Leave')";
                $resShort = Search($queryShort);

                if ($resultShort = mysqli_fetch_assoc($resShort)) {
                    if ($resultShort["shortleave"] == "") {
                        $Get_Separate_Short = 0;
                        $Available_Separate_Short = $one_month_short - 0;
                    } else {
                        $Get_Separate_Short = $resultShort["shortleave"];
                        $Available_Separate_Short = $one_month_short - $Get_Separate_Short;
                    }
                }

                $queryNopay = "select sum(days) as Nopayleave from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' AND MONTH(date) = '" . date('m') . "' AND (type like 'Nopay Full Day Leave' or type like 'Nopay Morning Leave' or type like 'Nopay Evening Leave')";
                $resNopay = Search($queryNopay);

                if ($resultNopay = mysqli_fetch_assoc($resNopay)) {
                    if ($resultNopay["Nopayleave"] == "") {
                        $NOPAYDATA = 0;
                    } else {
                        $NOPAYDATA = $resultNopay["Nopayleave"];
                    }
                }


                $queryLiue_Leave = "select sum(days) as liueleave from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' AND MONTH(date) = '" . date('m') . "' AND type like 'Lieu Leave'";
                $resLiue_Leave = Search($queryLiue_Leave);

                if ($resultLiue_Leave = mysqli_fetch_assoc($resLiue_Leave)) {
                    if ($resultLiue_Leave["liueleave"] == "") {
                        $LIUELEAVEDATA = 0;
                    } else {
                        $LIUELEAVEDATA = $resultLiue_Leave["liueleave"];
                    }
                }

                $Total_leave = $Available_Leave_Half_OR_CASUAL;
                $NOPAY = $NOPAYDATA - $LIUELEAVEDATA;
                $LIUE_LEAVE = $LIUELEAVEDATA;

                if ($NOPAY <= 0) {
                    $NOPAY = 0;
                }

                if ($Available_Leave_Half_OR_CASUAL <= 0) {
                    $Available_Leave_Half_OR_CASUAL = 0;
                }

                if ($Available_Separate_Short <= 0) {
                    $Available_Separate_Short = 0;
                }

                echo 'A' . "#" . $Get_Leave_Half_OR_CASUAL . "#" . $Available_Leave_Half_OR_CASUAL . "#" . $Get_Leave_Short_OR_ANNUAL . "#" . $Available_Leave_Short_OR_ANNUAL . "#" . $Get_Leave_MEDICAL . "#" . $Available_Leave_MEDICAL . "#" . $Total_leave . "#" . $Get_Separate_Short . "#" . $Available_Separate_Short . "#" . $Get_Separate_Half . "#" . $Available_Separate_Half . "#" . $NOPAY . "#" . $DUTY . "#" . $MATERNITY . "#" . $PARENTAL . "#" . $LIUE_LEAVE . "#" . $Flag;
            } else {
                if (date('m', strtotime($joinDate)) == "01" || date('m', strtotime($joinDate)) == "02" || date('m', strtotime($joinDate)) == "03") {
                    $casual_leaves = 7;
                    $annual_leaves = 14;
                    $medical_leaves = 7;
                } else if (date('m', strtotime($joinDate)) == "04" || date('m', strtotime($joinDate)) == "05" || date('m', strtotime($joinDate)) == "06") {
                    $casual_leaves = 7;
                    $annual_leaves = 10;
                    $medical_leaves = 5;
                } else if (date('m', strtotime($joinDate)) == "07" || date('m', strtotime($joinDate)) == "08" || date('m', strtotime($joinDate)) == "09") {
                    $casual_leaves = 7;
                    $annual_leaves = 7;
                    $medical_leaves = 3;
                } else if (date('m', strtotime($joinDate)) == "10" || date('m', strtotime($joinDate)) == "11" || date('m', strtotime($joinDate)) == "12") {
                    $casual_leaves = 7;
                    $annual_leaves = 4;
                    $medical_leaves = 0;
                }

                $one_month_short = 0.5; // 2 short leaves

                if (!empty($first_yr_end_Date) && $first_yr_end_Date != "0000-00-00") {
                    $Get_DATA = "AND date > '" . $first_yr_end_Date . "'";
                } else {
                    $Get_DATA = "";
                }

                $resHalf = Search("select sum(days) as halfleave from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' AND (type like 'Halfday Morning Leave' or type like 'Halfday Evening Leave') " . $Get_DATA . "");

                if ($resultHalf = mysqli_fetch_assoc($resHalf)) {
                    if ($resultHalf["halfleave"] == "") {
                        $Get_Separate_Half = 0;
                    } else {
                        $Get_Separate_Half = $resultHalf["halfleave"];
                    }
                }

                $res_casual = Search("select sum(days) as totalLeaves_casual from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' and type like 'Casual Leave'");
                if ($result_casual = mysqli_fetch_assoc($res_casual)) {
                    if ($result_casual["totalLeaves_casual"] == "") {
                        $Get_Leave_Half_OR_CASUAL = 0 + $Get_Separate_Half;
                        $Available_Leave_Half_OR_CASUAL = $casual_leaves - $Get_Separate_Half;
                    } else {
                        $Get_Leave_Half_OR_CASUAL = $result_casual["totalLeaves_casual"];

                        if ($Get_Leave_Half_OR_CASUAL >= $casual_leaves) {
                            $Available_Leave_Half_OR_CASUAL = 0;
                            $Get_Leave_Half_OR_CASUAL = $casual_leaves;
                        } else {
                            $Available_Leave_Half_OR_CASUAL = $casual_leaves - ($Get_Leave_Half_OR_CASUAL + $Get_Separate_Half);
                            $Get_Leave_Half_OR_CASUAL = $Get_Leave_Half_OR_CASUAL + $Get_Separate_Half; // 2024-05-14 added
                        }
                    }
                }


                $res_annual = Search("select sum(days) as totalLeaves_anual from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' and type like 'Annual Leave'");
                if ($result_annual = mysqli_fetch_assoc($res_annual)) {
                    if ($result_annual["totalLeaves_anual"] == "") {
                        $Get_Leave_Short_OR_ANNUAL = 0;
                        $Available_Leave_Short_OR_ANNUAL = $annual_leaves;
                    } else {
                        $Get_Leave_Short_OR_ANNUAL = $result_annual["totalLeaves_anual"];

                        if ($Get_Leave_Short_OR_ANNUAL >= $annual_leaves) {
                            $Get_Leave_Short_OR_ANNUAL = $annual_leaves;
                            $Available_Leave_Short_OR_ANNUAL = 0;
                        } else {
                            $Available_Leave_Short_OR_ANNUAL = $annual_leaves - $Get_Leave_Short_OR_ANNUAL;
                        }
                    }
                }

                $res_medical = Search("select sum(days) as totalLeaves_medical from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' and type like 'Medical Leave'");
                if ($result_medical = mysqli_fetch_assoc($res_medical)) {
                    if ($result_medical["totalLeaves_medical"] == "") {
                        $Get_Leave_MEDICAL = 0;
                        $Available_Leave_MEDICAL = $medical_leaves;
                    } else {
                        $Get_Leave_MEDICAL = $result_medical["totalLeaves_medical"];

                        if ($Get_Leave_MEDICAL >= $medical_leaves) {
                            $Get_Leave_MEDICAL = $medical_leaves;
                            $Available_Leave_MEDICAL = 0;
                        } else {
                            $Available_Leave_MEDICAL = $medical_leaves - $Get_Leave_MEDICAL;
                        }
                    }
                }

                $queryShort = "select sum(days) as shortleave from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' AND MONTH(date) = '" . date('m') . "' AND (type like 'Short Morning Leave' or type like 'Short Evening Leave')";
                $resShort = Search($queryShort);

                if ($resultShort = mysqli_fetch_assoc($resShort)) {
                    if ($resultShort["shortleave"] == "") {
                        $Get_Separate_Short = 0;
                        $Available_Separate_Short = $one_month_short - 0;
                    } else {
                        $Get_Separate_Short = $resultShort["shortleave"];
                        $Available_Separate_Short = $one_month_short - $Get_Separate_Short;
                    }
                }

                $queryNopay = "select sum(days) as Nopayleave from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' AND (type like 'Nopay Full Day Leave' or type like 'Nopay Morning Leave' or type like 'Nopay Evening Leave')";
                $resNopay = Search($queryNopay);

                if ($resultNopay = mysqli_fetch_assoc($resNopay)) {
                    if ($resultNopay["Nopayleave"] == "") {
                        $NOPAYDATA = 0;
                    } else {
                        $NOPAYDATA = $resultNopay["Nopayleave"];
                    }
                }


                $resDuty = Search("select sum(days) as Dutyleave from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' AND (type like 'Duty Full Day Leave' or type like 'Duty Morning Leave' or type like 'Duty Evening Leave')");

                if ($resultDuty = mysqli_fetch_assoc($resDuty)) {
                    if ($resultDuty["Dutyleave"] == "") {
                        $DUTY = 0;
                    } else {
                        $DUTY = $resultDuty["Dutyleave"];
                    }
                }



                $resMaternity = Search("select sum(days) as Maternityleave from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' AND type like 'Maternity Leave'");
                if ($resultMaternity = mysqli_fetch_assoc($resMaternity)) {
                    if ($resultMaternity["Maternityleave"] == "") {
                        $MATERNITY = 0;
                    } else {
                        $MATERNITY = $resultMaternity["Maternityleave"];
                    }
                }

                $resParental = Search("select sum(days) as Parentalleave from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' AND type like 'Parental Leave'");

                if ($resultParental = mysqli_fetch_assoc($resParental)) {
                    if ($resultParental["Parentalleave"] == "") {
                        $PARENTAL = 0;
                    } else {
                        $PARENTAL = $resultParental["Parentalleave"];
                    }
                }


                $queryLiue_Leave = "select sum(days) as liueleave from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' AND type like 'Lieu Leave'";
                $resLiue_Leave = Search($queryLiue_Leave);

                if ($resultLiue_Leave = mysqli_fetch_assoc($resLiue_Leave)) {
                    if ($resultLiue_Leave["liueleave"] == "") {
                        $LIUELEAVEDATA = 0;
                    } else {
                        $LIUELEAVEDATA = $resultLiue_Leave["liueleave"];
                    }
                }

                $NOPAY = $NOPAYDATA - $LIUELEAVEDATA;
                $LIUE_LEAVE = $LIUELEAVEDATA;

                if ($NOPAY <= 0) {
                    $NOPAY = 0;
                }

                $Total_leave = $annual_leaves + $casual_leaves;

                echo 'B' . "#" . $Get_Leave_Half_OR_CASUAL . "#" . $Available_Leave_Half_OR_CASUAL . "#" . $Get_Leave_Short_OR_ANNUAL . "#" . $Available_Leave_Short_OR_ANNUAL . "#" . $Get_Leave_MEDICAL . "#" . $Available_Leave_MEDICAL . "#" . $Total_leave . "#" . $Get_Separate_Short . "#" . $Available_Separate_Short . "#" . $Get_Separate_Half . "#" . $Available_Separate_Half . "#" . $NOPAY . "#" . $DUTY . "#" . $MATERNITY . "#" . $PARENTAL . "#" . $LIUE_LEAVE . "#" . $Flag;
            }
        } else {
            $casual_leaves = 7;
            $annual_leaves = 14;
            $medical_leaves = 7;
            $one_month_short = 0.5; // 2 short leaves


            $resHalf = Search("select sum(days) as halfleave from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' AND (type like 'Halfday Morning Leave' or type like 'Halfday Evening Leave') ");

            if ($resultHalf = mysqli_fetch_assoc($resHalf)) {
                if ($resultHalf["halfleave"] == "") {
                    $Get_Separate_Half = 0;
                } else {
                    $Get_Separate_Half = $resultHalf["halfleave"];
                }
            }

            $res_casual = Search("select sum(days) as totalLeaves_casual from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' and type like 'Casual Leave'");
            if ($result_casual = mysqli_fetch_assoc($res_casual)) {
                if ($result_casual["totalLeaves_casual"] == "") {
                    $Get_Leave_Half_OR_CASUAL = 0 + $Get_Separate_Half;
                    $Available_Leave_Half_OR_CASUAL = $casual_leaves - $Get_Separate_Half;
                } else {
                    $Get_Leave_Half_OR_CASUAL = $result_casual["totalLeaves_casual"];

                    if ($Get_Leave_Half_OR_CASUAL >= $casual_leaves) {
                        $Available_Leave_Half_OR_CASUAL = 0;
                        $Get_Leave_Half_OR_CASUAL = $casual_leaves;
                    } else {
                        $Available_Leave_Half_OR_CASUAL = $casual_leaves - ($Get_Leave_Half_OR_CASUAL + $Get_Separate_Half);
                        $Get_Leave_Half_OR_CASUAL = $Get_Leave_Half_OR_CASUAL + $Get_Separate_Half; // 2024-05-14 added
                    }
                }
            }


            $res_annual = Search("select sum(days) as totalLeaves_anual from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' and type like 'Annual Leave'");
            if ($result_annual = mysqli_fetch_assoc($res_annual)) {
                if ($result_annual["totalLeaves_anual"] == "") {
                    $Get_Leave_Short_OR_ANNUAL = 0;
                    $Available_Leave_Short_OR_ANNUAL = $annual_leaves;
                } else {
                    $Get_Leave_Short_OR_ANNUAL = $result_annual["totalLeaves_anual"];

                    if ($Get_Leave_Short_OR_ANNUAL >= $annual_leaves) {
                        $Get_Leave_Short_OR_ANNUAL = $annual_leaves;
                        $Available_Leave_Short_OR_ANNUAL = 0;
                    } else {
                        $Available_Leave_Short_OR_ANNUAL = $annual_leaves - $Get_Leave_Short_OR_ANNUAL;
                    }
                }
            }

            $res_medical = Search("select sum(days) as totalLeaves_medical from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' and type like 'Medical Leave'");
            if ($result_medical = mysqli_fetch_assoc($res_medical)) {
                if ($result_medical["totalLeaves_medical"] == "") {
                    $Get_Leave_MEDICAL = 0;
                    $Available_Leave_MEDICAL = $medical_leaves;
                } else {
                    $Get_Leave_MEDICAL = $result_medical["totalLeaves_medical"];

                    if ($Get_Leave_MEDICAL >= $medical_leaves) {
                        $Get_Leave_MEDICAL = $medical_leaves;
                        $Available_Leave_MEDICAL = 0;
                    } else {
                        $Available_Leave_MEDICAL = $medical_leaves - $Get_Leave_MEDICAL;
                    }
                }
            }

            $queryShort = "select sum(days) as shortleave from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' AND MONTH(date) = '" . date('m') . "' AND (type like 'Short Morning Leave' or type like 'Short Evening Leave')";
            $resShort = Search($queryShort);

            if ($resultShort = mysqli_fetch_assoc($resShort)) {
                if ($resultShort["shortleave"] == "") {
                    $Get_Separate_Short = 0;
                    $Available_Separate_Short = $one_month_short - 0;
                } else {
                    $Get_Separate_Short = $resultShort["shortleave"];
                    $Available_Separate_Short = $one_month_short - $Get_Separate_Short;
                }
            }

            $queryNopay = "select sum(days) as Nopayleave from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' AND (type like 'Nopay Full Day Leave' or type like 'Nopay Morning Leave' or type like 'Nopay Evening Leave')";
            $resNopay = Search($queryNopay);

            if ($resultNopay = mysqli_fetch_assoc($resNopay)) {
                if ($resultNopay["Nopayleave"] == "") {
                    $NOPAYDATA = 0;
                } else {
                    $NOPAYDATA = $resultNopay["Nopayleave"];
                }
            }


            $resDuty = Search("select sum(days) as Dutyleave from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' AND (type like 'Duty Full Day Leave' or type like 'Duty Morning Leave' or type like 'Duty Evening Leave')");

            if ($resultDuty = mysqli_fetch_assoc($resDuty)) {
                if ($resultDuty["Dutyleave"] == "") {
                    $DUTY = 0;
                } else {
                    $DUTY = $resultDuty["Dutyleave"];
                }
            }



            $resMaternity = Search("select sum(days) as Maternityleave from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' AND type like 'Maternity Leave'");
            if ($resultMaternity = mysqli_fetch_assoc($resMaternity)) {
                if ($resultMaternity["Maternityleave"] == "") {
                    $MATERNITY = 0;
                } else {
                    $MATERNITY = $resultMaternity["Maternityleave"];
                }
            }

            $resParental = Search("select sum(days) as Parentalleave from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' AND type like 'Parental Leave'");

            if ($resultParental = mysqli_fetch_assoc($resParental)) {
                if ($resultParental["Parentalleave"] == "") {
                    $PARENTAL = 0;
                } else {
                    $PARENTAL = $resultParental["Parentalleave"];
                }
            }


            $queryLiue_Leave = "select sum(days) as liueleave from employee_leave where uid = '" . $_SESSION["uid"] . "' AND YEAR(date) = '" . date('Y') . "' AND type like 'Lieu Leave'";
            $resLiue_Leave = Search($queryLiue_Leave);

            if ($resultLiue_Leave = mysqli_fetch_assoc($resLiue_Leave)) {
                if ($resultLiue_Leave["liueleave"] == "") {
                    $LIUELEAVEDATA = 0;
                } else {
                    $LIUELEAVEDATA = $resultLiue_Leave["liueleave"];
                }
            }

            $NOPAY = $NOPAYDATA - $LIUELEAVEDATA;
            $LIUE_LEAVE = $LIUELEAVEDATA;

            if ($NOPAY <= 0) {
                $NOPAY = 0;
            }


            $Total_leave = $annual_leaves + $casual_leaves;

            echo 'C' . "#" . $Get_Leave_Half_OR_CASUAL . "#" . $Available_Leave_Half_OR_CASUAL . "#" . $Get_Leave_Short_OR_ANNUAL . "#" . $Available_Leave_Short_OR_ANNUAL . "#" . $Get_Leave_MEDICAL . "#" . $Available_Leave_MEDICAL . "#" . $Total_leave . "#" . $Get_Separate_Short . "#" . $Available_Separate_Short . "#" . $Get_Separate_Half . "#" . $Available_Separate_Half . "#" . $NOPAY . "#" . $DUTY . "#" . $MATERNITY . "#" . $PARENTAL . "#" . $LIUE_LEAVE . "#" . $Flag;
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
    }
    //other methods
}

if (isset($_POST['btnsubmit'])) {

    $postArr = $_POST;


    unset($postArr['btnsubmit']);

    $keys = array_keys($postArr);
    for ($i = 0; $i < count($keys); $i++) {

        $value = $postArr[$keys[$i]];

        $valArr = explode("@", $value);

        $query = "select empid from emp_leave_request where empid = '" . $_SESSION["uid"] . "' and fromdate= '" . $valArr[2] . "' and reqstatus !='2' and leave_type = '" . $valArr[1] . "'";
        $res = Search($query);
        if ($result = mysqli_fetch_assoc($res)) {

            header('Location:  ../Views/emp_leave_req.php?success=3');
        } else {

            //insert request

            $insertQuery = "insert into emp_leave_request(empid, fromdate, days, leave_type, reqstatus, request_date, request_time, app_status, conf_status, reason)"
                . " values('" . $_SESSION["uid"] . "','" . $valArr[2] . "','" . $valArr[3] . "','" . $valArr[1] . "','0','" . date("Y-m-d") . "','" . date("H:i:s") . "','0','0','" . $valArr[4] . "')";
            $return = SUD($insertQuery);

            if ($return == "1") {
                header('Location:  ../Views/emp_leave_req.php?success=1');
            } else {
                header('Location:  ../Views/emp_leave_req.php?success=2');
            }
        }
    }
}


function GetHalfMorningIntime($UserID, $DATE)
{
    $resu = Search("select work_typ from user where uid = '" . $UserID . "' and isactive='1'");
    if ($resultu = mysqli_fetch_assoc($resu)) {
        $Work_TYPE = $resultu["work_typ"];
    }

    if ($Work_TYPE == "2") {
        //get shift type id according to User ID
        $res_shift = Search("select shift_type_id from emp_shift_has_dates eshd, emp_working_shift ews where ews.ewsid = eshd.ewsid and ews.uid = '" . $UserID . "' and eshd.date = '" . $DATE . "'");
        if ($result_shift = mysqli_fetch_assoc($res_shift)) {
            $Req_Shift_Typ_ID = $result_shift["shift_type_id"];
        } else {
            $Req_Shift_Typ_ID = "";
        }

        $resHalfMorningIntime = Search("select sh_half_slot_morning from shift_working_time_profile_settings where swtpsid = '" . $Req_Shift_Typ_ID . "'");
        if ($resultHalfMorningIntime = mysqli_fetch_assoc($resHalfMorningIntime)) {
            $Data = explode(" ", $resultHalfMorningIntime["sh_half_slot_morning"]);
            $INTIME = $Data[0];
        }
    } else {
        $resHalfMorningIntime = Search("select half_slot_morning from settings_working_times where update_user = '" . $UserID . "'");
        if ($resultHalfMorningIntime = mysqli_fetch_assoc($resHalfMorningIntime)) {
            $Data = explode(" ", $resultHalfMorningIntime["half_slot_morning"]);
            $INTIME = $Data[0];
        }
    }



    return $INTIME;
}

function GetHalfMorningOuttime($UserID, $DATE)
{
    $resu = Search("select work_typ from user where uid = '" . $UserID . "' and isactive='1'");
    if ($resultu = mysqli_fetch_assoc($resu)) {
        $Work_TYPE = $resultu["work_typ"];
    }

    if ($Work_TYPE == "2") {
        //get shift type id according to User ID
        $res_shift = Search("select shift_type_id from emp_shift_has_dates eshd, emp_working_shift ews where ews.ewsid = eshd.ewsid and ews.uid = '" . $UserID . "' and eshd.date = '" . $DATE . "'");
        if ($result_shift = mysqli_fetch_assoc($res_shift)) {
            $Req_Shift_Typ_ID = $result_shift["shift_type_id"];
        } else {
            $Req_Shift_Typ_ID = "";
        }

        $resHalfMorningOuttime = Search("select sh_half_slot_morning from shift_working_time_profile_settings where swtpsid = '" . $Req_Shift_Typ_ID . "'");
        if ($resultHalfMorningOuttime = mysqli_fetch_assoc($resHalfMorningOuttime)) {
            $Data = explode(" ", $resultHalfMorningOuttime["sh_half_slot_morning"]);
            $OUTTIME = $Data[3];
        }
    } else {
        $resHalfMorningOuttime = Search("select half_slot_morning from settings_working_times where update_user = '" . $UserID . "'");
        if ($resultHalfMorningOuttime = mysqli_fetch_assoc($resHalfMorningOuttime)) {
            $Data = explode(" ", $resultHalfMorningOuttime["half_slot_morning"]);
            $OUTTIME = $Data[3];
        }
    }

    return $OUTTIME;
}


function GetHalfEveningIntime($UserID, $DATE)
{
    $resu = Search("select work_typ from user where uid = '" . $UserID . "' and isactive='1'");
    if ($resultu = mysqli_fetch_assoc($resu)) {
        $Work_TYPE = $resultu["work_typ"];
    }

    if ($Work_TYPE == "2") {
        //get shift type id according to User ID
        $res_shift = Search("select shift_type_id from emp_shift_has_dates eshd, emp_working_shift ews where ews.ewsid = eshd.ewsid and ews.uid = '" . $UserID . "' and eshd.date = '" . $DATE . "'");
        if ($result_shift = mysqli_fetch_assoc($res_shift)) {
            $Req_Shift_Typ_ID = $result_shift["shift_type_id"];
        } else {
            $Req_Shift_Typ_ID = "";
        }

        $resHalfEveningIntime = Search("select sh_half_slot_evening from shift_working_time_profile_settings where swtpsid = '" . $Req_Shift_Typ_ID . "'");
        if ($resultHalfEveningIntime = mysqli_fetch_assoc($resHalfEveningIntime)) {
            $Data = explode(" ", $resultHalfEveningIntime["sh_half_slot_evening"]);
            $INTIME = $Data[0];
        }
    } else {
        $resHalfEveningIntime = Search("select half_slot_evening from settings_working_times where update_user = '" . $UserID . "'");
        if ($resultHalfEveningIntime = mysqli_fetch_assoc($resHalfEveningIntime)) {
            $Data = explode(" ", $resultHalfEveningIntime["half_slot_evening"]);
            $INTIME = $Data[0];
        }
    }

    return $INTIME;
}

function GetHalfEveningOuttime($UserID, $DATE)
{
    $resu = Search("select work_typ from user where uid = '" . $UserID . "' and isactive='1'");
    if ($resultu = mysqli_fetch_assoc($resu)) {
        $Work_TYPE = $resultu["work_typ"];
    }

    if ($Work_TYPE == "2") {
        //get shift type id according to User ID
        $res_shift = Search("select shift_type_id from emp_shift_has_dates eshd, emp_working_shift ews where ews.ewsid = eshd.ewsid and ews.uid = '" . $UserID . "' and eshd.date = '" . $DATE . "'");
        if ($result_shift = mysqli_fetch_assoc($res_shift)) {
            $Req_Shift_Typ_ID = $result_shift["shift_type_id"];
        } else {
            $Req_Shift_Typ_ID = "";
        }

        $resHalfEveningOuttime = Search("select sh_half_slot_evening from shift_working_time_profile_settings where swtpsid = '" . $Req_Shift_Typ_ID . "'");
        if ($resultHalfEveningOuttime = mysqli_fetch_assoc($resHalfEveningOuttime)) {
            $Data = explode(" ", $resultHalfEveningOuttime["sh_half_slot_evening"]);
            $OUTTIME = $Data[3];
        }
    } else {
        $resHalfEveningOuttime = Search("select half_slot_evening from settings_working_times where update_user = '" . $UserID . "'");
        if ($resultHalfEveningOuttime = mysqli_fetch_assoc($resHalfEveningOuttime)) {
            $Data = explode(" ", $resultHalfEveningOuttime["half_slot_evening"]);
            $OUTTIME = $Data[3];
        }
    }

    return $OUTTIME;
}


function GetShortMorningIntime($UserID, $DATE)
{
    $resu = Search("select work_typ from user where uid = '" . $UserID . "' and isactive='1'");
    if ($resultu = mysqli_fetch_assoc($resu)) {
        $Work_TYPE = $resultu["work_typ"];
    }

    if ($Work_TYPE == "2") {
        //get shift type id according to User ID
        $res_shift = Search("select shift_type_id from emp_shift_has_dates eshd, emp_working_shift ews where ews.ewsid = eshd.ewsid and ews.uid = '" . $UserID . "' and eshd.date = '" . $DATE . "'");
        if ($result_shift = mysqli_fetch_assoc($res_shift)) {
            $Req_Shift_Typ_ID = $result_shift["shift_type_id"];
        } else {
            $Req_Shift_Typ_ID = "";
        }

        $resShortMorningIntime = Search("select sh_short_morning from shift_working_time_profile_settings where swtpsid = '" . $Req_Shift_Typ_ID . "'");
        if ($resultShortMorningIntime = mysqli_fetch_assoc($resShortMorningIntime)) {
            $Data = explode(" ", $resultShortMorningIntime["sh_short_morning"]);
            $INTIME = $Data[0];
        }
    } else {
        $resShortMorningIntime = Search("select short_morning from settings_working_times where update_user = '" . $UserID . "'");
        if ($resultShortMorningIntime = mysqli_fetch_assoc($resShortMorningIntime)) {
            $Data = explode(" ", $resultShortMorningIntime["short_morning"]);
            $INTIME = $Data[0];
        }
    }

    return $INTIME;
}

function GetShortMorningOuttime($UserID, $DATE)
{
    $resu = Search("select work_typ from user where uid = '" . $UserID . "' and isactive='1'");
    if ($resultu = mysqli_fetch_assoc($resu)) {
        $Work_TYPE = $resultu["work_typ"];
    }

    if ($Work_TYPE == "2") {
        //get shift type id according to User ID
        $res_shift = Search("select shift_type_id from emp_shift_has_dates eshd, emp_working_shift ews where ews.ewsid = eshd.ewsid and ews.uid = '" . $UserID . "' and eshd.date = '" . $DATE . "'");
        if ($result_shift = mysqli_fetch_assoc($res_shift)) {
            $Req_Shift_Typ_ID = $result_shift["shift_type_id"];
        } else {
            $Req_Shift_Typ_ID = "";
        }

        $resShortMorningOuttime = Search("select sh_short_morning from shift_working_time_profile_settings where swtpsid = '" . $Req_Shift_Typ_ID . "'");
        if ($resultShortMorningOuttime = mysqli_fetch_assoc($resShortMorningOuttime)) {
            $Data = explode(" ", $resultShortMorningOuttime["sh_short_morning"]);
            $OUTTIME = $Data[3];
        }
    } else {
        $resShortMorningOuttime = Search("select short_morning from settings_working_times where update_user = '" . $UserID . "'");
        if ($resultShortMorningOuttime = mysqli_fetch_assoc($resShortMorningOuttime)) {
            $Data = explode(" ", $resultShortMorningOuttime["short_morning"]);
            $OUTTIME = $Data[3];
        }
    }

    return $OUTTIME;
}


function GetShortEveningIntime($UserID, $DATE)
{
    $resu = Search("select work_typ from user where uid = '" . $UserID . "' and isactive='1'");
    if ($resultu = mysqli_fetch_assoc($resu)) {
        $Work_TYPE = $resultu["work_typ"];
    }

    if ($Work_TYPE == "2") {
        //get shift type id according to User ID
        $res_shift = Search("select shift_type_id from emp_shift_has_dates eshd, emp_working_shift ews where ews.ewsid = eshd.ewsid and ews.uid = '" . $UserID . "' and eshd.date = '" . $DATE . "'");
        if ($result_shift = mysqli_fetch_assoc($res_shift)) {
            $Req_Shift_Typ_ID = $result_shift["shift_type_id"];
        } else {
            $Req_Shift_Typ_ID = "";
        }

        $resShortEveningIntime = Search("select sh_short_evening from shift_working_time_profile_settings where swtpsid = '" . $Req_Shift_Typ_ID . "'");
        if ($resultShortEveningIntime = mysqli_fetch_assoc($resShortEveningIntime)) {
            $Data = explode(" ", $resultShortEveningIntime["sh_short_evening"]);
            $INTIME = $Data[0];
        }
    } else {
        $resShortEveningIntime = Search("select short_evening from settings_working_times where update_user = '" . $UserID . "'");
        if ($resultShortEveningIntime = mysqli_fetch_assoc($resShortEveningIntime)) {
            $Data = explode(" ", $resultShortEveningIntime["short_evening"]);
            $INTIME = $Data[0];
        }
    }

    return $INTIME;
}

function GetShortEveningOuttime($UserID, $DATE)
{
    $resu = Search("select work_typ from user where uid = '" . $UserID . "' and isactive='1'");
    if ($resultu = mysqli_fetch_assoc($resu)) {
        $Work_TYPE = $resultu["work_typ"];
    }

    if ($Work_TYPE == "2") {
        //get shift type id according to User ID
        $res_shift = Search("select shift_type_id from emp_shift_has_dates eshd, emp_working_shift ews where ews.ewsid = eshd.ewsid and ews.uid = '" . $UserID . "' and eshd.date = '" . $DATE . "'");
        if ($result_shift = mysqli_fetch_assoc($res_shift)) {
            $Req_Shift_Typ_ID = $result_shift["shift_type_id"];
        } else {
            $Req_Shift_Typ_ID = "";
        }

        $resShortEveningOuttime = Search("select sh_short_evening from shift_working_time_profile_settings where swtpsid = '" . $Req_Shift_Typ_ID . "'");
        if ($resultShortEveningOuttime = mysqli_fetch_assoc($resShortEveningOuttime)) {
            $Data = explode(" ", $resultShortEveningOuttime["sh_short_evening"]);
            $OUTTIME = $Data[3];
        }
    } else {
        $resShortEveningOuttime = Search("select short_evening from settings_working_times where update_user = '" . $UserID . "'");
        if ($resultShortEveningOuttime = mysqli_fetch_assoc($resShortEveningOuttime)) {
            $Data = explode(" ", $resultShortEveningOuttime["short_evening"]);
            $OUTTIME = $Data[3];
        }
    }

    return $OUTTIME;
}
