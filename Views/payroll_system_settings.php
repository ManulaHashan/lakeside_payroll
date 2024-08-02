<?php
error_reporting(0);
include("../Contains/header.php");
include '../DB/DB.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Payroll Times Settings | Apex Payroll</title>
    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="../Images/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../Images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../Images/favicon/favicon-16x16.png">
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
    <style>
    #camera_wrapper,
    #show_saved_img {
        float: left;
        width: 250px;
    }
    </style>

    <script type="text/javascript" src="../JS/webcam.js"></script>

    <script type="text/javascript">
    window.onload = function() {
        $('#loading').hide();
    };

    $(document).ready(function() {
        loadTable();
        checkSettingsData();
    });
    $(document).ajaxStart(function() {
        $('#loading').show();
    }).ajaxStop(function() {
        $('#loading').hide();
    });

    function loadTable() {

        var url = "../Controller/payroll_system_settings.php?request=getSettings";

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                $('#tdata').html(data);
            }
        });
    }

    function inputSettingsData() {

        var intime = $('#wrkintime').val();
        var outtime = $('#wrkouttime').val();
        var wrkLate = $('#wrkLate').val();
        var wrkOT = $('#wrkOT').val();
        var SatIntime = $('#wrkintimeSat').val();
        var SatOuttime = $('#wrkouttimeSat').val();
        var wrkEndLate = $('#wrkEndLate').val();
        var wrkEndOT = $('#wrkEndOT').val();
        var halfMIntime = $('#halfmorningstart').val();
        var halfMOuttime = $('#halfmorningend').val();
        var halfEIntime = $('#halfeveningstart').val();
        var halfEOuttime = $('#halfeveningend').val();
        var halfMLate = $('#halfMLate').val();
        var halfELate = $('#halfELate').val();
        var shortMIntime = $('#shortmorningstart').val();
        var shortMOuttime = $('#shortmorningend').val();
        var shortEIntime = $('#shorteveningstart').val();
        var shortEOuttime = $('#shorteveningend').val();
        var shrtMLate = $('#shrtMLate').val();
        var shrtELate = $('#shrtELate').val();

        var url = "../Controller/payroll_system_settings.php?request=SaveSettings&intime=" + intime + "&outtime=" +
            outtime + "&halfMIntime=" + halfMIntime + "&halfMOuttime=" + halfMOuttime + "&halfEIntime=" + halfEIntime +
            "&halfEOuttime=" + halfEOuttime + "&shortMIntime=" + shortMIntime + "&shortMOuttime=" + shortMOuttime +
            "&shortEIntime=" + shortEIntime + "&shortEOuttime=" + shortEOuttime + "&SatIntime=" + SatIntime +
            "&SatOuttime=" + SatOuttime + "&wrkLate=" + wrkLate + "&wrkOT=" + wrkOT + "&wrkEndLate=" + wrkEndLate +
            "&wrkEndOT=" + wrkEndOT + "&halfMLate=" + halfMLate + "&halfELate=" + halfELate + "&shrtMLate=" +
            shrtMLate + "&shrtELate=" + shrtELate;

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                alert(data);
                checkSettingsData();
                loadTable();
            }
        });
    }


    function updateSettingsData() {
        var SWTID = $('#swtid').val();
        var intime = $('#wrkintime').val();
        var outtime = $('#wrkouttime').val();
        var wrkLate = $('#wrkLate').val();
        var wrkOT = $('#wrkOT').val();
        var SatIntime = $('#wrkintimeSat').val();
        var SatOuttime = $('#wrkouttimeSat').val();
        var wrkEndLate = $('#wrkEndLate').val();
        var wrkEndOT = $('#wrkEndOT').val();
        var halfMIntime = $('#halfmorningstart').val();
        var halfMOuttime = $('#halfmorningend').val();
        var halfEIntime = $('#halfeveningstart').val();
        var halfEOuttime = $('#halfeveningend').val();
        var halfMLate = $('#halfMLate').val();
        var halfELate = $('#halfELate').val();
        var shortMIntime = $('#shortmorningstart').val();
        var shortMOuttime = $('#shortmorningend').val();
        var shortEIntime = $('#shorteveningstart').val();
        var shortEOuttime = $('#shorteveningend').val();
        var shrtMLate = $('#shrtMLate').val();
        var shrtELate = $('#shrtELate').val();


        if (SWTID == "") {
            alert("Please Select Record On Table!");
        } else {
            var url = "../Controller/payroll_system_settings.php?request=UpdateSettings&intime=" + intime +
                "&outtime=" + outtime + "&halfMIntime=" + halfMIntime + "&halfMOuttime=" + halfMOuttime +
                "&halfEIntime=" + halfEIntime + "&halfEOuttime=" + halfEOuttime + "&shortMIntime=" + shortMIntime +
                "&shortMOuttime=" + shortMOuttime + "&shortEIntime=" + shortEIntime + "&shortEOuttime=" +
                shortEOuttime + "&SWTID=" + SWTID + "&SatIntime=" + SatIntime + "&SatOuttime=" + SatOuttime +
                "&wrkLate=" + wrkLate + "&wrkOT=" + wrkOT + "&wrkEndLate=" + wrkEndLate + "&wrkEndOT=" + wrkEndOT +
                "&halfMLate=" + halfMLate + "&halfELate=" + halfELate + "&shrtMLate=" + shrtMLate + "&shrtELate=" +
                shrtELate;

            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    alert(data);
                    checkSettingsData();
                    loadTable();
                }
            });
        }


    }

    function selectSettingsData(workingTimeID) {

        var url = "../Controller/payroll_system_settings.php?request=SelectSettings&workingTimeID=" + workingTimeID;

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {

                var arr = data.split("#");
                $('#swtid').val(arr[0]);
                $('#wrkintime').val(arr[1]);
                $('#wrkouttime').val(arr[2]);

                var halfMorning = arr[3].split(" ");
                $('#halfmorningstart').val(halfMorning[0]);
                $('#halfmorningend').val(halfMorning[3]);

                var halfEvening = arr[4].split(" ");
                $('#halfeveningstart').val(halfEvening[0]);
                $('#halfeveningend').val(halfEvening[3]);

                var shortMorning = arr[5].split(" ");
                $('#shortmorningstart').val(shortMorning[0]);
                $('#shortmorningend').val(shortMorning[3]);

                var shortEvening = arr[6].split(" ");
                $('#shorteveningstart').val(shortEvening[0]);
                $('#shorteveningend').val(shortEvening[3]);

                $('#wrkintimeSat').val(arr[10]);
                $('#wrkouttimeSat').val(arr[11]);

                $('#wrkLate').val(arr[12]);
                $('#wrkOT').val(arr[13]);

                $('#wrkEndLate').val(arr[14]);
                $('#wrkEndOT').val(arr[15]);

                $('#halfMLate').val(arr[16]);
                $('#halfELate').val(arr[17]);

                $('#shrtMLate').val(arr[18]);
                $('#shrtELate').val(arr[19]);
            }
        });
    }

    function checkSettingsData() {

        var url = "../Controller/payroll_system_settings.php?request=CheckSettings";

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {

                if (data == "OK") {
                    document.getElementById("settingsAdd").disabled = true;
                    document.getElementById("settingsUpdate").disabled = false;
                } else {
                    document.getElementById("settingsUpdate").disabled = true;
                    document.getElementById("settingsAdd").disabled = false;
                }
            }
        });
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
                    <div class="col-sm-5 col-md-6">
                        <div class="row x_title">
                            <div class="col-md-6">
                                <h4>Set Working Times <small>Change Working Times</small></h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12" style="border-right: 1px solid black;">
                                <table>
                                    <tr style="border-bottom: 1px solid black;">
                                        <td>Working Time (Week Days)</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td height="35px;" width="200px;">
                                            <p class="form-label">Working In Time</p>
                                        </td>
                                        <td><input id="wrkintime" type="time" name="wrkintime" class="input-text"
                                                style="width: 182px"></td>
                                        <td height="35px;" width="200px;">
                                            <p class="form-label">Working Out Time</p>
                                        </td>
                                        <td><input id="wrkouttime" type="time" name="wrkouttime" class="input-text"
                                                style="width: 182px">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                    </tr>

                                    <tr style="border-bottom: 1px solid black;">
                                        <td>Late Calculate Time Working Time (Week Days)</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>OT Calculate Time For Working Time (Week Days)</td>
                                        <td>&nbsp;&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td height="35px;" width="200px;">
                                            <p class="form-label">Late Time</p>
                                        </td>
                                        <td><input id="wrkLate" type="time" name="wrkLate" class="input-text"
                                                style="width: 182px"></td>
                                        <td height="35px;" width="200px;">
                                            <p class="form-label">OT Time</p>
                                        </td>
                                        <td><input id="wrkOT" type="time" name="wrkOT" class="input-text"
                                                style="width: 182px">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                    </tr>

                                    <tr style="border-bottom: 1px solid black;">
                                        <td>Working Time (Weekends)</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td height="35px;" width="200px;">
                                            <p class="form-label">Working In Time</p>
                                        </td>
                                        <td><input id="wrkintimeSat" type="time" name="wrkintimeSat" class="input-text"
                                                style="width: 182px"></td>
                                        <td height="35px;" width="200px;">
                                            <p class="form-label">Working Out Time</p>
                                        </td>
                                        <td><input id="wrkouttimeSat" type="time" name="wrkouttimeSat"
                                                class="input-text" style="width: 182px">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                    </tr>

                                    <tr style="border-bottom: 1px solid black;">
                                        <td>Late Calculate Time Working Time (Weekends)</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>OT Calculate Time For Working Time (Weekends)</td>
                                        <td>&nbsp;&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td height="35px;" width="200px;">
                                            <p class="form-label">Late Time</p>
                                        </td>
                                        <td><input id="wrkEndLate" type="time" name="wrkEndLate" class="input-text"
                                                style="width: 182px"></td>
                                        <td height="35px;" width="200px;">
                                            <p class="form-label">OT Time</p>
                                        </td>
                                        <td><input id="wrkEndOT" type="time" name="wrkEndOT" class="input-text"
                                                style="width: 182px">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                    </tr>

                                    <tr style="border-bottom: 1px solid black;">
                                        <td>Half Day Time</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                    </tr>

                                    <tr>
                                        <td height="35px;" width="200px;">
                                            <p class="form-label">Half Day Slot Start (Morning)</p>
                                        </td>
                                        <td><input id="halfmorningstart" type="time" name="halfmorningstart"
                                                class="input-text" style="width: 182px"></td>
                                        <td height="35px;" width="200px;">
                                            <p class="form-label">Half Day Slot End (Morning)</p>
                                        </td>
                                        <td><input id="halfmorningend" type="time" name="halfmorningend"
                                                class="input-text" style="width: 182px"></td>
                                    </tr>

                                    <tr>
                                        <td height="35px;" width="200px;">
                                            <p class="form-label">Half Day Slot Start (Evening)</p>
                                        </td>
                                        <td><input id="halfeveningstart" type="time" name="halfeveningstart"
                                                class="input-text" style="width: 182px"></td>
                                        <td height="35px;" width="200px;">
                                            <p class="form-label">Half Day Slot End (Evening)</p>
                                        </td>
                                        <td><input id="halfeveningend" type="time" name="halfeveningend"
                                                class="input-text" style="width: 182px"></td>
                                    </tr>


                                    <tr>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                    </tr>

                                    <tr style="border-bottom: 1px solid black;">
                                        <td>Late Calculate Time For Halfday Morning Slot</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>Late Calculate Time For Halfday Evening Slot</td>
                                        <td>&nbsp;&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td height="35px;" width="200px;">
                                            <p class="form-label">Late Time</p>
                                        </td>
                                        <td><input id="halfMLate" type="time" name="halfMLate" class="input-text"
                                                style="width: 182px"></td>
                                        <td height="35px;" width="200px;">
                                            <p class="form-label">Late Time</p>
                                        </td>
                                        <td><input id="halfELate" type="time" name="halfELate" class="input-text"
                                                style="width: 182px">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                    </tr>

                                    <tr style="border-bottom: 1px solid black;">
                                        <td>Short Leave Time</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                    </tr>


                                    <tr>
                                        <td height="35px;" width="200px;">
                                            <p class="form-label">Short Leave Slot Start (Evening)</p>
                                        </td>
                                        <td><input id="shortmorningstart" type="time" name="shortmorningstart"
                                                class="input-text" style="width: 182px"></td>
                                        <td height="35px;" width="200px;">
                                            <p class="form-label">Short Leave Slot End (Evening)</p>
                                        </td>
                                        <td><input id="shortmorningend" type="time" name="shortmorningend"
                                                class="input-text" style="width: 182px"></td>
                                    </tr>

                                    <tr>
                                        <td height="35px;" width="200px;">
                                            <p class="form-label">Short Leave Slot Start (Evening)</p>
                                        </td>
                                        <td><input id="shorteveningstart" type="time" name="shorteveningstart"
                                                class="input-text" style="width: 182px"></td>
                                        <td height="35px;" width="200px;">
                                            <p class="form-label">Short Leave Slot End (Evening)</p>
                                        </td>
                                        <td><input id="shorteveningend" type="time" name="shorteveningend"
                                                class="input-text" style="width: 182px"></td>
                                    </tr>

                                    <tr>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                    </tr>

                                    <tr style="border-bottom: 1px solid black;">
                                        <td>Late Calculate Time For Short Leave Morning Slot</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>Late Calculate Time For Short Leave Evening Slot</td>
                                        <td>&nbsp;&nbsp;</td>
                                    </tr>
                                    <tr>
                                        <td height="35px;" width="200px;">
                                            <p class="form-label">Late Time</p>
                                        </td>
                                        <td><input id="shrtMLate" type="time" name="shrtMLate" class="input-text"
                                                style="width: 182px"></td>
                                        <td height="35px;" width="200px;">
                                            <p class="form-label">Late Time</p>
                                        </td>
                                        <td><input id="shrtELate" type="time" name="shrtELate" class="input-text"
                                                style="width: 182px">
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                        <td>&nbsp;&nbsp;</td>
                                    </tr>

                                    <tr>
                                        <td><button style="margin-top: 10px; width: 150px;" value="Save"
                                                id="settingsAdd" onclick="inputSettingsData()"
                                                class="btn btn-success">Save</button></td>
                                        <td><button style="margin-top: 10px; width: 150px;" value="Update"
                                                id="settingsUpdate" onclick="updateSettingsData()"
                                                class="btn btn-warning">Update</button></td>
                                        <td><button class="btn btn-dark" style="margin-top: 10px; width: 150px;"
                                                onClick="window.location.reload();">Clear</button></td>
                                        <td>&nbsp;&nbsp;</td>
                                    </tr>

                                </table>
                                <input id="swtid" type="text" name="swtid" class="input-text" hidden="hidden">
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-5 offset-sm-2 col-md-6 offset-md-0">
                        <div class="row x_title">
                            <div class="col-md-6">
                                <h4>Working Time Table<small> View Current Working Times</small></h4>
                            </div>
                        </div>
                        <div id="tdata" style="overflow-y: scroll;width: 1500px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
<?php include("../Contains/footer.php"); 
?>

</html>