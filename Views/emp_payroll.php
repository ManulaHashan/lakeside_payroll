<?php
error_reporting(0);
include("../Contains/header.php");
include '../DB/DB.php';

$SlipId = ""; // new
if (isset($_GET["slipID"])) {
    $SlipId = $_GET["slipID"];
    $UId = $_GET["uid"];
}
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Generate Salary | Apex Payroll</title>
    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="../Images/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../Images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../Images/favicon/favicon-16x16.png">
    <link href="../Styles/contains.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/css/sweet-alert.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/nprogress/nprogress.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/animate.css/animate.min.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/css/custom.min.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/iCheck/skins/flat/green.css" rel="stylesheet" type="text/css">
    <script src="../JS/jquery-3.1.0.js"></script>
    <script src="../JS/numeral.min.js"></script>

    <script type="text/javascript">
    window.onload = function() {
        setSpace();
        $('#loading').hide();
        var date = new Date();
        document.getElementById('apdate').valueAsDate = date;

        var SLIP_ID = document.getElementById('SLIP_ID').value;
        var U_ID = document.getElementById('U_ID').value;
        // loadTable();
        var a = localStorage.getItem("daysount");

        $('#daysLBL').hide();
        $('#npdaysLBL').hide();
        $('#totothcusLBL').hide();
        $('#salbnsLBL').hide();
        $('#updsalbtn').hide(); // new

        if (a == null) {
            $('#days').val("26");
        } else {
            $('#days').val(a);
        }

        if (SLIP_ID != "") {
            $('#marker').val(U_ID);
            setUID(U_ID);
            LoadSlipData();
        }

    };

    $(document).ajaxStart(function() {
        $('#loading').show();
    }).ajaxStop(function() {
        $('#loading').hide();
    });

    function setSpace() {
        var wheight = $(window).height();
        var bheight = $('#body').height();

        if (wheight > bheight) {
            var x = wheight - bheight - 18;
            $('#space').height(x);
        }
    }

    function setUID(value) {
        $('#uid').val(value);
        loadUser();
        leaveCounter(value);
    }

    function LoadSlipData() //new
    {
        var Slip_ID = document.getElementById('SLIP_ID').value;

        var url = '../Controller/emp_payroll.php?request=LoadSlipData&Slip_ID=' + Slip_ID;
        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {

                var arr = data.split("#");

                $('#date1').val(arr[19]);
                $('#date2').val(arr[20]);
                $('#totnp').val(arr[47]);

                getLoadSlip();
                $('#npdaysLBL').show();
                $('#npdaysLBL').html(arr[47]);

            }
        });
    }

    function setEpdNo() {
        var epfno = document.getElementById('jcode').value;

        var url = '../Controller/emp_payroll.php?request=getUIDFromEPFno&epfno=' + epfno;
        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {


                if (data == "0") {

                } else {
                    $('#marker').val(data);
                    setUID(data);
                }

            }
        });
    }

    var BasicSal = 0;
    var BudALLOWS = 0;
    var EmployeeTYPE = "";
    var EPF_ENTITLE_DATE = "";

    function loadUser() {
        $('#uid').val($('#uid').val());

        $.ajax({
            type: 'POST',
            url: '../Controller/emp_payroll.php?request=getDetailsfromUID&uid=' + $('#uid').val(),
            success: function(data) {
                var arr = data.split("#");
                // $("#eposi").html("&nbsp" + arr[1]);
                $("#eposi").html(arr[6]);
                $("#empact").html(arr[7]);
                // $("#egrade").html("&nbsp" + arr[2]);
                $("#esal").html(" Rs. " + numeral(arr[0]).format('0,0.00'));
                // $("#esaltype").html("&nbsp" + arr[3]);

                basic = parseFloat(arr[0]);

                var str = $.trim(arr[4]);
                $("#jcode").val(str);
                $("#epfno").html(arr[5]);
                $("#empact").val(arr[7]);

                EmployeeTYPE = arr[8];

                EPF_ENTITLE_DATE = arr[9];

                BasicSal = arr[0];

                var url = '../Controller/emp_payroll.php?request=getIncreasementsfromUID&uid=' + $('#uid')
                    .val();
                $.ajax({
                    type: 'POST',
                    url: url,
                    success: function(data) {
                        $("#salinc").html(data);
                        getIncreasements();
                        getAlowances($('#uid').val());
                    }
                });
            }
        });
    }



    function getAlowances(uid) {
        otherAllowance = 0;
        var url = '../Controller/emp_payroll.php?request=getAlowances&uid=' + uid;
        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {

                var arr = data.split("//");
                var x = 1;
                var tr = "";
                for (var i = 0; arr.length > i; ++i) {
                    if (arr[i].split(":")[1] !== "undefined") {
                        if (isOdd(x)) {
                            tr += "<tr>";
                        }

                        var attid = "";

                        if (arr[i].split(":")[0] == "Attendance Allowance") {
                            Allowance = parseFloat(arr[i].split(":")[1]);
                            attid = "id='attendanceAllowance'";
                        } else {
                            otherAllowance += parseFloat(arr[i].split(":")[1]);
                        }

                        tr += "<td class='form-label'>" + arr[i].split(":")[0] + " Rs.</td><td " + attid +
                            ">" + numeral(arr[i].split(":")[1]).format('0,0.00') +
                            "</td><td width='85px'></td>";




                        if (!isOdd(x)) {
                            tr += "</tr>";
                        }

                    }

                    ++x;
                }
                $("#detailsTable").html($("#detailsTable").html() + tr);
            }
        });
    }

    function isOdd(number) {
        let reminder = number % 2;
        if (reminder == 0) {
            return false;
        } else if (reminder == Math.round(reminder)) {
            return true;
        }
    }


    var EPFin = 0;
    var EPFded = 0;
    var ETFin = 0;

    function calculateETFnEPF(basicSalary) {
        // deduct nopay
        var bsal = parseFloat(basicSalary);

        if (EPF_ENTITLE_DATE == "0") {
            EPFin = 0;
            EPFded = 0;
            ETFin = 0;
        } else {
            EPFin = (bsal * 12) / 100;
            EPFded = (bsal * 8) / 100;
            ETFin = (bsal * 3) / 100;
        }

        $("#epfin").html(numeral(EPFin).format('0,0.00'));
        $("#epfded").html(numeral(EPFded).format('0,0.00'));
        $("#etfin").html(numeral(ETFin).format('0,0.00'));
    }

    function selectIncreasement(siid) {
        var x = confirm("Are you sure you want to delete this record?");
        if (x) {
            var url = '../Controller/emp_payroll.php?request=removeIncreasement&siid=' + siid;
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    loadUser();
                }
            });
        }
    }

    function addIncreasement() {

        if ($('#uid').val() == "") {
            alert("Please Select Employee");
        } else {
            var date = $('#indate').val();
            var reason = $('#inreason').val();
            var amount = $('#inamount').val();

            var url = '../Controller/emp_payroll.php?request=addIncreasement&uid=' + $('#uid').val() + "&date=" + date +
                "&res=" + reason + "&amount=" + amount;
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    loadUser();
                    $('#indate').val("");
                    $('#inreason').val("");
                    $('#inamount').val("");
                }
            });
        }
    }

    function printSalSheet() {

        if ($('#jcode').val() == "") {
            alert("Please Select Employee");
        } else {
            prepair();

            $('#MealName').hide();
            $('#salb').hide();
            $('#1').show();
            $('#2').show();

            var month = $("#date2").val().split("-")[1];
            var year = $("#date2").val().split("-")[0];


            $('#selectmny').hide();

            var divToPrint0 = document.getElementById('geninfo');
            var divToPrint1 = document.getElementById('salinfo');
            var divToPrint2 = document.getElementById('salsheet');

            var newWin = window.open('', 'Print-Window');
            newWin.document.open();
            newWin.document.write(
                "<!doctype html><html><head><title>Salary Sheet - Appex Payroll</title><link href='../Styles/Stylie.css' rel='stylesheet' type='text/css'></head><body style='font-size:11px;'><img src='../Images/satlopaysheet.png' width='100%' height='180px'/></br><hr/></br> <div style='margin-left:10%;'><center><h2 class='Report_Header'><u>Salary Pay Slip</u></h2></center> <br/></h3>" +
                "<h3 style='font-size:14px;'>Employee Name : " + $("#marker option[value='" + $('#marker').val() +
                    "']").text() + " &nbsp; Month : " + month + " &nbsp;  Year : " + year + "</h3>" + divToPrint1
                .innerHTML + divToPrint2.innerHTML +
                "</br><hr/><p style='font-size:12px;'>Appex Payroll - Powered by Appex Solutions. WEB : www.appexsl.com / Email : info@appexsl.com</p></div></body></html>"
                );

            $('#selectmny').show();
        }

    }

    var totAtt = 0;
    var totAttSal = 0;
    var Allowance = 0;
    var otherAllowance = 0;
    var halfDayDeduct = 0;
    var halfDays = 0;
    var shortLeaves = 0;
    var leaves = 0;
    var OTallowance = 0;
    var DOTallowance = 0;
    var basic = 0;
    var lateminAM = 0;
    var latededuct = 0;

    var Travelling_Allowance = 0;
    var Attendence_Allowance = 0;
    var OTHERALLOW = 0;
    var OTPAYPH = 0;
    var OTPAYPH = 0;
    var DOTPAYPH = 0;
    var TOTAL_WORKING_DAYS = 0;



    var gradingAllowance = 0;
    var BR1 = 0;
    var BR2 = 0;
    var budgutAllowance = 0;

    //variables for salary calculation
    var oth = 0;
    var doth = 0;
    var latemins = 0;
    var minuteAmount = 0;

    var attIncentinve = 0;
    var OTDATA = 0;
    var LEAVECOUNT = 0;
    var CompanyLeaveCount = 0;


    function getPayroll() {
        getDeductions();
        getLoandetails();
        var uid = $('#uid').val();
        var EMPACT = $('#empact').val();
        if (uid !== "") {
            var date1 = $('#date1').val();
            var date2 = $('#date2').val();

            if (date1 !== "" && date2 !== "") {

                var url = '../Controller/emp_payroll.php?request=getPayroll&uid=' + uid + "&date1=" + date1 +
                    "&date2=" + date2;
                $.ajax({
                    type: 'POST',
                    url: url,
                    success: function(data) {

                        var arr = data.split("#");
                        $('#toth').html("&nbsp" + numeral(arr[0]).format('0.00'));
                        $('#totat').html("&nbsp" + arr[2]);
                        if (arr[3] == "") {
                            $('#totLeaves').html("&nbsp" + "0");
                            LEAVECOUNT = 0;
                        } else {
                            $('#totLeaves').html("&nbsp" + arr[3]);
                        }

                        $('#tototh').html("&nbsp" + numeral(arr[1]).format('0,0.00'));
                        oth = parseFloat(arr[1]);

                        $('#totsl').html("&nbsp" + arr[4]);
                        doth = parseFloat(arr[5]);
                        //late payment and deduct amount
                        latemins = parseFloat(arr[6]);
                        if (latemins < 0) {
                            latemins = 0;
                        }

                        $('#lam').html("&nbsp" + numeral(latemins).format('0.00'));
                        budgutAllowance = parseFloat(arr[11]) + parseFloat(arr[12]);

                        BR1 = parseFloat(arr[11]);
                        BR2 = parseFloat(arr[12]);
                        TOTAL_WORKING_DAYS = parseFloat(arr[13]);

                        if (EMPACT == "Shop and Office") {
                            //OT and DOT 
                            OTPAYPH = ((basic + budgutAllowance) / 240) * 1.5;
                            DOTPAYPH = ((basic + budgutAllowance) / 240) * 2;
                            OTallowance = oth * OTPAYPH;
                            DOTallowance = doth * DOTPAYPH;
                        } else {
                            //OT and DOT 
                            OTPAYPH = ((basic + budgutAllowance) / 200) * 1.5;
                            DOTPAYPH = ((basic + budgutAllowance) / 200) * 2;
                            OTallowance = oth * OTPAYPH;
                            DOTallowance = doth * DOTPAYPH;
                        }

                        $('#otal').html(numeral(OTallowance).format('0,0.00'));
                        $('#allowtotamount').val(numeral(arr[9]).format('0.00'));
                        $('#attallow').html(numeral(arr[14]).format('0.00'));
                        $('#trvlallow').html(numeral(arr[15]).format('0.00'));

                        Travelling_Allowance = parseFloat(arr[15]);
                        Attendence_Allowance = parseFloat(arr[14]);
                        OTHERALLOW = parseFloat(arr[16]);

                        if (arr[17] == "1") {
                            $('#btnPaySal').prop('disabled', 'disabled');
                            $('#updsalbtn').show(); // new
                        } else {
                            document.getElementById("btnPaySal").disabled = false;
                            $('#updsalbtn').hide(); // new
                        }

                        var bsal = basic + budgutAllowance;
                        CompanyLeaveCount = arr[18];
                        totAtt = arr[2];
                        leaves = calculateLeaves();
                        leaves = 0;
                        shortLeaves = parseFloat(arr[4]);
                        $('#totnp').val(arr[10]);
                        calculateNOPAY();
                        calculateAllowance();
                        getAdvances();
                    }
                });
            }
        } else {
            alert("Please select user!");
        }
    }


    function getLoadSlip() {

        getSlipDeductionsAfterPaid(); //new dev 2023-11-06
        getSlipLoandetailsAfterPaid(); //new dev 2023-11-06

        var uid = $('#uid').val();
        var EMPACT = $('#empact').val();
        if (uid !== "") {
            var date1 = $('#date1').val();
            var date2 = $('#date2').val();

            if (date1 !== "" && date2 !== "") {

                var url = '../Controller/emp_payroll.php?request=getPayroll&uid=' + uid + "&date1=" + date1 +
                    "&date2=" + date2;
                $.ajax({
                    type: 'POST',
                    url: url,
                    success: function(data) {

                        var arr = data.split("#");

                        $('#toth').html("&nbsp" + numeral(arr[0]).format('0.00'));
                        $('#totat').html("&nbsp" + arr[2]);
                        if (arr[3] == "") {
                            $('#totLeaves').html("&nbsp" + "0");
                            LEAVECOUNT = 0;
                        } else {
                            $('#totLeaves').html("&nbsp" + arr[3]);
                        }

                        $('#tototh').html("&nbsp" + numeral(arr[1]).format('0,0.00'));
                        oth = parseFloat(arr[1]);
                        $('#totsl').html("&nbsp" + arr[4]);
                        doth = parseFloat(arr[5]);
                        //late payment and deduct amount
                        latemins = parseFloat(arr[6]); // ignores 30 mints 

                        if (latemins < 0) {
                            latemins = 0;
                        }

                        $('#lam').html("&nbsp" + numeral(latemins).format('0.00'));
                        budgutAllowance = parseFloat(arr[11]) + parseFloat(arr[12]);

                        BR1 = parseFloat(arr[11]);
                        BR2 = parseFloat(arr[12]);
                        TOTAL_WORKING_DAYS = parseFloat(arr[13]);

                        if (EMPACT == "Shop and Office") {
                            //OT and DOT 
                            OTPAYPH = ((basic + budgutAllowance) / 240) * 1.5;
                            DOTPAYPH = ((basic + budgutAllowance) / 240) * 2;
                            OTallowance = oth * OTPAYPH;
                            DOTallowance = doth * DOTPAYPH;
                        } else {
                            //OT and DOT 
                            OTPAYPH = ((basic + budgutAllowance) / 200) * 1.5;
                            DOTPAYPH = ((basic + budgutAllowance) / 200) * 2;
                            OTallowance = oth * OTPAYPH;
                            DOTallowance = doth * DOTPAYPH;
                        }

                        $('#otal').html(numeral(OTallowance).format('0,0.00'));
                        $('#allowtotamount').val(numeral(arr[9]).format('0.00'));
                        $('#attallow').html(numeral(arr[14]).format('0.00'));
                        $('#trvlallow').html(numeral(arr[15]).format('0.00'));

                        Travelling_Allowance = parseFloat(arr[15]);
                        Attendence_Allowance = parseFloat(arr[14]);
                        OTHERALLOW = parseFloat(arr[16]);

                        if (arr[17] == "1") {
                            $('#btnPaySal').prop('disabled', 'disabled');
                            $('#updsalbtn').show(); // new
                        } else {
                            document.getElementById("btnPaySal").disabled = false;
                            $('#updsalbtn').hide(); // new
                        }

                        var bsal = basic + budgutAllowance;

                        CompanyLeaveCount = arr[18];
                        totAtt = arr[2];

                        leaves = calculateLeaves();
                        leaves = 0;
                        shortLeaves = parseFloat(arr[4]);
                        calculateNOPAY();
                        calculateAllowance();
                        getAdvances();
                    }
                });
            }
        } else {
            alert("Please select user!");
        }
    } //new

    function calculateLeaves() {
        var leaves = parseFloat($('#days').val()) - parseFloat(totAtt);
        $('#totlv').html(leaves);
        return leaves;
    }


    function calculateMealAllowances() {
        if ($('#mealdays').val() == "") {
            var mealcal = 0;
        } else {
            var mealcal = parseFloat($('#allowtotamount').val()) - parseFloat($('#mealdays').val() * 250);
        }

        $('#mealremaining').val(mealcal);
    }

    function daysClick(value) {
        if (value === "") {
            value = 0;
        }
        calculateAllowance();
        getPayroll();
    }

    var AllowanceX = Allowance;
    var nopay_leaves = 0;
    var totAtte = 0;
    var totTrvl = 0;
    var ATTALLOW = 0;
    var TRVALLOW = 0;

    function calculateAllowance() {

        var EmpAct = $('#empact').val();
        //get total leaves from short leave half days and leaves

        if ($("#totnp").val() !== "") {
            nopay_leaves = parseFloat($("#totnp").val());

            if (EmpAct == "Shop and Office") {
                ATTALLOW = (Attendence_Allowance / 30);
                TRVALLOW = (Travelling_Allowance / 30);
                totAtte = Attendence_Allowance - (ATTALLOW * nopay_leaves);
                totTrvl = Travelling_Allowance - (TRVALLOW * nopay_leaves);

                $('#attallow').html(numeral(totAtte).format('0.00'));
                $('#trvlallow').html(numeral(totTrvl).format('0.00'));

            } else if (EmpAct == "Wages Board") {
                ATTALLOW = (Attendence_Allowance / 26);
                TRVALLOW = (Travelling_Allowance / 26);
                totAtte = Attendence_Allowance - (ATTALLOW * nopay_leaves);
                totTrvl = Travelling_Allowance - (TRVALLOW * nopay_leaves);

                $('#attallow').html(numeral(totAtte).format('0.00'));
                $('#trvlallow').html(numeral(totTrvl).format('0.00'));
            } else if (EmpAct == "Driver Wages Board") {
                ATTALLOW = (Attendence_Allowance / 25);
                TRVALLOW = (Travelling_Allowance / 25);
                totAtte = Attendence_Allowance - (ATTALLOW * nopay_leaves);
                totTrvl = Travelling_Allowance - (TRVALLOW * nopay_leaves);

                $('#attallow').html(numeral(totAtte).format('0.00'));
                $('#trvlallow').html(numeral(totTrvl).format('0.00'));
            } else {
                ATTALLOW = (Attendence_Allowance / 26);
                TRVALLOW = (Travelling_Allowance / 26);
                totAtte = Attendence_Allowance - (ATTALLOW * nopay_leaves);
                totTrvl = Travelling_Allowance - (TRVALLOW * nopay_leaves);

                $('#attallow').html(numeral(totAtte).format('0.00'));
                $('#trvlallow').html(numeral(totTrvl).format('0.00'));
            }


        } else {
            totAtte = Attendence_Allowance;
            totTrvl = Travelling_Allowance;
            $('#attallow').html(numeral(totAtte).format('0.00'));
            $('#trvlallow').html(numeral(totTrvl).format('0.00'));
        }
    }

    var totDeductValue = 0;

    function getDeductions() {
        var uid = $('#uid').val();
        var d1 = $('#date1').val();
        var d2 = $('#date2').val();

        var url = '../Controller/emp_payroll.php?request=getSalDeductions&uid=' + uid + "&date1=" + d1 + "&date2=" + d2;
        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                var arr = data.split("##");
                $('#SalaryDeduction').html(arr[0]);
                $('#totsalded').html("&nbsp" + numeral(arr[1]).format('0,0.00'));
                totDeductValue = parseFloat(arr[1]);
            }
        });
    }


    function getSlipDeductionsAfterPaid() { //new dev 2023-11-06
        var uid = $('#uid').val();
        var d1 = $('#date1').val();
        var d2 = $('#date2').val();

        var url = '../Controller/emp_payroll.php?request=getSlipSalDeductionsAfterPaid&uid=' + uid + "&date1=" + d1 +
            "&date2=" + d2;
        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                var arr = data.split("##");
                $('#SalaryDeduction').html(arr[0]);
                $('#totsalded').html("&nbsp" + numeral(arr[1]).format('0,0.00'));
                totDeductValue = parseFloat(arr[1]);
            }
        });
    }


    var totIncValue = 0;

    function getIncreasements() {
        var uid = $('#uid').val();

        var url = '../Controller/emp_payroll.php?request=getIncreasements&uid=' + uid;
        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                var arr = data.split("##");
                $('#SalaryIncreasement').html(arr[0]);
                $('#totsali').html("&nbsp" + numeral(arr[1]).format('0,0.00'));
                totIncValue = parseFloat(arr[1]);
            }
        });
    }

    var totLoanValue = 0;

    function getLoandetails() {
        var uid = $('#uid').val();
        var d1 = $('#date1').val(); //new dev 2023-11-06

        var url = '../Controller/emp_payroll.php?request=getLoans&uid=' + uid + "&date1=" + d1;
        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                var arr = data.split("##");
                $('#FactoryLoan').html(arr[0]);
                $('#totfacloan').html("&nbsp" + numeral(arr[1]).format('0,0.00'));
                totLoanValue = parseFloat(arr[1]);
            }
        });
    }


    function getSlipLoandetailsAfterPaid() { //new dev 2023-11-06
        var uid = $('#uid').val();
        var d1 = $('#date1').val();

        var url = '../Controller/emp_payroll.php?request=getSlipSalLoansAfterPaid&uid=' + uid + "&date1=" + d1;
        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                var arr = data.split("##");
                $('#FactoryLoan').html(arr[0]);
                $('#totfacloan').html("&nbsp" + numeral(arr[1]).format('0,0.00'));
                totLoanValue = parseFloat(arr[1]);
            }
        });
    }

    var finalSal = 0;
    var Payee_TAX = 0;
    var tax_amount_mul = 0
    var tax_amount_per = 0
    var tax_amount = 0;

    function getFinalSalery() {
        var EmpACTS = $('#empact').val();

        totAttSal = basic + budgutAllowance + parseFloat($('#attallow').html()) + parseFloat($('#trvlallow').html());

        attIncentinve = 0;

        $('#aalw').html("&nbsp" + numeral(attIncentinve).format('0,0.00'));

        if (EmpACTS == "Shop and Office") {
            lateminAM = ((basic + budgutAllowance - NOPAY_deduct) / 30 / 8 / 60);
        } else if (EmpACTS == "Wages Board") {
            lateminAM = ((basic + budgutAllowance - NOPAY_deduct) / 26 / 8 / 60);
        } else if (EmpACTS == "Driver Wages Board") {
            lateminAM = ((basic + budgutAllowance - NOPAY_deduct) / 25 / 8 / 60);
        } else {
            lateminAM = ((basic + budgutAllowance - NOPAY_deduct) / 26 / 8 / 60);
        }

        latededuct = numeral(latemins * lateminAM).format('0.00');
        $('#lad').html("&nbsp" + numeral(latededuct).format('0.00'));

        finalSal = totAttSal + totIncValue + OTallowance + OTHERALLOW - totDeductValue - totLoanValue - totAdvances -
            latededuct + attIncentinve - NOPAY_deduct;

        Payee_TAX = totAttSal + totIncValue + OTallowance + OTHERALLOW + attIncentinve;

        //calculate EPF (8% from employee) with adding badget allowance to basic salery                 
        var bsalt = basic + budgutAllowance - NOPAY_deduct;
        // var bsalt = basic;
        $('#basicsalepf').html(numeral(bsalt).format('0,0.00'));

        calculateETFnEPF(bsalt);

        if (EPF_ENTITLE_DATE == "0") {
            var epf = 0;
        } else {
            var epf = (bsalt * 8) / 100;
        }


        $('#epfded').html("&nbsp" + numeral(epf).format('0,0.00'));

        finalSal = finalSal - epf;


        if (Payee_TAX > 100000 && Payee_TAX < 141666) {
            tax_amount_mul = Payee_TAX * 6;
            tax_amount_per = tax_amount_mul / 100;
            tax_amount = tax_amount_per - 6000;
            finalSal = finalSal - tax_amount;
        } else if (Payee_TAX > 141667 && Payee_TAX < 183332) {
            tax_amount_mul = Payee_TAX * 12;
            tax_amount_per = tax_amount_mul / 100;
            tax_amount = tax_amount_per - 14500;
            finalSal = finalSal - tax_amount;
        } else if (Payee_TAX > 183333 && Payee_TAX < 224999) {
            tax_amount_mul = Payee_TAX * 18;
            tax_amount_per = tax_amount_mul / 100;
            tax_amount = tax_amount_per - 25500;
            finalSal = finalSal - tax_amount;
        } else if (Payee_TAX > 225000 && Payee_TAX < 266666) {
            tax_amount_mul = Payee_TAX * 24;
            tax_amount_per = tax_amount_mul / 100;
            tax_amount = tax_amount_per - 39000;
            finalSal = finalSal - tax_amount;
        } else if (Payee_TAX > 266667 && Payee_TAX < 308332) {
            tax_amount_mul = Payee_TAX * 30;
            tax_amount_per = tax_amount_mul / 100;
            tax_amount = tax_amount_per - 55000;
            finalSal = finalSal - tax_amount;
        } else if (Payee_TAX > 308333) {
            tax_amount_mul = Payee_TAX * 36;
            tax_amount_per = tax_amount_mul / 100;
            tax_amount = tax_amount_per - 73500;
            finalSal = finalSal - tax_amount;
        } else {
            tax_amount = 0;
            finalSal = finalSal;
        }

        // alert("###TOTSAL = "+totAttSal+"####INCREMENT = "+totIncValue+"####OT = "+OTallowance+"####OTHEALLOW = "+OTHERALLOW+"####DEDUCT = "+totDeductValue+"####LOAN = "+totLoanValue+"#####ADVANCE = "+totAdvances+"####LATE = "+latededuct+"####ATTINCENTIVE = "+attIncentinve+"####NOPAY = "+NOPAY_deduct+"###Attend = "+parseFloat($('#attallow').html())+"### Trave = "+parseFloat($('#trvlallow').html())+"###Final Sal = "+finalSal)

        //deduct Attendance leave  
        /*var DayDeduct = bsalt / 25;
        var leaveDeduct = DayDeduct * leaves;
        finalSal = finalSal - leaveDeduct;               
        
        var salforatt = totAttSal - leaveDeduct;
        $('#totsfa').html(numeral(salforatt).format('0,0.00'));*/

        //Add Budget Allowance and Grading Allowance
        // finalSal = finalSal + parseFloat(budgutAllowance) + parseFloat(gradingAllowance);

        $('#totsalary').html(numeral(finalSal).format('0,0.00'));
        $('#tottaxamt').html(Math.floor(tax_amount * 100) / 100);
        $('#paid_sal').val(numeral(finalSal).format('0.00'));
    }

    function maxAdvance() {
        if (finalSal !== 0 && $('#apamount').val() !== "") {
            var advance = parseFloat($('#apamount').val());
            if (finalSal <= advance) {
                alert(
                    "Advance payment which you are entering is beyond the total salary of this employee. Please enter valid amount or do the total salary payment");
            }
        }

    }

    function advancePayment() {
        var month = $('#month').val();
        var account = $('#account').val();
        var year = $('#year').val();
        if (month !== "0" && year !== "0") {
            var uid = $('#uid').val();
            var date = $('#apdate').val();
            var amount = $('#apamount').val();
            if (amount !== "") {
                var url = '../Controller/emp_payroll.php?request=advancePayment&uid=' + uid + "&date=" + date +
                    "&amount=" + amount + "&month=" + month + "&year=" + year + "&account=" + account;
                $.ajax({
                    type: 'POST',
                    url: url,
                    success: function(data) {
                        alert(data);
                        getPayroll();
                        $('#apamount').val("");
                    }
                });
            } else {
                alert("Please enter amount!");
            }
        } else {
            alert("Plese select month and year and calculate the salary first!")
        }
    }

    var totAdvances = 0;

    function getAdvances() {
        var date = $('#date1').val();
        var url = '../Controller/emp_payroll.php?request=getAdvances&uid=' + $('#uid').val() + "&date=" + date;
        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                var arr = data.split("##");
                totAdvances = parseFloat(arr[1]);
                $("#saladp").html(arr[0]);
                $("#totAdv").html("Total Advance Paid Rs. " + numeral(arr[1]).format('0,0.00'));
                getFinalSalery();
                getAdvancesforSalSheet();
            }
        });

        ;
    }

    function getAdvancesforSalSheet() {
        var date = $('#date1').val();
        var url = '../Controller/emp_payroll.php?request=getAdvancesforsalsheet&uid=' + $('#uid').val() + "&date=" +
            date;
        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                $("#saladpx").html(data);
            }
        });
    }

    function removeAdvance(id) {
        var x = confirm("Are you sure you want to delete this record?");
        if (x) {
            var url = '../Controller/emp_payroll.php?request=removeAdvance&spid=' + id;
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    alert(data);
                    getPayroll();
                }
            });
        }
    }


    function doSalaryPayment() {

        if ($('#uid').val() == "") {
            alert("Plese select date and calculate the salary first!");
        } else {
            let confirmAction = confirm("Are you sure you want to pay for this employee?");

            if (confirmAction) {
                var date1 = $('#date1').val();
                var date2 = $('#date2').val();
                if (date1 !== "0" && date2 !== "0") {
                    var uid = $('#uid').val();
                    var d = new Date();
                    var date = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate();
                    var account = $('#accountfrpay').val();

                    var days = $('#days').val();
                    localStorage.setItem("daysount", days);
                    var bonus = $('#salbns').val();
                    var epf12 = EPFin;
                    var epf8 = EPFded;
                    var etf3 = ETFin;

                    var NOPAYDAYS = 0;
                    if ($("#totnp").val() == "") {
                        NOPAYDAYS = 0;
                    } else {
                        NOPAYDAYS = $("#totnp").val();
                    }

                    var dayDeduct = numeral(basic / 26).format('0.00');

                    var totallw = parseFloat(OTHERALLOW) + parseFloat(totIncValue);

                    // alert("###TOTSAL"+totAttSal+"####INCREMENT"+totIncValue+"####OT"+OTallowance+"####OTHEALLOW"+OTHERALLOW+"####DEDUCT"+totDeductValue+"####LOAN"+totLoanValue+"#####ADVANCE"+totAdvances+"####LATE"+latededuct+"####ATTINCENTIVE"+attIncentinve+"####NOPAY"+NOPAY_deduct)

                    var amount = finalSal;
                    if (amount !== "0") {
                        var url = '../Controller/emp_payroll.php?request=salaryPayment&uid=' + uid + "&date=" + date +
                            "&basic=" + basic +
                            "&date=" + date +
                            "&date1=" + date1 + "&date2=" + date2 +
                            "&totwdays=" + TOTAL_WORKING_DAYS +
                            "&wdays=" + totAtt +
                            "&bonus=" + "0" +
                            "&epf12=" + epf12 +
                            "&epf8=" + epf8 +
                            "&etf3=" + etf3 +
                            "&totadv=" + totAdvances +
                            "&allw=" + totallw +
                            "&npaypr=" + "0" +
                            "&npay=" + NOPAY_deduct +
                            "&poya=" + "0" +
                            "&gross=" + finalSal +
                            "&stamp=" + "0" +
                            "&loan=" + totLoanValue +
                            "&att1=" + totAtte +
                            "&tototherded=" + totDeductValue +
                            "&net=" + finalSal +
                            "&dto=" + $("#date1").val() +
                            "&dfrm=" + $("#date2").val() +
                            "&ot=" + OTallowance // OT amount
                            +
                            "&otday=" + DOTallowance // DOT amount
                            +
                            "&paidsal=" + $("#paid_sal").val() +
                            "&monthx=" + $("#date2").val().split("-")[1] +
                            "&yearx=" + $("#date2").val().split("-")[0]

                            +
                            "&attin=" + attIncentinve +
                            "&grading=" + "0" +
                            "&travl=" + totTrvl +
                            "&late=" + latededuct +
                            "&half=" + "0" +
                            "&short=" + shortLeaves +
                            "&leave_ded=" + "0" +
                            "&OtherALLOW=" + OTHERALLOW +
                            "&NopayDAys=" + NOPAYDAYS +
                            "&br1=" + BR1 +
                            "&br2=" + BR2 +
                            "&payee_tax=" + tax_amount;

                        // alert(url); 

                        $.ajax({
                            type: 'POST',
                            url: url,
                            success: function(data) {
                                alert(data);
                                window.location.href = "../Views/emp_payroll.php";
                                //getPayroll();
                            }
                        });
                    } else {
                        alert("Please enter amount!");
                    }
                } else {
                    alert("Plese select month and year and calculate the salary first!");
                }
            }
        }


    }


    function EditSalaryPayment() { // new


        if ($('#uid').val() == "") {
            alert("Plese select date and calculate the salary first!");
        } else {
            let confirmAction = confirm("Are you sure you want to pay for this employee?");

            if (confirmAction) {

                var date1 = $('#date1').val();
                var date2 = $('#date2').val();
                if (date1 !== "0" && date2 !== "0") {
                    var uid = $('#uid').val();
                    var d = new Date();
                    var date = d.getFullYear() + "-" + (d.getMonth() + 1) + "-" + d.getDate();
                    var account = $('#accountfrpay').val();

                    var days = $('#days').val();
                    localStorage.setItem("daysount", days);
                    var bonus = $('#salbns').val();
                    var epf12 = EPFin;
                    var epf8 = EPFded;
                    var etf3 = ETFin;

                    var NOPAYDAYS = 0;
                    if ($("#totnp").val() == "") {
                        NOPAYDAYS = 0;
                    } else {
                        NOPAYDAYS = $("#totnp").val();
                    }

                    var dayDeduct = numeral(basic / 26).format('0.00');

                    var totallw = parseFloat(OTHERALLOW) + parseFloat(totIncValue);

                    var amount = finalSal;
                    if (amount !== "0") {
                        var url = '../Controller/emp_payroll.php?request=EditsalaryPayment&uid=' + uid + "&date=" +
                            date +
                            "&basic=" + basic +
                            "&date=" + date +
                            "&date1=" + date1 + "&date2=" + date2 +
                            "&totwdays=" + TOTAL_WORKING_DAYS +
                            "&wdays=" + totAtt +
                            "&bonus=" + "0" +
                            "&epf12=" + epf12 +
                            "&epf8=" + epf8 +
                            "&etf3=" + etf3 +
                            "&totadv=" + totAdvances +
                            "&allw=" + totallw +
                            "&npaypr=" + "0" +
                            "&npay=" + NOPAY_deduct +
                            "&poya=" + "0" +
                            "&gross=" + finalSal +
                            "&stamp=" + "0" +
                            "&loan=" + totLoanValue +
                            "&att1=" + totAtte +
                            "&tototherded=" + totDeductValue +
                            "&net=" + finalSal +
                            "&dto=" + $("#date1").val() +
                            "&dfrm=" + $("#date2").val() +
                            "&ot=" + OTallowance // OT amount
                            +
                            "&otday=" + DOTallowance // DOT amount
                            +
                            "&paidsal=" + $("#paid_sal").val() +
                            "&monthx=" + $("#date2").val().split("-")[1] +
                            "&yearx=" + $("#date2").val().split("-")[0]

                            +
                            "&attin=" + attIncentinve +
                            "&grading=" + "0" +
                            "&travl=" + totTrvl +
                            "&late=" + latededuct +
                            "&half=" + "0" +
                            "&short=" + shortLeaves +
                            "&leave_ded=" + "0" +
                            "&OtherALLOW=" + OTHERALLOW +
                            "&NopayDAys=" + NOPAYDAYS +
                            "&br1=" + BR1 +
                            "&br2=" + BR2 +
                            "&payee_tax=" + tax_amount;

                        // alert(url); 

                        $.ajax({
                            type: 'POST',
                            url: url,
                            success: function(data) {
                                alert(data);
                                LoadSlipData();
                            }
                        });
                    } else {
                        alert("Please enter amount!");
                    }
                } else {
                    alert("Plese select month and year and calculate the salary first!");
                }
            }
        }


    }

    $(document).on('keydown keypress', '#jcode', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            var url = '../Controller/emp_payroll.php?request=getuidfromjobcode&jcode=' + $('#jcode').val();
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    $("#marker option[value=" + data + "]").attr('selected', 'selected');
                    setUID(data);
                }
            });
            return false;
        }
    });

    function prepair() {
        $('#days').hide();
        $('#totnp').hide();
        $('#totothcus').hide();
        $('#salbns').hide();

        $('#daysLBL').show();
        $('#npdaysLBL').show();
        $('#totothcusLBL').show();
        $('#salbnsLBL').show();

        $('#daysLBL').html($('#days').val());
        $('#npdaysLBL').html($('#totnp').val());
        $('#totothcusLBL').html($('#totothcus').val());
        $('#salbnsLBL').html($('#salbns').val());
    }

    OTallowanceCash = 0;

    function removeOTAllowance() {

        if (OTallowanceCash === 0) {
            OTallowanceCash = OTallowance;
            OTallowance = 0;
            DOTallowance = 0;

            getFinalSalery();
            alert("O.T. Allowance Removed!");
        } else {
            OTallowance = OTallowanceCash;
            OTallowanceCash = 0;
            DOTallowance = 0;

            getFinalSalery();
            alert("O.T. Allowance Added Back!");
        }

        $('#otal').html("&nbsp" + numeral(OTallowance).format('0,0.00'));
        $('#dotam').html("&nbsp" + numeral(DOTallowance).format('0,0.00'));
    }

    function removeIncentive() {
        attIncentinve = 0;
        $('#aalw').html("&nbsp" + numeral(attIncentinve).format('0,0.00'));
        getFinalSalery();
        alert("Incentive Removed!");
    }

    function removeMeal() {
        bonus = 0;
        $('#MealName').hide();
        $('#salb').hide();
        $('#1').show();
        $('#2').show();
        $('#salbns').val(numeral(bonus).format('0.00'));
        getFinalSalery();
        alert("Meal Allowance Removed!");
    }

    var NOPAY_deduct = 0;

    function calculateNOPAY() {
        var nopys = parseFloat($("#totnp").val());
        var EmpAct = $('#empact').val();

        if ($("#totnp").val() !== "") {
            if (EmpAct == "Shop and Office") {
                var dayDeduct = ((basic + budgutAllowance) / 30);
                NOPAY_deduct = dayDeduct * nopys;
                $('#nopayded').html(numeral(NOPAY_deduct).format('0,0.00'));

            } else if (EmpAct == "Wages Board") {
                var dayDeduct = ((basic + budgutAllowance) / 26);
                NOPAY_deduct = dayDeduct * nopys;
                $('#nopayded').html(numeral(NOPAY_deduct).format('0,0.00'));
            } else if (EmpAct == "Driver Wages Board") {
                var dayDeduct = ((basic + budgutAllowance) / 25);
                NOPAY_deduct = dayDeduct * nopys;
                $('#nopayded').html(numeral(NOPAY_deduct).format('0,0.00'));
            } else {
                var dayDeduct = ((basic + budgutAllowance) / 26);
                NOPAY_deduct = dayDeduct * nopys;
                $('#nopayded').html(numeral(NOPAY_deduct).format('0,0.00'));
            }

        } else {
            NOPAY_deduct = 0;
            $('#nopayded').html("0.00");
        }
        calculateAllowance();
        getFinalSalery();
    }

    function EnterMealRemaining() {

        var uid = $('#uid').val();
        var allowancedate = document.getElementById('allowdate').value;
        var totallowamount = document.getElementById('allowtotamount').value;
        var mdays = document.getElementById('mealdays').value;
        var mremaining = document.getElementById('mealremaining').value;

        if (uid == "" || mdays == "" || allowancedate == "") {
            alert("Please Select Employee and Calculate Salary First!");
        } else {
            let confirmAction = confirm("Are you sure this is correct?");

            if (confirmAction) {

                var url = "../Controller/emp_payroll.php?request=mealremaining&uid=" + uid + "&allowancedate=" +
                    allowancedate + "&totallowamount=" + totallowamount + "&mdays=" + mdays + "&mremaining=" +
                    mremaining;

                $.ajax({
                    type: 'POST',
                    url: url,
                    success: function(data) {

                        if (data == "1") {
                            alert("Meal Allowance Added Successfully!");
                            $("#allowtotamount").val("");
                            $("#mealdays").val("");
                            $("#mealremaining").val("");
                        } else {
                            alert("Meal Allowance Already Added In This Month!");
                        }

                    }
                });
            }
        }
    }


    function leaveCounter(id) {
        var url = "../Controller/emp_payroll.php?request=getleavecount&UID=" + id;

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {

                $('#getshortleaves').show();
                $('#availableshortleaves').show();

                var arr = data.split("#");

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
                        $('#leave_leaves').show();
                        $("#leave_leaves").html("&nbsp;&nbsp;Liue Leaves : <b>" + arr[7] + "</b>");
                    } else {
                        $('#opt_2').hide();
                        $('#leave_leaves').hide();
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
                        $('#leave_leaves').show();
                        $("#leave_leaves").html("&nbsp;&nbsp;Liue Leaves : <b>" + arr[7] + "</b>");
                    } else {
                        $('#leave_leaves').hide();
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

                        if (arr[8] == "1") {
                            $('#leave_leaves').show();
                            $("#leave_leaves").html("&nbsp;&nbsp;Liue Leaves : <b>" + arr[7] + "</b>");
                        } else {
                            $('#leave_leaves').hide();
                        }
                    }

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

                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">

                        <div class="row x_title">
                            <div class="col-md-6">
                                <h3>Generate Salary <small>Create employee salary sheet</small></h3>
                                <input type="text" id="SLIP_ID" value="<?php echo $SlipId; ?>" hidden="hidden">
                                <!-- new -->
                                <input type="text" id="U_ID" value="<?php echo $UId; ?>" hidden="hidden"><!-- new -->
                            </div>
                        </div>

                    </div>
                </div>

                <div id="geninfo">
                    <div>
                        <?php
                        $eid = "";
                        if (isset($_GET["eid"])) {
                            $eid = $_GET["eid"];
                        }
                        ?>
                        Employee Name :
                        <select id="marker" name="marker" class="select-basic" onchange="setUID(value)"
                            style="width: 200px; height: 23px;">
                            <option></option>
                            <?php
                            $query = "select uid,fname,lname,epfno,jobcode from user where isactive='1' and uid != '2' order by length(jobcode),jobcode ASC";
                            $res = Search($query);
                            while ($result = mysqli_fetch_assoc($res)) {
                            ?>
                            <option value="<?php echo $result["uid"]; ?>"><?php echo $result["jobcode"]; ?> :
                                <?php echo $result["fname"]; ?> </option>
                            <?php } ?>
                        </select>
                        <!-- &nbsp;  &nbsp; Employee ID : --> <input type="text" name="uid" id="uid"
                            value="<?php echo $eid; ?>" class="input-text" style="width: 200px;" hidden='hidden' />
                        &nbsp; &nbsp; Employee No : <input type="text" name="jcode" id="jcode"
                            value="<?php echo $result["jobcode"]; ?>" class="input-text" style="width: 200px;" />
                        &nbsp;&nbsp; <i class="fa fa-search" onclick="setEpdNo()"
                            style="float: next; cursor: pointer"></i>

                        <!-- <input type="button" value="Print Salary Sheet" class="btn btn-dark" onclick="printSalSheet()" style="float: right;margin-right: 20px;" /> -->
                    </div>
                </div>


                <hr />
                <br />

                <table width="100%">
                    <tr>
                        <td width="30%" valign="top">
                            <table width="100%">
                                <tr>
                                    <td width="30%" style="padding: 10px;">
                                        <table>
                                            <tr valign="top">
                                                <td>
                                                    <div id="salinfo">
                                                        <div>
                                                            <table class="table table-striped">
                                                                <thead>
                                                                    <tr>
                                                                        <td class="form-label" width="170px">Employee
                                                                            Designation</td>
                                                                        <td id="eposi"></td>
                                                                        <td width="75px"></td>
                                                                        <td class="form-label" width="170px">E.P.F.
                                                                            (12%) Rs.</td>
                                                                        <td id="epfin"></td>

                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="form-label" width="170px">E.P.F.
                                                                            Number</td>
                                                                        <td id="epfno"></td>
                                                                        <td width="75px"></td>
                                                                        <td class="form-label" width="170px">E.P.F. (8%)
                                                                            &nbsp;&nbsp;Rs.</td>
                                                                        <td id="epfded"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="form-label" width="170px">Employee
                                                                            Act</td>
                                                                        <td id="empact"></td>
                                                                        <td width="75px"></td>
                                                                        <td class="form-label" width="170px">E.T.F. (3%)
                                                                            &nbsp;&nbsp;Rs.</td>
                                                                        <td id="etfin"></td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="form-label" width="170px">Basic
                                                                            Salary</td>
                                                                        <td id="esal"></td>
                                                                        <td width="75px"></td>
                                                                        <td class="form-label" width="170px">Basic
                                                                            Salary For EPF &nbsp;&nbsp;Rs.</td>
                                                                        <td id="basicsalepf"></td>
                                                                    </tr>

                                                            </table>
                                                        </div>
                                                        <hr />
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>

                                        <table hidden="hidden">
                                            <tr>
                                                <td><u>Leave Details</u></td>
                                            </tr>
                                            <tr>
                                                <td>&nbsp;&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="form-group">
                                                        <label for="exampleInputEmail1" id="totalLeave"></label>
                                                        <label for="exampleInputEmail1" id="getLeave_1"></label>
                                                        <label for="exampleInputEmail1" id="AvailableLeave_1"></label>
                                                        <label for="exampleInputEmail1" id="getLeave_2"></label>
                                                        <label for="exampleInputEmail1" id="AvailableLeave_2"></label>
                                                        <label for="exampleInputEmail1" id="getshortleaves"></label>
                                                        <label for="exampleInputEmail1"
                                                            id="availableshortleaves"></label>
                                                        <label for="exampleInputEmail1" id="nopayleaves"></label>
                                                        <label for="exampleInputEmail1" id="leave_leaves"></label>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table></br>


                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <h4>Salary Increasements</h4>
                                            <div id="salinc">
                                                <p>There are no any increasements yet.</p>
                                            </div>
                                        </div>

                                        <br />
                                        <table class="table table-sm">
                                            <tr>
                                                <td>Date</td>
                                                <td><input type="date" id="indate" class="input-text"
                                                        style="width: 200px;"></td>
                                            </tr>
                                            <tr>
                                                <td>Reason</td>
                                                <td><input type="text" id="inreason" class="input-text"
                                                        style="width: 200px;"></td>
                                            </tr>
                                            <tr>
                                                <td>Amount Rs. &nbsp;</td>
                                                <td><input type="number" id="inamount" class="input-text"
                                                        style="width: 200px;"></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td align="right"><input type="button" value="Add Increasement"
                                                        class="btn btn-primary" style="margin-top: 5px;float: right;"
                                                        onclick="addIncreasement()"></td>
                                            </tr>
                                        </table>

                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <h4>Salary Advance Payments</h4>
                                            <p id="totAdv" style="font-size: 16px;"></p>
                                            <div id="saladp" style="width: 450px;">
                                                <p>There are no any payments yet.</p>
                                            </div>
                                        </div>
                                        <table class="table table-striped">
                                            <tr>
                                                <td>Date</td>
                                                <td><input type="date" id="apdate" class="input-text"
                                                        style="width: 200px;"></td>
                                            </tr>
                                            <tr>
                                                <td>Account</td>
                                                <td>
                                                    <select id="account" class="select-basic"
                                                        style="width: 200px; height: 23px;">
                                                        <?php
                                                        $query = "select * from bankaccounts";
                                                        $res = Search($query);
                                                        while ($result = mysqli_fetch_assoc($res)) {
                                                        ?>
                                                        <option value="<?php echo $result["baid"]; ?>">
                                                            <?php echo $result["name"]; ?> </option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Amount Rs. &nbsp;</td>
                                                <td><input type="number" id="apamount" class="input-text"
                                                        style="width: 200px;" onkeyup="maxAdvance()"></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td align="right"><input type="button" value="Pay Advance"
                                                        class="btn btn-primary" style="margin-top: 5px;"
                                                        onclick="advancePayment()"></td>
                                            </tr>
                                        </table>

                                        <div class="col-md-9 col-sm-9 col-xs-12" hidden="hidden">
                                            <h4>Meal Allowances</h4>
                                            <div id="mealallow">
                                                <p>There are no any meal allowances yet.</p>
                                            </div>
                                        </div>

                                        <table class="table table-sm" hidden="hidden">
                                            <tr>
                                                <td>Date</td>
                                                <td><input type="date" id="allowdate" class="input-text"
                                                        style="width: 200px;"></td>
                                            </tr>
                                            <tr>
                                                <td>Total Amount Rs. &nbsp;</td>
                                                <td><input type="text" id="allowtotamount" class="input-text"
                                                        style="width: 200px;"></td>
                                            </tr>
                                            <tr>
                                                <td>Days</td>
                                                <td><input type="text" id="mealdays" class="input-text"
                                                        onkeyup="calculateMealAllowances()" style="width: 200px;"></td>
                                            </tr>
                                            <tr>
                                                <td>Remaining Amount Rs. &nbsp;</td>
                                                <td><input type="text" id="mealremaining" class="input-text"
                                                        style="width: 200px;"></td>
                                            </tr>
                                            <tr>
                                                <td></td>
                                                <td align="right"><input type="button" value="Add Amount"
                                                        onclick="EnterMealRemaining()" class="btn btn-primary"
                                                        style="margin-top: 5px;float: right;"></td>
                                            </tr>
                                        </table>

                                        <input type="text" name="empact" id="empact" hidden="hidden">

                                    </td>
                                    <td width="10%">

                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td valign="top" width="70%">
                            <div id="salsheet">
                                <table id="selectmny" style="float: center;">
                                    <tr>
                                        <td>
                                            <h4 style="float: left;">Salary Calculator </h4>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td> Date From :<input type="date" id="date1" name="date1"
                                                onchange="getPayroll()" /></td>
                                        <td> To :<input type="date" id="date2" name="date2" onchange="getPayroll()" />
                                        </td>

                                    </tr>
                                </table>
                                <table class="table table-striped">
                                    <tr>
                                        <td id="attendanseDetails">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr style="height: 50px;">
                                                        <td class="form-label" width="170px">Total Attendance</td>
                                                        <td id="totat">0</td>
                                                        <td width="85px"></td>
                                                        <td class="form-label" width="170px">Total Leaves</td>
                                                        <td id="totLeaves">0</td>
                                                    </tr>
                                                </thead>
                                                <tbody id="detailsTable">
                                                    <tr style="height: 50px;">
                                                        <td class="form-label">Total Working Hours</td>
                                                        <td id="toth">0</td>
                                                        <td width="85px"></td>
                                                        <td class="form-label">Total Short Leaves</td>
                                                        <td id="totsl">0</td>
                                                    </tr>

                                                    <tr style="height: 50px;">
                                                        <td class="form-label">Late Att. Minutes</td>
                                                        <td id="lam">0.00</td>
                                                        <td width="85px"></td>
                                                        <td class="form-label">Late Att. Deduction</td>
                                                        <td id="lad">0.00</td>
                                                    </tr>

                                                    <tr style="height: 50px;">
                                                        <td class="form-label">Total OT Hours</td>
                                                        <td id="tototh"><input type="text" id="totothcus"
                                                                class="input-text" style="width: 100px;" /><span
                                                                id="totothcusLBL"></span></td>
                                                        <td width="85px"></td>
                                                        <td class="form-label">OT Amount Rs.</td>
                                                        <td id="otal">0.00</td>
                                                    </tr>

                                                    <tr>

                                                        <td class="form-label">No-Pay Days</td>

                                                        <td>
                                                            <!-- <span id="nopayded"></span> / -->
                                                            <input type="number" style="width: 50px;" id="totnp"
                                                                name="totnp" onkeyup="calculateNOPAY()" /> <span
                                                                id="npdaysLBL"></span>&nbsp;Days
                                                        </td>

                                                        <td width="50px"></td>

                                                        <td class="form-label" hidden="hidden">Attendance Incentive Rs.
                                                        </td>
                                                        <td id="aalw" hidden="hidden">0.00</td>

                                                        <td class="form-label">No-Pay Deduction Rs.</td>
                                                        <td id="nopayded">0.00</td>
                                                    </tr>

                                                    <tr style="height: 50px;">
                                                        <td class="form-label">Fixed Allowance Rs.</td>
                                                        <td id="attallow">0.00</td>
                                                        <td width="85px"></td>
                                                        <td class="form-label">Vehicle Allowance Rs.</td>
                                                        <td id="trvlallow">0.00</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td id="SalaryDeductions"
                                            style="border-bottom: 1px solid black; border-top: 1px solid black;">
                                            <br />
                                            <h4><u>Salary Deductions</u></h4>

                                            <div style="margin-left: 30px;" id="SalaryDeduction">

                                            </div>

                                            <p><span class="form-label" style="font-size: 12px;">Total Salary Deductions
                                                    Rs.&nbsp;</span><span id="totsalded">0.00</span></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td id="FactoryLoans" style="border-bottom: 1px solid black;">
                                            <h4><u>Company Loans</u></h4>

                                            <div style="margin-left: 30px;" id="FactoryLoan">

                                            </div>

                                            <p><span class="form-label" style="font-size: 12px;">Total Company Loans
                                                    Rs.&nbsp;</span><span id="totfacloan">0.00</span></p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td id="SalaryIncreasements" style="border-bottom: 1px solid black;">
                                            <h4><u>Salary Increments</u></h4>

                                            <div style="margin-left: 30px;" id="SalaryIncreasement">

                                            </div>

                                            <p><span class="form-label" style="font-size: 12px;">Total Salary Increments
                                                    Rs.&nbsp;</span><span id="totsali">0.00</span></p>
                                        </td>
                                    </tr>
                                    <tr id="advpayments" style="border-bottom: 1px solid black;">
                                        <td>
                                            <h4><u>Salary Advance Payments</u></h4>

                                            <div style="margin-left: 30px;" id="saladpx">

                                            </div>

                                        </td>
                                    </tr>

                                    <tr>
                                        <td id="totsaldiv">
                                            <h4><u>Monthly Salary</u></h4>

                                            <div style="margin-left: 30px;">

                                            </div>

                                            <p>
                                                <span class="form-label" style="font-size: 12px;">Total Salary
                                                    Rs.&nbsp;</span><span id="totsalary">0.00</span>
                                            </p>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td id="totTax">
                                            <h4><u>Payee Tax Amount</u></h4>

                                            <div style="margin-left: 30px;">

                                            </div>

                                            <p>
                                                <span class="form-label" style="font-size: 12px;">Tax Amount
                                                    Rs.&nbsp;</span><span id="tottaxamt">0.00</span>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <table>
                                <tr>
                                    <td>
                                        Salary Payment : <input type="text" id="paid_sal" name="paid_sal"
                                            readonly="readonly" />
                                    </td>

                                </tr>
                                <tr>
                                    <td>&nbsp;&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>
                                        <button
                                            style="margin-left: 100px; width: 200px; height: 50px; border-radius: 10px;"
                                            value="Pay Salary" id="btnPaySal" onclick="doSalaryPayment()"
                                            class="btn btn-success"><i class="fa fa-money"
                                                aria-hidden="true"></i>&nbsp;&nbsp;Pay Salary</button>
                                    </td>
                                    <td id="updsalbtn">
                                        <button
                                            style="margin-left: 100px; width: 200px; height: 50px; border-radius: 10px;"
                                            value="Update Salary" id="btnPaySal" onclick="EditSalaryPayment()"
                                            class="btn btn-warning"><i class="fa fa-money"
                                                aria-hidden="true"></i>&nbsp;&nbsp;Update Salary</button>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <input type="text" name="budALLOWANCE" id="budALLOWANCE" hidden="hidden">

            </div>
            <div id="space"></div>
            <?php include("../Contains/footer.php"); ?>
        </div>
    </div>
</body>

</html>