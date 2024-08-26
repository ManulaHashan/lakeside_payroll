<?php
error_reporting(0);
include("../Contains/header.php");
include '../DB/DB.php';
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Handle Roster Plans | Apex Payroll</title>
    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="../Images/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../Images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../Images/favicon/favicon-16x16.png">
    <link href="../Styles/contains.css" rel="stylesheet" type="text/css">
    <link href="../Styles/appStyles.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/css/sweet-alert.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/nprogress/nprogress.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/animate.css/animate.min.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/css/custom.min.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/iCheck/skins/flat/green.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.13.5/xlsx.full.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.13.5/jszip.js"></script>

    <script src="../JS/jquery-3.1.0.js"></script>
    <script src="../JS/numeral.min.js"></script>

    <script type="text/javascript">
    window.onload = function() {
        $('#loading').hide();
        setSpace();
        // $('#editshift').hide();
        var date = new Date();
        document.getElementById('fromDate').valueAsDate = date;
        document.getElementById('toDate').valueAsDate = date;
        loadRecordToTable();
        loadSelectedUserRecord();

    }

    // $(document).ready(function() {
    //     $('#loading').hide();
    //     setSpace();
    //     // $('#editshift').hide();
    //     var date = new Date();
    //     document.getElementById('shftdatefrom').valueAsDate = date;
    //     document.getElementById('shftdateto').valueAsDate = date;
    //     loadShifts();
    // });

    $(document).ajaxStart(function() {
        $('#loading').show();
    }).ajaxStop(function() {
        $('#loading').hide();
    });

    function setSpace() {
        var wheight = $(window).height();
        var bheight = $('#body').height();

        $('#space').height(180);
    }

    // SAVE Data TO DB
    function SaveClaimData() {
        var empid = $('#empid').val();
        var date = $('#billdate').val();
        var section = $('#section').val();
        var amount = $('#amount').val();
        var reason = $('#reason').val();
        var opd_bal = $('#opd_bal').val();
        var ipd_bal = $('#ipd_bal').val();

        var data_record = {
            'Empid': empid,
            'Date': date,
            'Section': section,
            'Amount': amount,
            'Reason': reason
        }
        var json_data = JSON.stringify(data_record);


        // alert(empid + " " + date + " " + section + " " + amount + " " + reason);

        if (date == "" || amount == "") {
            alert("Please fill all records!");
        } else {

            if (section == "1") {
                if (opd_bal <= 0) {
                    alert("OPD Balance limit is exceeded!");

                } else if (opd_bal < amount) {
                    alert("Can`t claim more than OPD balance!");
                } else {
                    $.ajax({
                        type: 'POST',
                        url: "../Controller/emp_opd_ipd_claim.php?request=SaveClaimData&claimData=" + json_data,
                        success: function(data) {
                            if (data == 0) {
                                alert("Claim record already added in this date!");

                            } else if (data == 2) {
                                alert("Error!");

                            } else {
                                alert("Claim records added!");
                                clearDataFields();
                                loadRecordToTable();
                                loadSelectedUserRecord()

                            }
                        }
                    });
                }


            } else {
                if (ipd_bal <= 0) {
                    alert("IPD Balance limit is exceeded!");

                } else if (ipd_bal < amount) {
                    alert("Can`t claim more than IPD balance!");
                } else {
                    $.ajax({
                        type: 'POST',
                        url: "../Controller/emp_opd_ipd_claim.php?request=SaveClaimData&claimData=" + json_data,
                        success: function(data) {
                            if (data == 0) {
                                alert("Claim record already added in this date!");

                            } else if (data == 2) {
                                alert("Error!");

                            } else {
                                alert("Claim records added!");
                                clearDataFields();
                                loadRecordToTable();
                                loadSelectedUserRecord()

                            }
                        }
                    });
                }
            }
        }
    }


    // ***********************Load details to table**************************************/
    //Load all details without search.(laod all data in table without any filter)
    // function loadRecordToTable() {
    //     var request_url = "../Controller/emp_opd_ipd_claim.php?request=getAllDetails";
    //     $.ajax({
    //         type: "GET",
    //         url: request_url,
    //         success: function(tbl_records) {
    //             $('#record_tbl').html(tbl_records);
    //         },
    //         error: function() {
    //             alert('Failed to load data.'); 
    //         }
    //     });
    // }
    //*************************************************************************************/


    // ***********************Load and search details to table************************
    function loadRecordToTable() {
        var from = $('#fromDate').val();
        var to = $('#toDate').val();
        var searchsection = $('#searchsection').val();
        var empdata = $('#empdata').val();
        var claimstatus = $('#claimstatus').val();


        var data_record = {
            "From": from,
            "To": to,
            "SearchSection": searchsection,
            "Empdata": empdata,
            "Claimstatus": claimstatus
        }

        var json_data = JSON.stringify(data_record);

        var request_url = "../Controller/emp_opd_ipd_claim.php?request=getAllDetails&searchData=" + json_data;
        $.ajax({
            type: "GET",
            url: request_url,
            success: function(tbl_records) {
                // alert(tbl_records)
                $('#record_tbl').html(tbl_records);
            },
            error: function() {
                alert('Failed to load data.'); // Error handling
            }

        });
    }


    // ***********************Load selected record to text feilds************************
    function loadSelectedRecord(userID) {
        var request_url = "../Controller/emp_opd_ipd_claim.php?request=getAllDetailsByEmpID&EmpID=" + userID;
        $.ajax({
            type: "GET",
            url: request_url,
            success: function(user_records) {
                var data_array = user_records.split("#");
                $('#claim_id').val(data_array[0]);
                $('#empid').val(data_array[3]);
                $('#billdate').val(data_array[1]);
                $('#section').val(data_array[2]);
                $('#amount').val(data_array[4]);
                $('#reason').val(data_array[7]);
                loadSelectedUserRecord()
            },
            error: function() {
                alert('Failed to load selected record.');
            }
        });
    }

    // ***********************Load selected Employee Claim details to right side table.************************

    function loadSelectedUserRecord() {
        var userID = $('#empid').val();
        var request_url = "../Controller/emp_opd_ipd_claim.php?request=getAllClaimDetailsByEmpID&EmpID=" + userID;
        $.ajax({
            type: "GET",
            url: request_url,
            success: function(user_records) {

                var data = JSON.parse(user_records);


                var opd_limit = parseFloat(data.opd_limit);
                var ipd_limit = parseFloat(data.ipd_limit);

                var opd_taken = parseFloat(data.opd_taken);
                var ipd_taken = parseFloat(data.ipd_taken);

                var opd_balance = opd_limit - opd_taken;
                var ipd_balance = ipd_limit - ipd_taken;


                $('#opd_limit').text(opd_limit.toFixed(2));
                $('#opd_taken').text(opd_taken.toFixed(2));
                $('#opd_balance').text(opd_balance.toFixed(2));
                $('#opd_bal').val(opd_balance.toFixed(2));
                $('#ipd_limit').text(ipd_limit.toFixed(2));
                $('#ipd_taken').text(ipd_taken.toFixed(2));
                $('#ipd_balance').text(ipd_balance.toFixed(2));
                $('#ipd_bal').val(ipd_balance.toFixed(2));
            },
            error: function() {
                alert('Failed to load selected record.');
            }
        });
    }

    // ***********************Cleara text feilds Details************************

    function clearDataFields() {
        // var empid = $('#empid').val();
        $('#claim_id').val("");
        $('#billdate').val("");
        $('#section').val("1");
        $('#amount').val("");
        $('#reason').val("");
    }
    // ***********************Update Details************************
    function updateRecords() {
        var claim_id = $('#claim_id').val();
        var empid = $('#empid').val();
        var billdate = $('#billdate').val();
        var section = $('#section').val();
        var amount = $('#amount').val();
        var reason = $('#reason').val();
        var opd_bal = $('#opd_bal').val();
        var ipd_bal = $('#ipd_bal').val();

        if (empid === "" || amount == "") {
            alert("Please select employee!");
        } else {
            var data_record = {
                "Claim_id": claim_id,
                "Empid": empid,
                "Date": billdate,
                "Section": section,
                "Amount": amount,
                "Reason": reason
            };

            var json_data = JSON.stringify(data_record);
            if (section == 1) {
                if (opd_bal <= 0) {
                    alert("OPD Balance limit is exceeded!");
                } else if (opd_bal < amount) {
                    alert("Can`t claim more than OPD balance!");
                } else {
                    $.ajax({
                        type: "POST",
                        url: "../Controller/emp_opd_ipd_claim.php?request=updateRecords&claimData=" +
                            encodeURIComponent(json_data),
                        success: function(response) {
                            if (response === "1") {
                                alert("Data updated successfully!");
                                clearDataFields();
                                loadSelectedUserRecord()
                                loadRecordToTable();
                            } else if (response === "2") {
                                alert("Error updating data!");
                            } else {
                                alert("Data not updated!");
                            }
                        },
                        error: function() {
                            alert("Failed to update data.");
                        }
                    });
                }

            } else {

                if (ipd_bal <= 0) {
                    alert("IPD Balance limit is exceeded!");
                } else if (ipd_bal < amount) {
                    alert("Can`t claim more than IPD balance!");
                } else {
                    $.ajax({
                        type: "POST",
                        url: "../Controller/emp_opd_ipd_claim.php?request=updateRecords&claimData=" +
                            encodeURIComponent(json_data),
                        success: function(response) {
                            if (response === "1") {
                                alert("Data updated successfully!");
                                clearDataFields();
                                loadSelectedUserRecord()
                                loadRecordToTable();
                            } else if (response === "2") {
                                alert("Error updating data!");
                            } else {
                                alert("Data not updated!");
                            }
                        },
                        error: function() {
                            alert("Failed to update data.");
                        }
                    });

                }


            }
        }
    }

    // ***********************Delete Details************************
    function deleteRecords() {
        var claim_id = $('#claim_id').val();
        var is_paid = $('#is_paid').val();

        if (claim_id === "") {
            alert("Please select record to delete!");

        } else if (is_paid = "1") {
            alert("Claim already paid!");

        } else {
            var data_record = {
                "Claim_id": claim_id,
                "Is_paid": is_paid
            }

            var json_data = JSON.stringify(data_record);

            $.ajax({
                type: "POST",
                url: "../Controller/emp_opd_ipd_claim.php?request=deleteRecords&claimID=" + json_data,
                success: function(response) {
                    if (response === "1") {
                        alert("Data Delete successful!");
                        clearDataFields();
                        loadSelectedUserRecord();
                        loadRecordToTable();
                    } else {
                        alert("Error deleting data!");
                    }
                }
            });
        }
    }



    // Attach the validation function to the form submission event
    $('form').submit(function(event) {
        if (!validateBalanceAmount()) {
            event.preventDefault(); // Stop the form from submitting
        }
    });
    </script>

