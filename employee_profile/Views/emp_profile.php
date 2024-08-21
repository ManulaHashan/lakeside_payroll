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
    <title>Profile | Employee Profile</title>
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

    <style type="text/css">
        .image-upload>input {
            display: none;
        }

        .image-upload img {
            width: 30px;
            cursor: pointer;
        }
    </style>

    <script type="text/javascript">
        window.onload = function() {
            searchByID();
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

        // 👇️ Search Employee Details By User ID
        function searchByID() {
            var id = document.getElementById('uid').value;

            if (id !== "") {
                $.ajax({
                    type: 'POST',
                    url: '../Controller/emp_profile.php?request=getEmpsbyID&uid=' + id,
                    success: function(data) {
                        if (data !== "usernotfound") {
                            $('#userImg').show();

                            var arr = data.split("#");
                            $("#fname").val(arr[1]);
                            $("#nic").val(arr[4]);
                            $("#mobp").val(arr[5]);
                            $("#landp").val(arr[6]);
                            $("#dob").val(arr[7]);
                            $("#email").val(arr[8]);
                            $("#desig").val(arr[9]);
                            $("#pname").html(arr[1]);
                            $("#pposition").html(arr[9]);
                            $("#psal").val(arr[11]);

                            if (arr[11] !== "0") {
                                $("#psal").val(arr[11]);
                            } else {

                                $("#psal").val("0");
                            }

                            $("#regdate").val(arr[13]);

                            getAddress(arr[14]);
                            getPAddress(arr[15]);
                            getPositionAndGread(arr[21], arr[18], arr[16]);
                            getDepartment(arr[36]);

                            $("#status").val(arr[17]);
                            $("#jc").html(arr[20]);
                            $("#epf").html(arr[22]);

                            var url = arr[31].split("/");
                            if (url == "") {
                                // $('#userImg').html("<img src='../img/userprof.png' class='rounded-circle' width='150'>");
                                userImg.setAttribute('src', "../img/userprof.png");
                                avtimg.setAttribute('src', "../img/userprof.png");
                            } else {
                                userImg.setAttribute('src', arr[31]);
                                avtimg.setAttribute('src', arr[31]);
                            }

                            $("#empact").val(arr[29]);
                            $("#probdate").val(arr[30]);


                        } else {
                            $('#form')[0].reset();
                            $('#userImg').html("");
                        }
                    }
                });

            } else {
                alert("Please select Employee ID!");
            }
        }


        // 👇️ Get User Address
        function getAddress(id) {
            $.ajax({
                type: 'POST',
                url: '../Controller/emp_profile.php?request=getAddress&aid=' + id,
                success: function(data) {
                    $("#naddress").val(data);
                }
            });
        }

        // 👇️ Get User's Personal Address
        function getPAddress(id) {
            $.ajax({
                type: 'POST',
                url: '../Controller/emp_profile.php?request=getAddress&aid=' + id,
                success: function(data) {
                    $("#paddress").val(data);
                }
            });
        }

        // 👇️ Get User's Personal Address
        function getDepartment(id) {
            $.ajax({
                type: 'POST',
                url: '../Controller/emp_profile.php?request=getDepartment&did=' + id,
                success: function(data) {
                    $("#pemp").val(data);
                }
            });
        }


        // 👇️ Get Employee Grade,Employee Type and Position
        function getPositionAndGread(id, emptyid, maritalid) {
            $.ajax({
                type: 'POST',
                url: '../Controller/emp_profile.php?request=getPOSIandGradefromID&id=' + id + '&emptyid=' +
                    emptyid + '&maritalid=' + maritalid,
                success: function(data) {
                    var arr = data.split("#");
                    $("#grade").val(arr[0]);
                    $("#dept").val(arr[1]);
                    $("#emptype").val(arr[2]);
                    $("#marstatus").val(arr[3]);
                }
            });
        }

        // 👇️ Notification View Status Update 
        function getrowid(id) {

            var url = "../Controller/emp_profile.php?request=viewnotification&notid=" + id;

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

            var url = "../Controller/emp_profile.php?request=getnotificationcount";

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

            var url = "../Controller/emp_profile.php?request=getnotification";

            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    $('#abcd').html(data);
                }
            });
        }

        // 👇️ Update Details
        function UpdateWantedDetails() {
            var id = document.getElementById('uid').value;
            var email = document.getElementById('email').value;

            var url = "../Controller/emp_profile.php?request=updateuser&id=" + id + "&email=" + email;

            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {

                    if (data == "1") {
                        sweetalert("success", "Success", "Data Update Successfully!");
                        searchByID();
                    } else {
                        sweetalert("error", "Error", "Request Error!");
                    }
                }
            });
        }


        // 👇️ Load Profile Image
        var loadFile = function(event) {
            var output = document.getElementById('userImg');
            var src = document.getElementById("file-input");
            output.src = URL.createObjectURL(event.target.files[0]);
            output.onload = function() {
                URL.revokeObjectURL(output.src) // free memory
            }
        };
    </script>
