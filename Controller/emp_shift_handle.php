<?php
error_reporting(0);
session_start();
date_default_timezone_set('Asia/Colombo');

include '../DB/DB.php';

$DB = new Database();

if (isset($_REQUEST["request"])) 
{
    $out = "";
    
    if($_REQUEST["request"]=="getShiftDetails")
    {
        $out="<table  class='table table-striped'><thead style='position : sticky; top : 0;  z-index: 0; background-color: #9eafba; color: black;'><tr><th>Employee Name</th><th>Date</th><th>Rost Name</th><th>Status</th><th></th></tr></thead>";

        $res = Search("select a.ehsid,a.espid,a.date,a.status,a.user_uid,b.name,c.fname,c.jobcode from emp_has_shift a LEFT JOIN shift_working_time_profile_settings b ON a.espid = b.swtpsid,user c where a.user_uid = c.uid and a.date between '".$_REQUEST["DATEFROM"]."' and '".$_REQUEST["DATETO"]."' and a.espid like '".$_REQUEST["TYPE"]."' and a.user_uid like '".$_REQUEST["EMP_ID"]."' and a.status like '".$_REQUEST["STATUS"]."' order by a.date ASC");

        while ($result = mysqli_fetch_assoc($res)) 
        {
            if(!$result["name"]==null)
            {
                $Shift_name=$result["name"];
            }
            else
            {
                $Shift_name="-";
            }

            $details=$result["ehsid"] . "#" . $result["date"] . "#" . $result["espid"] . "#" . $result["user_uid"]; 


            if($result["status"]=="0")
            {
                $Status="Not-Active";
                $Action="Style='color:red;'";

                $out.="<tr><td>".$result["jobcode"]." - ".$result["fname"]."</td><td>".$result["date"]."</td><td>".$Shift_name."</td><td ".$Action.">".$Status."</td><td></td></tr>";
            }
            else
            {
                $Status="Active";
                $Action="Style='color:green;'";

                $out.="<tr><td>".$result["jobcode"]." - ".$result["fname"]."</td><td>".$result["date"]."</td><td>".$Shift_name."</td><td ".$Action.">".$Status."</td><td><img src='../Icons/update.png' style='cursor: pointer;' id='".$details."' title='Update rost data'  onclick='select_shift(id)'>&nbsp;&nbsp;&nbsp;&nbsp;<img src='../Icons/remove.png' title='Delete rost data' onclick='deleterost(" . $result["ehsid"] . ")'></td></tr>";
            }
 
        }
        $out.="</table>";  
    }

    if( $_REQUEST["request"]=="updroster")
    { 
        $query = "select ehsid from emp_has_shift where date='".$_REQUEST["date"]."' and espid='".$_REQUEST["name"]."' and user_uid='".$_REQUEST["user"]."'";
        $res = Search($query);

        if ($result = mysqli_fetch_assoc($res)) 
        {
            $out="Record alredy added!";
        }
        else
        {
            $query="update emp_has_shift set date='".$_REQUEST["date"]."' , espid='".$_REQUEST["name"]."' where ehsid='".$_REQUEST["id"]."'";
            SUD($query);
            $out="Record updated!";
        }  
    }

    if ($_REQUEST["request"] == "deleteroster") 
    { 
        $query="update emp_has_shift set status='0' where ehsid='".$_REQUEST["rid"]."'";
            SUD($query);
            $out="Record deleted!";
    }
 
    if ($_REQUEST["request"] == "SaveShiftData") 
    {
        $query = "select ehsid from emp_has_shift where date='".$_REQUEST["DATE"]."' and user_uid='".$_REQUEST["EMP_ID"]."'";
        $res = Search($query);
        if ($result = mysqli_fetch_assoc($res)) 
        {
            $output = 0;
        }
        else
        {
            $query="insert into emp_has_shift(espid, date, user_uid, status) values('".$_REQUEST["TYPE"]."','".$_REQUEST["DATE"]."','".$_REQUEST["EMP_ID"]."','1')";
            $res = SUD($query);

            if ($res == 1) 
            {
               $output = 1;
            }
            else
            {
               $output = 2;
            }
        }
        echo $output;  
    }

    if ($_REQUEST["request"] == "AddExcelShiftData") 
    {
        $res_uid = Search("select uid from user where jobcode='".$_REQUEST["empno"]."'");
        if ($result_uid = mysqli_fetch_assoc($res_uid)) 
        {
            $User_ID = $result_uid["uid"];
        }
        else
        {
            $User_ID = 0;
        }

        if ($User_ID == 0) 
        {
            $output = 0;
        }
        else
        {
            $dateArr = explode("/", $_REQUEST["date"]);
            $Date = $dateArr[2]."-".$dateArr[0]."-".$dateArr[1];

            $res = Search("select ehsid from emp_has_shift where date='".$Date."' and user_uid='".$User_ID."'");
            if ($result = mysqli_fetch_assoc($res)) 
            {
                $output = 2;
            }
            else
            {
                $query="insert into emp_has_shift(espid, date, user_uid, status) values('".$_REQUEST["shiftno"]."','".$Date."','".$User_ID."','1')";
                $res = SUD($query);

                if ($res == 1) 
                {
                   $output = 1;
                }
                else
                {
                   $output = 0;
                }
            } 
        }
        echo $output;  
    }

    
    echo $out;
}      