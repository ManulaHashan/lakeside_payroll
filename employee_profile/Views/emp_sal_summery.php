<?php
    session_start();

    if(!isset($_SESSION["uid"])){
        header("Location: ../");
    } 
?>
<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Salary Summery | Employee Profile</title>
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
        <link rel="stylesheet" href="../node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css">
        <script src="../js/sweetalert2.js"></script>

        <script type="text/javascript">

            window.onload = function() {
                
                document.getElementById("year").value = new Date().getFullYear();
                searchByID();
                loadTable();
                loadNotifiation();
                noticount();
            };

            // 👇️ Sweet Alert Function
            function sweetalert(type, title, message) {
            Swal.fire({
                icon: type,
                title: title,
                text: message,
            })
           }

           // 👇️ Search Employee Image URL By User ID
            function searchByID() {
                
                    $.ajax({
                        type: 'POST',
                        url: '../Controller/emp_sal_summery.php?request=getEmpsbyID',
                        success: function(data) {
 
                            if (data == "0") {
                               
                               avtimg.setAttribute('src', "../img/userprof.png");
                                   
                            } 
                            else 
                            {
                               avtimg.setAttribute('src', '../'+data);
                            }
                        }
                    });

            }
           
           // 👇️ View Attendance Log Table 
           function loadTable() {

                var url = "../Controller/emp_sal_summery.php?request=getleave&Year=" + $('#year').val();

                $.ajax({
                    type: 'POST',
                    url: url,
                    success: function(data) {
                        $('#tdata').html(data);
                    }
                });
            }
            
            // 👇️ Notification View Status Update Function
            function getrowid(id){

                var url = "../Controller/emp_sal_summery.php?request=viewnotification&notid=" + id;

                $.ajax({
                    type: 'POST',
                    url: url,
                    success: function(data) {

                        if (data == "1") 
                        {
                            loadNotifiation();
                            noticount();
                        }
                        
                    }
                });
            }
            
            // 👇️ Get Current Notification Count
            function noticount(){

                var url = "../Controller/emp_sal_summery.php?request=getnotificationcount";

                $.ajax({
                    type: 'POST',
                    url: url,
                    success: function(data) {

                        $('#notic').html(data);
                        
                    }
                });
            }
            
            // 👇️ Load Notification to Notification Bar
            function loadNotifiation() {

                var url = "../Controller/emp_sal_summery.php?request=getnotification";

                $.ajax({
                    type: 'POST',
                    url: url,
                    success: function(data) {
                        $('#abcd').html(data);
                    }
                });
            }

            function ViewSlip(id){

                var url = '../Model/emp_salary_slip.php?slipID='+id;
                window.open(url, "_blank");
            }

            function PrintSlip(id){

                var url = '../Model/print_salary_slip.php?slipID='+id;
                window.open(url, "_blank");
            }

        </script>
    </head>

    <body>
        <div class="wrapper">
             <header class="header-top" header-theme="light">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between">
                        <div class="top-menu d-flex align-items-center">
                            <button type="button" class="btn-icon mobile-nav-toggle d-lg-none"><span></span></button>
                            <div class="header-search">
                                <div class="input-group">
                                    <span class="input-group-addon search-close"><i class="ik ik-x"></i></span>
                                    <input type="text" class="form-control">
                                    <span class="input-group-addon search-btn"><i class="ik ik-search"></i></span>
                                </div>
                            </div>
                            <button type="button" id="navbar-fullscreen" class="nav-link"><i class="ik ik-maximize"></i></button>
                        </div>
                        <div class="top-menu d-flex align-items-center">
                            <div class="dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="notiDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ik ik-bell"></i><span class="badge bg-danger" id="notic"></span></a>
                                <div class="dropdown-menu dropdown-menu-right notification-dropdown" aria-labelledby="notiDropdown">
                                    <h4 class="header">Notifications</h4>
                                    <div class="notifications-wrap" id="abcd">
                                    </div>
                                    <div class="footer"><a href="../Views/emp_notification.php">See all notifications</a></div>
                                </div>
                            </div>
                            <div class="dropdown">
                                <a class="dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img class="avatar" id="avtimg" alt=""></a>
                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                                    <a class="dropdown-item" href="../Views/emp_profile.php"><i class="ik ik-user dropdown-icon"></i> Profile</a>
                                    <a class="dropdown-item" href="../Views/emp_notification.php"><i class="ik ik-bell dropdown-icon"></i> Notification</a>
                                    <a class="dropdown-item" href="../Controller/logout.php"><i class="ik ik-power dropdown-icon"></i> Logout</a>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </header>

            <div class="page-wrap">
                <?php include("../Contains/other_cont_panel.php"); ?>
                <div class="main-content">
                    <div class="container-fluid">
                        <div class="page-header">
                            <div class="row align-items-end">
                                <div class="col-lg-8">
                                    <div class="page-header-title">
                                        <i class="ik ik-edit bg-blue"></i>
                                        <div class="d-inline">
                                            <h5>Salary Summery</h5>
                                            <span>You can view your monthly salary details from this page</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <nav class="breadcrumb-container" aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item">
                                                <a href="../home.php"><i class="ik ik-home"></i></a>
                                            </li>
                                            <li class="breadcrumb-item"><a href="#">Employee</a></li>
                                            <li class="breadcrumb-item active" aria-current="page">Salary Summery</li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header d-block">
                                        <h3>Monthly Salary Table</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive-sm">
                                             <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="example-name">Year</label>
                                                        <select id="year" name="year" class="form-control" onchange="loadTable()">
                                                            <?php
                                                              $year2 = 2023;
                                                              $endyears = date("Y")-1;
                                                              for ($years = $year2; $years <= $endyears; $years++) 
                                                              {?>
                                                                  <option value="<?php echo $years; ?>"><?php echo $years; ?></option><?php
                                                              }

                                                            ?> 

                                                            <?php
                                                              $year1 = date("Y");
                                                              $endyear = date("Y")+10;
                                                              for ($year = $year1; $year <= $endyear; $year++) 
                                                              {?>
                                                                  <option value="<?php echo $year; ?>"><?php echo $year; ?></option><?php
                                                              }

                                                            ?> 
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="tdata" style="height: 400px; overflow-y: scroll;width: 100%;"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php include("../Contains/footer.php"); ?>
            </div>
        </div>
        
        
        <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
        <script>window.jQuery || document.write('<script src="../src/js/vendor/jquery-3.3.1.min.js"><\/script>')</script>
        <script src="../node_modules/popper.js/dist/umd/popper.min.js"></script>
        <script src="../node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="../node_modules/perfect-scrollbar/dist/perfect-scrollbar.min.js"></script>
        <script src="../dist/js/theme.min.js"></script>
        <script src="../js/forms.js"></script>
        <script src="../node_modules/screenfull/dist/screenfull.js"></script>
        <script src="../node_modules/datatables.net/js/jquery.dataTables.min.js"></script>
        <script src="../node_modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
        <script src="../js/datatables.js"></script>
        <?php
             session_commit();
        ?>
    </body>
</html>

