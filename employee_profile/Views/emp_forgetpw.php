<?php
error_reporting(0);
session_start();
?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Forgot Password | Employee Profile</title>
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="../img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../img/favicon/favicon-16x16.png">

    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,600,700,800" rel="stylesheet">

    <link rel="stylesheet" href="../node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../node_modules/@fortawesome/fontawesome-free/css/all.min.css">
    <link rel="stylesheet" href="../node_modules/ionicons/dist/css/ionicons.min.css">
    <link rel="stylesheet" href="../node_modules/icon-kit/dist/css/iconkit.min.css">
    <link rel="stylesheet" href="../node_modules/perfect-scrollbar/css/perfect-scrollbar.css">
    <link rel="stylesheet" href="../dist/css/theme.min.css">
    <script src="../src/js/vendor/modernizr-2.8.3.min.js"></script>
    <script src="../js/sweetalert2.js"></script>

    <script type="text/javascript">
        window.onload = function() {
            $('#confirmpw').hide();
            $('#resetpw').hide();
        };

        // 👇️ Sweet Alert Function
        function sweetalert(type, title, message) {
            Swal.fire({
                icon: type,
                title: title,
                text: message,
            })
        }

        function AutoGenerateFPW() {

            var tpno = $('#tpno').val();

            if (tpno == "") {
                sweetalert("warning", "Warning", "Please Enter Your Mobile Number!");
            } else {
                (function() {
                    function IDGenerator() {

                        this.length = 5;
                        this.timestamp = +new Date;

                        var _getRandomInt = function(min, max) {
                            return Math.floor(Math.random() * (max - min + 1)) + min;
                        }

                        this.generate = function() {
                            var ts = this.timestamp.toString();
                            var parts = ts.split("").reverse();
                            var id = "FPW";

                            for (var i = 0; i < this.length; ++i) {
                                var index = _getRandomInt(0, parts.length - 1);
                                id += parts[index];
                            }

                            return id;
                        }


                    }

                    var generator = new IDGenerator();
                    var idd = generator.generate();
                    $('#tempcode').val(idd);

                })();

                var url = "../Controller/login.php?request=sendetpnocode&tpno=" + $('#tpno').val() + "&fwdcode=" + $(
                    '#tempcode').val();

                $.ajax({
                    type: 'POST',
                    url: url,
                    success: function(data) {

                        if (data == "1") {
                            $('#confirmpw').show();
                            $('#forgetpw').hide();
                            $('#resetpw').hide();
                        } else if (data == "0") {
                            sweetalert("error", "Error", "Your Mobile Number is Wrong!");
                            $('#confirmpw').hide();
                            $('#resetpw').hide();
                        } else {
                            sweetalert("error", "Error", "Sending Failed!");
                            $('#confirmpw').hide();
                            $('#resetpw').hide();
                        }
                    }
                });
            }




        }

        function CheckConfirm() {

            var concode = $('#confno').val();

            if (concode == "") {
                sweetalert("warning", "Warning", "Please Enter Confirmation Code!");
            } else {
                var url = "../Controller/login.php?request=checkconfirmcode&code=" + $('#confno').val();

                $.ajax({
                    type: 'POST',
                    url: url,
                    success: function(data) {

                        if (data == "0") {
                            sweetalert("error", "Error", "Confirmation Code is Wrong!");
                            $('#confirmpw').show();
                            $('#forgetpw').hide();
                            $('#resetpw').hide();
                        } else {
                            $('#confirmuid').val(data);
                            $('#confirmpw').hide();
                            $('#forgetpw').hide();
                            $('#resetpw').show();
                        }
                    }
                });
            }
        }


        function ResetPW() {

            var pword1 = $('#pw1').val();
            var pword2 = $('#pw2').val();

            if (pword1 == "" || pword2 == "") {
                sweetalert("warning", "Warning", "Please Enter New Password!");
            } else {
                if (pword1 == pword2) {
                    var url = "../Controller/login.php?request=resetpw&pass1=" + $('#pw1').val() + "&lid=" + $(
                        '#confirmuid').val();

                    $.ajax({
                        type: 'POST',
                        url: url,
                        success: function(data) {

                            if (data == "0") {
                                sweetalert("error", "Error", "Password Reset Unsuccessfully!");
                                $('#confirmpw').hide();
                                $('#forgetpw').hide();
                                $('#resetpw').show();
                            } else {
                                sweetalert("success", "Success", "Password Reset Successfully!");
                                window.location = "../index.php";
                            }
                        }
                    });
                } else {
                    sweetalert("warning", "Warning", "Pasword is Not Match!");
                }

            }
        }
    </script>
