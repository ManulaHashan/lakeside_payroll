<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Login | Apex Payroll</title>
    <link href="Styles/login.css" rel="stylesheet" type="text/css">
    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="Images/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="Images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="Images/favicon/favicon-16x16.png">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.0/animate.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="JS/sweetalert2.js"></script>


    <script type="text/javascript">
    window.onload = function() {
        GetCurrentMonthLeave();
        $('#myModal').hide();
    };

    function GetCurrentMonthLeave() {
        var url = "Controller/login.php?request=getPreviouseleavecount";
        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {

            }
        });
    }

    function Login() {

        var un = $('#usr').val();
        var pw = $('#pwrd').val();

        if (un !== "" && pw !== "") {


            var url = "Controller/login.php?submit=true&usr=" + $('#usr').val() + "&pwrd=" + $('#pwrd').val();
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {

                    if (data == "OK") {
                        window.location = "Views/Home.php";
                    } else if (data == "SET") {
                        $('#myModal').show();
                    } else {
                        sweetalert("error", "Error", "Wrong Username or Password!");
                        $('#usr').val("");
                        $('#pwrd').val("");

                    }
                }
            });
        } else {

            sweetalert("warning", "Warning", "Please Enter Username and Password!");
        }
    }

    $(document).keypress(function(e) {
        if (e.which === 13) {
            Login();
        }
    });


    function sweetalert(type, title, message) {
        Swal.fire({
            icon: type,
            title: title,
            text: message,
        })
    }

    // 👇️ Change Password
    function ChangePW() {

        var NEWpw = $('#newpw').val();
        var CNFpw = $('#cnfpw').val();

        if (NEWpw !== "" && CNFpw !== "") {
            if (NEWpw == CNFpw) {
                var url = "Controller/login.php?request=changePW&NEWPW=" + $('#newpw').val();

                $.ajax({
                    type: 'POST',
                    url: url,
                    success: function(data) {

                        if (data == "OK") {
                            window.location = "Views/Home.php";
                        } else {
                            sweetalert("error", "Error", "Try Again!");
                        }
                    }
                });
            } else {
                sweetalert("error", "Error", "Password not match!");
            }
        } else {
            sweetalert("warning", "Warning", "Please Fill All Fields!");
        }

    }



    // 👇️ View Change Password Section
    window.onclick = function(event) {

        var modal = document.getElementById("myModal");

        if (event.target == modal) {
            $('#myModal').hide();
        }
    }

    // 👇️ Close Change Password Section
    function CloseModel() {
        $('#myModal').hide();
    }
    </script>

    <style>
    /* The Modal (background) */
    .modal {
        position: fixed;
        /* Stay in place */
        z-index: 1;
        /* Sit on top */
        padding-top: 100px;
        /* Location of the box */
        left: 0;
        top: 0;
        width: 100%;
        /* Full width */
        height: 100%;
        /* Full height */
        overflow: auto;
        /* Enable scroll if needed */
        background-color: rgb(0, 0, 0);
        /* Fallback color */
        background-color: rgba(0, 0, 0, 0.4);
        /* Black w/ opacity */
    }

    /* Modal Content */
    .modal-content {
        background-color: #fefefe;
        margin: auto;
        padding: 20px;
        border: 1px solid #888;
        border-radius: 8px;
        width: 35%;
    }

    /* The Close Button */
    .close {
        color: #aaaaaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }
    </style>

</head>

<body class="main-bg">
    <div class="text-c" style="padding-top: 15px;">
        <h1 class="animate-charcter">Welcome To Lakeside Adventist Hospital Payroll System</h1>
    </div>
    <div class="login-container text-c animated flipInX" style="padding-top: 60px;">
        <div>
            <h1 class="text-c logo-badge text-whitesmoke"><span class="fa fa-user-circle"></span></h1>
        </div>
        <h3 class="text-whitesmoke">User Login</h3>
        <p class="text-whitesmoke"><small>If you want to check the details please login first !</small></p>
        <div class="container-content">
            <form class="margin-t">
                <div class="form-group">
                    <input type="text" id="usr" class="form-control empty" title="Please Enter Username"
                        placeholder="&#xf007; Username">
                </div>
                <div class="form-group">
                    <input type="password" id="pwrd" class="form-control empty" title="Please Enter Password"
                        placeholder="&#xf023; Password">
                </div>
                <button type="button" class="form-button button-l margin-b" title="Click Here to Login"
                    style="cursor: pointer;" onclick="Login()">LOGIN</button>

                <!-- <a href="Views/emp_forgetpw.php" style="margin-left: 99px;" class="reset_pass">Forget Password?</a> -->
            </form>
            <p class="margin-t text-whitesmoke"><img src="Images/appex_logo.png" style="width: 50px"></br><small><a
                        style="color: white; text-decoration: none;" href="http://appexsl.com">Powered By Apex Software
                        Solutions</a></br>&copy;&nbsp;<?php echo date('Y'); ?>&nbsp;All Rights Reserved.</small></p>
            <!-- &copy; -->
        </div>
    </div>

    <!-- The Modal -->
    <div id="myModal" class="modal">

        <!-- Modal content -->
        <div class="modal-content">
            <span class="close" onclick="CloseModel()">&times;</span>

            <div class="logo-centered" align="center">
                <img src="employee_profile/derana_circle.png" alt="" style="width: 90px;" align="center">
            </div>
            <h3 align="center">Change Password</h3>
            <p align="center">Please change your old password to a new password.</p>
            <div class="form-group">
                <input type="password" class="form-control" placeholder="New Password" id="newpw" name="newpw">
            </div>
            <div class="form-group">
                <input type="password" class="form-control" placeholder="Confirm Password" id="cnfpw" name="cnfpw">
            </div>
            <div class="sign-btn text-center">
                <button type="button" class="btn btn-theme" onclick="ChangePW()">Change Password</button>
            </div>
        </div>
    </div>
</body>

</html>