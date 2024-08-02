<?php
error_reporting(0);
include("../Contains/header.php");
include '../DB/DB.php';
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Salary Summery Report | Apex Payroll</title>
    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="../Images/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../Images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../Images/favicon/favicon-16x16.png">
    <link href="../Styles/contains.css" rel="stylesheet" type="text/css">
    <link href="../Styles/appStyles.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/css/sweet-alert.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/nprogress/nprogress.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/animate.css/animate.min.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/css/custom.min.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/iCheck/skins/flat/green.css" rel="stylesheet" type="text/css">

    <script src="../JS/jquery-3.1.0.js"></script>
    <script src="../JS/numeral.min.js"></script>

    <script type="text/javascript">
    $(document).ready(function() {
        $('#loading').hide();
        setSpace();

        if ($('#hmon').length !== 0) {
            $('#month').val($('#hmon').val());
            $('#year').val($('#hyer').val());
            $('#type').val($('#typ').val());
            $('#emp_status').val($('#emp_stat').val());
        }

    });
    $(document).ajaxStart(function() {
        $('#loading').show();
    }).ajaxStop(function() {
        $('#loading').hide();
    });

    function setSpace() {
        var wheight = $(window).height();
        var bheight = $('#body').height();
        if (wheight > bheight) {
            var x = wheight - bheight - 30;
            $('#space').height(x);
        }
    }

    function print() {
        var divToPrint0 = document.getElementById('report');
        var newWin = window.open('', 'Print-Window');
        newWin.document.open();
        newWin.document.write(divToPrint0.innerHTML + "<hr/><p>System By Appex Solutions ~ www.appexsl.com</p>");
        newWin.print();

    }

    function exportExcel() {

        var dataz = document.getElementById('report').innerHTML;

        // alert(dataz);

        var url = "../Controller/emp_manage.php?request=setSession";
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                'data': dataz,
                'filename': 'EPF_C_Form'
            },
            success: function(data) {

                // alert(data);

                var page = "../Model/excel_export.php";

                window.location = encodeURI(page);
            }
        });
    }

    function selectMonth() {
        $("#month").val($("#month").val());
    }
    </script>

</head>

