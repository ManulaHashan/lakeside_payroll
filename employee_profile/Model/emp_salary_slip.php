<?php
session_start();
date_default_timezone_set('Asia/Colombo');
setlocale(LC_MONETARY, 'en_US');

include '../DB/DB.php';
$DB = new Database();
?>

<html>

<head>
    <title>Salary Slip | Employee Profile</title>
    <link rel="icon" href="../favicon_derana.ico" type="image/x-icon" />
    <link href="../Styles/Stylie.css" rel="stylesheet" type="text/css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <style>
    .Report_Headerx {
        border-radius: 10px;
        width: 250px;
        display: block;
        background-color: #cc0033;
        color: white;
        padding-left: 20px;
        padding-right: 20px;
        padding-top: 5px;
        padding-bottom: 5px;
        font-size: 18px;
        font-family: "Times New Roman", Times, serif;
        font-weight: bolder;
    }

    .borderbtm {
        border-bottom-color: #000;
        border-bottom-style: solid;
        border-bottom-width: 1px;
    }

    .borderbtmAsh {
        border-bottom-color: #999999;
        border-bottom-style: solid;
        border-bottom-width: 1px;
    }
    </style>

</head>

<body>

    <script type="text/javascript">
    window.onload = function() {

        // window.print();

    };
    </script>


    <center>



        <?php
        if (isset($_GET["slipID"])) {
            $res = Search("select * from salarycomplete where id = '" . $_GET["slipID"] . "'");
            if ($result = mysqli_fetch_assoc($res)) {
                
                // $resx = Search("select fname,school,epfno,emp_act,jobcode from user where uid = '" . $result["uid"] . "'");
                // if ($resultx = mysqli_fetch_assoc($resx)) {
                //     $userName = $resultx["fname"];
                //     $userDesignation = $resultx["school"];
                //     $userEpfNo = $resultx["epfno"];
                //     $userAct = $resultx["emp_act"];
                //     $userNo = $resultx["jobcode"];
                // }

                $resx = Search("select u.fname,u.school,u.epfno,u.emp_act,u.jobcode,u.nic,u.bank,u.bankno,p.name as branch,d.name depname from user u LEFT JOIN emppost e ON u.emppost_id = e.id LEFT JOIN position p ON e.position_pid = p.pid LEFT JOIN emp_department d ON d.did = u.dept_id where uid = '" . $result["uid"] . "'");
                if ($resultx = mysqli_fetch_assoc($resx)) {
                    $userName = $resultx["fname"];
                    $userDesignation = $resultx["school"];
                    $userEpfNo = $resultx["epfno"];
                    $userAct = $resultx["emp_act"];
                    $userNo = $resultx["jobcode"];
                    $userBranch = $resultx["branch"];
                    $userDepartment = $resultx["depname"];
                    $userNIC = $resultx["nic"];
                    $userBankAndAccNo = $resultx["bank"]." / ".$resultx["bankno"];
                }

                $USER = $result["uid"];
                $month = $result["month"];
                $year = $result["year"];
                $EPF12 = $result["epf12"];
                $EPF8 = $result["epf"];
                $ETF3 = $result["etf3"];
                $Basic_sal = $result["basic"];
                $Basic_sal_for_epf = $result["basic"]-$result["nopay"];
                $working_days = $result["wdays"];
                $BR1 = $result["br1"];
                $BR2 = $result["br2"];
                $Np_Ded = $result["nopay"];

                if ($result["payee_tax"] == "") 
                {
                    $Payee_Tax = 0;
                }
                else
                {
                    $Payee_Tax = $result["payee_tax"];
                }
                


                $TOT_OTH = 0;
                $TOT_OTM = 0;

                $TOT_DOTH = 0;
                $TOT_DOTM = 0;

                $TOT_WORKH = 0;
                $TOT_WORKM = 0;
                $lam = 0;

                $query = "select *,count(a.aid) as att, sum(a.othours) as ot, sum(a.hours) as hours, sum(dothours) as dothours, sum(late_att_min) as lam, b.fname,b.lname,b.uid,b.gender,b.emp_act from attendance a,user b where a.User_uid = b.uid and a.date between '" . $result["datefrom"] . "' and '" . $result["dateto"] . "' and a.User_uid like '" . $result["uid"] . "' group by aid";

                $res = Search($query);
                while ($resultt = mysqli_fetch_assoc($res)) {
                    $att = $att + $resultt["att"];
                    $EMPLOYEE_ACT = $resultt["emp_act"];
                    //calculate times 
                    //OT
                    if($resultt["othours"] == ""){
                            $oth = $resultt["othours"];
                    }else{
                        $oth = number_format($resultt["othours"],2);

                        $othAR = explode(".", $oth);

                        $TOT_OTH += $othAR[0];
                        $TOT_OTM += $othAR[1];
                    }

                    if($resultt["dothours"] == ""){
                        $doth = $resultt["dothours"];
                    }else{
                        $doth = number_format($resultt["dothours"],2);

                        $othARD = explode(".", $doth);

                        $TOT_DOTH += $othARD[0];
                        $TOT_DOTM += $othARD[1];
                    }

                    if($resultt["hours"] == ""){
                        $hours = $resultt["hours"];
                    }else{
                        $hours = number_format($resultt["hours"],2);

                        $othARWork = explode(".", $hours);

                        $TOT_WORKH += $othARWork[0];
                        $TOT_WORKM += $othARWork[1];
                    }


                   $lam += $resultt["lam"]; 
                    
                }

                //Calculate OT Value
                $TOT_MinsOT = ($TOT_OTH*60) + $TOT_OTM;
                $TOT_OTvalue = $TOT_MinsOT/60;

                //Calculate DOT Value
                $TOT_Mins_DOT = ($TOT_DOTH*60) + $TOT_DOTM;
                $TOT_DOTvalue = $TOT_Mins_DOT/60;

                //Calculate WORK Value
                $TOT_Mins_WORK = ($TOT_WORKH*60) + $TOT_WORKM;
                $TOT_WORKvalue = $TOT_Mins_WORK/60; 

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



                $working_hours = $DataWORK;

                $OT_hours =  $DataOT; 

                if ($userAct == "Shop and Office") 
                {
                    //OT and DOT 
                    $OTPAYPH = (($result["basic"]+$result["br1"]+$result["br2"])/240) * 1.5;
                    $DOTPAYPH = (($result["basic"]+$result["br1"]+$result["br2"])/240) * 2; 
                }
                else
                {
                    //OT and DOT 
                    $OTPAYPH = (($result["basic"]+$result["br1"]+$result["br2"])/200) * 1.5;
                    $DOTPAYPH = (($result["basic"]+$result["br1"]+$result["br2"])/200) * 2; 

                }

                $resx0 = Search("select sum(days) as shortleavetot from employee_leave where uid='" . $result["uid"] . "' and type = 'Short Leave' and date between '" . $result["datefrom"] . "' and '" . $result["dateto"] . "'");
                if ($resultx0 = mysqli_fetch_assoc($resx0)) {
                    
                    if ($resultx0["shortleavetot"] == "") 
                    {
                        $short_leaves = 0;
                    }
                    else
                    {
                        $short_leaves = $resultx0["shortleavetot"];
                    }
                }

                 $query = "select sum(days) as totleaves from employee_leave where uid = '" . $result["uid"] . "' and type != 'Nopay Leave' and type != 'Liue Leave' and type != 'Short Leave' and type != 'Duty Leave' and type != 'Maternity Leave' and type != 'Parental Leave' and MONTH(date) = '".$month."' and YEAR(date) = '".$year."'";
                  $restt = Search($query);
                  if ($resultst = mysqli_fetch_assoc($restt)) {

                      if ($resultst["totleaves"] == "") 
                      {
                          $Gettotleave = 0;
                      }
                      else
                      {
                          $Gettotleave = $resultst["totleaves"];
                      }
                  }

                $NopayDays = $result["nopaydays"];
                $OT_Rate = $OTPAYPH;

                $Attendence_Incentive = $result["att_incen"];
                $Attendence_allow = $result["att1"];
                $Travelling_allow = $result["travl"];
                $Ot_allow = $result["ot"];

                $salary_advance = $result["advance"];

                $resLoan = Search("select sum(installment) as totloan from factoryloan where User_uid = '".$result["uid"]."' and status = '0' and year = '".$year."' and month = '".$month."'");
                 if ($resultLoan = mysqli_fetch_assoc($resLoan)) {

                    $loan =  $resultLoan["totloan"];
                 }
                 else
                 {
                   $loan =  0;
                 }

                $sal_deduct = $result["otherded"];
                $Late_ded = $result["late"];

                $Sal_date = $result["date"];


                $resOtherAllow = Search("select sum(a.amount) as TotalOther from user_has_allowances a, allowances b where a.alwid = b.alwid and a.uid='".$result["uid"]."' and lower(b.name) ='Other Allowances'");
                 if ($resultOtherAllow = mysqli_fetch_assoc($resOtherAllow)) {

                    $Other_allow =  $resultOtherAllow["TotalOther"];
                       
                 }
                 else
                 {
                    $Other_allow =  0;
                 }

                 
                 $resInsuarance = Search("select total as Insur from salerydeductions where user_uid = '".$result["uid"]."' and lower(description) = 'Insurance' and isactive = '0' and year = '".$year."' and month = '".$month."'");
                 if ($resultInsuarance = mysqli_fetch_assoc($resInsuarance)) {

                    $Insuarance =  $resultInsuarance["Insur"];
                       
                 }
                 else
                 {
                    $Insuarance =  0;
                 }
                
                 $resOtherDed = Search("select sum(total) as OtherDED from salerydeductions where user_uid = '".$result["uid"]."' and lower(description) != 'Insurance' and isactive = '0' and year = '".$year."' and month = '".$month."'");
                 if ($resultOtherDed = mysqli_fetch_assoc($resOtherDed)) {

                    $OtherDedS =  $resultOtherDed["OtherDED"];
                       
                 }
                 else
                 {
                    $OtherDedS =  0;
                 }
                
            }

        }
        ?>

        <img src='../../Images/derana_circle.png' align="center" width="10%" style="padding-top: 15px;" />
        <center>
            <h2 style="padding-top: 8px;"><b>- Lakeside Adventist Hospital - Kandy. -</b></h2>
            <h4><u>Salary Pay Slip</u></h4>
        </center>

        <center>
            <table>
                <tr>
                    <td>Year : <?php echo $year;?>
                    <td>&nbsp;</td>
                    <td>Month : <?php echo date("F", mktime(0, 0, 0, $month, 10));?></td>
                    </td>
                </tr>
            </table>
        </center>
        <hr /></br>

        <table width="80%"
            style="border: 1px solid black; padding: 5px; border-collapse: collapse; font-family: 'Times New Roman', Times, serif;">
            <tr style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                <td style="border: 1px solid black; padding: 5px; border-collapse: collapse; font-weight: bold;">
                    Employee Name</td>
                <td colspan='2' style="border: 1px solid black; padding: 5px; border-collapse: collapse;">:
                    <?php echo $userName; ?></td>
            </tr>

            <tr style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                <td style="border: 1px solid black; padding: 5px; border-collapse: collapse; font-weight: bold;">
                    Employee No</td>
                <td colspan='2' style="border: 1px solid black; padding: 5px; border-collapse: collapse;">:
                    <?php echo $userNo; ?></td>
            </tr>

            <tr style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                <td style="border: 1px solid black; padding: 5px; border-collapse: collapse; font-weight: bold;">E.P.F.
                    No</td>
                <td colspan='2' style="border: 1px solid black; padding: 5px; border-collapse: collapse;">:
                    <?php echo $userEpfNo; ?></td>
            </tr>

            <tr style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                <td style="border: 1px solid black; padding: 5px; border-collapse: collapse; font-weight: bold;">
                    Designation</td>
                <td colspan='2' style="border: 1px solid black; padding: 5px; border-collapse: collapse;">:
                    <?php echo $userDesignation; ?></td>
            </tr>

            <tr style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                <td style="border: 1px solid black; padding: 5px; border-collapse: collapse; font-weight: bold;">
                    Department</td>
                <td colspan='2' style="border: 1px solid black; padding: 5px; border-collapse: collapse;">:
                    <?php echo $userDepartment; ?></td>
            </tr>

            <tr style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                <td style="border: 1px solid black; padding: 5px; border-collapse: collapse; font-weight: bold;">Branch
                </td>
                <td colspan='2' style="border: 1px solid black; padding: 5px; border-collapse: collapse;">:
                    <?php echo $userBranch; ?></td>
            </tr>

            <tr style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                <td style="border: 1px solid black; padding: 5px; border-collapse: collapse; font-weight: bold;">NIC No
                </td>
                <td colspan='2' style="border: 1px solid black; padding: 5px; border-collapse: collapse;">:
                    <?php echo $userNIC; ?></td>
            </tr>

            <tr style="border: 1px solid white; padding: 5px; border-collapse: collapse;">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>

            <tr style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                <td style="border: 1px solid black; padding: 5px; border-collapse: collapse; font-weight: bold;">
                    Description</td>
                <td style="border: 1px solid black; padding: 5px; border-collapse: collapse; font-weight: bold;">Hours
                </td>
                <td style="border: 1px solid black; padding: 5px; border-collapse: collapse; font-weight: bold;">Amount
                    (LKR)</td>
            </tr>

            <tr style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                <td style="border: 1px solid black; padding: 5px; border-collapse: collapse;">Total OT Hours </td>
                <td align='center' style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                    <?php echo $OT_hours; ?></td>
                <td align='right' style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                    <?php echo number_format($Ot_allow,2); ?></td>
            </tr>

            <tr style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                <td style="border: 1px solid black; padding: 5px; border-collapse: collapse;">Basic Salary Rs.</td>
                <td colspan='2' align='right' style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                    <?php echo number_format($Basic_sal,2); ?></td>
            </tr>

            <tr style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                <td style="border: 1px solid black; padding: 5px; border-collapse: collapse; font-weight: bold;">Basic
                    Salary For EPF Rs.</td>
                <td colspan='2' align='right'
                    style="border: 1px solid black; padding: 5px; border-collapse: collapse; font-weight: bold;">
                    <?php echo number_format($Basic_sal_for_epf,2); ?></td>
            </tr>
            <tr style="border: 1px solid white; padding: 5px; border-collapse: collapse;">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr style="border: 1px solid white; padding: 5px; border-collapse: collapse; font-weight: bold;">
                <td align="left"><u>Allowances</u></td>
                <td>&nbsp;</td>
            </tr>

            <tr style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                <td style="border: 1px solid black; padding: 5px; border-collapse: collapse;">Fixed Allowance Rs.</td>
                <td colspan='2' align='right' style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                    <?php echo number_format($Attendence_allow,2); ?></td>
            </tr>

            <tr style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                <td style="border: 1px solid black; padding: 5px; border-collapse: collapse;">Vehicle Allowance Rs.
                </td>
                <td colspan='2' align='right' style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                    <?php echo number_format($Travelling_allow,2); ?></td>
            </tr>

            <tr style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                <td style="border: 1px solid black; padding: 5px; border-collapse: collapse;">Other Allowances Rs. </td>
                <td colspan='2' align='right' style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                    <?php echo number_format($Other_allow,2); ?></td>
            </tr>

            <?php
            $TotAllow = 0;
            $resAllow = Search("select a.amount, b.name,b.alwid from user_has_allowances a, allowances b where a.alwid = b.alwid and a.uid = '".$USER."' and b.name !='Attendance Allowance' and b.name !='Travelling Allowance' and b.name !='Other Allowances' and b.name !='Fixed Allowance' and b.name != 'Vehicle Allowance' and b.name !='Budgetary Relief Allowance 1' and b.name !='New Budgetary Relief Allowance ' order by b.alwid");
            while ($resultAllow = mysqli_fetch_assoc($resAllow)) {

                $TotAllow += $resultAllow["amount"]; 
 
                ?>
            <tr style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                <td style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                    <?php echo $resultAllow["name"]; ?></td>
                <td colspan='2' align='right' style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                    <?php echo number_format($resultAllow["amount"],2); ?></td>
            </tr>


            <?php
            }

            $Tot_Gross = $Basic_sal_for_epf + $Attendence_allow + $Travelling_allow + $Ot_allow + $Other_allow + $TotAllow;

            $Tot_Deduction = $EPF8 + $loan + $OtherDedS + $salary_advance + $Late_ded + $Payee_Tax;

            $Total_Net_amount = $Tot_Gross - $Tot_Deduction;

            ?>


            <tr style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                <td style="border: 1px solid black; padding: 5px; border-collapse: collapse; font-weight: bold;">Gross
                    Salary Rs.</td>
                <td colspan='2' align='right'
                    style="border: 1px solid black; padding: 5px; border-collapse: collapse; font-weight: bold;">
                    <?php echo number_format($Tot_Gross,2); ?></td>
            </tr>

            <tr style="border: 1px solid white; padding: 5px; border-collapse: collapse;">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr style="border: 1px solid white; padding: 5px; border-collapse: collapse; font-weight: bold;">
                <td align="left"><u>Deductions</u></td>
                <td>&nbsp;</td>
            </tr>

            <tr style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                <td style="border: 1px solid black; padding: 5px; border-collapse: collapse;">EPF Employee (8%) Rs.</td>
                <td colspan='2' align='right' style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                    <?php echo number_format($EPF8,2); ?></td>
            </tr>

            <tr style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                <td style="border: 1px solid black; padding: 5px; border-collapse: collapse;">Other Deduction Rs.</td>
                <td colspan='2' align='right' style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                    <?php echo number_format($OtherDedS,2); ?></td>
            </tr>

            <tr style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                <td style="border: 1px solid black; padding: 5px; border-collapse: collapse;">Loan Repayment Rs.</td>
                <td colspan='2' align='right' style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                    <?php echo number_format($loan,2); ?></td>
            </tr>

            <tr style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                <td style="border: 1px solid black; padding: 5px; border-collapse: collapse;">Salary Advance Rs.</td>
                <td colspan='2' align='right' style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                    <?php echo number_format($salary_advance,2); ?></td>
            </tr>

            <tr style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                <td style="border: 1px solid black; padding: 5px; border-collapse: collapse;">Late Att. Deduction</td>
                <td colspan='2' align='right' style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                    <?php echo number_format($Late_ded,2); ?></td>
            </tr>

            <tr style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                <td style="border: 1px solid black; padding: 5px; border-collapse: collapse;">PAYEE Tax Amount</td>
                <td colspan='2' align='right' style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                    <?php echo number_format($Payee_Tax,2); ?></td>
            </tr>

            <tr style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                <td style="border: 1px solid black; padding: 5px; border-collapse: collapse; font-weight: bold;">Total
                    Deduction Rs.</td>
                <td colspan='2' align='right'
                    style="border: 1px solid black; padding: 5px; border-collapse: collapse; font-weight: bold;">
                    <?php echo number_format($Tot_Deduction,2); ?></td>
            </tr>
            <tr style="border: 1px solid white; padding: 5px; border-collapse: collapse;">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                <td style="border: 1px solid black; padding: 5px; border-collapse: collapse; font-weight: bold;">Total
                    Net Salary Rs.</td>
                <td colspan='2' align='right'
                    style="border: 1px solid black; padding: 5px; border-collapse: collapse; font-weight: bold;">
                    <?php echo number_format($Total_Net_amount,2); ?></td>
            </tr>

            <tr style="border: 1px solid white; padding: 5px; border-collapse: collapse;">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr style="border: 1px solid white; padding: 5px; border-collapse: collapse; font-weight: bold;">
                <td align="left"><u>Employer's Contribution</u></td>
                <td>&nbsp;</td>
            </tr>
            <tr style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                <td style="border: 1px solid black; padding: 5px; border-collapse: collapse;">EPF Employer (12%) Rs.
                </td>
                <td colspan='2' align='right' style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                    <?php echo number_format($EPF12,2); ?></td>
            </tr>

            <tr style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                <td style="border: 1px solid black; padding: 5px; border-collapse: collapse;">ETF Employer (3%) Rs.</td>
                <td colspan='2' align='right' style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                    <?php echo number_format($ETF3,2); ?></td>
            </tr>

            <tr style="border: 1px solid white; padding: 5px; border-collapse: collapse;">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>

            <tr style="border: 1px solid black; padding: 5px; border-collapse: collapse;">
                <td style="border: 1px solid black; padding: 5px; border-collapse: collapse; font-weight: bold;">
                    <?php echo $userBankAndAccNo; ?></td>
                <td colspan='2' align='right'
                    style="border: 1px solid black; padding: 5px; border-collapse: collapse; font-weight: bold;">
                    <?php echo number_format($Total_Net_amount,2); ?></td>
            </tr>

            <tr style="border: 1px solid white; padding: 5px; border-collapse: collapse;">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr style="border: 1px solid white; padding: 5px; border-collapse: collapse;">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr style="border: 1px solid white; padding: 5px; border-collapse: collapse;">
                <td align="left">Date : <?php echo $Sal_date; ?></td>
                <td align="right">&nbsp;</td>
            </tr>
        </table>
        <br />
    </center>
    <hr />
    <center>
        <p style='font-size:12px; margin-right: 30px;'><small>Appex Payroll - Powered by Appex Software Solutions. WEB :
                www.appexsl.com / Email : info@appexsl.com</small></p>
    </center>
</body>

</html>