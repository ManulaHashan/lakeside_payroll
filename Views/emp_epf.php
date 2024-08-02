<?php
error_reporting(0);
include("../Contains/header.php");
include '../DB/DB.php';
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>EPF Report | Apex Payroll</title>
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

    var totrem = 0.00;

    function CalRemitance() {
        var cont = $("#contri").html();
        var serch = $("#surchrg").html();

        if (serch == "") {
            serch = 0;
        }

        totrem = parseFloat(cont) - parseFloat(serch);

        $("#remit").html(totrem.toFixed(2));

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

                                <table width="120%">
                                    <tr>
                                        <form action="#" method="get">
                                            <td>
                                                <h3>EPF Reporting<br /> <small>Genarate EPF Report</small></h3>
                                            </td>
                                            <td width="100">&nbsp;</td>
                                            <td width="50">
                                                Month :
                                                <select id="month" name="month" class="select-basic"
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
                                <h3>EPF Report</h3>
                                <p>Month : <?php echo $_GET["month"]; ?> | Year : <?php echo $_GET["year"]; ?> </p>
                            </center>
                            <hr />
                            <div>
                                <h4 style="float: right;">Printed Date : <?php echo date("Y/m/d"); ?>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h4>
                            </div>

                            </br>

                            <table width="90%">
                                <tr>
                                    <td>
                                        <table border="1" class="table table-bordered"
                                            style="border-collapse: collapse; width:70%;">
                                            <tr>
                                                <td><b>Derana Medical Laboratory Testing Services (Pvt) Ltd.</b><br>
                                                    No. 553/A, Madiwela Road,<br>
                                                    Thalawathugoda.
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Ref No :</td>
                                            </tr>
                                        </table>
                                    </td>

                                    <?php

                  $tot_cont = 0.00;

                  // $restotcont = Search("select sum(epf+epf12) as totcons from salarycomplete where month= '" . $_GET["month"] . "' and year = '" . $_GET["year"] . "'");

                  $restotcont = Search("select sum(salcom.epf+salcom.epf12) as totcons from salarycomplete salcom,user u where u.uid=salcom.uid and salcom.month= '" . $_GET["month"] . "' and salcom.year = '" . $_GET["year"] . "' and u.isactive like '" . $_GET["emp_status"] . "'"); //New Dev 2023-11-08


                  if ($resultcont = mysqli_fetch_assoc($restotcont)) {

                    $tot_cont = $resultcont["totcons"];
                  } else {
                    $tot_cont = 0.00;
                  }

                  ?>

                                    <td align="right">
                                        <table border="1" class="table table-bordered"
                                            style="border-collapse: collapse; width: 70%;">
                                            <tr>
                                                <th>
                                                    <h3>C Form</h3>
                                                </th>
                                                <th>
                                                    <h4>EPF Act No 15 of 1958</h4>
                                                </th>
                                            </tr>
                                            <tr>
                                                <td>E.P.F. Registration No.</td>
                                                <td>B - 40401</td>
                                            </tr>
                                            <tr>
                                                <td>Month & Year of Contribution</td>
                                                <td><?php echo $_GET["month"]; ?> - <?php echo $_GET["year"]; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Contributions Rs.</td>
                                                <td id="contri"><?php echo round($tot_cont, 2); ?></td>
                                            </tr>
                                            <tr>
                                                <td>Surcharges Rs</td>
                                                <td contenteditable="true" id="surchrg" onkeyup="CalRemitance()">0.00
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Total Remittance Rs.</td>
                                                <td id="remit">0.00</td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td align="right">Cheque No</td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td align="right">Bank Name</td>
                                                <td></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <br>
                            <div id="tdatax" style="width:90%;">
                                <table border="1" class="table table-bordered" style="border-collapse: collapse;">
                                    <thead>
                                        <tr>
                                            <th>Employee's Name</th>
                                            <th>NIC No</th>
                                            <th>EPF No.</th>
                                            <th>Total</th>
                                            <th>
                                                <center>Employer 12%</center>
                                            </th>
                                            <th>
                                                <center>Employee 8%</center>
                                            </th>
                                            <th>Total Earnings</th>
                                        </tr>

                                        <!--  <tr>
                   <th>Member Status E=Extg. N=New</th>
                <th>Zone</th>
                <th>Employer Number</th>
                <th>Contribution Period (YYYYMM)</th>
                <th>Date Submission Number</th>
                <th>No.of days worked</th>
                
                <th>Occupation Classification Grade</th>
                </tr> -->

                                    </thead>
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




                    $resalx = Search("select u.uid,u.fname as emp,u.nic,u.epfno from user u,salarycomplete sal where u.uid = sal.uid and u.isactive like '" . $_GET["emp_status"] . "' group by sal.uid order by cast(u.epfno as unsigned) ASC");
                    while ($resultalx = mysqli_fetch_assoc($resalx)) {

                      echo "<tr>";
                      echo "<td>" . $resultalx["emp"] . "</td>";
                      echo "<td><center>" . $resultalx["nic"] . "<center></td>";
                      echo "<td><center>" . $resultalx["epfno"] . "<center></td>";

                      $resalxr = Search("select a.* from salarycomplete a, user b where a.uid = '" . $resultalx["uid"] . "' and a.uid = b.uid and a.month = '" . $_GET["month"] . "' and a.year = '" . $_GET["year"] . "'");

                      if ($resultalxr = mysqli_fetch_assoc($resalxr)) {

                        $empTOT = $resultalxr["basic"] + $resultalxr["br1"] + $resultalxr["br2"] - $resultalxr["nopay"];
                        $Emp12 = $resultalxr["epf12"];
                        $Emp8 = $resultalxr["epf"];
                        $Totemp = $resultalxr["epf12"] + $resultalxr["epf"];

                        $tot_earn += $Totemp;
                        $epf8 += $Emp8;
                        $epf12 += $Emp12;
                        $totEM += $empTOT;

                        echo "<td style='text-align: right'>" . number_format($Totemp + $empTOT, 2) . "</td>";
                        echo "<td style='text-align: right'>" . number_format($Emp12, 2) . "</td>";
                        echo "<td style='text-align: right'>" . number_format($Emp8, 2) . "</td>";
                        echo "<td style='text-align: right'>" . number_format($empTOT, 2) . "</td>";
                      } else {
                        echo "<td style='text-align: right'>0.00</td>";
                        echo "<td style='text-align: right'>0.00</td>";
                        echo "<td style='text-align: right'>0.00</td>";
                        echo "<td style='text-align: right'>0.00</td>";
                      }

                      echo "</tr>";
                    }

                    echo "<tr style='border-top: 2px solid black;'>";
                    echo "<td colspan='2'></td>";
                    echo "<td style='text-align: right'><b>Total Amount</b></td>";
                    echo "<td style='text-align: right'>" . number_format($tot_earn + $totEM, 2) . "</td>";
                    echo "<td style='text-align: right'>" . number_format($epf12, 2) . "</td>";
                    echo "<td style='text-align: right'>" . number_format($epf8, 2) . "</td>";
                    echo "<td style='text-align: right'>" . number_format($totEM, 2) . "</td>";
                    echo "</tr>";
                    ?>


                                    </tbody>
                                </table>
                                </br>
                                <h2><u>Summery</u></h2>
                                </br>

                                <table border="1" class="table table-bordered"
                                    style="border-collapse: collapse; width:40%;">
                                    <tr>
                                        <th></th>
                                        <th>Total Amount</th>
                                        <th>
                                            <center>Employer 12%</center>
                                        </th>
                                        <th>
                                            <center>Employee 8%</center>
                                        </th>
                                        <th>Total Earnings</th>
                                    </tr>
                                    <?php
                  echo "<tr>";
                  echo "<td><b>TOTAL</b></td>";
                  echo "<td style='text-align: right'>" . number_format($tot_earn + $totEM, 2) . "</td>";
                  echo "<td style='text-align: right'>" . number_format($epf12, 2) . "</td>";
                  echo "<td style='text-align: right'>" . number_format($epf8, 2) . "</td>";
                  echo "<td style='text-align: right'>" . number_format($totEM, 2) . "</td>";
                  echo "</tr>";

                  ?>
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

    echo "<input type='hidden' id='hmon' value='" . $_REQUEST["month"] . "'>";
    echo "<input type='hidden' id='hyer' value='" . $_REQUEST["year"] . "'>";
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