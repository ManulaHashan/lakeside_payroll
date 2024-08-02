<?php
error_reporting(0);
include("../Contains/header.php");
include '../DB/DB.php';
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Employee Details | Apex Payroll</title>
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

        if ($('#user').length !== 0) {
            $('#employee').val($('#user').val());
            $('#dept').val($('#depart').val());
            $('#pmethod').val($('#pmeth').val());
            $('#stat').val($('#pstat').val());
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

                                <table>
                                    <tr>
                                        <td>
                                            <h3>Employee Details Reporting<br /> <small>Genarate Employee Details
                                                    Report</small></h3>
                                        </td>
                                    </tr>
                                </table>

                                <table width="120%">
                                    <tr>
                                        <form action="#" method="get">
                                            <td width="100">&nbsp;</td>
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
                                            <td width="50">
                                                Department :
                                                <select id="dept" name="dept" class="select-basic"
                                                    style="width: 150px; height: 23px;">
                                                    <option value="%"></option>
                                                    <?php
                          $query = "select pid,name from position order by pid";
                          $res = Search($query);
                          while ($result = mysqli_fetch_assoc($res)) {
                          ?>
                                                    <option value="<?php echo $result["pid"]; ?>">
                                                        <?php echo $result["name"]; ?> </option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                            <td>&nbsp;</td>
                                            <td width="50">
                                                Payment method :
                                                <select id="pmethod" name="pmethod" class="select-basic"
                                                    style="width: 150px; height: 23px;">
                                                    <option value="%"></option>
                                                    <option value="0">Cash</option>
                                                    <option value="1">Bank Transfer</option>
                                            </td>
                                            <td>&nbsp;</td>
                                            <td width="50">
                                                Status :
                                                <select id="stat" name="stat" class="select-basic"
                                                    style="width: 150px; height: 23px;">
                                                    <option value="1">Active</option>
                                                    <option value="0">Not Active</option>
                                            </td>
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
                                <h3>Employee Details Summery</h3>
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
                            <div id="tdatax" style="overflow-x: scroll;">
                                <table border="1" width="90%" class="table table-bordered"
                                    style="border-collapse: collapse;">
                                    <thead>

                                        <tr>
                                            <th>EPF No.</th>
                                            <th>Employee's Name</th>
                                            <th>Calling Name</th>
                                            <th>National Idt. No</th>
                                            <th>Contact No (Mobile)</th>
                                            <th>Contact No (Land)</th>
                                            <th>Date of Birth</th>
                                            <th>Email</th>
                                            <th>Designation</th>
                                            <th>Registered Date</th>
                                            <th>Address</th>
                                            <th>Permanent Address</th>
                                            <th>Employee Type</th>
                                            <th>Job Code</th>
                                            <th>Department</th>
                                        </tr>

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

                    if ($_GET["pmethod"] == "0") {
                      $resalx = Search("select a.uid,a.fname as emp,a.mname,a.nic,a.epfno,a.tpno, a.lpno, a.dob, a.email, a.school, a.registerdDate, a.address, a.permanentAddress,a.EmployeeType_etid, a.jobcode,a.emppost_id,d.pid from user a, emppost c, position d where a.uid like '" . $_GET["employee"] . "' and c.position_pid = d.pid and a.emppost_id = c.id and d.pid like '" . $_GET["dept"] . "'  and  a.bankno = '' and a.isactive='" . $_GET["stat"] . "' and a.uid != '2' order by cast(a.epfno as unsigned) ASC");
                    } elseif ($_GET["pmethod"] == "1") {
                      $resalx = Search("select a.uid,a.fname as emp,a.mname,a.nic,a.epfno,a.tpno, a.lpno, a.dob, a.email, a.school, a.registerdDate, a.address, a.permanentAddress,a.EmployeeType_etid, a.jobcode,a.emppost_id,d.pid from user a, emppost c, position d where a.uid like '" . $_GET["employee"] . "' and c.position_pid = d.pid and a.emppost_id = c.id and d.pid like '" . $_GET["dept"] . "'  and  a.bankno != '' and a.isactive='" . $_GET["stat"] . "' and a.uid != '2' order by cast(a.epfno as unsigned) ASC");
                    } else {
                      $resalx = Search("select a.uid,a.fname as emp,a.mname,a.nic,a.epfno,a.tpno, a.lpno, a.dob, a.email, a.school, a.registerdDate, a.address, a.permanentAddress,a.EmployeeType_etid, a.jobcode,a.emppost_id,d.pid from user a, emppost c, position d where a.uid like '" . $_GET["employee"] . "' and c.position_pid = d.pid and a.emppost_id = c.id and d.pid like '" . $_GET["dept"] . "'  and  a.bankno like '%' and a.isactive='" . $_GET["stat"] . "' and a.uid != '2' order by cast(a.epfno as unsigned) ASC");
                    }





                    while ($resultalx = mysqli_fetch_assoc($resalx)) {

                      echo "<tr>";
                      echo "<td><center>" . $resultalx["epfno"] . "</center></td>";
                      echo "<td>" . $resultalx["emp"] . "</td>";
                      echo "<td>" . $resultalx["mname"] . "</td>";
                      echo "<td><center>" . $resultalx["nic"] . "</center></td>";
                      echo "<td><center>" . $resultalx["tpno"] . "</center></td>";
                      echo "<td><center>" . $resultalx["lpno"] . "</center></td>";
                      echo "<td><center>" . $resultalx["dob"] . "</center></td>";
                      echo "<td>" . $resultalx["email"] . "</td>";
                      echo "<td>" . $resultalx["school"] . "</td>";
                      echo "<td><center>" . $resultalx["registerdDate"] . "</center></td>";

                      $resalAdd = Search("select address as Address from address where aid='" . $resultalx["address"] . "'");

                      if ($resultalAdd = mysqli_fetch_assoc($resalAdd)) {

                        echo "<td>" . $resultalAdd["Address"] . "</td>";
                      } else {
                        echo "<td></td>";
                      }

                      $resalxPerAdd = Search("select address as permenent from address where aid='" . $resultalx["permanentAddress"] . "'");

                      if ($resultalPerAdd = mysqli_fetch_assoc($resalxPerAdd)) {

                        echo "<td>" . $resultalPerAdd["permenent"] . "</td>";
                      } else {
                        echo "<td></td>";
                      }

                      $resalxrEtype = Search("select name as emptype from employeetype where etid='" . $resultalx["EmployeeType_etid"] . "'");

                      if ($resultalxrEtype = mysqli_fetch_assoc($resalxrEtype)) {

                        echo "<td>" . $resultalxrEtype["emptype"] . "</td>";
                      } else {
                        echo "<td></td>";
                      }


                      echo "<td><center>" . $resultalx["jobcode"] . "</center></td>";


                      $resalxr = Search("select name as postname from position where pid='" . $resultalx["pid"] . "'");

                      if ($resultalxr = mysqli_fetch_assoc($resalxr)) {

                        echo "<td>" . $resultalxr["postname"] . "</td>";
                      } else {
                        echo "<td></td>";
                      }


                      echo "</tr>";
                    }


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

    echo "<input type='hidden' id='user' value='" . $_REQUEST["employee"] . "'>";
    echo "<input type='hidden' id='depart' value='" . $_REQUEST["dept"] . "'>";
    echo "<input type='hidden' id='pmeth' value='" . $_REQUEST["pmethod"] . "'>";
    echo "<input type='hidden' id='pstat' value='" . $_REQUEST["stat"] . "'>";
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