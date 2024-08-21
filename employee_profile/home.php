<?php
error_reporting(0);
session_start();

if (!isset($_SESSION["uid"])) {
    header("Location: ../");
}

?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Home | Employee Profile</title>
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
    <link rel="stylesheet" href="node_modules/icon-kit/dist/css/iconkit.min.css">
    <link rel="stylesheet" href="node_modules/ionicons/dist/css/ionicons.min.css">
    <link rel="stylesheet" href="node_modules/perfect-scrollbar/css/perfect-scrollbar.css">
    <link rel="stylesheet" href="node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="node_modules/tempusdominus-bootstrap-4/build/css/tempusdominus-bootstrap-4.min.css">
    <link rel="stylesheet" href="node_modules/weather-icons/css/weather-icons.min.css">
    <link rel="stylesheet" href="node_modules/c3/c3.min.css">
    <link rel="stylesheet" href="node_modules/perfect-scrollbar/css/perfect-scrollbar.css">
    <link rel="stylesheet" href="node_modules/owl.carousel/dist/assets/owl.carousel.css">
    <link rel="stylesheet" href="node_modules/owl.carousel/dist/assets/owl.theme.default.css">
    <link rel="stylesheet" href="node_modules/fullcalendar/dist/fullcalendar.min.css">
    <link rel="stylesheet" href="dist/css/theme.min.css">
    <script src="src/js/vendor/modernizr-2.8.3.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

    <script type="text/javascript">
        window.onload = function() {

            loadNotifiation();
            noticount();
            searchByID();
            loadDashData();
            loadDashBox();
            leaveCounter();
            monthAttChart();
        };

        // 👇️ Search Employee Image URL By User ID
        function searchByID() {

            $.ajax({
                type: 'POST',
                url: 'Controller/home.php?request=getEmpsbyID',
                success: function(data) {

                    if (data == "0") {

                        avtimg.setAttribute('src', "img/userprof.png");

                    } else {
                        avtimg.setAttribute('src', data);
                    }
                }
            });

        }

        // 👇️ Month Attendance Calender
        function loadDashData() {

            var url = "Controller/home.php?request=getleave";

            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    $('#tdata').html(data);
                }
            });
        }

        // 👇️ Get Attendence Leaves Request Count Function
        function loadDashBox() {

            // 👇️ Current Month
            var dt = new Date();
            var month = dt.getMonth() + 1;
            var year = dt.getFullYear();
            var daysInMonth = new Date(year, month, 0).getDate();

            var url = "Controller/home.php?request=approveleave";

            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {

                    var arr = data.split("#");
                    $('#av').html(arr[1] + "<sub style='font-size:10px;'>Attended</sub> / " + daysInMonth);
                    $("#leavereq").html(arr[2]);
                }
            });
        }

        // 👇️ Get Leave Count
        function leaveCounter() {

            var url = "Controller/home.php?request=getleavecount";

            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {

                    var arr = data.split("#");

                    $("#medical_box").show();
                    if (arr[0] == "B") {
                        $("#box_1").html("Casual Leaves");
                        $("#box_2").html("Annual Leaves");
                        $("#box_1_leave").html(arr[1] + "<sub style='font-size:10px;'>Taken</sub> / " + arr[2] +
                            "<sub style='font-size:10px;'>Available</sub>");
                        $("#box_2_leave").html(arr[3] + "<sub style='font-size:10px;'>Taken</sub> / " + arr[4] +
                            "<sub style='font-size:10px;'>Available</sub>");
                        $("#box_medi_leave").html(arr[5] + "<sub style='font-size:10px;'>Taken</sub> / " + arr[
                            6] + "<sub style='font-size:10px;'>Available</sub>");
                        $("#box_3_leave").html(arr[12]);
                        $("#box_4_leave").html(arr[16]);
                        $("#box_1_msg").html("Total Casual Leaves");
                        $("#box_2_msg").html("Total Annual Leaves");
                    } else if (arr[0] == "C") {
                        $("#box_1").html("Casual Leaves");
                        $("#box_2").html("Annual Leaves");
                        $("#box_1_leave").html(arr[1] + "<sub style='font-size:10px;'>Taken</sub> / " + arr[2] +
                            "<sub style='font-size:10px;'>Available</sub>");
                        $("#box_2_leave").html(arr[3] + "<sub style='font-size:10px;'>Taken</sub> / " + arr[4] +
                            "<sub style='font-size:10px;'>Available</sub>");
                        $("#box_medi_leave").html(arr[5] + "<sub style='font-size:10px;'>Taken</sub> / " + arr[
                            6] + "<sub style='font-size:10px;'>Available</sub>");
                        $("#box_3_leave").html(arr[12]);
                        $("#box_4_leave").html(arr[16]);
                        $("#box_1_msg").html("Total Casual Leaves");
                        $("#box_2_msg").html("Total Annual Leaves");
                    } else {

                        if (arr[9] == "Empty") {
                            $("#casual_box").hide();
                            $("#annual_box").hide();
                            $("#nopay_box").hide();
                            $("#leave_leave_box").hide();
                            $("#request_box").hide();
                            $("#medical_box").hide();

                        } else {
                            $("#box_1").html("Halfday Leaves");
                            $("#box_2").html("Short Leaves");
                            $("#box_1_leave").html(arr[1] + "<sub style='font-size:10px;'>Taken</sub> / " + arr[
                                2] + "<sub style='font-size:10px;'>Available</sub>");
                            $("#box_2_leave").html(arr[8] + "<sub style='font-size:10px;'>Taken</sub> / " + arr[
                                9] + "<sub style='font-size:10px;'>Available</sub>");
                            $("#box_3_leave").html(arr[12]);
                            $("#box_4_leave").html(arr[16]);
                            $("#box_1_msg").html("Total Halfday Leaves");
                            $("#box_2_msg").html("Total Short Leaves");
                            $("#medical_box").hide();
                        }

                    }


                }
            });
        }

        // 👇️ Monthly Attendance Count Chart
        function monthAttChart() {
            var dt = new Date();
            var year = dt.getFullYear();
            var url = "Controller/home.php?request=getmonthlyattcount";

            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {


                    var arr = data.split("#");

                    var xValues = ["January", "February", "March", "April", "May", "June", "July", "Auguest",
                        "September", "October", "November", "December"
                    ];

                    var yValues = [arr[0], arr[1], arr[2], arr[3], arr[4], arr[5], arr[6], arr[7], arr[8], arr[
                        9], arr[10], arr[11]];
                    var barColors = [
                        "#b91d47",
                        "#00aba9",
                        "#2b5797",
                        "#e8c3b9",
                        "#1e7145",
                        "#ffcc00",
                        "#3333ff",
                        "#ff0000",
                        "#cc3399",
                        "#996633",
                        "#669999",
                        "#99cc00"
                    ];

                    new Chart("myChart", {
                        type: "doughnut",
                        data: {
                            labels: xValues,
                            datasets: [{
                                backgroundColor: barColors,
                                data: yValues
                            }]
                        },
                        options: {
                            title: {
                                display: true,
                                text: "Monthly Attendance Count In " + year
                            }
                        }
                    });

                }
            });
        }

        // 👇️ Notification View Status Update 
        function getrowid(id) {

            var url = "Controller/home.php?request=viewnotification&notid=" + id;

            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {

                    if (data == "1") {
                        loadNotifiation();
                        noticount();
                    }

                }
            });
        }

        // 👇️ Get Current Notification Count
        function noticount() {

            var url = "Controller/home.php?request=getnotificationcount";

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

            var url = "Controller/home.php?request=getnotification";

            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    $('#abcd').html(data);
                }
            });
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
                                <div class="footer"><a href="Views/emp_notification.php">See all notifications</a></div>
                            </div>
                        </div>
                        <div class="dropdown">
                            <a class="dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img class="avatar" id="avtimg" alt=""></a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="Views/emp_profile.php"><i class="ik ik-user dropdown-icon"></i> Profile</a>
                                <a class="dropdown-item" href="Views/emp_notification.php"><i class="ik ik-bell dropdown-icon"></i> Notification</a>
                                <a class="dropdown-item" href="Controller/logout.php"><i class="ik ik-power dropdown-icon"></i> Logout</a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </header>

        <div class="page-wrap">
            <?php include("Contains/main_cont_panel.php"); ?>
            <div class="main-content">
                <div class="container-fluid">
                    <div class="row clearfix">
                        <div class="col-lg-3 col-md-6 col-sm-12" id="casual_box">
                            <div class="widget">
                                <div class="widget-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="state">
                                            <h6 id="box_1">Casual Leaves</h6>
                                            <h2 id="box_1_leave"></h2>
                                        </div>
                                        <div class="icon">
                                            <i class="ik ik-clock"></i>
                                        </div>
                                    </div>
                                    <small class="text-small mt-10 d-block" id="box_1_msg">Total Casual Leaves</small>
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-success" role="progressbar" aria-valuenow="78" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12" id="annual_box">
                            <div class="widget">
                                <div class="widget-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="state">
                                            <h6 id="box_2">Annual Leaves</h6>
                                            <h2 id="box_2_leave"></h2>
                                        </div>
                                        <div class="icon">
                                            <i class="ik ik-clock"></i>
                                        </div>
                                    </div>
                                    <small class="text-small mt-10 d-block" id="box_2_msg">Total Annual Leaves</small>
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-info" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12" id="medical_box">
                            <div class="widget">
                                <div class="widget-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="state">
                                            <h6>Medical Leaves</h6>
                                            <h2 id="box_medi_leave"></h2>
                                        </div>
                                        <div class="icon">
                                            <i class="ik ik-clock"></i>
                                        </div>
                                    </div>
                                    <small class="text-small mt-10 d-block">Total Medical Leaves</small>
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-dark" role="progressbar" aria-valuenow="62" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12" id="nopay_box">
                            <div class="widget">
                                <div class="widget-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="state">
                                            <h6 id="box_3">Nopay Leaves</h6>
                                            <h2 id="box_3_leave"></h2>
                                        </div>
                                        <div class="icon">
                                            <i class="ik ik-clock"></i>
                                        </div>
                                    </div>
                                    <small class="text-small mt-10 d-block" id="box_3_msg">Total Nopay Leaves</small>
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-pink" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12" id="leave_leave_box">
                            <div class="widget">
                                <div class="widget-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="state">
                                            <h6 id="box_4">Lieu Leaves</h6>
                                            <h2 id="box_4_leave"></h2>
                                        </div>
                                        <div class="icon">
                                            <i class="ik ik-clock"></i>
                                        </div>
                                    </div>
                                    <small class="text-small mt-10 d-block" id="box_4_msg">Total Lieu Leaves</small>
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-yellow" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12">
                            <div class="widget">
                                <div class="widget-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="state">
                                            <h6>Total Attendance (Month)</h6>
                                            <h2 id="av"></h2>
                                        </div>
                                        <div class="icon">
                                            <i class="ik ik-calendar"></i>
                                        </div>
                                    </div>
                                    <small class="text-small mt-10 d-block">Total attendance of this month</small>
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="31" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-sm-12" id="request_box">
                            <div class="widget">
                                <div class="widget-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="state">
                                            <h6>Total Leave Request</h6>
                                            <h2 id="leavereq"></h2>
                                        </div>
                                        <div class="icon">
                                            <i class="ik ik-thumbs-up"></i>
                                        </div>
                                    </div>
                                    <small class="text-small mt-10 d-block">Requested leave count</small>
                                </div>
                                <div class="progress progress-sm">
                                    <div class="progress-bar bg-purple" role="progressbar" aria-valuenow="31" aria-valuemin="0" aria-valuemax="100" style="width: 100%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h3>Attendance Calender</h3>
                                </div>
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-lg-8 col-md-12">
                                            <div class="table-responsive-sm">
                                                <div id="tdata" style="width: 700px;"></div>
                                                <div>&nbsp;&nbsp;</div>
                                                <div>&nbsp;&nbsp;</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-4">
                            <div class="card" style="min-height: 422px;">
                                <div class="card-header">
                                    <h3>Monthly Attendance Chart</h3>
                                </div>
                                <div class="card-body">
                                    <!-- <div id="c3-donut-chart"></div> -->
                                    <canvas id="myChart" style="width:100%; height: 523px; max-width:600px"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php include("Contains/footer.php"); ?>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
    <script>
        window.jQuery || document.write('<script src="src/js/vendor/jquery-3.3.1.min.js"><\/script>')
    </script>
    <script src="node_modules/popper.js/dist/umd/popper.min.js"></script>
    <script src="node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="node_modules/perfect-scrollbar/dist/perfect-scrollbar.min.js"></script>
    <script src="node_modules/screenfull/dist/screenfull.js"></script>
    <script src="node_modules/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="node_modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="node_modules/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="node_modules/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js"></script>
    <script src="node_modules/moment/moment.js"></script>
    <script src="node_modules/tempusdominus-bootstrap-4/build/js/tempusdominus-bootstrap-4.min.js"></script>
    <script src="node_modules/fullcalendar/dist/fullcalendar.min.js"></script>
    <script src="node_modules/d3/dist/d3.min.js"></script>
    <script src="node_modules/c3/c3.min.js"></script>
    <script src="js/tables.js"></script>
    <script src="js/charts.js"></script>
    <script src="dist/js/theme.min.js"></script>
    <?php
    session_commit();
    ?>
</body>

</html>