<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Login | Employee Profile</title>
        <meta name="description" content="">
        <meta name="keywords" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <!-- Favicons -->
        <link rel="apple-touch-icon" sizes="180x180" href="img/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="img/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="img/favicon/favicon-16x16.png">

        <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:300,400,600,700,800" rel="stylesheet">
        
        <link rel="stylesheet" href="node_modules/bootstrap/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="node_modules/@fortawesome/fontawesome-free/css/all.min.css">
        <link rel="stylesheet" href="node_modules/ionicons/dist/css/ionicons.min.css">
        <link rel="stylesheet" href="node_modules/icon-kit/dist/css/iconkit.min.css">
        <link rel="stylesheet" href="node_modules/perfect-scrollbar/css/perfect-scrollbar.css">
        <link rel="stylesheet" href="dist/css/theme.min.css">
        <script src="src/js/vendor/modernizr-2.8.3.min.js"></script>
        <script src="js/sweetalert2.js"></script>
        <script type="text/javascript" src="https://code.jquery.com/jquery-1.7.1.min.js"></script>

        <script type="text/javascript">
            
            // 👇️ Sweet Alert Function
            function sweetalert(type, title, message) {
            Swal.fire({
                icon: type,
                title: title,
                text: message,
            })
           }

           window.onload = function() 
           {
                $('#myModal').hide();
           };
           
           // 👇️ User Login Function
            function Login() {


                var un = $('#uname').val();
                var pw = $('#pword').val();

                if (un !== "" && pw !== "") {
                    
                    var url = "Controller/login.php?submit=true&usr=" + $('#uname').val() + "&pwrd=" + $('#pword').val();
                
                    $.ajax({
                        type: 'POST',
                        url: url,
                        success: function(data) {

                            if (data == "OK") 
                            {
                                window.location = "../employee_profile/home.php";
                            }
                            else if (data == "SET") 
                            {
                                $('#myModal').show();
                            } 
                            else 
                            {
                                sweetalert("error","Error", "Wrong Username or Password!");
                            }
                        }
                    });
                }
                else
                {
                    sweetalert("warning","Warning", "Please Enter Username and Password!");
                }
                
            }

            // 👇️ Change Password
            function ChangePW()
            {
                var NEWpw = $('#newpw').val();
                var CNFpw = $('#cnfpw').val();
                
                if (NEWpw !== "" && CNFpw !== "") 
                {
                    if (NEWpw == CNFpw) 
                    {
                        var url = "Controller/login.php?request=changePW&NEWPW=" + $('#newpw').val();
                    
                        $.ajax({
                            type: 'POST',
                            url: url,
                            success: function(data) {

                                if (data == "OK") 
                                {
                                    window.location = "../employee_profile/home.php";
                                }
                                else 
                                {
                                    sweetalert("error","Error", "Try Again!");
                                } 
                            }
                        });
                    }
                    else
                    {
                        sweetalert("error","Error", "Password not match!");
                    }
                }
                else
                {
                    sweetalert("warning","Warning", "Please Fill All Fields!");
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


            
            // 👇️ Enter Button Keypress Event
            $(document).keypress(function(e) {
                if (e.which === 13) {
                    Login();
                }
            }); 
        </script>

        <style>
        /* The Modal (background) */
        .modal {
          position: fixed; /* Stay in place */
          z-index: 1; /* Sit on top */
          padding-top: 100px; /* Location of the box */
          left: 0;
          top: 0;
          width: 100%; /* Full width */
          height: 100%; /* Full height */
          overflow: auto; /* Enable scroll if needed */
          background-color: rgb(0,0,0); /* Fallback color */
          background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
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

    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <!-- img/auth/login-bg.jpg -->

        <div class="auth-wrapper">
            <div class="container-fluid h-100">
                <div class="row flex-row h-100 bg-white">
                    <div class="col-xl-8 col-lg-6 col-md-5 p-0 d-md-block d-lg-block d-sm-none d-none">
                        <div class="lavalite-bg" style="background-image: url('https://img.freepik.com/free-photo/man-woman-scientist-partners-write-clipboard-measuring-liquid-working-laboratory_839833-31913.jpg?w=996&t=st=1702096584~exp=1702097184~hmac=7ba42fcb48d3c61117249f55f2f8f9244994c02db9194d581c743bfc0c3048ad')">
                            <div class="lavalite-overlay"></div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-7 my-auto p-0">
                        <div class="authentication-form mx-auto">
                            <div class="logo-centered">
                                <a href="index.php"><img src="lakeside_circle.png" alt="" style="width: 90px;" align="center"></a>
                            </div>
                            <h3>Sign In to Lakeside Employee Profile</h3>
                            <p>Happy to see you again!</p>
                            <form>
                                <div class="form-group">
                                    <input type="text" class="form-control" placeholder="Username" id="uname" name="uname">
                                    <i class="ik ik-user"></i>
                                </div>
                                <div class="form-group">
                                    <input type="password" class="form-control" placeholder="Password" id="pword" name="pword">
                                    <i class="ik ik-lock"></i>
                                </div>
                                <div class="row">
                                    <div class="col text-left">
                                        <label class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="item_checkbox" name="item_checkbox" value="option1">
                                            <span class="custom-control-label">&nbsp;Remember Me</span>
                                        </label>
                                    </div>
                                    <div class="col text-right">
                                        <a href="Views/emp_forgetpw.php">Forgot Password ?</a>
                                    </div>
                                </div>
                                <div class="sign-btn text-center">
                                    <button  type="button" class="btn btn-theme" onclick="Login()">Sign In</button>
                                </div>
                            </form>
                            <div class="register">
                                <small>Powered By Apex Software Solutions</small></br>
                                <img src="appex_logo.png" style="width: 45px">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- The Modal -->
        <div id="myModal" class="modal">

            <!-- Modal content -->
            <div class="modal-content">
                <span class="close" onclick="CloseModel()">&times;</span>
                
                <div class="logo-centered" align="center">
                    <img src="derana_circle.png" alt="" style="width: 90px;" align="center">
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
                    <button  type="button" class="btn btn-theme" onclick="ChangePW()">Change Password</button>
                </div> 
            </div>
        </div>
        
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
        <script>window.jQuery || document.write('<script src="../src/js/vendor/jquery-3.3.1.min.js"><\/script>')</script>
        <script src="node_modules/popper.js/dist/umd/popper.min.js"></script>
        <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="node_modules/perfect-scrollbar/dist/perfect-scrollbar.min.js"></script>
        <script src="node_modules/screenfull/dist/screenfull.js"></script>
        <script src="dist/js/theme.js"></script>
        <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
    </body>
</html>
