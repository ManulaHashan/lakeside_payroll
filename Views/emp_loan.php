<?php
error_reporting(0);
include("../Contains/header.php");
include '../DB/DB.php';
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Loan Reports | Apex Payroll</title>
    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="../Images/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../Images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../Images/favicon/favicon-16x16.png">
    <!-- <link href="../Styles/Stylie.css" rel="stylesheet" type="text/css"> -->
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
            $('#year').val($('#hyer').val());
            $('#employee').val($('#user').val());
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

        var dataz = document.getElementById('tdatax').innerHTML;

        // alert(dataz);

        var url = "../Controller/emp_manage.php?request=setSession";
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                'data': dataz,
                'filename': 'Salary_report'
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

                                <table>
                                    <tr>
                                        <td>
                                            <h3>Loan Reporting<br /> <small>Genarate Loan Reports</small></h3>
                                        </td>
                                    </tr>
                                </table>

                                <table width="120%">
                                    <tr>
                                        <form action="#" method="get">
                                            <td width="150">&nbsp;</td>
                                            <td width="50">
                                                Year :
                                                <select id="year" name="year" class="select-basic"
                                                    style="height: 23px;">
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
                                            </td>
                                            <td>&nbsp;</td>
                                            <td width="50">
                                                Employee :
                                                <select id="employee" name="employee" class="select-basic"
                                                    style="width: 150px; height: 23px;">
                                                    <option value="%"></option>
                                                    <?php
                          $query = "select uid,fname,lname,jobcode,epfno from user where isactive='1' and uid != '2' order by length(jobcode),jobcode ASC";
                          $res = Search($query);
                          while ($result = mysqli_fetch_assoc($res)) {
                          ?>
                                                    <option value="<?php echo $result["uid"]; ?>">
                                                        <?php echo $result["jobcode"]; ?> : &nbsp;
                                                        <?php echo $result["fname"]; ?> </option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td>&nbsp;</td>
                                            <td width="500">&nbsp;<input type="submit" name="submit" value="Generate"
                                                    class="btn btn-primary">&nbsp;<input type="button"
                                                    value="Print Report" class="btn btn-dark"
                                                    onclick="print()">&nbsp;<input type="button" value="Export Excel"
                                                    class="btn btn-success" onclick="exportExcel()"></td>
                                        </form>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div id="report">

                            <center>
                                <h3>Loan Summery</h3>
                                <p>Year : <?php echo $_GET["year"]; ?></p>
                            </center>
                            <hr />
                            <div>
                                <h4 style="float: right;">Printed Date : <?php echo date("Y/m/d"); ?>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h4>
                            </div>

                            </br>

                            <?php
              //       $resal = Search("select etid,name from employeetype");
              // while ($resultal = mysqli_fetch_assoc($resal)) {

              //  echo "Department : ".$resultal["name"]. "<br/>";

              ?>
                            <br>
                            <br>
                            <div id="tdatax" style="width:98%;">
                                <table border="1" class="table table-bordered" style="border-collapse: collapse;">
                                    <thead>
                                        <th>Employee Name</th>
                                        <th>Added Date</th>
                                        <th>Loan Start</th>
                                        <th>Loan Completion</th>
                                        <th>Interest</th>
                                        <th>Installments Count</th>
                                        <th>Installment Amount Rs.</th>
                                        <th>Ongoing Count</th>
                                        <th>Completed Count</th>
                                        <th>Loan Amount Rs.</th>
                                        <th>Paid Amount Rs.</th>
                                        <th>Due Amount Rs.</th>
                                        <th>Remark</th>

                                    </thead>
                                    <tbody>
                                        <?php

                    $totinstall = 0;
                    $totAmount = 0;
                    $tot_paid = 0;
                    $Loan_amt = 0;
                    $tot_due = 0;

                    $resalx = Search("select User_uid,count(flid) as loancount,date,amount,interest,installment,real_installments,remark from factoryloan where YEAR(date) = '" . $_GET["year"] . "' and User_uid like '" . $_GET["employee"] . "' group by User_uid,date order by date");

                    while ($resultalx = mysqli_fetch_assoc($resalx)) {

                      //Loan Strat Year and Month
                      $res_start = Search("select year,month from factoryloan where User_uid = '" . $resultalx["User_uid"] . "' and date='" . $resultalx["date"] . "' order by flid ASC limit 1");

                      if ($result_start = mysqli_fetch_assoc($res_start)) {

                        $Start_Data = $result_start["year"] . " - " . date("F", mktime(0, 0, 0, $result_start["month"], 10));
                      } else {
                        $Start_Data = "None";
                      }

                      //Loan End Year and Month
                      $res_end = Search("select year,month from factoryloan where User_uid = '" . $resultalx["User_uid"] . "' and date='" . $resultalx["date"] . "' order by flid DESC limit 1");

                      if ($result_end = mysqli_fetch_assoc($res_end)) {

                        $End_Data = $result_end["year"] . " - " . date("F", mktime(0, 0, 0, $result_end["month"], 10));
                      } else {
                        $End_Data = "None";
                      }

                      //Loan Ongoing Count
                      $res_loan_ongoing = Search("select count(flid) as ongoingcount from factoryloan where User_uid = '" . $resultalx["User_uid"] . "' and date='" . $resultalx["date"] . "' and status = '1' ");

                      if ($result_ongoing = mysqli_fetch_assoc($res_loan_ongoing)) {
                        $Ongoing_Count = $result_ongoing["ongoingcount"];
                      } else {
                        $Ongoing_Count = "0";
                      }

                      //Loan Completed Count
                      $res_loan_complete = Search("select count(flid) as completecount from factoryloan where User_uid = '" . $resultalx["User_uid"] . "' and date='" . $resultalx["date"] . "' and status = '0' ");

                      if ($result_complete = mysqli_fetch_assoc($res_loan_complete)) {
                        $Complete_Count = $result_complete["completecount"];
                      } else {
                        $Complete_Count = "0";
                      }


                      //Employee Name
                      $res_emp_name = Search("select fname from user where uid = '" . $resultalx["User_uid"] . "'");

                      if ($result_emp = mysqli_fetch_assoc($res_emp_name)) {
                        $Emp_Name = $result_emp["fname"];
                      } else {
                        $Emp_Name = "None";
                      }

                      //Paid Amount
                      $resalxr = Search("select sum(installment) as totalpaid from factoryloan where User_uid = '" . $resultalx["User_uid"] . "' and date='" . $resultalx["date"] . "' and status = '0'");

                      if ($resultalxr = mysqli_fetch_assoc($resalxr)) {

                        $Emploan = $resultalxr["totalpaid"];
                        $tot_paid += $Emploan;
                      } else {
                        $Emploan = "0";
                        $tot_paid = "0";
                      }

                      $Loan_amt = $resultalx["amount"];
                      $Due_amt = $Loan_amt - $Emploan;
                      if ($Due_amt <= 0) {
                        $Due_amt = 0;
                      }
                      $tot_due += $Due_amt;

                      echo "<tr>";
                      echo "<td>" . $Emp_Name . "</td>";
                      echo "<td><center>" . $resultalx["date"] . "</center></td>";
                      echo "<td>" . $Start_Data . "</td>";
                      echo "<td>" . $End_Data . "</td>";
                      echo "<td><center>" . $resultalx["interest"] . "</center></td>";
                      echo "<td><center>" . $resultalx["real_installments"] . "</center></td>";
                      echo "<td align='right'>" . number_format($resultalx["installment"], 2) . "</td>";
                      echo "<td><center>" . $Ongoing_Count . "</center></td>";
                      echo "<td><center>" . $Complete_Count . "</center></td>";
                      echo "<td align='right'>" . number_format($resultalx["amount"], 2) . "</td>";
                      echo "<td align='right'>" . number_format($Emploan, 2) . "</td>";
                      echo "<td align='right'>" . number_format($Due_amt, 2) . "</td>";
                      echo "<td>" . $resultalx["remark"] . "</td>";
                      echo "</tr>";

                      $totinstall += $resultalx["installment"];
                      $totAmount += $resultalx["amount"];
                    }
                    ?>
                                        <tr style="font-weight: bold;">
                                            <td colspan="6" align="right">TOTAL</td>
                                            <td align='right'><?php echo number_format($totinstall, 2); ?></td>
                                            <td colspan="2"></td>
                                            <td align='right'><?php echo number_format($totAmount, 2); ?></td>
                                            <td align='right'><?php echo number_format($tot_paid, 2); ?></td>
                                            <td align='right'><?php echo number_format($tot_due, 2); ?></td>
                                            <td></td>
                                        </tr>

                                    </tbody>
                                </table>
                                </br>
                                <h3><u>Summery</u></h3>
                                <table border="1" class="table table-bordered"
                                    style="border-collapse: collapse; width: 600px;">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>Total Loan Amount Rs.</th>
                                            <th>Total Paid Amount Rs.</th>
                                            <th>Total Due Amount Rs.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Total</td>
                                            <td align='right'><?php echo number_format($totAmount, 2); ?></td>
                                            <td align='right'><?php echo number_format($tot_paid, 2); ?></td>
                                            <td align='right'><?php echo number_format($tot_due, 2); ?></td>
                                        </tr>
                                    </tbody>
                                </table>

                            </div>
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

    echo "<input type='hidden' id='hyer' value='" . $_REQUEST["year"] . "'>";
    echo "<input type='hidden' id='user' value='" . $_REQUEST["employee"] . "'>";
  }
  ?>

    <div id="space"></div>

    <?php include("../Contains/footer.php"); ?>
    </div>
    </div>
</body>

</html>