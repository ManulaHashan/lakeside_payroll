<?php
error_reporting(0);
include("../Contains/header.php");
include '../DB/DB.php';
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Daily Attendance Summery | Apex Payroll</title>
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

        document.getElementById('todate').valueAsDate = new Date();
        document.getElementById('fromdate').valueAsDate = new Date();

        if ($('#hmon').length !== 0) {
            $('#todate').val($('#hmon').val());
            $('#fromdate').val($('#hyer').val());
            $('#emptype').val($('#etype').val());
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
                'filename': 'Daily_Attendance_Summery'
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

                                <table width="120%">
                                    <tr>
                                        <form action="#" method="get">
                                            <td>
                                                <h3>Daily Attendance Summery<br /> <small>Genarate Attendance
                                                        Summery</small></h3>
                                            </td>
                                            <!-- <td width="100">&nbsp;</td> -->
                                            <td width="50">
                                                To Date :
                                                <input type="date" name="todate" id="todate" class="input-text"
                                                    style="width: 100px;">
                                            </td>
                                            <td>&nbsp;</td>
                                            <td width="50">
                                                From Date :
                                                <input type="date" name="fromdate" id="fromdate" class="input-text"
                                                    style="width: 100px;">
                                            </td>
                                            <td>&nbsp;</td>
                                            <td width="50">
                                                Employee Type:
                                                <select id="emptype" name="emptype" class="select-basic"
                                                    style="width: 120px; height: 26px;">
                                                    <option value="%"></option>
                                                    <?php
                          $query = "select etid,name from employeetype";
                          $res = Search($query);
                          while ($result = mysqli_fetch_assoc($res)) {
                          ?>
                                                    <option value="<?php echo $result["etid"]; ?>">
                                                        <?php echo $result["name"]; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td>&nbsp;</td>
                                            <td></br>&nbsp;&nbsp;<input type="submit" name="submit" value="Generate"
                                                    class="btn btn-primary"></td>
                                            <td></br>&nbsp;<input type="button" value="Print Report"
                                                    class="btn btn-success" onclick="print()"></td>
                                            <td></br>&nbsp;<input type="button" value="Export Excel"
                                                    class="btn btn-success" onclick="exportExcel()"></td>
                                        </form>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <?php

            $EmpType = "";

            if ($_GET["emptype"] == "%") {
              $EmpType = "All";
            } else {
              $resalEmp = Search("select name from employeetype where etid like '" . $_GET["emptype"] . "'");

              if ($resultEmp = mysqli_fetch_assoc($resalEmp)) {
                $EmpType = $resultEmp["name"];
              }
            }
            ?>


                        <div id="report">

                            <center>
                                <h3>Attendance Summery</h3>
                                <p>To Date : <?php echo $_GET["todate"]; ?> | From Date :
                                    <?php echo $_GET["fromdate"]; ?> | Employee Type : <?php echo $EmpType; ?> </p>
                            </center>
                            <hr />
                            <div>
                                <h4 style="float: right;">Printed Date : <?php echo date("Y/m/d"); ?>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h4>
                            </div>

                            </br>


                            <br>
                            <div id="tdatax" style="width:90%;">
                                <table border="1" class="table table-bordered" style="border-collapse: collapse;">
                                    <thead>

                                        <tr>
                                            <th colspan="3"></th>
                                            <?php


                      for ($a = date('m', strtotime($_GET["todate"])); $a <= date('m', strtotime($_GET["fromdate"])); $a++) {

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

                        $row = 0;
                        $resalxry = Search("select date from attendance where  MONTH(date) = '" . $a . "' and date between '" . $_GET["todate"] . "' and '" . $_GET["fromdate"] . "' group by date");

                        while ($resultalxry = mysqli_fetch_assoc($resalxry)) {

                          $row++;
                        }

                        $A = $row * 2;


                        echo "<th colspan='" . $A . "' style='text-align: center'>" . $monthname . "</th>";
                      }

                      ?>
                                            <th></th>
                                        </tr>

                                        <tr>
                                            <th colspan="3"></th>
                                            <?php

                      $resalxry = Search("select date from attendance where date between '" . $_GET["todate"] . "' and '" . $_GET["fromdate"] . "' group by date");

                      while ($resultalxry = mysqli_fetch_assoc($resalxry)) {

                        $att_date = $resultalxry["date"];
                        echo "<th colspan='2'><center>" . date('d', strtotime($att_date)) . "</center></th>";
                      }

                      ?>
                                            <th></th>
                                        </tr>

                                        <tr>
                                            <th>No</th>
                                            <th>Employee's Name</th>
                                            <th>EPF No</th>
                                            <?php

                      $resalxry = Search("select date from attendance where date between '" . $_GET["todate"] . "' and '" . $_GET["fromdate"] . "' group by date");

                      while ($resultalxry = mysqli_fetch_assoc($resalxry)) {

                        echo "<th>In Time</th>";
                        echo "<th>Out Time</th>";
                      }

                      ?>
                                            <th>Attendance Count</th>
                                        </tr>

                                    </thead>
                                    <tbody>
                                        <?php

                    $no = 0;
                    $tot_att_count = 0;

                    $employerNO = "123456";

                    $month = $_GET["month"];
                    if (count($_GET["month"]) == 1) {
                      $month = "0" . $month;
                    }




                    $resalx = Search("select uid,fname as emp,epfno from user where isactive='1' and EmployeeType_etid like '" . $_GET["emptype"] . "' order by uid");
                    while ($resultalx = mysqli_fetch_assoc($resalx)) {

                      echo "<tr>";
                      echo "<td><center>" . $no . "<center></td>";
                      echo "<td>" . $resultalx["emp"] . "</td>";
                      echo "<td><center>" . $resultalx["epfno"] . "<center></td>";

                      for ($b = date('m', strtotime($_GET["todate"])); $b <= date('m', strtotime($_GET["fromdate"])); $b++) {

                        $resalxry = Search("select date from attendance where MONTH(date) = '" . $b . "' and date between '" . $_GET["todate"] . "' and '" . $_GET["fromdate"] . "' group by date");

                        while ($resultalxry = mysqli_fetch_assoc($resalxry)) {

                          $att_date = $resultalxry["date"];

                          $resalxrT = Search("select intime,outtime from attendance where  MONTH(date) = '" . $b . "' and YEAR(date) = '" . date('Y', strtotime($_GET["todate"])) . "' and DAY(date) = '" . date('d', strtotime($att_date)) . "' and User_uid = '" . $resultalx["uid"] . "'");
                          if ($resultalxrT = mysqli_fetch_assoc($resalxrT)) {
                            echo "<td>" . $resultalxrT["intime"] . "</td>";
                            echo "<td>" . $resultalxrT["outtime"] . "</td>";
                          } else {
                            echo "<td></td>";
                            echo "<td></td>";
                          }
                        }
                      }

                      $resalxry = Search("select count(aid) as attcount from attendance where User_uid = '" . $resultalx["uid"] . "' and date between '" . $_GET["todate"] . "' and '" . $_GET["fromdate"] . "'");

                      if ($resultalxry = mysqli_fetch_assoc($resalxry)) {

                        $att = $resultalxry["attcount"];
                        echo "<td><center>" . $resultalxry["attcount"] . "</center></td>";
                      } else {
                        $att = 0;
                        echo "<td></td>";
                      }

                      echo "</tr>";

                      $no++;
                      $tot_att_count += $att;
                    }

                    echo "<tr style='border-top: 2px solid black;'>";
                    $B = 0;
                    for ($a = date('m', strtotime($_GET["todate"])); $a <= date('m', strtotime($_GET["fromdate"])); $a++) {
                      $row = 0;
                      $resalxry = Search("select date from attendance where  MONTH(date) = '" . $a . "' and date between '" . $_GET["todate"] . "' and '" . $_GET["fromdate"] . "' group by date");

                      while ($resultalxry = mysqli_fetch_assoc($resalxry)) {

                        $row++;
                      }

                      $A = $row * 2;
                      $B += $A;
                    }

                    $C = $B + 3;
                    echo "<td style='text-align: right' colspan='" . $C . "'><b>Total</b></td>";
                    echo "<td style='text-align: center'>" . number_format($tot_att_count) . "</td>";
                    echo "</tr>";
                    ?>


                                    </tbody>
                                </table>
                            </div>
                            </br>

                        </div>

                    </div>



                </div>

            </div>
        </div>
    </div>

    <?php
  if (isset($_REQUEST["submit"])) {

    echo "<input type='hidden' id='hmon' value='" . $_REQUEST["todate"] . "'>";
    echo "<input type='hidden' id='hyer' value='" . $_REQUEST["fromdate"] . "'>";
    echo "<input type='hidden' id='etype' value='" . $_REQUEST["emptype"] . "'>";
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