</head>

<body>
    <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->

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
                                <div class="footer"><a href="../Views/emp_notification.php">See all notifications</a>
                                </div>
                            </div>
                        </div>
                        <div class="dropdown">
                            <a class="dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img id="avtimg" class="avatar" alt=""></a>
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
                                    <i class="ik ik-file-text bg-blue"></i>
                                    <div class="d-inline">
                                        <h5>Profile</h5>
                                        <span>Personal Details of Employee</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <nav class="breadcrumb-container" aria-label="breadcrumb">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">
                                            <a href="../home.php"><i class="ik ik-home"></i></a>
                                        </li>
                                        <li class="breadcrumb-item active" aria-current="page">Profile</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-4 col-md-5">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center">
                                        <img id="userImg" class="rounded-circle" width="150"></img><br><br>
                                        <div>
                                            <form id="form2" action="../Controller/emp_profile.php" method="POST" enctype="multipart/form-data">
                                                <div class="image-upload">
                                                    <label for="file-input">
                                                        <img src="../img/camera.png" />
                                                    </label>

                                                    <input id="file-input" name="file-input" type="file" accept="image/*" onchange="loadFile(event)" />&nbsp;&nbsp;
                                                    <button name="btnupload" id="btnupload" title="Upload Image" style=" border-radius: 4px; width: 30px; height: 23px; background-color: #ffffff;"><i class="fa fa-upload" style="float: next; cursor: pointer"></i></button>

                                                </div>
                                            </form>
                                            <?php

                                            if (isset($_GET['msg']) && $_GET['msg'] == 1) {
                                            ?> <script>
                                                    sweetalert("warning", "Warning", "Please Add Image!");
                                                </script><?php
                                                        } else if (isset($_GET['msg']) && $_GET['msg'] == 2) {
                                                            ?> <script>
                                                    sweetalert("success", "Success", "Image Upload Successfully!");
                                                </script><?php
                                                        } else if (isset($_GET['msg']) && $_GET['msg'] == 3) {
                                                            ?> <script>
                                                    sweetalert("error", "Error", "Image Upload Unsuccessfully!");
                                                </script><?php
                                                        } else if (isset($_GET['msg']) && $_GET['msg'] == 4) {
                                                            ?> <script>
                                                    sweetalert("warning", "Warning", "Files Have Unknown File Formats!");
                                                </script><?php
                                                        }
                                                            ?>

                                        </div>
                                        <h4 class="card-title mt-10" id="pname"></h4>
                                        <p class="card-subtitle" id="pposition"></p>
                                    </div>
                                </div>
                                <hr class="mb-0">
                                <div class="card-body">
                                    <small class="text-muted d-block">ID </small>
                                    <h6><?php echo $_SESSION["uid"] ?></h6>
                                    <input type="text" name="uid" id="uid" value="<?php echo $_SESSION["uid"] ?>" hidden="hidden">
                                    <small class="text-muted d-block pt-10">Employee No</small>
                                    <h6 id="jc"></h6>
                                    <small class="text-muted d-block pt-10">EPF Number</small>
                                    <h6 id="epf"></h6>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8 col-md-7">
                            <div class="card">
                                <ul class="nav nav-pills custom-pills" id="pills-tab" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="pills-timeline-tab" data-toggle="pill" href="#current-month" role="tab" aria-controls="pills-timeline" aria-selected="true">Profile</a>
                                    </li>
                                </ul>
                                <div class="tab-content" id="pills-tabContent">
                                    <div class="tab-pane fade show active" id="current-month" role="tabpanel" aria-labelledby="pills-timeline-tab">
                                        <div class="card-body">
                                            <div class="profiletimeline mt-0">

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="example-name">Full Name</label>
                                                            <input type="text" class="form-control" name="fname" id="fname" disabled="disabled">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="example-name">NIC / Passport No</label>
                                                            <input type="text" class="form-control" name="nic" id="nic" disabled="disabled">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="example-name">Mobile Phone No</label>
                                                            <input type="text" class="form-control" name="mobp" id="mobp" pattern="[0-9]{10}" title="Enter valid phone number!" disabled="disabled">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="example-name">Land Phone No</label>
                                                            <input type="text" class="form-control" name="landp" id="landp" pattern="[0-9]{10}" title="Enter valid phone number!" disabled="disabled">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="example-name">Date of Birth</label>
                                                            <input type="text" class="form-control" name="dob" id="dob" disabled="disabled">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="example-name">Email</label>
                                                            <input type="text" class="form-control" name="email" id="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" title="Enter valid email address">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="exampleTextarea1">Address</label>
                                                            <textarea class="form-control" id="naddress" name="naddress" rows="2" disabled="disabled"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="exampleTextarea1">Permanent Address</label>
                                                            <textarea class="form-control" id="paddress" name="paddress" rows="2" disabled="disabled"></textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="example-name">Designation</label>
                                                            <input type="text" class="form-control" name="desig" id="desig" disabled="disabled">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="example-name">Marital Status</label>
                                                            <input type="text" class="form-control" name="marstatus" id="marstatus" disabled="disabled">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="example-name">Department</label>
                                                            <input type="text" class="form-control" name="pemp" id="pemp" disabled="disabled">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="example-name">Branch</label>
                                                            <input type="text" class="form-control" name="dept" id="dept" disabled="disabled">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="example-name">Grade</label>
                                                            <input type="text" class="form-control" name="grade" id="grade" disabled="disabled">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="example-name">Employee Type</label>
                                                            <input type="text" class="form-control" name="emptype" id="emptype" disabled="disabled">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="example-name">Employee Act</label>
                                                            <input type="text" class="form-control" name="empact" id="empact" disabled="disabled">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="example-name">Present Salary</label>
                                                            <input type="text" class="form-control" name="psal" id="psal" disabled="disabled">
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="example-name">Registered Date</label>
                                                            <input type="text" class="form-control" name="regdate" id="regdate" disabled="disabled">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="example-name">Probation Period End Date</label>
                                                            <input type="text" class="form-control" name="probdate" id="probdate" disabled="disabled">
                                                        </div>
                                                    </div>
                                                </div>

                                                <button class="btn btn-success" type="submit" id="updatedetails" name="updatedetails" onclick="UpdateWantedDetails()">Update
                                                    Profile</button>

                                            </div>
                                        </div>
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
    <script>
        window.jQuery || document.write('<script src="../src/js/vendor/jquery-3.3.1.min.js"><\/script>')
    </script>
    <script src="../node_modules/popper.js/dist/umd/popper.min.js"></script>
    <script src="../node_modules/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="../node_modules/perfect-scrollbar/dist/perfect-scrollbar.min.js"></script>
    <script src="../node_modules/screenfull/dist/screenfull.js"></script>
    <script src="../dist/js/theme.min.js"></script>
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
    <?php
    session_commit();
    ?>
</body>

</html>