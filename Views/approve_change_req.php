<?php
error_reporting(0);
// include("../Contains/header_approve_leave.php");
include("../Contains/header.php");
include '../DB/DB.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Employee Details Change Request Handle | Apex Payroll</title>
    <link href="../Styles/Stylie.css" rel="stylesheet" type="text/css">
    <link href="../Styles/contains.css" rel="stylesheet" type="text/css">
    <script src="../JS/jquery-3.1.0.js"></script>
    <script src="../JS/photobooth_min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
    <link href="../Vendor/css/sweet-alert.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/nprogress/nprogress.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/animate.css/animate.min.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/css/custom.min.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/iCheck/skins/flat/green.css" rel="stylesheet" type="text/css">
    <script src="https://smtpjs.com/v3/smtp.js"></script>
    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="../Images/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../Images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../Images/favicon/favicon-16x16.png">

    <script type="text/javascript">
    window.onload = function() {
        $('#loading').hide();
        var date = new Date();
        document.getElementById('fromdate').valueAsDate = date;
        document.getElementById('todate').valueAsDate = date;
        loadTable();
    };

    $(document).ajaxStart(function() {
        $('#loading').show();
    }).ajaxStop(function() {
        $('#loading').hide();
    });

    function loadTable() {
        var from = document.getElementById('fromdate').value;
        var to = document.getElementById('todate').value;
        var decision = document.getElementById('lvdes').value;

        var url = "../Controller/approve_change_req.php?request=getreq&fromDate=" + from + "&toDate=" + to + "&des=" +
            decision;
        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                $('#tdata').html(data);
            }
        });
    }

    function loadDataForApprove(id) {
        var url = "../Controller/approve_change_req.php?request=getApproveData&reqid=" + id;

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {

                if (data == "1") {
                    alert("Request approved successfully!");
                    loadTable();
                } else {
                    alert("Error!");
                }

            }
        });
    }

    function loadDataForDecline(id) {
        var url = "../Controller/approve_change_req.php?request=getDeclineData&reqid=" + id;

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {

                if (data == "1") {
                    alert("Request declined successfully!");
                    loadTable();
                } else {
                    alert("Error!");
                }
            }
        });
    }
    </script>
</head>

<body id="body" class="nav-md" style="background-color: white;">
    <!-- <?php include("../Contains/titlebar_approve_leave.php"); ?> -->
    <?php include("../Contains/titlebar_dboard.php"); ?>
    <div class="container body">
        <div class="main_container">
            <!-- page content -->
            <div class="" style="width: 100%; margin: 1%;" role="main">


                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">


                        <div class="row x_title">
                            <div class="col-md-6">
                                <h3>Employee Details Change Request Handle</h3>
                            </div>
                        </div>
                        <p>View & Approve Request</p>
                        <table>
                            <tr>
                                <td>From Date :&nbsp;</td>
                                <td><input id="fromdate" type="date" class="input-text" style="width: 182px"></td>
                                <td>&nbsp;&nbsp;To Date :&nbsp;</td>
                                <td><input id="todate" type="date" class="input-text" style="width: 182px"></td>
                                <td>&nbsp;&nbsp;Decision :&nbsp;</td>
                                <td><select id="lvdes" class="select-basic" style="width: 182px">
                                        <option value="0">Pending</option>
                                        <option value="1">Approved</option>
                                        <option value="2">Declined</option>
                                        <option value="3">Task Completed</option>
                                    </select>
                                </td>
                                <td><button class="btn btn-primary" onclick="loadTable()">Search</button></td>
                                <td>Decision Pending :&nbsp;&nbsp;<input type="text"
                                        style="width: 40px; background-color: #DAA520; border: none;"
                                        readonly="true">&nbsp;&nbsp;</td>
                                <td>Request Approved :&nbsp;&nbsp;<input type="text"
                                        style="width: 40px; background-color: #47b833; border: none;"
                                        readonly="true">&nbsp;&nbsp;</td>
                                <td>Request Declined :&nbsp;&nbsp;<input type="text"
                                        style="width: 40px; background-color: #ff0000; border: none;"
                                        readonly="true">&nbsp;&nbsp;</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <br>
                <div id="tdata" style="height: 476px; overflow-y: scroll;width: 1500px;"></div>
            </div>
        </div>
    </div>
</body>
<?php include("../Contains/footer.php");
?>

</html>