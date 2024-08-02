<?php
error_reporting(0);
session_start();
date_default_timezone_set('Asia/Colombo');
include '../DB/DB.php';
include '../Model/Finance/bankaccounts.php';
$DB = new Database();

if (isset($_REQUEST["request"])) {
    $out = "";
    if ($_REQUEST["request"] == "getDetailsfromUID") {
        $bsal;
        $posi;
        $grade = "";
        $csal;
        $saltype;
        $jobcode = "";
        $query = "select uid,emppost_id,presentSalary,jobcode,epfno,school,emp_act,EmployeeType_etid,epf_entitle_date from user where uid = '" . $_REQUEST["uid"] . "'";
        $res = Search($query);
        if ($result = mysqli_fetch_assoc($res)) {

            $desig = $result["school"];

            $queryx = "select position_pid,grade_gid,basicsal from emppost where id = '" . $result["emppost_id"] . "'";
            $resx = Search($queryx);
            if ($resultx = mysqli_fetch_assoc($resx)) {
                if($result["presentSalary"] != '0'){
                    $bsal = $result["presentSalary"];                  
                }else{
                    $bsal = $resultx["basicsal"];                    
                }
                
                // $jobcode = $result["jobcode"]; 2023-01-18 changed
                $jobcode = $result["jobcode"];
                $epf = $result["epfno"];
                $empID = $result["uid"];
                $empAct = $result["emp_act"];
                $empTYPE = $result["EmployeeType_etid"];

                if ($result["epf_entitle_date"] == "" || $result["epf_entitle_date"] == "0000-00-00") 
                {
                    $epf_entitle_date = 0;
                }
                else
                {
                    $epf_entitle_date = $result["epf_entitle_date"];
                }

                

                $queryxy = "select name from position where pid = '" . $resultx["position_pid"] . "'";
                $resxy = Search($queryxy);
                if ($resultxy = mysqli_fetch_assoc($resxy)) {
                    $posi = $resultxy["name"];
                }

                $queryxyz = "select name from grade where gid = '" . $resultx["grade_gid"] . "'";
                $resxyz = Search($queryxyz);
                if ($resultxyz = mysqli_fetch_assoc($resxyz)) {
                    $grade = $resultxyz["name"];
                }
            }
        }
        
        $out = $bsal . "#" . $posi . "#" . $grade . "#" . $empID . "#" . $jobcode . "#" . $epf. "#" . $desig . "#" . $empAct . "#" . $empTYPE . "#" . $epf_entitle_date;
    }
    if ($_REQUEST["request"] == "getUIDFromEPFno") {
        
        $queryemp = "select uid from user where jobcode = '" . $_REQUEST["epfno"] . "'";
        $resemp = Search($queryemp);
        if ($resultemp = mysqli_fetch_assoc($resemp)) {
            echo $resultemp["uid"];
        }
        else
        {
            echo "0";
        }
       
    }

    if ($_REQUEST["request"] == "LoadSlipData") {
        
        $res = Search("select * from salarycomplete where id = '" . $_REQUEST["Slip_ID"] . "'");
        $result = mysqli_fetch_assoc($res);

        echo implode( "#", $result);
        
    }


    if ($_REQUEST["request"] == "getIncreasementsfromUID") {
        $out = "<table width='70%'>";
        $query = "select siid,date,reason,amount from salaryincreasement where user_uid = '" . $_REQUEST["uid"] . "' and isactive='1' order by date";
        $res = Search($query);
        while ($result = mysqli_fetch_assoc($res)) {
            $out .= "<tr><td width='100px'>" . $result["date"] . "</td><td>" . $result["reason"] . "</td><td width='100px' align='right'>Rs. " . $result["amount"] . ".00</td><td width='16px'> <img src='../Icons/remove.png' style='cursor: pointer' onclick='selectIncreasement(" . $result["siid"] . ")'></td></tr>";
        }
        $out .= "</table>";
    }
    if ($_REQUEST["request"] == "removeIncreasement") {
        $query = "update salaryincreasement set isactive = '0' where siid = '" . $_REQUEST["siid"] . "'";
        $res = SUD($query);
        echo $res;
    }
    if ($_REQUEST["request"] == "addIncreasement") {
        $query = "insert into salaryincreasement(user_uid,date,reason,amount,isactive) values('" . $_REQUEST["uid"] . "','" . $_REQUEST["date"] . "','" . $_REQUEST["res"] . "','" . $_REQUEST["amount"] . "','1')";
        $res = SUD($query);
        echo $res;
    }
    if ($_REQUEST["request"] == "getPayroll") {

        $halfDaysCount = "0";
        $shortLeaves = "0";

        $halfDays = 0;

        $att = 0;
        $TotLeaves = 0;
        $othrs = 0;
        $otmints = 0;
        $hhrs = 0;
        $hmints = 0; 

        $otc = 0;
        $otcN = 0;
        
        $BR1 = 0;
        $BR2 = 0;
        $TOTHRS = 0;
        $lam = 0;

        $TOT_OTH = 0;
        $TOT_OTM = 0;

        $TOT_DOTH = 0;
        $TOT_DOTM = 0;

        $TOT_WORKH = 0;
        $TOT_WORKM = 0;

        $querBR1 = "select uha.amount,a.name from allowances a,user_has_allowances uha where a.alwid = uha.alwid and a.name = 'Budgetary Relief Allowance 1' and uha.uid = '".$_REQUEST["uid"]."'";

        $resBR1 = Search($querBR1);
        if ($resultBR1 = mysqli_fetch_assoc($resBR1)) 
        {
            $BR1 = $resultBR1["amount"];
        }
        else
        {
            $BR1 = 0;
        }

        $querBR2 = "select uha.amount,a.name from allowances a,user_has_allowances uha where a.alwid = uha.alwid and a.name = 'New Budgetary Relief Allowance' and uha.uid = '".$_REQUEST["uid"]."'";

        $resBR2 = Search($querBR2);
        if ($resultBR2 = mysqli_fetch_assoc($resBR2)) 
        {
            $BR2 = $resultBR2["amount"];
        }
        else
        {
            $BR2 = 0;
        }

        $query = "select *,count(a.aid) as att, sum(a.othours) as ot, sum(a.hours) as hours, sum(dothours) as dothours, sum(late_att_min) as lam, b.fname,b.lname,b.uid,b.gender,b.emp_act from attendance a,user b where a.User_uid = b.uid and a.date between '" . $_REQUEST["date1"] . "' and '" . $_REQUEST["date2"] . "' and a.User_uid like '" . $_REQUEST["uid"] . "' group by aid";

        $res = Search($query);
        while ($result = mysqli_fetch_assoc($res)) {
            $att = $att + $result["att"];
            $EMPLOYEE_ACT = $result["emp_act"];
            //calculate times 
            //OT
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


            if($result["gender"] == "1")
            {
                //get ot or dot meal count  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~       
                $outtime  = date("H:i:s", strtotime($result["outtime"]));
                $TH10PM = date("H:i:s", strtotime("10:00 PM"));
                $TH830AM = date("H:i:s", strtotime("08:30 AM"));

                $OUTTIME = date("A", strtotime($outtime));

                if ($OUTTIME == "AM") 
                {
                    if ($TH830AM > $outtime) 
                    {
                       $otc += 1;
                    }
                    
                }
                else
                {
                    if($outtime > $TH10PM){
                        $otc += 1;
                    }
                }
            }
            else if ($result["gender"] == "2") 
            {
                //get ot or dot meal count  ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~       
                $outtime  = date("H:i:s", strtotime($result["outtime"]));
                $TH8PM = date("H:i:s", strtotime("08:00 PM"));
                $TH830AM = date("H:i:s", strtotime("08:30 AM"));

                $OUTTIME = date("A", strtotime($outtime));

                if ($OUTTIME == "AM") 
                {
                    if ($TH830AM > $outtime) 
                    {
                       $otc += 1;
                    }
                    
                }
                else
                {
                    if($outtime > $TH8PM){
                        $otc += 1;
                    }
                }
            }

            $otcAm = $otc * 250; //New Update 2023-11-17

            //~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

            //late att minutes 
            $lam += $result["lam"];

            $bsal = $result["presentSalary"];
            
        }

        $hours = ((($hhrs * 60) + ($hmints * 30))) / 60;

        //cal total hours
        $hours = MinutesTOHours(($hhrs * 60) + ($hmints * 10));

        //cal total OT
        $ot = MinutesTOHours(($othrs * 60) + ($otmints * 10));

        // cal total DOT
        $dot = MinutesTOHours(($dothrs * 60) + ($dotmints * 10)); 

        $query = "select sum(days) as totleaves from employee_leave where uid = '" . $_REQUEST["uid"] . "' and type != 'Nopay Leave' and type != 'Liue Leave' and type != 'Parental Leave' and type != 'Maternity Leave' and type != 'Duty Leave' and type != 'Short Leave' and date between '" . $_REQUEST["date1"] . "' and '" . $_REQUEST["date2"] . "'";
        $res = Search($query);
        if ($result = mysqli_fetch_assoc($res)) {

            $TotLeaves = $result["totleaves"];
        }
        else
        {
            $TotLeaves = 0;
        }



        $query = "select count(aid) as att from attendance where date between '" . $_REQUEST["date1"] . "' and '" . $_REQUEST["date2"] . "' and User_uid = '" . $_REQUEST["uid"] . "' and shortleave='1'";
        $res = Search($query);
        while ($result = mysqli_fetch_assoc($res)) {
            $shortLeaves = $result["att"];
        }

        $querysNopay = "select sum(days) as nopaydays from employee_leave where date between '" . $_REQUEST["date1"] . "' and '" . $_REQUEST["date2"] . "' and uid = '" . $_REQUEST["uid"] . "' and type='Nopay Leave'";
        $ressNopay = Search($querysNopay);
        if ($resultssNopay = mysqli_fetch_assoc($ressNopay)) {
            $NopayDays = $resultssNopay["nopaydays"];
        }
        
        
        $resattendence = Search("select a.amount as FA from user_has_allowances a, allowances b where a.alwid = b.alwid and a.uid = '".$_REQUEST["uid"]."' and b.alwid ='9'");
        if ($resultattallow = mysqli_fetch_assoc($resattendence)) 
        {
            $AttendenceAllow = $resultattallow["FA"]; 
        }
        else
        {
            $AttendenceAllow = 0.00;
        }

        $restravel = Search("select a.amount as VA from user_has_allowances a, allowances b where a.alwid = b.alwid and a.uid = '".$_REQUEST["uid"]."' and b.alwid ='10'");
        if ($resulttravel = mysqli_fetch_assoc($restravel)) 
        {
            $TravelAllow = $resulttravel["VA"]; 
        }
        else
        {
            $TravelAllow = 0.00;
        }

        $restOtherAllow = Search("select sum(a.amount) as OTHERALLOW from user_has_allowances a, allowances b where a.alwid = b.alwid and a.uid = '".$_REQUEST["uid"]."' and b.name !='Attendance Allowance' and b.name !='Travelling Allowance' and b.name !='Budgetary Relief Allowance 1' and b.name !='New Budgetary Relief Allowance' and b.name !='Fixed Allowance' and b.name != 'Vehicle Allowance'");
        if ($resultOtherAllow = mysqli_fetch_assoc($restOtherAllow)) 
        {
            $OtherAllow = $resultOtherAllow["OTHERALLOW"]; 
        }
        else
        {
            $OtherAllow = 0.00;
        }

        $restAvailable = Search("select id from salarycomplete where month='".date("m",strtotime($_REQUEST["date1"]))."' and year='".date("Y",strtotime($_REQUEST["date2"]))."' and uid = '".$_REQUEST["uid"]."'");
        if ($resultAvailable = mysqli_fetch_assoc($restAvailable)) 
        {
            $SalAvailable = "1"; 
        }
        else
        {
            $SalAvailable = "0";
        }

        $queryOT = "select sum(othours) as TotOT,sum(hours) as HRS,sum(hours) as HRS,sum(dothours) as DOTHRS  from attendance where User_uid like '" . $_REQUEST["uid"] . "' and date between '" . $_REQUEST["date1"] . "' and '" . $_REQUEST["date2"] . "'";

            $resOT = Search($queryOT);
            if ($resultOT = mysqli_fetch_assoc($resOT)) 
            {
                $othr = number_format($resultOT["TotOT"],2);
                $tothrs = number_format($resultOT["HRS"],2);                
                $doth = number_format($resultOT["DOTHRS"],2);    
            }

        $CompanyTotLeaves = 0;    

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

        
        $thisMonth = cal_days_in_month(CAL_GREGORIAN, date("m"), date("Y"));

        //add badget allowance and grading allowance to basic salary to genarate OT Allowance
        
        if ($EMPLOYEE_ACT == "Shop and Office") 
        {
            $lamded = (($bsal+$BR1+$BR2)/30/8/60);
        }
        else if ($EMPLOYEE_ACT == "Wages Board") 
        {
            $lamded = (($bsal+$BR1+$BR2)/26/8/60);
        }
        else if ($EMPLOYEE_ACT == "Driver Wages Board") 
        {
            $lamded = (($bsal+$BR1+$BR2)/25/8/60);
        }
        else
        {
            $lamded = (($bsal+$BR1+$BR2)/26/8/60);
        }

        $out = $DataWORK . "#" . $DataOT . "#" . $att . "#" . $TotLeaves . "#" . $shortLeaves . "#" . $DataDOT. "#" . $lam. "#" . $lamded. "#" . $bsal."#".$otcAm."#".$NopayDays."#".$BR1."#".$BR2."#".$thisMonth."#".$AttendenceAllow."#".$TravelAllow."#".$OtherAllow."#".$SalAvailable."#".$CompanyTotLeaves;
    }

    if ($_REQUEST["request"] == "getSalDeductions") {
        $out = "<table width='400px'>";
        $tot = 0;
        // $query = "select * from salerydeductions where User_uid='" . $_REQUEST["uid"] . "' and date between '" . $_REQUEST["date1"] . "' and '" . $_REQUEST["date2"] . "' and isactive = '1'";

        // and isactive = '1'

        $query = "select * from salerydeductions where year='".date("Y",strtotime($_REQUEST["date1"]))."' and month='".date("m",strtotime($_REQUEST["date1"]))."' and User_uid='" . $_REQUEST["uid"] . "'";
        $res = Search($query);
        while ($result = mysqli_fetch_assoc($res)) {
            $out .= "<tr><td width='100px'>" . $result["date"] . "</td><td width='200px'>" . $result["description"] . "</td><td>Rs. " . $result["total"] . "</td></tr>";
            $tot += $result["total"];
        }
        $out .= "</table>##" . $tot;
    }

    if ($_REQUEST["request"] == "getSlipSalDeductionsAfterPaid") {  //new change 2023-11-06
        $out = "<table width='400px'>";
        $tot = 0;
        // $query = "select * from salerydeductions where User_uid='" . $_REQUEST["uid"] . "' and date between '" . $_REQUEST["date1"] . "' and '" . $_REQUEST["date2"] . "' and isactive = '1'";

        // and isactive = '0'

        $query = "select * from salerydeductions where year='".date("Y",strtotime($_REQUEST["date1"]))."' and month='".date("m",strtotime($_REQUEST["date1"]))."' and User_uid='" . $_REQUEST["uid"] . "'";
        $res = Search($query);
        while ($result = mysqli_fetch_assoc($res)) {
            $out .= "<tr><td width='100px'>" . $result["date"] . "</td><td width='200px'>" . $result["description"] . "</td><td>Rs. " . $result["total"] . "</td></tr>";
            $tot += $result["total"];
        }
        $out .= "</table>##" . $tot;
    }

    if ($_REQUEST["request"] == "getIncreasements") {
        $out = "<table width='400px'>";
        $tot = 0;
        $query = "select siid,date,reason,amount from salaryincreasement where user_uid = '" . $_REQUEST["uid"] . "' and isactive='1' order by date";
        $res = Search($query);
        while ($result = mysqli_fetch_assoc($res)) {
            $out .= "<tr><td width='100px'>" . $result["date"] . "</td><td width='200px'>" . $result["reason"] . "</td><td>Rs. " . $result["amount"] . ".00</td></tr>";
            $tot += $result["amount"];
        }
        $out .= "</table>##" . $tot;
    }

    if ($_REQUEST["request"] == "getLoans") {
        $out = "<table width='400px'>";
        $tot = 0;

        // and status = '1'

        $query = "select * from factoryloan where year='".date("Y",strtotime($_REQUEST["date1"]))."' and month='".date("m",strtotime($_REQUEST["date1"]))."' and User_uid='" . $_REQUEST["uid"] . "'";
        $res = Search($query);
        while ($result = mysqli_fetch_assoc($res)) {
            $out .= "<tr><td width='100px'>" . $result["date"] . "</td><td width='200px'>Loan Rs. " . $result["amount"] . "</td><td>Rs. " . $result["installment"] . "</td></tr>";
            $tot += $result["installment"];
        }
        $out .= "</table>##" . $tot;
    }

    if ($_REQUEST["request"] == "getSlipSalLoansAfterPaid") {
        $out = "<table width='400px'>";
        $tot = 0;

        // and status = '0'

        $query = "select * from factoryloan where year='".date("Y",strtotime($_REQUEST["date1"]))."' and month='".date("m",strtotime($_REQUEST["date1"]))."' and User_uid='" . $_REQUEST["uid"] . "'";
        $res = Search($query);
        while ($result = mysqli_fetch_assoc($res)) {
            $out .= "<tr><td width='100px'>" . $result["date"] . "</td><td width='200px'>Loan Rs. " . $result["amount"] . "</td><td>Rs. " . $result["installment"] . "</td></tr>";
            $tot += $result["installment"];
        }
        $out .= "</table>##" . $tot;
    }

    if($_REQUEST["request"] == "getAlowances"){
        //search allowances
        $outals= "";
        $resal = Search("select a.amount, b.name from user_has_allowances a, allowances b where a.alwid = b.alwid and a.uid = '".$_REQUEST["uid"]."' and b.alwid !='1' and b.alwid !='2' and b.alwid !='9' and b.alwid !='10' order by b.alwid");
        while ($resultal = mysqli_fetch_assoc($resal)) {
            $outals .= $resultal["name"].":".$resultal["amount"]."//"; 
        }

        $out = substr($outals, 0, -2); 
    }
    if ($_REQUEST["request"] == "advancePayment") {
        $query = "insert into salarypayment(User_uid,date,month,year,tot,complete) "
        . "values('" . $_REQUEST["uid"] . "','" . $_REQUEST["date"] . "','" . date("m",strtotime($_REQUEST["date"])) . "','" . date("Y",strtotime($_REQUEST["date"])) . "','" . $_REQUEST["amount"] . "','0')";
        $res = SUD($query);

        if ($res == '1') {
            //insert into regularpayment for effects to the finance.
            $ptype = "Salary Advance Payment";
            if (isset($_REQUEST["paymenttype"])) {
                $ptype = "Salary Payment";
            }
            $query = "insert into regularpayments(description,rpaymenttype_rptid,tot,date,time,User_uid,paymentmethod_pmid) "
            . "values('" . $ptype . " UID : " . $_REQUEST["uid"] . "','1','" . $_REQUEST["amount"] . "','" . $_REQUEST["date"] . "','" . date('h:i:s') . "','" . $_SESSION["uid"] . "','1')";
            $res = SUD($query);

            $recID = $DB->getConnection()->insert_id;

            $Query = "insert into payments_has_account(payments_rpid,account_baid,amount) "
            . "values('" . $recID . "','" . $_REQUEST["account"] . "','" . $_REQUEST["amount"] . "')";
            $return = SUD($Query);

            // if ($return == "1") {
            //     deductBalanceFromFinanceAccount($_REQUEST["account"], $_REQUEST["date"], $_REQUEST["amount"], 'Salary Payment');
            // }

            echo 'Advance paid!';
        } else {
            echo 'Error Oparation';
        }
    }
    if ($_REQUEST["request"] == "getAdvances") {
        $out = "<table width='70%'>";
        $tot = 0;
        $query = "select * from salarypayment where User_uid = '" . $_REQUEST["uid"] . "' and month = '" . date("m",strtotime($_REQUEST["date"])) . "' and year = '" . date("Y",strtotime($_REQUEST["date"])) . "' order by date DESC";
        $res = Search($query);
        while ($result = mysqli_fetch_assoc($res)) {
            $tot += $result["tot"];
            $out .= "<tr><td width='100px'>" . $result["date"] . "</td><td>Rs. " . $result["tot"] . "</td><td width='16px'> <img src='../Icons/remove.png' style='cursor: pointer' onclick='removeAdvance(" . $result["spid"] . ")'></td></tr>";
        }
        $out .= "</table>##" . $tot;
    }
    if ($_REQUEST["request"] == "getAdvancesforsalsheet") {
        $out = "<table width='400px'>";
        $query = "select * from salarypayment where User_uid = '" . $_REQUEST["uid"] . "' and month = '" . date("m",strtotime($_REQUEST["date"])) . "' and year = '" . date("Y",strtotime($_REQUEST["date"])) . "' order by date DESC";
        $res = Search($query);
        while ($result = mysqli_fetch_assoc($res)) {
            $out .= "<tr><td width='100px'>" . $result["date"] . "</td><td>Rs. " . $result["tot"] . "</td></tr>";
        }
        $out .= "</table>";
    }


    if ($_REQUEST["request"] == "removeAdvance") {
        $query = "delete from salarypayment where spid = '" . $_REQUEST["spid"] . "'";
        $res = SUD($query);
        if ($res == '1') { 
            echo 'Payment Removed!';
        } else {
            echo 'Error Oparation';
        }
    }
    if ($_REQUEST["request"] == "getuidfromjobcode") {
        $query = "select uid from user where epfno = '" . $_REQUEST["jcode"] . "'";
        $res = Search($query);
        if ($result = mysqli_fetch_assoc($res)) {
            $out = $result["uid"];
        }
    } 
    if ($_REQUEST["request"] == "salaryPayment") {

        $querys = "select id from salarycomplete where month = '".$_REQUEST["monthx"]."' and year = '".$_REQUEST["yearx"]."' and uid = '".$_REQUEST["uid"]."'";
        $rest = Search($querys);
        if ($resultt = mysqli_fetch_assoc($rest)) 
        {
            echo 'Already Paid!';
        }
        else
        {
            $query = "insert into salarycomplete(basic, ball, nopaypr, epfpr, poya, alw, gross, nopay, epf, advance, stamp, loan, otherded, net, uid, date, epf12, etf3, month, year, wdays,totwdays, arrears,daypay,ot,otday,att1,bonus,overpay,balance,paid,additional,deductions,datefrom,dateto, att_incen, meal, grading, travl, late, half, short, leave_ded,br1, br2, otherallow, nopaydays, payee_tax) "
            . "values('" . $_REQUEST["basic"] . "','0','0','" . $_REQUEST["epf8"] . "','0','0','" . $_REQUEST["gross"] . "','" . $_REQUEST["npay"] . "','" . $_REQUEST["epf8"] . "','" . $_REQUEST["totadv"] . "','" . $_REQUEST["stamp"] . "','" . $_REQUEST["loan"] . "','" . $_REQUEST["tototherded"] . "','" . $_REQUEST["net"] . "','" . $_REQUEST["uid"] . "','" . $_REQUEST["date"] . "','" . $_REQUEST["epf12"] . "','" . $_REQUEST["etf3"] . "','" . $_REQUEST["monthx"] . "','" . $_REQUEST["yearx"] . "','" . $_REQUEST["wdays"] . "','" . $_REQUEST["totwdays"] . "','0','0','" . $_REQUEST["ot"] . "','" . $_REQUEST["otday"] . "','" . $_REQUEST["att1"] . "','0','0','" . $_REQUEST["net"] . "','" . $_REQUEST["paidsal"] . "','0','0','" . $_REQUEST["dto"] . "','" . $_REQUEST["dfrm"] . "','" . $_REQUEST["attin"] . "','0','0','" . $_REQUEST["travl"] . "','" . $_REQUEST["late"] . "','0','" . $_REQUEST["short"] . "','0','" . $_REQUEST["br1"] . "','" . $_REQUEST["br2"] . "','" . $_REQUEST["OtherALLOW"] . "','" . $_REQUEST["NopayDAys"] . "','".$_REQUEST["payee_tax"]."')";  
            $res = SUD($query); 

            if ($res == 1) 
            {
                $res_ded = Search("select sdid from salerydeductions where year='".$_REQUEST["yearx"]."' and month='".$_REQUEST["monthx"]."' and user_uid='" . $_REQUEST["uid"] . "' and isactive = '1'");
                while ($results = mysqli_fetch_assoc($res_ded)) 
                {
                    $res_ded_upd = SUD("update salerydeductions set isactive = '0' where sdid = '" . $results["sdid"] . "'");
                }

                $res_loan = Search("select flid from factoryloan where year='".$_REQUEST["yearx"]."' and month='".$_REQUEST["monthx"]."' and User_uid='" . $_REQUEST["uid"] . "' and status = '1'");
                while ($results_l = mysqli_fetch_assoc($res_loan)) 
                {
                    $res_loan_upd = SUD("update factoryloan set status = '0',complete_date = '".date("Y-m-d")."' where flid = '" . $results_l["flid"] . "'");
                }

                echo 'Details Saved!';
            }
            else
            {
                echo 'Error!';
            }
        } 
    }
    
    if ($_REQUEST["request"] == "EditsalaryPayment") {

        $query = "update salarycomplete set basic = '" . $_REQUEST["basic"] . "', epfpr = '" . $_REQUEST["epf8"] . "', gross = '" . $_REQUEST["gross"] . "', nopay = '" . $_REQUEST["npay"] . "', epf = '" . $_REQUEST["epf8"] . "', advance = '" . $_REQUEST["totadv"] . "', stamp = '" . $_REQUEST["stamp"] . "', loan = '" . $_REQUEST["loan"] . "', otherded = '" . $_REQUEST["tototherded"] . "', net = '" . $_REQUEST["net"] . "', epf12 = '" . $_REQUEST["epf12"] . "', etf3 = '" . $_REQUEST["etf3"] . "', wdays = '" . $_REQUEST["wdays"] . "', totwdays = '" . $_REQUEST["totwdays"] . "', ot = '" . $_REQUEST["ot"] . "', otday = '" . $_REQUEST["otday"] . "', att1 = '" . $_REQUEST["att1"] . "', balance = '" . $_REQUEST["net"] . "', paid = '" . $_REQUEST["paidsal"] . "', datefrom = '" . $_REQUEST["dto"] . "', dateto = '" . $_REQUEST["dfrm"] . "', att_incen = '" . $_REQUEST["attin"] . "', travl = '" . $_REQUEST["travl"] . "', late = '" . $_REQUEST["late"] . "', short = '" . $_REQUEST["short"] . "', br1 = '" . $_REQUEST["br1"] . "', br2 = '" . $_REQUEST["br2"] . "', otherallow = '" . $_REQUEST["OtherALLOW"] . "', nopaydays = '" . $_REQUEST["NopayDAys"] . "', payee_tax = '".$_REQUEST["payee_tax"]."' where uid = '" . $_REQUEST["uid"] . "' and month = '" . $_REQUEST["monthx"] . "' and year = '" . $_REQUEST["yearx"] . "'";

         $res = SUD($query);

         if ($res == "1") 
         {
            $res_ded = Search("select sdid from salerydeductions where year='".$_REQUEST["yearx"]."' and month='".$_REQUEST["monthx"]."' and user_uid='" . $_REQUEST["uid"] . "' and isactive = '1'");
            while ($results = mysqli_fetch_assoc($res_ded)) 
            {
                $res_ded_upd = SUD("update salerydeductions set isactive = '0' where sdid = '" . $results["sdid"] . "'");
            }

            $res_loan = Search("select flid from factoryloan where year='".$_REQUEST["yearx"]."' and month='".$_REQUEST["monthx"]."' and User_uid='" . $_REQUEST["uid"] . "' and status = '1'");
            while ($results_l = mysqli_fetch_assoc($res_loan)) 
            {
                $res_loan_upd = SUD("update factoryloan set status = '0',complete_date = '".date("Y-m-d")."' where flid = '" . $results_l["flid"] . "'");
            }
            
            echo 'Details Updated!';
         }
         else
         {
            echo 'Error!';
         }    
        
    }
    if ($_REQUEST["request"] == "mealremaining") {

        $querys = "select mrid from mealremaining where Month(date) = '".date("m",strtotime($_REQUEST["allowancedate"]))."' and Year(date) = '".date("Y",strtotime($_REQUEST["allowancedate"]))."' and uid = '".$_REQUEST["uid"]."'";
        $rest = Search($querys);
        if ($resultt = mysqli_fetch_assoc($rest)) 
        {
            echo '0';
        }
        else
        {
            $query = "insert into mealremaining(date, totalamount, days, remainingamount, uid) "
            . "values('" . $_REQUEST["allowancedate"] . "','" . $_REQUEST["totallowamount"] . "','" . $_REQUEST["mdays"] . "','" . $_REQUEST["mremaining"] . "','" . $_REQUEST["uid"] . "')";  
            $res = SUD($query); 

            echo '1';
        }
        
    }
    if ($_REQUEST["request"] == "getleavecount") {

       // $YearDiff = 0;
       $queryYears = "select registerdDate from user where uid = '" . $_REQUEST["UID"] . "'";
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

       if ($years >= "0" && $years < "2") 
       {

          if ($years == "0" && $months == "0") 
          {
               $Total_leave = 0;
               $CheckMonth = "Empty";
               
               echo 'A'."#".$GetHalf."#".$Half_Available."#".$GetShort."#".$Short_Available."#".$Total_leave."#".$NOPAY."#".$LEAVE_LEAVE."#".$CheckUser."#".$CheckMonth;

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

                $queryHalf = "select sum(days) as halfleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y')."' AND MONTH(date) = '".date('m')."' AND (type like 'Halfday Leave' or type like 'Leave')";
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
                        $resPreviouseHalf = Search("select total_leave as previousehalf from total_leave_data where uid = '" . $_REQUEST["UID"] . "'");
                
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
                

                $queryShort = "select sum(days) as shortleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y')."' AND MONTH(date) = '".date('m')."' AND type like 'Short Leave'";
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

                $queryNopay = "select sum(days) as Nopayleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y')."' AND MONTH(date) = '".date('m')."' AND type like 'Nopay Leave'";
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


                $queryUserCheck = "select jobcode from user where uid = '" . $_REQUEST["UID"] . "' and (EmployeeType_etid like '5' or EmployeeType_etid like '2')";
                $resUserCheck = Search($queryUserCheck);
                    
                if ($resultUserCheck = mysqli_fetch_assoc($resUserCheck)) 
                {
                    $CheckUser = "1";
                    $queryLeaveLeave = "select sum(days) as LeaveLeavetot from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y')."' AND MONTH(date) = '".date('m')."' AND type like 'Liue Leave'";
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

               echo 'A'."#".$GetHalf."#".$Half_Available."#".$GetShort."#".$Short_Available."#".$Total_leave."#".$NOPAY."#".$LEAVE_LEAVE."#".$CheckUser."#".$CheckMonth;


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

                 $one_month_short = 0.5; // 2 short leaves
                 $GetShort = 0;
                 $Short_Available = 0;

                 $query = "select sum(days) as totalLeaves from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y')."' and type != 'Short Leave' and type != 'Company Leave' and type != 'Liue Leave' and type != 'Nopay Leave' ";
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

                 $queryShort = "select sum(days) as shortleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y')."' AND MONTH(date) = '".date('m')."' AND type like 'Short Leave'";
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

                 $queryNopay = "select sum(days) as Nopayleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y')."' AND type like 'Nopay Leave'";
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

                $queryUserCheck = "select jobcode from user where uid = '" . $_REQUEST["UID"] . "' and (EmployeeType_etid like '5' or EmployeeType_etid like '2')";
                $resUserCheck = Search($queryUserCheck);
                    
                if ($resultUserCheck = mysqli_fetch_assoc($resUserCheck)) 
                {
                    $CheckUser = "1";
                    $queryLeaveLeave = "select sum(days) as LeaveLeavetot from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y')."' AND type like 'Liue Leave'";
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
       else
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

               $query = "select sum(days) as totalLeaves from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y')."' and type != 'Short Leave' and type != 'Company Leave' and type != 'Liue Leave' and type != 'Nopay Leave' ";
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


               $queryShort = "select sum(days) as shortleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y')."' AND MONTH(date) = '".date('m')."' AND type like 'Short Leave'";
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

               $queryNopay = "select sum(days) as Nopayleave from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y')."' AND type like 'Nopay Leave'";
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

              $queryUserCheck = "select jobcode from user where uid = '" . $_REQUEST["UID"] . "' and (EmployeeType_etid like '5' or EmployeeType_etid like '2')";
              $resUserCheck = Search($queryUserCheck);
                  
              if ($resultUserCheck = mysqli_fetch_assoc($resUserCheck)) 
              {
                  $CheckUser = "1";
                  $queryLeaveLeave = "select sum(days) as LeaveLeavetot from employee_leave where uid = '" . $_REQUEST["UID"] . "' AND YEAR(date) = '".date('Y')."' AND type like 'Liue Leave'";
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
 
    echo $out;
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
?>