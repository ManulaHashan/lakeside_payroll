<?php
error_reporting(0);
include("../Contains/header.php");
include '../DB/DB.php';
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>ETF 6 Months Return | Apex Payroll</title>
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

        if ($('#hmon').length !== 0 || $('#fmon').length !== 0) {
            $('#tomonth').val($('#hmon').val());
            $('#frommonth').val($('#fmon').val());
            $('#year').val($('#hyer').val());
            $('#year2').val($('#hyer2').val());
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

        var dataz = document.getElementById('tdatax').innerHTML;

        // alert(dataz);

        var url = "../Controller/emp_manage.php?request=setSession";
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                'data': dataz,
                'filename': 'ETF_6_Month_Rep'
            },
            success: function(data) {

                // alert(data);

                var page = "../Model/excel_export.php";

                window.location = encodeURI(page);
            }
        });
    }

    function selectMonth() {
        $("#tomonth").val($("#tomonth").val());
        $("#frommonth").val($("#frommonth").val());
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

                                <table width="150%">
                                    <tr>
                                        <form action="#" method="get">
                                            <td>
                                                <h3>ETF 6 Months Return<br /> <small>Genarate 6 Months Return
                                                        Report</small></h3>
                                            </td>
                                            <td width="100">&nbsp;</td>
                                            <td width="50">
                                                From Month :
                                                <select id="tomonth" name="tomonth" class="select-basic"
                                                    onchange="selectMonth()" style="height: 23px;">
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
                                                To Month :
                                                <select id="frommonth" name="frommonth" class="select-basic"
                                                    onchange="selectMonth()" style="height: 23px;">
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
                                            <td width="70">
                                                From Year :
                                                <select id="year" name="year" class="select-basic"
                                                    style="width: 70px; height: 23px;">
                                                    <option value="2019">2019</option>
                                                    <option value="2020">2020</option>
                                                    <option value="2021">2021</option>
                                                    <option value="2022">2022</option>
                                                    <option value="2023">2023</option>
                                                    <option value="2024">2024</option>
                                                    <option value="2025">2025</option>
                                                    <option value="2026">2026</option>
                                                </select>
                                            </td>
                                            <td>&nbsp;</td>
                                            <td width="50">
                                                To Year :
                                                <select id="year2" name="year2" class="select-basic"
                                                    style="height: 23px;">
                                                    <option value="2019">2019</option>
                                                    <option value="2020">2020</option>
                                                    <option value="2021">2021</option>
                                                    <option value="2022">2022</option>
                                                    <option value="2023">2023</option>
                                                    <option value="2024">2024</option>
                                                    <option value="2025">2025</option>
                                                    <option value="2026">2026</option>
                                                </select>
                                            </td>
                                            <td>&nbsp;</td>
                                            <td width="100">
                                                Emp.Status :
                                                <select id="emp_status" name="emp_status" class="select-basic"
                                                    style="height: 23px;">
                                                    <option value="1">Active</option>
                                                    <option value="0">Not Active</option>
                                                    <option value="%">All</option>
                                            </td>
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
                                <h3>ETF 6 Months Return</h3>
                                <p>From Month : <?php echo $_GET["tomonth"]; ?> | To Month :
                                    <?php echo $_GET["frommonth"]; ?> | From Year :
                                    <?php echo $_GET["year"];
                                                                                                                              ?> | To Year : <?php echo $_GET["year2"];
                                  ?></p>
                            </center>
                            <hr />
                            <div>
                                <h4 style="float: right;">Printed Date : <?php echo date("Y/m/d"); ?>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h4>
                            </div>

                            <br>
                            <div id="tdatax" style="width:90%;">
                                <table border="1" class="table table-bordered" style="border-collapse: collapse;">
                                    <thead>

                                        <tr>
                                            <th colspan="5"></th>
                                            <th colspan="29" style="text-align: center">Total Gross Wages And
                                                Contribution</th>
                                        </tr>
                                        <tr>
                                            <th colspan="5"></th>
                                            <?php

                      for ($b = $_GET["year"]; $b <= $_GET["year2"]; $b++) {
                        for ($a = $_GET["tomonth"]; $a <= $_GET["frommonth"]; $a++) {

                          if ($a == 1) {

                            $monthname = "January";
                          } elseif ($a == 2) {

                            $monthname = "February";
                          } elseif ($a == 3) {

                            $monthname = "March";
                          } elseif ($a == 4) {

                            $monthname = "April";
                          } elseif ($a == 5) {

                            $monthname = "May";
                          } elseif ($a == 6) {

                            $monthname = "June";
                          } elseif ($a == 7) {

                            $monthname = "July";
                          } elseif ($a == 8) {

                            $monthname = "August";
                          } elseif ($a == 9) {

                            $monthname = "September";
                          } elseif ($a == 10) {

                            $monthname = "October";
                          } elseif ($a == 11) {

                            $monthname = "November";
                          } elseif ($a == 12) {

                            $monthname = "December";
                          }


                          echo "<th colspan='2' style='text-align: center'>" . $monthname . "-" . $b . "</th>";
                        }
                      }




                      ?>
                                        </tr>

                                        <tr>
                                            <th>No.</th>
                                            <th>Name of Memeber</th>
                                            <th>Member No.</th>
                                            <th>NIC No</th>
                                            <th>Total Contribution</th>


                                            <?php

                      for ($c = $_GET["year"]; $c <= $_GET["year2"]; $c++) {
                        for ($b = $_GET["tomonth"]; $b <= $_GET["frommonth"]; $b++) {
                          if ($b == 1) {

                            $A = "To Earn";
                            $B =  "To Con";
                          } elseif ($b == 2) {

                            $A = "To Earn";
                            $B =  "To Con";
                          } elseif ($b == 3) {

                            $A = "To Earn";
                            $B =  "To Con";
                          } elseif ($b == 4) {

                            $A = "To Earn";
                            $B =  "To Con";
                          } elseif ($b == 5) {

                            $A = "To Earn";
                            $B =  "To Con";
                          } elseif ($b == 6) {

                            $A = "To Earn";
                            $B =  "To Con";
                          } elseif ($b == 7) {

                            $A = "To Earn";
                            $B =  "To Con";
                          } elseif ($b == 8) {

                            $A = "To Earn";
                            $B =  "To Con";
                          } elseif ($b == 9) {

                            $A = "To Earn";
                            $B =  "To Con";
                          } elseif ($b == 10) {

                            $A = "To Earn";
                            $B =  "To Con";
                          } elseif ($b == 11) {

                            $A = "To Earn";
                            $B =  "To Con";
                          } elseif ($b == 12) {

                            $A = "To Earn";
                            $B =  "To Con";
                          }


                          echo "<th>" . $A . "</th>";
                          echo "<th>" . $B . "</th>";
                        }
                      }





                      ?>
                                        </tr>

                                    </thead>
                                    <tbody>
                                        <?php
                    $totAdd = 0;

                    $totCons = 0;
                    $totEM = 0;
                    $totEMPE = 0;
                    $totEarn = 0;
                    $Totetf = 0;
                    $etf3 = 0;
                    $etf3A = 0;


                    $employerNO = "123456";

                    $tomonth = $_GET["tomonth"];
                    if (count($_GET["tomonth"]) == 1) {
                      $tomonth = "0" . $tomonth;
                    }

                    $frommonth = $_GET["frommonth"];
                    if (count($_GET["frommonth"]) == 1) {
                      $frommonth = "0" . $frommonth;
                    }

                    $resalx = Search("select u.uid,u.fname as emp,u.nic,u.epfno from user u,salarycomplete sal where u.uid = sal.uid and u.isactive like '" . $_GET["emp_status"] . "' group by sal.uid order by cast(u.epfno as unsigned) ASC");

                    $id = 0;
                    while ($resultalx = mysqli_fetch_assoc($resalx)) {

                      $id++;
                      echo "<tr>";
                      echo "<td>" . $id . "</td>";
                      echo "<td>" . $resultalx["emp"] . "</td>";
                      echo "<td><center>" . $resultalx["epfno"] . "<center></td>";
                      echo "<td><center>" . $resultalx["nic"] . "<center></td>";

                      $tot_conn = 0;
                      for ($ac = $_GET["year"]; $ac <= $_GET["year2"]; $ac++) {


                        for ($m = $_GET["tomonth"]; $m <= $_GET["frommonth"]; $m++) {
                          $tot_earn_mo = 0;

                          $resalxra = Search("select (basic+br1+br2-nopay) as totearn,etf3 from salarycomplete where uid = '" . $resultalx["uid"] . "' and month = '" . $m . "' and year = '" . $ac . "'");

                          while ($resultalaa = mysqli_fetch_assoc($resalxra)) {

                            $tot_earn_mo = $resultalaa["totearn"];
                          }

                          $tot_earn += $tot_earn_mo;
                          $etf3 = $tot_earn_mo * 0.03;
                          $tot_conn = $tot_conn + $etf3;
                        }
                      }
                      $totCons += $tot_conn;
                      echo "<td align='right'>" . number_format($tot_conn, 2) . "</td>";


                      for ($bc = $_GET["year"]; $bc <= $_GET["year2"]; $bc++) {
                        for ($i = $_GET["tomonth"]; $i <= $_GET["frommonth"]; $i++) {
                          $tot_earn_mo = 0;


                          $resalxr = Search("select (basic+br1+br2-nopay) as totearn,etf3 from salarycomplete where uid = '" . $resultalx["uid"] . "' and month = '" . $i . "' and year = '" . $bc . "'");

                          while ($resultalxr = mysqli_fetch_assoc($resalxr)) {

                            $tot_earn_mo = $resultalxr["totearn"];
                          }

                          $tot_earn += $tot_earn_mo;
                          $etf3 = $tot_earn_mo * 0.03;

                          echo "<td align='right'>" . number_format($tot_earn_mo, 2) . "</td>";
                          echo "<td align='right'>" . number_format($etf3, 2) . "</td>";
                        }
                      }



                      echo "</tr>";
                    }


                    echo "<tr style='border-top: 2px solid black;'>";
                    echo "<td colspan='4' style='text-align: center'><b>PAGE TOTAL</b></td>";
                    echo "<td align='right'>" . number_format($totCons, 2) . "</td>";



                    for ($cc = $_GET["year"]; $cc <= $_GET["year2"]; $cc++) {
                      for ($c = $_GET["tomonth"]; $c <= $_GET["frommonth"]; $c++) {

                        $tot_earn_mo = 0;

                        $resalxr = Search("select sum(a.basic+a.br1+a.br2-a.nopay) as totearn,sum(a.etf3) as etfsum from salarycomplete a, user b where a.uid = b.uid and a.month = '" . $c . "' and a.year = '" . $cc . "' and b.isactive like '" . $_GET["emp_status"] . "'");

                        if ($resultalxr = mysqli_fetch_assoc($resalxr)) {

                          $tot_earn_month = $resultalxr["totearn"];
                          $tot_etf = $resultalxr["etfsum"];
                          echo "<td align='right'>" . number_format($tot_earn_month, 2) . "</td>";
                          echo "<td align='right'>" . number_format($tot_etf, 2) . "</td>";
                        } else {
                          $tot_earn_month = "0.00";
                          $tot_etf = "0.00";
                          echo "<td align='right'>" . number_format($tot_earn_month, 2) . "</td>";
                          echo "<td align='right'>" . number_format($tot_etf, 2) . "</td>";
                        }
                      }
                    }

                    echo "</tr>";
                    ?>


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

    echo "<input type='hidden' id='hmon' value='" . $_REQUEST["tomonth"] . "'>";
    echo "<input type='hidden' id='fmon' value='" . $_REQUEST["frommonth"] . "'>";
    echo "<input type='hidden' id='hyer' value='" . $_REQUEST["year"] . "'>";
    echo "<input type='hidden' id='hyer2' value='" . $_REQUEST["year2"] . "'>";
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