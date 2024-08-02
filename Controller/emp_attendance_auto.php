<?php
error_reporting(0);
session_start();
date_default_timezone_set('Asia/Colombo');

// include '../DB/DB.php';
$DB = new Database();

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


function getTimeDifference($date1, $time1, $date2, $time2) 
{
    $start = date_create($date1 . " " . $time1);
    $end = date_create($date2 . " " . $time2);
    $diff = date_diff($end, $start); 
    $rouded = $diff->format('%h.%I');
    return $rouded;
}

function MinutesTOHours($minutes) 
{
    $dev = $minutes/60;
    return $dev; 
}

function getTimeDifference2($date1, $time1, $date2, $time2) 
{
    $start = date_create($date1 . " " . $time1);
    $end = date_create($date2 . " " . $time2);
    $diff = date_diff($end, $start); 
    $rouded = $diff->format('%h.%I');
    return  $rouded;
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