</head>

<body>

    <div class="auth-wrapper">
        <div class="container-fluid h-100">
            <div class="row flex-row h-100 bg-white">
                <div class="col-xl-8 col-lg-6 col-md-5 p-0 d-md-block d-lg-block d-sm-none d-none">
                    <div class="lavalite-bg" style="background-image: url('../img/auth/login-bg.jpg')">
                        <div class="lavalite-overlay"></div>
                    </div>
                </div>
                <div class="col-xl-4 col-lg-6 col-md-7 my-auto p-0">
                    <div class="authentication-form mx-auto">
                        <div class="logo-centered">
                            <a href="index.php"><img src="../derana_circle.png" alt="" style="width: 90px;" align="center"></a>
                        </div>

                        <div id="forgetpw">
                            <h3>Forgot Password</h3>
                            <p>We will send you a code to reset password.</p>
                            <div class="form-group">
                                <input type="text" class="form-control" id="tpno" placeholder="Your Mobile No" required="">
                                <i class="ik ik-phone-call"></i>
                            </div>
                            <input type="text" name="tempcode" id="tempcode" hidden="hidden">
                            <div class="sign-btn text-center">
                                <button class="btn btn-theme" onclick="AutoGenerateFPW()">Submit</button>
                            </div>
                        </div>

                        <div id="confirmpw">
                            <h3>Confirmation</h3>
                            <p>Please enter your confirmation code.</p>
                            <div class="form-group">
                                <input type="text" class="form-control" id="confno" placeholder="Your Confirmation Code" required="">
                                <i class="ik ik-code"></i>
                            </div>
                            <div class="sign-btn text-center">
                                <button class="btn btn-theme" onclick="CheckConfirm()">Submit</button>
                            </div>
                        </div>

                        <div id="resetpw">
                            <h3>Re-Set Password</h3>
                            <p>Please enter your new password.</p>
                            <div class="form-group">
                                <input type="password" class="form-control" id="pw1" placeholder="Your Password" required="">
                                <i class="ik ik-code"></i>
                            </div>
                            <div class="form-group">
                                <input type="password" class="form-control" id="pw2" placeholder="Re-Password" required="">
                                <i class="ik ik-code"></i>
                            </div>
                            <div class="sign-btn text-center">
                                <button class="btn btn-theme" onclick="ResetPW()">Submit</button>
                            </div>
                        </div>
                        <input type="text" name="confirmuid" id="confirmuid" hidden="hidden">
                        <div class="register">
                            <small>Powered By Appex Software Solutions</small></br>
                            <img src="../appex_logo.png" style="width: 45px">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script>
        window.jQuery || document.write('<script src="../src/js/vendor/jquery-3.3.1.min.js"><\/script>')
    </script>
    <script src="../node_modules/popper.js/dist/umd/popper.min.js"></script>
    <script src="../node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="../node_modules/perfect-scrollbar/dist/perfect-scrollbar.min.js"></script>
    <script src="../node_modules/screenfull/dist/screenfull.js"></script>
    <script src="../dist/js/theme.js"></script>
    <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
    <script>
        (function(b, o, i, l, e, r) {
            b.GoogleAnalyticsObject = l;
            b[l] || (b[l] =
                function() {
                    (b[l].q = b[l].q || []).push(arguments)
                });
            b[l].l = +new Date;
            e = o.createElement(i);
            r = o.getElementsByTagName(i)[0];
            e.src = 'https://www.google-analytics.com/analytics.js';
            r.parentNode.insertBefore(e, r)
        }(window, document, 'script', 'ga'));
        ga('create', 'UA-XXXXX-X', 'auto');
        ga('send', 'pageview');
    </script>
</body>

</html>