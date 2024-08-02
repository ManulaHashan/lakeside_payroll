<?php
error_reporting(0);
include("../Contains/header.php");
include '../DB/DB.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Employee Leave Request | Apex Payroll</title>
    <link href="../Styles/Stylie.css" rel="stylesheet" type="text/css">
    <link href="../Styles/contains.css" rel="stylesheet" type="text/css">
    <script src="../JS/jquery-3.1.0.js"></script>
    <script src="../JS/photobooth_min.js"></script>
    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="../Images/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../Images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../Images/favicon/favicon-16x16.png">
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

    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
    <script type="text/javascript">
    (function() {
        emailjs.init("J8hxLdsmtgm0e-uFJ");
    })();
    </script>

    <script type="text/javascript">
    //Old email security token = 9271fec4-3341-4820-a48d-6dc37ffd3aa8


    window.onload = function() {
        $('#loading').hide();
        document.getElementById('fromdate').valueAsDate = new Date();
        document.getElementById('todate').valueAsDate = new Date();
        $('#half_slot').hide();
        $('#short_slot').hide();
        $('#nopay_slot').hide();
        $("#leave_app_btn").hide();
        $("#leave_dec_btn").hide();
        $("#leave_cnf_btn").hide();

    };

    $(document).ready(function() {
        loadTable();
    });
    $(document).ajaxStart(function() {
        $('#loading').show();
    }).ajaxStop(function() {
        $('#loading').hide();
    });

    function loadTable() {

        var url = "../Controller/emp_approve_leave.php?request=getleave&from=" + $("#fromdate").val() + "&to=" + $(
            "#todate").val() + "&decission=" + $("#lvdes").val();

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                $('#tdata').html(data);
            }
        });
    }

    function loadDataForFields(id) {

        $("#elid").val(id);

        var url = "../Controller/emp_approve_leave.php?request=getDataField&id=" + id;

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {

                var arr = data.split("#");
                $("#empid").val(arr[0]);
                $("#empname").val(arr[1]);
                $("#ltype").val(arr[2]);
                $("#reqdate").val(arr[3]);


                if (arr[2] == "Halfday Leave") {
                    $("#tslot_Half").val(arr[5]);
                    $("#nodays").val(arr[4]);
                } else if (arr[2] == "Short Leave") {
                    $("#tslot_Short").val(arr[5]);
                    $("#nodays").val(arr[4]);
                } else if (arr[2] == "Liue Leave") {
                    $("#tslot_Half").val(arr[5]);
                    $("#select_noofdays").val(arr[4]);
                } else if (arr[2] == "Company Leave") {
                    $("#tslot_Half").val(arr[5]);
                    $("#select_noofdays").val(arr[4]);
                } else if (arr[2] == "Nopay Leave") {
                    $("#tslot_Half").val(arr[5]);
                    $("#select_noofdays").val(arr[4]);
                } else {
                    $("#nodays").val(arr[4]);
                }

                leavetypeChange();
                leaveCounter();

                if (arr[6] == "1") {
                    $("#leave_app_btn").show();
                    $("#leave_dec_btn").show();
                    $("#leave_cnf_btn").hide();
                } else {
                    $("#leave_app_btn").hide();
                    $("#leave_dec_btn").hide();
                    $("#leave_cnf_btn").show();
                }

            }
        });
    }

    var Part = "";

    function leaveCounter() {

        var url = "../Controller/emp_approve_leave.php?request=getleavecount&userid=" + $("#empid").val();

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {

                $('#getshortleaves').show();
                $('#availableshortleaves').show();
                var arr = data.split("#");

                Part = arr[0];

                if (arr[0] == "B") {
                    $("#totalLeave").html("Total Leaves : <b>" + arr[5] + "</b>");
                    $("#getLeave_1").html("&nbsp;&nbsp;Number Of Casual Leaves Taken : <b>" + arr[1] +
                        "</b>");
                    $("#AvailableLeave_1").html("&nbsp;&nbsp;Available Casual Leaves : <b>" + arr[2] +
                        "</b>");
                    $("#getLeave_2").html("&nbsp;&nbsp;Number Of Annual Leaves Taken : <b>" + arr[3] +
                        "</b>");
                    $("#AvailableLeave_2").html("&nbsp;&nbsp;Available Annual Leaves : <b>" + arr[4] +
                        "</b>");
                    $("#getshortleaves").html("&nbsp;&nbsp;Number Of Short Leaves Taken : <b>" + arr[9] +
                        "</b>");
                    $("#availableshortleaves").html("&nbsp;&nbsp;Available Short Leaves : <b>" + arr[10] +
                        "</b>");
                    $("#nopayleaves").html("&nbsp;&nbsp;Nopay Leaves : <b>" + arr[6] + "</b>");

                    if (arr[8] == "1") {
                        $("#leave_leaves").html("&nbsp;&nbsp;Liue Leaves : <b>" + arr[7] + "</b>");
                        //$('#opt_2').show();
                    }
                } else if (arr[0] == "C") {
                    $("#totalLeave").html("Total Leaves : <b>" + arr[5] + "</b>");
                    $("#getLeave_1").html("&nbsp;&nbsp;Number Of Casual Leaves Taken : <b>" + arr[1] +
                        "</b>");
                    $("#AvailableLeave_1").html("&nbsp;&nbsp;Available Casual Leaves : <b>" + arr[2] +
                        "</b>");
                    $("#getLeave_2").html("&nbsp;&nbsp;Number Of Annual Leaves Taken : <b>" + arr[3] +
                        "</b>");
                    $("#AvailableLeave_2").html("&nbsp;&nbsp;Available Annual Leaves : <b>" + arr[4] +
                        "</b>");
                    $("#getshortleaves").html("&nbsp;&nbsp;Number Of Short Leaves Taken : <b>" + arr[9] +
                        "</b>");
                    $("#availableshortleaves").html("&nbsp;&nbsp;Available Short Leaves : <b>" + arr[10] +
                        "</b>");
                    $("#nopayleaves").html("&nbsp;&nbsp;Nopay Leaves : <b>" + arr[6] + "</b>");

                    if (arr[8] == "1") {
                        $("#leave_leaves").html("&nbsp;&nbsp;Liue Leaves : <b>" + arr[7] + "</b>");
                        //$('#opt_2').show();
                    }
                } else {
                    if (arr[9] == "Empty") {
                        $("#totalLeave").html("Total Leaves : <b>" + arr[5] + "</b>");

                    } else {
                        $("#totalLeave").html("Total Leaves : <b>" + arr[5] + "</b>");
                        $("#getLeave_1").html("&nbsp;&nbsp;Number Of Halfday Leaves Taken : <b>" + arr[1] +
                            "</b>");
                        $("#AvailableLeave_1").html("&nbsp;&nbsp;Available Halfday Leaves : <b>" + arr[2] +
                            "</b>");
                        $("#getLeave_2").html("&nbsp;&nbsp;Number Of Short Leaves Taken : <b>" + arr[3] +
                            "</b>");
                        $("#AvailableLeave_2").html("&nbsp;&nbsp;Available Short Leaves : <b>" + arr[4] +
                            "</b>");
                        $("#nopayleaves").html("&nbsp;&nbsp;Nopay Leaves : <b>" + arr[6] + "</b>");
                        $('#getshortleaves').hide();
                        $('#availableshortleaves').hide();
                        // $('#opt_1').hide();
                        // $('#opt_3').hide();
                        // $('#opt_4').show();
                        // $('#opt_5').show();

                        if (arr[8] == "1") {
                            $("#leave_leaves").html("&nbsp;&nbsp;Liue Leaves : <b>" + arr[7] + "</b>");
                            //$('#opt_2').show();
                        }
                    }

                }


            }
        });
    }


    function LeaveUpdate() {

        var id = document.getElementById('elid').value;
        var leavetype = document.getElementById('ltype').value;
        var DAYValue = document.getElementById('nodays').value;
        var DAYValueToOther = document.getElementById('select_noofdays').value;

        if (leavetype == "Halfday Leave") {
            var timeSlot = document.getElementById('tslot_Half').value;
        } else if (leavetype == "Short Leave") {
            var timeSlot = document.getElementById('tslot_Short').value;
        } else if (leavetype == "Liue Leave") {
            var timeSlot = document.getElementById('tslot_Half').value;
        } else if (leavetype == "Company Leave") {
            var timeSlot = document.getElementById('tslot_Half').value;
        } else if (leavetype == "Nopay Leave") {
            var timeSlot = document.getElementById('tslot_Half').value;
        }


        if (id == "") {
            alert("Please Select Record On Table!");
        } else {
            var url = "../Controller/emp_approve_leave.php?request=updateleavedetails&eid=" + id + "&leavetype=" +
                leavetype + "&DAYValue=" + DAYValue + "&timeSlot=" + timeSlot + "&DAYValueToOther=" + DAYValueToOther;

            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {

                    if (data == "1") {
                        alert("Data Update Successfully!");
                        loadTable();
                        $("#empid").val("");
                        $("#empname").val("");
                        $("#ltype").val("");
                        $("#reqdate").val("");
                        $("#nodays").val("");
                        $("#tslot_Half").val("");
                        $("#tslot_Short").val("");
                        $("#select_noofdays").val("");
                        $("#elid").val("");
                    } else {
                        alert("Data Update Unsuccessfully!");
                    }

                }
            });
        }



    }


    function requestApprove() {

        var id = document.getElementById('elid').value;
        var employeeID = document.getElementById('empid').value;
        var LeaveType = document.getElementById('ltype').value;
        var DaysValue = document.getElementById('nodays').value;
        var DAYValueToOther = document.getElementById('select_noofdays').value;

        if (LeaveType == "Halfday Leave") {
            var timeSlot = document.getElementById('tslot_Half').value;
        } else if (LeaveType == "Short Leave") {
            var timeSlot = document.getElementById('tslot_Short').value;
        } else if (LeaveType == "Liue Leave") {
            var timeSlot = document.getElementById('tslot_Half').value;
        } else if (LeaveType == "Company Leave") {
            var timeSlot = document.getElementById('tslot_Half').value;
        } else if (LeaveType == "Nopay Leave") {
            var timeSlot = document.getElementById('tslot_Half').value;
        }

        if (id == "") {
            alert("Please Select Record On Table!");
        } else {
            let confirmAction = confirm("Are you sure this is correct?");

            if (confirmAction) {

                var url = "../Controller/emp_approve_leave.php?request=approveleave&eid=" + id + "&employeeID=" +
                    employeeID + "&LeaveType=" + LeaveType + "&DaysValue=" + DaysValue + "&DAYValueToOther=" +
                    DAYValueToOther;

                $.ajax({
                    type: 'POST',
                    url: url,
                    success: function(data) {

                        if (data == "1") {
                            // getLeaveMail(id);
                            alert("Leave Approve Successfully!");
                            loadTable();
                            $("#empid").val("");
                            $("#empname").val("");
                            $("#ltype").val("");
                            $("#reqdate").val("");
                            $("#nodays").val("");
                            $("#tslot_Half").val("");
                            $("#tslot_Short").val("");
                            $("#select_noofdays").val("");
                            $("#elid").val("");
                        } else if (data == "half") {
                            alert("Can't Approve This Leave. Available Leave Count is Over!");
                        } else if (data == "short") {
                            alert("Can't Approve This Leave. Available Leave Count is Over!");
                        } else {
                            alert("Leave Approve Unsuccessfully!");
                        }

                    }
                });
            }
        }

    }

    function getLeaveMail(id) {
        var url = "../Controller/emp_approve_leave.php?request=LeaveEmail&eid=" + id;

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {

                // alert(data)

                // sendEmail(data);

            }
        });
    }

    function sendEmail(bodydata) {

        var subject = "Satlo Industries - Employee's Leave Notification";
        // var data = bodydata;

        var send_to =
            "rasika@satloi.com, dulip@satloi.com, arjunay@satloi.com, sureshk@satloi.com, hr@satloi.com,  stores@satloi.com, roshan.satloi@gmail.com";


        var obj = JSON.parse(bodydata);

        var obj_email = obj["Email"];

        var leave_person_fname = "";
        var leave_person = "";
        var leave_person_epf = "";
        var leave_typs = "";
        var leave_dates = "";
        var leave_approve = "";
        var leave_approve_call = "";
        var approve_date = "";

        //Email
        obj_email.forEach(function(email_details) {
            leave_person_fname = email_details["Leave_Person_Fname"];
            leave_person = email_details["Leave_Person"];
            leave_person_epf = email_details["Leave_Person_EPF"];
            leave_typs = email_details["Leave_TYP"];
            leave_dates = email_details["Leave_DATE"];
            leave_approve = email_details["Leave_Approve"];
            leave_approve_call = email_details["Leave_Approve_Call"];
            approve_date = email_details["Approve_Date"];
        });

        //Email template parameters
        var data_log = {
            to_email: send_to,
            subject: subject,
            leavePerson_fname: leave_person_fname,
            leavePerson: leave_person,
            leavePerson_epfno: leave_person_epf,
            leave_typ: leave_typs,
            leave_date: leave_dates,
            approve_person: leave_approve,
            approve_person_call: leave_approve_call,
            app_date: approve_date
        };

        emailjs.send("service_j3mgxta", "template_zyu2wma", data_log).then(
            function(response) {
                // alert(response.status+"###"+response.text);
                if (response.text == "OK") {
                    alert("Mail Sent");
                } else {
                    alert("Mail Not Sent");
                }
            },
            function(error) {
                alert(error.text);
            }
        );





        // Email.send({
        //   SecureToken : "0af2e68b-cc64-48b4-bd0b-5161ecf2d30f",
        //   To : 'rasika@satloi.com, dulip@satloi.com, arjunay@satloi.com, sureshk@satloi.com, hr@satloi.com, admin@satloi.com, stores@satloi.com, roshan.satloi@gmail.com',
        //   From : "satlomalabefp@gmail.com",
        //   Subject : subject,
        //   Body: data,
        //   }).then(
        //     message => {
        //           if (message == "OK") 
        //           {
        //               alert("Mail Sent");
        //           }
        //      }
        //   ); 

    }


    function requestDecline() {

        var id = document.getElementById('elid').value;

        if (id == "") {
            alert("Please Select Record On Table!");
        } else {
            let confirmAction = confirm("Are you sure this is correct?");

            if (confirmAction) {

                var url = "../Controller/emp_approve_leave.php?request=declineleave&eid=" + id;

                $.ajax({
                    type: 'POST',
                    url: url,
                    success: function(data) {

                        if (data == "1") {
                            alert("Leave Decline Successfully!");
                            loadTable();
                            $("#empid").val("");
                            $("#empname").val("");
                            $("#ltype").val("");
                            $("#reqdate").val("");
                            $("#nodays").val("");
                            $("#tslot_Half").val("");
                            $("#tslot_Short").val("");
                            $("#select_noofdays").val("");
                            $("#elid").val("");
                        } else {
                            alert("Leave Decline Unsuccessfully!");
                        }

                    }
                });
            }
        }

    }

    function requestConfirm() {

        var id = document.getElementById('elid').value;
        var employeeID = document.getElementById('empid').value;
        var L_TYPE = document.getElementById('ltype').value;

        var arrANL = $("#AvailableLeave_2").text().split(":");
        var arrCSL = $("#AvailableLeave_1").text().split(":");
        var arrShort = $("#availableshortleaves").text().split(":");

        var availableANL = arrANL[1];
        var availableCSL = arrCSL[1];
        var avShort = arrShort[1];

        if (id == "") {
            alert("Please Select Record On Table!");
        } else {
            if (availableANL <= 0 && availableCSL <= 0) {
                if (Part == "A") {
                    if (L_TYPE == "Liue Leave" || L_TYPE == "Company Leave" || L_TYPE == "Nopay Leave") {
                        let confirmAction = confirm("Are you sure this is correct?");

                        if (confirmAction) {

                            var url = "../Controller/emp_approve_leave.php?request=confrmleave&eid=" + id +
                                "&employeeID=" + employeeID;

                            $.ajax({
                                type: 'POST',
                                url: url,
                                success: function(data) {

                                    if (data == "1") {
                                        // getRequestToAuthorizedPerson(id);
                                        // getRequestToSecondAuthorizedPerson(id);
                                        alert("Leave Confirm Successfully!");
                                        loadTable();
                                        $("#empid").val("");
                                        $("#empname").val("");
                                        $("#ltype").val("");
                                        $("#reqdate").val("");
                                        $("#nodays").val("");
                                        $("#tslot_Half").val("");
                                        $("#tslot_Short").val("");
                                        $("#select_noofdays").val("");
                                        $("#elid").val("");

                                    } else {
                                        alert("Leave Confirm Unsuccessfully!");
                                    }

                                }
                            });
                        }
                    } else {
                        $("#leave_cnf_btn").hide();
                        alert("Can't Confirm This Leave. Available Leave Count is Over!");

                    }
                } else {
                    if (avShort <= 0) {
                        if (L_TYPE == "Liue Leave" || L_TYPE == "Company Leave" || L_TYPE == "Nopay Leave") {
                            let confirmAction = confirm("Are you sure this is correct?");

                            if (confirmAction) {

                                var url = "../Controller/emp_approve_leave.php?request=confrmleave&eid=" + id +
                                    "&employeeID=" + employeeID;

                                $.ajax({
                                    type: 'POST',
                                    url: url,
                                    success: function(data) {

                                        if (data == "1") {
                                            // getRequestToAuthorizedPerson(id);
                                            // getRequestToSecondAuthorizedPerson(id);
                                            alert("Leave Confirm Successfully!");
                                            loadTable();
                                            $("#empid").val("");
                                            $("#empname").val("");
                                            $("#ltype").val("");
                                            $("#reqdate").val("");
                                            $("#nodays").val("");
                                            $("#tslot_Half").val("");
                                            $("#tslot_Short").val("");
                                            $("#select_noofdays").val("");
                                            $("#elid").val("");
                                        } else {
                                            alert("Leave Confirm Unsuccessfully!");
                                        }

                                    }
                                });
                            }
                        } else {
                            $("#leave_cnf_btn").hide();
                            alert("Can't Confirm This Leave. Available Leave Count is Over!");
                        }
                    } else {
                        if (L_TYPE == "Liue Leave" || L_TYPE == "Company Leave" || L_TYPE == "Nopay Leave" || L_TYPE ==
                            "Short Leave") {
                            let confirmAction = confirm("Are you sure this is correct?");

                            if (confirmAction) {

                                var url = "../Controller/emp_approve_leave.php?request=confrmleave&eid=" + id +
                                    "&employeeID=" + employeeID;

                                $.ajax({
                                    type: 'POST',
                                    url: url,
                                    success: function(data) {

                                        if (data == "1") {
                                            // getRequestToAuthorizedPerson(id);
                                            // getRequestToSecondAuthorizedPerson(id);
                                            alert("Leave Confirm Successfully!");
                                            loadTable();
                                            $("#empid").val("");
                                            $("#empname").val("");
                                            $("#ltype").val("");
                                            $("#reqdate").val("");
                                            $("#nodays").val("");
                                            $("#tslot_Half").val("");
                                            $("#tslot_Short").val("");
                                            $("#select_noofdays").val("");
                                            $("#elid").val("");
                                        } else {
                                            alert("Leave Confirm Unsuccessfully!");
                                        }

                                    }
                                });
                            }
                        } else {
                            $("#leave_cnf_btn").hide();
                            alert("Can't Confirm This Leave. Available Leave Count is Over!");
                        }
                    }

                }

            } else {
                let confirmAction = confirm("Are you sure this is correct?");

                if (confirmAction) {

                    var url = "../Controller/emp_approve_leave.php?request=confrmleave&eid=" + id + "&employeeID=" +
                        employeeID;

                    $.ajax({
                        type: 'POST',
                        url: url,
                        success: function(data) {

                            if (data == "1") {
                                // getRequestToAuthorizedPerson(id);
                                // getRequestToSecondAuthorizedPerson(id);
                                alert("Leave Confirm Successfully!");
                                loadTable();
                                $("#empid").val("");
                                $("#empname").val("");
                                $("#ltype").val("");
                                $("#reqdate").val("");
                                $("#nodays").val("");
                                $("#tslot_Half").val("");
                                $("#tslot_Short").val("");
                                $("#select_noofdays").val("");
                                $("#elid").val("");
                            } else {
                                alert("Leave Confirm Unsuccessfully!");
                            }

                        }
                    });
                }
            }
        }
    }

    function getRequestToAuthorizedPerson(id) {
        var url = "../Controller/emp_approve_leave.php?request=RequestToAuthorizedPerson&eid=" + id;

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {

                var arr = data.split("#@#");

                // sendApproveLeaveMail(arr[0],arr[1]);

            }
        });
    }


    function getRequestToSecondAuthorizedPerson(id) {
        var url = "../Controller/emp_approve_leave.php?request=RequestToSecondAuthorizedPerson&eid=" + id;

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {

                var arr = data.split("#@#");

                // sendApproveSecondLeaveMail(arr[0],arr[1]);

            }
        });
    }

    function sendApproveLeaveMail(ReqData, User) {

        var subject = "Satlo Industries - Employee's Leave Request";
        //var data = ReqData;

        var obj = JSON.parse(ReqData);

        var obj_email = obj["Email"];

        var auth_person = "";
        var leave_person_fname = "";
        var leave_person = "";
        var leave_person_epf = "";
        var leave_typs = "";
        var leave_dates = "";
        var leave_auths = "";
        var leave_emps = "";

        //Email
        obj_email.forEach(function(email_details) {
            auth_person = email_details["Auth_person"];
            leave_person_fname = email_details["Leave_Person_Fname"];
            leave_person = email_details["Leave_Person"];
            leave_person_epf = email_details["Leave_Person_EPF"];
            leave_typs = email_details["Leave_TYP"];
            leave_dates = email_details["Leave_DATE"];
            leave_auths = email_details["Leave_AUTH"];
            leave_emps = email_details["Leave_EMP"];
        });

        var USER_DATA = "";

        if (User == "10") {
            USER_DATA = "dulip@satloi.com";
        } else if (User == "2") {
            USER_DATA = "rasika@satloi.com";
        } else if (User == "7") {
            USER_DATA = "sureshk@satloi.com";
        } else if (User == "81") {
            USER_DATA = "hr@satloi.com";
        } else if (User == "112") {
            USER_DATA = "stores@satloi.com";
        }

        //Email template parameters
        var data_log = {
            to_email: USER_DATA,
            subject: subject,
            authperson_to: auth_person,
            leavePerson_fname: leave_person_fname,
            leavePerson: leave_person,
            leavePerson_epfno: leave_person_epf,
            leave_typ: leave_typs,
            leave_date: leave_dates,
            leave_auth: leave_auths,
            leave_EMP: leave_emps
        };

        emailjs.send("service_j3mgxta", "template_guj7nug", data_log).then(
            function(response) {
                // alert(response.status+"###"+response.text);
                if (response.text == "OK") {
                    alert("Mail Sent To Authorized Person");
                } else {
                    alert("Mail Not Sent");
                }
            },
            function(error) {
                alert(error.text);
            }
        );


        // Email.send({
        //   SecureToken : "0af2e68b-cc64-48b4-bd0b-5161ecf2d30f",
        //   To : USER_DATA,
        //   From : "satlomalabefp@gmail.com",
        //   Subject : subject,
        //   Body: data,
        //   }).then(
        //     message => {
        //           if (message == "OK") 
        //           {
        //               alert("Mail Sent To First Authorized Person");
        //           }
        //      }
        //   ); 

    }


    function sendApproveSecondLeaveMail(ReqData, User) {

        var subject = "Satlo Industries - Employee's Leave Request";
        //var data = ReqData;

        var obj = JSON.parse(ReqData);

        var obj_email = obj["Email"];

        var auth_person = "";
        var leave_person_fname = "";
        var leave_person = "";
        var leave_person_epf = "";
        var leave_typs = "";
        var leave_dates = "";
        var leave_auths = "";
        var leave_emps = "";

        //Email
        obj_email.forEach(function(email_details) {
            auth_person = email_details["Auth_person"];
            leave_person_fname = email_details["Leave_Person_Fname"];
            leave_person = email_details["Leave_Person"];
            leave_person_epf = email_details["Leave_Person_EPF"];
            leave_typs = email_details["Leave_TYP"];
            leave_dates = email_details["Leave_DATE"];
            leave_auths = email_details["Leave_AUTH"];
            leave_emps = email_details["Leave_EMP"];
        });

        var USER_DATA = "";

        if (User == "10") {
            USER_DATA = "dulip@satloi.com";
        } else if (User == "2") {
            USER_DATA = "rasika@satloi.com";
        } else if (User == "7") {
            USER_DATA = "sureshk@satloi.com";
        } else if (User == "81") {
            USER_DATA = "hr@satloi.com";
        } else if (User == "112") {
            USER_DATA = "stores@satloi.com";
        }


        //Email template parameters
        var data_log = {
            to_email: USER_DATA,
            subject: subject,
            authperson_to: auth_person,
            leavePerson_fname: leave_person_fname,
            leavePerson: leave_person,
            leavePerson_epfno: leave_person_epf,
            leave_typ: leave_typs,
            leave_date: leave_dates,
            leave_auth: leave_auths,
            leave_EMP: leave_emps
        };

        emailjs.send("service_j3mgxta", "template_guj7nug", data_log).then(
            function(response) {
                // alert(response.status+"###"+response.text);
                if (response.text == "OK") {
                    alert("Mail Sent To Second Authorized Person");
                } else {
                    alert("Mail Not Sent");
                }
            },
            function(error) {
                alert(error.text);
            }
        );


        // Email.send({
        //   SecureToken : "0af2e68b-cc64-48b4-bd0b-5161ecf2d30f",
        //   To : USER_DATA,
        //   From : "satlomalabefp@gmail.com",
        //   Subject : subject,
        //   Body: data,
        //   }).then(
        //     message => {
        //           if (message == "OK") 
        //           {
        //               alert("Mail Sent To Second Authorized Person");
        //           }
        //      }
        //   ); 

    }

    function leavetypeChange() {
        var leavetype = document.getElementById('ltype').value;
        var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        var dateF = document.getElementById('reqdate').value;
        var d = new Date(dateF);
        var dayName = days[d.getDay()];
        var nopay_days_value = document.getElementById('select_noofdays').value;

        if (leavetype == "Halfday Leave") {
            $('#nodays').val("0.5");
            $('#half_slot').show();
            $('#short_slot').hide();
            $('#nopay_slot').hide();
            $('#other_slot').show();
            $('#select_noofdays').val("");
        } else if (leavetype == "Short Leave") {
            $('#nodays').val("0.25");
            $('#half_slot').hide();
            $('#short_slot').show();
            $('#nopay_slot').hide();
            $('#other_slot').show();
            $('#select_noofdays').val("");
        } else if (leavetype == "") {
            $('#nodays').val("");
            $('#half_slot').hide();
            $('#short_slot').hide();
            $('#nopay_slot').hide();
            $('#other_slot').show();
            $('#select_noofdays').val("");
        } else if (leavetype == "Nopay Leave") {
            $('#nodays').val("");
            $('#short_slot').hide();
            $('#nopay_slot').show();
            $('#other_slot').hide();

            if (nopay_days_value == "0.5") {
                $('#half_slot').show();
            } else {
                $('#half_slot').hide();
            }
        } else if (leavetype == "Liue Leave") {
            $('#nodays').val("");
            $('#short_slot').hide();
            $('#nopay_slot').show();
            $('#other_slot').hide();

            if (nopay_days_value == "0.5") {
                $('#half_slot').show();
            } else {
                $('#half_slot').hide();
            }
        } else if (leavetype == "Company Leave") {
            $('#nodays').val("");
            $('#short_slot').hide();
            $('#nopay_slot').show();
            $('#other_slot').hide();

            if (nopay_days_value == "0.5") {
                $('#half_slot').show();
            } else {
                $('#half_slot').hide();
            }
        } else {
            if (dayName == "Saturday") {
                $('#nodays').val("1");
            } else {
                $('#nodays').val("1");
            }

            $('#half_slot').hide();
            $('#short_slot').hide();
            $('#nopay_slot').hide();
            $('#other_slot').show();
            $('#select_noofdays').val("");

        }
    }
    </script>
