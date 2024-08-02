<?php
    session_start();

    include '../DB/DB.php';
    $DB = new Database();
    date_default_timezone_set('Asia/Colombo');

    if(!isset($_SESSION["uid"])){
        header("Location: ../");
    } 
?>
<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Leave Request | Employee Profile</title>
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
        <script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>

        <script type="text/javascript">

            window.onload = function() {
                document.getElementById('pdate').valueAsDate = new Date();
                document.getElementById('fdate').valueAsDate = new Date();
                document.getElementById('tdate').valueAsDate = new Date();
                searchByID();
                loadTable();
                leaveCounter();
                loadNotifiation();
                noticount();
                leavetypeChange();
                emptyTable();
                $('#half_slot').hide();
                $('#short_slot').hide();
                $('#opt_2').hide();
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
                        url: '../Controller/emp_leave_req.php?request=getEmpsbyID',
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


            
            // 👇️ View Requested Leave Table with Date Range
            function loadTable() {

                var url = "../Controller/emp_leave_req.php?request=getleave&frmdate=" + $('#fdate').val() + "&tod=" + $('#tdate').val() + "&decission="+ $("#lvdes").val();

                $.ajax({
                    type: 'POST',
                    url: url,
                    success: function(data) {
                        $('#tdata').html(data);
                    }
                });
            }
            
            // 👇️ Notification View Status Update 
            function getrowid(id){

                var url = "../Controller/emp_leave_req.php?request=viewnotification&notid=" + id;

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

                var url = "../Controller/emp_leave_req.php?request=getnotificationcount";

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

                var url = "../Controller/emp_leave_req.php?request=getnotification";

                $.ajax({
                    type: 'POST',
                    url: url,
                    success: function(data) {
                        $('#abcd').html(data);
                    }
                });
            }

            // 👇️ Get Leave Count
            function leaveCounter() {

                var url = "../Controller/emp_leave_req.php?request=getleavecount";

                $.ajax({
                    type: 'POST',
                    url: url,
                    success: function(data) {

                        var arr = data.split("#");

                        if (arr[0] == "B") 
                        {
                            $("#totalLeave").html("Total Leaves : <b>"+arr[5]+"</b>");
                            $("#getLeave_1").html("&nbsp;&nbsp;Casual Leaves Count Taken By You : <b>"+arr[1]+"</b>");
                            $("#AvailableLeave_1").html("&nbsp;&nbsp;Available Casual Leaves : <b>"+arr[2]+"</b>");
                            $("#getLeave_2").html("&nbsp;&nbsp;Annual Leaves Count Taken By You : <b>"+arr[3]+"</b>");
                            $("#AvailableLeave_2").html("&nbsp;&nbsp;Available Annual Leaves : <b>"+arr[4]+"</b>");
                            $("#nopayleaves").html("&nbsp;&nbsp;Nopay Leaves : <b>"+arr[6]+"</b>");
                            
                            if (arr[8] == "1") 
                            {
                                $("#leave_leaves").html("&nbsp;&nbsp;Liue Leaves : <b>"+arr[7]+"</b>");
                                $('#opt_2').show();
                            }
                        }
                        else if (arr[0] == "C") 
                        {
                            $("#totalLeave").html("Total Leaves : <b>"+arr[5]+"</b>");
                            $("#getLeave_1").html("&nbsp;&nbsp;Casual Leaves Count Taken By You : <b>"+arr[1]+"</b>");
                            $("#AvailableLeave_1").html("&nbsp;&nbsp;Available Casual Leaves : <b>"+arr[2]+"</b>");
                            $("#getLeave_2").html("&nbsp;&nbsp;Annual Leaves Count Taken By You : <b>"+arr[3]+"</b>");
                            $("#AvailableLeave_2").html("&nbsp;&nbsp;Available Annual Leaves : <b>"+arr[4]+"</b>");
                            $("#nopayleaves").html("&nbsp;&nbsp;Nopay Leaves : <b>"+arr[6]+"</b>");
                            
                            if (arr[8] == "1") 
                            {
                                $("#leave_leaves").html("&nbsp;&nbsp;Liue Leaves : <b>"+arr[7]+"</b>");
                                $('#opt_2').show();
                            }
                        }
                        else
                        {
                            if (arr[9] == "Empty") 
                            {
                                $("#totalLeave").html("Total Leaves : <b>"+arr[5]+"</b>");
                                $('#btnadd').prop('disabled', 'disabled');

                            }
                            else
                            {
                                document.getElementById("btnadd").disabled = false;
                                $("#totalLeave").html("Total Leaves : <b>"+arr[5]+"</b>");
                                $("#getLeave_1").html("&nbsp;&nbsp;Halfday Leaves Count Taken By You : <b>"+arr[1]+"</b>");
                                $("#AvailableLeave_1").html("&nbsp;&nbsp;Available Halfday Leaves : <b>"+arr[2]+"</b>");
                                $("#getLeave_2").html("&nbsp;&nbsp;Short Leaves Count Taken By You : <b>"+arr[3]+"</b>");
                                $("#AvailableLeave_2").html("&nbsp;&nbsp;Available Short Leaves : <b>"+arr[4]+"</b>");
                                $("#nopayleaves").html("&nbsp;&nbsp;Nopay Leaves : <b>"+arr[6]+"</b>");
                                
                                $('#opt_1').hide();
                                $('#opt_3').hide();
                                $('#opt_4').show();
                                $('#opt_5').show();

                                if (arr[8] == "1") 
                                {
                                    $("#leave_leaves").html("&nbsp;&nbsp;Liue Leaves : <b>"+arr[7]+"</b>");
                                    $('#opt_2').show();
                                }
                            }
                            
                        }
                        
                        
                    }
                });
            }

           // 👇️ Get Employee wise Halfday Slots
            function getEmployeeWiseHalfSlots() 
            {
                var DATE = document.getElementById('datefrom').value;

                var url = "../Controller/emp_leave_req.php?request=getEmpWiseHalfSlots&date=" + DATE;
                $.ajax({
                    type: 'POST',
                    url: url,
                    success: function(data) 
                    {
                        $('#tslot_Half').html(data); 
                    }
                });
            }
            
            // 👇️ Get Employee wise Shortleave Slots
            function getEmployeeWiseShortSlots() 
            {
                var DATE = document.getElementById('datefrom').value;

                var url = "../Controller/emp_leave_req.php?request=getEmpWiseShortSlots&date=" + DATE;
                $.ajax({
                    type: 'POST',
                    url: url,
                    success: function(data) 
                    {
                        $('#tslot_Short').html(data);
                    }
                });
            }

            // 👇️ Leave Type Change Function
            function leavetypeChange()
            {
                 var leavetype = document.getElementById('ltype').value;
                 var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                 var dateF = document.getElementById('datefrom').value;
                 var d = new Date(dateF);
                 var dayName = days[d.getDay()];

                 if (leavetype == "Halfday Leave") 
                 {
                     $('#days').prop('disabled', 'disabled');
                     $('#days').val("0.5");
                     $('#half_slot').show();
                     $('#short_slot').hide();
                 }
                 else if (leavetype == "Short Leave") 
                 {
                     $('#days').prop('disabled', 'disabled');
                     $('#days').val("0.25");
                     $('#half_slot').hide();
                     $('#short_slot').show();
                 }
                 else
                 {
                     if (dayName == "Saturday") 
                     {
                        $('#days').prop('disabled', 'disabled');
                        $('#days').val("1");
                     }
                     else
                     {
                        $('#days').prop('disabled', 'disabled');
                        $('#days').val("1");
                     }

                     $('#half_slot').hide();
                     $('#short_slot').hide();
    
                 }

            }
            
            // 👇️ Request a Leave Function
            function requestLeave() {

                if ($('#datefrom').val() !="" && $('#days').val() != "") 
                {
                    if ($('#datefrom').val() >= $('#pdate').val()) 
                    {
                        let confirmAction = confirm("Are you sure this is correct?");

                        if (confirmAction) {

                          var url = "../Controller/emp_leave_req.php?request=leaverequest&leavetype=" + $('#ltype').val() + "&from=" + $('#datefrom').val() + "&day=" + $('#days').val();

                            $.ajax({
                                type: 'POST',
                                url: url,
                                success: function(data) {
                                    
                                    if (data == "1") 
                                    {
                                        sweetalert("warning","Warning", "You Already Requested This Leave!");
                                    }
                                    else if (data == "2") 
                                    {
                                        sweetalert("success","Success", "Request Sent Successfully!");
                                        $('#datefrom').val("");
                                        $('#days').val("");
                                        loadTable();
                                    }
                                    else
                                    {
                                        sweetalert("error","Error", "Request Error!");
                                    }

                                    
                                }
                            });

                        } 
                    }
                    else
                    {
                        sweetalert("error","Error", "You Can't Request This Date!");
                    }
                }
                else
                {
                    sweetalert("warning","Warning", "Please Select Date!");
                }
                
            }

            // 👇️ Refresh Fields
            function refresh() {
                $('#datefrom').val("");
                $('#days').val("");
                loadTable();
            }

            var rowid = 1;
            var totalval = 0;
            var itemList = [];
            var Leave_No = "";

            function addItem() {

                var leavetype = document.getElementById('ltype').value;

                if ($('#ltype').val() == "" || $('#datefrom').val() == "") 
                {
                    sweetalert("warning","Warning", "Please Fill All Details!");
                }
                else
                {
                     if (leavetype == "Halfday Leave") 
                     {
                        var lid = rowid;
                        var ltype = $('#ltype').val(); 
                        var ldate = $('#datefrom').val(); 
                        var lnom = $('#days').val();
                        var tsh = $('#tslot_Half').val();

                        Leave_No = "1";
                        
                        

                        var x = itemList.indexOf((Leave_No+""+ldate.replace(/[^a-zA-Z0-9]/g, "")));
                        if(x == -1)
                        {
                            itemList.push(Leave_No+""+ldate.replace(/[^a-zA-Z0-9]/g, ""));
                            var tr = "<tbody><tr id='midtr" + lid + "'><td><center>" + lid + "</center></td>";
                            tr += "<td>" + ltype + "</td>";
                            tr += "<td>" + ldate + "</td>";
                            tr += "<td>" + tsh + "</td>";
                            tr += "<td align='right'>" + parseFloat(lnom).toFixed(2) + "</td>";
                            tr += "<td><center><img src='../img/reject.png' onclick='removeItem(" + lid + ","+lnom+","+Leave_No+""+ldate.replace(/[^a-zA-Z0-9]/g, "")+")' style='cursor:pointer; width:35px;'></center><input type='text' name='midI" + lid + "' value='"+lid+"@"+ltype+"@" + ldate + "@"+ lnom +"@"+ tsh +"' style='width:100px' hidden='hidden'></td></tr></tbody>";

                            $('#leaveTable').append(tr);

                            total(parseFloat(lnom));

                            ++rowid;

                            emptyTable();
                        }
                        else
                        {
                            sweetalert("warning","Warning", "This Leave Already Exists in The Table!");
                        }

                        
                     }
                     else if (leavetype == "Short Leave") 
                     {
                        var lid = rowid;
                        var ltype = $('#ltype').val(); 
                        var ldate = $('#datefrom').val(); 
                        var lnom = $('#days').val();
                        var tssh = $('#tslot_Short').val();

                        Leave_No = "2";

                        

                        var x = itemList.indexOf((Leave_No+""+ldate.replace(/[^a-zA-Z0-9]/g, "")));
                        if(x == -1)
                        {
                            itemList.push(Leave_No+""+ldate.replace(/[^a-zA-Z0-9]/g, ""));
                            var tr = "<tbody><tr id='midtr" + lid + "'><td><center>" + lid + "</center></td>";
                            tr += "<td>" + ltype + "</td>";
                            tr += "<td>" + ldate + "</td>";
                            tr += "<td>" + tssh + "</td>";
                            tr += "<td align='right'>" + parseFloat(lnom).toFixed(2) + "</td>";
                            tr += "<td><center><img src='../img/reject.png' onclick='removeItem(" + lid + ","+lnom+","+Leave_No+""+ldate.replace(/[^a-zA-Z0-9]/g, "")+")' style='cursor:pointer; width:35px;'></center><input type='text' name='midI" + lid + "' value='"+lid+"@"+ltype+"@" + ldate + "@"+ lnom +"@"+ tssh +"' style='width:100px' hidden='hidden'></td></tr></tbody>";

                            $('#leaveTable').append(tr);

                            total(parseFloat(lnom));

                            ++rowid;

                            emptyTable();
                        }
                        else
                        {
                            sweetalert("warning","Warning", "This Leave Already Exists in The Table!");
                        }

                        
                     }
                     else
                     {
                        var lid = rowid;
                        var ltype = $('#ltype').val(); 
                        var ldate = $('#datefrom').val(); 
                        var lnom = $('#days').val();
                        var Non_val = "None";

                        if (leavetype == "Leave") 
                        {
                           Leave_No = "3";
                        }
                        else if (leavetype == "Liue Leave") 
                        {
                           Leave_No = "4";
                        }
                        else if (leavetype == "Company Leave") 
                        {
                           Leave_No = "5";     
                        }

                        var x = itemList.indexOf((Leave_No+""+ldate.replace(/[^a-zA-Z0-9]/g, "")));
                        if(x == -1)
                        {
                            itemList.push(Leave_No+""+ldate.replace(/[^a-zA-Z0-9]/g, ""));
                            var tr = "<tbody><tr id='midtr" + lid + "'><td><center>" + lid + "</center></td>";
                            tr += "<td>" + ltype + "</td>";
                            tr += "<td>" + ldate + "</td>";
                            tr += "<td>" + Non_val + "</td>";
                            tr += "<td align='right'>" + parseFloat(lnom).toFixed(2) + "</td>";
                            tr += "<td><center><img src='../img/reject.png' onclick='removeItem(" + lid + ","+lnom+","+Leave_No+""+ldate.replace(/[^a-zA-Z0-9]/g, "")+")' style='cursor:pointer; width:35px;'></center><input type='text' name='midI" + lid + "' value='"+lid+"@"+ltype+"@" + ldate + "@"+ lnom +"@"+ Non_val +"' style='width:100px' hidden='hidden'></td></tr></tbody>";

                            $('#leaveTable').append(tr);

                            total(parseFloat(lnom));

                            ++rowid;

                            emptyTable();
                        }
                        else
                        {
                           sweetalert("warning","Warning", "This Leave Already Exists in The Table!");
                        }

                     }
                }

                

                
            }

            function removeItem(lid,nomber,ArrData) 
            {
                var x = confirm("Are you sure you want to remove this item?");
                if (x) {
                    
                    for( var i = 0; i < itemList.length; i++){ 
    
                        if ( itemList[i] == ArrData) 
                        { 
                            itemList.splice(i, 1); 
                        }
                    }

                    $('#midtr' + lid).remove();

                    totalval = parseFloat($('#totval').val()) - nomber;
                    $('#totval').val(numeral(totalval).format('0.0'));
                    $('#totamount').html(numeral(totalval).format('0.0'));

                    emptyTable();  
                }
            }

            function total(a) {
                totalval = parseFloat($('#totval').val()) + a;
                $('#totval').val(numeral(totalval).format('0.0'));
                $('#totamount').html(Math.floor(totalval * 100) / 100);
            }

            function emptyTable(){

                var x = document.getElementById("leaveTable").rows.length;

                if (x == 2) 
                {
                    $('#btnsubmit').prop('disabled', 'disabled');
                }
                else
                {
                     document.getElementById("btnsubmit").disabled = false;
                }
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
                <div class="app-sidebar colored">
                    <div class="sidebar-header">
                        <a class="header-brand" href="../home.php">
                            <div class="logo-img">
                               <img src="../derana_circle.png" class="header-brand-img" style="width: 36px;" alt="lavalite"> 
                            </div>
                            <span class="text" style="font-size: 17px;">&nbsp;Derana Employees</span>
                        </a>
                        <button type="button" class="nav-toggle"><i data-toggle="expanded" class="ik ik-toggle-right toggle-icon"></i></button>
                        <button id="sidebarClose" class="nav-close"><i class="ik ik-x"></i></button>
                    </div>
                    
                    <div class="sidebar-content">
                        <div class="nav-container">
                            <nav id="main-menu-navigation" class="navigation-main">
                                <div class="nav-lavel">Navigation</div>
                                <div class="nav-item active">
                                    <a href="../home.php"><i class="ik ik-bar-chart-2"></i><span>Dashboard</span></a>
                                </div>
                                <div class="nav-item has-sub">
                                    <a href="javascript:void(0)"><i class="ik ik-user"></i><span>Employee</span></a>
                                    <div class="submenu-content">
                                        <a href="../Views/emp_leave_req.php" class="menu-item"><i class="ik ik-watch"></i>Leave Request</a>
                                        <a href="../Views/emp_attendance_log.php" class="menu-item"><i class="ik ik-calendar"></i>Attendance Log</a>
                                    </div>
                                </div>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="main-content">
                    <div class="container-fluid">
                        <div class="page-header">
                            <div class="row align-items-end">
                                <div class="col-lg-8">
                                    <div class="page-header-title">
                                        <i class="ik ik-edit bg-blue"></i>
                                        <div class="d-inline">
                                            <h5>Leave Request</h5>
                                            <span>You can requesting your leave from this page</span>
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
                                            <li class="breadcrumb-item active" aria-current="page">Leave Request</li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header"><h3>Request a Leave</h3></div>
                                    <div class="card-body">
                                            <div class="form-group">
                                                <label for="exampleInputEmail1" id="totalLeave"></label>
                                                <label for="exampleInputEmail1" id="getLeave_1"></label>
                                                <label for="exampleInputEmail1" id="AvailableLeave_1"></label>
                                                <label for="exampleInputEmail1" id="getLeave_2"></label>
                                                <label for="exampleInputEmail1" id="AvailableLeave_2"></label>
                                                <label for="exampleInputEmail1" id="nopayleaves"></label>
                                                <label for="exampleInputEmail1" id="leave_leaves"></label>
                                            </div>

                                            <div class="form-group">
                                                <label for="exampleInputEmail1">Date</label>
                                                <input type="date" class="form-control" id="datefrom" name="datefrom" onchange="getEmployeeWiseHalfSlots();getEmployeeWiseShortSlots();">
                                            </div>

                                            <div class="form-group">
                                                <label for="exampleInputUsername1">Leave Type</label>
                                                <select class="form-control" id="ltype" name="ltype" onchange="leavetypeChange()">
                                                    <option value=""></option>
                                                    <option value="Leave" id="opt_1">Leave</option>
                                                    <option value="Liue Leave" id="opt_2">Liue Leave</option>
                                                    <option value="Company Leave" id="opt_3">Company Leave</option>
                                                    <option value="Short Leave" id="opt_4">Short Leave</option>
                                                    <option value="Halfday Leave" id="opt_5">Halfday Leave</option>
                                                </select>
                                            </div>

                                            <div class="form-group" id="half_slot">
                                                <label for="exampleInputUsername1">Time Slot</label>
                                                <select class="form-control" id="tslot_Half" name="tslot_Half"></select>
                                            </div>

                                            <div class="form-group" id="short_slot">
                                                <label for="exampleInputUsername1">Time Slot</label>
                                                <select class="form-control" id="tslot_Short" name="tslot_Short"></select>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label for="exampleInputPassword1">Number of Days</label>
                                                <input type="number" class="form-control" id="days" name="days">
                                            </div>
                                            <!-- <button type="submit" class="btn btn-primary mr-2" onclick="requestLeave()">Submit</button> -->
                                            <button type="submit" class="btn btn-primary mr-2" id="btnadd" onclick="addItem()">ADD</button>
                                            <button class="btn btn-light" onclick="refresh()">Cancel</button>

                                            <input type="date" name="pdate" id="pdate" hidden="hidden">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header d-block">
                                        <h3>Leave Table</h3>
                                    </div>
                                    <div class="card-body">
                                        <form action="../Controller/emp_leave_req.php" method="POST">
                                        <div class="table-responsive pt-3">
                                            <div id="bookdata">
                                                <table id='leaveTable' class='table table-bordered' ><thead>
                                                    <tr>
                                                        <th width='10%'>No</th> 
                                                        <th width='30%'>Leave Type</th>
                                                        <th width='10%'>Date</th>
                                                        <th width='10%'>Time Slot</th>
                                                        <th width='10%'>Day</th>
                                                        <th width='10%'></th>
                                                    </tr></thead>
                                                    <tfoot>
                                                    <tr>
                                                      <td colspan="4" align="right">Total Leaves</td>
                                                      <td align="right"><span id="totamount">0</span></td>
                                                      <td></td>
                                                    </tr>
                                                  </tfoot>
                                                </table>
                                            </div>
                                            <button type="submit" name="btnsubmit" id="btnsubmit" class="btn btn-lg btn-social btn-primary">Request</button>
                                        </div>

                                         <?php
                                                    
                                                    if ( isset($_GET['success']) && $_GET['success'] == 1 )
                                                    {
                                                         ?> <script>
                                                         sweetalert("success","Success", "Leave Request Successfully...");
                                                         </script><?php
                                                    }
                                                    else if ( isset($_GET['success']) && $_GET['success'] == 2 )
                                                    {
                                                         ?> <script>
                                                         sweetalert("error","Error", "Leave Request Unsuccessfully...");
                                                         </script><?php
                                                    }
                                                    else if ( isset($_GET['success']) && $_GET['success'] == 3 )
                                                    {
                                                         ?> <script>
                                                         sweetalert("warning","Warning", "Some Leave Days Already Exsist...");
                                                         </script><?php
                                                    }
                                                    
                                            ?>
                                      </form>
                                      <input type="text" name="totval" id="totval" value="0" hidden="hidden">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="card">
                                    <div class="card-header d-block">
                                        <h3>Requested Leave Table</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive-sm">
                                             <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="example-name">From Date</label>
                                                        <input type="date" class="form-control" name="fdate" id="fdate" onchange="loadTable()">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="example-name">To Date</label>
                                                        <input type="date" class="form-control" name="tdate" id="tdate" onchange="loadTable()">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="example-name">Decission</label>
                                                        <select name="lvdes" id="lvdes" class="form-control" onchange="loadTable()">
                                                            <option value="0">Pending</option>
                                                            <option value="1">Approved</option>
                                                            <option value="2">Declined</option>
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
                
                 <footer class="footer">
                    <div class="w-100 clearfix">
                        <span class="text-center text-sm-left d-md-inline-block">Copyright &copy;&nbsp;<?php echo date('Y'); ?>&nbsp;All Rights Reserved.</span>
                        <span class="float-none float-sm-right mt-1 mt-sm-0 text-center">Powered By Appex Software Solutions</span>
                    </div>
                </footer>
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

