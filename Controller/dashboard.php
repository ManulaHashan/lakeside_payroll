<?php
error_reporting(0);
session_start();
date_default_timezone_set('Asia/Colombo');

include '../DB/DB.php';

$DB = new Database();

if (isset($_POST["dtype"])) {
    $loadDashBoardData = $_POST['dtype'];

    switch ($loadDashBoardData) {
        case 'dashboard_data':
            loadDashBoardData();
            break;

            //case 'other' : // do something;break;
            // other cases
    }
}

/*load dashboard data*/
function loadDashBoardData()
{
    $result['Message'] = "loded";
    $result['Type'] = "DashboardData";

    $myJSON = json_encode($result);
    echo $myJSON;
}

if (isset($_REQUEST["request"])) {

    $out;
    if ($_REQUEST["request"] == "empcount") {
        $resTotEmp = Search("select count(u.uid) as totalemp from user u, emppost emp, position pos where emp.position_pid = pos.pid and u.emppost_id = emp.id and u.isactive='1' and u.uid !='2' and pos.pid like '" . $_REQUEST["dept_data"] . "'");
        if ($resultTotEmp = mysqli_fetch_assoc($resTotEmp)) {

            $TotEmp =  $resultTotEmp["totalemp"];
        }

        $resTotAdmin = Search("select count(u.uid) as totaladm from user u, emppost emp, position pos where emp.position_pid = pos.pid and u.emppost_id = emp.id and u.isactive='1' and u.gender = '1' and u.uid !='2' and pos.pid like '" . $_REQUEST["dept_data"] . "'");
        if ($resultTotAdmin = mysqli_fetch_assoc($resTotAdmin)) {

            $TotAdmin =  $resultTotAdmin["totaladm"];
        }

        $resTotMan = Search("select count(u.uid) as totalman from user u, emppost emp, position pos where emp.position_pid = pos.pid and u.emppost_id = emp.id and u.isactive='1' and u.gender = '2' and u.uid !='2' and pos.pid like '" . $_REQUEST["dept_data"] . "'");
        if ($resultTotMan = mysqli_fetch_assoc($resTotMan)) {

            $TotMan =  $resultTotMan["totalman"];
        }

        $MaleattTbl = array();
        $MaleTbl = array();


        $queryMale = "select u.uid from user u, emppost emp, position pos where emp.position_pid = pos.pid and u.emppost_id = emp.id and u.isactive='1' and u.uid!='2' and pos.pid like '" . $_REQUEST["dept_data"] . "' order by cast(u.epfno as unsigned) ASC";
        $resMale = Search($queryMale);
        while ($resultMale = mysqli_fetch_assoc($resMale)) {
            $MaleTbl[] = $resultMale["uid"];
        }

        $attM = array();

        $query1 = "select att.User_uid as AtteMale from attendance att, user u, emppost emp, position pos where u.uid = att.User_uid and emp.position_pid = pos.pid and u.emppost_id = emp.id and att.date = '" . date("Y-m-d") . "' and pos.pid like '" . $_REQUEST["dept_data"] . "' group by att.User_uid";
        $Count_Male = 0;
        $res1 = Search($query1);
        while ($result1 = mysqli_fetch_assoc($res1)) {

            $allAttM = $result1["AtteMale"];
            $MaleattTbl[] = $allAttM;
        }

        $Male1 = array_diff($MaleattTbl, $MaleTbl);
        $Male2 = array_diff($MaleTbl, $MaleattTbl);
        $final_output_male = array_merge($Male1, $Male2);
        $Count_Male = count($final_output_male);

        echo "#" . $TotEmp . "#" . $TotAdmin . "#" . $TotMan . "#" . $Count_Male . "#";
    }

    if ($_REQUEST["request"] == "mailcount") {
        $resTotMail = Search("select count(elid) as totalmail from emaillog where date = '" . date("Y/m/d") . "' and type='Birthday'");
        if ($resultTotMail = mysqli_fetch_assoc($resTotMail)) {

            $TotMail =  $resultTotMail["totalmail"];
        } else {
            $TotMail =  0;
        }

        echo $TotMail;
    }

    if ($_REQUEST["request"] == "bdaydetails") {

        echo "<table class='table table-striped'><thead><tr><th>EPF No</th><th>Employee's Name</th><th>Calling Name</th><th>Date</th><th></th></tr></thead><tbody>";
        // $resBday = Search("select fname,epfno,mname,dob,gender from  user where isactive='1' and uid !='2' and MONTH(dob)='".$_REQUEST["Month_data"]."' ORDER BY CAST(epfno AS UNSIGNED ) ASC");

        $resBday = Search("select u.fname,u.epfno,u.mname,u.dob,u.gender,u.jobcode from  user u,emppost emp,position pos where emp.position_pid = pos.pid and u.emppost_id = emp.id and isactive='1' and uid !='2' and MONTH(dob)='" . $_REQUEST["Month_data"] . "'and pos.pid like '" . $_REQUEST["dept_data"] . "' ORDER BY CAST(epfno AS UNSIGNED ) ASC");

        while ($resultBday = mysqli_fetch_assoc($resBday)) {

            $BDate = explode("-", $resultBday["dob"]);
            $NewDate = date("Y") . "-" . $BDate[1] . "-" . $BDate[2];

            if ($resultBday["gender"] == "1") {
                $CODE = "<img src='../Images/man.png' style='width:35px;'>";
            } else if ($resultBday["gender"] == "2") {
                $CODE = "<img src='../Images/women.png' style='width:35px;'>";
            }

            echo "<tr><td>" . $resultBday["jobcode"] . "</td><td>" . $resultBday["fname"] . "</td><td>" . $resultBday["mname"] . "</td><td>" . $NewDate . "</td><td align='center'>" . $CODE . "</td></tr>";
        }

        echo "</tr><tbody></table>";
    }

    if ($_REQUEST["request"] == "getmonthlyattcount") {

        $att = array();
        for ($i = 1; $i <= 12; $i++) {

            $query1 = "select count(a.aid) as AtteOfMontg from attendance a,user u, emppost emp, position pos where u.uid = a.User_uid and emp.position_pid = pos.pid and u.emppost_id = emp.id and YEAR(a.date) = '" . $_REQUEST["Year_data"] . "' and MONTH(a.date) = '" . $i . "' and pos.pid like '" . $_REQUEST["dept"] . "'";
            $res1 = Search($query1);
            if ($result1 = mysqli_fetch_assoc($res1)) {
                $allAtt = $result1["AtteOfMontg"];

                $att[] = $allAtt;
            }
        }

        foreach ($att as $value) {
            echo $value . "#";
        }
    }

    if ($_REQUEST["request"] == "dailyLateCount") {

        $attM = array();
        $attF = array();

        for ($i = 1; $i <= 31; $i++) {

            $query1 = "select count(a.aid) as AtteOfMontg from attendance a,user u, emppost emp, position pos where a.User_uid = u.uid and emp.position_pid = pos.pid and u.emppost_id = emp.id and YEAR(a.date) = '" . $_REQUEST["Year_data"] . "' and MONTH(a.date) = '" . $_REQUEST["Month_data"] . "' and DAY(a.date) = '" . $i . "' and pos.pid like '" . $_REQUEST["dept"] . "' and a.late_att_min != 0 and u.gender = '1'";
            $res1 = Search($query1);
            if ($result1 = mysqli_fetch_assoc($res1)) {
                $allAttM = $result1["AtteOfMontg"];

                $attM[] = $allAttM;
            }

            $query2 = "select count(a.aid) as AtteOfMontg from attendance a,user u, emppost emp, position pos where a.User_uid = u.uid and emp.position_pid = pos.pid and u.emppost_id = emp.id and YEAR(a.date) = '" . $_REQUEST["Year_data"] . "' and MONTH(a.date) = '" . $_REQUEST["Month_data"] . "' and DAY(a.date) = '" . $i . "' and pos.pid like '" . $_REQUEST["dept"] . "' and a.late_att_min != 0 and u.gender = '2'";
            $res2 = Search($query2);
            if ($result2 = mysqli_fetch_assoc($res2)) {
                $allAttF = $result2["AtteOfMontg"];

                $attF[] = $allAttF;
            }
        }


        foreach ($attM as $valueM) {
            echo $valueM . "#";
        }

        foreach ($attF as $valueF) {
            echo $valueF . "@";
        }
    }

    if ($_REQUEST["request"] == "dailyAbsentCount") {

        $MaleattTbl = array();
        $FemaleattTbl = array();
        $MaleTbl = array();
        $FemaleTbl = array();

        $queryMale = "select uid from user where isactive='1' and gender='1' and (uid!='2' and uid!='7' and uid!='8' and uid!='40') order by cast(epfno as unsigned) ASC";
        $resMale = Search($queryMale);
        while ($resultMale = mysqli_fetch_assoc($resMale)) {
            $MaleTbl[] = $resultMale["uid"];
        }

        $queryFemale = "select uid from user where isactive='1' and gender='2' and (uid!='2' and uid!='7' and uid!='8' and uid!='40') order by cast(epfno as unsigned) ASC";
        $resFemale = Search($queryFemale);
        while ($resultFemale = mysqli_fetch_assoc($resFemale)) {
            $FemaleTbl[] = $resultFemale["uid"];
        }

        $attM = array();
        $attF = array();

        for ($i = 1; $i <= 31; $i++) {

            $query1 = "select a.User_uid as AtteMale from attendance a,user u, emppost emp, position pos where a.User_uid = u.uid and emp.position_pid = pos.pid and u.emppost_id = emp.id and YEAR(a.date) = '" . $_REQUEST["Year_data"] . "' and MONTH(a.date) = '" . $_REQUEST["Month_data"] . "' and DAY(a.date) = '" . $i . "' and pos.pid like '" . $_REQUEST["dept"] . "' and u.gender = '1'";

            $Count_Male = 0;
            $res1 = Search($query1);
            while ($result1 = mysqli_fetch_assoc($res1)) {

                $allAttM = $result1["AtteMale"];
                $MaleattTbl[] = $allAttM;

                $Male1 = array_diff($MaleattTbl, $MaleTbl);
                $Male2 = array_diff($MaleTbl, $MaleattTbl);
                $final_output_male = array_merge($Male1, $Male2);
                $Count_Male = count($final_output_male);
            }

            unset($MaleattTbl);
            $attM[] = $Count_Male;



            $query2 = "select a.User_uid as AtteFemale from attendance a,user u, emppost emp, position pos where a.User_uid = u.uid and emp.position_pid = pos.pid and u.emppost_id = emp.id and YEAR(a.date) = '" . $_REQUEST["Year_data"] . "' and MONTH(a.date) = '" . $_REQUEST["Month_data"] . "' and DAY(a.date) = '" . $i . "' and pos.pid like '" . $_REQUEST["dept"] . "' and u.gender = '2'";

            $Count_Female = 0;
            $res2 = Search($query2);
            while ($result2 = mysqli_fetch_assoc($res2)) {

                $allAttF = $result2["AtteFemale"];
                $FemaleattTbl[] = $allAttF;

                $Female1 = array_diff($FemaleattTbl, $FemaleTbl);
                $Female2 = array_diff($FemaleTbl, $FemaleattTbl);
                $final_output_female = array_merge($Female1, $Female2);
                $Count_Female = count($final_output_female);
            }

            unset($FemaleattTbl);
            $attF[] = $Count_Female;
        }


        foreach ($attM as $valueM) {
            echo $valueM . "#";
        }

        foreach ($attF as $valueF) {
            echo $valueF . "@";
        }
    }
}