</head>

<body id="body" class="nav-md" style="background-color: white;">
    <?php include("../Contains/titlebar_dboard.php"); ?>
    <div class="container body">
        <div class="main_container">

            <?php
            $privs = array();
            $query = "select name from features where fid in (select features_fid from privillages where User_uid='" . $_SESSION["uid"] . "')";
            $res = Search($query);
            while ($result = mysqli_fetch_assoc($res)) {
                array_push($privs, $result["name"]);
            }

            ?>


            <!-- page content -->
            <div class="" style="width: 100%; margin: 1%;" role="main">


                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">


                        <div class="row x_title">
                            <div class="col-md-6">
                                <h3>Employee Leave Request <small>View & Approve Requested Leave</small></h3>
                            </div>
                        </div>
                        <br>
                        <p>Edit Leave Request</p>
                        <hr>
                        <div class="form-group">
                            <label for="exampleInputEmail1" id="totalLeave"></label>
                            <label for="exampleInputEmail1" id="getLeave_1"></label>
                            <label for="exampleInputEmail1" id="AvailableLeave_1"></label>
                            <label for="exampleInputEmail1" id="getLeave_2"></label>
                            <label for="exampleInputEmail1" id="AvailableLeave_2"></label>
                            <label for="exampleInputEmail1" id="getshortleaves"></label>
                            <label for="exampleInputEmail1" id="availableshortleaves"></label>
                            <label for="exampleInputEmail1" id="nopayleaves"></label>
                            <label for="exampleInputEmail1" id="leave_leaves"></label>
                        </div>

                        <table>
                            <tr>
                                <td height="35px;" width="200px;">
                                    <p class="form-label">Employee ID</p>
                                </td>
                                <td><input id="empid" type="text" name="empid" class="input-text" readonly="readonly">
                                </td>
                            </tr>
                            <tr>
                                <td height="35px;" width="200px;">
                                    <p class="form-label">Employee Name</p>
                                </td>
                                <td><input id="empname" type="text" name="empname" class="input-text"
                                        readonly="readonly"></td>
                            </tr>

                            <tr>
                                <td height="35px;" width="200px;">
                                    <p class="form-label">Date</p>
                                </td>
                                <td><input id="reqdate" type="date" name="reqdate" class="input-text"
                                        style="width: 182px" readonly="readonly"></td>
                            </tr>

                            <tr>
                                <td height="35px;" width="200px;">
                                    <p class="form-label">Leave Type</p>
                                </td>
                                <td><select name="ltype" id="ltype" class="select-basic" onchange="leavetypeChange()"
                                        style="width: 182px">
                                        <option value=""></option>
                                        <option value="Leave" id="opt_1">Leave</option>
                                        <option value="Liue Leave" id="opt_2">Liue Leave</option>
                                        <option value="Company Leave" id="opt_3">Company Leave</option>
                                        <option value="Short Leave" id="opt_4">Short Leave</option>
                                        <option value="Halfday Leave" id="opt_5">Halfday Leave</option>
                                        <option value="Nopay Leave" id="opt_6">Nopay Leave</option>
                                    </select>
                                </td>
                            </tr>

                            <tr id="nopay_slot">
                                <td height="35px;" width="200px;">
                                    <p class="form-label">Number of Days</p>
                                </td>
                                <td><select class="input-text" id="select_noofdays" name="select_noofdays"
                                        onchange="leavetypeChange()" style="width: 182px">
                                        <option value=""></option>
                                        <option value="1">1</option>
                                        <option value="0.5">0.5</option>
                                    </select>
                                </td>
                            </tr>


                            <tr id="other_slot">
                                <td height="35px;" width="200px;">
                                    <p class="form-label">Number of Days</p>
                                </td>
                                <td><input id="nodays" type="number" name="nodays" class="input-text"
                                        readonly="readonly" style="width: 182px"></td>
                            </tr>

                            <tr id="half_slot">
                                <td height="35px;" width="200px;">
                                    <p class="form-label">Time Slot</p>
                                </td>
                                <td><select class="input-text" id="tslot_Half" name="tslot_Half" style="width: 182px">
                                        <option value=""></option>
                                        <?php
                                        $query = "select half_slot_morning, half_slot_evening from settings_working_times where isactive='1'";
                                        $res = Search($query);
                                        while ($result = mysqli_fetch_assoc($res)) {
                                        ?>
                                        <option value="<?php echo ltrim($result["half_slot_morning"], "0"); ?>">
                                            <?php echo ltrim($result["half_slot_morning"], "0"); ?></option>
                                        <option value="<?php echo $result["half_slot_evening"]; ?>">
                                            <?php echo $result["epfno"]; ?><?php echo $result["half_slot_evening"]; ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>

                            <tr id="short_slot">
                                <td height="35px;" width="200px;">
                                    <p class="form-label">Time Slot</p>
                                </td>
                                <td><select class="input-text" id="tslot_Short" name="tslot_Short" style="width: 182px">
                                        <option value=""></option>
                                        <?php
                                        $query = "select short_morning, short_evening from settings_working_times where isactive='1'";
                                        $res = Search($query);
                                        while ($result = mysqli_fetch_assoc($res)) {
                                        ?>
                                        <option value="<?php echo ltrim($result["short_morning"], "0"); ?>">
                                            <?php echo ltrim($result["short_morning"], "0"); ?></option>
                                        <option value="<?php echo $result["short_evening"]; ?>">
                                            <?php echo $result["epfno"]; ?><?php echo $result["short_evening"]; ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <td><input type="button" class="btn btn-warning"
                                        style="margin-top: 10px; width: 150px; float: right;" name="leaveupdate"
                                        id="leaveupdate" onclick="LeaveUpdate()" value="Update"></td>

                                <?php if (in_array("Approve Requested Leave", $privs)) { ?>

                                <td id="leave_cnf_btn"><input type="button" class="btn btn-dark"
                                        style="margin-top: 10px; width: 150px; float: right;" name="confirmbtn"
                                        id="confirmbtn" style="width: 100px" onclick="requestConfirm()" value="Confirm">
                                </td>

                                <td id="leave_app_btn"><input type="button" class="btn btn-primary"
                                        style="margin-top: 10px; width: 150px; float: right;" name="requestapprove"
                                        id="requestapprove" onclick="requestApprove()" value="Approve"></td>

                                <td id="leave_dec_btn"><input type="button" class="btn btn-danger"
                                        style="margin-top: 10px; width: 150px; float: right;" name="requestdecline"
                                        id="requestdecline" onclick="requestDecline()" value="Decline"></td>

                                <?php } ?>

                            </tr>

                        </table>
                        <input id="elid" type="text" name="elid" class="input-text" hidden="hidden">
                        <hr><br>
                        <p>View & Approve Leave Request</p>
                    </div>
                </div>
                <div>
                    <table>
                        <tr>
                            <td>From Date :&nbsp;</td>
                            <td><input id="fromdate" type="date" name="fromdate" class="input-text" style="width: 182px"
                                    onchange="loadTable()"></td>
                            <td>&nbsp;&nbsp;To Date :&nbsp;</td>
                            <td><input id="todate" type="date" name="todate" class="input-text" style="width: 182px"
                                    onchange="loadTable()"></td>
                            <td>&nbsp;&nbsp;Decision :&nbsp;</td>
                            <td><select name="lvdes" id="lvdes" class="select-basic" onchange="loadTable()"
                                    style="width: 182px">
                                    <option value="NC">Not-Confirmed</option>
                                    <option value="0">Pending</option>
                                    <option value="1">Approved</option>
                                    <option value="2">Declined</option>
                                </select>
                            </td>
                            <td>&nbsp;&nbsp;</td>
                            <td>Leave Not-Confirmed :&nbsp;&nbsp;<input type="text"
                                    style="width: 40px; background-color: #81b2c9; border: none;"
                                    readonly="true">&nbsp;&nbsp;</td>
                            <td>Decision Pending :&nbsp;&nbsp;<input type="text"
                                    style="width: 40px; background-color: #DAA520; border: none;"
                                    readonly="true">&nbsp;&nbsp;</td>
                            <td>Leave Approved :&nbsp;&nbsp;<input type="text"
                                    style="width: 40px; background-color: #ff0000; border: none;"
                                    readonly="true">&nbsp;&nbsp;</td>
                            <td>Leave Declined :&nbsp;&nbsp;<input type="text"
                                    style="width: 40px; background-color: #47b833; border: none;"
                                    readonly="true">&nbsp;&nbsp;</td>
                        </tr>
                    </table>
                </div><br>
                <div id="tdata" style="height: 525px; overflow-y: scroll;width: 1500px;"></div>
            </div>
        </div>
    </div>

</body>
<?php include("../Contains/footer.php");
?>

</html>