<?php
error_reporting(0);
include("../Contains/header.php");
include '../DB/DB.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Roster Working Profile | Apex Payroll</title>
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

        var url = "../Controller/emp_shift_profile.php?request=getSettings";

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                $('#tdata').html(data);
            }
        });
    }

    function inputSettingsData() {
        var code = $('#wrkCode').val();
        var name = $('#wrkName').val();
        var intime = $('#wrkintime').val();
        var outtime = $('#wrkouttime').val();
        var wrkLate = $('#wrkLate').val();
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
        var clrCode = encodeURIComponent($('#favcolor').val());

        if (name == "") {
            alert("Please add the roster name!");
        } else {
            var url = "../Controller/emp_shift_profile.php?request=SaveSettings&intime=" + intime + "&outtime=" +
                outtime + "&halfMIntime=" + halfMIntime + "&halfMOuttime=" + halfMOuttime + "&halfEIntime=" +
                halfEIntime + "&halfEOuttime=" + halfEOuttime + "&shortMIntime=" + shortMIntime + "&shortMOuttime=" +
                shortMOuttime + "&shortEIntime=" + shortEIntime + "&shortEOuttime=" + shortEOuttime + "&wrkLate=" +
                wrkLate + "&halfMLate=" + halfMLate + "&halfELate=" + halfELate + "&shrtMLate=" + shrtMLate +
                "&shrtELate=" + shrtELate + "&wrkCode=" + code + "&wrkname=" + name + "&clrcode=" + clrCode;

            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    alert(data);
                    $('#wrkName').val("");
                    checkSettingsData();
                    loadTable();
                }
            });
        }
    }


    function updateSettingsData() {
        var SWTID = $('#swtid').val();
        var code = $('#wrkCode').val();
        var name = $('#wrkName').val();
        var intime = $('#wrkintime').val();
        var outtime = $('#wrkouttime').val();
        var wrkLate = $('#wrkLate').val();
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
        var clrCode = encodeURIComponent($('#favcolor').val());

        if (SWTID == "") {
            alert("Please Select Record On Table!");
        } else {
            var url = "../Controller/emp_shift_profile.php?request=UpdateSettings&intime=" + intime + "&outtime=" +
                outtime + "&halfMIntime=" + halfMIntime + "&halfMOuttime=" + halfMOuttime + "&halfEIntime=" +
                halfEIntime + "&halfEOuttime=" + halfEOuttime + "&shortMIntime=" + shortMIntime + "&shortMOuttime=" +
                shortMOuttime + "&shortEIntime=" + shortEIntime + "&shortEOuttime=" + shortEOuttime + "&wrkLate=" +
                wrkLate + "&halfMLate=" + halfMLate + "&halfELate=" + halfELate + "&shrtMLate=" + shrtMLate +
                "&shrtELate=" + shrtELate + "&wrkname=" + name + "&SWTID=" + SWTID + "&clrcode=" + clrCode;

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

        var url = "../Controller/emp_shift_profile.php?request=SelectSettings&workingTimeID=" + workingTimeID;

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {

                var arr = data.split("@");
                $('#swtid').val(arr[0]);
                $('#wrkName').val(arr[1]);
                $('#wrkCode').val(arr[14]);

                if (arr[2] == "00:00:00" || arr[2] == "") {
                    $('#wrkintime').val("");
                } else {
                    $('#wrkintime').val(arr[2]);
                }


                if (arr[3] == "00:00:00" || arr[3] == "") {
                    $('#wrkouttime').val("");
                } else {
                    $('#wrkouttime').val(arr[3]);
                }

                var halfMorning = arr[4].split(" ");
                $('#halfmorningstart').val(halfMorning[0]);
                $('#halfmorningend').val(halfMorning[3]);

                var halfEvening = arr[5].split(" ");
                $('#halfeveningstart').val(halfEvening[0]);
                $('#halfeveningend').val(halfEvening[3]);

                var shortMorning = arr[6].split(" ");
                $('#shortmorningstart').val(shortMorning[0]);
                $('#shortmorningend').val(shortMorning[3]);

                var shortEvening = arr[7].split(" ");
                $('#shorteveningstart').val(shortEvening[0]);
                $('#shorteveningend').val(shortEvening[3]);

                if (arr[8] == "00:00:00" || arr[8] == "") {
                    $('#wrkLate').val("");
                } else {
                    $('#wrkLate').val(arr[8]);
                }


                if (arr[9] == "00:00:00" || arr[9] == "") {
                    $('#halfMLate').val("");
                } else {
                    $('#halfMLate').val(arr[9]);
                }


                if (arr[10] == "00:00:00" || arr[10] == "") {
                    $('#halfELate').val("");
                } else {
                    $('#halfELate').val(arr[10]);
                }


                if (arr[11] == "00:00:00" || arr[11] == "") {
                    $('#shrtMLate').val("");
                } else {
                    $('#shrtMLate').val(arr[11]);
                }


                if (arr[12] == "00:00:00" || arr[12] == "") {
                    $('#shrtELate').val("");
                } else {
                    $('#shrtELate').val(arr[12]);
                }

                $('#favcolor').val(arr[13]);

                checkSettingsData();
            }
        });
    }

    function checkSettingsData() {

        var url = "../Controller/emp_shift_profile.php?request=CheckSettings&WRKNAME=" + $('#wrkName').val();

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

                <div class="row x_title">
                    <div class="col-md-10">
                        <h4>New Roster Profile <small>Create Roster Times</small></h4>
                    </div>
                </div>

                <div class="row">
                    <table>
                        <tr style="border-bottom: 1px solid black;">
                            <td>Working Time</td>
                            <td>&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                            <td height="35px;" width="200px;">
                                <p class="form-label">Roster Code</p>
                            </td>
                            <td><input id="wrkCode" type="text" name="wrkCode" class="input-text" style="width: 150px">
                            </td>
                            <td height="35px;" width="200px;">
                                <p class="form-label">Roster Name</p>
                            </td>
                            <td><input id="wrkName" type="text" name="wrkName" class="input-text" style="width: 150px">
                            </td>
                            <td height="35px;" width="200px;">
                                <p class="form-label">Working In Time</p>
                            </td>
                            <td><input id="wrkintime" type="time" name="wrkintime" class="input-text"
                                    style="width: 150px"></td>
                            <td height="35px;" width="200px;">
                                <p class="form-label">Working Out Time</p>
                            </td>
                            <td><input id="wrkouttime" type="time" name="wrkouttime" class="input-text"
                                    style="width: 150px">
                            </td>


                        </tr>
                        <tr>
                            <td height="35px;" width="200px;">
                                <p class="form-label">Late Calculate Time For Working Time</p>
                            </td>
                            <td><input id="wrkLate" type="time" name="wrkLate" class="input-text" style="width: 150px">
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
                            <td>&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                            <td height="35px;" width="200px;">
                                <p class="form-label">Half Day Slot Start (Morning)</p>
                            </td>
                            <td><input id="halfmorningstart" type="time" name="halfmorningstart" class="input-text"
                                    style="width: 150px"></td>
                            <td height="35px;" width="200px;">
                                <p class="form-label">Half Day Slot End (Morning)</p>
                            </td>
                            <td><input id="halfmorningend" type="time" name="halfmorningend" class="input-text"
                                    style="width: 150px"></td>
                            <td height="35px;" width="200px;">
                                <p class="form-label">Late Calculate Time For Halfday Morning</p>
                            </td>
                            <td><input id="halfMLate" type="time" name="halfMLate" class="input-text"
                                    style="width: 150px">
                            <td>&nbsp;&nbsp;</td>
                        </tr>

                        <tr>
                            <td height="35px;" width="200px;">
                                <p class="form-label">Half Day Slot Start (Evening)</p>
                            </td>
                            <td><input id="halfeveningstart" type="time" name="halfeveningstart" class="input-text"
                                    style="width: 150px"></td>
                            <td height="35px;" width="200px;">
                                <p class="form-label">Half Day Slot End (Evening)</p>
                            </td>
                            <td><input id="halfeveningend" type="time" name="halfeveningend" class="input-text"
                                    style="width: 150px"></td>
                            <td height="35px;" width="200px;">
                                <p class="form-label">Late Calculate Time For Halfday Evening</p>
                            </td>
                            <td><input id="halfELate" type="time" name="halfELate" class="input-text"
                                    style="width: 150px"></td>
                            <td>&nbsp;&nbsp;</td>
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
                            <td>&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;</td>
                        </tr>

                        <tr>
                            <td height="35px;" width="200px;">
                                <p class="form-label">Short Leave Slot Start (Morning)</p>
                            </td>
                            <td><input id="shortmorningstart" type="time" name="shortmorningstart" class="input-text"
                                    style="width: 150px"></td>
                            <td height="35px;" width="200px;">
                                <p class="form-label">Short Leave Slot End (Morning)</p>
                            </td>
                            <td><input id="shortmorningend" type="time" name="shortmorningend" class="input-text"
                                    style="width: 150px"></td>
                            <td height="35px;" width="200px;">
                                <p class="form-label">Late Calculate Time For Short Leave Morning</p>
                            </td>
                            <td><input id="shrtMLate" type="time" name="shrtMLate" class="input-text"
                                    style="width: 150px"></td>
                        </tr>

                        <tr>
                            <td height="35px;" width="200px;">
                                <p class="form-label">Short Leave Slot Start (Evening)</p>
                            </td>
                            <td><input id="shorteveningstart" type="time" name="shorteveningstart" class="input-text"
                                    style="width: 150px"></td>
                            <td height="35px;" width="200px;">
                                <p class="form-label">Short Leave Slot End (Evening)</p>
                            </td>
                            <td><input id="shorteveningend" type="time" name="shorteveningend" class="input-text"
                                    style="width: 150px"></td>
                            <td height="35px;" width="200px;">
                                <p class="form-label">Late Calculate Time For Short Leave Evening</p>
                            </td>
                            <td><input id="shrtELate" type="time" name="shrtELate" class="input-text"
                                    style="width: 150px">
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;</td>
                        </tr>

                        <tr style="border-bottom: 1px solid black;">
                            <td>Select Color Code</td>
                            <td>&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;</td>
                        </tr>

                        <tr>
                            <td>&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;</td>
                        </tr>

                        <tr>
                            <td height="35px;" width="200px;">
                                <p class="form-label">Color Code</p>
                            </td>
                            <td><input type="color" id="favcolor" name="favcolor" class="input-text"
                                    style="width: 150px"></td>
                            <td>&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;</td>
                        </tr>
                        <tr>
                            <td>&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;</td>
                        </tr>

                        <tr>
                            <td><button style="margin-top: 10px; width: 150px;" value="Save" id="settingsAdd"
                                    onclick="inputSettingsData()" class="btn btn-success">Save</button></td>
                            <td><button style="margin-top: 10px; width: 150px;" value="Update" id="settingsUpdate"
                                    onclick="updateSettingsData()" class="btn btn-warning">Update</button></td>
                            <td><button class="btn btn-dark" style="margin-top: 10px; width: 150px;"
                                    onClick="window.location.reload();">Clear</button></td>
                            <td>&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;</td>
                            <td>&nbsp;&nbsp;</td>
                        </tr>

                    </table>
                    <input id="swtid" type="text" name="swtid" class="input-text" hidden="hidden">
                </div></br>

                <div class="row x_title">
                    <div class="col-md-10">
                        <h4>Roster Profile Table<small> View Current Rosters</small></h4>
                    </div>
                </div>

                <div class="row">
                    <div id="tdata" style="overflow-y: scroll;width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>

</body>
<?php include("../Contains/footer.php");
?>

</html>