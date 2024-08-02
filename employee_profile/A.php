<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Login | Employee Profile</title>
        <meta name="description" content="">
        <meta name="keywords" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <link rel="icon" href="favicon_derana.ico" type="image/x-icon" />

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
                            if (data === "OK") {
                                    window.location = "../employee_profile/home.php";
                            } else {
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
            
            // 👇️ Enter Button Keypress Event
            $(document).keypress(function(e) {
                if (e.which === 13) {
                    Login();
                }
            }); 
        </script>
    </head>

    <body>
        <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

        <div class="auth-wrapper">
            <div class="container-fluid h-100">
                <div class="row flex-row h-100 bg-white">
                    <div class="col-xl-8 col-lg-6 col-md-5 p-0 d-md-block d-lg-block d-sm-none d-none">
                        <div class="lavalite-bg" style="background-image: url('img/auth/login-bg.jpg')">
                            <div class="lavalite-overlay"></div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-md-7 my-auto p-0">
                        <div class="authentication-form mx-auto">
                            <div class="logo-centered">
                                <a href="index.php"><img src="derana_circle.png" alt="" style="width: 90px; padding-left: 15px;" align="center"></a>
                            </div>
                            <h3>Sign In to Derana Employee Profile</h3>
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