</head>

<body id="body" class="nav-md" style="background-color: white;">
    <?php include("../Contains/titlebar_dboard.php"); ?>
    <div class="container body">
        <div class="main_container">
            <div class="" style="width: 100%; margin: 1%;" role="main">
                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="row x_title">
                            <div class="col-md-6">
                                <h3>Handle OPD And IPD Claim Bills <small>Manage OPD and IPD claim bill details</small>
                                </h3>
                            </div>
                        </div>

                        <div style="display: table;">
                            <div style="display: table-cell;">

                                <table>
                                    <tr>
                                        <td>
                                            <table style="margin-left: 10px;">
                                                <tr>
                                                    <td colspan="2">
                                                        <h4>Add OPD and IPD Claim Bill Records For Employee</h4>
                                                        </br>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td height="35px;" width="200px;">Employee </td>
                                                    <td>
                                                        <select id="empid" name="empid" class="form-control"
                                                            style="width: 300px; border: 1px solid black; height: 28px;"
                                                            onchange="loadSelectedUserRecord()">
                                                            <?php
                                                            $query = "select jobcode,uid,fname,lname,epfno from user where isactive='1' and uid != '2'  order by length(jobcode),jobcode ASC";
                                                            $res = Search($query);
                                                            while ($result = mysqli_fetch_assoc($res)) {
                                                            ?>
                                                            <option value="<?php echo $result["uid"]; ?>">
                                                                <?php echo $result["jobcode"]; ?>: &nbsp;
                                                                <?php echo $result["fname"]; ?> </option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td height="35px;" width="200px;">Date</td>
                                                    <td>
                                                        <input type="date" name="billdate" id="billdate"
                                                            class="form-control"
                                                            style="width: 300px; border: 1px solid black; height: 28px;">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td height="35px;" width="200px;">Section</td>
                                                    <td>
                                                        <select id="section" name="section" class="form-control"
                                                            style="width: 300px; border: 1px solid black; height: 28px;">
                                                            <option value="1">OPD</option>
                                                            <option value="2">IPD</option>

                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td height="35px;" width="200px;">Amount</td>
                                                    <td>
                                                        <input type="text" id="amount" name="amount"
                                                            class="form-control"
                                                            style="width: 300px; border: 1px solid black; height: 28px;">
                                                        </input>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td height="35px;" width="200px;">Reason</td>
                                                    <td>
                                                        <input type="text" id="reason" name="reason"
                                                            class="form-control"
                                                            style="width: 300px; border: 1px solid black; height: 28px;">
                                                        </input>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>&nbsp;&nbsp;&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td align="right">
                                                        <a id="btnshiftupld" class="btn btn-primary"
                                                            onclick="SaveClaimData()"
                                                            style="width: 150px; text-decoration: none; margin-left: 0px;">Save</a>
                                                    </td>
                                                    <td align="right">
                                                        <a id="claimUpdate" class="btn btn-warning"
                                                            onclick="updateRecords()"
                                                            style="width: 150px; text-decoration: none; margin-left: 0px;">Update</a>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger w-100"
                                                            id="deletebtn" onclick="deleteRecords()">Delete</button>
                                                    </td>


                                                </tr>
                                            </table>

                                        </td>
                                        <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        </td>
                                        <td>

                                            <table class="table table-bordered">
                                                <tr>
                                                    <thead>
                                                        <th
                                                            style="border-top: 1px solid transparent; border-left: 1px solid transparent;">
                                                            #</th>


                                                        <th>Value Limit</th>
                                                        <th>Taken Amount</th>
                                                        <th>Balance Amount</th>
                                                    </thead>
                                                    <tbody>

                                                        <tr>
                                                            <td align="center"> OPD
                                                            </td>
                                                            <td align="right" id="opd_limit">0.00</td>
                                                            <td align="right" id="opd_taken">0.00</td>
                                                            <td align="right" id="opd_balance">0.00</td>
                                                        </tr>
                                                        <tr>
                                                            <td align="center"> IPD
                                                            </td>
                                                            <td align="right" id="ipd_limit">0.00</td>
                                                            <td align="right" id="ipd_taken">0.00</td>
                                                            <td align="right" id="ipd_balance">0.00</td>
                                                        </tr>

                                                    </tbody>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                                <input type="hidden" id="claim_id" name="claim_id">
                                <input type="hidden" id="opd_bal" name="opd_bal">
                                <input type="hidden" id="ipd_bal" name="ipd_bal">
                                <input type="hidden" id="is_paid" name="is_paid">
                            </div>
                            <div style="display: table-cell;width: 10%"></div>
                        </div>
                        <hr />

                        <div style="margin-top: 0px" class="col-md-10">
                            <h3>View / Edit Claim &nbsp; <small>View / Edit Claim Details</small></h3>
                            <table>
                                <tr>
                                    <td>From&nbsp;&nbsp;</td>
                                    <td> <input type="date" id="fromDate" name="fromDate" style="width: 200px;"
                                            class="form-control" /></td>
                                    <td>&nbsp;&nbsp;To&nbsp;&nbsp;</td>
                                    <td> <input type="date" id="toDate" name="toDate" style="width: 200px;"
                                            class="form-control" /></td>
                                    <td>&nbsp;&nbsp;Section&nbsp;&nbsp;</td>
                                    <td>
                                        <select id="searchsection" name="searchsection" class="form-control"
                                            style="width: 200px;">
                                            <option value="%">All</option>
                                            <option value="1">OPD</option>
                                            <option value="2">IPD</option>
                                        </select>
                                    </td>
                                    <td>&nbsp;&nbsp;Employee&nbsp;&nbsp;</td>
                                    <td>
                                        <select id="empdata" name="empdata" class="form-control" style="width: 200px;">
                                            <option value="%">All</option>
                                            <?php
                                            $query = "select jobcode,uid,fname,lname,epfno from user where isactive='1' and uid != '2' order by length(jobcode),jobcode ASC";
                                            $res = Search($query);
                                            while ($result = mysqli_fetch_assoc($res)) {
                                            ?>
                                            <option value="<?php echo $result["uid"]; ?>">
                                                <?php echo $result["jobcode"]; ?>: &nbsp;
                                                <?php echo $result["fname"]; ?> </option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td>&nbsp;&nbsp;Status&nbsp;&nbsp;</td>
                                    <td>
                                        <select id="claimstatus" name="claimstatus" class="form-control"
                                            style="width: 100px;">
                                            <option value="%">All</option>
                                            <option value="1">Active</option>
                                            <option value="0">Not-Active</option>
                                        </select>
                                    </td>
                                    <td>&nbsp;&nbsp;</td>
                                    <td><img src="../Icons/search.png" onclick="loadRecordToTable()"
                                            style="cursor: pointer" />
                                    </td>
                                </tr>
                            </table></br>

                            <div style="overflow:scroll; height:400px;" id=record_tbl></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    </br>
    <?php include("../Contains/footer.php"); ?>
    </div>
    </div>
</body>

</html>