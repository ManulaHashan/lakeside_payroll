<?php
error_reporting(0);
session_start();
date_default_timezone_set('Asia/Colombo');

include '../DB/DB.php';

$DB = new Database();

if (isset($_POST["submit"])) {
    if ($_POST["submit"] == "Update") {
        //upload csv file
        $target_dir = "../UploadFiles/";
        $target_file = $target_dir . basename($_FILES["attfile"]["name"]);

        $uploadOk = 1;
        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
        if (move_uploaded_file($_FILES["attfile"]["tmp_name"], $target_file)) {
            $uploadOk = 1;
            //update Attendance from uploaded file

            /**
                Convert excel file to csv
                */
                $excel_readers = array(
                    'Excel5' , 
                    'Excel2003XML' , 
                    'Excel2007'
                );
                require_once('../lib/PHPExcel/PHPExcel.php');
                $reader = PHPExcel_IOFactory::createReader('Excel2007');
                $reader->setReadDataOnly(true);
                $path = $target_file;
                $excel = $reader->load($path);
                $writer = PHPExcel_IOFactory::createWriter($excel, 'CSV');
                $writer->save('../UploadFiles/data.csv');

                $target_file = "../UploadFiles/data.csv";


                updateUserAttendanceFromRange($target_file);

            } else {
                $uploadOk = 0;
                echo "Error Uploading File! Please try again using proper internet connection.";
            }
        }
        if ($_POST["submit"] == "Enter Attendance") {
            $UID = $_POST['ceemp'];
            $Date = $_POST['cedate'];
            $Time = $_POST['ceintime'];

            if ($UID == "") 
            {
                header("Location: ../Views/emp_attendance.php?&state=Employee No is empty. Please add it first!");
            }
            else
            {
                $Status = "";
                $Action = "";
                $JobCode = "";

            //for Intime
                if($Time != ""){
                    $OutState = "C/In";
                    $Action = $OutState;
                    updateUserAttendanceRecord($UID, $Status, $Action, $JobCode, $Date, $Time, true);
                }


            // //for outtime 
                $Time = $_POST['ceouttime'];
                $Date = $_POST['cedateout'];
                
                if($Time != ""){
                    $OutState = "C/Out"; 
                    $Action = $OutState;  
                    updateUserAttendanceRecord($UID, $Status, $Action, $JobCode, $Date, $Time, true);
                }

                header("Location: ../Views/emp_attendance.php?&state=Attendance Updated!");
            }

        }

        if ($_POST["submit"] == "Enter Leave") {
            $UID = $_POST['leemp'];
            $Date = $_POST['ledate'];
            $type = $_POST['ltype'];
            $days = $_POST['noofdays'];
            $ts_Half = $_POST['tslot_Half'];
            $ts_short = $_POST['tslot_Short'];
            $Nopay_Data = $_POST['select_noofdays'];

            if ($Nopay_Data != "") 
            {
                $queryT = "select lid from employee_leave where uid = '" . $UID . "' and date= '".$Date."' and type = '".$type."'";
                    $rest = Search($queryT);
                if ($result = mysqli_fetch_assoc($rest)) 
                {

                    header("Location: ../Views/emp_attendance.php?&state=Leave Already Added!"); 

                }
                else
                {
                    $query = "insert into employee_leave(date,uid,type,days) values('" . $Date . "','" . $UID . "','" . $type . "','".$days."')"; 

                    $res = SUD($query);

                    header("Location: ../Views/emp_attendance.php?&state=Leave Updated!");
                }
            }
            else
            {
                if ($ts_Half == "" && $ts_short == "") 
                {
                    $queryT = "select lid from employee_leave where uid = '" . $UID . "' and date= '".$Date."' and type = '".$type."' and time_slot like '".$ts_short."'";
                        $rest = Search($queryT);
                    if ($result = mysqli_fetch_assoc($rest)) 
                    {

                        header("Location: ../Views/emp_attendance.php?&state=Leave Already Added!"); 

                    }
                    else
                    {
                        $query = "insert into employee_leave(date,uid,type,days,time_slot) values('" . $Date . "','" . $UID . "','" . $type . "','".$days."','')"; 

                        $res = SUD($query);

                        header("Location: ../Views/emp_attendance.php?&state=Leave Updated!");
                    } 
                }
                else
                {
                    if ($ts_Half != "") 
                    {
                        $queryT = "select lid from employee_leave where uid = '" . $UID . "' and date= '".$Date."' and type = '".$type."' and time_slot like '".$ts_Half."'";
                        $rest = Search($queryT);
                        if ($result = mysqli_fetch_assoc($rest)) 
                        {

                            header("Location: ../Views/emp_attendance.php?&state=Leave Already Added!"); 

                        }
                        else
                        {
                            $query = "insert into employee_leave(date,uid,type,days,time_slot) values('" . $Date . "','" . $UID . "','" . $type . "','".$days."','".$ts_Half."')"; 

                            $res = SUD($query);

                            header("Location: ../Views/emp_attendance.php?&state=Leave Updated!");
                        }
                    }
                    else if ($ts_short != "") 
                    {
                        $queryT = "select lid from employee_leave where uid = '" . $UID . "' and date= '".$Date."' and type = '".$type."' and time_slot like '".$ts_short."'";
                        $rest = Search($queryT);
                        if ($result = mysqli_fetch_assoc($rest)) 
                        {

                            header("Location: ../Views/emp_attendance.php?&state=Leave Already Added!"); 

                        }
                        else
                        {
                            $query = "insert into employee_leave(date,uid,type,days,time_slot) values('" . $Date . "','" . $UID . "','" . $type . "','".$days."','".$ts_short."')"; 

                            $res = SUD($query);

                            header("Location: ../Views/emp_attendance.php?&state=Leave Updated!");
                        }
                    } 
                }
            } 

        }
    }

    if (isset($_REQUEST["request"])) {
        $out = "";
        if ($_REQUEST["request"] == "getAttendance") {
            $out = "<table width='95%' class='table table-striped' style='color: black;'><thead class='thead-dark' style='background-color: #9eafba; position : sticky; top : 0; z-index: 0;'><tr><th>EMP No</th><th>Employee</th><th>Date</th><th>In Time</th><th>Out Time</th><th align='right'>Total Work Hours</th><th align='right'>OT Hours</th><th align='right'>Double OT Hours</th><th align='right'>Half Day</th><th align='right'>Short Leave</th> <th align='right'>Late Att. Min.</th><th></th></tr></thead>";

            $query = "select *,count(a.aid) as att, sum(a.othours) as ot, a.othours, sum(a.hours) as hours,sum(a.late_att_min) as latemins, b.fname,b.lname,b.uid from attendance a,user b, emppost c, position d where a.User_uid = b.uid and c.position_pid = d.pid and b.emppost_id = c.id and a.User_uid like '" . $_REQUEST["user"] . "' and a.date between '" . $_REQUEST["date"] . "' and '" . $_REQUEST["ldate"] . "' and d.pid like '".$_REQUEST["department"]."' group by aid order by date,length(b.jobcode),b.jobcode ASC";

            $att = 0;

            $othrs = 0;
            $otmints = 0;

            $dothrs = 0;
            $dotmins = 0;

            $hhrs = 0;
            $hmints = 0;

            $otc = 0;
            $lmt = 0;

            $TOT_OTH = 0;
            $TOT_OTM = 0;

            $TOT_DOTH = 0;
            $TOT_DOTM = 0;

            $TOT_WORKH = 0;
            $TOT_WORKM = 0;

            $attTbl = array();
            $UserTbl = array();

            $attDates = array();

            $query22 = "select uid from user where isactive='1' and uid!='2' order by length(jobcode),jobcode ASC"; //and uid!='10'
            $res22 = Search($query22);
            while ($result22 = mysqli_fetch_assoc($res22)) 
            {
                $UserTbl[] = $result22["uid"];
            }

            $res = Search($query);
            while ($result = mysqli_fetch_assoc($res)) {

                $Absent_Date = $result["date"];
                $attTbl[] = $result["User_uid"];

                if($result["othours"] == ""){
                    $oth = $result["othours"];
                }else{
                    $oth = number_format($result["othours"],2);

                    $othAR = explode(".", $oth);

                    $TOT_OTH += $othAR[0];
                    $TOT_OTM += $othAR[1];
                }

                if($result["dothours"] == ""){
                    $doth = $result["dothours"];
                }else{
                    $doth = number_format($result["dothours"],2);

                    $othARD = explode(".", $doth);

                    $TOT_DOTH += $othARD[0];
                    $TOT_DOTM += $othARD[1];
                }

                if($result["hours"] == ""){
                    $hours = $result["hours"];
                }else{
                    $hours = number_format($result["hours"],2);

                    $othARWork = explode(".", $hours);

                    $TOT_WORKH += $othARWork[0];
                    $TOT_WORKM += $othARWork[1];
                }

                $clean1 = array_diff($attTbl, $UserTbl); 
                $clean2 = array_diff($UserTbl, $attTbl); 
                $final_output = array_merge($clean1, $clean2);

                $queryleavereq = "select count(elrid) as reqleave from emp_leave_request where request_date between '" . $_REQUEST["date"] . "' and '" . $_REQUEST["ldate"] . "' and empid = '".$result["uid"]."' and app_status = 0"; //2023-07-17
                $resleavereq = Search($queryleavereq);
                if ($resultleave = mysqli_fetch_assoc($resleavereq)) 
                {
                    if ($resultleave["reqleave"] == 0) 
                    {
                        $out .= "<tr onclick='selectRecord(" . $result["aid"] . ")'><td>" . $result["jobcode"] . "</td><td>" . $result["fname"] . "</td><td>" . $result["date"] . "</td><td align=''>" . $result["intime"] . "</td><td align=''>" . $result["outtime"] . "</td><td align='center'>" . $hours . "</td><td align='center'>".$oth ."</td><td align='center'>" . $doth . "</td><td align='center'>" . $result["halfday"] . "</td><td align='center'>" . $result["shortleave"] . "</td><td align='center'>" . number_format($result["late_att_min"]) . "</td><td><img src='../Icons/remove.png' onclick='deleteRecord(".$result["aid"].",\"" . $result["date"] . "\")'></td></tr>";
                    }
                    else
                    {
                        $out .= "<tr style='background-color:#deba6d; color:black;' onclick='selectRecord(" . $result["aid"] . ")'><td>" . $result["jobcode"] . "</td><td>" . $result["fname"] . "</td><td>" . $result["date"] . "</td><td align=''>" . $result["intime"] . "</td><td align=''>" . $result["outtime"] . "</td><td align='center'>" . $hours . "</td><td align='center'>".$oth ."</td><td align='center'>" . $doth . "</td><td align='center'>" . $result["halfday"] . "</td><td align='center'>" . $result["shortleave"] . "</td><td align='center'>" . number_format($result["late_att_min"]) . "</td><td><img src='../Icons/remove.png' onclick='deleteRecord(".$result["aid"].",\"" . $result["date"] . "\")'></td></tr>";
                    }     
                }
               

                 
                $att = $att + $result["att"];

                $lmt += $result["latemins"];

                $attDates[] = $result["date"];

            }


            if ($_REQUEST["user"] != "%" && $_REQUEST["department"] == "%") 
            {
                $dates = array();
                $start = $current = strtotime($_REQUEST["date"]);
                $end = strtotime($_REQUEST["ldate"]);

                while ($current <= $end) {
                    $dates[] = date('Y-m-d', $current);
                    $current = strtotime('+1 days', $current);
                }


                $AAA = array_diff($attDates, $dates); 
                $BBB = array_diff($dates, $attDates); 
                $Date_output = array_merge($AAA, $BBB);

                

                foreach ($Date_output as $valuedd) 
                {

                    $resLeave = Search("SELECT type FROM employee_leave where uid='".$_REQUEST["user"]."' and date='".$valuedd."'");
                    if ($resultLEAVE = mysqli_fetch_assoc($resLeave)) 
                    {
                        $LEAVENAME = $resultLEAVE["type"];
                        $CELLCOLOR = "#EF9A9A";
                        $ABS_STAT = "";
                    }
                    else
                    {
                        $querp = "select name from poyadays where date = '".$valuedd."'";
                        $resp = Search($querp);
                        if ($resulp = mysqli_fetch_assoc($resp)) 
                        {
                            $LEAVENAME = $resulp["name"];
                            $CELLCOLOR = "#e6e600";
                            $ABS_STAT = "";
                        }
                        else
                        {
                            $DAYNAME = strtotime($valuedd);
                            $DAYNAME = date('l', $DAYNAME);
                            //if date is sunday 
                            if ($DAYNAME == "Sunday") 
                            {
                                $LEAVENAME = "Sunday";
                                $CELLCOLOR = "#00e64d";
                                $ABS_STAT = "";
                            }
                            else
                            {
                                //if date is Saturday 
                                if ($DAYNAME == "Saturday") 
                                {
                                    $LEAVENAME = "Saturday";
                                    $CELLCOLOR = "#EF9A9A";
                                    $ABS_STAT = "";
                                }
                                else
                                {
                                    $LEAVENAME = "-";
                                    $CELLCOLOR = "#EF9A9A";
                                    $ABS_STAT = "ABSENT";
                                }
                            }

                            
                        }
                    }


                    $out .= "<tr style='background-color:#EF9A9A; color:#000000;'><td>".$valuedd."</td><td style='background-color:".$CELLCOLOR."; color:#000000;'>".$LEAVENAME."</td><td></td><td>".$ABS_STAT."</td><td colspan='8'></td></tr>"; 
                    
                }
            }
            else
            {
                foreach ($final_output as $value) 
                {
                    
                    $resLeave = Search("SELECT type FROM employee_leave where uid='".$value."' and date='".$Absent_Date."'");
                    if ($resultLEAVE = mysqli_fetch_assoc($resLeave)) 
                    {
                        $LEAVENAME = $resultLEAVE["type"];
                        $CELLCOLOR = "#EF9A9A";
                        $ABS_STAT = "";
                    }
                    else
                    {
                        $querp = "select name from poyadays where date = '".$Absent_Date."'";
                        $resp = Search($querp);
                        if ($resulp = mysqli_fetch_assoc($resp)) 
                        {
                            $LEAVENAME = $resulp["name"];
                            $CELLCOLOR = "#e6e600";
                            $ABS_STAT = "";
                        }
                        else
                        {
                            $DAYNAME = strtotime($Absent_Date);
                            $DAYNAME = date('l', $DAYNAME);
                            //if date is sunday 
                            if ($DAYNAME == "Sunday") 
                            {
                                $LEAVENAME = "Sunday";
                                $CELLCOLOR = "#00e64d";
                                $ABS_STAT = "";
                            }
                            else
                            {
                                //if date is Saturday 
                                if ($DAYNAME == "Saturday") 
                                {
                                    $LEAVENAME = "Saturday";
                                    $CELLCOLOR = "#EF9A9A";
                                    $ABS_STAT = "";
                                }
                                else
                                {
                                    $LEAVENAME = "-";
                                    $CELLCOLOR = "#EF9A9A";
                                    $ABS_STAT = "ABSENT";
                                }
                            }

                            
                        }
                    }


                    $queryAbsent = "select epfno,jobcode,fname from user where isactive='1' and uid = '".$value."' order by length(jobcode),jobcode ASC"; //and uid!='10'
                    $resAbsent = Search($queryAbsent);
                    while ($resultAbsent = mysqli_fetch_assoc($resAbsent)) 
                    {
                        $out .= "<tr style='background-color:#EF9A9A; color:#000000;'><td>".$resultAbsent["jobcode"]."</td><td>".$resultAbsent["fname"]."</td><td>".$Absent_Date."</td><td>".$LEAVENAME."</td><td>".$ABS_STAT."</td><td colspan='7'></td></tr>";
                    } 
                    
                }

            }

            
       
            $queryhd = "select count(aid) as aid from attendance where User_uid like '" . $_REQUEST["user"] . "' and halfday='1' and date between '" . $_REQUEST["date"] . "' and '" . $_REQUEST["ldate"] . "'";

            $reshd = Search($queryhd);
            if ($resulthd = mysqli_fetch_assoc($reshd)) {
                $hd = $resulthd["aid"];
            }

            $querysl = "select count(aid) as aid from attendance where User_uid like '" . $_REQUEST["user"] . "' and shortleave='1' and date between '" . $_REQUEST["date"] . "' and '" . $_REQUEST["ldate"] . "'";

            $ressl = Search($querysl);
            if ($resultsl = mysqli_fetch_assoc($ressl)) {
                $sl = $resultsl["aid"];
            }

            $queryOT = "select sum(othours) as TotOT,sum(hours) as HRS,sum(hours) as HRS,sum(dothours) as DOTHRS  from attendance where User_uid like '" . $_REQUEST["user"] . "' and date between '" . $_REQUEST["date"] . "' and '" . $_REQUEST["ldate"] . "'";

            $resOT = Search($queryOT);
            if ($resultOT = mysqli_fetch_assoc($resOT)) {
                $othr = number_format($resultOT["TotOT"],2);
                $tothrs = number_format($resultOT["HRS"],2);                
                $doth = number_format($resultOT["DOTHRS"],2);
                
            }

            $lateMin = number_format($lmt,2);

            // //Calculate OT Value
            // $TOT_MinsOT = ($TOT_OTH*60) + $TOT_OTM;
            // $TOT_OTvalue = $TOT_MinsOT/60;

            // //Calculate DOT Value
            // $TOT_Mins_DOT = ($TOT_DOTH*60) + $TOT_DOTM;
            // $TOT_DOTvalue = $TOT_Mins_DOT/60;

            // //Calculate WORK Value
            // $TOT_Mins_WORK = ($TOT_WORKH*60) + $TOT_WORKM;
            // $TOT_WORKvalue = $TOT_Mins_WORK/60;

            // $out .= "</table>///" . $att . "#" . $TOT_OTvalue . "#" . $TOT_WORKvalue . "#" . $hd . "#" . $sl . "#".$TOT_DOTvalue. "#".$lateMin;

            //Calculate OT Value
            $TOT_MinsOT = ($TOT_OTH*60) + $TOT_OTM;
            $TOT_OTvalue = floor($TOT_MinsOT/60);
            $total_OTMin  = floor($TOT_MinsOT % 60);
            $DataOT = $TOT_OTvalue.".".$total_OTMin;

            //Calculate DOT Value
            $TOT_Mins_DOT = ($TOT_DOTH*60) + $TOT_DOTM;
            $TOT_DOTvalue = floor($TOT_Mins_DOT/60);
            $total_DOTMin  = floor($TOT_Mins_DOT % 60);
            $DataDOT = $TOT_DOTvalue.".".$total_DOTMin;

            //Calculate WORK Value
            $TOT_Mins_WORK = ($TOT_WORKH*60) + $TOT_WORKM;
            $TOT_WORKvalue = floor($TOT_Mins_WORK/60);
            $total_WRKMin  = floor($TOT_Mins_WORK % 60);
            $DataWORK = $TOT_WORKvalue.".".$total_WRKMin;

            $out .= "</table>///" . $att . "#" . $DataOT . "#" . $DataWORK . "#" . $hd . "#" . $sl . "#".$DataDOT. "#".$lateMin;

        }
        if ($_REQUEST["request"] == "approveleave") 
        {
            
           if ($_REQUEST["LeaveType"] == "Nopay Full Day Leave" || $_REQUEST["LeaveType"] == "Nopay Morning Leave" || $_REQUEST["LeaveType"] == "Nopay Evening Leave")
           {

                $resCHKLeave = Search("select lid from employee_leave where uid='".$_REQUEST["employeeID"]."' and date='".$_REQUEST["date"]."' and type='".$_REQUEST["LeaveType"]."'");
                if ($resultCHKLeave = mysqli_fetch_assoc($resCHKLeave)) 
                {
                    echo "2";
                }
                else
                {
                    $query = "insert into employee_leave(date,uid,type,days,reason) values('" . $_REQUEST["date"] . "','" . $_REQUEST["employeeID"] . "','" . $_REQUEST["LeaveType"] . "','".$_REQUEST["DaysValue"]."','".$_REQUEST["reason"]."')"; 

                    $res = SUD($query);

                    if ($res == 1) 
                    {
                       echo "1";
                    }
                    else
                    {
                       echo "0";
                    }
                }
                
           }
           else if ($_REQUEST["LeaveType"] == "Duty Full Day Leave" || $_REQUEST["LeaveType"] == "Duty Morning Leave" || $_REQUEST["LeaveType"] == "Duty Evening Leave")
           {
                $resCHKLeave = Search("select lid from employee_leave where uid='".$_REQUEST["employeeID"]."' and date='".$_REQUEST["date"]."' and type='".$_REQUEST["LeaveType"]."'");
                if ($resultCHKLeave = mysqli_fetch_assoc($resCHKLeave)) 
                {
                    echo "2";
                }
                else
                {
                    $query = "insert into employee_leave(date,uid,type,days,reason) values('" . $_REQUEST["date"] . "','" . $_REQUEST["employeeID"] . "','" . $_REQUEST["LeaveType"] . "','".$_REQUEST["DaysValue"]."','".$_REQUEST["reason"]."')"; 

                    $res = SUD($query);

                    if ($res == 1) 
                    {
                       echo "1";
                    }
                    else
                    {
                       echo "0";
                    }
                }
 
           }
           else if ($_REQUEST["LeaveType"] == "Maternity Leave")
           {
                $resCHKLeave = Search("select lid from employee_leave where uid='".$_REQUEST["employeeID"]."' and date='".$_REQUEST["date"]."' and type='".$_REQUEST["LeaveType"]."'");
                if ($resultCHKLeave = mysqli_fetch_assoc($resCHKLeave)) 
                {
                    echo "2";
                }
                else
                {
                    $query = "insert into employee_leave(date,uid,type,days,reason) values('" . $_REQUEST["date"] . "','" . $_REQUEST["employeeID"] . "','" . $_REQUEST["LeaveType"] . "','".$_REQUEST["DaysValue"]."','".$_REQUEST["reason"]."')"; 

                    $res = SUD($query);

                    if ($res == 1) 
                    {
                       echo "1";
                    }
                    else
                    {
                       echo "0";
                    }
                }
 
           }
           else if ($_REQUEST["LeaveType"] == "Parental Leave")
           {
                $resCHKLeave = Search("select lid from employee_leave where uid='".$_REQUEST["employeeID"]."' and date='".$_REQUEST["date"]."' and type='".$_REQUEST["LeaveType"]."'");
                if ($resultCHKLeave = mysqli_fetch_assoc($resCHKLeave)) 
                {
                    echo "2";
                }
                else
                {
                    $query = "insert into employee_leave(date,uid,type,days,reason) values('" . $_REQUEST["date"] . "','" . $_REQUEST["employeeID"] . "','" . $_REQUEST["LeaveType"] . "','".$_REQUEST["DaysValue"]."','".$_REQUEST["reason"]."')"; 

                    $res = SUD($query);

                    if ($res == 1) 
                    {
                       echo "1";
                    }
                    else
                    {
                       echo "0";
                    }
                }
 
           }
           else if ($_REQUEST["LeaveType"] == "Lieu Leave")
           {
                $resCHKLeave = Search("select lid from employee_leave where uid='".$_REQUEST["employeeID"]."' and date='".$_REQUEST["date"]."' and type='".$_REQUEST["LeaveType"]."'");
                if ($resultCHKLeave = mysqli_fetch_assoc($resCHKLeave)) 
                {
                    echo "2";
                }
                else
                {
                    $query = "insert into employee_leave(date,uid,type,days,reason) values('" . $_REQUEST["date"] . "','" . $_REQUEST["employeeID"] . "','" . $_REQUEST["LeaveType"] . "','".$_REQUEST["DaysValue"]."','".$_REQUEST["reason"]."')"; 

                    $res = SUD($query);

                    if ($res == 1) 
                    {
                       echo "1";
                    }
                    else
                    {
                       echo "0";
                    }
                }
 
           }
           else if ($_REQUEST["LeaveType"] == "Halfday Morning Leave" || $_REQUEST["LeaveType"] == "Halfday Evening Leave")
           {

                $resCHKLeave = Search("select lid from employee_leave where uid='".$_REQUEST["employeeID"]."' and date='".$_REQUEST["date"]."' and type='".$_REQUEST["LeaveType"]."'");
                if ($resultCHKLeave = mysqli_fetch_assoc($resCHKLeave)) 
                {
                    echo "2";
                }
                else
                {
                    $query = "insert into employee_leave(date,uid,type,days,reason) values('" . $_REQUEST["date"] . "','" . $_REQUEST["employeeID"] . "','" . $_REQUEST["LeaveType"] . "','".$_REQUEST["DaysValue"]."','".$_REQUEST["reason"]."')"; 

                    $res = SUD($query);

                    if ($res == 1) 
                    {
                        $resCHKPreviouseHalf = Search("select pfylid,total_leave from total_leave_data where uid='".$_REQUEST["employeeID"]."'");
                        if ($resultCHKPreviouseHalf = mysqli_fetch_assoc($resCHKPreviouseHalf)) 
                        {
                            $Total_Value = $resultCHKPreviouseHalf["total_leave"] - $_REQUEST["DaysValue"];

                            $querysUpdate = "Update total_leave_data set total_leave='".$Total_Value."' where pfylid='" . $resultCHKPreviouseHalf["pfylid"] . "'";
                            $ret = SUD($querysUpdate);
                        }
                         
                       echo "1";
                    }
                    else
                    {
                       echo "0";
                    }
                }
    
           }
           else if ($_REQUEST["LeaveType"] == "Short Morning Leave" || $_REQUEST["LeaveType"] == "Short Evening Leave")
           {
                
                $resCHKLeave = Search("select lid from employee_leave where uid='".$_REQUEST["employeeID"]."' and date='".$_REQUEST["date"]."' and type='".$_REQUEST["LeaveType"]."'");
                if ($resultCHKLeave = mysqli_fetch_assoc($resCHKLeave)) 
                {
                    echo "2";
                }
                else
                {
                    $query = "insert into employee_leave(date,uid,type,days,reason) values('" . $_REQUEST["date"] . "','" . $_REQUEST["employeeID"] . "','" . $_REQUEST["LeaveType"] . "','".$_REQUEST["DaysValue"]."','".$_REQUEST["reason"]."')"; 

                    $res = SUD($query);

                    if ($res == 1) 
                    {
                       echo "1";
                    }
                    else
                    {
                       echo "0";
                    }
                }
                
           }
           else if ($_REQUEST["LeaveType"] == "Casual Leave")
           {
                
                $resCHKLeave = Search("select lid from employee_leave where uid='".$_REQUEST["employeeID"]."' and date='".$_REQUEST["date"]."' and type='".$_REQUEST["LeaveType"]."'");
                if ($resultCHKLeave = mysqli_fetch_assoc($resCHKLeave)) 
                {
                    echo "2";
                }
                else
                {
                    $query = "insert into employee_leave(date,uid,type,days,reason) values('" . $_REQUEST["date"] . "','" . $_REQUEST["employeeID"] . "','" . $_REQUEST["LeaveType"] . "','".$_REQUEST["DaysValue"]."','".$_REQUEST["reason"]."')"; 

                    $res = SUD($query);

                    if ($res == 1) 
                    { 
                        echo "1";
                    }
                    else
                    {
                        echo "0";
                    }
                }  
           }
           else if ($_REQUEST["LeaveType"] == "Annual Leave")
           {
                
                $resCHKLeave = Search("select lid from employee_leave where uid='".$_REQUEST["employeeID"]."' and date='".$_REQUEST["date"]."' and type='".$_REQUEST["LeaveType"]."'");
                if ($resultCHKLeave = mysqli_fetch_assoc($resCHKLeave)) 
                {
                    echo "2";
                }
                else
                {
                    $query = "insert into employee_leave(date,uid,type,days,reason) values('" . $_REQUEST["date"] . "','" . $_REQUEST["employeeID"] . "','" . $_REQUEST["LeaveType"] . "','".$_REQUEST["DaysValue"]."','".$_REQUEST["reason"]."')"; 

                    $res = SUD($query);

                    if ($res == 1) 
                    { 
                        echo "1";
                    }
                    else
                    {
                        echo "0";
                    }
                }  
           }
           else if ($_REQUEST["LeaveType"] == "Medical Leave")
           {
                
                $resCHKLeave = Search("select lid from employee_leave where uid='".$_REQUEST["employeeID"]."' and date='".$_REQUEST["date"]."' and type='".$_REQUEST["LeaveType"]."'");
                if ($resultCHKLeave = mysqli_fetch_assoc($resCHKLeave)) 
                {
                    echo "2";
                }
                else
                {
                    $query = "insert into employee_leave(date,uid,type,days,reason) values('" . $_REQUEST["date"] . "','" . $_REQUEST["employeeID"] . "','" . $_REQUEST["LeaveType"] . "','".$_REQUEST["DaysValue"]."','".$_REQUEST["reason"]."')"; 

                    $res = SUD($query);

                    if ($res == 1) 
                    { 
                        echo "1";
                    }
                    else
                    {
                        echo "0";
                    }
                }  
           }

        }

        if ($_REQUEST["request"] == "getEmpWiseHalfSlots") 
        {
            $res = Search("select work_typ from user where uid = '" . $_REQUEST["empid"] . "'");
            if ($result = mysqli_fetch_assoc($res)) 
            {
                 $work_type = $result["work_typ"];   
            }

            if ($work_type == "2") 
            {
                $res_sid = Search("select eshd.shift_type_id from emp_shift_has_dates eshd, emp_working_shift ews where ews.ewsid = eshd.ewsid and ews.uid = '".$_REQUEST["empid"]."' and eshd.date = '".$_REQUEST["date"]."'");
                if ($result_sid = mysqli_fetch_assoc($res_sid)) 
                {
                    $Shift_ID = $result_sid["shift_type_id"];   
                }
                else
                {
                    $Shift_ID = 0;
                }

                $res_shift = Search("select sh_half_slot_morning, sh_half_slot_evening from shift_working_time_profile_settings where swtpsid='".$Shift_ID."'");
                if ($result_data_shift = mysqli_fetch_assoc($res_shift)) 
                {
                    echo "<option value='".ltrim($result_data_shift["sh_half_slot_morning"], "0")."'>" .ltrim($result_data_shift["sh_half_slot_morning"], "0"). "</option><option value='".ltrim($result_data_shift["sh_half_slot_evening"], "0")."'>" .ltrim($result_data_shift["sh_half_slot_evening"], "0"). "</option>";     
                }   
            }
            else
            {
                $res_data = Search("select half_slot_morning, half_slot_evening from settings_working_times where isactive='1' and update_user='".$_REQUEST["empid"]."'");
                if ($result_data = mysqli_fetch_assoc($res_data)) 
                {
                    echo "<option value='".ltrim($result_data["half_slot_morning"], "0")."'>" .ltrim($result_data["half_slot_morning"], "0"). "</option><option value='".ltrim($result_data["half_slot_evening"], "0")."'>" .ltrim($result_data["half_slot_evening"], "0"). "</option>";     
                }
            }

        }


        if ($_REQUEST["request"] == "getEmpWiseShortSlots") 
        {
            $res = Search("select work_typ from user where uid = '" . $_REQUEST["empid"] . "'");
            if ($result = mysqli_fetch_assoc($res)) 
            {
                 $work_type = $result["work_typ"];   
            }

            if ($work_type == "2") 
            {
                $res_sid = Search("select eshd.shift_type_id from emp_shift_has_dates eshd, emp_working_shift ews where ews.ewsid = eshd.ewsid and ews.uid = '".$_REQUEST["empid"]."' and eshd.date = '".$_REQUEST["date"]."'");
                if ($result_sid = mysqli_fetch_assoc($res_sid)) 
                {
                    $Shift_ID = $result_sid["shift_type_id"];   
                }
                else
                {
                    $Shift_ID = 0;
                }

                $res_shift = Search("select sh_short_morning, sh_short_evening  from shift_working_time_profile_settings where swtpsid='".$Shift_ID."'");
                if ($result_data_shift = mysqli_fetch_assoc($res_shift)) 
                {
                     echo "<option value='".ltrim($result_data_shift["sh_short_morning"], "0")."'>" .ltrim($result_data_shift["sh_short_morning"], "0"). "</option><option value='".ltrim($result_data_shift["sh_short_evening"], "0")."'>" .ltrim($result_data_shift["sh_short_evening"], "0"). "</option>";      
                }   
            }
            else
            {
                $res_data = Search("select short_morning, short_evening  from settings_working_times where isactive='1' and update_user='".$_REQUEST["empid"]."'");
                if ($result_data = mysqli_fetch_assoc($res_data)) 
                {
                     echo "<option value='".ltrim($result_data["short_morning"], "0")."'>" .ltrim($result_data["short_morning"], "0"). "</option><option value='".ltrim($result_data["short_evening"], "0")."'>" .ltrim($result_data["short_evening"], "0"). "</option>";    
                }
            }
        }

        if ($_REQUEST["request"] == "selectRecord") 
        {
            $query = "select * from attendance where aid = '" . $_REQUEST["aid"] . "'";
            $res = Search($query);
            if ($result = mysqli_fetch_assoc($res)) {
                $out = $result["intime"] . "#" . $result["outtime"] . "#" . $result["otin"] . "#" . $result["otout"] . "#" . $result["shortleave"] . "#" . $result["halfday"] . "#" . $result["hours"] . "#" . $result["othours"]. "#" . $result["dothours"]. "#" . $result["late_att_min"];
            }
        }
        if ($_REQUEST["request"] == "AutoGenRecord") 
        {
            $ATTID = $_REQUEST["aid"];

            $queryAuto = "select date,User_uid,intime,outtime,halfday,shortleave,late_att_min from attendance where aid = '" . $ATTID . "'";
            $resAuto = Search($queryAuto);
            if ($resultAuto = mysqli_fetch_assoc($resAuto)) 
            {
                
                
                $Uid = $resultAuto["User_uid"];
                $UID = preg_replace("/[^0-9]/", "", $Uid);
                $TimeA = $_REQUEST["intime"];
                $TimeB = $_REQUEST["outtime"];
                $Date = $resultAuto["date"];
                $IS_HALFDAY = $resultAuto["halfday"];
                $IS_SHORTLEAVE = $resultAuto["shortleave"];
                $IS_LATE = $resultAuto["late_att_min"];


                $time_in_24_hour_formatA  = date("H:i:s", strtotime($TimeA));
                $Time1 = $time_in_24_hour_formatA;

                $time_in_24_hour_formatB  = date("H:i:s", strtotime($TimeB));
                $Time2 = $time_in_24_hour_formatB;

                //$UserID = 0;
                $queryu = "select uid,work_typ from user where uid = '" . $UID . "' and isactive='1'";
                $resu = Search($queryu);
                if ($resultu = mysqli_fetch_assoc($resu)) 
                {
                    $UserID = $resultu["uid"];
                    $Work_TYPE = $resultu["work_typ"];
                }

                $x = strtotime($Date);
                $x = date('l', $x);

                //if date is Saturday 
                $saturday = false;
                if ($x == "Saturday") {
                    $saturday = true;
                }

                $static_morning_OT = 0;
                $MOThours = 0;


                // New system of saving attendance depends on input flag ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                

                if ($Work_TYPE == "2") 
                {
                    //===================SHIFT STAFF================================================

                    //get shift type id according to User ID
                    $res_shift = Search("select espid from emp_has_shift where user_uid = '".$UserID."' and date = '".$Date."'");
                    if ($result_shift = mysqli_fetch_assoc($res_shift)) 
                    {
                        $Req_Shift_Typ_ID = $result_shift["espid"];
                    }
                    else
                    {
                        $Req_Shift_Typ_ID = "";
                    }


                    if ($IS_HALFDAY == "1") 
                    {
                        if (strtotime($Time1) > strtotime(GetShiftLate($Req_Shift_Typ_ID)))     
                        {
                            $lateMints = (strtotime($Time1) - strtotime(GetShiftIntime($Req_Shift_Typ_ID)))/60;
                        }
                        else
                        {
                            $lateMints = 0;
                        }

                        $halfDay = 1;
                        $shortLeave = 0;

                    }
                    else if ($IS_SHORTLEAVE == "1") 
                    {
                        if (strtotime($Time1) > strtotime(GetShiftLate($Req_Shift_Typ_ID)))     
                        {
                            $lateMints = (strtotime($Time1) - strtotime(GetShiftIntime($Req_Shift_Typ_ID)))/60;
                        }
                        else
                        {
                            $lateMints = 0;
                        }

                        $halfDay = 0;
                        $shortLeave = 1;
                    }
                    else
                    {
                        $LeaveType = "";
                        $Slot = "";
                        $halfDay = "";
                        $shortLeave="";
                        $queryHalfShortCheck = "select type,time_slot,days from employee_leave where date = '" . $Date . "' and uid = '" . $UserID . "' and (type like 'Halfday Morning Leave' or type like 'Short Morning Leave' or type like 'Nopay Morning Leave' or type like 'Duty Morning Leave') and is_att_leave = '0'";
                        $resHalfShortCheck = Search($queryHalfShortCheck);
                        if ($resultHalfShortCheck = mysqli_fetch_assoc($resHalfShortCheck)) 
                        {
                            $LeaveType =  $resultHalfShortCheck["type"];
                            $Slot =  $resultHalfShortCheck["time_slot"];
                            $DAYS =  $resultHalfShortCheck["days"];

                            if ($LeaveType == "Halfday Morning Leave") 
                            {
                                if (strtotime($Time1) > strtotime(GetShiftHalfMLate($Req_Shift_Typ_ID))) 
                                {
                                    $lateMints = (strtotime($Time1) - strtotime(GetShiftHalfMLate($Req_Shift_Typ_ID)))/60;
                                }
                                else
                                {
                                    $lateMints = 0;
                                }

                                $halfDay = 1;
                                $shortLeave = 0;
                            }
                            else if ($LeaveType == "Nopay Morning Leave" || $LeaveType == "Duty Morning Leave") 
                            {
                                if (strtotime($Time1) > strtotime(GetShiftHalfMLate($Req_Shift_Typ_ID))) 
                                {
                                    $lateMints = (strtotime($Time1) - strtotime(GetShiftHalfMLate($Req_Shift_Typ_ID)))/60;
                                }
                                else
                                {
                                    $lateMints = 0;
                                }

                                $halfDay = 0;
                                $shortLeave = 0;
                            }
                            else if ($LeaveType == "Short Morning Leave") 
                            {
                                if (strtotime($Time1) > strtotime(GetShiftShortMLate($Req_Shift_Typ_ID))) 
                                {
                                    $lateMints = (strtotime($Time1) - strtotime(GetShiftShortMLate($Req_Shift_Typ_ID)))/60;
                                }
                                else
                                {
                                    $lateMints = 0;
                                }
                                
                                $halfDay = 0;
                                $shortLeave = 1;       
                            }
                            else
                            {
                                if (strtotime($Time1) > strtotime(GetShiftLate($Req_Shift_Typ_ID)))     
                                {
                                    $lateMints = (strtotime($Time1) - strtotime(GetShiftIntime($Req_Shift_Typ_ID)))/60;
                                }
                                else
                                {
                                    $lateMints = 0;
                                }

                                $halfDay = 0;
                                $shortLeave = 0;
                            }   
                        }
                        else
                        {
                            if (strtotime($Time1) > strtotime(GetShiftLate($Req_Shift_Typ_ID)))     
                            {
                                $lateMints = (strtotime($Time1) - strtotime(GetShiftIntime($Req_Shift_Typ_ID)))/60;
                            }
                            else
                            {
                                $lateMints = 0;
                            }

                            $halfDay = 0;
                            $shortLeave = 0;
                        }
                    }
                       
                    

                    $static_morning_OT = $MOThours;
                    


                    $OUTTIME = date("A", strtotime($Time2));
                
                    if ($OUTTIME == "AM") 
                    {

                        if ($Time2 >= date("H:i:s", strtotime("12:00 AM")) && $Time2 < date("H:i:s", strtotime(GetShiftIntime($Req_Shift_Typ_ID)))) 
                        {
                            // echo "<br/> Special Case ~~~~~~~~~~~~~~~~~~~";
                            //check earlier date checked out exists (for OT and DOT calculation)

                            //get earlier date
                            $ydate = strtotime($Date);
                            $ydate = strtotime("+1 day", $ydate);
                            $ydate = date('y-m-d', $ydate);


                            $query = "select aid,intime,outtime,late_att_min,halfday, shortleave from attendance where date = '" . $Date . "' and User_uid = '" . $UserID . "'";
                            $res = Search($query);
                            if ($result = mysqli_fetch_assoc($res)) 
                            {
                                
                                $earlyDateIn = date("H:i:s", strtotime(GetShiftIntime($Req_Shift_Typ_ID)));
                                $Late_min_DATA = $lateMints;
                                $halfDays = $halfDay;
                                $shortLeaves = $shortLeave;

                                $hours = getTimeDifference($Date, $earlyDateIn, $ydate, $Time2);
                                
                                $timeIN24HR = date("H:i:s", strtotime($Time2));
                                $T130Aftn = date("H:i:s", strtotime(GetShiftHalfMorningOuttime($Req_Shift_Typ_ID)));
                                // if intime not between 8.30 and 1.30 no deduction fro lunch hour
                                if($earlyDateIn < $timeIN24HR && $T130Aftn > $timeIN24HR)
                                {
                                 
                                }
                                else
                                {
                                    $hourd_pre_ded_intavels = $hours;
                                    $hours = $hours - 0.5; //Lunch Time Duration
                                }

                                $hours = number_format(floatval($hours),2);
                                
                                $Att_DATE = explode('-', $Date);
                                $check_Tot_WORK = Search("select sum(hours) as Total_WORK_HRS from attendance where YEAR(date) = '" . $Att_DATE[0] . "' and MONTH(date) = '" . $Att_DATE[1] . "' and User_uid = '" . $UserID . "'");
                                if ($results_tot_work = mysqli_fetch_assoc($check_Tot_WORK)) 
                                {
                                    $TOTAL_WORK_HRS = $results_tot_work["Total_WORK_HRS"];
                                }

                                $othAR = explode(".", number_format($TOTAL_WORK_HRS,2));

                                $TOT_OTH = $othAR[0];
                                $TOT_OTM = $othAR[1];
                                //Calculate OT Value
                                $TOT_MinsOT = ($TOT_OTH*60) + $TOT_OTM;
                                $TOT_WORKvalue = floor($TOT_MinsOT/60);

                                $T5Eve = date("H:i:s", strtotime(GetShiftOuttime($Req_Shift_Typ_ID))); 
                                if ($TOT_WORKvalue > 200) 
                                {
                                    $OThours = getTimeDifference($Date, $T5Eve, $ydate, $Time2);
                                    $OThours = number_format(floatval($OThours),2);
                                }
                                else
                                {
                                    $OTHours = 0;
                                }

                                echo $hours."###".$OThours."###".$Late_min_DATA."###".$halfDays."###".$shortLeaves."###".$DOThours."###".$MOThours;
                                      
                               
                            }

                            
                        }
                        else
                        {
                            $T8Mng = date("H:i:s", strtotime(GetShiftIntime($Req_Shift_Typ_ID)));
                            $hours = getTimeDifference($Date, $T8Mng, $Date, $Time2);
                            $static_morning_OT = $MOThours;
                            $Late_MIN_DATA = $lateMints;
                            $timeIN24HR = date("H:i:s", strtotime($Time2));
                            $T5Eve = date("H:i:s", strtotime(GetShiftOuttime($Req_Shift_Typ_ID)));

                            $T130Aftn = date("H:i:s", strtotime(GetShiftHalfMorningOuttime($Req_Shift_Typ_ID)));
                            // if intime not between 8.30 and 1.30 no deduction fro lunch hour
                            if($T8Mng < $timeIN24HR && $T130Aftn > $timeIN24HR)
                            {
                             
                            }
                            else
                            {
                                $hourd_pre_ded_intavels = $hours;
                                $hours = $hours - 0.5; //Lunch Time Duration
                            }

                            $hours = number_format(floatval($hours),2);

                            $Att_DATE = explode('-', $Date);
                            $check_Tot_WORK = Search("select sum(hours) as Total_WORK_HRS from attendance where YEAR(date) = '" . $Att_DATE[0] . "' and MONTH(date) = '" . $Att_DATE[1] . "' and User_uid = '" . $UserID . "'");
                            if ($results_tot_work = mysqli_fetch_assoc($check_Tot_WORK)) 
                            {
                                $TOTAL_WORK_HRS = $results_tot_work["Total_WORK_HRS"];
                            }

                            $othAR = explode(".", number_format($TOTAL_WORK_HRS,2));

                            $TOT_OTH = $othAR[0];
                            $TOT_OTM = $othAR[1];
                            //Calculate OT Value
                            $TOT_MinsOT = ($TOT_OTH*60) + $TOT_OTM;
                            $TOT_WORKvalue = floor($TOT_MinsOT/60);

                            if ($TOT_WORKvalue > 200) 
                            {
                                if($T5Eve < $timeIN24HR){
                                    $OTfromPeriod = getTimeDifference($Date, $T5Eve, $Date, $Time2);
                                    $OTfromPeriod = number_format(floatval($OTfromPeriod),2);
                                }
                            }
                            else
                            {
                                $OTfromPeriod = 0;
                            }

                            $DOTHORS = "";                            
                            $lateMintsData = 0;
                            $OT_to_save = $static_morning_OT + $OTfromPeriod;
                            $New_Late_Min = $Late_MIN_DATA + $lateMintsData;

                            echo $hours."###".$OT_to_save."###".$New_Late_Min."###".$halfDay."###".$shortLeave."###".$DOTHORS."###".$MOThours;
                            
                        }
                        
                    }
                    else
                    {   
                        $T8Mng = date("H:i:s", strtotime(GetShiftIntime($Req_Shift_Typ_ID)));
                        $hours = getTimeDifference($Date, $T8Mng, $Date, $Time2);
                        $static_morning_OT = $MOThours;
                        $Late_MIN_DATA = $lateMints;
                        $timeIN24HR = date("H:i:s", strtotime($Time2));
                        $T5Eve = date("H:i:s", strtotime(GetShiftOuttime($Req_Shift_Typ_ID))); 

                        $T130Aftn = date("H:i:s", strtotime(GetShiftHalfMorningOuttime($Req_Shift_Typ_ID)));
                        // if intime not between 8.30 and 1.30 no deduction fro lunch hour
                        if($T8Mng < $timeIN24HR && $T130Aftn > $timeIN24HR)
                        {
                         
                        }
                        else
                        {
                            $hourd_pre_ded_intavels = $hours;
                            $hours = $hours - 0.5; //Lunch Time Duration
                        }

                        $hours = number_format(floatval($hours),2);

                        $Att_DATE = explode('-', $Date);
                        $check_Tot_WORK = Search("select sum(hours) as Total_WORK_HRS from attendance where YEAR(date) = '" . $Att_DATE[0] . "' and MONTH(date) = '" . $Att_DATE[1] . "' and User_uid = '" . $UserID . "'");
                        if ($results_tot_work = mysqli_fetch_assoc($check_Tot_WORK)) 
                        {
                            $TOTAL_WORK_HRS = $results_tot_work["Total_WORK_HRS"];
                        }

                        $othAR = explode(".", number_format($TOTAL_WORK_HRS,2));

                        $TOT_OTH = $othAR[0];
                        $TOT_OTM = $othAR[1];
                        //Calculate OT Value
                        $TOT_MinsOT = ($TOT_OTH*60) + $TOT_OTM;
                        $TOT_WORKvalue = floor($TOT_MinsOT/60);
                        
                        if ($TOT_WORKvalue > 200) 
                        {
                            if($T5Eve < $timeIN24HR){
                                $OTfromPeriod = getTimeDifference($Date, $T5Eve, $Date, $Time);
                                $OTfromPeriod = number_format(floatval($OTfromPeriod),2);
                            }
                        }
                        else
                        {
                            $OTfromPeriod = 0;
                        }

                        $DOTHORS = "";                        
                        $lateMintsData = 0;
                        $OT_to_save = $static_morning_OT + $OTfromPeriod;
                        $New_Late_Min = $Late_MIN_DATA + $lateMintsData;

                        echo $hours."###".$OT_to_save."###".$New_Late_Min."###".$halfDay."###".$shortLeave."###".$DOTHORS."###".$MOThours;
                        
                    }

                }
                else
                {

                   //===================OFFICE STAFF================================================

                    if ($IS_HALFDAY == "1") 
                    {
                        if (strtotime($Time1) > strtotime(GetWorkingWeekLate($UserID)))     
                        {
                            $lateMints = (strtotime($Time1) - strtotime(GetWorkingIntimeWeek($UserID)))/60;
                        }
                        else
                        {
                            $lateMints = 0;
                        }

                        $halfDay = 1;
                        $shortLeave = 0;
                    }
                    else if ($IS_SHORTLEAVE == "1") 
                    {
                        if (strtotime($Time1) > strtotime(GetWorkingWeekLate($UserID)))     
                        {
                            $lateMints = (strtotime($Time1) - strtotime(GetWorkingIntimeWeek($UserID)))/60;
                        }
                        else
                        {
                            $lateMints = 0;
                        }

                        $halfDay = 0;
                        $shortLeave = 1;
                    }
                    else
                    {
                        $LeaveType = "";
                        $Slot = "";
                        $halfDay = "";
                        $shortLeave="";
                        $queryHalfShortCheck = "select type,time_slot,days from employee_leave where date = '" . $Date . "' and uid = '" . $UserID . "' and (type like 'Halfday Morning Leave' or type like 'Short Morning Leave' or type like 'Nopay Morning Leave' or type like 'Duty Morning Leave') and is_att_leave = '0'";
                        $resHalfShortCheck = Search($queryHalfShortCheck);
                        if ($resultHalfShortCheck = mysqli_fetch_assoc($resHalfShortCheck)) 
                        {
                            $LeaveType =  $resultHalfShortCheck["type"];
                            $Slot =  $resultHalfShortCheck["time_slot"];
                            $DAYS =  $resultHalfShortCheck["days"];

                            if ($LeaveType == "Halfday Morning Leave") 
                            {
                                if (strtotime($Time1) > strtotime(GetHalfMLate($UserID))) 
                                {
                                    $lateMints = (strtotime($Time1) - strtotime(GetHalfMLate($UserID)))/60;
                                }
                                else
                                {
                                    $lateMints = 0;
                                }

                                $halfDay = 1;
                                $shortLeave = 0;
                            }
                            else if ($LeaveType == "Nopay Morning Leave" || $LeaveType == "Duty Morning Leave") 
                            {
                                if (strtotime($Time1) > strtotime(GetHalfMLate($UserID))) 
                                {
                                    $lateMints = (strtotime($Time1) - strtotime(GetHalfMLate($UserID)))/60;
                                }
                                else
                                {
                                    $lateMints = 0;
                                }

                                $halfDay = 0;
                                $shortLeave = 0;
                            }
                            else if ($LeaveType == "Short Morning Leave") 
                            {
                                if (strtotime($Time1) > strtotime(GetShortMLate($UserID))) 
                                {
                                    $lateMints = (strtotime($Time1) - strtotime(GetShortMLate($UserID)))/60;
                                }
                                else
                                {
                                    $lateMints = 0;
                                }

                                $halfDay = 0;
                                $shortLeave = 1;  
                            }
                            else
                            {
                                if (strtotime($Time1) > strtotime(GetWorkingWeekLate($UserID)))     
                                {
                                    $lateMints = (strtotime($Time1) - strtotime(GetWorkingIntimeWeek($UserID)))/60;
                                }
                                else
                                {
                                    $lateMints = 0;                     
                                }

                                $halfDay = 0;
                                $shortLeave = 0;
                            }   
                        }
                        else
                        {
                            if (strtotime($Time1) > strtotime(GetWorkingWeekLate($UserID)))     
                            {
                                $lateMints = (strtotime($Time1) - strtotime(GetWorkingIntimeWeek($UserID)))/60;
                            }
                            else
                            {
                                $lateMints = 0;                     
                            }

                            $halfDay = 0;
                            $shortLeave = 0;
                        }
                    }
                       
                    

                    $static_morning_OT = $MOThours;
                    


                    $OUTTIME = date("A", strtotime($Time2));
                
                    if ($OUTTIME == "AM") 
                    {

                        if ($Time2 >= date("H:i:s", strtotime("12:00 AM")) && $Time2 < date("H:i:s", strtotime(GetWorkingIntimeWeek($UserID)))) 
                        {
                            // echo "<br/> Special Case ~~~~~~~~~~~~~~~~~~~";
                            //check earlier date checked out exists (for OT and DOT calculation)

                            //get earlier date
                            $ydate = strtotime($Date);
                            $ydate = strtotime("+1 day", $ydate);
                            $ydate = date('y-m-d', $ydate);


                            $query = "select aid,intime,outtime,late_att_min,halfday, shortleave from attendance where date = '" . $Date . "' and User_uid = '" . $UserID . "'";
                            $res = Search($query);
                            if ($result = mysqli_fetch_assoc($res)) {


                                // new saving ~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                                $earlyDateIn = date("H:i:s", strtotime(GetWorkingIntimeWeek($UserID)));
                                // $earlyDateIn = date("H:i:s", strtotime("08:30 AM"));
                                $Late_min_DATA = $lateMints;
                                $halfDays = $halfDay;
                                $shortLeaves = $shortLeave;

                                $hours = getTimeDifference($Date, $earlyDateIn, $ydate, $Time2);
                                
                                $timeIN24HR = date("H:i:s", strtotime($Time2));
                                $T130Aftn = date("H:i:s", strtotime(GetHalfMorningOuttime($UserID)));
                                // if intime not between 8.30 and 1.30 no deduction fro lunch hour
                                if($earlyDateIn < $timeIN24HR && $T130Aftn > $timeIN24HR)
                                {
                                 
                                }
                                else
                                {
                                    $hourd_pre_ded_intavels = $hours;
                                    $hours = $hours - 0.5; //Lunch Time Duration
                                }

                                $hours = number_format(floatval($hours),2);
                                
                                $Att_DATE = explode('-', $Date);
                                $check_Tot_WORK = Search("select sum(hours) as Total_WORK_HRS from attendance where YEAR(date) = '" . $Att_DATE[0] . "' and MONTH(date) = '" . $Att_DATE[1] . "' and User_uid = '" . $UserID . "'");
                                if ($results_tot_work = mysqli_fetch_assoc($check_Tot_WORK)) 
                                {
                                    $TOTAL_WORK_HRS = $results_tot_work["Total_WORK_HRS"];
                                }

                                $othAR = explode(".", number_format($TOTAL_WORK_HRS,2));

                                $TOT_OTH = $othAR[0];
                                $TOT_OTM = $othAR[1];
                                //Calculate OT Value
                                $TOT_MinsOT = ($TOT_OTH*60) + $TOT_OTM;
                                $TOT_WORKvalue = floor($TOT_MinsOT/60);


                               
                                if($saturday)
                                {
                                    $TH130 = date("H:i:s", strtotime(GetWorkingOuttimeWeekends($UserID)));
                                    
                                    if ($TOT_WORKvalue > 200) 
                                    {
                                        $OTHours = getTimeDifference($Date, $TH130, $ydate, $Time2);
                                        $OTHours = number_format(floatval($OTHours),2);
                                    }
                                    else
                                    {
                                        $OTHours = 0;
                                    }

                                    if (strtotime($Time2) < strtotime(GetWorkingOuttimeWeekends($UserID)))  
                                    {
                                        $lateMintsData = (strtotime(GetWorkingOuttimeWeekends($UserID)) - strtotime($Time2))/60;
                                    }
                                    else
                                    {
                                        $lateMintsData = 0;
                                    }


                                    $queryEmpType = "select EmployeeType_etid from user where uid = '" . $UserID . "' and isactive='1'";
                                    $resEmpType = Search($queryEmpType);
                                    if ($resultEmpType = mysqli_fetch_assoc($resEmpType)) 
                                    {
                                         if ($resultEmpType["EmployeeType_etid"] == "1") 
                                         {
                                            $OT_to_save = 0;
                                         }
                                         else if ($resultEmpType["EmployeeType_etid"] == "2") 
                                         {
                                            $OT_to_save = 0;
                                         }
                                         else if ($resultEmpType["EmployeeType_etid"] == "3") 
                                         {
                                            $OT_to_save = 0;
                                         }
                                         else if ($resultEmpType["EmployeeType_etid"] == "4") 
                                         {
                                            $OT_to_save = 0;
                                         }
                                         else if ($resultEmpType["EmployeeType_etid"] == "5") 
                                         {
                                            $OT_to_save = 0;
                                         }
                                         else if ($resultEmpType["EmployeeType_etid"] == "6") 
                                         {
                                            $OT_to_save = 0;
                                         }
                                         else
                                         {
                                            $OT_to_save = $static_morning_OT + $OTHours;
                                         }
                                    }

                                    $New_Late_Min = $Late_min_DATA + $lateMintsData;

                                    echo $hours."###".$OT_to_save."###".$New_Late_Min."###".$halfDays."###".$shortLeaves."###".$DOThours."###".$MOThours;

                                }
                                else
                                {
                                    $T5Eve = date("H:i:s", strtotime(GetWorkingOuttimeWeek($UserID))); //change 5 to 5.30 (2022/06/13)
                                    if ($TOT_WORKvalue > 200) 
                                    {
                                        $OTHours = getTimeDifference($Date, $T5Eve, $ydate, $Time2);
                                        $OTHours = number_format(floatval($OTHours),2);
                                    }
                                    else
                                    {
                                        $OTHours = 0;
                                    }

                                    $queryEmpType = "select EmployeeType_etid from user where uid = '" . $UserID . "' and isactive='1'";
                                    $resEmpType = Search($queryEmpType);
                                    if ($resultEmpType = mysqli_fetch_assoc($resEmpType)) 
                                    {
                                         if ($resultEmpType["EmployeeType_etid"] == "1") 
                                         {
                                            $OTHours = 0;
                                         }
                                         else if ($resultEmpType["EmployeeType_etid"] == "2") 
                                         {
                                            $OTHours = 0;
                                         }
                                         else if ($resultEmpType["EmployeeType_etid"] == "3") 
                                         {
                                            $OTHours = 0;
                                         }
                                         else if ($resultEmpType["EmployeeType_etid"] == "4") 
                                         {
                                            $OTHours = 0;
                                         }
                                         else if ($resultEmpType["EmployeeType_etid"] == "5") 
                                         {
                                            $OTHours = 0;
                                         }
                                         else if ($resultEmpType["EmployeeType_etid"] == "6") 
                                         {
                                            $OTHours = 0;
                                         }
                                    }

                                    echo $hours."###".$OTHours."###".$Late_min_DATA."###".$halfDays."###".$shortLeaves."###".$DOThours."###".$MOThours;
                                }       
                               
                            }

                            
                        }
                        else
                        {
                            $T8Mng = date("H:i:s", strtotime(GetWorkingIntimeWeek($UserID)));
                            $hours = getTimeDifference($Date, $T8Mng, $Date, $Time2);

                            $static_morning_OT = $MOThours;
                            $Late_MIN_DATA = $lateMints;
                            //remove Lunch Hour if out time is before 1:30 afternoon
                            $timeIN24HR = date("H:i:s", strtotime($Time2));
                            $T5Eve = date("H:i:s", strtotime(GetWorkingOuttimeWeek($UserID)));

                            $T130Aftn = date("H:i:s", strtotime(GetHalfMorningOuttime($UserID)));
                            // if intime not between 8.30 and 1.30 no deduction fro lunch hour
                            if($T8Mng < $timeIN24HR && $T130Aftn > $timeIN24HR)
                            {
                             
                            }
                            else
                            {
                                $hourd_pre_ded_intavels = $hours;
                                $hours = $hours - 0.5; //Lunch Time Duration
                            }

                            $hours = number_format(floatval($hours),2);

                            $Att_DATE = explode('-', $Date);
                            $check_Tot_WORK = Search("select sum(hours) as Total_WORK_HRS from attendance where YEAR(date) = '" . $Att_DATE[0] . "' and MONTH(date) = '" . $Att_DATE[1] . "' and User_uid = '" . $UserID . "'");
                            if ($results_tot_work = mysqli_fetch_assoc($check_Tot_WORK)) 
                            {
                                $TOTAL_WORK_HRS = $results_tot_work["Total_WORK_HRS"];
                            }

                            $othAR = explode(".", number_format($TOTAL_WORK_HRS,2));

                            $TOT_OTH = $othAR[0];
                            $TOT_OTM = $othAR[1];
                            //Calculate OT Value
                            $TOT_MinsOT = ($TOT_OTH*60) + $TOT_OTM;
                            $TOT_WORKvalue = floor($TOT_MinsOT/60);

                            if ($TOT_WORKvalue > 200) 
                            {
                                if($T5Eve < $timeIN24HR){
                                    $OTfromPeriod = getTimeDifference($Date, $T5Eve, $Date, $Time2);
                                    $OTfromPeriod = number_format(floatval($OTfromPeriod),2);
                                }
                            }
                            else
                            {
                                $OTfromPeriod = 0;
                            }


                            // new saving ~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                            $DOTHORS = "";
                            if($saturday)
                            {
                               //from 8.30 normal hrs , from 1.30 OT hours
                                $T830 = "08:30";
                                $T130 = "13:30";

                                $TH830 = date("H:i:s", strtotime(GetWorkingIntimeWeekends($UserID)));
                                $TH130 = date("H:i:s", strtotime(GetWorkingOuttimeWeekends($UserID)));
                                $TH145 = date("H:i:s", strtotime("01:45 PM"));
                                $THIN = date("H:i:s", strtotime($Time1));

                                if ($TOT_WORKvalue > 200) 
                                {
                                    $OTHours = getTimeDifference($Date, $TH130, $Date, $Time2);
                                }
                                else
                                {
                                    $OTHours = 0;
                                }

                                if (strtotime($Time2) < strtotime(GetWorkingOuttimeWeekends($UserID))) 
                                {
                                    $lateMintsDataA = (strtotime(GetWorkingOuttimeWeekends($UserID)) - strtotime($Time2))/60;
                                }
                                else
                                {
                                    $lateMintsDataA = 0;
                                }

                                $queryEmpType = "select EmployeeType_etid from user where uid = '" . $UserID . "' and isactive='1'";
                                    $resEmpType = Search($queryEmpType);
                                if ($resultEmpType = mysqli_fetch_assoc($resEmpType)) 
                                {
                                     if ($resultEmpType["EmployeeType_etid"] == "1") 
                                     {
                                        $OT_to_save = 0;
                                     }
                                     else if ($resultEmpType["EmployeeType_etid"] == "2") 
                                     {
                                        $OT_to_save = 0;
                                     }
                                     else if ($resultEmpType["EmployeeType_etid"] == "3") 
                                     {
                                        $OT_to_save = 0;
                                     }
                                     else if ($resultEmpType["EmployeeType_etid"] == "4") 
                                     {
                                        $OT_to_save = 0;
                                     }
                                     else if ($resultEmpType["EmployeeType_etid"] == "5") 
                                     {
                                        $OT_to_save = 0;
                                     }
                                     else if ($resultEmpType["EmployeeType_etid"] == "6") 
                                     {
                                        $OT_to_save = 0;
                                     }
                                     else
                                     {
                                        $OT_to_save = $static_morning_OT + $OTHours;
                                     }
                                }

                                $New_Late = $Late_MIN_DATA + $lateMintsDataA;

                                echo $hours."###".$OT_to_save."###".$New_Late."###".$halfDay."###".$shortLeave."###".$DOTHORS."###".$MOThours;

                            }
                            else
                            {
                                $LeaveType = "";
                                $Slot = "";
                                $halfDay = "";
                                $shortLeave = "";
                                $queryHalfShortCheck = "select type,time_slot,days from employee_leave where date = '" . $Date . "' and uid = '" . $UserID . "' and (type like 'Halfday Evening Leave' or type like 'Short Evening Leave' or type like 'Nopay Evening Leave' or type like 'Duty Evening Leave') and is_att_leave = '0'";
                                $resHalfShortCheck = Search($queryHalfShortCheck);
                                if ($resultHalfShortCheck = mysqli_fetch_assoc($resHalfShortCheck)) 
                                {
                                    $LeaveType =  $resultHalfShortCheck["type"];
                                    $Slot =  $resultHalfShortCheck["time_slot"];
                                    $DAYS =  $resultHalfShortCheck["days"];

                                    if ($LeaveType == "Halfday Evening Leave") 
                                    {
                                        if (strtotime($Time2) < strtotime(GetHalfELate($UserID))) 
                                        {
                                            $lateMintsData = (strtotime(GetHalfELate($UserID)) - strtotime($Time2))/60;
                                        }
                                        else
                                        {
                                            $lateMintsData = 0;
                                        }

                                        $halfDay = 1;
                                        $shortLeave = 0;
    
                                    }
                                    else if ($LeaveType == "Nopay Evening Leave" || $LeaveType == "Duty Evening Leave") 
                                    {
                                        if (strtotime($Time2) < strtotime(GetHalfELate($UserID))) 
                                        {
                                            $lateMintsData = (strtotime(GetHalfELate($UserID)) - strtotime($Time2))/60;
                                        }
                                        else
                                        {
                                            $lateMintsData = 0;
                                        }

                                        $halfDay = 0;
                                        $shortLeave = 0;
    
                                    }
                                    else if ($LeaveType == "Short Evening Leave") 
                                    {
                                        if (strtotime($Time2) < strtotime(GetShortELate($UserID))) 
                                        {
                                            $lateMintsData = (strtotime(GetShortELate($UserID)) - strtotime($Time2))/60;
                                        }
                                        else
                                        {
                                            $lateMintsData = 0;
                                        }

                                        $halfDay = 0;
                                        $shortLeave = 1;
                                    }
                                    else
                                    {
                                        if (strtotime($Time2) < strtotime(GetWorkingOuttimeWeek($UserID)))  
                                        {
                                            $lateMintsData = (strtotime(GetWorkingOuttimeWeek($UserID)) - strtotime($Time2))/60;
                                        }
                                        else
                                        {
                                            $lateMintsData = 0;
                                        }

                                        $halfDay = 0;
                                        $shortLeave = 0;
                                    }      
                                }
                                else
                                {
                                    if (strtotime($Time2) < strtotime(GetWorkingOuttimeWeek($UserID)))  
                                    {
                                        $lateMintsData = (strtotime(GetWorkingOuttimeWeek($UserID)) - strtotime($Time2))/60;
                                    }
                                    else
                                    {
                                        $lateMintsData = 0;
                                    }

                                    $halfDay = 0;
                                    $shortLeave = 0;
                                }

                                $queryEmpType = "select EmployeeType_etid from user where uid = '" . $UserID . "' and isactive='1'";
                                    $resEmpType = Search($queryEmpType);
                                if ($resultEmpType = mysqli_fetch_assoc($resEmpType)) 
                                {
                                     if ($resultEmpType["EmployeeType_etid"] == "1") 
                                     {
                                        $OTfromPeriod = 0;
                                     }
                                     else if ($resultEmpType["EmployeeType_etid"] == "2") 
                                     {
                                        $OTfromPeriod = 0;
                                     }
                                     else if ($resultEmpType["EmployeeType_etid"] == "3") 
                                     {
                                        $OTfromPeriod = 0;
                                     }
                                     else if ($resultEmpType["EmployeeType_etid"] == "4") 
                                     {
                                        $OTfromPeriod = 0;
                                     }
                                     else if ($resultEmpType["EmployeeType_etid"] == "5") 
                                     {
                                        $OTfromPeriod = 0;
                                     }
                                     else if ($resultEmpType["EmployeeType_etid"] == "6") 
                                     {
                                        $OTfromPeriod = 0;
                                     }
                                }


                                $OT_to_save = $static_morning_OT + $OTfromPeriod;
                                $New_Late_Min = $Late_MIN_DATA + $lateMintsData;

                                echo $hours."###".$OT_to_save."###".$New_Late_Min."###".$halfDay."###".$shortLeave."###".$DOTHORS."###".$MOThours;
                            }
                        }
                        
                    }
                    else
                    {   
                        $T8Mng = date("H:i:s", strtotime(GetWorkingIntimeWeek($UserID)));
                        $hours = getTimeDifference($Date, $T8Mng, $Date, $Time2);
                        $static_morning_OT = $MOThours;
                        $Late_MIN_DATA = $lateMints;
                        $timeIN24HR = date("H:i:s", strtotime($Time2));
                        $T5Eve = date("H:i:s", strtotime(GetWorkingOuttimeWeek($UserID)));

                        $T130Aftn = date("H:i:s", strtotime(GetHalfMorningOuttime($UserID)));
                        // if intime not between 8.30 and 1.30 no deduction fro lunch hour
                        if($T8Mng < $timeIN24HR && $T130Aftn > $timeIN24HR)
                        {
                         
                        }
                        else
                        {
                            $hourd_pre_ded_intavels = $hours;
                            $hours = $hours - 0.5; //Lunch Time Duration
                        } 

                        $hours = number_format(floatval($hours),2);

                        $Att_DATE = explode('-', $Date);
                        $check_Tot_WORK = Search("select sum(hours) as Total_WORK_HRS from attendance where YEAR(date) = '" . $Att_DATE[0] . "' and MONTH(date) = '" . $Att_DATE[1] . "' and User_uid = '" . $UserID . "'");
                        if ($results_tot_work = mysqli_fetch_assoc($check_Tot_WORK)) 
                        {
                            $TOTAL_WORK_HRS = $results_tot_work["Total_WORK_HRS"];
                        }

                        $othAR = explode(".", number_format($TOTAL_WORK_HRS,2));

                        $TOT_OTH = $othAR[0];
                        $TOT_OTM = $othAR[1];
                        //Calculate OT Value
                        $TOT_MinsOT = ($TOT_OTH*60) + $TOT_OTM;
                        $TOT_WORKvalue = floor($TOT_MinsOT/60);
                        
                        if ($TOT_WORKvalue > 200) 
                        {
                            if($T5Eve < $timeIN24HR){
                                $OTfromPeriod = getTimeDifference($Date, $T5Eve, $Date, $Time);
                                $OTfromPeriod = number_format(floatval($OTfromPeriod),2);
                            }
                        }
                        else
                        {
                            $OTfromPeriod = 0;
                        }


                        // new saving ~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                        $DOTHORS = "";
                        if($saturday)
                        {
                           //from 8.30 normal hrs , from 1.30 OT hours
                            $T830 = "08:30";
                            $T130 = "13:30";

                            $TH830 = date("H:i:s", strtotime(GetWorkingIntimeWeekends($UserID)));
                            $TH130 = date("H:i:s", strtotime(GetWorkingOuttimeWeekends($UserID)));
                            $THIN = date("H:i:s", strtotime($Time1));


                            if ($TOT_WORKvalue > 200) 
                            {
                                $OTHours = getTimeDifference($Date, $TH130, $Date, $Time2);
                            }
                            else
                            {
                                $OTHours = 0;
                            }

                            if (strtotime($Time2) < strtotime(GetWorkingOuttimeWeekends($UserID))) 
                            {
                                $lateMintsDataA = (strtotime(GetWorkingOuttimeWeekends($UserID)) - strtotime($Time2))/60;
                            }
                            else
                            {
                                $lateMintsDataA = 0;
                            }

                            $queryEmpType = "select EmployeeType_etid from user where uid = '" . $UserID . "' and isactive='1'";
                                    $resEmpType = Search($queryEmpType);
                            if ($resultEmpType = mysqli_fetch_assoc($resEmpType)) 
                            {
                                 if ($resultEmpType["EmployeeType_etid"] == "1") 
                                 {
                                    $OTHours = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "2") 
                                 {
                                    $OTHours = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "3") 
                                 {
                                    $OTHours = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "4") 
                                 {
                                    $OTHours = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "5") 
                                 {
                                    $OTHours = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "6") 
                                 {
                                    $OTHours = 0;
                                 }
                            }

                            $OT_to_save = $static_morning_OT + $OTHours;
                            $New_Late = $Late_MIN_DATA + $lateMintsDataA;

                            echo $hours."###".$OT_to_save."###".$New_Late."###".$halfDay."###".$shortLeave."###".$DOTHORS."###".$MOThours;

                        }
                        else
                        {
                            $LeaveType = "";
                            $Slot = "";
                            $halfDay = "";
                            $shortLeave = "";
                            $queryHalfShortCheck = "select type,time_slot,days from employee_leave where date = '" . $Date . "' and uid = '" . $UserID . "' and (type like 'Halfday Evening Leave' or type like 'Short Evening Leave' or type like 'Nopay Evening Leave' or type like 'Duty Evening Leave') and is_att_leave = '0'";
                            $resHalfShortCheck = Search($queryHalfShortCheck);
                            if ($resultHalfShortCheck = mysqli_fetch_assoc($resHalfShortCheck)) 
                            {
                                $LeaveType =  $resultHalfShortCheck["type"];
                                $Slot =  $resultHalfShortCheck["time_slot"];
                                $DAYS =  $resultHalfShortCheck["days"];

                                if ($LeaveType == "Halfday Evening Leave") 
                                {
                                    if (strtotime($Time2) < strtotime(GetHalfELate($UserID))) 
                                    {
                                        $lateMintsData = (strtotime(GetHalfELate($UserID)) - strtotime($Time2))/60;
                                    }
                                    else
                                    {
                                        $lateMintsData = 0;
                                    }

                                    $halfDay = 1;
                                    $shortLeave = 0;
                                }
                                else if ($LeaveType == "Nopay Evening Leave" || $LeaveType == "Duty Evening Leave") 
                                {
                                    if (strtotime($Time2) < strtotime(GetHalfELate($UserID))) 
                                    {
                                        $lateMintsData = (strtotime(GetHalfELate($UserID)) - strtotime($Time2))/60;
                                    }
                                    else
                                    {
                                        $lateMintsData = 0;
                                    }

                                    $halfDay = 0;
                                    $shortLeave = 0;
                                }
                                else if ($LeaveType == "Short Evening Leave") 
                                {
                                    if (strtotime($Time2) < strtotime(GetShortELate($UserID))) 
                                    {
                                        $lateMintsData = (strtotime(GetShortELate($UserID)) - strtotime($Time2))/60;
                                    }
                                    else
                                    {
                                        $lateMintsData = 0;
                                    }

                                    $halfDay = 0;
                                    $shortLeave = 1;
                                }
                                else
                                {
                                    if (strtotime($Time2) < strtotime(GetWorkingOuttimeWeek($UserID)))  
                                    {
                                        $lateMintsData = (strtotime(GetWorkingOuttimeWeek($UserID)) - strtotime($Time2))/60;
                                    }
                                    else
                                    {
                                        $lateMintsData = 0;
                                    }

                                    $halfDay = 0;
                                    $shortLeave = 0;
                                }       
                            }
                            else
                            {
                                if (strtotime($Time2) < strtotime(GetWorkingOuttimeWeek($UserID)))  
                                {
                                    $lateMintsData = (strtotime(GetWorkingOuttimeWeek($UserID)) - strtotime($Time2))/60;
                                }
                                else
                                {
                                    $lateMintsData = 0;
                                }

                                $halfDay = 0;
                                $shortLeave = 0;
                            }

                            $queryEmpType = "select EmployeeType_etid from user where uid = '" . $UserID . "' and isactive='1'";
                                    $resEmpType = Search($queryEmpType);
                            if ($resultEmpType = mysqli_fetch_assoc($resEmpType)) 
                            {
                                 if ($resultEmpType["EmployeeType_etid"] == "1") 
                                 {
                                    $OTfromPeriod = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "2") 
                                 {
                                    $OTfromPeriod = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "3") 
                                 {
                                    $OTfromPeriod = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "4") 
                                 {
                                    $OTfromPeriod = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "5") 
                                 {
                                    $OTfromPeriod = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "6") 
                                 {
                                    $OTfromPeriod = 0;
                                 }
                            }


                            $OT_to_save = $static_morning_OT + $OTfromPeriod;
                            $New_Late_Min = $Late_MIN_DATA + $lateMintsData;

                            echo $hours."###".$OT_to_save."###".$New_Late_Min."###".$halfDay."###".$shortLeave."###".$DOTHORS."###".$MOThours;
                        }
                    }

                }
                

            }

        }

        if ($_REQUEST["request"] == "getleavecount") {

       // $YearDiff = 0;
       $queryYears = "select registerdDate,first_year_leave_end_date from user where uid = '" . $_REQUEST["UID"] . "'";
       $resYears = Search($queryYears);

       if ($resultYears = mysqli_fetch_assoc($resYears)) 
       {
           $joinDate = $resultYears["registerdDate"];
           $first_yr_end_Date = $resultYears["first_year_leave_end_date"];
       }

       $YearDiff = date('Y-m-d') - $joinDate;
       $date1 = $joinDate;
       $date2 = date('Y-m-d');

       $diff = abs(strtotime($date2) - strtotime($date1));

       $years = floor($diff / (365*60*60*24));
       $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
       $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
       
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
       

       if ($years >= "0" && $years < "2") 
       {

          if ($years == "0" && $months == "0") 
          {
               $Total_leave = 0;
               $Flag = "Empty";
               echo 'A'."#".$Get_Leave_Half_OR_CASUAL."#".$Available_Leave_Half_OR_CASUAL."#".$Get_Leave_Short_OR_ANNUAL."#".$Available_Leave_Short_OR_ANNUAL."#".$Get_Leave_MEDICAL."#".$Available_Leave_MEDICAL."#".$Total_leave."#".$Get_Separate_Short."#".$Available_Separate_Short."#".$Get_Separate_Half."#".$Available_Separate_Half."#".$NOPAY."#".$DUTY."#".$MATERNITY."#".$PARENTAL."#".$LIUE_LEAVE."#".$Flag;

          }
          else if ($years == "0" && $months >= "1" && $months <= "12") 
          {
                //2024-06-22 NEW PART
                if (date('m', strtotime($joinDate)) == "07" || date('m', strtotime($joinDate)) == "08" || date('m', strtotime($joinDate)) == "09" || date('m', strtotime($joinDate)) == "10" || date('m', strtotime($joinDate)) == "11" || date('m', strtotime($joinDate)) == "12") 
                {
                    if ($years == "0" && $months >= "6")
                    {
                        if (date('m', strtotime($joinDate)) == "01" || date('m', strtotime($joinDate)) == "02" || date('m', strtotime($joinDate)) == "03") 
                        {
                            $casual_leaves = 7;
                            $annual_leaves = 14;
                            $medical_leaves = 7;
                        }
                        else if (date('m', strtotime($joinDate)) == "04" || date('m', strtotime($joinDate)) == "05" || date('m', strtotime($joinDate)) == "06") 
                        {
                            $casual_leaves = 7;
                            $annual_leaves = 10;
                            $medical_leaves = 5;
                        }
                        else if (date('m', strtotime($joinDate)) == "07" || date('m', strtotime($joinDate)) == "08" || date('m', strtotime($joinDate)) == "09") 
                        {
                            $casual_leaves = 7;
                            $annual_leaves = 7;
                            $medical_leaves = 3;
                        }
                         else if (date('m', strtotime($joinDate)) == "10" || date('m', strtotime($joinDate)) == "11" || date('m', strtotime($joinDate)) == "12")
                        {
                            $casual_leaves = 7;
                            $annual_leaves = 4;
                            $medical_leaves = 0;
                        }
                         
                        $one_month_short = 0.5; // 2 short leaves

                        if (!empty($first_yr_end_Date) && $first_yr_end_Date != "0000-00-00") 
                        {
                            $Get_DATA="AND date > '".$first_yr_end_Date."'";
                        }
                        else
                        {
                            $Get_DATA= "";
                        }

                         

                        $resHalf = Search("select sum(days) as halfleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' AND (type like 'Halfday Morning Leave' or type like 'Halfday Evening Leave') ".$Get_DATA."");
                        
                        if ($resultHalf = mysqli_fetch_assoc($resHalf)) 
                        {
                            if ($resultHalf["halfleave"] == "") 
                            {
                                $Get_Separate_Half = 0;
                            }
                            else
                            {
                                $Get_Separate_Half = $resultHalf["halfleave"];
                            }          
                        }

                        $res_casual = Search("select sum(days) as totalLeaves_casual from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' and type like 'Casual Leave'");
                        if ($result_casual = mysqli_fetch_assoc($res_casual)) 
                        {
                            if ($result_casual["totalLeaves_casual"] == "")
                            {
                               $Get_Leave_Half_OR_CASUAL = 0 + $Get_Separate_Half; 
                               $Available_Leave_Half_OR_CASUAL = $casual_leaves - $Get_Separate_Half;
                            }
                            else
                            {
                               $Get_Leave_Half_OR_CASUAL = $result_casual["totalLeaves_casual"];
                               
                               if ($Get_Leave_Half_OR_CASUAL >= $casual_leaves) 
                               {
                                   $Available_Leave_Half_OR_CASUAL = 0;
                                   $Get_Leave_Half_OR_CASUAL = $casual_leaves;
                               }
                               else
                               {
                                   $Available_Leave_Half_OR_CASUAL = $casual_leaves - ($Get_Leave_Half_OR_CASUAL + $Get_Separate_Half);
                                   $Get_Leave_Half_OR_CASUAL = $Get_Leave_Half_OR_CASUAL + $Get_Separate_Half; // 2024-05-14 added
                               } 
                            }
                        }


                        $res_annual = Search("select sum(days) as totalLeaves_anual from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' and type like 'Annual Leave'");
                        if ($result_annual = mysqli_fetch_assoc($res_annual)) 
                        {
                            if ($result_annual["totalLeaves_anual"] == "")
                            {
                                $Get_Leave_Short_OR_ANNUAL = 0;
                                $Available_Leave_Short_OR_ANNUAL = $annual_leaves;
                            }
                            else
                            {
                               $Get_Leave_Short_OR_ANNUAL = $result_annual["totalLeaves_anual"];
                               
                               if ($Get_Leave_Short_OR_ANNUAL >= $annual_leaves) 
                               {
                                   $Get_Leave_Short_OR_ANNUAL = $annual_leaves;
                                   $Available_Leave_Short_OR_ANNUAL = 0;
                               }
                               else
                               {
                                   $Available_Leave_Short_OR_ANNUAL = $annual_leaves - $Get_Leave_Short_OR_ANNUAL;
                               }
                            }
                        }

                        $res_medical = Search("select sum(days) as totalLeaves_medical from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' and type like 'Medical Leave'");
                        if ($result_medical = mysqli_fetch_assoc($res_medical)) 
                        {
                            if ($result_medical["totalLeaves_medical"] == "")
                            {
                                $Get_Leave_MEDICAL = 0;
                                $Available_Leave_MEDICAL = $medical_leaves;
                            }
                            else
                            {
                               $Get_Leave_MEDICAL = $result_medical["totalLeaves_medical"];
                               
                               if ($Get_Leave_MEDICAL >= $medical_leaves) 
                               {
                                   $Get_Leave_MEDICAL = $medical_leaves;
                                   $Available_Leave_MEDICAL = 0;
                               }
                               else
                               {
                                   $Available_Leave_MEDICAL = $medical_leaves - $Get_Leave_MEDICAL;
                               }
                            }
                        }

                        $queryShort = "select sum(days) as shortleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' AND MONTH(date) = '".date('m',strtotime($_REQUEST["SelDate"]))."' AND (type like 'Short Morning Leave' or type like 'Short Evening Leave')";
                        $resShort = Search($queryShort);
                        
                        if ($resultShort = mysqli_fetch_assoc($resShort)) 
                        {
                            if ($resultShort["shortleave"] == "") 
                            {
                                $Get_Separate_Short = 0;
                                $Available_Separate_Short = $one_month_short - 0;
                            }
                            else
                            {
                                $Get_Separate_Short = $resultShort["shortleave"];
                                $Available_Separate_Short = $one_month_short - $Get_Separate_Short;
                            }          
                        }

                        $queryNopay = "select sum(days) as Nopayleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' AND (type like 'Nopay Full Day Leave' or type like 'Nopay Morning Leave' or type like 'Nopay Evening Leave')";
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

                        
                        $resDuty = Search("select sum(days) as Dutyleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' AND (type like 'Duty Full Day Leave' or type like 'Duty Morning Leave' or type like 'Duty Evening Leave')");
                              
                        if ($resultDuty = mysqli_fetch_assoc($resDuty)) 
                        {
                            if ($resultDuty["Dutyleave"] == "") 
                            {
                              $DUTY = 0;
                            }
                            else
                            {
                              $DUTY = $resultDuty["Dutyleave"];
                            }           
                        }


                        
                        $resMaternity = Search("select sum(days) as Maternityleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' AND type like 'Maternity Leave'");      
                        if ($resultMaternity = mysqli_fetch_assoc($resMaternity)) 
                        {
                              if ($resultMaternity["Maternityleave"] == "") 
                              {
                                  $MATERNITY = 0;
                              }
                              else
                              {
                                  $MATERNITY = $resultMaternity["Maternityleave"];
                              }           
                        }
                        
                        $resParental = Search("select sum(days) as Parentalleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' AND type like 'Parental Leave'");
                              
                        if ($resultParental = mysqli_fetch_assoc($resParental)) 
                        {
                              if ($resultParental["Parentalleave"] == "") 
                              {
                                  $PARENTAL = 0;
                              }
                              else
                              {
                                  $PARENTAL = $resultParental["Parentalleave"];
                              }           
                        }


                        $queryLiue_Leave = "select sum(days) as liueleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' AND type like 'Lieu Leave'";
                        $resLiue_Leave = Search($queryLiue_Leave);
                        
                        if ($resultLiue_Leave = mysqli_fetch_assoc($resLiue_Leave)) 
                        {
                            if ($resultLiue_Leave["liueleave"] == "") 
                            {
                                $LIUELEAVEDATA = 0;
                            }
                            else
                            {
                                $LIUELEAVEDATA = $resultLiue_Leave["liueleave"];
                            }          
                        }

                        $NOPAY = $NOPAYDATA - $LIUELEAVEDATA;
                        $LIUE_LEAVE = $LIUELEAVEDATA;

                        if ($NOPAY <= 0) 
                        {
                          $NOPAY = 0;
                        }

                        $Total_leave = $annual_leaves + $casual_leaves; 

                        echo 'B'."#".$Get_Leave_Half_OR_CASUAL."#".$Available_Leave_Half_OR_CASUAL."#".$Get_Leave_Short_OR_ANNUAL."#".$Available_Leave_Short_OR_ANNUAL."#".$Get_Leave_MEDICAL."#".$Available_Leave_MEDICAL."#".$Total_leave."#".$Get_Separate_Short."#".$Available_Separate_Short."#".$Get_Separate_Half."#".$Available_Separate_Half."#".$NOPAY."#".$DUTY."#".$MATERNITY."#".$PARENTAL."#".$LIUE_LEAVE."#".$Flag;
                    }
                }


                $one_month_half = 0.5; // 1 half day
                $one_month_short = 0.5; // 2 short leaves

                $queryHalf = "select sum(days) as halfleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' AND MONTH(date) = '".date('m',strtotime($_REQUEST["SelDate"]))."' AND (type like 'Halfday Morning Leave' or type like 'Halfday Evening Leave')";
                $resHalf = Search($queryHalf);
                
                if ($resultHalf = mysqli_fetch_assoc($resHalf)) 
                {
                    if ($resultHalf["halfleave"] == "") 
                    {
                        $resPreviouseHalf = Search("select total_leave as previousehalf from total_leave_data where uid = '" . $_REQUEST["UID"] . "'");
                
                        if ($resultPreviouseHalf = mysqli_fetch_assoc($resPreviouseHalf)) 
                        {
                            $TOTAL = $resultPreviouseHalf["previousehalf"];

                            if ($resultPreviouseHalf["previousehalf"] == "") 
                            {
                                $Get_Leave_Half_OR_CASUAL = 0;
                                $Available_Leave_Half_OR_CASUAL = $one_month_half;
                            }
                            else
                            {
                                $Get_Leave_Half_OR_CASUAL = 0;
                                $Available_Leave_Half_OR_CASUAL = $resultPreviouseHalf["previousehalf"];
                            }
                                       
                        }

                    }
                    else
                    {
                        $resPreviouseHalf = Search("select total_leave as previousehalf from total_leave_data where uid = '" . $_REQUEST["UID"] . "'");
                
                        if ($resultPreviouseHalf = mysqli_fetch_assoc($resPreviouseHalf)) 
                        {
                            $TOTAL = $resultPreviouseHalf["previousehalf"];

                            if ($resultPreviouseHalf["previousehalf"] == "") 
                            {
                                $Get_Leave_Half_OR_CASUAL = 0;
                                $Available_Leave_Half_OR_CASUAL = $one_month_half;
                            }
                            else
                            {
                                $Get_Leave_Half_OR_CASUAL = $resultHalf["halfleave"];
                                $Available_Leave_Half_OR_CASUAL = $resultPreviouseHalf["previousehalf"];
                            }
                                       
                        }
                        
                    }
                               
                }


                $queryShort = "select sum(days) as shortleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' AND MONTH(date) = '".date('m',strtotime($_REQUEST["SelDate"]))."' AND (type like 'Short Morning Leave' or type like 'Short Evening Leave')";
                $resShort = Search($queryShort);
                
                if ($resultShort = mysqli_fetch_assoc($resShort)) 
                {
                    if ($resultShort["shortleave"] == "") 
                    {
                        $Get_Separate_Short = 0;
                        $Available_Separate_Short = $one_month_short - 0;
                    }
                    else
                    {
                        $Get_Separate_Short = $resultShort["shortleave"];
                        $Available_Separate_Short = $one_month_short - $Get_Separate_Short;
                    }
                    
                           
                }

                $queryNopay = "select sum(days) as Nopayleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' AND MONTH(date) = '".date('m',strtotime($_REQUEST["SelDate"]))."' AND (type like 'Nopay Full Day Leave' or type like 'Nopay Morning Leave' or type like 'Nopay Evening Leave')";
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

                
                $queryLiue_Leave = "select sum(days) as liueleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' AND MONTH(date) = '".date('m',strtotime($_REQUEST["SelDate"]))."' AND type like 'Lieu Leave'";
                $resLiue_Leave = Search($queryLiue_Leave);
                
                if ($resultLiue_Leave = mysqli_fetch_assoc($resLiue_Leave)) 
                {
                    if ($resultLiue_Leave["liueleave"] == "") 
                    {
                        $LIUELEAVEDATA = 0;
                    }
                    else
                    {
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

               echo 'A'."#".$Get_Leave_Half_OR_CASUAL."#".$Available_Leave_Half_OR_CASUAL."#".$Get_Leave_Short_OR_ANNUAL."#".$Available_Leave_Short_OR_ANNUAL."#".$Get_Leave_MEDICAL."#".$Available_Leave_MEDICAL."#".$Total_leave."#".$Get_Separate_Short."#".$Available_Separate_Short."#".$Get_Separate_Half."#".$Available_Separate_Half."#".$NOPAY."#".$DUTY."#".$MATERNITY."#".$PARENTAL."#".$LIUE_LEAVE."#".$Flag;


          }
          else
          {
                 if (date('m', strtotime($joinDate)) == "01" || date('m', strtotime($joinDate)) == "02" || date('m', strtotime($joinDate)) == "03") 
                 {
                     $casual_leaves = 7;
                     $annual_leaves = 14;
                     $medical_leaves = 7;
                 }
                 else if (date('m', strtotime($joinDate)) == "04" || date('m', strtotime($joinDate)) == "05" || date('m', strtotime($joinDate)) == "06") 
                 {
                     $casual_leaves = 7;
                     $annual_leaves = 10;
                     $medical_leaves = 5;
                 }
                 else if (date('m', strtotime($joinDate)) == "07" || date('m', strtotime($joinDate)) == "08" || date('m', strtotime($joinDate)) == "09") 
                 {
                     $casual_leaves = 7;
                     $annual_leaves = 7;
                     $medical_leaves = 3;
                 }
                 else if (date('m', strtotime($joinDate)) == "10" || date('m', strtotime($joinDate)) == "11" || date('m', strtotime($joinDate)) == "12")
                 {
                     $casual_leaves = 7;
                     $annual_leaves = 4;
                     $medical_leaves = 0;
                 }
                 
                 $one_month_short = 0.5; // 2 short leaves

                 if (!empty($first_yr_end_Date) && $first_yr_end_Date != "0000-00-00") 
                 {
                    $Get_DATA="AND date > '".$first_yr_end_Date."'";
                 }
                 else
                 {
                    $Get_DATA= "";
                 }

                 

                $resHalf = Search("select sum(days) as halfleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' AND (type like 'Halfday Morning Leave' or type like 'Halfday Evening Leave') ".$Get_DATA."");
                
                if ($resultHalf = mysqli_fetch_assoc($resHalf)) 
                {
                    if ($resultHalf["halfleave"] == "") 
                    {
                        $Get_Separate_Half = 0;
                    }
                    else
                    {
                        $Get_Separate_Half = $resultHalf["halfleave"];
                    }          
                }

                $res_casual = Search("select sum(days) as totalLeaves_casual from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' and type like 'Casual Leave'");
                if ($result_casual = mysqli_fetch_assoc($res_casual)) 
                {
                    if ($result_casual["totalLeaves_casual"] == "")
                    {
                       $Get_Leave_Half_OR_CASUAL = 0 + $Get_Separate_Half; 
                       $Available_Leave_Half_OR_CASUAL = $casual_leaves - $Get_Separate_Half;
                    }
                    else
                    {
                       $Get_Leave_Half_OR_CASUAL = $result_casual["totalLeaves_casual"];
                       
                       if ($Get_Leave_Half_OR_CASUAL >= $casual_leaves) 
                       {
                           $Available_Leave_Half_OR_CASUAL = 0;
                           $Get_Leave_Half_OR_CASUAL = $casual_leaves;
                       }
                       else
                       {
                           $Available_Leave_Half_OR_CASUAL = $casual_leaves - ($Get_Leave_Half_OR_CASUAL + $Get_Separate_Half);
                           $Get_Leave_Half_OR_CASUAL = $Get_Leave_Half_OR_CASUAL + $Get_Separate_Half; // 2024-05-14 added
                       } 
                    }
                }


                $res_annual = Search("select sum(days) as totalLeaves_anual from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' and type like 'Annual Leave'");
                if ($result_annual = mysqli_fetch_assoc($res_annual)) 
                {
                    if ($result_annual["totalLeaves_anual"] == "")
                    {
                        $Get_Leave_Short_OR_ANNUAL = 0;
                        $Available_Leave_Short_OR_ANNUAL = $annual_leaves;
                    }
                    else
                    {
                       $Get_Leave_Short_OR_ANNUAL = $result_annual["totalLeaves_anual"];
                       
                       if ($Get_Leave_Short_OR_ANNUAL >= $annual_leaves) 
                       {
                           $Get_Leave_Short_OR_ANNUAL = $annual_leaves;
                           $Available_Leave_Short_OR_ANNUAL = 0;
                       }
                       else
                       {
                           $Available_Leave_Short_OR_ANNUAL = $annual_leaves - $Get_Leave_Short_OR_ANNUAL;
                       }
                    }
                }

                $res_medical = Search("select sum(days) as totalLeaves_medical from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' and type like 'Medical Leave'");
                if ($result_medical = mysqli_fetch_assoc($res_medical)) 
                {
                    if ($result_medical["totalLeaves_medical"] == "")
                    {
                        $Get_Leave_MEDICAL = 0;
                        $Available_Leave_MEDICAL = $medical_leaves;
                    }
                    else
                    {
                       $Get_Leave_MEDICAL = $result_medical["totalLeaves_medical"];
                       
                       if ($Get_Leave_MEDICAL >= $medical_leaves) 
                       {
                           $Get_Leave_MEDICAL = $medical_leaves;
                           $Available_Leave_MEDICAL = 0;
                       }
                       else
                       {
                           $Available_Leave_MEDICAL = $medical_leaves - $Get_Leave_MEDICAL;
                       }
                    }
                }

                $queryShort = "select sum(days) as shortleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' AND MONTH(date) = '".date('m',strtotime($_REQUEST["SelDate"]))."' AND (type like 'Short Morning Leave' or type like 'Short Evening Leave')";
                $resShort = Search($queryShort);
                
                if ($resultShort = mysqli_fetch_assoc($resShort)) 
                {
                    if ($resultShort["shortleave"] == "") 
                    {
                        $Get_Separate_Short = 0;
                        $Available_Separate_Short = $one_month_short - 0;
                    }
                    else
                    {
                        $Get_Separate_Short = $resultShort["shortleave"];
                        $Available_Separate_Short = $one_month_short - $Get_Separate_Short;
                    }          
                }

                $queryNopay = "select sum(days) as Nopayleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' AND (type like 'Nopay Full Day Leave' or type like 'Nopay Morning Leave' or type like 'Nopay Evening Leave')";
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

                
                $resDuty = Search("select sum(days) as Dutyleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' AND (type like 'Duty Full Day Leave' or type like 'Duty Morning Leave' or type like 'Duty Evening Leave')");
                      
                if ($resultDuty = mysqli_fetch_assoc($resDuty)) 
                {
                    if ($resultDuty["Dutyleave"] == "") 
                    {
                      $DUTY = 0;
                    }
                    else
                    {
                      $DUTY = $resultDuty["Dutyleave"];
                    }           
                }


                
                $resMaternity = Search("select sum(days) as Maternityleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' AND type like 'Maternity Leave'");      
                if ($resultMaternity = mysqli_fetch_assoc($resMaternity)) 
                {
                      if ($resultMaternity["Maternityleave"] == "") 
                      {
                          $MATERNITY = 0;
                      }
                      else
                      {
                          $MATERNITY = $resultMaternity["Maternityleave"];
                      }           
                }
                
                $resParental = Search("select sum(days) as Parentalleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' AND type like 'Parental Leave'");
                      
                if ($resultParental = mysqli_fetch_assoc($resParental)) 
                {
                      if ($resultParental["Parentalleave"] == "") 
                      {
                          $PARENTAL = 0;
                      }
                      else
                      {
                          $PARENTAL = $resultParental["Parentalleave"];
                      }           
                }


                $queryLiue_Leave = "select sum(days) as liueleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' AND type like 'Lieu Leave'";
                $resLiue_Leave = Search($queryLiue_Leave);
                
                if ($resultLiue_Leave = mysqli_fetch_assoc($resLiue_Leave)) 
                {
                    if ($resultLiue_Leave["liueleave"] == "") 
                    {
                        $LIUELEAVEDATA = 0;
                    }
                    else
                    {
                        $LIUELEAVEDATA = $resultLiue_Leave["liueleave"];
                    }          
                }

                $NOPAY = $NOPAYDATA - $LIUELEAVEDATA;
                $LIUE_LEAVE = $LIUELEAVEDATA;

                if ($NOPAY <= 0) 
                {
                  $NOPAY = 0;
                }

                $Total_leave = $annual_leaves + $casual_leaves; 

                echo 'B'."#".$Get_Leave_Half_OR_CASUAL."#".$Available_Leave_Half_OR_CASUAL."#".$Get_Leave_Short_OR_ANNUAL."#".$Available_Leave_Short_OR_ANNUAL."#".$Get_Leave_MEDICAL."#".$Available_Leave_MEDICAL."#".$Total_leave."#".$Get_Separate_Short."#".$Available_Separate_Short."#".$Get_Separate_Half."#".$Available_Separate_Half."#".$NOPAY."#".$DUTY."#".$MATERNITY."#".$PARENTAL."#".$LIUE_LEAVE."#".$Flag;

          }

  
       }
       else
       {
               $casual_leaves = 7;
               $annual_leaves = 14;
               $medical_leaves = 7;
               $one_month_short = 0.5; // 2 short leaves
               

               $resHalf = Search("select sum(days) as halfleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' AND (type like 'Halfday Morning Leave' or type like 'Halfday Evening Leave')");
                
                if ($resultHalf = mysqli_fetch_assoc($resHalf)) 
                {
                    if ($resultHalf["halfleave"] == "") 
                    {
                        $Get_Separate_Half = 0;
                    }
                    else
                    {
                        $Get_Separate_Half = $resultHalf["halfleave"];
                    }          
                }

                $res_casual = Search("select sum(days) as totalLeaves_casual from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' and type like 'Casual Leave'");
                if ($result_casual = mysqli_fetch_assoc($res_casual)) 
                {
                    if ($result_casual["totalLeaves_casual"] == "")
                    {
                       $Get_Leave_Half_OR_CASUAL = 0 + $Get_Separate_Half; 
                       $Available_Leave_Half_OR_CASUAL = $casual_leaves - $Get_Separate_Half;
                    }
                    else
                    {
                       $Get_Leave_Half_OR_CASUAL = $result_casual["totalLeaves_casual"];
                       
                       if ($Get_Leave_Half_OR_CASUAL >= $casual_leaves) 
                       {
                           $Available_Leave_Half_OR_CASUAL = 0;
                           $Get_Leave_Half_OR_CASUAL = $casual_leaves;
                       }
                       else
                       {
                           $Available_Leave_Half_OR_CASUAL = $casual_leaves - ($Get_Leave_Half_OR_CASUAL + $Get_Separate_Half);
                           $Get_Leave_Half_OR_CASUAL = $Get_Leave_Half_OR_CASUAL + $Get_Separate_Half; // 2024-05-14 added
                       } 
                    }
                }


                $res_annual = Search("select sum(days) as totalLeaves_anual from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' and type like 'Annual Leave'");
                if ($result_annual = mysqli_fetch_assoc($res_annual)) 
                {
                    if ($result_annual["totalLeaves_anual"] == "")
                    {
                        $Get_Leave_Short_OR_ANNUAL = 0;
                        $Available_Leave_Short_OR_ANNUAL = $annual_leaves;
                    }
                    else
                    {
                       $Get_Leave_Short_OR_ANNUAL = $result_annual["totalLeaves_anual"];
                       
                       if ($Get_Leave_Short_OR_ANNUAL >= $annual_leaves) 
                       {
                           $Get_Leave_Short_OR_ANNUAL = $annual_leaves;
                           $Available_Leave_Short_OR_ANNUAL = 0;
                       }
                       else
                       {
                           $Available_Leave_Short_OR_ANNUAL = $annual_leaves - $Get_Leave_Short_OR_ANNUAL;
                       }
                    }
                }

                $res_medical = Search("select sum(days) as totalLeaves_medical from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' and type like 'Medical Leave'");
                if ($result_medical = mysqli_fetch_assoc($res_medical)) 
                {
                    if ($result_medical["totalLeaves_medical"] == "")
                    {
                        $Get_Leave_MEDICAL = 0;
                        $Available_Leave_MEDICAL = $medical_leaves;
                    }
                    else
                    {
                       $Get_Leave_MEDICAL = $result_medical["totalLeaves_medical"];
                       
                       if ($Get_Leave_MEDICAL >= $medical_leaves) 
                       {
                           $Get_Leave_MEDICAL = $medical_leaves;
                           $Available_Leave_MEDICAL = 0;
                       }
                       else
                       {
                           $Available_Leave_MEDICAL = $medical_leaves - $Get_Leave_MEDICAL;
                       }
                    }
                }

                $queryShort = "select sum(days) as shortleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' AND MONTH(date) = '".date('m',strtotime($_REQUEST["SelDate"]))."' AND (type like 'Short Morning Leave' or type like 'Short Evening Leave')";
                $resShort = Search($queryShort);
                
                if ($resultShort = mysqli_fetch_assoc($resShort)) 
                {
                    if ($resultShort["shortleave"] == "") 
                    {
                        $Get_Separate_Short = 0;
                        $Available_Separate_Short = $one_month_short - 0;
                    }
                    else
                    {
                        $Get_Separate_Short = $resultShort["shortleave"];
                        $Available_Separate_Short = $one_month_short - $Get_Separate_Short;
                    }          
                }

                $queryNopay = "select sum(days) as Nopayleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' AND (type like 'Nopay Full Day Leave' or type like 'Nopay Morning Leave' or type like 'Nopay Evening Leave')";
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

                
                $resDuty = Search("select sum(days) as Dutyleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' AND (type like 'Duty Full Day Leave' or type like 'Duty Morning Leave' or type like 'Duty Evening Leave')");
                      
                if ($resultDuty = mysqli_fetch_assoc($resDuty)) 
                {
                    if ($resultDuty["Dutyleave"] == "") 
                    {
                      $DUTY = 0;
                    }
                    else
                    {
                      $DUTY = $resultDuty["Dutyleave"];
                    }           
                }


                
                $resMaternity = Search("select sum(days) as Maternityleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' AND type like 'Maternity Leave'");      
                if ($resultMaternity = mysqli_fetch_assoc($resMaternity)) 
                {
                      if ($resultMaternity["Maternityleave"] == "") 
                      {
                          $MATERNITY = 0;
                      }
                      else
                      {
                          $MATERNITY = $resultMaternity["Maternityleave"];
                      }           
                }
                
                $resParental = Search("select sum(days) as Parentalleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' AND type like 'Parental Leave'");
                      
                if ($resultParental = mysqli_fetch_assoc($resParental)) 
                {
                      if ($resultParental["Parentalleave"] == "") 
                      {
                          $PARENTAL = 0;
                      }
                      else
                      {
                          $PARENTAL = $resultParental["Parentalleave"];
                      }           
                }


                $queryLiue_Leave = "select sum(days) as liueleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y',strtotime($_REQUEST["SelDate"]))."' AND type like 'Lieu Leave'";
                $resLiue_Leave = Search($queryLiue_Leave);
                
                if ($resultLiue_Leave = mysqli_fetch_assoc($resLiue_Leave)) 
                {
                    if ($resultLiue_Leave["liueleave"] == "") 
                    {
                        $LIUELEAVEDATA = 0;
                    }
                    else
                    {
                        $LIUELEAVEDATA = $resultLiue_Leave["liueleave"];
                    }          
                }

                $NOPAY = $NOPAYDATA - $LIUELEAVEDATA;
                $LIUE_LEAVE = $LIUELEAVEDATA;

                if ($NOPAY <= 0) 
                {
                  $NOPAY = 0;
                }


               $Total_leave = $annual_leaves + $casual_leaves;               

              echo 'C'."#".$Get_Leave_Half_OR_CASUAL."#".$Available_Leave_Half_OR_CASUAL."#".$Get_Leave_Short_OR_ANNUAL."#".$Available_Leave_Short_OR_ANNUAL."#".$Get_Leave_MEDICAL."#".$Available_Leave_MEDICAL."#".$Total_leave."#".$Get_Separate_Short."#".$Available_Separate_Short."#".$Get_Separate_Half."#".$Available_Separate_Half."#".$NOPAY."#".$DUTY."#".$MATERNITY."#".$PARENTAL."#".$LIUE_LEAVE."#".$Flag;

       }
         
    }

        if ($_REQUEST["request"] == "updateRecord") {
            $queryx = "select date from attendance where aid = '" . $_REQUEST["aid"] . "'";
            $res = Search($queryx);
            if ($result = mysqli_fetch_assoc($res)) {
                $query = "update attendance set intime = '" . $_REQUEST["int"] . "',outtime = '" . $_REQUEST["out"] . "',shortleave = '" . $_REQUEST["sl"] . "',halfday = '" . $_REQUEST["hd"] . "',hours = '" . $_REQUEST["hours"] . "',othours = '" . $_REQUEST["othours"] . "',late_att_min = '" . $_REQUEST["lm"] . "',dothours = '" . $_REQUEST["dot"] . "' where aid = '" . $_REQUEST["aid"] . "'";
                $res = SUD($query);
            } else {
                $query = "insert into attendance(date,intime,outtime,shortleave,halfday,hours,othours,User_uid,attendanceType_atid,attendance) values('" . $_REQUEST["date"] . "','" . $_REQUEST["int"] . "','" . $_REQUEST["out"] . "','" . $_REQUEST["sl"] . "','" . $_REQUEST["hd"] . "','" . $_REQUEST["hours"] . "','" . $_REQUEST["othours"] . "','" . $_REQUEST["uid"] . "','1','1')";
                $res = SUD($query);
            }

            if ($res = "1") {
                echo 'Record Updated!';
            } else {
                echo 'Record Updating Error!';
            }}
            if ($_REQUEST["request"] == "deleteattendance") 
            {
                $res_att_leave = Search("select User_uid from attendance where aid = '" . $_REQUEST["aid"] . "'");
                if ($result_att_leave = mysqli_fetch_assoc($res_att_leave)) 
                {
                    $user = $result_att_leave["User_uid"];
                }

                $res_Check_leave = Search("select lid from employee_leave where uid = '" . $user . "' and date = '".$_REQUEST["attdate"]."' and is_att_leave='1'");
                if ($result_check_leave = mysqli_fetch_assoc($res_Check_leave)) 
                {
                    SUD("delete from attendance where aid = '" . $_REQUEST["aid"] . "'");
                    SUD("delete from employee_leave where lid = '" . $result_check_leave["lid"] . "'");
                }
                else
                {
                    SUD("delete from attendance where aid = '" . $_REQUEST["aid"] . "'");
                }
            }
            if ($_REQUEST["request"] == "viewLeaves") {
                $out = "";
                $arrSTY = explode("-", $_REQUEST["date"]);
                $res = Search("select type,date,lid from employee_leave where uid = '".$_REQUEST["eid"] ."' and Year(date) = '".$arrSTY[0]."' and Month(date) = '".$arrSTY[1]."' order by date");
                while ($result = mysqli_fetch_assoc($res)) {
                    $out .= "<tr><td width='200'>".$result["type"]."</td><td width='200'>".$result["date"]."</td><td><img src='../Icons/remove.png' onclick='deleteLeave(" . $result["lid"] . ")'></td></tr>";
                }

        // echo $out;  
            }
            if($_REQUEST["request"]=="getHolidays"){

                $out="<table  class='table table-striped' style='color: black;'><tr style='background-color: #9eafba; position : sticky; top : 0; z-index: 0;'><th>No</th><th>Date</th><th>Description</th><th></th></tr>";
                $query = "select * from poyadays where YEAR(date)='".date("Y")."' order by date ASC";
                $res = Search($query);

                while ($result = mysqli_fetch_assoc($res)) {
                    if(!$result["name"]==null){
                        $descrip=$result["name"];
                    }else{
                        $descrip="-";
                    }

                    $details=$result["idpd"] . "#" . $result["date"] . "#" . $result["name"]; 

                    $out.="<tr style='cursor: pointer;' id='".$details."' onclick='select_holidayx(id)'><td>".$result["idpd"]."</td><td>".$result["date"]."</td><td>".$descrip."</td><td><img src='../Icons/remove.png' onclick='deleteholid(" . $result["idpd"] . ")'></td></tr>";
                }
                $out.="</table>";
            // echo $out;
            }
            if ($_REQUEST["request"] == "AddHolidayExcelData") {

                $query = "select * from poyadays where date='".$_REQUEST["date"]."'";
                $res = Search($query);
                if ($result = mysqli_fetch_assoc($res)) 
                {

                }
                else
                {
                    $query="insert into poyadays(date,name) values('".$_REQUEST["date"]."','".$_REQUEST["name"]."')";
                    SUD($query);
                }

                
            }
            if( $_REQUEST["request"]=="inholidy"){

                $query = "select * from poyadays where date='".$_REQUEST["date"]."'";
                $res = Search($query);

                if ($result = mysqli_fetch_assoc($res)) {

                    $out="Alredy Added !!!";
                }else{

                    $query="insert into poyadays(date,name) values('".$_REQUEST["date"]."','".$_REQUEST["name"]."')";
                    SUD($query);

                    $out="Added !!!";
                }
                   
            }
            if( $_REQUEST["request"]=="upholiday"){
                   
                $query="update poyadays set date='".$_REQUEST["date"]."' , name='".$_REQUEST["name"]."' where idpd='".$_REQUEST["id"]."'";
                SUD($query);

                $out="Updated !!!";

            }

            if ($_REQUEST["request"] == "deleteholiday") { 
                $query="Delete from poyadays where idpd='".$_REQUEST["hid"]."'";
                SUD($query);
                $out="Deleted !!!";
            }

            if ($_REQUEST["request"] == "deleteLeave") {
                $query = "delete from employee_leave where lid = '" . $_REQUEST["lid"] . "'";
                SUD($query);
                echo "Leave Deleted!";
            }
            

            if ($_REQUEST["request"] == "SaveShiftData") {

                $query = "select ewsid from emp_working_shift where year='".$_REQUEST["YEAR"]."' and month='".$_REQUEST["MONTH"]."' and uid='".$_REQUEST["EMP_ID"]."'";
                $res = Search($query);
                if ($result = mysqli_fetch_assoc($res)) 
                {
                    $shiftID = 0;
                }
                else
                {
                    $query="insert into emp_working_shift(year, month, uid) values('".$_REQUEST["YEAR"]."','".$_REQUEST["MONTH"]."','".$_REQUEST["EMP_ID"]."')";
                    $shiftID = SUDwithKeys($query);
                }

                echo $shiftID;  
            }

            if ($_REQUEST["request"] == "AddShiftData") {

                $query = "select swtpsid from shift_working_time_profile_settings where name ='".$_REQUEST["name"]."'";
                $res = Search($query);
                if ($result = mysqli_fetch_assoc($res)) 
                {
                   $shift_typ = $result["swtpsid"];
                }
                else
                {
                   $shift_typ = 0;
                }

                $query="insert into emp_shift_has_dates(ewsid, shift_type_id, date) values('".$_REQUEST["shiftid"]."','".$shift_typ."','".$_REQUEST["date"]."')";
                SUD($query);

                echo "Complete";
                
            }


            if ($_REQUEST["request"] == "viewShift") {
                $out = "";

                $res = Search("select ewsid from emp_working_shift where uid = '".$_REQUEST["empid"] ."' and year = '".$_REQUEST["year"]."' and month = '".$_REQUEST["month"]."'");
                while ($result = mysqli_fetch_assoc($res)) {
                    $out .= "<tr><td width='200'>WSNo : ".$result["ewsid"]."</td><td><img src='../Icons/remove.png' onclick='deleteShift(" . $result["ewsid"] . ")'></td></tr>";
                } 
            }


            if ($_REQUEST["request"] == "deleteshift") { 
               
                $response1 = SUD("Delete from emp_shift_has_dates where ewsid='".$_REQUEST["shiftid"]."'");

                $response2 = SUD("Delete from emp_working_shift where ewsid='".$_REQUEST["shiftid"]."'");

                if ($response1 == 1 && $response2 == 1) 
                {
                    echo "Record deleted!";
                }
                else
                {
                    echo "Error!";
                }  
            }




            if ($_REQUEST["request"] == "AddExcelData") {
                    
                    if ($_REQUEST["intime"] == "" && $_REQUEST["outtime"] == "") 
                    {
                        $msg_IN = "Att_IN_YES";
                        $msg_OUT = "Att_OUT_YES";
                    }
                    else
                    {
                        if ($_REQUEST["stat1"] == "IN") 
                        {
                            $uid = $_REQUEST["empno"];
                            $Date = $_REQUEST["date"];
                            $INTime = $_REQUEST["intime"];
                            $Status = "";
                            $Action = "C/In";
                            $JobCode = $_REQUEST["empno"];
                            $dateArr = explode("/", $Date);
                            $Date = $dateArr[2]."-".$dateArr[0]."-".$dateArr[1];

                            //save in
                            $msg_IN = UserAttendanceRecordFORExcelFile($uid, $Status, $Action, $JobCode, $Date, $INTime);
                        }

                        if ($_REQUEST["stat2"] == "OUT") 
                        {
                                //save out
                                $uid = $_REQUEST["empno"];
                                $Date = $_REQUEST["date"];
                                $INTime = $_REQUEST["outtime"];
                                $Status = "";
                                $JobCode = $_REQUEST["empno"];
                                $Date = $dateArr[2]."-".$dateArr[0]."-".$dateArr[1];
                                $Action = "C/Out";

                            $msg_OUT = UserAttendanceRecordFORExcelFile($uid, $Status, $Action, $JobCode, $Date, $INTime); 
                        }
                    }   

                echo $msg_IN."#".$msg_OUT;     
                
            }
            if ($_REQUEST["request"] == "getleavedetails") { 
        // $query = "select sum(totwdays - wdays) as leaves from salarycomplete where uid = ".$_REQUEST["eid"] ." and year(datefrom) = '".$_REQUEST["year"]."'";
        // $res = Search($query);
        // if ($result = mysqli_fetch_assoc($res)) {
        //     $out = $result["leaves"];
        // }

                $out = 0;

                $query = "select sum(days) as totdaysYear from employee_leave where uid = '".$_REQUEST["eid"] ."' and year(date) = '".$_REQUEST["year"]."'";
                $res = Search($query);
                while ($result = mysqli_fetch_assoc($res)) {
                    // if($result["type"] == "Halfday Leave"){
                    //     $out += 0.5;
                    // }else{
                    //     $out += 1;
                    // }

                    $out = $result["totdaysYear"];
                }

                $query = "select * from employee_leave where uid = '".$_REQUEST["eid"] ."' and year(date) = '".$_REQUEST["year"]."' and month(date) = '".$_REQUEST["month"]."' order by date asc";
                $res = Search($query);
                $outx = "";        
                while ($result = mysqli_fetch_assoc($res)) {
                    $outx .= $result["date"]." : ".$result["type"]." : ".$result["days"]."</br>";

                }


                $lids = 0;
                $query = "select sum(days) as totdaysMonth from employee_leave where uid = '".$_REQUEST["eid"] ."' and year(date) = '".$_REQUEST["year"]."' and month(date) = '".$_REQUEST["month"]."' order by date asc";
                $res = Search($query);     
                while ($result = mysqli_fetch_assoc($res)) {                
                    // if($result["type"] == "Halfday Leave"){
                    //     $lids += 0.5;
                    // }else{
                    //     $lids += 1;
                    // } 

                    $lids = $result["totdaysMonth"]; 
                }

                $out = $out."#/#".$outx."#/#".$lids;
            }

            echo $out;
        }



        function updateUserAttendanceFromRange($fileName) {

            require_once('../lib/PHPExcel/PHPExcel.php');

            $excelObject = PHPExcel_IOFactory::load($fileName); 
            $Sheet = $excelObject->getActiveSheet()->toArray(null);

            for ($x = 1; $x < count($Sheet); $x++) {    

               // echo "<pre>".print_r($Sheet[$x])."</pre>";     

                for ($y = 0; $y < count($Sheet[$x]); ++$y) {

    //      //set the parameters
                    $UID = $Sheet[$x][0];

                    $Date = $Sheet[$x][1];
                    $INTime = $Sheet[$x][2];
            //$OUTTime = explode(" ", $Sheet[$x][8])[1];

                    $Status = "";
                    $Action = "C/In";
                    $JobCode = $UID; 

                    // echo "UID : ".$JobCode." ".$INTime." ".$Date."</br>";   

                // $Date = DateTime::createFromFormat('m/d/y', $Date)->format('Y-m-d');

                //convert format
                    $dateArr = explode("/", $Date);

                    $Date = $dateArr[2]."-".$dateArr[0]."-".$dateArr[1];

                    // echo $Date; 

                    // echo "</br>".$JobCode." | ".$Date." | ".$INTime." | ".$Action."</br>";  


                //save in
                    updateUserAttendanceRecord($UID, $Status, $Action, $JobCode, $Date, $INTime); 


                //save out
                    $Date = $Sheet[$x][1];
                    $Date = $dateArr[2]."-".$dateArr[0]."-".$dateArr[1];
                    $INTime = $Sheet[$x][3]; 
                    $Action = "C/Out";

                    // echo "UID : ".$JobCode." ".$INTime."</br>";

                    // echo "</br>".$JobCode." | ".$Date." | ".$INTime." | ".$Action."</br>";  

                    updateUserAttendanceRecord($UID, $Status, $Action, $JobCode, $Date, $INTime); 



                    break;
                } 

            }

            header("Location: ../Views/emp_attendance.php?&state=Uploaded Successfully!");
        }



       
function updateUserAttendanceRecord($UID, $Status, $Action, $JobCode, $Date, $Time) 
{

    // $UID = preg_replace("/[^0-9]/", "", $UID);
    // $Status = preg_replace("/[^0-9]/", "", $Status);
    // $JobCode = preg_replace("/[^0-9]/", "", $JobCode);

    $time_in_24_hour_format  = date("H:i:s", strtotime($Time));
    $Time = $time_in_24_hour_format;

    $poyaDay = false;
    //if date is poya day
    $querp = "select date from poyadays where date = '".$Date."'";
    $resp = Search($querp);
    if ($resulp = mysqli_fetch_assoc($resp)) {
        $poyaDay = true;
    }else{
        $poyaDay = false;
    }

    $x = strtotime($Date);
    $x = date('l', $x);

    //if date is Saturday 
    $saturday = false;
    if ($x == "Saturday") {
        $saturday = true;
    }

    $static_morning_OT = 0;

    //$UserID = 0;
    $queryu = "select uid,work_typ from user where jobcode = '" . $UID . "' and isactive='1'";
    $resu = Search($queryu);
    if ($resultu = mysqli_fetch_assoc($resu)) {
        $UserID = $resultu["uid"];
        $Work_TYPE = $resultu["work_typ"];
    }


    //Get Join Date
    $resYears = Search("select registerdDate,first_year_leave_end_date from user where uid = '" . $UserID . "'");
    if ($resultYears = mysqli_fetch_assoc($resYears)) 
    {
       $joinDate = $resultYears["registerdDate"];
       $first_yr_end_Date = $resultYears["first_year_leave_end_date"];
    }

    $YearDiff = date('Y-m-d') - $joinDate;
    $date1 = $joinDate;
    $date2 = date('Y-m-d');

    $diff = abs(strtotime($date2) - strtotime($date1));

    $years = floor($diff / (365*60*60*24));
    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

    
    $Total_leave = 0;
    $Get_Leave_Half_OR_CASUAL = 0;
    $Available_Leave_Half_OR_CASUAL = 0;
    $Get_Leave_Short_OR_ANNUAL = 0;
    $Available_Leave_Short_OR_ANNUAL = 0;

    $Get_Short = 0;
    $Available_Short = 0;   

    if ($years >= "0" && $years < "2") 
    {

        if ($years == "0" && $months == "0") 
        {
            $Section = "A";
            $Total_leave = 0;
            $Get_Leave_Half_OR_CASUAL = 0;
            $Available_Leave_Half_OR_CASUAL = 0;
            $Get_Leave_Short_OR_ANNUAL = 0;
            $Available_Leave_Short_OR_ANNUAL = 0;
            $Get_Short = 0;
            $Available_Short = 0;   
        }
        else if ($years == "0" && $months >= "1" && $months <= "12") 
        {
            //2024-06-22 NEW PART
            if (date('m', strtotime($joinDate)) == "07" || date('m', strtotime($joinDate)) == "08" || date('m', strtotime($joinDate)) == "09" || date('m', strtotime($joinDate)) == "10" || date('m', strtotime($joinDate)) == "11" || date('m', strtotime($joinDate)) == "12") 
            {
                if ($years == "0" && $months >= "6")
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
                    
                    $Total_leave = 0;
                    $Get_Leave_Half_OR_CASUAL = 0;
                    $Available_Leave_Half_OR_CASUAL = 0;
                    $Get_Leave_Short_OR_ANNUAL = 0;
                    $Available_Leave_Short_OR_ANNUAL = 0;

                    $one_month_short = 0.5; // 2 short leaves
                    $Get_Short = 0;
                    $Available_Short = 0;


                    if (!empty($first_yr_end_Date) && $first_yr_end_Date != "0000-00-00") 
                    {
                        $Get_DATA="AND date > '".$first_yr_end_Date."'";
                    }
                    else
                    {
                        $Get_DATA= "";
                    }

                     

                    $resHalf = Search("select sum(days) as halfleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y')."' AND (type like 'Halfday Morning Leave' or type like 'Halfday Evening Leave') ".$Get_DATA."");
                    
                    if ($resultHalf = mysqli_fetch_assoc($resHalf)) 
                    {
                        if ($resultHalf["halfleave"] == "") 
                        {
                            $Get_Separate_Half = 0;
                        }
                        else
                        {
                            $Get_Separate_Half = $resultHalf["halfleave"];
                        }          
                    }
                    
                    $res_casual = Search("select sum(days) as totalLeaves_casual from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y')."' and type like 'Casual Leave'");
                    if ($result_casual = mysqli_fetch_assoc($res_casual)) 
                    {
                        if ($result_casual["totalLeaves_casual"] == "")
                        {
                           $Get_Leave_Half_OR_CASUAL = 0 + $Get_Separate_Half; 
                           $Available_Leave_Half_OR_CASUAL = $casual_leaves - $Get_Separate_Half;
                        }
                        else
                        {
                           $Get_Leave_Half_OR_CASUAL = $result_casual["totalLeaves_casual"];
                           
                           if ($Get_Leave_Half_OR_CASUAL >= $casual_leaves) 
                           {
                               $Available_Leave_Half_OR_CASUAL = 0;
                               $Get_Leave_Half_OR_CASUAL = $casual_leaves;
                           }
                           else
                           {
                               $Available_Leave_Half_OR_CASUAL = $casual_leaves - ($Get_Leave_Half_OR_CASUAL+ $Get_Separate_Half);
                               $Get_Leave_Half_OR_CASUAL = $Get_Leave_Half_OR_CASUAL + $Get_Separate_Half; // 2024-05-14 added
                           } 
                        }
                    }


                    $res_annual = Search("select sum(days) as totalLeaves_anual from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y')."' and type like 'Annual Leave'");
                    if ($result_annual = mysqli_fetch_assoc($res_annual)) 
                    {
                        if ($result_annual["totalLeaves_anual"] == "")
                        {
                            $Get_Leave_Short_OR_ANNUAL = 0;
                            $Available_Leave_Short_OR_ANNUAL = $annual_leaves;
                        }
                        else
                        {
                           $Get_Leave_Short_OR_ANNUAL = $result_annual["totalLeaves_anual"];
                           
                           if ($Get_Leave_Short_OR_ANNUAL >= $annual_leaves) 
                           {
                               $Get_Leave_Short_OR_ANNUAL = $annual_leaves;
                               $Available_Leave_Short_OR_ANNUAL = 0;
                           }
                           else
                           {
                               $Available_Leave_Short_OR_ANNUAL = $annual_leaves - $Get_Leave_Short_OR_ANNUAL;
                           }
                        }
                    }

                    $queryShort = "select sum(days) as shortleave from employee_leave where uid = '" . $UserID . "' AND YEAR(date) = '".date('Y',strtotime($Date))."' AND MONTH(date) = '".date('m',strtotime($Date))."' AND (type like 'Short Morning Leave' or type like 'Short Evening Leave')";
                    $resShort = Search($queryShort);
                    
                    if ($resultShort = mysqli_fetch_assoc($resShort)) 
                    {
                        if ($resultShort["shortleave"] == "") 
                        {
                            $Get_Short = 0;
                            $Available_Short = $one_month_short - 0;
                        }
                        else
                        {
                            $Get_Short = $resultShort["shortleave"];
                            $Available_Short = $one_month_short - $Get_Short;
                        }          
                    }

                    $Total_leave = $annual_leaves + $casual_leaves;
                }
            }


            $one_month_half = 0.5; // 1 half day
            $one_month_short = 0.5; // 2 short leaves

            $Total_leave = 0;
            $Get_Leave_Half_OR_CASUAL = 0;
            $Available_Leave_Half_OR_CASUAL = 0;
            $Get_Leave_Short_OR_ANNUAL = 0;
            $Available_Leave_Short_OR_ANNUAL = 0;

            $queryHalf = "select sum(days) as halfleave from employee_leave where uid = '" . $UserID . "' AND YEAR(date) = '".date('Y',strtotime($Date))."' AND MONTH(date) = '".date('m',strtotime($Date))."' AND (type like 'Halfday Morning Leave' or type like 'Halfday Evening Leave')";
            $resHalf = Search($queryHalf);
            
            if ($resultHalf = mysqli_fetch_assoc($resHalf)) 
            {
                if ($resultHalf["halfleave"] == "") 
                {
                    $resPreviouseHalf = Search("select total_leave as previousehalf from total_leave_data where uid = '" . $UserID . "'");
            
                    if ($resultPreviouseHalf = mysqli_fetch_assoc($resPreviouseHalf)) 
                    {
                        $TOTAL = $resultPreviouseHalf["previousehalf"];

                        if ($resultPreviouseHalf["previousehalf"] == "") 
                        {
                            
                        }
                        else
                        {
                            $Get_Leave_Half_OR_CASUAL = 0;
                            $Available_Leave_Half_OR_CASUAL = $resultPreviouseHalf["previousehalf"];
                        }
                                   
                    }

                }
                else
                {
                    $resPreviouseHalf = Search("select total_leave as previousehalf from total_leave_data where uid = '" . $UserID . "'");
            
                    if ($resultPreviouseHalf = mysqli_fetch_assoc($resPreviouseHalf)) 
                    {
                        $TOTAL = $resultPreviouseHalf["previousehalf"];

                        if ($resultPreviouseHalf["previousehalf"] == "") 
                        {
                        
                        }
                        else
                        {
                            $Get_Leave_Half_OR_CASUAL = $resultHalf["halfleave"];
                            $Available_Leave_Half_OR_CASUAL = $resultPreviouseHalf["previousehalf"];
                        }
                                   
                    }
                    
                }
                           
            }

            $queryShort = "select sum(days) as shortleave from employee_leave where uid = '" . $UserID . "' AND YEAR(date) = '".date('Y',strtotime($Date))."' AND MONTH(date) = '".date('m',strtotime($Date))."' AND (type like 'Short Morning Leave' or type like 'Short Evening Leave')";
            $resShort = Search($queryShort);
            
            if ($resultShort = mysqli_fetch_assoc($resShort)) 
            {
                if ($resultShort["shortleave"] == "") 
                {
                    $Get_Short = 0;
                    $Available_Short = $one_month_short - 0;
                }
                else
                {
                    $Get_Short = $resultShort["shortleave"];
                    $Available_Short = $one_month_short - $Get_Short;
                }       
            }

           $Total_leave = $TOTAL;
           
           if ($Available_Leave_Half_OR_CASUAL <= 0) {
               $Available_Leave_Half_OR_CASUAL = 0;
           }

           if ($Available_Short <= 0) {
               $Available_Short = 0;
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
            
            $Total_leave = 0;
            $Get_Leave_Half_OR_CASUAL = 0;
            $Available_Leave_Half_OR_CASUAL = 0;
            $Get_Leave_Short_OR_ANNUAL = 0;
            $Available_Leave_Short_OR_ANNUAL = 0;

            $one_month_short = 0.5; // 2 short leaves
            $Get_Short = 0;
            $Available_Short = 0;


            if (!empty($first_yr_end_Date) && $first_yr_end_Date != "0000-00-00") 
            {
                $Get_DATA="AND date > '".$first_yr_end_Date."'";
            }
            else
            {
                $Get_DATA= "";
            }

             

            $resHalf = Search("select sum(days) as halfleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y')."' AND (type like 'Halfday Morning Leave' or type like 'Halfday Evening Leave') ".$Get_DATA."");
            
            if ($resultHalf = mysqli_fetch_assoc($resHalf)) 
            {
                if ($resultHalf["halfleave"] == "") 
                {
                    $Get_Separate_Half = 0;
                }
                else
                {
                    $Get_Separate_Half = $resultHalf["halfleave"];
                }          
            }
            
            $res_casual = Search("select sum(days) as totalLeaves_casual from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y')."' and type like 'Casual Leave'");
            if ($result_casual = mysqli_fetch_assoc($res_casual)) 
            {
                if ($result_casual["totalLeaves_casual"] == "")
                {
                    $Get_Leave_Half_OR_CASUAL = 0 + $Get_Separate_Half; 
                    $Available_Leave_Half_OR_CASUAL = $casual_leaves - $Get_Separate_Half;
                }
                else
                {
                   $Get_Leave_Half_OR_CASUAL = $result_casual["totalLeaves_casual"];
                   
                   if ($Get_Leave_Half_OR_CASUAL >= $casual_leaves) 
                   {
                       $Available_Leave_Half_OR_CASUAL = 0;
                       $Get_Leave_Half_OR_CASUAL = $casual_leaves;
                   }
                   else
                   {
                       $Available_Leave_Half_OR_CASUAL = $casual_leaves - ($Get_Leave_Half_OR_CASUAL+ $Get_Separate_Half);
                       $Get_Leave_Half_OR_CASUAL = $Get_Leave_Half_OR_CASUAL + $Get_Separate_Half; // 2024-05-14 added
                   } 
                }
            }


            $res_annual = Search("select sum(days) as totalLeaves_anual from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y')."' and type like 'Annual Leave'");
            if ($result_annual = mysqli_fetch_assoc($res_annual)) 
            {
                if ($result_annual["totalLeaves_anual"] == "")
                {
                    $Get_Leave_Short_OR_ANNUAL = 0;
                    $Available_Leave_Short_OR_ANNUAL = $annual_leaves;
                }
                else
                {
                   $Get_Leave_Short_OR_ANNUAL = $result_annual["totalLeaves_anual"];
                   
                   if ($Get_Leave_Short_OR_ANNUAL >= $annual_leaves) 
                   {
                       $Get_Leave_Short_OR_ANNUAL = $annual_leaves;
                       $Available_Leave_Short_OR_ANNUAL = 0;
                   }
                   else
                   {
                       $Available_Leave_Short_OR_ANNUAL = $annual_leaves - $Get_Leave_Short_OR_ANNUAL;
                   }
                }
            }

            $queryShort = "select sum(days) as shortleave from employee_leave where uid = '" . $UserID . "' AND YEAR(date) = '".date('Y',strtotime($Date))."' AND MONTH(date) = '".date('m',strtotime($Date))."' AND (type like 'Short Morning Leave' or type like 'Short Evening Leave')";
            $resShort = Search($queryShort);
            
            if ($resultShort = mysqli_fetch_assoc($resShort)) 
            {
                if ($resultShort["shortleave"] == "") 
                {
                    $Get_Short = 0;
                    $Available_Short = $one_month_short - 0;
                }
                else
                {
                    $Get_Short = $resultShort["shortleave"];
                    $Available_Short = $one_month_short - $Get_Short;
                }          
            }

            $Total_leave = $annual_leaves + $casual_leaves;
        }
    }
    else
    {
        $casual_leaves = 7;
        $annual_leaves = 14;
        $Total_leave = 0;
        $Get_Leave_Half_OR_CASUAL = 0;
        $Available_Leave_Half_OR_CASUAL = 0;
        $Get_Leave_Short_OR_ANNUAL = 0;
        $Available_Leave_Short_OR_ANNUAL = 0;
           
        $one_month_short = 0.5; // 2 short leaves
        $Get_Short = 0;
        $Available_Short = 0;

       $res_casual = Search("select sum(days) as totalLeaves_casual from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y')."' and (type like 'Casual Leave' or type like 'Halfday Morning Leave' or type like 'Halfday Evening Leave')");
        if ($result_casual = mysqli_fetch_assoc($res_casual)) 
        {
            if ($result_casual["totalLeaves_casual"] == "")
            {
               $Get_Leave_Half_OR_CASUAL = 0; 
               $Available_Leave_Half_OR_CASUAL = $casual_leaves;
            }
            else
            {
               $Get_Leave_Half_OR_CASUAL = $result_casual["totalLeaves_casual"];
               
               if ($Get_Leave_Half_OR_CASUAL >= $casual_leaves) 
               {
                   $Available_Leave_Half_OR_CASUAL = 0;
                   $Get_Leave_Half_OR_CASUAL = $casual_leaves;
               }
               else
               {
                   $Available_Leave_Half_OR_CASUAL = $casual_leaves - $Get_Leave_Half_OR_CASUAL;
               } 
            }
        }


        $res_annual = Search("select sum(days) as totalLeaves_anual from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y')."' and type like 'Annual Leave'");
        if ($result_annual = mysqli_fetch_assoc($res_annual)) 
        {
            if ($result_annual["totalLeaves_anual"] == "")
            {
                $Get_Leave_Short_OR_ANNUAL = 0;
                $Available_Leave_Short_OR_ANNUAL = $annual_leaves;
            }
            else
            {
               $Get_Leave_Short_OR_ANNUAL = $result_annual["totalLeaves_anual"];
               
               if ($Get_Leave_Short_OR_ANNUAL >= $annual_leaves) 
               {
                   $Get_Leave_Short_OR_ANNUAL = $annual_leaves;
                   $Available_Leave_Short_OR_ANNUAL = 0;
               }
               else
               {
                   $Available_Leave_Short_OR_ANNUAL = $annual_leaves - $Get_Leave_Short_OR_ANNUAL;
               }
            }
        }

        $queryShort = "select sum(days) as shortleave from employee_leave where uid = '" . $UserID . "' AND YEAR(date) = '".date('Y',strtotime($Date))."' AND MONTH(date) = '".date('m',strtotime($Date))."' AND (type like 'Short Morning Leave' or type like 'Short Evening Leave')";
        $resShort = Search($queryShort);
            
        if ($resultShort = mysqli_fetch_assoc($resShort)) 
        {
            if ($resultShort["shortleave"] == "") 
            {
                $Get_Short = 0;
                $Available_Short = $one_month_short - 0;
            }
            else
            {
                $Get_Short = $resultShort["shortleave"];
                $Available_Short = $one_month_short - $Get_Short;
            }          
        }

        $Total_leave = $annual_leaves + $casual_leaves; 
    }

    if ($Work_TYPE == "2") 
    {
        //===================SHIFT STAFF================================================

        //get shift type id according to User ID
        $res_shift = Search("select espid from emp_has_shift where user_uid = '".$UserID."' and date = '".$Date."'");
        if ($result_shift = mysqli_fetch_assoc($res_shift)) 
        {
            $Req_Shift_Typ_ID = $result_shift["espid"];
        }
        else
        {
            $Req_Shift_Typ_ID = "";
        }

        
        // Action = In (Intime Calculation)
        if($Action == "C/In")
        {

            $MOThours = 0;

            $LeaveType = "";
            $Slot = "";
            $IS_HALF = 0;
            $IS_SHORT = 0;

            $queryHalfShortCheck = "select type,time_slot,days from employee_leave where date = '" . $Date . "' and uid = '" . $UserID . "' and (type like 'Halfday Morning Leave' or type like 'Short Morning Leave' or type like 'Nopay Morning Leave' or type like 'Duty Morning Leave') and is_att_leave = '0'";
            $resHalfShortCheck = Search($queryHalfShortCheck);
            if ($resultHalfShortCheck = mysqli_fetch_assoc($resHalfShortCheck)) 
            {
                $LeaveType =  $resultHalfShortCheck["type"];
                $Slot =  $resultHalfShortCheck["time_slot"];
                $DAYS =  $resultHalfShortCheck["days"];

                if ($LeaveType == "Halfday Morning Leave") 
                {   
                    if (strtotime($Time) > strtotime(GetShiftHalfMLate($Req_Shift_Typ_ID))) 
                    {
                        $lateMints = (strtotime($Time) - strtotime(GetShiftHalfMLate($Req_Shift_Typ_ID)))/60;
                    }
                    else
                    {
                        $lateMints = 0;
                    }

                    $IS_HALF = 1;
                    $IS_SHORT = 0;   
                }
                else if ($LeaveType == "Nopay Morning Leave" || $LeaveType == "Duty Morning Leave") 
                {   
                    if (strtotime($Time) > strtotime(GetShiftHalfMLate($Req_Shift_Typ_ID))) 
                    {
                        $lateMints = (strtotime($Time) - strtotime(GetShiftHalfMLate($Req_Shift_Typ_ID)))/60;
                    }
                    else
                    {
                        $lateMints = 0;
                    }
                    
                    $IS_HALF = 0;
                    $IS_SHORT = 0;   
                }
                else if ($LeaveType == "Short Morning Leave") 
                {
                    if (strtotime($Time) > strtotime(GetShiftShortMLate($Req_Shift_Typ_ID))) 
                    {
                        $lateMints = (strtotime($Time) - strtotime(GetShiftShortMLate($Req_Shift_Typ_ID)))/60;
                    }
                    else
                    {
                        $lateMints = 0;
                    }

                    $IS_HALF = 0;
                    $IS_SHORT = 1; 
                }
                else
                {
                    if (strtotime($Time) > strtotime(GetShiftLate($Req_Shift_Typ_ID)))     
                    {
                       // $lateMints = (strtotime($Time1) - strtotime("08:30:00"))/60;
                        $lateMints = (strtotime($Time) - strtotime(GetShiftIntime($Req_Shift_Typ_ID)))/60;
                    }
                    else
                    {
                        $lateMints = 0;
                    }

                    $IS_HALF = 0;
                    $IS_SHORT = 0;
                }  
            }
            else
            {
                //if (strtotime($Time1) > strtotime("08:36:00")) 
                if (strtotime($Time) > strtotime(GetShiftLate($Req_Shift_Typ_ID)))     
                {
                   // $lateMints = (strtotime($Time1) - strtotime("08:30:00"))/60;
                    $lateMints = (strtotime($Time) - strtotime(GetShiftIntime($Req_Shift_Typ_ID)))/60;
                }
                else
                {
                    $lateMints = 0;
                }

                $IS_HALF = 0;
                $IS_SHORT = 0;
            }


            if ($lateMints > 0) 
            {
                $Att_DATE = explode('-', $Date);
                $check_Tot_Late = Search("select sum(late_att_min) as Total_Late_Min from attendance where YEAR(date) = '" . $Att_DATE[0] . "' and MONTH(date) = '" . $Att_DATE[1] . "' and User_uid = '" . $UserID . "'");
                if ($results_tot_late = mysqli_fetch_assoc($check_Tot_Late)) 
                {
                    $TOTAL_LATE_MIN = $results_tot_late["Total_Late_Min"];
                }

                if ($TOTAL_LATE_MIN > 60) 
                {
                   $query = "insert into employee_leave(date,uid,type,days,time_slot,is_att_leave) values('" . $Date . "','" . $UserID . "','Nopay Leave','1','','1')"; 
                       $res = SUD($query);
                }
                else
                {
                    if ($lateMints > 15 && $lateMints < 30) 
                    {
                        if ($Available_Short == "0") 
                        {
                            $query = "insert into employee_leave(date,uid,type,days,time_slot,is_att_leave) values('" . $Date . "','" . $UserID . "','Nopay Leave','1','','1')"; 
                            $res = SUD($query);
                        }
                        else
                        {
                            $query = "insert into employee_leave(date,uid,type,days,time_slot,is_att_leave) values('" . $Date . "','" . $UserID . "','Short Morning Leave','0.25','','1')"; 
                            $res = SUD($query);

                            $IS_HALF = 0;
                            $IS_SHORT = 1;
                        }
                    }
                    else if ($lateMints > 30) 
                    {
                        if ($Available_Leave_Half_OR_CASUAL == "0") 
                        {
                           $query = "insert into employee_leave(date,uid,type,days,time_slot,is_att_leave) values('" . $Date . "','" . $UserID . "','Nopay Leave','1','','1')"; 
                            $res = SUD($query);
                        }
                        else
                        {
                            $query = "insert into employee_leave(date,uid,type,days,time_slot,is_att_leave) values('" . $Date . "','" . $UserID . "','Halfday Morning Leave','0.5','','1')"; 
                            $res = SUD($query);

                            $IS_HALF = 1;
                            $IS_SHORT = 0;
                        }
                    }
                } 
            }

            $static_morning_OT = $MOThours;

            $resCheckDoubleDate = Search("select * from attendance where User_uid = '" . $UserID . "' and date = '".$Date."' and intime = '".$Time."'");
            if ($resultCheckDoubleDate = mysqli_fetch_assoc($resCheckDoubleDate)) 
            {
                 
            }
            else
            {
                $InsertQuery = "insert into attendance(date,intime,attendance,User_uid,attendanceType_atid,late_att_min,othours,halfday, shortleave) values('" . $Date . "','" . $Time . "','1','" . $UserID . "','1','" . $lateMints . "','" . $MOThours . "','" . $IS_HALF . "','" . $IS_SHORT . "')";
                SUD($InsertQuery);
            }
        }
        elseif($Action == "C/Out") //Action = OUT (Outtime Calaculation)~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        { 
            $OUTTIME = date("A", strtotime($Time));
            
            if ($OUTTIME == "AM") 
            {

                $queryInDate = "select date from attendance where date = '" . $Date . "' and User_uid = '" . $UserID . "'";
                $resInDate = Search($queryInDate);
                if ($resultInDate = mysqli_fetch_assoc($resInDate)) 
                {
                    $query = "select aid,intime,othours,late_att_min from attendance where date = '" . $Date . "' and User_uid = '" . $UserID . "'";
                    $res = Search($query);
                    if ($result = mysqli_fetch_assoc($res)) 
                    {
                        if ($result["intime"] != "") 
                        {
                            // $T8Mng = date("H:i:s", strtotime("08:30 AM"));
                            $T8Mng = date("H:i:s", strtotime(GetShiftIntime($Req_Shift_Typ_ID)));
                            $hours = getTimeDifference($Date, $T8Mng, $Date, $Time);
                            $static_morning_OT = $result["othours"];
                            $Late_MIN_DATA = $result["late_att_min"];
                            $timeIN24HR = date("H:i:s", strtotime($Time));
                            

                            $T8Mng = date("H:i:s", strtotime(GetShiftIntime($Req_Shift_Typ_ID)));
                            $T5Eve = date("H:i:s", strtotime(GetShiftOuttime($Req_Shift_Typ_ID)));

                            $T130Aftn = date("H:i:s", strtotime(GetShiftHalfMorningOuttime($Req_Shift_Typ_ID)));
                            // if intime not between 8.30 and 1.30 no deduction fro lunch hour
                            if($T8Mng < $timeIN24HR && $T130Aftn > $timeIN24HR)
                            {
                             
                            }
                            else
                            {
                                $hourd_pre_ded_intavels = $hours;
                                $hours = $hours - 0.5; //Lunch Time Duration
                            }

                            $hours = number_format(floatval($hours),2);

                            $Att_DATE = explode('-', $Date);
                            $check_Tot_WORK = Search("select sum(hours) as Total_WORK_HRS from attendance where YEAR(date) = '" . $Att_DATE[0] . "' and MONTH(date) = '" . $Att_DATE[1] . "' and User_uid = '" . $UserID . "'");
                            if ($results_tot_work = mysqli_fetch_assoc($check_Tot_WORK)) 
                            {
                                $TOTAL_WORK_HRS = $results_tot_work["Total_WORK_HRS"];
                            }

                            $othAR = explode(".", number_format($TOTAL_WORK_HRS,2));

                            $TOT_OTH = $othAR[0];
                            $TOT_OTM = $othAR[1];

                            //Calculate OT Value
                            $TOT_MinsOT = ($TOT_OTH*60) + $TOT_OTM;
                            $TOT_WORKvalue = floor($TOT_MinsOT/60);
                            
                            if ($TOT_WORKvalue > 200) 
                            {
                                if($T5Eve < $timeIN24HR){
                                    $OTfromPeriod = getTimeDifference($Date, $T5Eve, $Date, $Time);
                                    $OTfromPeriod = number_format(floatval($OTfromPeriod),2);
                                }
                            }
                            else
                            {
                                $OTfromPeriod = 0;
                            }


                            $LeaveType = "";
                            $Slot = "";
                            $queryHalfShortCheck = "select type,time_slot,days from employee_leave where date = '" . $Date . "' and uid = '" . $UserID . "' and (type like 'Halfday Evening Leave' or type like 'Short Evening Leave' or type like 'Nopay Evening Leave' or type like 'Duty Evening Leave') and is_att_leave = '0'";
                            $resHalfShortCheck = Search($queryHalfShortCheck);
                            if ($resultHalfShortCheck = mysqli_fetch_assoc($resHalfShortCheck)) 
                            {
                                $LeaveType =  $resultHalfShortCheck["type"];
                                $Slot =  $resultHalfShortCheck["time_slot"];
                                $DAYS =  $resultHalfShortCheck["days"];

                                if ($LeaveType == "Halfday Evening Leave") 
                                {
                                    // if (strtotime($Time) < strtotime("13:00:00"))
                                    if (strtotime($Time) < strtotime(GetShiftHalfELate($Req_Shift_Typ_ID))) 
                                    {
                                        // $lateMintsData = (strtotime("13:00:00") - strtotime($Time))/60;
                                        $lateMintsData = (strtotime(GetShiftHalfELate($Req_Shift_Typ_ID)) - strtotime($Time))/60;
                                    }
                                    else
                                    {
                                        $lateMintsData = 0;
                                    }

                                    $UpdateQuery = "update attendance set halfday='1' where aid = '" . $result["aid"] . "'";
                                    SUD($UpdateQuery);

                                    $OT_to_save = $static_morning_OT + $OTfromPeriod;
                                }
                                else if ($LeaveType == "Nopay Evening Leave" || $LeaveType == "Duty Evening Leave") 
                                {
                                    // if (strtotime($Time) < strtotime("13:00:00"))
                                    if (strtotime($Time) < strtotime(GetShiftHalfELate($Req_Shift_Typ_ID))) 
                                    {
                                        // $lateMintsData = (strtotime("13:00:00") - strtotime($Time))/60;
                                        $lateMintsData = (strtotime(GetShiftHalfELate($Req_Shift_Typ_ID)) - strtotime($Time))/60;
                                    }
                                    else
                                    {
                                        $lateMintsData = 0;
                                    }

                                    $OT_to_save = $static_morning_OT + $OTfromPeriod;
                                }
                                else if ($LeaveType == "Short Evening Leave") 
                                {
                                    if (strtotime($Time) < strtotime(GetShiftShortELate($Req_Shift_Typ_ID))) 
                                    {
                                        // $lateMintsData = (strtotime("16:00:00") - strtotime($Time))/60;
                                        $lateMintsData = (strtotime(GetShiftShortELate($Req_Shift_Typ_ID)) - strtotime($Time))/60;
                                    }
                                    else
                                    {
                                        $lateMintsData = 0;
                                    }

                                    $UpdateQuery = "update attendance set shortleave='1' where aid = '" . $result["aid"] . "'";
                                    SUD($UpdateQuery);

                                    $OT_to_save = $static_morning_OT + $OTfromPeriod;
                                }
                                else
                                {
                                    if (strtotime($Time) < strtotime(GetShiftOuttime($Req_Shift_Typ_ID)))  
                                    {
                                        // $lateMintsData = (strtotime("17:30:00") - strtotime($Time))/60;
                                        $lateMintsData = (strtotime(GetShiftOuttime($Req_Shift_Typ_ID)) - strtotime($Time))/60;
                                    }
                                    else
                                    {
                                        $lateMintsData = 0;
                                    }

                                    $OT_to_save = $static_morning_OT + $OTfromPeriod;
                                }   
                            }
                            else
                            {
                                // if (strtotime($Time) < strtotime("17:30:00"))
                                if (strtotime($Time) < strtotime(GetShiftOuttime($Req_Shift_Typ_ID)))  
                                {
                                    // $lateMintsData = (strtotime("17:30:00") - strtotime($Time))/60;
                                    $lateMintsData = (strtotime(GetShiftOuttime($Req_Shift_Typ_ID)) - strtotime($Time))/60;
                                }
                                else
                                {
                                    $lateMintsData = 0;
                                }

                                $OT_to_save = $static_morning_OT + $OTfromPeriod;

                            }


                            $queryEmpType = "select EmployeeType_etid from user where uid = '" . $UserID . "' and isactive='1'";
                            $resEmpType = Search($queryEmpType);
                            if ($resultEmpType = mysqli_fetch_assoc($resEmpType)) 
                            {
                                 if ($resultEmpType["EmployeeType_etid"] == "1") 
                                 {
                                    $OT_to_save = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "2") 
                                 {
                                    $OT_to_save = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "3") 
                                 {
                                    $OT_to_save = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "4") 
                                 {
                                    $OT_to_save = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "5") 
                                 {
                                    $OT_to_save = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "6") 
                                 {
                                    $OT_to_save = 0;
                                 }
                            }
                            
                            $New_Late_Min = $Late_MIN_DATA + $lateMintsData; 

                            $resCheckDoubleDate = Search("select * from attendance where User_uid = '" . $UserID . "' and date = '".$Date."' and outtime = '".$Time."'");
                            if ($resultCheckDoubleDate = mysqli_fetch_assoc($resCheckDoubleDate)) 
                            {
                                 
                            }
                            else
                            {
                                $UpdateQuery = "update attendance set outtime='" . $Time . "', hours = '" . $hours . "', othours = '" . $OT_to_save . "',late_att_min = '".$New_Late_Min."'  where aid = '" . $result["aid"] . "'";
                                SUD($UpdateQuery);
                            } 

                    
                        }
                        
                    }
                }
                else
                {
                    //check earlier date checked out exists (for OT and DOT calculation)
                    //get earlier date
                    $ydate = strtotime($Date);
                    $ydate = strtotime("-1 day", $ydate);
                    $ydate = date('y-m-d', $ydate);

                    $y = strtotime($ydate);
                    $y = date('l', $y);

                    //if date is Saturday 
                    $saturdays = false;
                    if ($y == "Saturday") {
                        $saturdays = true;
                    }

                    $query = "select aid,intime,outtime from attendance where date = '" . $ydate . "' and User_uid = '" . $UserID . "'";
                    $res = Search($query);
                    if ($result = mysqli_fetch_assoc($res)) {

                        if($result["outtime"] == "")
                        {
                            // $earlyDateIn = date("H:i:s", strtotime("08:30 AM"));
                            $earlyDateIn = date("H:i:s", strtotime(GetShiftIntime($Req_Shift_Typ_ID)));
                            $hours = getTimeDifference($ydate, $earlyDateIn, $Date, $Time);
                            
                            $timeIN24HR = date("H:i:s", strtotime($Time));
                            $T130Aftn = date("H:i:s", strtotime(GetShiftHalfMorningOuttime($Req_Shift_Typ_ID)));
                            // if intime not between 8.30 and 1.30 no deduction fro lunch hour
                            if($earlyDateIn < $timeIN24HR && $T130Aftn > $timeIN24HR)
                            {
                             
                            }
                            else
                            {
                                $hourd_pre_ded_intavels = $hours;
                                $hours = $hours - 0.5; //Lunch Time Duration
                            }



                            $hours = number_format(floatval($hours),2);

                            $Att_DATE = explode('-', $Date);
                            $check_Tot_WORK = Search("select sum(hours) as Total_WORK_HRS from attendance where YEAR(date) = '" . $Att_DATE[0] . "' and MONTH(date) = '" . $Att_DATE[1] . "' and User_uid = '" . $UserID . "'");
                            if ($results_tot_work = mysqli_fetch_assoc($check_Tot_WORK)) 
                            {
                                $TOTAL_WORK_HRS = $results_tot_work["Total_WORK_HRS"];
                            }

                            $othAR = explode(".", number_format($TOTAL_WORK_HRS,2));

                            $TOT_OTH = $othAR[0];
                            $TOT_OTM = $othAR[1];
                            //Calculate OT Value
                            $TOT_MinsOT = ($TOT_OTH*60) + $TOT_OTM;
                            $TOT_WORKvalue = floor($TOT_MinsOT/60);
                            
                            if ($TOT_WORKvalue > 200) 
                            {
                                $OThours = getTimeDifference($ydate, $T5Eve, $Date, $Time);
                                $OThours = number_format(floatval($OThours),2);
                            }
                            else
                            {
                                $OThours = 0;//NEW PART
                            }

                            $queryEmpType = "select EmployeeType_etid from user where uid = '" . $UserID . "' and isactive='1'";
                            $resEmpType = Search($queryEmpType);
                            if ($resultEmpType = mysqli_fetch_assoc($resEmpType)) 
                            {
                                 if ($resultEmpType["EmployeeType_etid"] == "1") 
                                 {
                                    $OThours = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "2") 
                                 {
                                    $OThours = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "3") 
                                 {
                                    $OThours = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "4") 
                                 {
                                    $OThours = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "5") 
                                 {
                                    $OThours = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "6") 
                                 {
                                    $OThours = 0;
                                 }
                            }


                            $resCheckDoubleDate = Search("select * from attendance where User_uid = '" . $UserID . "' and date = '".$Date."' and outtime = '".$Time."'");
                            if ($resultCheckDoubleDate = mysqli_fetch_assoc($resCheckDoubleDate)) 
                            {
                                 
                            }
                            else
                            {
                                $UpdateQuery = "update attendance set date = '".$ydate."', outtime='" . $Time . "',attendance='1',User_uid = '".$UserID."',attendanceType_atid = '1', hours = '" . $hours . "', othours = '" . $OThours . "'  where aid = '" . $result["aid"] . "'";
                                SUD($UpdateQuery);
                            }

                        }
                        else
                        {
                            //no clue of the out time (earlier date out exists, current date in missing)
                        }

                    }
                }        
        
            }
            else
            {

                $query = "select aid,intime,othours,late_att_min from attendance where date = '" . $Date . "' and User_uid = '" . $UserID . "'";
                $res = Search($query);
                if ($result = mysqli_fetch_assoc($res)) 
                {
                    if ($result["intime"] != "") 
                    {
                        // $T8Mng = date("H:i:s", strtotime("08:30 AM"));
                        $T8Mng = date("H:i:s", strtotime(GetShiftIntime($Req_Shift_Typ_ID)));
                        $hours = getTimeDifference($Date, $T8Mng, $Date, $Time);

                        $static_morning_OT = $result["othours"];
                        $Late_MIN_DATA = $result["late_att_min"];

                    // echo "Raw Hours ".$hours;

                        //remove Lunch Hour if out time is before 1:30 afternoon
                        $timeIN24HR = date("H:i:s", strtotime($Time));
                        // $T8Mng = date("H:i:s", strtotime("08:30 AM"));
                        $T8Mng = date("H:i:s", strtotime(GetShiftIntime($Req_Shift_Typ_ID)));
                        // $T5Eve = date("H:i:s", strtotime("05:30 PM")); //change 5 to 5.30 (2022/06/13)
                        $T5Eve = date("H:i:s", strtotime(GetShiftOuttime($Req_Shift_Typ_ID))); //change 5 to 5.30 (2022/06/13)


                        $T130Aftn = date("H:i:s", strtotime(GetShiftHalfMorningOuttime($Req_Shift_Typ_ID)));
                        // if intime not between 8.30 and 1.30 no deduction fro lunch hour
                        if($T8Mng < $timeIN24HR && $T130Aftn > $timeIN24HR)
                        {
                         
                        }
                        else
                        {
                            $hourd_pre_ded_intavels = $hours;
                            $hours = $hours - 0.5; //Lunch Time Duration
                        }

                        $hours = number_format(floatval($hours),2);

                        $Att_DATE = explode('-', $Date);
                        $check_Tot_WORK = Search("select sum(hours) as Total_WORK_HRS from attendance where YEAR(date) = '" . $Att_DATE[0] . "' and MONTH(date) = '" . $Att_DATE[1] . "' and User_uid = '" . $UserID . "'");
                        if ($results_tot_work = mysqli_fetch_assoc($check_Tot_WORK)) 
                        {
                            $TOTAL_WORK_HRS = $results_tot_work["Total_WORK_HRS"];
                        }

                        $othAR = explode(".", number_format($TOTAL_WORK_HRS,2));

                        $TOT_OTH = $othAR[0];
                        $TOT_OTM = $othAR[1];
                        //Calculate OT Value
                        $TOT_MinsOT = ($TOT_OTH*60) + $TOT_OTM;
                        $TOT_WORKvalue = floor($TOT_MinsOT/60);
                        
                        if ($TOT_WORKvalue > 200) 
                        {
                            if($T5Eve < $timeIN24HR){
                                $OTfromPeriod = getTimeDifference($Date, $T5Eve, $Date, $Time);
                                $OTfromPeriod = number_format(floatval($OTfromPeriod),2);
                            }
                        }
                        else
                        {
                            $OTfromPeriod = 0;
                        }

                        $LeaveType = "";
                        $Slot = "";
                        $queryHalfShortCheck = "select type,time_slot,days from employee_leave where date = '" . $Date . "' and uid = '" . $UserID . "' and (type like 'Halfday Evening Leave' or type like 'Short Evening Leave' or type like 'Nopay Evening Leave' or type like 'Duty Evening Leave') and is_att_leave = '0'";
                        $resHalfShortCheck = Search($queryHalfShortCheck);
                        if ($resultHalfShortCheck = mysqli_fetch_assoc($resHalfShortCheck)) 
                        {
                            $LeaveType =  $resultHalfShortCheck["type"];
                            $Slot =  $resultHalfShortCheck["time_slot"];
                            $DAYS =  $resultHalfShortCheck["days"];

                             if ($LeaveType == "Halfday Evening Leave") 
                             {
                                if (strtotime($Time) < strtotime(GetShiftHalfELate($Req_Shift_Typ_ID))) 
                                {
                                    // $lateMintsData = (strtotime("13:00:00") - strtotime($Time))/60;
                                    $lateMintsData = (strtotime(GetShiftHalfELate($Req_Shift_Typ_ID)) - strtotime($Time))/60;
                                }
                                else
                                {
                                    $lateMintsData = 0;
                                }

                                $UpdateQuery = "update attendance set halfday='1' where aid = '" . $result["aid"] . "'";
                                SUD($UpdateQuery);

                                $OT_to_save = $static_morning_OT + $OTfromPeriod;

                             }
                             else if ($LeaveType == "Nopay Evening Leave" || $LeaveType == "Duty Evening Leave") 
                             {
                                if (strtotime($Time) < strtotime(GetShiftHalfELate($Req_Shift_Typ_ID))) 
                                {
                                    // $lateMintsData = (strtotime("13:00:00") - strtotime($Time))/60;
                                    $lateMintsData = (strtotime(GetShiftHalfELate($Req_Shift_Typ_ID)) - strtotime($Time))/60;
                                }
                                else
                                {
                                    $lateMintsData = 0;
                                }

                                $OT_to_save = $static_morning_OT + $OTfromPeriod;

                             }
                             else if ($LeaveType == "Short Evening Leave") 
                             {
                                if (strtotime($Time) < strtotime(GetShiftShortELate($Req_Shift_Typ_ID))) 
                                {
                                    // $lateMintsData = (strtotime("16:00:00") - strtotime($Time))/60;
                                    $lateMintsData = (strtotime(GetShiftShortELate($Req_Shift_Typ_ID)) - strtotime($Time))/60;
                                }
                                else
                                {
                                    $lateMintsData = 0;
                                }

                                $UpdateQuery = "update attendance set shortleave='1' where aid = '" . $result["aid"] . "'";
                                SUD($UpdateQuery);

                                $OT_to_save = $static_morning_OT + $OTfromPeriod;
                             }
                             else
                             {
                                if (strtotime($Time) < strtotime(GetShiftOuttime($Req_Shift_Typ_ID)))  
                                {
                                    // $lateMintsData = (strtotime("17:30:00") - strtotime($Time))/60;
                                    $lateMintsData = (strtotime(GetShiftOuttime($Req_Shift_Typ_ID)) - strtotime($Time))/60;
                                }
                                else
                                {
                                    $lateMintsData = 0;
                                }

                                $OT_to_save = $static_morning_OT + $OTfromPeriod;
                             }  
                        }
                        else
                        {
                            // if (strtotime($Time) < strtotime("17:30:00"))
                            if (strtotime($Time) < strtotime(GetShiftOuttime($Req_Shift_Typ_ID)))  
                            {
                                // $lateMintsData = (strtotime("17:30:00") - strtotime($Time))/60;
                                $lateMintsData = (strtotime(GetShiftOuttime($Req_Shift_Typ_ID)) - strtotime($Time))/60;
                            }
                            else
                            {
                                $lateMintsData = 0;
                            }

                            $OT_to_save = $static_morning_OT + $OTfromPeriod;
                        }


                        $queryEmpType = "select EmployeeType_etid from user where uid = '" . $UserID . "' and isactive='1'";
                        $resEmpType = Search($queryEmpType);
                        if ($resultEmpType = mysqli_fetch_assoc($resEmpType)) 
                        {
                             if ($resultEmpType["EmployeeType_etid"] == "1") 
                             {
                                $OT_to_save = 0;
                             }
                             else if ($resultEmpType["EmployeeType_etid"] == "2") 
                             {
                                $OT_to_save = 0;
                             }
                             else if ($resultEmpType["EmployeeType_etid"] == "3") 
                             {
                                $OT_to_save = 0;
                             }
                             else if ($resultEmpType["EmployeeType_etid"] == "4") 
                             {
                                $OT_to_save = 0;
                             }
                             else if ($resultEmpType["EmployeeType_etid"] == "5") 
                             {
                                $OT_to_save = 0;
                             }
                             else if ($resultEmpType["EmployeeType_etid"] == "6") 
                             {
                                $OT_to_save = 0;
                             }
                        }
                        
                        $New_Late_Min = $Late_MIN_DATA + $lateMintsData; 

                        $resCheckDoubleDate = Search("select * from attendance where User_uid = '" . $UserID . "' and date = '".$Date."' and outtime = '".$Time."'");
                        if ($resultCheckDoubleDate = mysqli_fetch_assoc($resCheckDoubleDate)) 
                        {
                             
                        }
                        else
                        {
                            $UpdateQuery = "update attendance set outtime='" . $Time . "', hours = '" . $hours . "', othours = '" . $OT_to_save . "',late_att_min = '".$New_Late_Min."'  where aid = '" . $result["aid"] . "'";
                            SUD($UpdateQuery);
                        }  
                    }
                    
                }
            } 
        }
    }
    else
    {
        //===================OFFICE STAFF================================================
        // Action = In (Intime Calculation)
        if($Action == "C/In")
        {
            $MOThours = 0;
            $is_half = 0;
            $is_short = 0;
            $LeaveType = "";
            $Slot = "";
            $queryHalfShortCheck = "select type,time_slot,days from employee_leave where date = '" . $Date . "' and uid = '" . $UserID . "' and (type like 'Halfday Morning Leave' or type like 'Short Morning Leave' or type like 'Nopay Morning Leave' or type like 'Duty Morning Leave') and is_att_leave = '0'";
            $resHalfShortCheck = Search($queryHalfShortCheck);
            if ($resultHalfShortCheck = mysqli_fetch_assoc($resHalfShortCheck)) 
            {
                $LeaveType =  $resultHalfShortCheck["type"];
                $Slot =  $resultHalfShortCheck["time_slot"];
                $DAYS =  $resultHalfShortCheck["days"];

                 if ($LeaveType == "Halfday Morning Leave") 
                 {   
                    if (strtotime($Time) > strtotime(GetHalfMLate($UserID))) 
                    {
                        $lateMints = (strtotime($Time) - strtotime(GetHalfMLate($UserID)))/60;
                    }
                    else
                    {
                        $lateMints = 0;
                    }

                    $is_half = 1;
                    $is_short = 0;
                     
                 }
                 else if ($LeaveType == "Nopay Morning Leave" || $LeaveType == "Duty Morning Leave") 
                 {   
                    if (strtotime($Time) > strtotime(GetHalfMLate($UserID))) 
                    {
                        $lateMints = (strtotime($Time) - strtotime(GetHalfMLate($UserID)))/60;
                    }
                    else
                    {
                        $lateMints = 0;
                    }

                    $is_half = 0;
                    $is_short = 0;
                     
                 }
                 else if ($LeaveType == "Short Morning Leave") 
                 {
                    if (strtotime($Time) > strtotime(GetShortMLate($UserID))) 
                    {
                        $lateMints = (strtotime($Time) - strtotime(GetShortMLate($UserID)))/60;
                    }
                    else
                    {
                        $lateMints = 0;
                    }

                    $is_half = 0;
                    $is_short = 1;
                 }
                 else
                 {
                    if (strtotime($Time) > strtotime(GetWorkingWeekLate($UserID)))     
                    {
                        $lateMints = (strtotime($Time) - strtotime(GetWorkingIntimeWeek($UserID)))/60;
                    }
                    else
                    {
                        $lateMints = 0;                     
                    }

                    $is_half = 0;
                    $is_short = 0;
                 }   
            }
            else
            {
                if (strtotime($Time) > strtotime(GetWorkingWeekLate($UserID)))     
                {
                    $lateMints = (strtotime($Time) - strtotime(GetWorkingIntimeWeek($UserID)))/60;
                }
                else
                {
                    $lateMints = 0;                     
                }

                $is_half = 0;
                $is_short = 0;
            }


            if ($lateMints > 0) 
            {
                $Att_DATE = explode('-', $Date);
                $check_Tot_Late = Search("select sum(late_att_min) as Total_Late_Min from attendance where YEAR(date) = '" . $Att_DATE[0] . "' and MONTH(date) = '" . $Att_DATE[1] . "' and User_uid = '" . $UserID . "'");
                if ($results_tot_late = mysqli_fetch_assoc($check_Tot_Late)) 
                {
                    $TOTAL_LATE_MIN = $results_tot_late["Total_Late_Min"];
                }

                if ($TOTAL_LATE_MIN > 60) 
                {
                   $query = "insert into employee_leave(date,uid,type,days,time_slot,is_att_leave) values('" . $Date . "','" . $UserID . "','Nopay Leave','1','','1')"; 
                       $res = SUD($query);
                }
                else
                {
                    if ($lateMints > 15 && $lateMints < 30) 
                    {
                        if ($Available_Short == "0") 
                        {
                            $query = "insert into employee_leave(date,uid,type,days,time_slot,is_att_leave) values('" . $Date . "','" . $UserID . "','Nopay Leave','1','','1')"; 
                            $res = SUD($query);
                        }
                        else
                        {
                            $query = "insert into employee_leave(date,uid,type,days,time_slot,is_att_leave) values('" . $Date . "','" . $UserID . "','Short Morning Leave','0.25','','1')"; 
                            $res = SUD($query);

                            $is_half = 0;
                            $is_short = 1;
                        }
                    }
                    else if ($lateMints > 30) 
                    {
                        if ($Available_Leave_Half_OR_CASUAL == "0") 
                        {
                           $query = "insert into employee_leave(date,uid,type,days,time_slot,is_att_leave) values('" . $Date . "','" . $UserID . "','Nopay Leave','1','','1')"; 
                            $res = SUD($query);
                        }
                        else
                        {
                            $query = "insert into employee_leave(date,uid,type,days,time_slot,is_att_leave) values('" . $Date . "','" . $UserID . "','Halfday Morning Leave','0.5','','1')"; 
                            $res = SUD($query);

                            $is_half = 1;
                            $is_short = 0;
                        }
                    }
                }
            }

            $static_morning_OT = $MOThours;

            $resCheckDoubleDate = Search("select * from attendance where User_uid = '" . $UserID . "' and date = '".$Date."' and intime = '".$Time."'");
            if ($resultCheckDoubleDate = mysqli_fetch_assoc($resCheckDoubleDate)) 
            {
                 
            }
            else
            {
                $InsertQuery = "insert into attendance(date,intime,attendance,User_uid,attendanceType_atid,late_att_min,othours,halfday, shortleave) values('" . $Date . "','" . $Time . "','1','" . $UserID . "','1','" . $lateMints . "','" . $MOThours . "','" . $is_half . "','" . $is_short . "')";
                SUD($InsertQuery);
            }

        }
        elseif($Action == "C/Out") //Action = OUT (Outtime Calaculation)~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        { 
            $OUTTIME = date("A", strtotime($Time));
            
            if ($OUTTIME == "AM") 
            {
                $queryInDate = "select date from attendance where date = '" . $Date . "' and User_uid = '" . $UserID . "'";
                $resInDate = Search($queryInDate);
                if ($resultInDate = mysqli_fetch_assoc($resInDate)) 
                {
                    $query = "select aid,intime,othours,late_att_min from attendance where date = '" . $Date . "' and User_uid = '" . $UserID . "'";
                    $res = Search($query);
                    if ($result = mysqli_fetch_assoc($res)) 
                    {
                        if ($result["intime"] != "") 
                        {
                            // $T8Mng = date("H:i:s", strtotime("08:30 AM"));
                            $T8Mng = date("H:i:s", strtotime(GetWorkingIntimeWeek($UserID)));
                            $hours = getTimeDifference($Date, $T8Mng, $Date, $Time);

                            $static_morning_OT = $result["othours"];
                            $Late_MIN_DATA = $result["late_att_min"];

                            // echo "Raw Hours ".$hours;

                            //remove Lunch Hour if out time is before 1:30 afternoon
                            $timeIN24HR = date("H:i:s", strtotime($Time));
                            // $T8Mng = date("H:i:s", strtotime("08:30 AM"));
                            $T8Mng = date("H:i:s", strtotime(GetWorkingIntimeWeek($UserID)));
                            $T5Eve = date("H:i:s", strtotime(GetWorkingOuttimeWeek($UserID))); //change 5 to 5.30 (2022/06/13)
                            // $T5Eve = date("H:i:s", strtotime("05:30 PM")); //change 5 to 5.30 (2022/06/13)

                            $T130Aftn = date("H:i:s", strtotime(GetHalfMorningOuttime($UserID)));
                            // if intime not between 8.30 and 1.30 no deduction fro lunch hour
                            if($T8Mng < $timeIN24HR && $T130Aftn > $timeIN24HR)
                            {
                             
                            }
                            else
                            {
                                $hourd_pre_ded_intavels = $hours;
                                $hours = $hours - 0.5; //Lunch Time Duration
                            } 
                            

                            $hours = number_format(floatval($hours),2);

                            $Att_DATE = explode('-', $Date);
                            $check_Tot_WORK = Search("select sum(hours) as Total_WORK_HRS from attendance where YEAR(date) = '" . $Att_DATE[0] . "' and MONTH(date) = '" . $Att_DATE[1] . "' and User_uid = '" . $UserID . "'");
                            if ($results_tot_work = mysqli_fetch_assoc($check_Tot_WORK)) 
                            {
                                $TOTAL_WORK_HRS = $results_tot_work["Total_WORK_HRS"];
                            }

                            $othAR = explode(".", number_format($TOTAL_WORK_HRS,2));

                            $TOT_OTH = $othAR[0];
                            $TOT_OTM = $othAR[1];
                            //Calculate OT Value
                            $TOT_MinsOT = ($TOT_OTH*60) + $TOT_OTM;
                            $TOT_WORKvalue = floor($TOT_MinsOT/60);
                            
                            if ($TOT_WORKvalue > 200) 
                            {
                                if($T5Eve < $timeIN24HR){
                                    $OTfromPeriod = getTimeDifference($Date, $T5Eve, $Date, $Time);
                                    $OTfromPeriod = number_format(floatval($OTfromPeriod),2);
                                }
                            }
                            else
                            {
                                $OTfromPeriod = 0;
                            }

                            // new saving ~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                            if($saturday)
                            {
                               //from 8.30 normal hrs , from 1.30 OT hours
                               // echo "<br/>Saturday~~~~~~~~~~~~~~~~~<br/>";
                                $T830 = "08:30";
                                $T130 = "13:30";

                                $TH830 = date("H:i:s", strtotime("08:00 AM"));
                                // $TH130 = date("H:i:s", strtotime("01:30 PM"));
                                $TH130 = date("H:i:s", strtotime(GetWorkingOuttimeWeekends($UserID)));
                                // $TH145 = date("H:i:s", strtotime("01:45 PM"));
                                $TH145 = date("H:i:s", strtotime(GetWorkingWeekEndsOT($UserID)));
                                $THIN = date("H:i:s", strtotime($result["intime"]));

                                if ($TOT_WORKvalue > 200) 
                                {
                                    $OTHours = getTimeDifference($Date, $TH130, $Date, $Time);
                                }
                                else
                                {
                                    $OTHours = 0;
                                }


                                // if (strtotime($Time) < strtotime("13:30:00"))
                                if (strtotime($Time) < strtotime(GetWorkingOuttimeWeekends($UserID)))  
                                {
                                   // $lateMintsData = (strtotime("13:30:00") - strtotime($Time))/60;
                                    $lateMintsData = (strtotime(GetWorkingOuttimeWeekends($UserID)) - strtotime($Time))/60;
                                }
                                else
                                {
                                    $lateMintsData = 0;
                                }


                                $queryEmpType = "select EmployeeType_etid from user where uid = '" . $UserID . "' and isactive='1'";
                                $resEmpType = Search($queryEmpType);
                                if ($resultEmpType = mysqli_fetch_assoc($resEmpType)) 
                                {
                                     if ($resultEmpType["EmployeeType_etid"] == "1") 
                                     {
                                        $OT_to_save = 0;
                                     }
                                     else if ($resultEmpType["EmployeeType_etid"] == "2") 
                                     {
                                        $OT_to_save = 0;
                                     }
                                     else if ($resultEmpType["EmployeeType_etid"] == "3") 
                                     {
                                        $OT_to_save = 0;
                                     }
                                     else if ($resultEmpType["EmployeeType_etid"] == "4") 
                                     {
                                        $OT_to_save = 0;
                                     }
                                     else if ($resultEmpType["EmployeeType_etid"] == "5") 
                                     {
                                        $OT_to_save = 0;
                                     }
                                     else if ($resultEmpType["EmployeeType_etid"] == "6") 
                                     {
                                        $OT_to_save = 0;
                                     }
                                     else
                                     {
                                        $OT_to_save = $static_morning_OT + $OTHours;
                                     }
                                }
                                
                                $New_Late_Min = $Late_MIN_DATA + $lateMintsData;


                                $resCheckDoubleDate = Search("select * from attendance where User_uid = '" . $UserID . "' and date = '".$Date."' and outtime = '".$Time."'");
                                if ($resultCheckDoubleDate = mysqli_fetch_assoc($resCheckDoubleDate)) 
                                {
                                     
                                }
                                else
                                {
                                    $UpdateQuery = "update attendance set hours='" . $hours . "', outtime='" . $Time . "',othours='" . $OT_to_save . "',late_att_min = '".$New_Late_Min."' where aid = '" . $result["aid"] . "'";
                                    SUD($UpdateQuery);
                                }

                            }
                            else
                            {

                                $LeaveType = "";
                                $Slot = "";
                                $queryHalfShortCheck = "select type,time_slot,days from employee_leave where date = '" . $Date . "' and uid = '" . $UserID . "' and (type like 'Halfday Evening Leave' or type like 'Short Evening Leave' or type like 'Nopay Evening Leave' or type like 'Duty Evening Leave') and is_att_leave = '0'";
                                $resHalfShortCheck = Search($queryHalfShortCheck);
                                if ($resultHalfShortCheck = mysqli_fetch_assoc($resHalfShortCheck)) 
                                {
                                    $LeaveType =  $resultHalfShortCheck["type"];
                                    $Slot =  $resultHalfShortCheck["time_slot"];
                                    $DAYS =  $resultHalfShortCheck["days"];

                                    if ($LeaveType == "Halfday Evening Leave") 
                                    {
                                        if (strtotime($Time) < strtotime(GetHalfELate($UserID))) 
                                        {
                                            $lateMintsData = (strtotime(GetHalfELate($UserID)) - strtotime($Time))/60;
                                        }
                                        else
                                        {
                                            $lateMintsData = 0;
                                        }

                                        $UpdateQuery = "update attendance set halfday='1' where aid = '" . $result["aid"] . "'";
                                        SUD($UpdateQuery);

                                        $OT_to_save = $static_morning_OT + $OTfromPeriod;
                                    }
                                    else if ($LeaveType == "Nopay Evening Leave" || $LeaveType == "Duty Evening Leave") 
                                    {
                                        if (strtotime($Time) < strtotime(GetHalfELate($UserID))) 
                                        {
                                            $lateMintsData = (strtotime(GetHalfELate($UserID)) - strtotime($Time))/60;
                                        }
                                        else
                                        {
                                            $lateMintsData = 0;
                                        }

                                        $OT_to_save = $static_morning_OT + $OTfromPeriod;
                                    }
                                    else if ($LeaveType == "Short Evening Leave") 
                                    {  
                                        if (strtotime($Time) < strtotime(GetShortELate($UserID))) 
                                        {
                                            $lateMintsData = (strtotime(GetShortELate($UserID)) - strtotime($Time))/60;
                                        }
                                        else
                                        {
                                            $lateMintsData = 0;
                                        }

                                        $UpdateQuery = "update attendance set shortleave='1' where aid = '" . $result["aid"] . "'";
                                        SUD($UpdateQuery);

                                        $OT_to_save = $static_morning_OT + $OTfromPeriod;
                                    }
                                    else
                                    {
                                        if (strtotime($Time) < strtotime(GetWorkingOuttimeWeek($UserID)))  
                                        {
                                            $lateMintsData = (strtotime(GetWorkingOuttimeWeek($UserID)) - strtotime($Time))/60;
                                        }
                                        else
                                        {
                                            $lateMintsData = 0;
                                        }

                                        $OT_to_save = $static_morning_OT + $OTfromPeriod;
                                    }   
                                }
                                else
                                {
                                    if (strtotime($Time) < strtotime(GetWorkingOuttimeWeek($UserID)))  
                                    {
                                        $lateMintsData = (strtotime(GetWorkingOuttimeWeek($UserID)) - strtotime($Time))/60;
                                    }
                                    else
                                    {
                                        $lateMintsData = 0;
                                    }

                                    $OT_to_save = $static_morning_OT + $OTfromPeriod;
                                }

                                $queryEmpType = "select EmployeeType_etid from user where uid = '" . $UserID . "' and isactive='1'";
                                $resEmpType = Search($queryEmpType);
                                if ($resultEmpType = mysqli_fetch_assoc($resEmpType)) 
                                {
                                    if ($resultEmpType["EmployeeType_etid"] == "1") 
                                    {
                                        $OT_to_save = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "2") 
                                    {
                                        $OT_to_save = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "3") 
                                    {
                                        $OT_to_save = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "4") 
                                    {
                                        $OT_to_save = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "5") 
                                    {
                                        $OT_to_save = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "6") 
                                    {
                                        $OT_to_save = 0;
                                    }
                                }
                                
                                $New_Late_Min = $Late_MIN_DATA + $lateMintsData; 

                                $resCheckDoubleDate = Search("select * from attendance where User_uid = '" . $UserID . "' and date = '".$Date."' and outtime = '".$Time."'");
                                if ($resultCheckDoubleDate = mysqli_fetch_assoc($resCheckDoubleDate)) 
                                {
                                     
                                }
                                else
                                {
                                    $UpdateQuery = "update attendance set outtime='" . $Time . "', hours = '" . $hours . "', othours = '" . $OT_to_save . "',late_att_min = '".$New_Late_Min."'  where aid = '" . $result["aid"] . "'";
                                    SUD($UpdateQuery);
                                } 
                                
                            }

                        }
                        
                    }
                }
                else
                {
                    //check earlier date checked out exists (for OT and DOT calculation)
                    //get earlier date
                    $ydate = strtotime($Date);
                    $ydate = strtotime("-1 day", $ydate);
                    $ydate = date('y-m-d', $ydate);

                    $y = strtotime($ydate);
                    $y = date('l', $y);

                    //if date is Saturday 
                    $saturdays = false;
                    if ($y == "Saturday") 
                    {
                        $saturdays = true;
                    }


                    $query = "select aid,intime,outtime from attendance where date = '" . $ydate . "' and User_uid = '" . $UserID . "'";
                    $res = Search($query);
                    if ($result = mysqli_fetch_assoc($res)) {

                        if($result["outtime"] == "")
                        {
                            // $earlyDateIn = date("H:i:s", strtotime("08:30 AM"));
                            $earlyDateIn = date("H:i:s", strtotime(GetWorkingIntimeWeek($UserID)));
                            $hours = getTimeDifference($ydate, $earlyDateIn, $Date, $Time);
                            
                            $timeIN24HR = date("H:i:s", strtotime($Time));
                            $T130Aftn = date("H:i:s", strtotime(GetHalfMorningOuttime($UserID)));
                            // if intime not between 8.30 and 1.30 no deduction fro lunch hour
                            if($earlyDateIn < $timeIN24HR && $T130Aftn > $timeIN24HR)
                            {
                             
                            }
                            else
                            {
                                $hourd_pre_ded_intavels = $hours;
                                $hours = $hours - 0.5; //Lunch Time Duration
                            }


                            $hours = number_format(floatval($hours),2);

                            $Att_DATE = explode('-', $Date);
                            $check_Tot_WORK = Search("select sum(hours) as Total_WORK_HRS from attendance where YEAR(date) = '" . $Att_DATE[0] . "' and MONTH(date) = '" . $Att_DATE[1] . "' and User_uid = '" . $UserID . "'");
                            if ($results_tot_work = mysqli_fetch_assoc($check_Tot_WORK)) 
                            {
                                $TOTAL_WORK_HRS = $results_tot_work["Total_WORK_HRS"];
                            }

                            $othAR = explode(".", number_format($TOTAL_WORK_HRS,2));

                            $TOT_OTH = $othAR[0];
                            $TOT_OTM = $othAR[1];
                            //Calculate OT Value
                            $TOT_MinsOT = ($TOT_OTH*60) + $TOT_OTM;
                            $TOT_WORKvalue = floor($TOT_MinsOT/60);

                            if($saturdays)
                            {
                                $TH130 = date("H:i:s", strtotime(GetWorkingOuttimeWeekends($UserID)));
                                $TH12 = date("H:i:s", strtotime("12:00 AM"));
                                $GetTime = date("H:i:s", strtotime($Time));            //NEW PART

                                if ($TOT_WORKvalue > 200) 
                                {
                                    $OTHours = getTimeDifference($ydate, $TH130, $Date, $Time);//NEW PART
                                    $OTHours = number_format(floatval($OTHours),2);//NEW PART
                                }
                                else
                                {
                                    $OTHours = 0;//NEW PART
                                }

                                $queryEmpType = "select EmployeeType_etid from user where uid = '" . $UserID . "' and isactive='1'";
                                $resEmpType = Search($queryEmpType);
                                if ($resultEmpType = mysqli_fetch_assoc($resEmpType)) 
                                {
                                    if ($resultEmpType["EmployeeType_etid"] == "1") 
                                    {
                                        $OT_to_save = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "2") 
                                    {
                                        $OT_to_save = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "3") 
                                    {
                                        $OT_to_save = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "4") 
                                    {
                                        $OT_to_save = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "5") 
                                    {
                                        $OT_to_save = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "6") 
                                    {
                                        $OT_to_save = 0;
                                    }
                                    else
                                    {
                                        $OT_to_save = $static_morning_OT + $OTHours;
                                    }
                                }
                                
                                
                                $resCheckDoubleDate = Search("select * from attendance where User_uid = '" . $UserID . "' and date = '".$Date."' and outtime = '".$Time."'");
                                if ($resultCheckDoubleDate = mysqli_fetch_assoc($resCheckDoubleDate)) 
                                {
                                     
                                }
                                else
                                {
                                    $UpdateQuery = "update attendance set hours='" . $hours . "', outtime='" . $Time . "',othours='" . $OT_to_save . "',dothours='" . $DOTHours . "' where aid = '" . $result["aid"] . "'";
                                    SUD($UpdateQuery);
                                }

                            }
                            else
                            {
                                //outs from OT and DOT done
                                // $T5Eve = date("H:i:s", strtotime("05:30 PM")); //change 5 to 5.30 (2022/06/13)
                                $T5Eve = date("H:i:s", strtotime(GetWorkingOuttimeWeek($UserID))); //change 5 to 5.30 (2022/06/13)
                                if ($TOT_WORKvalue > 200) 
                                {
                                    $OThours = getTimeDifference($ydate, $T5Eve, $Date, $Time);
                                    $OThours = number_format(floatval($OThours),2);
                                }
                                else
                                {
                                    $OThours = 0;//NEW PART
                                }

                                $queryEmpType = "select EmployeeType_etid from user where uid = '" . $UserID . "' and isactive='1'";
                                $resEmpType = Search($queryEmpType);
                                if ($resultEmpType = mysqli_fetch_assoc($resEmpType)) 
                                {
                                    if ($resultEmpType["EmployeeType_etid"] == "1") 
                                    {
                                        $OThours = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "2") 
                                    {
                                        $OThours = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "3") 
                                    {
                                        $OThours = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "4") 
                                    {
                                        $OThours = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "5") 
                                    {
                                        $OThours = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "6") 
                                    {
                                        $OThours = 0;
                                    }
                                }


                                $resCheckDoubleDate = Search("select * from attendance where User_uid = '" . $UserID . "' and date = '".$Date."' and outtime = '".$Time."'");
                                if ($resultCheckDoubleDate = mysqli_fetch_assoc($resCheckDoubleDate)) 
                                {
                                     
                                }
                                else
                                {
                                    $UpdateQuery = "update attendance set date = '".$ydate."', outtime='" . $Time . "',attendance='1',User_uid = '".$UserID."',attendanceType_atid = '1', hours = '" . $hours . "', othours = '" . $OThours . "'  where aid = '" . $result["aid"] . "'";
                                    SUD($UpdateQuery);
                                }
                            }

                        }
                        else
                        {
                            //no clue of the out time (earlier date out exists, current date in missing)
                        }

                    }
                }        
        
            }
            else
            {

                $query = "select aid,intime,othours,late_att_min from attendance where date = '" . $Date . "' and User_uid = '" . $UserID . "'";
                $res = Search($query);
                if ($result = mysqli_fetch_assoc($res)) 
                {
                    if ($result["intime"] != "") 
                    {
                        // $T8Mng = date("H:i:s", strtotime("08:30 AM"));
                        $T8Mng = date("H:i:s", strtotime(GetWorkingIntimeWeek($UserID)));
                        $hours = getTimeDifference($Date, $T8Mng, $Date, $Time);

                        $static_morning_OT = $result["othours"];
                        $Late_MIN_DATA = $result["late_att_min"];

                    // echo "Raw Hours ".$hours;

                        //remove Lunch Hour if out time is before 1:30 afternoon
                        $timeIN24HR = date("H:i:s", strtotime($Time));
                        // $T8Mng = date("H:i:s", strtotime("08:30 AM"));
                        $T8Mng = date("H:i:s", strtotime(GetWorkingIntimeWeek($UserID)));
                        // $T5Eve = date("H:i:s", strtotime("05:30 PM")); //change 5 to 5.30 (2022/06/13)
                        $T5Eve = date("H:i:s", strtotime(GetWorkingOuttimeWeek($UserID))); //change 5 to 5.30 (2022/06/13)

                        $T130Aftn = date("H:i:s", strtotime(GetHalfMorningOuttime($UserID)));
                        // if intime not between 8.30 and 1.30 no deduction fro lunch hour
                        if($T8Mng < $timeIN24HR && $T130Aftn > $timeIN24HR)
                        {
                         
                        }
                        else
                        {
                            $hourd_pre_ded_intavels = $hours;
                            $hours = $hours - 0.5; //Lunch Time Duration
                        }

                        $hours = number_format(floatval($hours),2);

                        $Att_DATE = explode('-', $Date);
                        $check_Tot_WORK = Search("select sum(hours) as Total_WORK_HRS from attendance where YEAR(date) = '" . $Att_DATE[0] . "' and MONTH(date) = '" . $Att_DATE[1] . "' and User_uid = '" . $UserID . "'");
                        if ($results_tot_work = mysqli_fetch_assoc($check_Tot_WORK)) 
                        {
                            $TOTAL_WORK_HRS = $results_tot_work["Total_WORK_HRS"];
                        }

                        $othAR = explode(".", number_format($TOTAL_WORK_HRS,2));

                        $TOT_OTH = $othAR[0];
                        $TOT_OTM = $othAR[1];
                        //Calculate OT Value
                        $TOT_MinsOT = ($TOT_OTH*60) + $TOT_OTM;
                        $TOT_WORKvalue = floor($TOT_MinsOT/60);
                        
                        if ($TOT_WORKvalue > 200) 
                        {
                            if($T5Eve < $timeIN24HR){
                                $OTfromPeriod = getTimeDifference($Date, $T5Eve, $Date, $Time);
                                $OTfromPeriod = number_format(floatval($OTfromPeriod),2);
                            }
                        }
                        else
                        {
                            $OTfromPeriod = 0;
                        }


                        // new saving ~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                        if($saturday)
                        {
                           //from 8.30 normal hrs , from 1.30 OT hours
                           // echo "<br/>Saturday~~~~~~~~~~~~~~~~~<br/>";
                            $T830 = "08:30";
                            $T130 = "13:30";

                            $TH830 = date("H:i:s", strtotime("08:00 AM"));
                            // $TH130 = date("H:i:s", strtotime("01:30 PM"));
                            $TH130 = date("H:i:s", strtotime(GetWorkingOuttimeWeekends($UserID)));
                            // $TH145 = date("H:i:s", strtotime("01:45 PM"));
                            $TH145 = date("H:i:s", strtotime(GetWorkingWeekEndsOT($UserID)));
                            $THIN = date("H:i:s", strtotime($result["intime"]));

                            if ($TOT_WORKvalue > 200) 
                            {
                                $OTHours = getTimeDifference($Date, $TH130, $Date, $Time);
                            }
                            else
                            {
                                $OTHours = 0;
                            }

                            // if (strtotime($Time) < strtotime("13:30:00"))
                            if (strtotime($Time) < strtotime(GetWorkingOuttimeWeekends($UserID)))  
                            {
                                //$lateMintsData = (strtotime("13:30:00") - strtotime($Time))/60;
                                $lateMintsData = (strtotime(GetWorkingOuttimeWeekends($UserID)) - strtotime($Time))/60;
                            }
                            else
                            {
                                $lateMintsData = 0;
                            }


                            $queryEmpType = "select EmployeeType_etid from user where uid = '" . $UserID . "' and isactive='1'";
                            $resEmpType = Search($queryEmpType);
                            if ($resultEmpType = mysqli_fetch_assoc($resEmpType)) 
                            {
                                if ($resultEmpType["EmployeeType_etid"] == "1") 
                                {
                                    $OT_to_save = 0;
                                }
                                else if ($resultEmpType["EmployeeType_etid"] == "2") 
                                {
                                    $OT_to_save = 0;
                                }
                                else if ($resultEmpType["EmployeeType_etid"] == "3") 
                                {
                                    $OT_to_save = 0;
                                }
                                else if ($resultEmpType["EmployeeType_etid"] == "4") 
                                {
                                    $OT_to_save = 0;
                                }
                                else if ($resultEmpType["EmployeeType_etid"] == "5") 
                                {
                                    $OT_to_save = 0;
                                }
                                else if ($resultEmpType["EmployeeType_etid"] == "6") 
                                {
                                    $OT_to_save = 0;
                                }
                                else
                                {
                                    $OT_to_save = $static_morning_OT + $OTHours;
                                }
                            }
                            
                            $New_Late_Min = $Late_MIN_DATA + $lateMintsData;

                            $resCheckDoubleDate = Search("select * from attendance where User_uid = '" . $UserID . "' and date = '".$Date."' and outtime = '".$Time."'");
                            if ($resultCheckDoubleDate = mysqli_fetch_assoc($resCheckDoubleDate)) 
                            {
                                 
                            }
                            else
                            {
                                $UpdateQuery = "update attendance set hours='" . $hours . "', outtime='" . $Time . "',othours='" . $OT_to_save . "',late_att_min = '".$New_Late_Min."' where aid = '" . $result["aid"] . "'";
                                SUD($UpdateQuery);
                            }

                        }
                        else
                        {
                            $LeaveType = "";
                            $Slot = "";
                            $queryHalfShortCheck = "select type,time_slot,days from employee_leave where date = '" . $Date . "' and uid = '" . $UserID . "' and (type like 'Halfday Evening Leave' or type like 'Short Evening Leave' or type like 'Nopay Evening Leave' or type like 'Duty Evening Leave') and is_att_leave = '0'";
                            $resHalfShortCheck = Search($queryHalfShortCheck);
                            if ($resultHalfShortCheck = mysqli_fetch_assoc($resHalfShortCheck)) 
                            {
                                $LeaveType =  $resultHalfShortCheck["type"];
                                $Slot =  $resultHalfShortCheck["time_slot"];
                                $DAYS =  $resultHalfShortCheck["days"];

                                if ($LeaveType == "Halfday Evening Leave") 
                                {
                                    if (strtotime($Time) < strtotime(GetHalfELate($UserID))) 
                                    {
                                        $lateMintsData = (strtotime(GetHalfELate($UserID)) - strtotime($Time))/60;
                                    }
                                    else
                                    {
                                        $lateMintsData = 0;
                                    }

                                    $UpdateQuery = "update attendance set halfday='1' where aid = '" . $result["aid"] . "'";
                                    SUD($UpdateQuery);

                                    $OT_to_save = $static_morning_OT + $OTfromPeriod;
                                }
                                else if ($LeaveType == "Nopay Evening Leave" || $LeaveType == "Duty Evening Leave") 
                                {
                                    if (strtotime($Time) < strtotime(GetHalfELate($UserID))) 
                                    {
                                        $lateMintsData = (strtotime(GetHalfELate($UserID)) - strtotime($Time))/60;
                                    }
                                    else
                                    {
                                        $lateMintsData = 0;
                                    }

                                    $OT_to_save = $static_morning_OT + $OTfromPeriod;
                                }
                                else if ($LeaveType == "Short Evening Leave") 
                                {
                                    if (strtotime($Time) < strtotime(GetShortELate($UserID))) 
                                    {
                                        $lateMintsData = (strtotime(GetShortELate($UserID)) - strtotime($Time))/60;
                                    }
                                    else
                                    {
                                        $lateMintsData = 0;
                                    }

                                    $UpdateQuery = "update attendance set shortleave='1' where aid = '" . $result["aid"] . "'";
                                    SUD($UpdateQuery);

                                    $OT_to_save = $static_morning_OT + $OTfromPeriod;
                                }
                                else
                                {
                                    if (strtotime($Time) < strtotime(GetWorkingOuttimeWeek($UserID)))  
                                    {
                                        $lateMintsData = (strtotime(GetWorkingOuttimeWeek($UserID)) - strtotime($Time))/60;
                                    }
                                    else
                                    {
                                        $lateMintsData = 0;
                                    }

                                    $OT_to_save = $static_morning_OT + $OTfromPeriod;
                                }   
                            }
                            else
                            {
                                if (strtotime($Time) < strtotime(GetWorkingOuttimeWeek($UserID)))  
                                {
                                    $lateMintsData = (strtotime(GetWorkingOuttimeWeek($UserID)) - strtotime($Time))/60;
                                }
                                else
                                {
                                    $lateMintsData = 0;
                                }

                                $OT_to_save = $static_morning_OT + $OTfromPeriod;

                            }


                            $queryEmpType = "select EmployeeType_etid from user where uid = '" . $UserID . "' and isactive='1'";
                            $resEmpType = Search($queryEmpType);
                            if ($resultEmpType = mysqli_fetch_assoc($resEmpType)) 
                            {
                                if ($resultEmpType["EmployeeType_etid"] == "1") 
                                {
                                    $OT_to_save = 0;
                                }
                                else if ($resultEmpType["EmployeeType_etid"] == "2") 
                                {
                                    $OT_to_save = 0;
                                }
                                else if ($resultEmpType["EmployeeType_etid"] == "3") 
                                {
                                    $OT_to_save = 0;
                                }
                                else if ($resultEmpType["EmployeeType_etid"] == "4") 
                                {
                                    $OT_to_save = 0;
                                }
                                else if ($resultEmpType["EmployeeType_etid"] == "5") 
                                {
                                    $OT_to_save = 0;
                                }
                                else if ($resultEmpType["EmployeeType_etid"] == "6") 
                                {
                                    $OT_to_save = 0;
                                }
                            }
                            
                            $New_Late_Min = $Late_MIN_DATA + $lateMintsData; 

                            $resCheckDoubleDate = Search("select * from attendance where User_uid = '" . $UserID . "' and date = '".$Date."' and outtime = '".$Time."'");
                            if ($resultCheckDoubleDate = mysqli_fetch_assoc($resCheckDoubleDate)) 
                            {
                                 
                            }
                            else
                            {
                                $UpdateQuery = "update attendance set outtime='" . $Time . "', hours = '" . $hours . "', othours = '" . $OT_to_save . "',late_att_min = '".$New_Late_Min."'  where aid = '" . $result["aid"] . "'";
                                SUD($UpdateQuery);
                            }     
                        }
                    }
                    
                }
            } 
        }
    }
    
}

function UserAttendanceRecordFORExcelFile($UID, $Status, $Action, $JobCode, $Date, $Time) 
{

    // $UID = preg_replace("/[^0-9]/", "", $UID);
    // $Status = preg_replace("/[^0-9]/", "", $Status);
    // $JobCode = preg_replace("/[^0-9]/", "", $JobCode);

    if ($Time == "") 
    {
       $Time = "0";
    }
    else
    {
       $time_in_24_hour_format  = date("H:i:s", strtotime($Time));
       $Time = $time_in_24_hour_format; 
    }


    $poyaDay = false;
    //if date is poya day
    $querp = "select date from poyadays where date = '".$Date."'";
    $resp = Search($querp);
    if ($resulp = mysqli_fetch_assoc($resp)) {
        $poyaDay = true;
    }else{
        $poyaDay = false;
    }

    $x = strtotime($Date);
    $x = date('l', $x);

    //if date is Saturday 
    $saturday = false;
    if ($x == "Saturday") {
        $saturday = true;
    }

    $static_morning_OT = 0;

    //$UserID = 0;
    $queryu = "select uid,work_typ from user where jobcode = '" . $UID . "' and isactive='1'";
    $resu = Search($queryu);
    if ($resultu = mysqli_fetch_assoc($resu)) {
        $UserID = $resultu["uid"];
        $Work_TYPE = $resultu["work_typ"];
    }


    //Get Join Date
    $resYears = Search("select registerdDate,first_year_leave_end_date from user where uid = '" . $UserID . "'");
    if ($resultYears = mysqli_fetch_assoc($resYears)) 
    {
       $joinDate = $resultYears["registerdDate"];
       $first_yr_end_Date = $resultYears["first_year_leave_end_date"];
    }

    $YearDiff = date('Y-m-d') - $joinDate;
    $date1 = $joinDate;
    $date2 = date('Y-m-d');

    $diff = abs(strtotime($date2) - strtotime($date1));

    $years = floor($diff / (365*60*60*24));
    $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
    $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

    
    $Total_leave = 0;
    $Get_Leave_Half_OR_CASUAL = 0;
    $Available_Leave_Half_OR_CASUAL = 0;
    $Get_Leave_Short_OR_ANNUAL = 0;
    $Available_Leave_Short_OR_ANNUAL = 0;

    $Get_Short = 0;
    $Available_Short = 0;   

    if ($years >= "0" && $years < "2") 
    {

        if ($years == "0" && $months == "0") 
        {
            $Section = "A";
            $Total_leave = 0;
            $Get_Leave_Half_OR_CASUAL = 0;
            $Available_Leave_Half_OR_CASUAL = 0;
            $Get_Leave_Short_OR_ANNUAL = 0;
            $Available_Leave_Short_OR_ANNUAL = 0;
            $Get_Short = 0;
            $Available_Short = 0;   
        }
        else if ($years == "0" && $months >= "1" && $months <= "12") 
        {
            //2024-06-22 NEW PART
            if (date('m', strtotime($joinDate)) == "07" || date('m', strtotime($joinDate)) == "08" || date('m', strtotime($joinDate)) == "09" || date('m', strtotime($joinDate)) == "10" || date('m', strtotime($joinDate)) == "11" || date('m', strtotime($joinDate)) == "12") 
            {
                if ($years == "0" && $months >= "6")
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
                    
                    $Total_leave = 0;
                    $Get_Leave_Half_OR_CASUAL = 0;
                    $Available_Leave_Half_OR_CASUAL = 0;
                    $Get_Leave_Short_OR_ANNUAL = 0;
                    $Available_Leave_Short_OR_ANNUAL = 0;

                    $one_month_short = 0.5; // 2 short leaves
                    $Get_Short = 0;
                    $Available_Short = 0;


                    if (!empty($first_yr_end_Date) && $first_yr_end_Date != "0000-00-00") 
                    {
                        $Get_DATA="AND date > '".$first_yr_end_Date."'";
                    }
                    else
                    {
                        $Get_DATA= "";
                    }

                     

                    $resHalf = Search("select sum(days) as halfleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y')."' AND (type like 'Halfday Morning Leave' or type like 'Halfday Evening Leave') ".$Get_DATA."");
                    
                    if ($resultHalf = mysqli_fetch_assoc($resHalf)) 
                    {
                        if ($resultHalf["halfleave"] == "") 
                        {
                            $Get_Separate_Half = 0;
                        }
                        else
                        {
                            $Get_Separate_Half = $resultHalf["halfleave"];
                        }          
                    }
                    
                    $res_casual = Search("select sum(days) as totalLeaves_casual from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y')."' and type like 'Casual Leave'");
                    if ($result_casual = mysqli_fetch_assoc($res_casual)) 
                    {
                        if ($result_casual["totalLeaves_casual"] == "")
                        {
                           $Get_Leave_Half_OR_CASUAL = 0 + $Get_Separate_Half; 
                           $Available_Leave_Half_OR_CASUAL = $casual_leaves - $Get_Separate_Half;
                        }
                        else
                        {
                           $Get_Leave_Half_OR_CASUAL = $result_casual["totalLeaves_casual"];
                           
                           if ($Get_Leave_Half_OR_CASUAL >= $casual_leaves) 
                           {
                               $Available_Leave_Half_OR_CASUAL = 0;
                               $Get_Leave_Half_OR_CASUAL = $casual_leaves;
                           }
                           else
                           {
                               $Available_Leave_Half_OR_CASUAL = $casual_leaves - ($Get_Leave_Half_OR_CASUAL+ $Get_Separate_Half);
                               $Get_Leave_Half_OR_CASUAL = $Get_Leave_Half_OR_CASUAL + $Get_Separate_Half; // 2024-05-14 added
                           } 
                        }
                    }


                    $res_annual = Search("select sum(days) as totalLeaves_anual from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y')."' and type like 'Annual Leave'");
                    if ($result_annual = mysqli_fetch_assoc($res_annual)) 
                    {
                        if ($result_annual["totalLeaves_anual"] == "")
                        {
                            $Get_Leave_Short_OR_ANNUAL = 0;
                            $Available_Leave_Short_OR_ANNUAL = $annual_leaves;
                        }
                        else
                        {
                           $Get_Leave_Short_OR_ANNUAL = $result_annual["totalLeaves_anual"];
                           
                           if ($Get_Leave_Short_OR_ANNUAL >= $annual_leaves) 
                           {
                               $Get_Leave_Short_OR_ANNUAL = $annual_leaves;
                               $Available_Leave_Short_OR_ANNUAL = 0;
                           }
                           else
                           {
                               $Available_Leave_Short_OR_ANNUAL = $annual_leaves - $Get_Leave_Short_OR_ANNUAL;
                           }
                        }
                    }

                    $queryShort = "select sum(days) as shortleave from employee_leave where uid = '" . $UserID . "' AND YEAR(date) = '".date('Y',strtotime($Date))."' AND MONTH(date) = '".date('m',strtotime($Date))."' AND (type like 'Short Morning Leave' or type like 'Short Evening Leave')";
                    $resShort = Search($queryShort);
                    
                    if ($resultShort = mysqli_fetch_assoc($resShort)) 
                    {
                        if ($resultShort["shortleave"] == "") 
                        {
                            $Get_Short = 0;
                            $Available_Short = $one_month_short - 0;
                        }
                        else
                        {
                            $Get_Short = $resultShort["shortleave"];
                            $Available_Short = $one_month_short - $Get_Short;
                        }          
                    }

                    $Total_leave = $annual_leaves + $casual_leaves;
                }
            }


            $one_month_half = 0.5; // 1 half day
            $one_month_short = 0.5; // 2 short leaves

            $Total_leave = 0;
            $Get_Leave_Half_OR_CASUAL = 0;
            $Available_Leave_Half_OR_CASUAL = 0;
            $Get_Leave_Short_OR_ANNUAL = 0;
            $Available_Leave_Short_OR_ANNUAL = 0;

            $queryHalf = "select sum(days) as halfleave from employee_leave where uid = '" . $UserID . "' AND YEAR(date) = '".date('Y',strtotime($Date))."' AND MONTH(date) = '".date('m',strtotime($Date))."' AND (type like 'Halfday Morning Leave' or type like 'Halfday Evening Leave')";
            $resHalf = Search($queryHalf);
            
            if ($resultHalf = mysqli_fetch_assoc($resHalf)) 
            {
                if ($resultHalf["halfleave"] == "") 
                {
                    $resPreviouseHalf = Search("select total_leave as previousehalf from total_leave_data where uid = '" . $UserID . "'");
            
                    if ($resultPreviouseHalf = mysqli_fetch_assoc($resPreviouseHalf)) 
                    {
                        $TOTAL = $resultPreviouseHalf["previousehalf"];

                        if ($resultPreviouseHalf["previousehalf"] == "") 
                        {
                            
                        }
                        else
                        {
                            $Get_Leave_Half_OR_CASUAL = 0;
                            $Available_Leave_Half_OR_CASUAL = $resultPreviouseHalf["previousehalf"];
                        }
                                   
                    }

                }
                else
                {
                    $resPreviouseHalf = Search("select total_leave as previousehalf from total_leave_data where uid = '" . $UserID . "'");
            
                    if ($resultPreviouseHalf = mysqli_fetch_assoc($resPreviouseHalf)) 
                    {
                        $TOTAL = $resultPreviouseHalf["previousehalf"];

                        if ($resultPreviouseHalf["previousehalf"] == "") 
                        {
                        
                        }
                        else
                        {
                            $Get_Leave_Half_OR_CASUAL = $resultHalf["halfleave"];
                            $Available_Leave_Half_OR_CASUAL = $resultPreviouseHalf["previousehalf"];
                        }
                                   
                    }
                    
                }
                           
            }

            $queryShort = "select sum(days) as shortleave from employee_leave where uid = '" . $UserID . "' AND YEAR(date) = '".date('Y',strtotime($Date))."' AND MONTH(date) = '".date('m',strtotime($Date))."' AND (type like 'Short Morning Leave' or type like 'Short Evening Leave')";
            $resShort = Search($queryShort);
            
            if ($resultShort = mysqli_fetch_assoc($resShort)) 
            {
                if ($resultShort["shortleave"] == "") 
                {
                    $Get_Short = 0;
                    $Available_Short = $one_month_short - 0;
                }
                else
                {
                    $Get_Short = $resultShort["shortleave"];
                    $Available_Short = $one_month_short - $Get_Short;
                }       
            }

           $Total_leave = $TOTAL;
           
           if ($Available_Leave_Half_OR_CASUAL <= 0) {
               $Available_Leave_Half_OR_CASUAL = 0;
           }

           if ($Available_Short <= 0) {
               $Available_Short = 0;
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
            
            $Total_leave = 0;
            $Get_Leave_Half_OR_CASUAL = 0;
            $Available_Leave_Half_OR_CASUAL = 0;
            $Get_Leave_Short_OR_ANNUAL = 0;
            $Available_Leave_Short_OR_ANNUAL = 0;

            $one_month_short = 0.5; // 2 short leaves
            $Get_Short = 0;
            $Available_Short = 0;


            if (!empty($first_yr_end_Date) && $first_yr_end_Date != "0000-00-00") 
            {
                $Get_DATA="AND date > '".$first_yr_end_Date."'";
            }
            else
            {
                $Get_DATA= "";
            }

             

            $resHalf = Search("select sum(days) as halfleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y')."' AND (type like 'Halfday Morning Leave' or type like 'Halfday Evening Leave') ".$Get_DATA."");
            
            if ($resultHalf = mysqli_fetch_assoc($resHalf)) 
            {
                if ($resultHalf["halfleave"] == "") 
                {
                    $Get_Separate_Half = 0;
                }
                else
                {
                    $Get_Separate_Half = $resultHalf["halfleave"];
                }          
            }
            
            $res_casual = Search("select sum(days) as totalLeaves_casual from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y')."' and type like 'Casual Leave'");
            if ($result_casual = mysqli_fetch_assoc($res_casual)) 
            {
                if ($result_casual["totalLeaves_casual"] == "")
                {
                    $Get_Leave_Half_OR_CASUAL = 0 + $Get_Separate_Half; 
                    $Available_Leave_Half_OR_CASUAL = $casual_leaves - $Get_Separate_Half;
                }
                else
                {
                   $Get_Leave_Half_OR_CASUAL = $result_casual["totalLeaves_casual"];
                   
                   if ($Get_Leave_Half_OR_CASUAL >= $casual_leaves) 
                   {
                       $Available_Leave_Half_OR_CASUAL = 0;
                       $Get_Leave_Half_OR_CASUAL = $casual_leaves;
                   }
                   else
                   {
                       $Available_Leave_Half_OR_CASUAL = $casual_leaves - ($Get_Leave_Half_OR_CASUAL+ $Get_Separate_Half);
                       $Get_Leave_Half_OR_CASUAL = $Get_Leave_Half_OR_CASUAL + $Get_Separate_Half; // 2024-05-14 added
                   } 
                }
            }


            $res_annual = Search("select sum(days) as totalLeaves_anual from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y')."' and type like 'Annual Leave'");
            if ($result_annual = mysqli_fetch_assoc($res_annual)) 
            {
                if ($result_annual["totalLeaves_anual"] == "")
                {
                    $Get_Leave_Short_OR_ANNUAL = 0;
                    $Available_Leave_Short_OR_ANNUAL = $annual_leaves;
                }
                else
                {
                   $Get_Leave_Short_OR_ANNUAL = $result_annual["totalLeaves_anual"];
                   
                   if ($Get_Leave_Short_OR_ANNUAL >= $annual_leaves) 
                   {
                       $Get_Leave_Short_OR_ANNUAL = $annual_leaves;
                       $Available_Leave_Short_OR_ANNUAL = 0;
                   }
                   else
                   {
                       $Available_Leave_Short_OR_ANNUAL = $annual_leaves - $Get_Leave_Short_OR_ANNUAL;
                   }
                }
            }

            $queryShort = "select sum(days) as shortleave from employee_leave where uid = '" . $UserID . "' AND YEAR(date) = '".date('Y',strtotime($Date))."' AND MONTH(date) = '".date('m',strtotime($Date))."' AND (type like 'Short Morning Leave' or type like 'Short Evening Leave')";
            $resShort = Search($queryShort);
            
            if ($resultShort = mysqli_fetch_assoc($resShort)) 
            {
                if ($resultShort["shortleave"] == "") 
                {
                    $Get_Short = 0;
                    $Available_Short = $one_month_short - 0;
                }
                else
                {
                    $Get_Short = $resultShort["shortleave"];
                    $Available_Short = $one_month_short - $Get_Short;
                }          
            }

            $Total_leave = $annual_leaves + $casual_leaves;
        }
    }
    else
    {
        $casual_leaves = 7;
        $annual_leaves = 14;
        $Total_leave = 0;
        $Get_Leave_Half_OR_CASUAL = 0;
        $Available_Leave_Half_OR_CASUAL = 0;
        $Get_Leave_Short_OR_ANNUAL = 0;
        $Available_Leave_Short_OR_ANNUAL = 0;
           
        $one_month_short = 0.5; // 2 short leaves
        $Get_Short = 0;
        $Available_Short = 0;

       $res_casual = Search("select sum(days) as totalLeaves_casual from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y')."' and (type like 'Casual Leave' or type like 'Halfday Morning Leave' or type like 'Halfday Evening Leave')");
        if ($result_casual = mysqli_fetch_assoc($res_casual)) 
        {
            if ($result_casual["totalLeaves_casual"] == "")
            {
               $Get_Leave_Half_OR_CASUAL = 0; 
               $Available_Leave_Half_OR_CASUAL = $casual_leaves;
            }
            else
            {
               $Get_Leave_Half_OR_CASUAL = $result_casual["totalLeaves_casual"];
               
               if ($Get_Leave_Half_OR_CASUAL >= $casual_leaves) 
               {
                   $Available_Leave_Half_OR_CASUAL = 0;
                   $Get_Leave_Half_OR_CASUAL = $casual_leaves;
               }
               else
               {
                   $Available_Leave_Half_OR_CASUAL = $casual_leaves - $Get_Leave_Half_OR_CASUAL;
               } 
            }
        }


        $res_annual = Search("select sum(days) as totalLeaves_anual from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y')."' and type like 'Annual Leave'");
        if ($result_annual = mysqli_fetch_assoc($res_annual)) 
        {
            if ($result_annual["totalLeaves_anual"] == "")
            {
                $Get_Leave_Short_OR_ANNUAL = 0;
                $Available_Leave_Short_OR_ANNUAL = $annual_leaves;
            }
            else
            {
               $Get_Leave_Short_OR_ANNUAL = $result_annual["totalLeaves_anual"];
               
               if ($Get_Leave_Short_OR_ANNUAL >= $annual_leaves) 
               {
                   $Get_Leave_Short_OR_ANNUAL = $annual_leaves;
                   $Available_Leave_Short_OR_ANNUAL = 0;
               }
               else
               {
                   $Available_Leave_Short_OR_ANNUAL = $annual_leaves - $Get_Leave_Short_OR_ANNUAL;
               }
            }
        }

        $queryShort = "select sum(days) as shortleave from employee_leave where uid = '" . $UserID . "' AND YEAR(date) = '".date('Y',strtotime($Date))."' AND MONTH(date) = '".date('m',strtotime($Date))."' AND (type like 'Short Morning Leave' or type like 'Short Evening Leave')";
        $resShort = Search($queryShort);
            
        if ($resultShort = mysqli_fetch_assoc($resShort)) 
        {
            if ($resultShort["shortleave"] == "") 
            {
                $Get_Short = 0;
                $Available_Short = $one_month_short - 0;
            }
            else
            {
                $Get_Short = $resultShort["shortleave"];
                $Available_Short = $one_month_short - $Get_Short;
            }          
        }

        $Total_leave = $annual_leaves + $casual_leaves; 
    }

    if ($Work_TYPE == "2") 
    {
        //===================SHIFT STAFF================================================

        //get shift type id according to User ID
        $res_shift = Search("select espid from emp_has_shift where user_uid = '".$UserID."' and date = '".$Date."'");
        if ($result_shift = mysqli_fetch_assoc($res_shift)) 
        {
            $Req_Shift_Typ_ID = $result_shift["espid"];
        }
        else
        {
            $Req_Shift_Typ_ID = "";
        }

        
        // Action = In (Intime Calculation)
        if($Action == "C/In")
        {

            $MOThours = 0;

            $LeaveType = "";
            $Slot = "";
            $IS_HALF = 0;
            $IS_SHORT = 0;

            if ($Time == "0") 
            {
               $Time = GetShiftIntime($Req_Shift_Typ_ID);
            }


            $queryHalfShortCheck = "select type,time_slot,days from employee_leave where date = '" . $Date . "' and uid = '" . $UserID . "' and (type like 'Halfday Morning Leave' or type like 'Short Morning Leave' or type like 'Nopay Morning Leave' or type like 'Duty Morning Leave') and is_att_leave = '0'";
            $resHalfShortCheck = Search($queryHalfShortCheck);
            if ($resultHalfShortCheck = mysqli_fetch_assoc($resHalfShortCheck)) 
            {
                $LeaveType =  $resultHalfShortCheck["type"];
                $Slot =  $resultHalfShortCheck["time_slot"];
                $DAYS =  $resultHalfShortCheck["days"];

                if ($LeaveType == "Halfday Morning Leave") 
                {   
                    if (strtotime($Time) > strtotime(GetShiftHalfMLate($Req_Shift_Typ_ID))) 
                    {
                        $lateMints = (strtotime($Time) - strtotime(GetShiftHalfMLate($Req_Shift_Typ_ID)))/60;
                    }
                    else
                    {
                        $lateMints = 0;
                    }

                    $IS_HALF = 1;
                    $IS_SHORT = 0;   
                 }
                 else if ($LeaveType == "Nopay Morning Leave" || $LeaveType == "Duty Morning Leave") 
                 {   
                    if (strtotime($Time) > strtotime(GetShiftHalfMLate($Req_Shift_Typ_ID))) 
                    {
                        $lateMints = (strtotime($Time) - strtotime(GetShiftHalfMLate($Req_Shift_Typ_ID)))/60;
                    }
                    else
                    {
                        $lateMints = 0;
                    }

                    $IS_HALF = 0;
                    $IS_SHORT = 0;   
                 }
                 else if ($LeaveType == "Short Morning Leave") 
                 {
                    if (strtotime($Time) > strtotime(GetShiftShortMLate($Req_Shift_Typ_ID))) 
                    {
                        $lateMints = (strtotime($Time) - strtotime(GetShiftShortMLate($Req_Shift_Typ_ID)))/60;
                    }
                    else
                    {
                        $lateMints = 0;
                    }

                    $IS_HALF = 0;
                    $IS_SHORT = 1; 
                 }
                 else
                 {
                    if (strtotime($Time) > strtotime(GetShiftLate($Req_Shift_Typ_ID)))     
                    {
                       // $lateMints = (strtotime($Time1) - strtotime("08:30:00"))/60;
                        $lateMints = (strtotime($Time) - strtotime(GetShiftIntime($Req_Shift_Typ_ID)))/60;
                    }
                    else
                    {
                        $lateMints = 0;
                    }

                    $IS_HALF = 0;
                    $IS_SHORT = 0;
                 }  
            }
            else
            {
                //if (strtotime($Time1) > strtotime("08:36:00")) 
                if (strtotime($Time) > strtotime(GetShiftLate($Req_Shift_Typ_ID)))     
                {
                   // $lateMints = (strtotime($Time1) - strtotime("08:30:00"))/60;
                    $lateMints = (strtotime($Time) - strtotime(GetShiftIntime($Req_Shift_Typ_ID)))/60;
                }
                else
                {
                    $lateMints = 0;
                }

                $IS_HALF = 0;
                $IS_SHORT = 0;
            }


            if ($lateMints > 0) 
            {
                $Att_DATE = explode('-', $Date);
                $check_Tot_Late = Search("select sum(late_att_min) as Total_Late_Min from attendance where YEAR(date) = '" . $Att_DATE[0] . "' and MONTH(date) = '" . $Att_DATE[1] . "' and User_uid = '" . $UserID . "'");
                if ($results_tot_late = mysqli_fetch_assoc($check_Tot_Late)) 
                {
                    $TOTAL_LATE_MIN = $results_tot_late["Total_Late_Min"];
                }

                if ($TOTAL_LATE_MIN > 60) 
                {
                   $query = "insert into employee_leave(date,uid,type,days,time_slot,is_att_leave) values('" . $Date . "','" . $UserID . "','Nopay Leave','1','','1')"; 
                       $res = SUD($query);
                }
                else
                {
                    if ($lateMints > 15 && $lateMints < 30) 
                    {
                        if ($Available_Short == "0") 
                        {
                            $query = "insert into employee_leave(date,uid,type,days,time_slot,is_att_leave) values('" . $Date . "','" . $UserID . "','Nopay Leave','1','','1')"; 
                            $res = SUD($query);
                        }
                        else
                        {
                            $query = "insert into employee_leave(date,uid,type,days,time_slot,is_att_leave) values('" . $Date . "','" . $UserID . "','Short Morning Leave','0.25','','1')"; 
                            $res = SUD($query);

                            $IS_HALF = 0;
                            $IS_SHORT = 1;
                        }
                    }
                    else if ($lateMints > 30) 
                    {
                        if ($Available_Leave_Half_OR_CASUAL == "0") 
                        {
                           $query = "insert into employee_leave(date,uid,type,days,time_slot,is_att_leave) values('" . $Date . "','" . $UserID . "','Nopay Leave','1','','1')"; 
                            $res = SUD($query);
                        }
                        else
                        {
                            $query = "insert into employee_leave(date,uid,type,days,time_slot,is_att_leave) values('" . $Date . "','" . $UserID . "','Halfday Morning Leave','0.5','','1')"; 
                            $res = SUD($query);

                            $IS_HALF = 1;
                            $IS_SHORT = 0;
                        }
                    }
                } 
            }

            $static_morning_OT = $MOThours;

            $resCheckDoubleDate = Search("select * from attendance where User_uid = '" . $UserID . "' and date = '".$Date."' and intime = '".$Time."'");
            if ($resultCheckDoubleDate = mysqli_fetch_assoc($resCheckDoubleDate)) 
            {
                 return "Att_IN_YES";
            }
            else
            {
                $InsertQuery = "insert into attendance(date,intime,attendance,User_uid,attendanceType_atid,late_att_min,othours,halfday, shortleave) values('" . $Date . "','" . $Time . "','1','" . $UserID . "','1','" . $lateMints . "','" . $MOThours . "','" . $IS_HALF . "','" . $IS_SHORT . "')";
                $res1 = SUD($InsertQuery);
                
                if ($res1 == 1) 
                {
                    return "Att_IN_YES";
                }
                else
                {
                    return "Att_IN_NO";
                }

            }
        }
        elseif($Action == "C/Out") //Action = OUT (Outtime Calaculation)~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        { 
            $OUTTIME = date("A", strtotime($Time));

            if ($Time == "0") 
            {
               $Time = GetShiftOuttime($Req_Shift_Typ_ID);
            }
            
            if ($OUTTIME == "AM") 
            {

                $queryInDate = "select date from attendance where date = '" . $Date . "' and User_uid = '" . $UserID . "'";
                $resInDate = Search($queryInDate);
                if ($resultInDate = mysqli_fetch_assoc($resInDate)) 
                {
                    $query = "select aid,intime,othours,late_att_min from attendance where date = '" . $Date . "' and User_uid = '" . $UserID . "'";
                    $res = Search($query);
                    if ($result = mysqli_fetch_assoc($res)) 
                    {
                        if ($result["intime"] != "") 
                        {
                            // $T8Mng = date("H:i:s", strtotime("08:30 AM"));
                            $T8Mng = date("H:i:s", strtotime(GetShiftIntime($Req_Shift_Typ_ID)));
                            $hours = getTimeDifference($Date, $T8Mng, $Date, $Time);
                            $static_morning_OT = $result["othours"];
                            $Late_MIN_DATA = $result["late_att_min"];
                            $timeIN24HR = date("H:i:s", strtotime($Time));
                            

                            $T8Mng = date("H:i:s", strtotime(GetShiftIntime($Req_Shift_Typ_ID)));
                            $T5Eve = date("H:i:s", strtotime(GetShiftOuttime($Req_Shift_Typ_ID)));

                            $T130Aftn = date("H:i:s", strtotime(GetShiftHalfMorningOuttime($Req_Shift_Typ_ID)));
                            // if intime not between 8.30 and 1.30 no deduction fro lunch hour
                            if($T8Mng < $timeIN24HR && $T130Aftn > $timeIN24HR)
                            {
                             
                            }
                            else
                            {
                                $hourd_pre_ded_intavels = $hours;
                                $hours = $hours - 0.5; //Lunch Time Duration
                            }

                            $hours = number_format(floatval($hours),2);

                            $Att_DATE = explode('-', $Date);
                            $check_Tot_WORK = Search("select sum(hours) as Total_WORK_HRS from attendance where YEAR(date) = '" . $Att_DATE[0] . "' and MONTH(date) = '" . $Att_DATE[1] . "' and User_uid = '" . $UserID . "'");
                            if ($results_tot_work = mysqli_fetch_assoc($check_Tot_WORK)) 
                            {
                                $TOTAL_WORK_HRS = $results_tot_work["Total_WORK_HRS"];
                            }

                            $othAR = explode(".", number_format($TOTAL_WORK_HRS,2));

                            $TOT_OTH = $othAR[0];
                            $TOT_OTM = $othAR[1];

                            //Calculate OT Value
                            $TOT_MinsOT = ($TOT_OTH*60) + $TOT_OTM;
                            $TOT_WORKvalue = floor($TOT_MinsOT/60);
                            
                            if ($TOT_WORKvalue > 200) 
                            {
                                if($T5Eve < $timeIN24HR){
                                    $OTfromPeriod = getTimeDifference($Date, $T5Eve, $Date, $Time);
                                    $OTfromPeriod = number_format(floatval($OTfromPeriod),2);
                                }
                            }
                            else
                            {
                                $OTfromPeriod = 0;
                            }


                            $LeaveType = "";
                            $Slot = "";
                            $queryHalfShortCheck = "select type,time_slot,days from employee_leave where date = '" . $Date . "' and uid = '" . $UserID . "' and (type like 'Halfday Evening Leave' or type like 'Short Evening Leave' or type like 'Nopay Evening Leave' or type like 'Duty Evening Leave') and is_att_leave = '0'";
                            $resHalfShortCheck = Search($queryHalfShortCheck);
                            if ($resultHalfShortCheck = mysqli_fetch_assoc($resHalfShortCheck)) 
                            {
                                $LeaveType =  $resultHalfShortCheck["type"];
                                $Slot =  $resultHalfShortCheck["time_slot"];
                                $DAYS =  $resultHalfShortCheck["days"];


                                if ($LeaveType == "Halfday Evening Leave") 
                                {
                                    if (strtotime($Time) < strtotime(GetShiftHalfELate($Req_Shift_Typ_ID))) 
                                    {
                                        // $lateMintsData = (strtotime("13:00:00") - strtotime($Time))/60;
                                        $lateMintsData = (strtotime(GetShiftHalfELate($Req_Shift_Typ_ID)) - strtotime($Time))/60;
                                    }
                                    else
                                    {
                                        $lateMintsData = 0;
                                    }

                                    $UpdateQuery = "update attendance set halfday='1' where aid = '" . $result["aid"] . "'";
                                    SUD($UpdateQuery);

                                    $OT_to_save = $static_morning_OT + $OTfromPeriod;
                                }
                                else if ($LeaveType == "Nopay Evening Leave" || $LeaveType == "Duty Evening Leave") 
                                {
                                    if (strtotime($Time) < strtotime(GetShiftHalfELate($Req_Shift_Typ_ID))) 
                                    {
                                        // $lateMintsData = (strtotime("13:00:00") - strtotime($Time))/60;
                                        $lateMintsData = (strtotime(GetShiftHalfELate($Req_Shift_Typ_ID)) - strtotime($Time))/60;
                                    }
                                    else
                                    {
                                        $lateMintsData = 0;
                                    }

                                    $OT_to_save = $static_morning_OT + $OTfromPeriod;
                                }
                                else if ($LeaveType == "Short Evening Leave") 
                                {
                                    if (strtotime($Time) < strtotime(GetShiftShortELate($Req_Shift_Typ_ID))) 
                                    {
                                        // $lateMintsData = (strtotime("16:00:00") - strtotime($Time))/60;
                                        $lateMintsData = (strtotime(GetShiftShortELate($Req_Shift_Typ_ID)) - strtotime($Time))/60;
                                    }
                                    else
                                    {
                                        $lateMintsData = 0;
                                    }

                                    $UpdateQuery = "update attendance set shortleave='1' where aid = '" . $result["aid"] . "'";
                                    SUD($UpdateQuery);

                                    $OT_to_save = $static_morning_OT + $OTfromPeriod;
                                }
                                else
                                {
                                    if (strtotime($Time) < strtotime(GetShiftOuttime($Req_Shift_Typ_ID)))  
                                    {
                                        // $lateMintsData = (strtotime("17:30:00") - strtotime($Time))/60;
                                        $lateMintsData = (strtotime(GetShiftOuttime($Req_Shift_Typ_ID)) - strtotime($Time))/60;
                                    }
                                    else
                                    {
                                        $lateMintsData = 0;
                                    }

                                    $OT_to_save = $static_morning_OT + $OTfromPeriod;
                                }   
                            }
                            else
                            {
                                // if (strtotime($Time) < strtotime("17:30:00"))
                                if (strtotime($Time) < strtotime(GetShiftOuttime($Req_Shift_Typ_ID)))  
                                {
                                    // $lateMintsData = (strtotime("17:30:00") - strtotime($Time))/60;
                                    $lateMintsData = (strtotime(GetShiftOuttime($Req_Shift_Typ_ID)) - strtotime($Time))/60;
                                }
                                else
                                {
                                    $lateMintsData = 0;
                                }

                                $OT_to_save = $static_morning_OT + $OTfromPeriod;

                            }


                            $queryEmpType = "select EmployeeType_etid from user where uid = '" . $UserID . "' and isactive='1'";
                            $resEmpType = Search($queryEmpType);
                            if ($resultEmpType = mysqli_fetch_assoc($resEmpType)) 
                            {
                                 if ($resultEmpType["EmployeeType_etid"] == "1") 
                                 {
                                    $OT_to_save = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "2") 
                                 {
                                    $OT_to_save = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "3") 
                                 {
                                    $OT_to_save = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "4") 
                                 {
                                    $OT_to_save = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "5") 
                                 {
                                    $OT_to_save = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "6") 
                                 {
                                    $OT_to_save = 0;
                                 }
                            }
                            
                            $New_Late_Min = $Late_MIN_DATA + $lateMintsData; 

                            $resCheckDoubleDate = Search("select * from attendance where User_uid = '" . $UserID . "' and date = '".$Date."' and outtime = '".$Time."'");
                            if ($resultCheckDoubleDate = mysqli_fetch_assoc($resCheckDoubleDate)) 
                            {
                                 return "Att_OUT_YES";
                            }
                            else
                            {
                                $UpdateQuery = "update attendance set outtime='" . $Time . "', hours = '" . $hours . "', othours = '" . $OT_to_save . "',late_att_min = '".$New_Late_Min."'  where aid = '" . $result["aid"] . "'";
                                $res2 = SUD($UpdateQuery);

                                if ($res2 == 1) 
                                {
                                    return "Att_OUT_YES";
                                }
                                else
                                {
                                    return "Att_OUT_NO";
                                }
                            } 

                    
                        }
                        
                    }
                }
                else
                {
                    //check earlier date checked out exists (for OT and DOT calculation)
                    //get earlier date
                    $ydate = strtotime($Date);
                    $ydate = strtotime("-1 day", $ydate);
                    $ydate = date('y-m-d', $ydate);

                    $y = strtotime($ydate);
                    $y = date('l', $y);

                    //if date is Saturday 
                    $saturdays = false;
                    if ($y == "Saturday") {
                        $saturdays = true;
                    }

                    $query = "select aid,intime,outtime from attendance where date = '" . $ydate . "' and User_uid = '" . $UserID . "'";
                    $res = Search($query);
                    if ($result = mysqli_fetch_assoc($res)) {

                        if($result["outtime"] == "")
                        {
                            // $earlyDateIn = date("H:i:s", strtotime("08:30 AM"));
                            $earlyDateIn = date("H:i:s", strtotime(GetShiftIntime($Req_Shift_Typ_ID)));
                            $hours = getTimeDifference($ydate, $earlyDateIn, $Date, $Time);
                           
                            $timeIN24HR = date("H:i:s", strtotime($Time));
                            $T130Aftn = date("H:i:s", strtotime(GetShiftHalfMorningOuttime($Req_Shift_Typ_ID)));
                            // if intime not between 8.30 and 1.30 no deduction fro lunch hour
                            if($earlyDateIn < $timeIN24HR && $T130Aftn > $timeIN24HR)
                            {
                             
                            }
                            else
                            {
                                $hourd_pre_ded_intavels = $hours;
                                $hours = $hours - 0.5; //Lunch Time Duration
                            }

                            $hours = number_format(floatval($hours),2);

                            $Att_DATE = explode('-', $Date);
                            $check_Tot_WORK = Search("select sum(hours) as Total_WORK_HRS from attendance where YEAR(date) = '" . $Att_DATE[0] . "' and MONTH(date) = '" . $Att_DATE[1] . "' and User_uid = '" . $UserID . "'");
                            if ($results_tot_work = mysqli_fetch_assoc($check_Tot_WORK)) 
                            {
                                $TOTAL_WORK_HRS = $results_tot_work["Total_WORK_HRS"];
                            }

                            $othAR = explode(".", number_format($TOTAL_WORK_HRS,2));

                            $TOT_OTH = $othAR[0];
                            $TOT_OTM = $othAR[1];
                            //Calculate OT Value
                            $TOT_MinsOT = ($TOT_OTH*60) + $TOT_OTM;
                            $TOT_WORKvalue = floor($TOT_MinsOT/60);
                            
                            if ($TOT_WORKvalue > 200) 
                            {
                                $OThours = getTimeDifference($ydate, $T5Eve, $Date, $Time);
                                $OThours = number_format(floatval($OThours),2);
                            }
                            else
                            {
                                $OThours = 0;//NEW PART
                            }

                            $queryEmpType = "select EmployeeType_etid from user where uid = '" . $UserID . "' and isactive='1'";
                            $resEmpType = Search($queryEmpType);
                            if ($resultEmpType = mysqli_fetch_assoc($resEmpType)) 
                            {
                                 if ($resultEmpType["EmployeeType_etid"] == "1") 
                                 {
                                    $OThours = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "2") 
                                 {
                                    $OThours = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "3") 
                                 {
                                    $OThours = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "4") 
                                 {
                                    $OThours = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "5") 
                                 {
                                    $OThours = 0;
                                 }
                                 else if ($resultEmpType["EmployeeType_etid"] == "6") 
                                 {
                                    $OThours = 0;
                                 }
                            }


                            $resCheckDoubleDate = Search("select * from attendance where User_uid = '" . $UserID . "' and date = '".$Date."' and outtime = '".$Time."'");
                            if ($resultCheckDoubleDate = mysqli_fetch_assoc($resCheckDoubleDate)) 
                            {
                                 return "Att_OUT_YES";
                            }
                            else
                            {
                                $UpdateQuery = "update attendance set date = '".$ydate."', outtime='" . $Time . "',attendance='1',User_uid = '".$UserID."',attendanceType_atid = '1', hours = '" . $hours . "', othours = '" . $OThours . "'  where aid = '" . $result["aid"] . "'";
                                $res2 = SUD($UpdateQuery);

                                if ($res2 == 1) 
                                {
                                    return "Att_OUT_YES";
                                }
                                else
                                {
                                    return "Att_OUT_NO";
                                }
                            }

                        }
                        else
                        {
                            //no clue of the out time (earlier date out exists, current date in missing)
                        }

                    }
                }        
        
            }
            else
            {

                $query = "select aid,intime,othours,late_att_min from attendance where date = '" . $Date . "' and User_uid = '" . $UserID . "'";
                $res = Search($query);
                if ($result = mysqli_fetch_assoc($res)) 
                {
                    if ($result["intime"] != "") 
                    {
                        // $T8Mng = date("H:i:s", strtotime("08:30 AM"));
                        $T8Mng = date("H:i:s", strtotime(GetShiftIntime($Req_Shift_Typ_ID)));
                        $hours = getTimeDifference($Date, $T8Mng, $Date, $Time);

                        $static_morning_OT = $result["othours"];
                        $Late_MIN_DATA = $result["late_att_min"];

                    // echo "Raw Hours ".$hours;

                        //remove Lunch Hour if out time is before 1:30 afternoon
                        $timeIN24HR = date("H:i:s", strtotime($Time));
                        // $T8Mng = date("H:i:s", strtotime("08:30 AM"));
                        $T8Mng = date("H:i:s", strtotime(GetShiftIntime($Req_Shift_Typ_ID)));
                        // $T5Eve = date("H:i:s", strtotime("05:30 PM")); //change 5 to 5.30 (2022/06/13)
                        $T5Eve = date("H:i:s", strtotime(GetShiftOuttime($Req_Shift_Typ_ID))); //change 5 to 5.30 (2022/06/13)

                        $T130Aftn = date("H:i:s", strtotime(GetShiftHalfMorningOuttime($Req_Shift_Typ_ID)));
                        // if intime not between 8.30 and 1.30 no deduction fro lunch hour
                        if($T8Mng < $timeIN24HR && $T130Aftn > $timeIN24HR)
                        {
                         
                        }
                        else
                        {
                            $hourd_pre_ded_intavels = $hours;
                            $hours = $hours - 0.5; //Lunch Time Duration
                        }

                        $hours = number_format(floatval($hours),2);

                        $Att_DATE = explode('-', $Date);
                        $check_Tot_WORK = Search("select sum(hours) as Total_WORK_HRS from attendance where YEAR(date) = '" . $Att_DATE[0] . "' and MONTH(date) = '" . $Att_DATE[1] . "' and User_uid = '" . $UserID . "'");
                        if ($results_tot_work = mysqli_fetch_assoc($check_Tot_WORK)) 
                        {
                            $TOTAL_WORK_HRS = $results_tot_work["Total_WORK_HRS"];
                        }

                        $othAR = explode(".", number_format($TOTAL_WORK_HRS,2));

                        $TOT_OTH = $othAR[0];
                        $TOT_OTM = $othAR[1];
                        //Calculate OT Value
                        $TOT_MinsOT = ($TOT_OTH*60) + $TOT_OTM;
                        $TOT_WORKvalue = floor($TOT_MinsOT/60);
                        
                        if ($TOT_WORKvalue > 200) 
                        {
                            if($T5Eve < $timeIN24HR){
                                $OTfromPeriod = getTimeDifference($Date, $T5Eve, $Date, $Time);
                                $OTfromPeriod = number_format(floatval($OTfromPeriod),2);
                            }
                        }
                        else
                        {
                            $OTfromPeriod = 0;
                        }

                        $LeaveType = "";
                        $Slot = "";
                        $queryHalfShortCheck = "select type,time_slot,days from employee_leave where date = '" . $Date . "' and uid = '" . $UserID . "' and (type like 'Halfday Evening Leave' or type like 'Short Evening Leave' or type like 'Nopay Evening Leave' or type like 'Duty Evening Leave') and is_att_leave = '0'";
                        $resHalfShortCheck = Search($queryHalfShortCheck);
                        if ($resultHalfShortCheck = mysqli_fetch_assoc($resHalfShortCheck)) 
                        {
                            $LeaveType =  $resultHalfShortCheck["type"];
                            $Slot =  $resultHalfShortCheck["time_slot"];
                            $DAYS =  $resultHalfShortCheck["days"];

                             if ($LeaveType == "Halfday Evening Leave") 
                             {
                                 if (strtotime($Time) < strtotime(GetShiftHalfELate($Req_Shift_Typ_ID))) 
                                {
                                    // $lateMintsData = (strtotime("13:00:00") - strtotime($Time))/60;
                                    $lateMintsData = (strtotime(GetShiftHalfELate($Req_Shift_Typ_ID)) - strtotime($Time))/60;
                                }
                                else
                                {
                                    $lateMintsData = 0;
                                }

                                $UpdateQuery = "update attendance set halfday='1' where aid = '" . $result["aid"] . "'";
                                SUD($UpdateQuery);

                                $OT_to_save = $static_morning_OT + $OTfromPeriod;

                             }
                             else if ($LeaveType == "Nopay Evening Leave" || $LeaveType == "Duty Evening Leave") 
                             {
                                 if (strtotime($Time) < strtotime(GetShiftHalfELate($Req_Shift_Typ_ID))) 
                                {
                                    // $lateMintsData = (strtotime("13:00:00") - strtotime($Time))/60;
                                    $lateMintsData = (strtotime(GetShiftHalfELate($Req_Shift_Typ_ID)) - strtotime($Time))/60;
                                }
                                else
                                {
                                    $lateMintsData = 0;
                                }

                                $OT_to_save = $static_morning_OT + $OTfromPeriod;

                             }
                             else if ($LeaveType == "Short Evening Leave") 
                             {
                                if (strtotime($Time) < strtotime(GetShiftShortELate($Req_Shift_Typ_ID))) 
                                {
                                    // $lateMintsData = (strtotime("16:00:00") - strtotime($Time))/60;
                                    $lateMintsData = (strtotime(GetShiftShortELate($Req_Shift_Typ_ID)) - strtotime($Time))/60;
                                }
                                else
                                {
                                    $lateMintsData = 0;
                                }

                                $UpdateQuery = "update attendance set shortleave='1' where aid = '" . $result["aid"] . "'";
                                SUD($UpdateQuery);

                                 $OT_to_save = $static_morning_OT + $OTfromPeriod;
                             }
                             else
                             {
                                if (strtotime($Time) < strtotime(GetShiftOuttime($Req_Shift_Typ_ID)))  
                                {
                                    // $lateMintsData = (strtotime("17:30:00") - strtotime($Time))/60;
                                    $lateMintsData = (strtotime(GetShiftOuttime($Req_Shift_Typ_ID)) - strtotime($Time))/60;
                                }
                                else
                                {
                                    $lateMintsData = 0;
                                }

                                $OT_to_save = $static_morning_OT + $OTfromPeriod;
                             }  
                        }
                        else
                        {
                            // if (strtotime($Time) < strtotime("17:30:00"))
                            if (strtotime($Time) < strtotime(GetShiftOuttime($Req_Shift_Typ_ID)))  
                            {
                                // $lateMintsData = (strtotime("17:30:00") - strtotime($Time))/60;
                                $lateMintsData = (strtotime(GetShiftOuttime($Req_Shift_Typ_ID)) - strtotime($Time))/60;
                            }
                            else
                            {
                                $lateMintsData = 0;
                            }

                            $OT_to_save = $static_morning_OT + $OTfromPeriod;

                        }


                        $queryEmpType = "select EmployeeType_etid from user where uid = '" . $UserID . "' and isactive='1'";
                        $resEmpType = Search($queryEmpType);
                        if ($resultEmpType = mysqli_fetch_assoc($resEmpType)) 
                        {
                             if ($resultEmpType["EmployeeType_etid"] == "1") 
                             {
                                $OT_to_save = 0;
                             }
                             else if ($resultEmpType["EmployeeType_etid"] == "2") 
                             {
                                $OT_to_save = 0;
                             }
                             else if ($resultEmpType["EmployeeType_etid"] == "3") 
                             {
                                $OT_to_save = 0;
                             }
                             else if ($resultEmpType["EmployeeType_etid"] == "4") 
                             {
                                $OT_to_save = 0;
                             }
                             else if ($resultEmpType["EmployeeType_etid"] == "5") 
                             {
                                $OT_to_save = 0;
                             }
                             else if ($resultEmpType["EmployeeType_etid"] == "6") 
                             {
                                $OT_to_save = 0;
                             }
                        }
                        
                        $New_Late_Min = $Late_MIN_DATA + $lateMintsData; 

                        $resCheckDoubleDate = Search("select * from attendance where User_uid = '" . $UserID . "' and date = '".$Date."' and outtime = '".$Time."'");
                        if ($resultCheckDoubleDate = mysqli_fetch_assoc($resCheckDoubleDate)) 
                        {
                            return "Att_OUT_YES";
                        }
                        else
                        {
                            $UpdateQuery = "update attendance set outtime='" . $Time . "', hours = '" . $hours . "', othours = '" . $OT_to_save . "',late_att_min = '".$New_Late_Min."'  where aid = '" . $result["aid"] . "'";
                            $res2 = SUD($UpdateQuery);

                            if ($res2 == 1) 
                            {
                                return "Att_OUT_YES";
                            }
                            else
                            {
                                return "Att_OUT_NO";
                            }
                        }  
                    }
                    
                }
            } 
        }
    }
    else
    {
        //===================OFFICE STAFF================================================
        // Action = In (Intime Calculation)
        if($Action == "C/In")
        {
            if ($Time == "0") 
            {
               $Time = GetWorkingIntimeWeek($UserID);
            }

            $MOThours = 0;
            $is_half = 0;
            $is_short = 0;
            $LeaveType = "";
            $Slot = "";
            $queryHalfShortCheck = "select type,time_slot,days from employee_leave where date = '" . $Date . "' and uid = '" . $UserID . "' and (type like 'Halfday Morning Leave' or type like 'Short Morning Leave' or type like 'Nopay Morning Leave' or type like 'Duty Morning Leave') and is_att_leave = '0'";
            $resHalfShortCheck = Search($queryHalfShortCheck);
            if ($resultHalfShortCheck = mysqli_fetch_assoc($resHalfShortCheck)) 
            {
                $LeaveType =  $resultHalfShortCheck["type"];
                $Slot =  $resultHalfShortCheck["time_slot"];
                $DAYS =  $resultHalfShortCheck["days"];

                 if ($LeaveType == "Halfday Morning Leave") 
                 {   
                    if (strtotime($Time) > strtotime(GetHalfMLate($UserID))) 
                    {
                        $lateMints = (strtotime($Time) - strtotime(GetHalfMLate($UserID)))/60;
                    }
                    else
                    {
                        $lateMints = 0;
                    }

                    $is_half = 1;
                    $is_short = 0;
                     
                 }
                 else if ($LeaveType == "Nopay Morning Leave" || $LeaveType == "Duty Morning Leave") 
                 {   
                    if (strtotime($Time) > strtotime(GetHalfMLate($UserID))) 
                    {
                        $lateMints = (strtotime($Time) - strtotime(GetHalfMLate($UserID)))/60;
                    }
                    else
                    {
                        $lateMints = 0;
                    }

                    $is_half = 0;
                    $is_short = 0;
                     
                 }
                 else if ($LeaveType == "Short Morning Leave") 
                 {
                    if (strtotime($Time) > strtotime(GetShortMLate($UserID))) 
                    {
                        $lateMints = (strtotime($Time) - strtotime(GetShortMLate($UserID)))/60;
                    }
                    else
                    {
                        $lateMints = 0;
                    }

                    $is_half = 0;
                    $is_short = 1;
                 }
                 else
                 {
                    if (strtotime($Time) > strtotime(GetWorkingWeekLate($UserID)))     
                    {
                        $lateMints = (strtotime($Time) - strtotime(GetWorkingIntimeWeek($UserID)))/60;
                    }
                    else
                    {
                        $lateMints = 0;                     
                    }

                    $is_half = 0;
                    $is_short = 0;
                 }   
            }
            else
            {
                if (strtotime($Time) > strtotime(GetWorkingWeekLate($UserID)))     
                {
                    $lateMints = (strtotime($Time) - strtotime(GetWorkingIntimeWeek($UserID)))/60;
                }
                else
                {
                    $lateMints = 0;                     
                }

                $is_half = 0;
                $is_short = 0;
            }


            if ($lateMints > 0) 
            {
                $Att_DATE = explode('-', $Date);
                $check_Tot_Late = Search("select sum(late_att_min) as Total_Late_Min from attendance where YEAR(date) = '" . $Att_DATE[0] . "' and MONTH(date) = '" . $Att_DATE[1] . "' and User_uid = '" . $UserID . "'");
                if ($results_tot_late = mysqli_fetch_assoc($check_Tot_Late)) 
                {
                    $TOTAL_LATE_MIN = $results_tot_late["Total_Late_Min"];
                }

                if ($TOTAL_LATE_MIN > 60) 
                {
                   $query = "insert into employee_leave(date,uid,type,days,time_slot,is_att_leave) values('" . $Date . "','" . $UserID . "','Nopay Leave','1','','1')"; 
                       $res = SUD($query);
                }
                else
                {
                    if ($lateMints > 15 && $lateMints < 30) 
                    {
                        if ($Available_Short == "0") 
                        {
                            $query = "insert into employee_leave(date,uid,type,days,time_slot,is_att_leave) values('" . $Date . "','" . $UserID . "','Nopay Leave','1','','1')"; 
                            $res = SUD($query);
                        }
                        else
                        {
                            $query = "insert into employee_leave(date,uid,type,days,time_slot,is_att_leave) values('" . $Date . "','" . $UserID . "','Short Morning Leave','0.25','','1')"; 
                            $res = SUD($query);

                            $is_half = 0;
                            $is_short = 1;
                        }
                    }
                    else if ($lateMints > 30) 
                    {
                        if ($Available_Leave_Half_OR_CASUAL == "0") 
                        {
                           $query = "insert into employee_leave(date,uid,type,days,time_slot,is_att_leave) values('" . $Date . "','" . $UserID . "','Nopay Leave','1','','1')"; 
                            $res = SUD($query);
                        }
                        else
                        {
                            $query = "insert into employee_leave(date,uid,type,days,time_slot,is_att_leave) values('" . $Date . "','" . $UserID . "','Halfday Morning Leave','0.5','','1')"; 
                            $res = SUD($query);

                            $is_half = 1;
                            $is_short = 0;
                        }
                    }
                }
            }

            $static_morning_OT = $MOThours;

            $resCheckDoubleDate = Search("select * from attendance where User_uid = '" . $UserID . "' and date = '".$Date."' and intime = '".$Time."'");
            if ($resultCheckDoubleDate = mysqli_fetch_assoc($resCheckDoubleDate)) 
            {
                return "Att_IN_YES"; 
            }
            else
            {
                $InsertQuery = "insert into attendance(date,intime,attendance,User_uid,attendanceType_atid,late_att_min,othours,halfday, shortleave) values('" . $Date . "','" . $Time . "','1','" . $UserID . "','1','" . $lateMints . "','" . $MOThours . "','" . $is_half . "','" . $is_short . "')";
                $res1 = SUD($InsertQuery);

                if ($res1 == 1) 
                {
                    return "Att_IN_YES";
                }
                else
                {
                    return "Att_IN_NO";
                }
            }

        }
        elseif($Action == "C/Out") //Action = OUT (Outtime Calaculation)~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        { 
            if ($Time == "0") 
            {
               $Time = GetWorkingOuttimeWeek($UserID);
            }

            $OUTTIME = date("A", strtotime($Time));
            
            if ($OUTTIME == "AM") 
            {
                $queryInDate = "select date from attendance where date = '" . $Date . "' and User_uid = '" . $UserID . "'";
                $resInDate = Search($queryInDate);
                if ($resultInDate = mysqli_fetch_assoc($resInDate)) 
                {
                    $query = "select aid,intime,othours,late_att_min from attendance where date = '" . $Date . "' and User_uid = '" . $UserID . "'";
                    $res = Search($query);
                    if ($result = mysqli_fetch_assoc($res)) 
                    {
                        if ($result["intime"] != "") 
                        {
                            // $T8Mng = date("H:i:s", strtotime("08:30 AM"));
                            $T8Mng = date("H:i:s", strtotime(GetWorkingIntimeWeek($UserID)));
                            $hours = getTimeDifference($Date, $T8Mng, $Date, $Time);

                            $static_morning_OT = $result["othours"];
                            $Late_MIN_DATA = $result["late_att_min"];

                            // echo "Raw Hours ".$hours;

                            //remove Lunch Hour if out time is before 1:30 afternoon
                            $timeIN24HR = date("H:i:s", strtotime($Time));
                            // $T8Mng = date("H:i:s", strtotime("08:30 AM"));
                            $T8Mng = date("H:i:s", strtotime(GetWorkingIntimeWeek($UserID)));
                            $T5Eve = date("H:i:s", strtotime(GetWorkingOuttimeWeek($UserID))); //change 5 to 5.30 (2022/06/13)
                            // $T5Eve = date("H:i:s", strtotime("05:30 PM")); //change 5 to 5.30 (2022/06/13)

                            $T130Aftn = date("H:i:s", strtotime(GetHalfMorningOuttime($UserID)));
                            // if intime not between 8.30 and 1.30 no deduction fro lunch hour
                            if($T8Mng < $timeIN24HR && $T130Aftn > $timeIN24HR)
                            {
                             
                            }
                            else
                            {
                                $hourd_pre_ded_intavels = $hours;
                                $hours = $hours - 0.5; //Lunch Time Duration
                            }
                            

                            $hours = number_format(floatval($hours),2);

                            $Att_DATE = explode('-', $Date);
                            $check_Tot_WORK = Search("select sum(hours) as Total_WORK_HRS from attendance where YEAR(date) = '" . $Att_DATE[0] . "' and MONTH(date) = '" . $Att_DATE[1] . "' and User_uid = '" . $UserID . "'");
                            if ($results_tot_work = mysqli_fetch_assoc($check_Tot_WORK)) 
                            {
                                $TOTAL_WORK_HRS = $results_tot_work["Total_WORK_HRS"];
                            }

                            $othAR = explode(".", number_format($TOTAL_WORK_HRS,2));

                            $TOT_OTH = $othAR[0];
                            $TOT_OTM = $othAR[1];
                            //Calculate OT Value
                            $TOT_MinsOT = ($TOT_OTH*60) + $TOT_OTM;
                            $TOT_WORKvalue = floor($TOT_MinsOT/60);
                            
                            if ($TOT_WORKvalue > 200) 
                            {
                                if($T5Eve < $timeIN24HR){
                                    $OTfromPeriod = getTimeDifference($Date, $T5Eve, $Date, $Time);
                                    $OTfromPeriod = number_format(floatval($OTfromPeriod),2);
                                }
                            }
                            else
                            {
                                $OTfromPeriod = 0;
                            }

                            // new saving ~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                            if($saturday)
                            {
                               //from 8.30 normal hrs , from 1.30 OT hours
                               // echo "<br/>Saturday~~~~~~~~~~~~~~~~~<br/>";
                                $T830 = "08:30";
                                $T130 = "13:30";

                                if ($Time == "0") 
                                {
                                   $Time = GetWorkingOuttimeWeekends($UserID);
                                }

                                $TH830 = date("H:i:s", strtotime("08:00 AM"));
                                // $TH130 = date("H:i:s", strtotime("01:30 PM"));
                                $TH130 = date("H:i:s", strtotime(GetWorkingOuttimeWeekends($UserID)));
                                // $TH145 = date("H:i:s", strtotime("01:45 PM"));
                                $TH145 = date("H:i:s", strtotime(GetWorkingWeekEndsOT($UserID)));
                                $THIN = date("H:i:s", strtotime($result["intime"]));

                                if ($TOT_WORKvalue > 200) 
                                {
                                    $OTHours = getTimeDifference($Date, $TH130, $Date, $Time);
                                }
                                else
                                {
                                    $OTHours = 0;
                                }


                                // if (strtotime($Time) < strtotime("13:30:00"))
                                if (strtotime($Time) < strtotime(GetWorkingOuttimeWeekends($UserID)))  
                                {
                                   // $lateMintsData = (strtotime("13:30:00") - strtotime($Time))/60;
                                    $lateMintsData = (strtotime(GetWorkingOuttimeWeekends($UserID)) - strtotime($Time))/60;
                                }
                                else
                                {
                                    $lateMintsData = 0;
                                }


                                $queryEmpType = "select EmployeeType_etid from user where uid = '" . $UserID . "' and isactive='1'";
                                $resEmpType = Search($queryEmpType);
                                if ($resultEmpType = mysqli_fetch_assoc($resEmpType)) 
                                {
                                     if ($resultEmpType["EmployeeType_etid"] == "1") 
                                     {
                                        $OT_to_save = 0;
                                     }
                                     else if ($resultEmpType["EmployeeType_etid"] == "2") 
                                     {
                                        $OT_to_save = 0;
                                     }
                                     else if ($resultEmpType["EmployeeType_etid"] == "3") 
                                     {
                                        $OT_to_save = 0;
                                     }
                                     else if ($resultEmpType["EmployeeType_etid"] == "4") 
                                     {
                                        $OT_to_save = 0;
                                     }
                                     else if ($resultEmpType["EmployeeType_etid"] == "5") 
                                     {
                                        $OT_to_save = 0;
                                     }
                                     else if ($resultEmpType["EmployeeType_etid"] == "6") 
                                     {
                                        $OT_to_save = 0;
                                     }
                                     else
                                     {
                                        $OT_to_save = $static_morning_OT + $OTHours;
                                     }
                                }
                                
                                $New_Late_Min = $Late_MIN_DATA + $lateMintsData;


                                $resCheckDoubleDate = Search("select * from attendance where User_uid = '" . $UserID . "' and date = '".$Date."' and outtime = '".$Time."'");
                                if ($resultCheckDoubleDate = mysqli_fetch_assoc($resCheckDoubleDate)) 
                                {
                                     return "Att_OUT_YES";
                                }
                                else
                                {
                                    $UpdateQuery = "update attendance set hours='" . $hours . "', outtime='" . $Time . "',othours='" . $OT_to_save . "',late_att_min = '".$New_Late_Min."' where aid = '" . $result["aid"] . "'";
                                    $res2 = SUD($UpdateQuery);

                                    if ($res2 == 1) 
                                    {
                                        return "Att_OUT_YES";
                                    }
                                    else
                                    {
                                        return "Att_OUT_NO";
                                    }
                                }

                            }
                            else
                            {

                                $LeaveType = "";
                                $Slot = "";
                                $queryHalfShortCheck = "select type,time_slot,days from employee_leave where date = '" . $Date . "' and uid = '" . $UserID . "' and (type like 'Halfday Evening Leave' or type like 'Short Evening Leave' or type like 'Nopay Evening Leave' or type like 'Duty Evening Leave') and is_att_leave = '0'";
                                $resHalfShortCheck = Search($queryHalfShortCheck);
                                if ($resultHalfShortCheck = mysqli_fetch_assoc($resHalfShortCheck)) 
                                {
                                    $LeaveType =  $resultHalfShortCheck["type"];
                                    $Slot =  $resultHalfShortCheck["time_slot"];
                                    $DAYS =  $resultHalfShortCheck["days"];

                                    if ($LeaveType == "Halfday Evening Leave") 
                                    {
                                        if (strtotime($Time) < strtotime(GetHalfELate($UserID))) 
                                        {
                                            $lateMintsData = (strtotime(GetHalfELate($UserID)) - strtotime($Time))/60;
                                        }
                                        else
                                        {
                                            $lateMintsData = 0;
                                        }

                                        $UpdateQuery = "update attendance set halfday='1' where aid = '" . $result["aid"] . "'";
                                        SUD($UpdateQuery);

                                        $OT_to_save = $static_morning_OT + $OTfromPeriod;
                                    }
                                    else if ($LeaveType == "Nopay Evening Leave" || $LeaveType == "Duty Evening Leave") 
                                    {
                                        if (strtotime($Time) < strtotime(GetHalfELate($UserID))) 
                                        {
                                            $lateMintsData = (strtotime(GetHalfELate($UserID)) - strtotime($Time))/60;
                                        }
                                        else
                                        {
                                            $lateMintsData = 0;
                                        }

                                        $OT_to_save = $static_morning_OT + $OTfromPeriod;
                                    }
                                    else if ($LeaveType == "Short Evening Leave") 
                                    {  
                                        if (strtotime($Time) < strtotime(GetShortELate($UserID))) 
                                        {
                                            $lateMintsData = (strtotime(GetShortELate($UserID)) - strtotime($Time))/60;
                                        }
                                        else
                                        {
                                            $lateMintsData = 0;
                                        }

                                        $UpdateQuery = "update attendance set shortleave='1' where aid = '" . $result["aid"] . "'";
                                        SUD($UpdateQuery);

                                        $OT_to_save = $static_morning_OT + $OTfromPeriod;
                                    }
                                    else 
                                    {
                                        if (strtotime($Time) < strtotime(GetWorkingOuttimeWeek($UserID)))  
                                        {
                                            $lateMintsData = (strtotime(GetWorkingOuttimeWeek($UserID)) - strtotime($Time))/60;
                                        }
                                        else
                                        {
                                            $lateMintsData = 0;
                                        }

                                        $OT_to_save = $static_morning_OT + $OTfromPeriod;
                                    }   
                                }
                                else
                                {
                                    if (strtotime($Time) < strtotime(GetWorkingOuttimeWeek($UserID)))  
                                    {
                                        $lateMintsData = (strtotime(GetWorkingOuttimeWeek($UserID)) - strtotime($Time))/60;
                                    }
                                    else
                                    {
                                        $lateMintsData = 0;
                                    }

                                    $OT_to_save = $static_morning_OT + $OTfromPeriod;
                                }

                                $queryEmpType = "select EmployeeType_etid from user where uid = '" . $UserID . "' and isactive='1'";
                                $resEmpType = Search($queryEmpType);
                                if ($resultEmpType = mysqli_fetch_assoc($resEmpType)) 
                                {
                                    if ($resultEmpType["EmployeeType_etid"] == "1") 
                                    {
                                        $OT_to_save = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "2") 
                                    {
                                        $OT_to_save = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "3") 
                                    {
                                        $OT_to_save = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "4") 
                                    {
                                        $OT_to_save = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "5") 
                                    {
                                        $OT_to_save = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "6") 
                                    {
                                        $OT_to_save = 0;
                                    }
                                }
                                
                                $New_Late_Min = $Late_MIN_DATA + $lateMintsData; 

                                $resCheckDoubleDate = Search("select * from attendance where User_uid = '" . $UserID . "' and date = '".$Date."' and outtime = '".$Time."'");
                                if ($resultCheckDoubleDate = mysqli_fetch_assoc($resCheckDoubleDate)) 
                                {
                                    return "Att_OUT_YES";
                                }
                                else
                                {
                                    $UpdateQuery = "update attendance set outtime='" . $Time . "', hours = '" . $hours . "', othours = '" . $OT_to_save . "',late_att_min = '".$New_Late_Min."'  where aid = '" . $result["aid"] . "'";
                                    $res2 = SUD($UpdateQuery);

                                    if ($res2 == 1) 
                                    {
                                        return "Att_OUT_YES";
                                    }
                                    else
                                    {
                                        return "Att_OUT_NO";
                                    }
                                } 
                                
                            }

                        }
                        
                    }
                }
                else
                {
                    //check earlier date checked out exists (for OT and DOT calculation)
                    //get earlier date
                    $ydate = strtotime($Date);
                    $ydate = strtotime("-1 day", $ydate);
                    $ydate = date('y-m-d', $ydate);

                    $y = strtotime($ydate);
                    $y = date('l', $y);

                    //if date is Saturday 
                    $saturdays = false;
                    if ($y == "Saturday") 
                    {
                        $saturdays = true;
                    }

                    if ($Time == "0") 
                    {
                       $Time = GetWorkingOuttimeWeek($UserID);
                    }


                    $query = "select aid,intime,outtime from attendance where date = '" . $ydate . "' and User_uid = '" . $UserID . "'";
                    $res = Search($query);
                    if ($result = mysqli_fetch_assoc($res)) {

                        if($result["outtime"] == "")
                        {
                            // $earlyDateIn = date("H:i:s", strtotime("08:30 AM"));
                            $earlyDateIn = date("H:i:s", strtotime(GetWorkingIntimeWeek($UserID)));
                            $hours = getTimeDifference($ydate, $earlyDateIn, $Date, $Time);
                            
                            $timeIN24HR = date("H:i:s", strtotime($Time));
                            $T130Aftn = date("H:i:s", strtotime(GetHalfMorningOuttime($UserID)));
                            // if intime not between 8.30 and 1.30 no deduction fro lunch hour
                            if($earlyDateIn < $timeIN24HR && $T130Aftn > $timeIN24HR)
                            {
                             
                            }
                            else
                            {
                                $hourd_pre_ded_intavels = $hours;
                                $hours = $hours - 0.5; //Lunch Time Duration
                            }

                            $hours = number_format(floatval($hours),2);

                            $Att_DATE = explode('-', $Date);
                            $check_Tot_WORK = Search("select sum(hours) as Total_WORK_HRS from attendance where YEAR(date) = '" . $Att_DATE[0] . "' and MONTH(date) = '" . $Att_DATE[1] . "' and User_uid = '" . $UserID . "'");
                            if ($results_tot_work = mysqli_fetch_assoc($check_Tot_WORK)) 
                            {
                                $TOTAL_WORK_HRS = $results_tot_work["Total_WORK_HRS"];
                            }

                            $othAR = explode(".", number_format($TOTAL_WORK_HRS,2));

                            $TOT_OTH = $othAR[0];
                            $TOT_OTM = $othAR[1];
                            //Calculate OT Value
                            $TOT_MinsOT = ($TOT_OTH*60) + $TOT_OTM;
                            $TOT_WORKvalue = floor($TOT_MinsOT/60);

                            if($saturdays)
                            {
                                if ($Time == "0") 
                                {
                                   $Time = GetWorkingOuttimeWeekends($UserID);
                                }

                                $TH130 = date("H:i:s", strtotime(GetWorkingOuttimeWeekends($UserID)));
                                $TH12 = date("H:i:s", strtotime("12:00 AM"));
                                $GetTime = date("H:i:s", strtotime($Time));            //NEW PART

                                if ($TOT_WORKvalue > 200) 
                                {
                                    $OTHours = getTimeDifference($ydate, $TH130, $Date, $Time);//NEW PART
                                    $OTHours = number_format(floatval($OTHours),2);//NEW PART
                                }
                                else
                                {
                                    $OTHours = 0;//NEW PART
                                }

                                $queryEmpType = "select EmployeeType_etid from user where uid = '" . $UserID . "' and isactive='1'";
                                $resEmpType = Search($queryEmpType);
                                if ($resultEmpType = mysqli_fetch_assoc($resEmpType)) 
                                {
                                    if ($resultEmpType["EmployeeType_etid"] == "1") 
                                    {
                                        $OT_to_save = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "2") 
                                    {
                                        $OT_to_save = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "3") 
                                    {
                                        $OT_to_save = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "4") 
                                    {
                                        $OT_to_save = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "5") 
                                    {
                                        $OT_to_save = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "6") 
                                    {
                                        $OT_to_save = 0;
                                    }
                                    else
                                    {
                                        $OT_to_save = $static_morning_OT + $OTHours;
                                    }
                                }
                                
                                
                                $resCheckDoubleDate = Search("select * from attendance where User_uid = '" . $UserID . "' and date = '".$Date."' and outtime = '".$Time."'");
                                if ($resultCheckDoubleDate = mysqli_fetch_assoc($resCheckDoubleDate)) 
                                {
                                    return "Att_OUT_YES";
                                }
                                else
                                {
                                    $UpdateQuery = "update attendance set hours='" . $hours . "', outtime='" . $Time . "',othours='" . $OT_to_save . "',dothours='" . $DOTHours . "' where aid = '" . $result["aid"] . "'";
                                    $res2 = SUD($UpdateQuery);

                                    if ($res2 == 1) 
                                    {
                                        return "Att_OUT_YES";
                                    }
                                    else
                                    {
                                        return "Att_OUT_NO";
                                    }
                                }

                            }
                            else
                            {
                                //outs from OT and DOT done
                                // $T5Eve = date("H:i:s", strtotime("05:30 PM")); //change 5 to 5.30 (2022/06/13)
                                $T5Eve = date("H:i:s", strtotime(GetWorkingOuttimeWeek($UserID))); //change 5 to 5.30 (2022/06/13)
                                if ($TOT_WORKvalue > 200) 
                                {
                                    $OThours = getTimeDifference($ydate, $T5Eve, $Date, $Time);
                                    $OThours = number_format(floatval($OThours),2);
                                }
                                else
                                {
                                    $OThours = 0;//NEW PART
                                }

                                $queryEmpType = "select EmployeeType_etid from user where uid = '" . $UserID . "' and isactive='1'";
                                $resEmpType = Search($queryEmpType);
                                if ($resultEmpType = mysqli_fetch_assoc($resEmpType)) 
                                {
                                    if ($resultEmpType["EmployeeType_etid"] == "1") 
                                    {
                                        $OThours = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "2") 
                                    {
                                        $OThours = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "3") 
                                    {
                                        $OThours = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "4") 
                                    {
                                        $OThours = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "5") 
                                    {
                                        $OThours = 0;
                                    }
                                    else if ($resultEmpType["EmployeeType_etid"] == "6") 
                                    {
                                        $OThours = 0;
                                    }
                                }


                                $resCheckDoubleDate = Search("select * from attendance where User_uid = '" . $UserID . "' and date = '".$Date."' and outtime = '".$Time."'");
                                if ($resultCheckDoubleDate = mysqli_fetch_assoc($resCheckDoubleDate)) 
                                {
                                    return "Att_OUT_YES"; 
                                }
                                else
                                {
                                    $UpdateQuery = "update attendance set date = '".$ydate."', outtime='" . $Time . "',attendance='1',User_uid = '".$UserID."',attendanceType_atid = '1', hours = '" . $hours . "', othours = '" . $OThours . "'  where aid = '" . $result["aid"] . "'";
                                    $res2 = SUD($UpdateQuery);

                                    if ($res2 == 1) 
                                    {
                                        return "Att_OUT_YES";
                                    }
                                    else
                                    {
                                        return "Att_OUT_NO";
                                    }
                                }
                            }

                        }
                        else
                        {
                            //no clue of the out time (earlier date out exists, current date in missing)
                        }

                    }
                }        
        
            }
            else
            {
                if ($Time == "0") 
                {
                   $Time = GetWorkingOuttimeWeeke($UserID);
                }

                $query = "select aid,intime,othours,late_att_min from attendance where date = '" . $Date . "' and User_uid = '" . $UserID . "'";
                $res = Search($query);
                if ($result = mysqli_fetch_assoc($res)) 
                {
                    if ($result["intime"] != "") 
                    {
                        // $T8Mng = date("H:i:s", strtotime("08:30 AM"));
                        $T8Mng = date("H:i:s", strtotime(GetWorkingIntimeWeek($UserID)));
                        $hours = getTimeDifference($Date, $T8Mng, $Date, $Time);

                        $static_morning_OT = $result["othours"];
                        $Late_MIN_DATA = $result["late_att_min"];

                    // echo "Raw Hours ".$hours;

                        //remove Lunch Hour if out time is before 1:30 afternoon
                        $timeIN24HR = date("H:i:s", strtotime($Time));
                        // $T8Mng = date("H:i:s", strtotime("08:30 AM"));
                        $T8Mng = date("H:i:s", strtotime(GetWorkingIntimeWeek($UserID)));
                        // $T5Eve = date("H:i:s", strtotime("05:30 PM")); //change 5 to 5.30 (2022/06/13)
                        $T5Eve = date("H:i:s", strtotime(GetWorkingOuttimeWeek($UserID))); //change 5 to 5.30 (2022/06/13)

                        $T130Aftn = date("H:i:s", strtotime(GetHalfMorningOuttime($UserID)));
                        // if intime not between 8.30 and 1.30 no deduction fro lunch hour
                        if($T8Mng < $timeIN24HR && $T130Aftn > $timeIN24HR)
                        {
                         
                        }
                        else
                        {
                            $hourd_pre_ded_intavels = $hours;
                            $hours = $hours - 0.5; //Lunch Time Duration
                        }

                        $hours = number_format(floatval($hours),2);

                        $Att_DATE = explode('-', $Date);
                        $check_Tot_WORK = Search("select sum(hours) as Total_WORK_HRS from attendance where YEAR(date) = '" . $Att_DATE[0] . "' and MONTH(date) = '" . $Att_DATE[1] . "' and User_uid = '" . $UserID . "'");
                        if ($results_tot_work = mysqli_fetch_assoc($check_Tot_WORK)) 
                        {
                            $TOTAL_WORK_HRS = $results_tot_work["Total_WORK_HRS"];
                        }

                        $othAR = explode(".", number_format($TOTAL_WORK_HRS,2));

                        $TOT_OTH = $othAR[0];
                        $TOT_OTM = $othAR[1];
                        //Calculate OT Value
                        $TOT_MinsOT = ($TOT_OTH*60) + $TOT_OTM;
                        $TOT_WORKvalue = floor($TOT_MinsOT/60);
                        
                        if ($TOT_WORKvalue > 200) 
                        {
                            if($T5Eve < $timeIN24HR){
                                $OTfromPeriod = getTimeDifference($Date, $T5Eve, $Date, $Time);
                                $OTfromPeriod = number_format(floatval($OTfromPeriod),2);
                            }
                        }
                        else
                        {
                            $OTfromPeriod = 0;
                        }


                        // new saving ~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                        if($saturday)
                        {
                           //from 8.30 normal hrs , from 1.30 OT hours
                           // echo "<br/>Saturday~~~~~~~~~~~~~~~~~<br/>";
                            $T830 = "08:30";
                            $T130 = "13:30";

                            if ($Time == "0") 
                            {
                               $Time = GetWorkingOuttimeWeekends($UserID);
                            }

                            $TH830 = date("H:i:s", strtotime("08:00 AM"));
                            // $TH130 = date("H:i:s", strtotime("01:30 PM"));
                            $TH130 = date("H:i:s", strtotime(GetWorkingOuttimeWeekends($UserID)));
                            // $TH145 = date("H:i:s", strtotime("01:45 PM"));
                            $TH145 = date("H:i:s", strtotime(GetWorkingWeekEndsOT($UserID)));
                            $THIN = date("H:i:s", strtotime($result["intime"]));

                            if ($TOT_WORKvalue > 200) 
                            {
                                $OTHours = getTimeDifference($Date, $TH130, $Date, $Time);
                            }
                            else
                            {
                                $OTHours = 0;
                            }

                            // if (strtotime($Time) < strtotime("13:30:00"))
                            if (strtotime($Time) < strtotime(GetWorkingOuttimeWeekends($UserID)))  
                            {
                                //$lateMintsData = (strtotime("13:30:00") - strtotime($Time))/60;
                                $lateMintsData = (strtotime(GetWorkingOuttimeWeekends($UserID)) - strtotime($Time))/60;
                            }
                            else
                            {
                                $lateMintsData = 0;
                            }


                            $queryEmpType = "select EmployeeType_etid from user where uid = '" . $UserID . "' and isactive='1'";
                            $resEmpType = Search($queryEmpType);
                            if ($resultEmpType = mysqli_fetch_assoc($resEmpType)) 
                            {
                                if ($resultEmpType["EmployeeType_etid"] == "1") 
                                {
                                    $OT_to_save = 0;
                                }
                                else if ($resultEmpType["EmployeeType_etid"] == "2") 
                                {
                                    $OT_to_save = 0;
                                }
                                else if ($resultEmpType["EmployeeType_etid"] == "3") 
                                {
                                    $OT_to_save = 0;
                                }
                                else if ($resultEmpType["EmployeeType_etid"] == "4") 
                                {
                                    $OT_to_save = 0;
                                }
                                else if ($resultEmpType["EmployeeType_etid"] == "5") 
                                {
                                    $OT_to_save = 0;
                                }
                                else if ($resultEmpType["EmployeeType_etid"] == "6") 
                                {
                                    $OT_to_save = 0;
                                }
                                else
                                {
                                    $OT_to_save = $static_morning_OT + $OTHours;
                                }
                            }
                            
                            $New_Late_Min = $Late_MIN_DATA + $lateMintsData;

                            $resCheckDoubleDate = Search("select * from attendance where User_uid = '" . $UserID . "' and date = '".$Date."' and outtime = '".$Time."'");
                            if ($resultCheckDoubleDate = mysqli_fetch_assoc($resCheckDoubleDate)) 
                            {
                                return "Att_OUT_YES"; 
                            }
                            else
                            {
                                $UpdateQuery = "update attendance set hours='" . $hours . "', outtime='" . $Time . "',othours='" . $OT_to_save . "',late_att_min = '".$New_Late_Min."' where aid = '" . $result["aid"] . "'";
                                $res2 = SUD($UpdateQuery);

                                if ($res2 == 1) 
                                {
                                    return "Att_OUT_YES";
                                }
                                else
                                {
                                    return "Att_OUT_NO";
                                }
                            }

                        }
                        else
                        {
                            $LeaveType = "";
                            $Slot = "";
                            $queryHalfShortCheck = "select type,time_slot,days from employee_leave where date = '" . $Date . "' and uid = '" . $UserID . "' and (type like 'Halfday Evening Leave' or type like 'Short Evening Leave' or type like 'Nopay Evening Leave' or type like 'Duty Evening Leave') and is_att_leave = '0'";
                            $resHalfShortCheck = Search($queryHalfShortCheck);
                            if ($resultHalfShortCheck = mysqli_fetch_assoc($resHalfShortCheck)) 
                            {
                                $LeaveType =  $resultHalfShortCheck["type"];
                                $Slot =  $resultHalfShortCheck["time_slot"];
                                $DAYS =  $resultHalfShortCheck["days"];

                                if ($LeaveType == "Halfday Evening Leave") 
                                {
                                    if (strtotime($Time) < strtotime(GetHalfELate($UserID))) 
                                    {
                                        $lateMintsData = (strtotime(GetHalfELate($UserID)) - strtotime($Time))/60;
                                    }
                                    else
                                    {
                                        $lateMintsData = 0;
                                    }

                                    $UpdateQuery = "update attendance set halfday='1' where aid = '" . $result["aid"] . "'";
                                    SUD($UpdateQuery);

                                    $OT_to_save = $static_morning_OT + $OTfromPeriod;
                                }
                                else if ($LeaveType == "Nopay Evening Leave" || $LeaveType == "Duty Evening Leave") 
                                {
                                    if (strtotime($Time) < strtotime(GetHalfELate($UserID))) 
                                    {
                                        $lateMintsData = (strtotime(GetHalfELate($UserID)) - strtotime($Time))/60;
                                    }
                                    else
                                    {
                                        $lateMintsData = 0;
                                    }

                                    $OT_to_save = $static_morning_OT + $OTfromPeriod;
                                }
                                else if ($LeaveType == "Short Evening Leave") 
                                {
                                    if (strtotime($Time) < strtotime(GetShortELate($UserID))) 
                                    {
                                        $lateMintsData = (strtotime(GetShortELate($UserID)) - strtotime($Time))/60;
                                    }
                                    else
                                    {
                                        $lateMintsData = 0;
                                    }

                                    $UpdateQuery = "update attendance set shortleave='1' where aid = '" . $result["aid"] . "'";
                                    SUD($UpdateQuery);

                                    $OT_to_save = $static_morning_OT + $OTfromPeriod;
                                }
                                else
                                {
                                    if (strtotime($Time) < strtotime(GetWorkingOuttimeWeek($UserID)))  
                                    {
                                        $lateMintsData = (strtotime(GetWorkingOuttimeWeek($UserID)) - strtotime($Time))/60;
                                    }
                                    else
                                    {
                                        $lateMintsData = 0;
                                    }

                                    $OT_to_save = $static_morning_OT + $OTfromPeriod;
                                }   
                            }
                            else
                            {
                                if (strtotime($Time) < strtotime(GetWorkingOuttimeWeek($UserID)))  
                                {
                                    $lateMintsData = (strtotime(GetWorkingOuttimeWeek($UserID)) - strtotime($Time))/60;
                                }
                                else
                                {
                                    $lateMintsData = 0;
                                }

                                $OT_to_save = $static_morning_OT + $OTfromPeriod;

                            }


                            $queryEmpType = "select EmployeeType_etid from user where uid = '" . $UserID . "' and isactive='1'";
                            $resEmpType = Search($queryEmpType);
                            if ($resultEmpType = mysqli_fetch_assoc($resEmpType)) 
                            {
                                if ($resultEmpType["EmployeeType_etid"] == "1") 
                                {
                                    $OT_to_save = 0;
                                }
                                else if ($resultEmpType["EmployeeType_etid"] == "2") 
                                {
                                    $OT_to_save = 0;
                                }
                                else if ($resultEmpType["EmployeeType_etid"] == "3") 
                                {
                                    $OT_to_save = 0;
                                }
                                else if ($resultEmpType["EmployeeType_etid"] == "4") 
                                {
                                    $OT_to_save = 0;
                                }
                                else if ($resultEmpType["EmployeeType_etid"] == "5") 
                                {
                                    $OT_to_save = 0;
                                }
                                else if ($resultEmpType["EmployeeType_etid"] == "6") 
                                {
                                    $OT_to_save = 0;
                                }
                            }
                            
                            $New_Late_Min = $Late_MIN_DATA + $lateMintsData; 

                            $resCheckDoubleDate = Search("select * from attendance where User_uid = '" . $UserID . "' and date = '".$Date."' and outtime = '".$Time."'");
                            if ($resultCheckDoubleDate = mysqli_fetch_assoc($resCheckDoubleDate)) 
                            {
                                return "Att_OUT_YES"; 
                            }
                            else
                            {
                                $UpdateQuery = "update attendance set outtime='" . $Time . "', hours = '" . $hours . "', othours = '" . $OT_to_save . "',late_att_min = '".$New_Late_Min."'  where aid = '" . $result["aid"] . "'";
                                $res2 = SUD($UpdateQuery);

                                if ($res2 == 1) 
                                {
                                    return "Att_OUT_YES";
                                }
                                else
                                {
                                    return "Att_OUT_NO";
                                }
                            }     
                        }
                    }
                    
                }
            } 
        }
    }
    
}

function getTimeDifference($date1, $time1, $date2, $time2) {

    $start = date_create($date1 . " " . $time1);
    $end = date_create($date2 . " " . $time2);
    $diff = date_diff($end, $start); 
    $rouded = $diff->format('%h.%I');

    return $rouded;
}

function MinutesTOHours($minutes) {

    $dev = $minutes/60;
    return $dev; 
}


// Normal Working Data

function GetWorkingIntimeWeek($UserID) 
{
    $resWorkingIntime = Search("select intime from settings_working_times where update_user = '".$UserID."'");
    if ($resultIntime = mysqli_fetch_assoc($resWorkingIntime)) 
    {
        $INTIME = $resultIntime["intime"];
    }

    return $INTIME; 
}

function GetWorkingOuttimeWeek($UserID) 
{
    $resWorkingOuttime = Search("select outtime from settings_working_times where update_user = '".$UserID."'");
    if ($resultOuttime = mysqli_fetch_assoc($resWorkingOuttime)) 
    {
        $OUTTIME = $resultOuttime["outtime"];
    }
    
    return $OUTTIME; 
}

function GetWorkingIntimeWeekends($UserID) 
{
    $resWorkingIntimeWeekends = Search("select satintime from settings_working_times where update_user = '".$UserID."'");
    if ($resultWorkingIntimeWeekends = mysqli_fetch_assoc($resWorkingIntimeWeekends)) 
    {
        $INTIME = $resultWorkingIntimeWeekends["satintime"];
    }
    
    return $INTIME; 
}

function GetWorkingOuttimeWeekends($UserID) 
{
    $resWorkingOuttimeWeekends = Search("select satouttime from settings_working_times where update_user = '".$UserID."'");
    if ($resultWorkingOuttimeWeekends = mysqli_fetch_assoc($resWorkingOuttimeWeekends)) 
    {
        $OUTTIME = $resultWorkingOuttimeWeekends["satouttime"];
    }
    
    return $OUTTIME; 
}


function GetHalfMorningIntime($UserID) 
{
    $resHalfMorningIntime = Search("select half_slot_morning from settings_working_times where update_user = '".$UserID."'");
    if ($resultHalfMorningIntime = mysqli_fetch_assoc($resHalfMorningIntime)) 
    {
        $Data = explode(" ",$resultHalfMorningIntime["half_slot_morning"]);
        $INTIME = $Data[0];
    }

    return $INTIME; 
}

function GetHalfMorningOuttime($UserID) 
{
    $resHalfMorningOuttime = Search("select half_slot_morning from settings_working_times where update_user = '".$UserID."'");
    if ($resultHalfMorningOuttime = mysqli_fetch_assoc($resHalfMorningOuttime)) 
    {
        $Data = explode(" ",$resultHalfMorningOuttime["half_slot_morning"]);
        $OUTTIME = $Data[3];
    }
    
    return $OUTTIME; 
}


function GetHalfEveningIntime($UserID) 
{
    $resHalfEveningIntime = Search("select half_slot_evening from settings_working_times where update_user = '".$UserID."'");
    if ($resultHalfEveningIntime = mysqli_fetch_assoc($resHalfEveningIntime)) 
    {
        $Data = explode(" ",$resultHalfEveningIntime["half_slot_evening"]);
        $INTIME = $Data[0];
    }

    return $INTIME; 
}

function GetHalfEveningOuttime($UserID) 
{
    $resHalfEveningOuttime = Search("select half_slot_evening from settings_working_times where update_user = '".$UserID."'");
    if ($resultHalfEveningOuttime = mysqli_fetch_assoc($resHalfEveningOuttime)) 
    {
        $Data = explode(" ",$resultHalfEveningOuttime["half_slot_evening"]);
        $OUTTIME = $Data[3];
    }
    
    return $OUTTIME; 
}


function GetShortMorningIntime($UserID) 
{
    $resShortMorningIntime = Search("select short_morning from settings_working_times where update_user = '".$UserID."'");
    if ($resultShortMorningIntime = mysqli_fetch_assoc($resShortMorningIntime)) 
    {
        $Data = explode(" ",$resultShortMorningIntime["short_morning"]);
        $INTIME = $Data[0];
    }

    return $INTIME; 
}

function GetShortMorningOuttime($UserID) 
{
    $resShortMorningOuttime = Search("select short_morning from settings_working_times where update_user = '".$UserID."'");
    if ($resultShortMorningOuttime = mysqli_fetch_assoc($resShortMorningOuttime)) 
    {
        $Data = explode(" ",$resultShortMorningOuttime["short_morning"]);
        $OUTTIME = $Data[3];
    }
    
    return $OUTTIME; 
}


function GetShortEveningIntime($UserID) 
{
    $resShortEveningIntime = Search("select short_evening from settings_working_times where update_user = '".$UserID."'");
    if ($resultShortEveningIntime = mysqli_fetch_assoc($resShortEveningIntime)) 
    {
        $Data = explode(" ",$resultShortEveningIntime["short_evening"]);
        $INTIME = $Data[0];
    }

    return $INTIME; 
}

function GetShortEveningOuttime($UserID) 
{
    $resShortEveningOuttime = Search("select short_evening from settings_working_times where update_user = '".$UserID."'");
    if ($resultShortEveningOuttime = mysqli_fetch_assoc($resShortEveningOuttime)) 
    {
        $Data = explode(" ",$resultShortEveningOuttime["short_evening"]);
        $OUTTIME = $Data[3];
    }
    
    return $OUTTIME; 
}

function GetWorkingWeekLate($UserID) 
{
    $resWorkingWeekLate = Search("select weekdays_late from settings_working_times where update_user = '".$UserID."'");
    if ($resultWeekLate = mysqli_fetch_assoc($resWorkingWeekLate)) 
    {
        $INTIME = $resultWeekLate["weekdays_late"];
    }

    return $INTIME; 
}

function GetWorkingWeekOT($UserID) 
{
    $resWorkingWeekOT = Search("select weekdays_ot from settings_working_times where update_user = '".$UserID."'");
    if ($resultWeekOT = mysqli_fetch_assoc($resWorkingWeekOT)) 
    {
        $INTIME = $resultWeekOT["weekdays_ot"];
    }

    return $INTIME; 
}

function GetWorkingWeekEndsLate($UserID) 
{
    $resWorkingWeekEndsLate = Search("select weekends_late from settings_working_times where update_user = '".$UserID."'");
    if ($resultWeekEndsLate = mysqli_fetch_assoc($resWorkingWeekEndsLate)) 
    {
        $INTIME = $resultWeekEndsLate["weekends_late"];
    }

    return $INTIME; 
}

function GetWorkingWeekEndsOT($UserID) 
{
    $resWorkingWeekEndsOT = Search("select weekends_ot from settings_working_times where update_user = '".$UserID."'");
    if ($resultWeekEndsOT = mysqli_fetch_assoc($resWorkingWeekEndsOT)) 
    {
        $INTIME = $resultWeekEndsOT["weekends_ot"];
    }

    return $INTIME; 
}

function GetHalfMLate($UserID) 
{
    $resHalfMLate = Search("select half_m_late from settings_working_times where update_user = '".$UserID."'");
    if ($resultHalfMLate = mysqli_fetch_assoc($resHalfMLate)) 
    {
        $INTIME = $resultHalfMLate["half_m_late"];
    }

    return $INTIME; 
}

function GetHalfELate($UserID) 
{
    $resHalfELate = Search("select half_e_late from settings_working_times where update_user = '".$UserID."'");
    if ($resultHalfELate = mysqli_fetch_assoc($resHalfELate)) 
    {
        $INTIME = $resultHalfELate["half_e_late"];
    }

    return $INTIME; 
}

function GetShortMLate($UserID) 
{
    $resWorkingShortMLate = Search("select short_m_late from settings_working_times where update_user = '".$UserID."'");
    if ($resultShortMLate = mysqli_fetch_assoc($resWorkingShortMLate)) 
    {
        $INTIME = $resultShortMLate["short_m_late"];
    }

    return $INTIME; 
}

function GetShortELate($UserID) 
{
    $resWorkingShortELate = Search("select short_e_late from settings_working_times where update_user = '".$UserID."'");
    if ($resultShortELate = mysqli_fetch_assoc($resWorkingShortELate)) 
    {
        $INTIME = $resultShortELate["short_e_late"];
    }

    return $INTIME; 
}





// Shift Working Data

function GetShiftIntime($Shift_T_ID) 
{
    $resWorkingIntime = Search("select sh_intime from shift_working_time_profile_settings where swtpsid = '".$Shift_T_ID."'");
    if ($resultIntime = mysqli_fetch_assoc($resWorkingIntime)) 
    {
        $INTIME = $resultIntime["sh_intime"];
    }

    return $INTIME; 
}

function GetShiftOuttime($Shift_T_ID) 
{
    $resWorkingOuttime = Search("select sh_outtime from shift_working_time_profile_settings where swtpsid = '".$Shift_T_ID."'");
    if ($resultOuttime = mysqli_fetch_assoc($resWorkingOuttime)) 
    {
        $OUTTIME = $resultOuttime["sh_outtime"];
    }
    
    return $OUTTIME; 
}

function GetShiftHalfMorningIntime($Shift_T_ID) 
{
    $resHalfMorningIntime = Search("select sh_half_slot_morning from shift_working_time_profile_settings where swtpsid = '".$Shift_T_ID."'");
    if ($resultHalfMorningIntime = mysqli_fetch_assoc($resHalfMorningIntime)) 
    {
        $Data = explode(" ",$resultHalfMorningIntime["sh_half_slot_morning"]);
        $INTIME = $Data[0];
    }

    return $INTIME; 
}

function GetShiftHalfMorningOuttime($Shift_T_ID) 
{
    $resHalfMorningOuttime = Search("select sh_half_slot_morning from shift_working_time_profile_settings where swtpsid = '".$Shift_T_ID."'");
    if ($resultHalfMorningOuttime = mysqli_fetch_assoc($resHalfMorningOuttime)) 
    {
        $Data = explode(" ",$resultHalfMorningOuttime["sh_half_slot_morning"]);
        $OUTTIME = $Data[3];
    }
    
    return $OUTTIME; 
}


function GetShiftHalfEveningIntime($Shift_T_ID) 
{
    $resHalfEveningIntime = Search("select sh_half_slot_evening from shift_working_time_profile_settings where swtpsid = '".$Shift_T_ID."'");
    if ($resultHalfEveningIntime = mysqli_fetch_assoc($resHalfEveningIntime)) 
    {
        $Data = explode(" ",$resultHalfEveningIntime["sh_half_slot_evening"]);
        $INTIME = $Data[0];
    }

    return $INTIME; 
}

function GetShiftHalfEveningOuttime($Shift_T_ID) 
{
    $resHalfEveningOuttime = Search("select sh_half_slot_evening from shift_working_time_profile_settings where swtpsid = '".$Shift_T_ID."'");
    if ($resultHalfEveningOuttime = mysqli_fetch_assoc($resHalfEveningOuttime)) 
    {
        $Data = explode(" ",$resultHalfEveningOuttime["sh_half_slot_evening"]);
        $OUTTIME = $Data[3];
    }
    
    return $OUTTIME; 
}


function GetShiftShortMorningIntime($Shift_T_ID) 
{
    $resShortMorningIntime = Search("select sh_short_morning from shift_working_time_profile_settings where swtpsid = '".$Shift_T_ID."'");
    if ($resultShortMorningIntime = mysqli_fetch_assoc($resShortMorningIntime)) 
    {
        $Data = explode(" ",$resultShortMorningIntime["sh_short_morning"]);
        $INTIME = $Data[0];
    }

    return $INTIME; 
}

function GetShiftShortMorningOuttime($Shift_T_ID) 
{
    $resShortMorningOuttime = Search("select sh_short_morning from shift_working_time_profile_settings where swtpsid = '".$Shift_T_ID."'");
    if ($resultShortMorningOuttime = mysqli_fetch_assoc($resShortMorningOuttime)) 
    {
        $Data = explode(" ",$resultShortMorningOuttime["sh_short_morning"]);
        $OUTTIME = $Data[3];
    }
    
    return $OUTTIME; 
}


function GetShiftShortEveningIntime($Shift_T_ID) 
{
    $resShortEveningIntime = Search("select sh_short_evening from shift_working_time_profile_settings where swtpsid = '".$Shift_T_ID."'");
    if ($resultShortEveningIntime = mysqli_fetch_assoc($resShortEveningIntime)) 
    {
        $Data = explode(" ",$resultShortEveningIntime["sh_short_evening"]);
        $INTIME = $Data[0];
    }

    return $INTIME; 
}

function GetShiftShortEveningOuttime($Shift_T_ID) 
{
    $resShortEveningOuttime = Search("select sh_short_evening from shift_working_time_profile_settings where swtpsid = '".$Shift_T_ID."'");
    if ($resultShortEveningOuttime = mysqli_fetch_assoc($resShortEveningOuttime)) 
    {
        $Data = explode(" ",$resultShortEveningOuttime["sh_short_evening"]);
        $OUTTIME = $Data[3];
    }
    
    return $OUTTIME; 
}

function GetShiftLate($Shift_T_ID) 
{
    $resWorkingWeekLate = Search("select sh_working_late from shift_working_time_profile_settings where swtpsid = '".$Shift_T_ID."'");
    if ($resultWeekLate = mysqli_fetch_assoc($resWorkingWeekLate)) 
    {
        $INTIME = $resultWeekLate["sh_working_late"];
    }

    return $INTIME; 
}


function GetShiftHalfMLate($Shift_T_ID) 
{
    $resHalfMLate = Search("select sh_half_m_late from shift_working_time_profile_settings where swtpsid = '".$Shift_T_ID."'");
    if ($resultHalfMLate = mysqli_fetch_assoc($resHalfMLate)) 
    {
        $INTIME = $resultHalfMLate["sh_half_m_late"];
    }

    return $INTIME; 
}

function GetShiftHalfELate($Shift_T_ID) 
{
    $resHalfELate = Search("select sh_half_e_late from shift_working_time_profile_settings where swtpsid = '".$Shift_T_ID."'");
    if ($resultHalfELate = mysqli_fetch_assoc($resHalfELate)) 
    {
        $INTIME = $resultHalfELate["sh_half_e_late"];
    }

    return $INTIME; 
}

function GetShiftShortMLate($Shift_T_ID) 
{
    $resWorkingShortMLate = Search("select sh_short_m_late from shift_working_time_profile_settings where swtpsid = '".$Shift_T_ID."'");
    if ($resultShortMLate = mysqli_fetch_assoc($resWorkingShortMLate)) 
    {
        $INTIME = $resultShortMLate["sh_short_m_late"];
    }

    return $INTIME; 
}

function GetShiftShortELate($Shift_T_ID) 
{
    $resWorkingShortELate = Search("select sh_short_e_late from shift_working_time_profile_settings where swtpsid = '".$Shift_T_ID."'");
    if ($resultShortELate = mysqli_fetch_assoc($resWorkingShortELate)) 
    {
        $INTIME = $resultShortELate["sh_short_e_late"];
    }

    return $INTIME; 
}

?>