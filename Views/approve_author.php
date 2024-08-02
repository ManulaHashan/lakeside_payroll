<?php
error_reporting(0);
// include("../Contains/header_approve_leave.php");
include("../Contains/header.php");
include '../DB/DB.php';
// if (isset($_GET["UID"])) {
//     $UID_DATA = $_GET["UID"];
//     $EMP_DATA = $_GET["EMPID"];
//   }
$UID_DATA = $_SESSION["uid"];
$EMP_DATA = $_SESSION["uid"];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Employee Leave Approve | Apex Payroll</title>
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
    <style>
    #camera_wrapper,
    #show_saved_img {
        float: left;
        width: 250px;
    }
    </style>

    <script type="text/javascript" src="../JS/webcam.js"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
    <script type="text/javascript">
    (function() {
        emailjs.init("J8hxLdsmtgm0e-uFJ");
    })();
    </script>

    <script type="text/javascript">
    window.onload = function() {
        $('#loading').hide();
    };

    $(document).ready(function() {
        loadTable();
    });
    $(document).ajaxStart(function() {
        $('#loading').show();
    }).ajaxStop(function() {
        $('#loading').hide();
    });

    function loadTable() {
        var uID = document.getElementById('useridData').value;
        var empID = document.getElementById('empidData').value;

        var url = "../Controller/approve_author.php?request=getleave&uID=" + uID + "&empID=" + empID;


        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                $('#tdata').html(data);
            }
        });
    }

    function loadDataForApprove(id) {
        $("#elid").val(id);
        var uID = document.getElementById('useridData').value;

        var url = "../Controller/approve_author.php?request=getApproveData&eid=" + id + "&Auth=" + uID;

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                loadTable();
            }
        });
    }

    function loadDataForDecline(id) {
        $("#elid").val(id);
        var uID = document.getElementById('useridData').value;

        var url = "../Controller/approve_author.php?request=getDeclineData&eid=" + id + "&Auth=" + uID;

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                loadTable();
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
                                <h3>Approve Requested Leave</h3>
                            </div>
                        </div>
                        <input id="elid" type="text" name="elid" class="input-text" hidden="hidden">
                        <input id="useridData" type="text" name="useridData" class="input-text"
                            value="<?php echo $UID_DATA; ?>" hidden="hidden">
                        <input id="empidData" type="text" name="empidData" class="input-text"
                            value="<?php echo $EMP_DATA; ?>" hidden="hidden">
                        <p>View & Approve Leave Request</p>
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