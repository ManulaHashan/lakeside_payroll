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
    $(document).ready(function() {
        $('#loading').hide();
        setSpace();
        $('#editshift').hide();
        var date = new Date();
        document.getElementById('shftdatefrom').valueAsDate = date;
        document.getElementById('shftdateto').valueAsDate = date;
        loadShifts();
    });

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

    function SaveShiftData() {
        var empid = $('#shiftceemp').val();
        var date = $('#shiftdate').val();
        var type = $('#shifttype').val();

        if (date == "" || type == "%") {
            alert("Please fill all records!");
        } else {
            var url = "../Controller/emp_shift_handle.php?request=SaveShiftData&EMP_ID=" + empid + "&DATE=" + date +
                "&TYPE=" + type;
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    if (data == 0) {
                        alert("Roster record already added in this date!");
                        loadShifts();
                    } else if (data == 2) {
                        alert("Error!");
                        loadShifts();
                    } else {
                        alert("Roster records added!");
                        loadShifts();
                        $('#shiftdate').val("");
                        $('#shifttype').val("%");
                    }
                }
            });
        }
    }

    function deleteShift(shiftid) {
        let confirmAction = confirm("Do you want to delete this roster record?");

        if (confirmAction) {

            var url = "../Controller/emp_shift_handle.php?request=deleteshift&shiftid=" + shiftid;
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    alert(data);
                    showShiftData();
                    loadUserWiseRosters();
                }
            });
        }
    }

    function loadShifts() {
        var empid = $('#empdata').val();
        var datefrom = $('#shftdatefrom').val();
        var dateto = $('#shftdateto').val();
        var type = $('#shname').val();
        var status = $('#shstatus').val();

        var url = "../Controller/emp_shift_handle.php?request=getShiftDetails&EMP_ID=" + empid + "&DATEFROM=" +
            datefrom + "&DATETO=" + dateto + "&TYPE=" + type + "&STATUS=" + status;

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                $('#rosttable').html(data);
            }
        });
    }

    var selectedShiftID = "";
    var selectedUserID = "";

    function select_shift(detail) {
        var arr = detail.split("#");
        $("#shftdate").val(arr[1]);
        $("#shiftname").val(arr[2]);
        selectedShiftID = arr[0];
        selectedUserID = arr[3];
        $('#editshift').show();
    }

    function update_rost() {
        if (selectedShiftID !== "") {
            var name = $("#shiftname").val();
            var date = $("#shftdate").val();
            var selectedShift = selectedShiftID;
            var selectedUser = selectedUserID;

            var url = "../Controller/emp_shift_handle.php?request=updroster&name=" + name + "&date=" + date + "&id=" +
                selectedShift + "&user=" + selectedUser;
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    alert(data);
                    loadShifts();
                    selectedShiftID = "";
                    selectedUserID = "";
                    $('#editshift').hide();
                }
            });
        } else {
            alert("Please select the record you want to update from the table below!");
        }
    }

    function deleterost(rostid) {
        let confirmAction = confirm("Do you want to delete this roster record?");

        if (confirmAction) {

            var url = "../Controller/emp_shift_handle.php?request=deleteroster&rid=" + rostid;
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    alert(data);
                    loadShifts();
                    $('#editshift').hide();
                }
            });
        }
    }


    //Upload Employee Shifts
    function UploadEmployeeShiftsData() {
        var file = document.getElementById("shiftfile").files.length;

        if (file == 0) {
            alert("Please add the excel file!");
        } else {
            var fileUpload = document.getElementById("shiftfile");
            var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.xls|.xlsx)$/;
            if (regex.test(fileUpload.value.toLowerCase())) {
                if (typeof(FileReader) != "undefined") {
                    var reader = new FileReader();
                    if (reader.readAsBinaryString) {
                        reader.onload = function(e) {
                            GetEmployeeShiftTableFromExcel(e.target.result);
                        };
                        reader.readAsBinaryString(fileUpload.files[0]);
                    } else {
                        reader.onload = function(e) {
                            var data = "";
                            var bytes = new Uint8Array(e.target.result);
                            for (var i = 0; i < bytes.byteLength; i++) {
                                data += String.fromCharCode(bytes[i]);
                            }
                            GetEmployeeShiftTableFromExcel(data);
                        };
                        reader.readAsArrayBuffer(fileUpload.files[0]);
                    }
                } else {
                    alert("This browser does not support HTML5.");
                }
            } else {
                alert("Please upload a valid Excel file.");
            }
        }

    };


    function GetEmployeeShiftTableFromExcel(data) {
        var workbook = XLSX.read(data, {
            type: 'binary'
        });
        var Sheet = workbook.SheetNames[0];
        var excelRows = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[Sheet]);
        var successCount = 0;

        function handleCompletion() {
            alert("All records added successfully!");
        }

        function handleCompletion2() {
            alert("Some records already added!");
        }

        function handleCompletion3() {
            alert("Error!");
        }

        var totalRows = excelRows.length;
        excelRows.forEach(function(row, index) {
            AddData(row["EMP No"], row["Date"], row["Rost No"], function(result) {
                if (result === "1") {
                    successCount++;
                    if (successCount === totalRows) {
                        handleCompletion();
                    }
                } else if (result === "2") {
                    successCount++;
                    if (successCount === totalRows) {
                        handleCompletion2();
                    }
                } else {
                    successCount++;
                    if (successCount === totalRows) {
                        handleCompletion3();
                    }
                }
            });
        });
    };


    function AddData(empno, date, shiftno, callback) {
        var url = "../Controller/emp_shift_handle.php?request=AddExcelShiftData&empno=" + empno + "&date=" + date +
            "&shiftno=" + shiftno;
        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                if (data == "1") {
                    callback("1"); // Call the callback function with success value
                } else if (data == "2") {
                    callback("2"); // Call the callback function with success value
                } else {
                    callback("0"); // Call the callback function with failure value
                }
            },
            error: function(xhr, status, error) {
                alert("Error occurred while processing data:", error);
                callback("-1"); // Call the callback function with error value
            }
        });
    }


    function DownloadExcelTemplatesFiles(fileName) {
        //Set the File URL.
        var url = "../Images/excel_templates/" + fileName;

        //Create XMLHTTP Request.
        var req = new XMLHttpRequest();
        req.open("GET", url, true);
        req.responseType = "blob";
        req.onload = function() {
            //Convert the Byte Data to BLOB object.
            var blob = new Blob([req.response], {
                type: "application/octetstream"
            });

            //Check the Browser type and download the File.
            var isIE = false || !!document.documentMode;
            if (isIE) {
                window.navigator.msSaveBlob(blob, fileName);
            } else {
                var url = window.URL || window.webkitURL;
                link = url.createObjectURL(blob);
                var a = document.createElement("a");
                a.setAttribute("download", fileName);
                a.setAttribute("href", link);
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            }
        };
        req.send();
    };
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
                                <h3>Handle Roster Plans <small>Manage employee roster plans</small></h3>
                            </div>
                        </div>

                        <div style="display: table;">
                            <div style="display: table-cell;">
                                <form action="../Controller/emp_shift_handle.php" method="POST"
                                    enctype="multipart/form-data">
                                    <table>
                                        <tr>
                                            <td>
                                                <table style="margin-left: 10px;">
                                                    <tr>
                                                        <td colspan="2">
                                                            <h4>Add Roster Date For Employee</h4></br>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td height="35px;" width="200px;">Employee </td>
                                                        <td>
                                                            <select id="shiftceemp" name="shiftceemp"
                                                                class="form-control"
                                                                style="width: 200px; border: 1px solid black; height: 28px;">
                                                                <?php
                                                                $query = "select jobcode,uid,fname,lname,epfno from user where isactive='1' and work_typ = '2' and uid != '2' order by length(jobcode),jobcode ASC";
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
                                                            <input type="date" name="shiftdate" id="shiftdate"
                                                                class="form-control"
                                                                style="width: 200px; border: 1px solid black; height: 28px;">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td height="35px;" width="200px;">Rost Name</td>
                                                        <td>
                                                            <select id="shifttype" name="shifttype" class="form-control"
                                                                style="width: 200px; border: 1px solid black; height: 28px;">
                                                                <option value="%"></option>
                                                                <?php
                                                                $query = "select swtpsid,name from shift_working_time_profile_settings order by name ASC";
                                                                $res = Search($query);
                                                                while ($result = mysqli_fetch_assoc($res)) {
                                                                ?>
                                                                <option value="<?php echo $result["swtpsid"]; ?>">
                                                                    <?php echo $result["name"]; ?></option>
                                                                <?php } ?>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td align="right">
                                                            <a id="btnshiftupld" class="btn btn-primary"
                                                                onclick="SaveShiftData()"
                                                                style="width: 150px; text-decoration: none; margin-left: 0px;">Save</a>
                                                        </td>
                                                    </tr>
                                                    <tr style="border-bottom: 1px solid black;">
                                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2">
                                                            <h4>Upload Roster Dates Excel For Employee</h4>
                                                            <p>Create rost excel file using this heading names (EMP
                                                                No,Date,Rost No)</p>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td colspan="2">Download rost details template here
                                                            &nbsp;&nbsp;<a id="sftfilesdown" title="Download Excel File"
                                                                class="btn btn-success"
                                                                onclick="DownloadExcelTemplatesFiles('shift_records_template.xlsx')"
                                                                style="width: 50px; text-decoration: none; margin-left: 0px;"><i
                                                                    class="fa fa-file-excel-o"
                                                                    style="float: next; cursor: pointer"></i></a></td>
                                                    </tr>

                                                    <tr>
                                                        <td height="35px;" width="200px;">Rost Detail File</td>
                                                        <td>
                                                            <input type="file" name="shiftfile" id="shiftfile"
                                                                class="input-file;btn btn-default submit"
                                                                style="width: 200px" />
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td></td>
                                                        <td align="right">
                                                            <a id="btnshiftupld" class="btn btn-info"
                                                                onclick="UploadEmployeeShiftsData()"
                                                                style="width: 150px; text-decoration: none; margin-left: 0px;">Upload</a>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            </td>
                                            <td>
                                                <p>Roster Plans</p>
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <thead>
                                                            <th>Roster Code</th>
                                                            <th>Roster Name</th>
                                                            <th colspan="2">Time</th>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $query = "select swtpsid,name,sh_intime,rost_code,sh_outtime from shift_working_time_profile_settings order by swtpsid ASC";
                                                            $res = Search($query);
                                                            while ($result = mysqli_fetch_assoc($res)) {

                                                                if ($result["sh_intime"] == "" || $result["sh_intime"] == null) {
                                                                    $INTIME = "";
                                                                } else {
                                                                    $INTIME = date("H:i A", strtotime($result["sh_intime"]));
                                                                }

                                                                if ($result["sh_outtime"] == "" || $result["sh_outtime"] == null) {
                                                                    $OUTTIME = "";
                                                                } else {
                                                                    $OUTTIME = date("H:i A", strtotime($result["sh_outtime"]));
                                                                }

                                                            ?>
                                                            <tr>
                                                                <td align="center"><?php echo $result["rost_code"]; ?>
                                                                </td>
                                                                <td><?php echo $result["name"]; ?></td>
                                                                <td colspan="2"><?php echo $INTIME; ?> -
                                                                    <?php echo $OUTTIME; ?></td>
                                                            </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </form>
                            </div>
                            <div style="display: table-cell;width: 10%"></div>
                        </div>
                        <hr />

                        <div style="margin-top: 0px" class="col-md-10">
                            <h3>View / Edit Rosters &nbsp; <small>View / Edit Roster Plans</small></h3>
                            <table>
                                <tr>
                                    <td>From&nbsp;&nbsp;</td>
                                    <td> <input type="date" id="shftdatefrom" name="shftdatefrom" style="width: 200px;"
                                            class="form-control" /></td>
                                    <td>&nbsp;&nbsp;To&nbsp;&nbsp;</td>
                                    <td> <input type="date" id="shftdateto" name="shftdateto" style="width: 200px;"
                                            class="form-control" /></td>
                                    <td>&nbsp;&nbsp;Rost&nbsp;&nbsp;</td>
                                    <td>
                                        <select id="shname" name="shname" class="form-control" style="width: 200px;">
                                            <option value="%">All</option>
                                            <?php
                                            $query = "select swtpsid,name from shift_working_time_profile_settings order by name ASC";
                                            $res = Search($query);
                                            while ($result = mysqli_fetch_assoc($res)) {
                                            ?>
                                            <option value="<?php echo $result["swtpsid"]; ?>">
                                                <?php echo $result["name"]; ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td>&nbsp;&nbsp;Employee&nbsp;&nbsp;</td>
                                    <td>
                                        <select id="empdata" name="empdata" class="form-control" style="width: 200px;">
                                            <option value="%">All</option>
                                            <?php
                                            $query = "select jobcode,uid,fname,lname,epfno from user where isactive='1' and work_typ = '2' and uid != '2' order by length(jobcode),jobcode ASC";
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
                                        <select id="shstatus" name="shstatus" class="form-control"
                                            style="width: 100px;">
                                            <option value="%">All</option>
                                            <option value="1">Active</option>
                                            <option value="0">Not-Active</option>
                                        </select>
                                    </td>
                                    <td>&nbsp;&nbsp;</td>
                                    <td><img src="../Icons/search.png" onclick="loadShifts()" style="cursor: pointer" />
                                    </td>
                                </tr>
                            </table></br>
                            <table id="editshift">
                                <tr>
                                    <td colspan="7">
                                        <p><u>- Rost Update Section -</u></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Date&nbsp;&nbsp;&nbsp;</td>
                                    <td> <input type="date" id="shftdate" name="shftdate" style="width: 200px;"
                                            class="form-control" /></td>
                                    <td>&nbsp;&nbsp;&nbsp;Rost Name&nbsp;&nbsp;&nbsp;</td>
                                    <td>
                                        <select id="shiftname" name="shiftname" class="form-control"
                                            style="width: 200px;">
                                            <option value="%"></option>
                                            <?php
                                            $query = "select swtpsid,name from shift_working_time_profile_settings order by name ASC";
                                            $res = Search($query);
                                            while ($result = mysqli_fetch_assoc($res)) {
                                            ?>
                                            <option value="<?php echo $result["swtpsid"]; ?>">
                                                <?php echo $result["name"]; ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td>&nbsp;&nbsp;&nbsp;</td>
                                    <td>&nbsp;&nbsp;<input type="button" id="upholiday" value="Update"
                                            onclick="update_rost()" class="btn btn-warning"></td>
                                </tr>
                            </table></br>
                            <div style="overflow:scroll; height:400px;" id=rosttable></div>
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