<body id="body" class="nav-md" style="background-color: white;">
    <?php include("../Contains/titlebar_dboard.php"); ?>
    <div class="container body">
        <div class="main_container">
            <!-- page content -->
            <div class="" style="width: 100%; margin: 1%;" role="main">

                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">

                        <div class="row x_title">
                            <div class="col-md-6">
                                <h3>Salary Summery Reporting<br /> <small>Genarate Salary Summery Report</small></h3>
                                <table width="120%">
                                    <tr>
                                        <form action="#" method="get">
                                            <td width="180">&nbsp;</td>
                                            <td width="50">
                                                Month :
                                                <select id="month" name="month" class="select-basic"
                                                    onchange="selectMonth()">
                                                    <option value="1">January</option>
                                                    <option value="2">February</option>
                                                    <option value="3">March</option>
                                                    <option value="4">April</option>
                                                    <option value="5">May</option>
                                                    <option value="6">June</option>
                                                    <option value="7">July</option>
                                                    <option value="8">August</option>
                                                    <option value="9">September</option>
                                                    <option value="10">October</option>
                                                    <option value="11">November</option>
                                                    <option value="12">December</option>
                                                </select>
                                            </td>
                                            <td>&nbsp;</td>
                                            <td width="50">
                                                Year :
                                                <select id="year" name="year" class="select-basic">
                                                    <option value="2019">2019</option>
                                                    <option value="2020">2020</option>
                                                    <option value="2021">2021</option>
                                                    <option value="2022">2022</option>
                                                    <option value="2023">2023</option>
                                                    <option value="2024">2024</option>
                                                    <option value="2025">2025</option>
                                                    <option value="2026">2026</option>
                                                    <option value="2027">2027</option>
                                                    <option value="2028">2028</option>
                                                    <option value="2029">2029</option>
                                                    <option value="2030">2030</option>
                                                    <option value="2031">2031</option>
                                                    <option value="2032">2032</option>
                                                    <option value="2033">2033</option>
                                                    <option value="2034">2034</option>
                                                    <option value="2035">2035</option>
                                                    <option value="2036">2036</option>
                                                    <option value="2037">2037</option>
                                                    <option value="2038">2038</option>
                                                    <option value="2039">2039</option>
                                                    <option value="2040">2040</option>
                                            </td>
                                            <td>&nbsp;</td>
                                            <td width="50">
                                                Type :
                                                <select id="type" name="type" class="select-basic">
                                                    <option value="0">For Company</option>
                                                    <option value="1">For Audit</option>
                                            </td>
                                            <td>&nbsp;</td>
                                            <td width="100">
                                                Emp.Status :
                                                <select id="emp_status" name="emp_status" class="select-basic">
                                                    <option value="1">Active</option>
                                                    <option value="0">Not Active</option>
                                                    <option value="%">All</option>
                                            </td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td>&nbsp;</td>
                                            <td></br>&nbsp;<input type="submit" name="submit" value="Generate"
                                                    class="btn btn-primary"></td>
                                            <td></br>&nbsp;<input type="button" value="Print Report"
                                                    class="btn btn-dark" onclick="print()"></td>
                                            <td></br>&nbsp;<input type="button" value="Export Excel"
                                                    class="btn btn-success" onclick="exportExcel()"></td>
                                        </form>
                                    </tr>
                                </table>
                            </div>
                        </div>


                        <div id="report">

                            <center>
                                <h1><b>Derana Medical Laboratory Testing Services (Pvt) Ltd.</b></h1>
                                <h3>Salary Summery Report</h3>

                                <?php
                if (isset($_GET["month"])) {
                  $MONTH = $_GET["month"];

                  $dateObj   = DateTime::createFromFormat('!m', $MONTH);
                  $monthName = $dateObj->format('F');

                  $currentMonth = $monthName;
                  $previouseMonth =  Date('F', strtotime($currentMonth . " last month"));
                }
                ?>
                                <p>Month of <?php echo $monthName; ?> in <?php echo $_GET["year"]; ?> </p>
                                <p><small>Printed Date : <?php echo date("Y/m/d"); ?></small></p>
                            </center>
                            <hr />
                            </br>


                            <center>
                                <div id="tdatax" style="width:50%;">
                                    <table border="1" class="table table-bordered" style="border-collapse: collapse;">
                                        <tbody>
                                            <?php
                      $totAdd = 0;

                      $totCons = 0;
                      $totEM = 0;
                      $totEMPE = 0;
                      $totEarn = 0;
                      $tot_earn = 0;
                      $epf8 = 0;
                      $epf12 = 0;

                      $employerNO = "123456";

                      $month = $_GET["month"];
                      if (count($_GET["month"]) == 1) {
                        $month = "0" . $month;
                      }


                      if ($_GET["type"] == 0) {

                        // select sum(basic) as basicsalary, sum(br1) as BR1, sum(br2) as BR2, sum(nopay) as Nopay, sum(att1) as attendenceallow, sum(travl) as travelallow, sum(att_incen) as attIncentive, sum(ot) as NormalOt, sum(advance) as Advance, sum(late) as latededuct, sum(epf) as epf8, sum(epf12) as EPF12, sum(etf3) as ETF3, sum(otday) as DOT, sum(payee_tax) as PayeeTax from salarycomplete where month = '".$_GET["month"]."' and year = '".$_GET["year"]."'

                        $resalx = Search("select sum(s.basic) as basicsalary, sum(s.br1) as BR1, sum(s.br2) as BR2, sum(s.nopay) as Nopay, sum(s.att1) as attendenceallow, sum(s.travl) as travelallow, sum(s.att_incen) as attIncentive, sum(s.ot) as NormalOt, sum(s.advance) as Advance, sum(s.late) as latededuct, sum(s.epf) as epf8, sum(s.epf12) as EPF12, sum(s.etf3) as ETF3, sum(s.otday) as DOT, sum(s.payee_tax) as PayeeTax from salarycomplete s, user u where s.uid=u.uid and s.month = '" . $_GET["month"] . "' and s.year = '" . $_GET["year"] . "' and u.isactive like '" . $_GET["emp_status"] . "'");

                        if ($resultalx = mysqli_fetch_assoc($resalx)) {

                          $Basic_Salary = $resultalx["basicsalary"];
                          $Br1 = $resultalx["BR1"];
                          $Br2 = $resultalx["BR2"];
                          $Nopay_deduction = $resultalx["Nopay"];
                          $Att_Allow = $resultalx["attendenceallow"];
                          $Trvl_Allow = $resultalx["travelallow"];
                          $Att_Incentive = $resultalx["attIncentive"];
                          $Normal_OT = $resultalx["NormalOt"];
                          $DOT_Pay = $resultalx["DOT"];
                          $Salary_Advance = $resultalx["Advance"];
                          $Late_Deduction = $resultalx["latededuct"];
                          $EPF8 = $resultalx["epf8"];
                          $EPF12 = $resultalx["EPF12"];
                          $ETF3 = $resultalx["ETF3"];

                          if ($resultalx["PayeeTax"] == "") {
                            $PAYEE = 0;
                          } else {
                            $PAYEE = $resultalx["PayeeTax"];
                          }
                        }

                        $resProduct = Search("select sum(a.amount) as TotalProduct from user_has_allowances a, allowances b, salarycomplete c,user u where a.alwid = b.alwid and lower(b.name) ='Production Incentive' and a.uid = c.uid and u.uid=c.uid and u.isactive like '" . $_GET["emp_status"] . "' and c.month = '" . $_GET["month"] . "' and c.year = '" . $_GET["year"] . "'");

                        if ($resultProduct = mysqli_fetch_assoc($resProduct)) {

                          $Produ =  $resultProduct["TotalProduct"];
                        } else {
                          $Produ =  0;
                        }

                        $resOtherAllow = Search("select sum(a.amount) as TotalOther from user_has_allowances a, allowances b, salarycomplete c,user u where a.alwid = b.alwid and a.uid = c.uid and u.uid=c.uid and u.isactive like '" . $_GET["emp_status"] . "' and c.month = '" . $_GET["month"] . "' and c.year = '" . $_GET["year"] . "' and lower(b.name) ='Other Allowances'");

                        if ($resultOtherAllow = mysqli_fetch_assoc($resOtherAllow)) {

                          $Other =  $resultOtherAllow["TotalOther"];
                        } else {
                          $Other =  0;
                        }

                        $resLoan = Search("select sum(fact.installment) as totloan from factoryloan fact, user u where u.uid = fact.User_uid and u.isactive like '" . $_GET["emp_status"] . "' and fact.status = '0' and fact.year = '" . $_GET["year"] . "' and fact.month = '" . $_GET["month"] . "'");
                        if ($resultLoan = mysqli_fetch_assoc($resLoan)) {

                          $Loan =  $resultLoan["totloan"];
                        } else {
                          $Loan =  0;
                        }

                        $insData = 0;
                        $resalxts = Search("select u.uid,u.fname as emp,u.bankno,u.bank from user u,salarycomplete sal where u.uid = sal.uid and sal.month = '" . $_GET["month"] . "' and sal.year = '" . $_GET["year"] . "' and u.isactive like '" . $_GET["emp_status"] . "' group by sal.uid order by cast(u.epfno as unsigned) ASC");


                        while ($resultalxts = mysqli_fetch_assoc($resalxts)) {

                          //select total as Insur from salerydeductions where year = '".$_GET["year"]."' and user_uid = '".$resultalxts["uid"]."' and lower(description) = 'Insurance' and isactive = '1'
                          //2023-06-12 Changed

                          $resInsuarance = Search("select total as Insur from salerydeductions where user_uid = '" . $resultalxts["uid"] . "' and lower(description) = 'Insurance' and isactive = '0' and year = '" . $_GET["year"] . "' and month = '" . $_GET["month"] . "'");
                          if ($resultInsuarance = mysqli_fetch_assoc($resInsuarance)) {

                            $Insuarance =  $resultInsuarance["Insur"];
                          } else {
                            $Insuarance = 0;
                          }

                          $insData += $Insuarance;
                        }




                        $resOtherDed = Search("select sum(sd.total) as OtherDED from salerydeductions sd, user u where sd.user_uid = u.uid and u.isactive like '" . $_GET["emp_status"] . "' and sd.isactive = '0' and sd.year = '" . $_GET["year"] . "' and sd.month = '" . $_GET["month"] . "' and lower(sd.description) != 'Insurance'");

                        if ($resultOtherDed = mysqli_fetch_assoc($resOtherDed)) {

                          $OtherDedS =  $resultOtherDed["OtherDED"];
                        } else {
                          $OtherDedS =  0;
                        }

                        $resTeamLead = Search("select sum(a.amount) as TotalTeamLead from user_has_allowances a, allowances b,salarycomplete c,user u  where a.uid = c.uid and u.uid=c.uid and u.isactive like '" . $_GET["emp_status"] . "' and a.alwid = b.alwid and lower(b.name) ='Team Leader Incentive' and c.month = '" . $_GET["month"] . "' and c.year = '" . $_GET["year"] . "'");

                        if ($resultTeamLead = mysqli_fetch_assoc($resTeamLead)) {

                          $TeamLeads =  $resultTeamLead["TotalTeamLead"];
                        } else {
                          $TeamLeads =  0;
                        }

                        $resMeal = Search("select sum(meal.remainingamount) as TotalMeal from mealremaining meal,user u where u.uid = meal.uid and u.isactive like '" . $_GET["emp_status"] . "' and YEAR(meal.date) = '" . $_GET["year"] . "' and MONTH(meal.date) = '" . $_GET["month"] . "'");

                        if ($resultMeal = mysqli_fetch_assoc($resMeal)) {

                          $MealAmnt =  $resultMeal["TotalMeal"];
                        } else {
                          $MealAmnt =  0;
                        }

                        $epaid = 0;
                        $BankMealRem = 0;
                        $BankDOT = 0;
                        $resalxt = Search("select u.uid,u.fname as emp,u.bankno,u.bank from user u,salarycomplete sal where u.uid = sal.uid and u.bankno != '' and u.bank != '' and u.isactive like '" . $_GET["emp_status"] . "' group by sal.uid order by cast(u.epfno as unsigned) ASC");


                        while ($resultalxt = mysqli_fetch_assoc($resalxt)) {

                          $resalxr = Search("select a.paid,a.otday from salarycomplete a, user b where a.uid = '" . $resultalxt["uid"] . "' and a.uid = b.uid and a.month = '" . $_GET["month"] . "' and a.year = '" . $_GET["year"] . "'");

                          if ($resultalxr = mysqli_fetch_assoc($resalxr)) {


                            $Empaid = $resultalxr["paid"];
                            $EmpDOT = $resultalxr["otday"];

                            $epaid += $Empaid;
                            $BankDOT += $EmpDOT;
                          }

                          $resMealBank = Search("select remainingamount as TotalMeal from mealremaining a,salarycomplete b  where b.uid = a.uid and a.uid = '" . $resultalxt["uid"] . "' and YEAR(a.date) = '" . $_GET["year"] . "' and MONTH(a.date) = '" . $_GET["month"] . "'");
                          if ($resultMealBank = mysqli_fetch_assoc($resMealBank)) {

                            $MealAmntBank =  $resultMealBank["TotalMeal"];
                          } else {
                            $MealAmntBank =  0;
                          }

                          $BankMealRem += $MealAmntBank;
                        }

                        $epaid_cash = 0;
                        $CashMealRem = 0;
                        $CashDOT = 0;
                        $resalxtCC = Search("select u.uid,u.fname as emp,u.bankno,u.bank from user u,salarycomplete sal where u.uid = sal.uid and u.bankno = '' and u.bank = '' and u.isactive like '" . $_GET["emp_status"] . "' group by sal.uid order by cast(u.epfno as unsigned) ASC");


                        while ($resultalxtCC = mysqli_fetch_assoc($resalxtCC)) {

                          $resalxrC = Search("select a.paid,a.otday from salarycomplete a, user b where a.uid = '" . $resultalxtCC["uid"] . "' and a.uid = b.uid and a.month = '" . $_GET["month"] . "' and a.year = '" . $_GET["year"] . "'");

                          if ($resultalxrC = mysqli_fetch_assoc($resalxrC)) {


                            $Empaid_cash = $resultalxrC["paid"];
                            $EmpDOT_Cash = $resultalxrC["otday"];

                            $epaid_cash += $Empaid_cash;
                            $CashDOT += $EmpDOT_Cash;
                          }

                          $resMealCash = Search("select remainingamount as TotalMeal from mealremaining a,salarycomplete b  where b.uid = a.uid and a.uid = '" . $resultalxtCC["uid"] . "' and YEAR(a.date) = '" . $_GET["year"] . "' and MONTH(a.date) = '" . $_GET["month"] . "'");
                          if ($resultMealCash = mysqli_fetch_assoc($resMealCash)) {

                            $MealAmntCash =  $resultMealCash["TotalMeal"];
                          } else {
                            $MealAmntCash =  0;
                          }

                          $CashMealRem += $MealAmntCash;
                        }

                        echo "<tr>";
                        echo "<td width='50%'>Basic Salary</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($Basic_Salary, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Budgetary Allowance 1</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($Br1, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Budgetary Allowance 2</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($Br2, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'><b>Basic With BR</b></td>";
                        echo "<td style='text-align: right' width='50%'><b>" . number_format($Basic_Salary + $Br1 + $Br2, 2) . "</b></td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>&nbsp;</td>";
                        echo "<td width='50%'>&nbsp;</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Nopay Deduction</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($Nopay_deduction, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Basic Salary For EPF</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($Basic_Salary + $Br1 + $Br2 - $Nopay_deduction, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Attendance Allowance</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($Att_Allow, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Travelling Allowance</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($Trvl_Allow, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Production Incentive</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($Produ, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Other Allowance</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($Other, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Salary Arrears (Month of " . $previouseMonth . ")</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format(0.00, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Meal Allowance</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($MealAmnt, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Team Leader Incentive</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($TeamLeads, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Attendance Incentive</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($Att_Incentive, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Normal OT Payment</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($Normal_OT, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Double OT Payment</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($DOT_Pay, 2) . "</td>";
                        echo "</tr>";

                        $gross = $Basic_Salary + $Br1 + $Br2 - $Nopay_deduction + $Att_Allow + $Trvl_Allow + $Produ + $Other + $Att_Incentive + $Normal_OT + $DOT_Pay + $MealAmnt + $TeamLeads;

                        echo "<tr>";
                        echo "<td width='50%'><b>Gross Salary Amount</b></td>";
                        echo "<td style='text-align: right' width='50%'><b>" . number_format($gross, 2) . "</b></td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>&nbsp;</td>";
                        echo "<td width='50%'>&nbsp;</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Salary Advance</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($Salary_Advance, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Staff Loan</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($Loan, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Insurance Deduction</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($insData, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Late Deduction</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($Late_Deduction, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Other Deduction</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($OtherDedS, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>EPF Contribution (8%)</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($EPF8, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>PAYEE Tax Amount</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($PAYEE, 2) . "</td>";
                        echo "</tr>";

                        $tot_deduct = $Salary_Advance + $Loan + $insData + $Late_Deduction + $OtherDedS + $EPF8 + $PAYEE;

                        echo "<tr>";
                        echo "<td width='50%'><b>Total Deduction</b></td>";
                        echo "<td style='text-align: right' width='50%'><b>" . number_format($tot_deduct, 2) . "</b></td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>&nbsp;</td>";
                        echo "<td width='50%'>&nbsp;</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'><b>Net Salary Amount</b></td>";
                        echo "<td style='text-align: right' width='50%'><b>" . number_format($gross - $tot_deduct, 2) . "</b></td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>&nbsp;</td>";
                        echo "<td width='50%'>&nbsp;</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Bank Transfer Amount</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($epaid + $BankDOT + $BankMealRem, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Cash Payment</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($epaid_cash + $CashDOT + $CashMealRem, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>EPF Contribution (12%)</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($EPF12, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Total EPF Contribution (20%)</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($EPF8 + $EPF12, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>&nbsp;</td>";
                        echo "<td width='50%'>&nbsp;</td>";
                        echo "</tr>";


                        echo "<tr>";
                        echo "<td width='50%'>ETF Contribution (3%)</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($ETF3, 2) . "</td>";
                        echo "</tr>";
                      } else {

                        // select sum(basic) as basicsalary, sum(br1) as BR1, sum(br2) as BR2, sum(nopay) as Nopay, sum(att1) as attendenceallow, sum(travl) as travelallow, sum(att_incen) as attIncentive, sum(ot) as NormalOt, sum(advance) as Advance, sum(late) as latededuct, sum(epf) as epf8, sum(epf12) as EPF12, sum(etf3) as ETF3, sum(payee_tax) as PayeeTax from salarycomplete where month = '".$_GET["month"]."' and year = '".$_GET["year"]."'


                        $resalx = Search("select sum(s.basic) as basicsalary, sum(s.br1) as BR1, sum(s.br2) as BR2, sum(s.nopay) as Nopay, sum(s.att1) as attendenceallow, sum(s.travl) as travelallow, sum(s.att_incen) as attIncentive, sum(s.ot) as NormalOt, sum(s.advance) as Advance, sum(s.late) as latededuct, sum(s.epf) as epf8, sum(s.epf12) as EPF12, sum(s.etf3) as ETF3, sum(s.payee_tax) as PayeeTax from salarycomplete s, user u where s.uid=u.uid and s.month = '" . $_GET["month"] . "' and s.year = '" . $_GET["year"] . "' and u.isactive like '" . $_GET["emp_status"] . "'");


                        if ($resultalx = mysqli_fetch_assoc($resalx)) {

                          $Basic_Salary = $resultalx["basicsalary"];
                          $Br1 = $resultalx["BR1"];
                          $Br2 = $resultalx["BR2"];
                          $Nopay_deduction = $resultalx["Nopay"];
                          $Att_Allow = $resultalx["attendenceallow"];
                          $Trvl_Allow = $resultalx["travelallow"];
                          $Att_Incentive = $resultalx["attIncentive"];
                          $Normal_OT = $resultalx["NormalOt"];
                          $Salary_Advance = $resultalx["Advance"];
                          $Late_Deduction = $resultalx["latededuct"];
                          $EPF8 = $resultalx["epf8"];
                          $EPF12 = $resultalx["EPF12"];
                          $ETF3 = $resultalx["ETF3"];

                          if ($resultalx["PayeeTax"] == "") {
                            $PAYEE = 0;
                          } else {
                            $PAYEE = $resultalx["PayeeTax"];
                          }
                        }

                        $resProduct = Search("select sum(a.amount) as TotalProduct from user_has_allowances a, allowances b, salarycomplete c,user u where a.alwid = b.alwid and lower(b.name) ='Production Incentive' and a.uid = c.uid and u.uid=c.uid and u.isactive like '" . $_GET["emp_status"] . "' and c.month = '" . $_GET["month"] . "' and c.year = '" . $_GET["year"] . "'");
                        if ($resultProduct = mysqli_fetch_assoc($resProduct)) {

                          $Produ =  $resultProduct["TotalProduct"];
                        } else {
                          $Produ =  0;
                        }

                        $resOtherAllow = Search("select sum(a.amount) as TotalOther from user_has_allowances a, allowances b, salarycomplete c,user u where a.alwid = b.alwid and a.uid = c.uid and u.uid=c.uid and u.isactive like '" . $_GET["emp_status"] . "' and c.month = '" . $_GET["month"] . "' and c.year = '" . $_GET["year"] . "' and lower(b.name) ='Other Allowances'");
                        if ($resultOtherAllow = mysqli_fetch_assoc($resOtherAllow)) {

                          $Other =  $resultOtherAllow["TotalOther"];
                        } else {
                          $Other =  0;
                        }

                        $resLoan = Search("select sum(fact.installment) as totloan from factoryloan fact, user u where u.uid = fact.User_uid and u.isactive like '" . $_GET["emp_status"] . "' and fact.status = '0' and fact.year = '" . $_GET["year"] . "' and fact.month = '" . $_GET["month"] . "'");
                        if ($resultLoan = mysqli_fetch_assoc($resLoan)) {

                          $Loan =  $resultLoan["totloan"];
                        } else {
                          $Loan =  0;
                        }

                        $insData = 0;
                        $resalxts = Search("select u.uid,u.fname as emp,u.bankno,u.bank from user u,salarycomplete sal where u.uid = sal.uid and sal.month = '" . $_GET["month"] . "' and sal.year = '" . $_GET["year"] . "' and u.isactive like '" . $_GET["emp_status"] . "' group by sal.uid order by cast(u.epfno as unsigned) ASC");


                        while ($resultalxts = mysqli_fetch_assoc($resalxts)) {

                          //select total as Insur from salerydeductions where year = '".$_GET["year"]."' and user_uid = '".$resultalxts["uid"]."' and lower(description) = 'Insurance' and isactive = '1'
                          //2023-06-12 Changed

                          $resInsuarance = Search("select total as Insur from salerydeductions where user_uid = '" . $resultalxts["uid"] . "' and lower(description) = 'Insurance' and isactive = '0' and year = '" . $_GET["year"] . "' and month = '" . $_GET["month"] . "'");
                          if ($resultInsuarance = mysqli_fetch_assoc($resInsuarance)) {

                            $Insuarances =  $resultInsuarance["Insur"];
                            $insData += $Insuarance;
                          } else {
                            $Insuarances = 0;
                          }

                          $insData += $Insuarances;
                        }




                        $resOtherDed = Search("select sum(sd.total) as OtherDED from salerydeductions sd, user u where sd.user_uid = u.uid and u.isactive like '" . $_GET["emp_status"] . "' and sd.isactive = '0' and sd.year = '" . $_GET["year"] . "' and sd.month = '" . $_GET["month"] . "' and lower(sd.description) != 'Insurance'");
                        if ($resultOtherDed = mysqli_fetch_assoc($resOtherDed)) {

                          $OtherDedS =  $resultOtherDed["OtherDED"];
                        } else {
                          $OtherDedS =  0;
                        }


                        $resTeamLead = Search("select sum(a.amount) as TotalTeamLead from user_has_allowances a, allowances b,salarycomplete c,user u  where a.uid = c.uid and u.uid=c.uid and u.isactive like '" . $_GET["emp_status"] . "' and a.alwid = b.alwid and lower(b.name) ='Team Leader Incentive' and c.month = '" . $_GET["month"] . "' and c.year = '" . $_GET["year"] . "'");
                        if ($resultTeamLead = mysqli_fetch_assoc($resTeamLead)) {

                          $TeamLead =  $resultTeamLead["TotalTeamLead"];
                        } else {
                          $TeamLead =  0;
                        }

                        $epaid = 0;
                        $BankMealRems = 0;
                        $BankDOTs = 0;
                        $resalxt = Search("select u.uid,u.fname as emp,u.bankno,u.bank from user u,salarycomplete sal where u.uid = sal.uid and u.bankno != '' and u.bank != '' and u.isactive like '" . $_GET["emp_status"] . "' group by sal.uid order by cast(u.epfno as unsigned) ASC");


                        while ($resultalxt = mysqli_fetch_assoc($resalxt)) {

                          $resalxr = Search("select a.paid,a.otday from salarycomplete a, user b where a.uid = '" . $resultalxt["uid"] . "' and a.uid = b.uid and a.month = '" . $_GET["month"] . "' and a.year = '" . $_GET["year"] . "'");

                          if ($resultalxr = mysqli_fetch_assoc($resalxr)) {


                            $Empaid = $resultalxr["paid"];
                            $EmpDOT = $resultalxr["otday"];

                            $epaid += $Empaid;
                            $BankDOTs += $EmpDOT;
                          }

                          $resMealBank = Search("select remainingamount as TotalMeal from mealremaining a,salarycomplete b  where b.uid = a.uid and a.uid = '" . $resultalxt["uid"] . "' and YEAR(a.date) = '" . $_GET["year"] . "' and MONTH(a.date) = '" . $_GET["month"] . "'");
                          if ($resultMealBank = mysqli_fetch_assoc($resMealBank)) {

                            $MealAmntBank =  $resultMealBank["TotalMeal"];
                          } else {
                            $MealAmntBank =  0;
                          }

                          $BankMealRems += $MealAmntBank;
                        }

                        echo "<tr>";
                        echo "<td width='50%'>Basic Salary</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($Basic_Salary, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Budgetary Allowance 1</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($Br1, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Budgetary Allowance 2</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($Br2, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'><b>Basic With BR</b></td>";
                        echo "<td style='text-align: right' width='50%'><b>" . number_format($Basic_Salary + $Br1 + $Br2, 2) . "</b></td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>&nbsp;</td>";
                        echo "<td width='50%'>&nbsp;</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Nopay Deduction</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($Nopay_deduction, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Basic Salary For EPF</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($Basic_Salary + $Br1 + $Br2 - $Nopay_deduction, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Attendance Allowance</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($Att_Allow, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Travelling Allowance</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($Trvl_Allow, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Production Incentive</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($Produ, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Other Allowance</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($Other, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Salary Arrears (Month of " . $previouseMonth . ")</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format(0.00, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Team Leader Incentive</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($TeamLead, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Attendance Incentive</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($Att_Incentive, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Normal OT Payment</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($Normal_OT, 2) . "</td>";
                        echo "</tr>";

                        $gross = $Basic_Salary + $Br1 + $Br2 - $Nopay_deduction + $Att_Allow + $Trvl_Allow + $Produ + $Other + $Att_Incentive + $Normal_OT + $TeamLead;

                        echo "<tr>";
                        echo "<td width='50%'><b>Gross Salary Amount</b></td>";
                        echo "<td style='text-align: right' width='50%'><b>" . number_format($gross, 2) . "</b></td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>&nbsp;</td>";
                        echo "<td width='50%'>&nbsp;</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Salary Advance</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($Salary_Advance, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Staff Loan</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($Loan, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Insurance Deduction</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($insData, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Late Deduction</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($Late_Deduction, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Other Deduction</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($OtherDedS, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>EPF Contribution (8%)</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($EPF8, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>PAYEE Tax Amount</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($PAYEE, 2) . "</td>";
                        echo "</tr>";

                        $tot_deduct = $Salary_Advance + $Loan + $insData + $Late_Deduction + $OtherDedS + $EPF8 + $PAYEE;

                        echo "<tr>";
                        echo "<td width='50%'><b>Total Deduction</b></td>";
                        echo "<td style='text-align: right' width='50%'><b>" . number_format($tot_deduct, 2) . "</b></td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>&nbsp;</td>";
                        echo "<td width='50%'>&nbsp;</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'><b>Net Salary Amount</b></td>";
                        echo "<td style='text-align: right' width='50%'><b>" . number_format($gross - $tot_deduct, 2) . "</b></td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>&nbsp;</td>";
                        echo "<td width='50%'>&nbsp;</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Bank Transfer Amount</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($epaid + $BankDOTs + $BankMealRems, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>EPF Contribution (12%)</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($EPF12, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>Total EPF Contribution (20%)</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($EPF8 + $EPF12, 2) . "</td>";
                        echo "</tr>";

                        echo "<tr>";
                        echo "<td width='50%'>&nbsp;</td>";
                        echo "<td width='50%'>&nbsp;</td>";
                        echo "</tr>";


                        echo "<tr>";
                        echo "<td width='50%'>ETF Contribution (3%)</td>";
                        echo "<td style='text-align: right' width='50%'>" . number_format($ETF3, 2) . "</td>";
                        echo "</tr>";
                      }





                      ?>


                                        </tbody>
                                    </table>
                                    </br>
                                </div>
                            </center>

                            </br>
                            <?php

              // }
              ?>




                        </div>

                    </div>



                </div>

            </div>
        </div>
    </div>

    <?php
  if (isset($_REQUEST["submit"])) {

    echo "<input type='hidden' id='hmon' value='" . $_REQUEST["month"] . "'>";
    echo "<input type='hidden' id='hyer' value='" . $_REQUEST["year"] . "'>";
    echo "<input type='hidden' id='typ' value='" . $_REQUEST["type"] . "'>";
    echo "<input type='hidden' id='emp_stat' value='" . $_REQUEST["emp_status"] . "'>";
  }
  ?>

    <div id="space"></div>

    <?php include("../Contains/footer.php"); ?>

    <?php

  function getSurName($name)
  {
    $initials = "";
    $words = explode(' ', $name);
    for ($i = 0; $i < count($words) - 1; $i++) {
      $initials .= strtoupper(substr($words[$i], 0, 1)) . " ";
    }

    return rtrim($initials);
  }

  ?>

    </div>
    </div>
</body>

</html>