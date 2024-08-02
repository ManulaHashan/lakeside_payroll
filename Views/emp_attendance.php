<?php
error_reporting(0);
include("../Contains/header.php");
include '../DB/DB.php';
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Employee Attendance | Apex Payroll</title>
    <!-- <link href="../Styles/Stylie.css" rel="stylesheet" type="text/css"> -->
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
        var date = new Date();
        document.getElementById('fxdate').valueAsDate = date;
        document.getElementById('lxdate').valueAsDate = date;
        // $('#half_slot').hide();
        // $('#short_slot').hide();
        // $('#nopay_slot').hide();
        loadAttendance();
        loadHolidays();
        // getEmployeeWiseHalfSlots();
        // getEmployeeWiseShortSlots();

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

    function loadView() {
        $('#smonth').hide();
        $('#syear').hide();
        $('#sfdate').show();
        $('#sldate').show();
    }

    function loadAttendance() {
        var view = $('#type').val();
        var date = $('#fxdate').val();
        var user = $('#emp').val();

        var ldate = $('#lxdate').val();
        var month = $('#month').val();
        var year = $('#year').val();
        var department = $('#dept').val();

        var url = "../Controller/emp_attendance.php?request=getAttendance&date=" + date + "&user=" + user + "&view=" +
            view + "&ldate=" + ldate + "&month=" + month + "&year=" + year + "&department=" + department;

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                var arr = data.split("///");
                $('#attview').html(arr[0]);

                var arr2 = arr[1].split("#");
                $('#tota').html(arr2[0]);
                $('#totaview').html(arr2[0]);
                $('#totot').html(numeral(arr2[1]).format('00.00'));
                $('#totl').html(numeral(arr2[2]).format('00.00'));
                $('#tothd').html(arr2[3]);
                $('#totsl').html(arr2[4]);
                $('#tdotot').html(arr2[5]);
                $('#otc').html(arr2[6]);
                $('#otca').html("<b>Rs. " + numeral(arr2[7]).format('0,0.00') + "</b>");
                $('#tlm').html(arr2[6]);


                xaid = 0;
            }
        });
        setSpace();
    }

    var xaid = 0;

    function selectRecord(aid) {
        xaid = aid;
        var url = "../Controller/emp_attendance.php?request=selectRecord&aid=" + aid;
        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                var arr = data.split("#");
                $('#int').val(arr[0]);
                $('#outt').val(arr[1]);
                $('#sl').val(arr[4]);
                $('#hd').val(arr[5]);
                $('#hrs').val(arr[6]);
                $('#othrs').val(arr[7]);
                $('#dothrs').val(arr[8]);
                $('#lm').val(arr[9]);

            }
        });
    }

    function RemoveLate() {
        if ($('#rmLate').is(':checked')) {

            Late = 0;

            $('#lm').val(Late);


        } else {
            AutoGenAtt();
        }
    }


    function AutoGenAtt() {
        var intime = $('#int').val();
        var outtime = $('#outt').val();

        if ($('#morOT').is(':checked')) {
            AllowMorningOT = "true";
        } else {
            AllowMorningOT = "false";
        }


        var url = "../Controller/emp_attendance.php?request=AutoGenRecord&aid=" + xaid + "&intime=" + intime +
            "&outtime=" + outtime + "&AllowMorningOT=" + AllowMorningOT;
        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                //alert(data);
                var arrAuto = data.split("###");

                $('#hrs').val(arrAuto[0]);
                $('#othrs').val(arrAuto[1]);
                $('#lm').val(arrAuto[2]);
                $('#hd').val(arrAuto[3]);
                $('#sl').val(arrAuto[4]);
                $('#dothrs').val(arrAuto[5]);
                $('#mot').val(arrAuto[6]);
                // loadAttendance();
            }
        });
    }


    function updateRecord() {
        var int = $('#int').val();
        var out = $('#outt').val();

        var hours = $('#hrs').val();
        var othours = $('#othrs').val();
        var dothours = $('#dothrs').val();

        var sl = $('#sl').val();
        var lm = $('#lm').val();

        var hd = $('#hd').val();
        var uid = $('#emp').val();
        var date = $('#fxdate').val();

        var url = "../Controller/emp_attendance.php?request=updateRecord&aid=" + xaid + "&int=" + int + "&out=" + out +
            "&sl=" + sl + "&hd=" + hd + "&uid=" + uid + "&date=" + date + "&hours=" + hours + "&othours=" + othours +
            "&lm=" + lm + "&dot=" + dothours;
        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                alert(data);
                loadAttendance();
            }
        });
    }



    function deleteRecord(aid, date) {
        var x = confirm("Are you sure you want to delete this record?");
        if (x) {
            var url = "../Controller/emp_attendance.php?request=deleteattendance&aid=" + aid + "&attdate=" + date;
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    alert("Record Deleted!");
                    loadAttendance();
                }
            });
        }
    }

    function printdetails() {
        var month = $("fxdate").val();
        var year = $('#lxdate').val();

        var divToPrint0 = document.getElementById('print0');
        var divToPrint1 = document.getElementById('print1');
        var divToPrint2 = document.getElementById('print2');


        var newWin = window.open('', 'Print-Window');
        newWin.document.open();
        newWin.document.write(
            "<!doctype html><html><head><link href='../Styles/contains.css' rel='stylesheet' type='text/css'><link href='../Styles/appStyles.css' rel='stylesheet' type='text/css'><link href='../Vendor/css/sweet-alert.css' rel='stylesheet' type='text/css'><link href='../Vendor/bootstrap/dist/css/bootstrap.min.css' rel='stylesheet' type='text/css'><link href='../Vendor/font-awesome/css/font-awesome.min.css' rel='stylesheet' type='text/css'><link href='../Vendor/nprogress/nprogress.css' rel='stylesheet' type='text/css'><link href='../Vendor/animate.css/animate.min.css' rel='stylesheet' type='text/css'><link href='../Vendor/css/custom.min.css' rel='stylesheet' type='text/css'><link href='../Vendor/iCheck/skins/flat/green.css' rel='stylesheet' type='text/css'><title>Attendance Details - Appex Payroll</title><link href='../Styles/Stylie.css' rel='stylesheet' type='text/css'></head><body style='font-size:11px;'><img src='' width='75%'/> <div style='margin-left:10%;'><h2 class='Report_Header'><u>Attendance Details</u></h2> <br/></h3>" +
            "<h3 style='font-size:14px;'>Employee Name : " + $("#marker option[value='" + $('#marker').val() + "']")
            .text() + " &nbsp; From : " + month + " &nbsp;  TO : " + year + "</h3>" + divToPrint0.innerHTML +
            "<br/>" + divToPrint2.innerHTML + "<br/>" + divToPrint1.innerHTML +
            "<br/><hr/><p style='font-size:12px;'>Appex Payroll - Powered by Appex Solutions. WEB : www.appexsl.com / Email : info@appexsl.com</p></div></body></html>"
            );

        $('#selectmny').show();
    }

    AnnualLeave = 14;
    casualLeave = 7;

    function getLeaveDetails() {
        var x = $("#empl").val !== "";
        if (x) {

            var eid = $('#empl').val();
            var year = $('#yearl').val();
            var month = $('#monthl').val();



            var url = "../Controller/emp_attendance.php?request=getleavedetails&eid=" + eid + "&year=" + year +
                "&month=" + month;
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {

                    var arr = data.split("#/#");

                    var leaves = parseFloat(arr[0]);
                    var balance = AnnualLeave + casualLeave - leaves;

                    $("#totlvs").html("<b>" + arr[0] + "</b>");
                    $("#totlvbal").html("<b>" + balance + "</b>");
                    $("#leavedetails").html("<b>" + arr[1] + "</b>");
                    $("#leavedetailstot").html("<b>" + arr[2] + "</b>");

                    var leavesx = parseFloat(arr[2]);
                    var balancex = AnnualLeave + casualLeave - leavesx;

                    $("#leavedetailsbal").html("<b>" + balancex + "</b>");




                }
            });
        }

    }

    function printLeaves() {


        var divToPrint0 = document.getElementById('leavescope');

        var customHR = "<div style='width:650px; height:1px; background-color:black;'>";

        var newWin = window.open('', 'Print-Window');
        newWin.document.open();

        newWin.document.write(
            "<!doctype html><html><head><title>Leave Report - Appex Payroll</title><link href='../Styles/Stylie.css' rel='stylesheet' type='text/css'></head><body style='font-size:11px; font-family:Arial;'><img src='' width='100%'/> <h2> Satlo Industries </h2> " +
            customHR +
            " </div> <div style='margin-left:0%;'><h3 class='Report_Header'><u>Leave Report</u></h3> <br/></h3>" +
            divToPrint0.innerHTML + "<br/><br/><p>..............................<br/></span>Signature</p> " +
            customHR +
            " <p style='font-size:12px; padding-top:5px;'>Appex Payroll Management System. By Appex Solutions | www.appexsl.com </p></div></body></html>"
            );




    }

    function exportExcel() {

        var dataz = document.getElementById('attview').innerHTML;

        // alert(dataz);

        var url = "../Controller/emp_manage.php?request=setSession";
        $.ajax({
            type: 'POST',
            url: url,
            data: {
                'data': dataz,
                'filename': 'Attendance'
            },
            success: function(data) {

                // alert(data);

                var page = "../Model/excel_export.php";

                window.location = encodeURI(page);
            }
        });
    }

    function showLeaves() {
        var eid = $('#leemp').val();
        var date = $('#ledate').val();

        var url = "../Controller/emp_attendance.php?request=viewLeaves&eid=" + eid + "&date=" + date;
        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                $("#viewleaves").html(data);
            }
        });

    }

    function deleteLeave(lid) {
        var url = "../Controller/emp_attendance.php?request=deleteLeave&lid=" + lid;
        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                alert(data);
                showLeaves();
                leaveCounter();
            }
        });
    }

    $(document).on('keydown keypress', '#jcode', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            var url = '../Controller/emp_payroll.php?request=getuidfromjobcode&jcode=' + $('#jcode').val();
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    $("#emp option[value=" + data + "]").attr('selected', 'selected');
                }
            });
            return false;
        }
    });

    function UploadProcess() {
        //Reference the FileUpload element.
        var fileUpload = document.getElementById("attfile");

        //Validate whether File is valid Excel file.
        var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.xls|.xlsx)$/;
        if (regex.test(fileUpload.value.toLowerCase())) {
            if (typeof(FileReader) != "undefined") {
                var reader = new FileReader();

                //For Browsers other than IE.
                if (reader.readAsBinaryString) {
                    reader.onload = function(e) {
                        GetTableFromExcel(e.target.result);
                    };
                    reader.readAsBinaryString(fileUpload.files[0]);
                } else {
                    //For IE Browser.
                    reader.onload = function(e) {
                        var data = "";
                        var bytes = new Uint8Array(e.target.result);
                        for (var i = 0; i < bytes.byteLength; i++) {
                            data += String.fromCharCode(bytes[i]);
                        }
                        GetTableFromExcel(data);
                    };
                    reader.readAsArrayBuffer(fileUpload.files[0]);
                }
            } else {
                alert("This browser does not support HTML5.");
            }
        } else {
            alert("Please upload a valid Excel file.");
        }
    };


    function GetTableFromExcel(data) {

        //Read the Excel File data in binary
        var workbook = XLSX.read(data, {
            type: 'binary'
        });

        //get the name of First Sheet.
        var Sheet = workbook.SheetNames[0];

        //Read all rows from First Sheet into an JSON array.
        var excelRows = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[Sheet]);

        // Counter to track successful additions
        var successCount = 0;

        // Function to be called after all rows are processed
        function handleCompletion() {
            alert("All records added successfully!");
        }

        function handleCompletion3() {
            alert("Error!");
        }

        // Add the data rows from Excel file.
        var totalRows = excelRows.length;
        excelRows.forEach(function(row, index) {
            AddData(row["EMP No"], row["Date"], row["In Time"], 'IN', row["Out Time"], 'OUT', function(result) {

                if (result === "1") {
                    successCount++;
                    // If all records are successfully added, call handleCompletion
                    if (successCount === totalRows) {
                        handleCompletion();
                    }
                } else {
                    successCount++;
                    if (successCount === totalRows) {
                        handleCompletion3();
                    }
                    // alert("Failed to add record at index", index);
                }
            });
        });

    };

    function AddData(empno, date, intime, stat1, outtime, stat2, callback) {
        if (intime == undefined) {
            intime = "";
        } else if (outtime == undefined) {
            outtime = "";
        }

        var url = "../Controller/emp_attendance.php?request=AddExcelData&empno=" + empno + "&date=" + date +
            "&intime=" + intime + "&outtime=" + outtime + "&stat1=" + stat1 + "&stat2=" + stat2;
        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                var arr = data.split("#");

                if (arr[0] == "Att_IN_YES" || arr[1] == "Att_OUT_YES") {
                    callback("1"); // Call the callback function with success value
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






    // //Upload Employee Shifts

    // function UploadEmployeeShiftsData(Shift_ID) {
    //      //Reference the FileUpload element.
    //      var fileUpload = document.getElementById("shiftfile");

    //      //Validate whether File is valid Excel file.
    //      var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.xls|.xlsx)$/;
    //      if (regex.test(fileUpload.value.toLowerCase())) {
    //          if (typeof (FileReader) != "undefined") {
    //              var reader = new FileReader();

    //              //For Browsers other than IE.
    //              if (reader.readAsBinaryString) {
    //                  reader.onload = function (e) {
    //                      GetEmployeeShiftTableFromExcel(e.target.result,Shift_ID);
    //                  };
    //                  reader.readAsBinaryString(fileUpload.files[0]);
    //              } else {
    //                  //For IE Browser.
    //                  reader.onload = function (e) {
    //                      var data = "";
    //                      var bytes = new Uint8Array(e.target.result);
    //                      for (var i = 0; i < bytes.byteLength; i++) {
    //                          data += String.fromCharCode(bytes[i]);
    //                      }
    //                      GetEmployeeShiftTableFromExcel(data,Shift_ID);
    //                  };
    //                  reader.readAsArrayBuffer(fileUpload.files[0]);
    //              }
    //          } else {
    //              alert("This browser does not support HTML5.");
    //          }
    //      } else {
    //          alert("Please upload a valid Excel file.");
    //      }
    //  };


    //  function GetEmployeeShiftTableFromExcel(data,shift_id) {

    //      //Read the Excel File data in binary
    //      var workbook = XLSX.read(data, {
    //          type: 'binary'
    //      });

    //      //get the name of First Sheet.
    //      var Sheet = workbook.SheetNames[0];

    //      //Read all rows from First Sheet into an JSON array.
    //      var excelRows = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[Sheet]);

    //      //Add the data rows from Excel file.
    //      for (var i = 0; i < excelRows.length; i++) {

    //          AddShiftData(excelRows[i]["Date"],excelRows[i]["Shift Name"],shift_id);

    //     }

    // };


    // function AddShiftData(date,name,shift_ID)
    // {
    //     var url = "../Controller/emp_attendance.php?request=AddShiftData&date=" + date + "&name=" + name.trim() + "&shiftid=" + shift_ID;
    //          $.ajax({
    //              type: 'POST',
    //              url: url,
    //              success: function(data) 
    //              {

    //              } 
    //          });    
    // }


    // function SaveShiftData()
    // {
    //     var empid = $('#shiftceemp').val();
    //     var year = $('#shiftyear').val();
    //     var month = $('#shiftmonth').val();
    //     var file = document.getElementById("shiftfile").files.length;

    //     if (file == 0) 
    //     {
    //        alert("Please add the excel file!");
    //     }
    //     else
    //     {
    //          var url = "../Controller/emp_attendance.php?request=SaveShiftData&EMP_ID=" + empid + "&YEAR=" + year + "&MONTH=" + month;
    //          $.ajax({
    //              type: 'POST',
    //              url: url,
    //              success: function(data) 
    //              {
    //                  if (data == 0) 
    //                  {
    //                     alert("Excel file already added!");
    //                     showShiftData();
    //                  }
    //                  else
    //                  {
    //                     UploadEmployeeShiftsData(data);
    //                     alert("Shift records added!");
    //                     showShiftData();
    //                  }
    //              } 
    //          });
    //     }              
    // }


    //  function showShiftData(){

    //      var empid = $('#shiftceemp').val();
    //      var year = $('#shiftyear').val();
    //      var month = $('#shiftmonth').val();

    //      var url = "../Controller/emp_attendance.php?request=viewShift&empid=" + empid +"&year="+year+"&month="+month;
    //      $.ajax({
    //          type: 'POST',
    //          url: url,
    //          success: function(data) {
    //              $("#viewshiftData").html(data);
    //          }
    //      });   
    //  }


    //  function deleteShift(shiftid) {

    //      let confirmAction = confirm("Do you want to delete this shift record?");

    //      if (confirmAction) {

    //          var url = "../Controller/emp_attendance.php?request=deleteshift&shiftid=" + shiftid;
    //          $.ajax({
    //              type: 'POST',
    //              url: url,
    //              success: function(data) {
    //                  alert(data); 
    //                  showShiftData();  
    //              }
    //          });
    //      }

    //  }


    //Upload Holiday Data Section

    function UploadHolidayData() {
        //Reference the FileUpload element.
        var fileUpload = document.getElementById("holifile");

        //Validate whether File is valid Excel file.
        var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.xls|.xlsx)$/;
        if (regex.test(fileUpload.value.toLowerCase())) {
            if (typeof(FileReader) != "undefined") {
                var reader = new FileReader();

                //For Browsers other than IE.
                if (reader.readAsBinaryString) {
                    reader.onload = function(e) {
                        GetHolidayTableFromExcel(e.target.result);
                    };
                    reader.readAsBinaryString(fileUpload.files[0]);
                } else {
                    //For IE Browser.
                    reader.onload = function(e) {
                        var data = "";
                        var bytes = new Uint8Array(e.target.result);
                        for (var i = 0; i < bytes.byteLength; i++) {
                            data += String.fromCharCode(bytes[i]);
                        }
                        GetHolidayTableFromExcel(data);
                    };
                    reader.readAsArrayBuffer(fileUpload.files[0]);
                }
            } else {
                alert("This browser does not support HTML5.");
            }
        } else {
            alert("Please upload a valid Excel file.");
        }
    };

    function GetHolidayTableFromExcel(data) {

        //Read the Excel File data in binary
        var workbook = XLSX.read(data, {
            type: 'binary'
        });

        //get the name of First Sheet.
        var Sheet = workbook.SheetNames[0];

        //Read all rows from First Sheet into an JSON array.
        var excelRows = XLSX.utils.sheet_to_row_object_array(workbook.Sheets[Sheet]);

        //Add the data rows from Excel file.
        for (var i = 0; i < excelRows.length; i++) {

            AddHolidayData(excelRows[i]["Date"], excelRows[i]["Holiday Name"]);

        }

    };

    function AddHolidayData(date, name) {
        var url = "../Controller/emp_attendance.php?request=AddHolidayExcelData&date=" + date + "&name=" + name;
        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                // alert("Record Upload Successfully...!");
            }
        });
        loadHolidays();
    }

    function loadHolidays() {

        var url = "../Controller/emp_attendance.php?request=getHolidays";

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                var arr = data.split("///");

                $('#holidaytable').html(arr[0]);

            }
        });
        setSpace();
    }

    var selectedHoliday = "";

    function select_holidayx(detail) {
        // alert(detail);  
        var arr = detail.split("#");
        $("#holidaydc").val(arr[1]);
        $("#holidaydis").val(arr[2]);
        selectedHoliday = arr[0];

    }

    function insertholiday() {
        var name = $("#holidaydis").val();
        var date = $("#holidaydc").val();

        if (date == "") {
            alert("Please select the date !")
        } else {
            var url = "../Controller/emp_attendance.php?request=inholidy&name=" + name + "&date=" + date;
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    alert(data);
                    loadHolidays();
                    $("#holidaydis").val("");

                }
            });
        }


    }

    function updateholiday() {
        if (selectedHoliday !== "") {


            var name = $("#holidaydis").val();
            var date = $("#holidaydc").val();
            var selected = selectedHoliday;

            var url = "../Controller/emp_attendance.php?request=upholiday&name=" + name + "&date=" + date + "&id=" +
                selected;
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    alert(data);
                    loadHolidays();
                    $("#holidaydc").val("");
                    $("#holidaydis").val("");
                    selectedHoliday = "";
                }
            });
        } else {

            alert("Please select a holiday to update !");
        }
    }

    function deleteholid(holid) {

        let confirmAction = confirm("Do you want to delete this holiday?");

        if (confirmAction) {

            var url = "../Controller/emp_attendance.php?request=deleteholiday&hid=" + holid;
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    alert(data);
                    loadHolidays();
                }
            });
        }

    }

    function leavetypeChange() {
        var leavetype = document.getElementById('ltype').value;
        // var days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        // var dateF = document.getElementById('ledate').value;
        // var d = new Date(dateF);
        // var dayName = days[d.getDay()];
        // var nopay_days_value = document.getElementById('select_noofdays').value;

        if (leavetype == "Halfday Morning Leave" || leavetype == "Halfday Evening Leave") {
            $('#noofdays').val("0.5");
        } else if (leavetype == "Nopay Morning Leave" || leavetype == "Nopay Evening Leave") {
            $('#noofdays').val("0.5");
        } else if (leavetype == "Duty Morning Leave" || leavetype == "Duty Evening Leave") {
            $('#noofdays').val("0.5");
        } else if (leavetype == "Short Morning Leave" || leavetype == "Short Evening Leave") {
            $('#noofdays').val("0.25");
        } else if (leavetype == "") {
            $('#noofdays').val("");
        } else {
            $('#noofdays').val("1");
        }

    }


    var Part = "";

    function leaveCounter() {

        var url = "../Controller/emp_attendance.php?request=getleavecount&UID=" + $('#leemp').val() + "&SelDate=" + $(
            '#ledate').val();

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {

                $('#get_medical').show();
                $('#available_medical').show();
                $('#gethalfday').show();
                $('#duty_leaves').show();
                $('#maternity_leaves').show();
                $('#parental_leaves').show();
                $('#getshortleaves').show();
                $('#availableshortleaves').show();
                $('#liueleaves').show();

                var arr = data.split("#");
                Part = arr[0];

                if (arr[0] == "B") {
                    $("#totalLeave").html("Total Leaves : <b>" + arr[7] + "</b>");
                    $("#getLeave_1").html("&nbsp;&nbsp;Taken Casual Leaves : <b>" + arr[1] + "</b>");
                    $("#AvailableLeave_1").html("&nbsp;&nbsp;Available Casual Leaves : <b>" + arr[2] +
                        "</b>");
                    $("#getLeave_2").html("&nbsp;&nbsp;Taken Annual Leaves : <b>" + arr[3] + "</b>");
                    $("#AvailableLeave_2").html("&nbsp;&nbsp;Available Annual Leaves : <b>" + arr[4] +
                        "</b>");
                    $("#get_medical").html("&nbsp;&nbsp;Taken Medical Leaves : <b>" + arr[5] + "</b>");
                    $("#available_medical").html("&nbsp;&nbsp;Available Medical Leaves : <b>" + arr[6] +
                        "</b>");
                    $("#gethalfday").html("&nbsp;&nbsp;Taken Halfday Leaves : <b>" + arr[10] + "</b>");
                    $("#getshortleaves").html("&nbsp;&nbsp;Taken Short Leaves : <b>" + arr[8] + "</b>");
                    $("#availableshortleaves").html("&nbsp;&nbsp;Available Short Leaves : <b>" + arr[9] +
                        "</b>");
                    $("#nopayleaves").html("&nbsp;&nbsp;Nopay Leaves : <b>" + arr[12] + "</b>");
                    $("#liueleaves").html("&nbsp;&nbsp;Lieu Leaves : <b>" + arr[16] + "</b>");
                    $("#duty_leaves").html("&nbsp;&nbsp;Duty Leaves : <b>" + arr[13] + "</b>");
                    $("#maternity_leaves").html("&nbsp;&nbsp;Maternity Leave : <b>" + arr[14] + "</b>");
                    $("#parental_leaves").html("&nbsp;&nbsp;Parental Leave : <b>" + arr[15] + "</b>");
                    $("#leavecountdata").val(arr[2]);
                    $("#leavecountdata2").val(arr[4]);
                    $("#leavecountdata3").val(arr[6]);

                } else if (arr[0] == "C") {
                    $("#totalLeave").html("Total Leaves : <b>" + arr[7] + "</b>");
                    $("#getLeave_1").html("&nbsp;&nbsp;Taken Casual Leaves : <b>" + arr[1] + "</b>");
                    $("#AvailableLeave_1").html("&nbsp;&nbsp;Available Casual Leaves : <b>" + arr[2] +
                        "</b>");
                    $("#getLeave_2").html("&nbsp;&nbsp;Taken Annual Leaves : <b>" + arr[3] + "</b>");
                    $("#AvailableLeave_2").html("&nbsp;&nbsp;Available Annual Leaves : <b>" + arr[4] +
                        "</b>");
                    $("#get_medical").html("&nbsp;&nbsp;Taken Medical Leaves : <b>" + arr[5] + "</b>");
                    $("#available_medical").html("&nbsp;&nbsp;Available Medical Leaves : <b>" + arr[6] +
                        "</b>");
                    $("#gethalfday").html("&nbsp;&nbsp;Taken Halfday Leaves : <b>" + arr[10] + "</b>");
                    $("#getshortleaves").html("&nbsp;&nbsp;Taken Short Leaves : <b>" + arr[8] + "</b>");
                    $("#availableshortleaves").html("&nbsp;&nbsp;Available Short Leaves : <b>" + arr[9] +
                        "</b>");
                    $("#nopayleaves").html("&nbsp;&nbsp;Nopay Leaves : <b>" + arr[12] + "</b>");
                    $("#liueleaves").html("&nbsp;&nbsp;Lieu Leaves : <b>" + arr[16] + "</b>");
                    $("#duty_leaves").html("&nbsp;&nbsp;Duty Leaves : <b>" + arr[13] + "</b>");
                    $("#maternity_leaves").html("&nbsp;&nbsp;Maternity Leave : <b>" + arr[14] + "</b>");
                    $("#parental_leaves").html("&nbsp;&nbsp;Parental Leave : <b>" + arr[15] + "</b>");
                    $("#leavecountdata").val(arr[2]);
                    $("#leavecountdata2").val(arr[4]);
                    $("#leavecountdata3").val(arr[6]);
                } else {
                    if (arr[17] == "Empty") {
                        $("#totalLeave").html("Total Leaves : <b>" + arr[7] + "</b>");
                    } else {
                        $("#totalLeave").html("Total Leaves : <b>" + arr[7] + "</b>");
                        $("#getLeave_1").html("&nbsp;&nbsp;Number Of Halfday Leaves Taken : <b>" + arr[1] +
                            "</b>");
                        $("#AvailableLeave_1").html("&nbsp;&nbsp;Available Halfday Leaves : <b>" + arr[2] +
                            "</b>");
                        $("#getLeave_2").html("&nbsp;&nbsp;Number Of Short Leaves Taken : <b>" + arr[8] +
                            "</b>");
                        $("#AvailableLeave_2").html("&nbsp;&nbsp;Available Short Leaves : <b>" + arr[9] +
                            "</b>");
                        $("#nopayleaves").html("&nbsp;&nbsp;Nopay Leaves : <b>" + arr[12] + "</b>");
                        $("#liueleaves").html("&nbsp;&nbsp;Lieu Leaves : <b>" + arr[16] + "</b>");
                        $("#leavecountdata").val(arr[2]);
                        $("#leavecountdata2").val(arr[9]);
                        $("#leavecountdata3").val(arr[6]);
                        $('#get_medical').hide();
                        $('#available_medical').hide();
                        $('#gethalfday').hide();
                        $('#duty_leaves').hide();
                        $('#maternity_leaves').hide();
                        $('#parental_leaves').hide();
                        $('#getshortleaves').hide();
                        $('#availableshortleaves').hide();


                    }

                }


            }
        });
    }


    function getEmployeeWiseHalfSlots() {
        var employeeID = document.getElementById('leemp').value;
        var DATE = document.getElementById('ledate').value;

        var url = "../Controller/emp_attendance.php?request=getEmpWiseHalfSlots&empid=" + employeeID + "&date=" + DATE;
        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                $('#tslot_Half').html(data);
            }
        });
    }

    function getEmployeeWiseShortSlots() {
        var employeeID = document.getElementById('leemp').value;
        var DATE = document.getElementById('ledate').value;

        var url = "../Controller/emp_attendance.php?request=getEmpWiseShortSlots&empid=" + employeeID + "&date=" + DATE;
        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                $('#tslot_Short').html(data);
            }
        });
    }


    function EnterLeave() {

        var employeeID = document.getElementById('leemp').value;
        var DATE = document.getElementById('ledate').value;
        var LeaveType = document.getElementById('ltype').value;
        // var halfDAy = document.getElementById('tslot_Half').value;
        // var shorLE = document.getElementById('tslot_Short').value;
        var DaysValue = document.getElementById('noofdays').value;
        var reason = document.getElementById('lreason').value;
        // var NopayDaysValue = document.getElementById('select_noofdays').value;
        var Leave_COUNT = document.getElementById('leavecountdata').value;
        var Leave_COUNT_2 = document.getElementById('leavecountdata2').value;
        var Leave_COUNT_3 = document.getElementById('leavecountdata3').value;


        if (LeaveType == "Halfday Morning Leave" || LeaveType == "Halfday Evening Leave") {
            if (DaysValue == "") {
                alert("Please Enter All Details!");
            } else {
                if (Part == "A") {
                    if (Leave_COUNT == "0") {
                        alert("Can't Add This Leave. Available Leave Count is Over!");
                    } else {
                        let confirmAction = confirm("Are you sure this is correct?");

                        if (confirmAction) {

                            var url = "../Controller/emp_attendance.php?request=approveleave&employeeID=" + employeeID +
                                "&LeaveType=" + LeaveType + "&DaysValue=" + DaysValue + "&date=" + DATE + "&reason=" +
                                reason;

                            $.ajax({
                                type: 'POST',
                                url: url,
                                success: function(data) {

                                    if (data == "1") {
                                        alert("Leave Added Successfully!");
                                        leaveCounter();
                                        $("#ltype").val("");
                                        $("#noofdays").val("");
                                        $("#lreason").val("");
                                    } else if (data == "2") {
                                        alert("Leave Already Added In This Date!");
                                    } else {
                                        alert("Leave Added Unsuccessfully!");
                                    }

                                }
                            });
                        }
                    }
                } else {
                    if (Leave_COUNT == "0" && Leave_COUNT_2 == "0") {
                        alert("Can't Add This Leave. Available Leave Count is Over!");
                    } else {
                        let confirmAction = confirm("Are you sure this is correct?");

                        if (confirmAction) {

                            var url = "../Controller/emp_attendance.php?request=approveleave&employeeID=" + employeeID +
                                "&LeaveType=" + LeaveType + "&DaysValue=" + DaysValue + "&date=" + DATE + "&reason=" +
                                reason;

                            $.ajax({
                                type: 'POST',
                                url: url,
                                success: function(data) {

                                    if (data == "1") {
                                        alert("Leave Added Successfully!");
                                        leaveCounter();
                                        $("#ltype").val("");
                                        $("#noofdays").val("");
                                        $("#lreason").val("");
                                    } else if (data == "2") {
                                        alert("Leave Already Added In This Date!");
                                    } else {
                                        alert("Leave Added Unsuccessfully!");
                                    }

                                }
                            });
                        }
                    }
                }
            }
        } else if (LeaveType == "Short Morning Leave" || LeaveType == "Short Evening Leave") {
            if (DaysValue == "") {
                alert("Please Enter All Details!");
            } else {
                if (Part == "A") {
                    if (Leave_COUNT_2 == "0") {
                        alert("Can't Add This Leave. Available Leave Count is Over!");
                    } else {
                        let confirmAction = confirm("Are you sure this is correct?");

                        if (confirmAction) {

                            var url = "../Controller/emp_attendance.php?request=approveleave&employeeID=" + employeeID +
                                "&LeaveType=" + LeaveType + "&DaysValue=" + DaysValue + "&date=" + DATE + "&reason=" +
                                reason;

                            $.ajax({
                                type: 'POST',
                                url: url,
                                success: function(data) {

                                    if (data == "1") {
                                        alert("Leave Added Successfully!");
                                        leaveCounter();
                                        $("#ltype").val("");
                                        $("#noofdays").val("");
                                        $("#lreason").val("");
                                    } else if (data == "2") {
                                        alert("Leave Already Added In This Date!");
                                    } else {
                                        alert("Leave Added Unsuccessfully!");
                                    }

                                }
                            });
                        }
                    }
                } else {
                    if (Leave_COUNT == "0" && Leave_COUNT_2 == "0") {
                        alert("Can't Add This Leave. Available Leave Count is Over!");
                    } else {
                        let confirmAction = confirm("Are you sure this is correct?");

                        if (confirmAction) {

                            var url = "../Controller/emp_attendance.php?request=approveleave&employeeID=" + employeeID +
                                "&LeaveType=" + LeaveType + "&DaysValue=" + DaysValue + "&date=" + DATE + "&reason=" +
                                reason;

                            $.ajax({
                                type: 'POST',
                                url: url,
                                success: function(data) {

                                    if (data == "1") {
                                        alert("Leave Added Successfully!");
                                        leaveCounter();
                                        $("#ltype").val("");
                                        $("#noofdays").val("");
                                        $("#lreason").val("");
                                    } else if (data == "2") {
                                        alert("Leave Already Added In This Date!");
                                    } else {
                                        alert("Leave Added Unsuccessfully!");
                                    }

                                }
                            });
                        }
                    }
                }
            }
        } else if (LeaveType == "Casual Leave") {
            if (DaysValue == "") {
                alert("Please Enter All Details!");
            } else {
                if (Leave_COUNT == "0") {
                    alert("Can't Add This Leave. Available Leave Count is Over!");
                } else {
                    let confirmAction = confirm("Are you sure this is correct?");

                    if (confirmAction) {

                        var url = "../Controller/emp_attendance.php?request=approveleave&employeeID=" + employeeID +
                            "&LeaveType=" + LeaveType + "&DaysValue=" + DaysValue + "&date=" + DATE + "&reason=" +
                            reason;

                        $.ajax({
                            type: 'POST',
                            url: url,
                            success: function(data) {

                                if (data == "1") {
                                    alert("Leave Added Successfully!");
                                    leaveCounter();
                                    $("#ltype").val("");
                                    $("#noofdays").val("");
                                    $("#lreason").val("");
                                } else if (data == "2") {
                                    alert("Leave Already Added In This Date!");
                                } else {
                                    alert("Leave Added Unsuccessfully!");
                                }

                            }
                        });
                    }
                }

            }
        } else if (LeaveType == "Annual Leave") {
            if (DaysValue == "") {
                alert("Please Enter All Details!");
            } else {
                if (Leave_COUNT_2 == "0") {
                    alert("Can't Add This Leave. Available Leave Count is Over!");
                } else {
                    let confirmAction = confirm("Are you sure this is correct?");

                    if (confirmAction) {

                        var url = "../Controller/emp_attendance.php?request=approveleave&employeeID=" + employeeID +
                            "&LeaveType=" + LeaveType + "&DaysValue=" + DaysValue + "&date=" + DATE + "&reason=" +
                            reason;

                        $.ajax({
                            type: 'POST',
                            url: url,
                            success: function(data) {

                                if (data == "1") {
                                    alert("Leave Added Successfully!");
                                    leaveCounter();
                                    $("#ltype").val("");
                                    $("#noofdays").val("");
                                    $("#lreason").val("");
                                } else if (data == "2") {
                                    alert("Leave Already Added In This Date!");
                                } else {
                                    alert("Leave Added Unsuccessfully!");
                                }

                            }
                        });
                    }
                }

            }
        } else if (LeaveType == "Medical Leave") {
            if (DaysValue == "") {
                alert("Please Enter All Details!");
            } else {
                if (Leave_COUNT_3 == "0") {
                    alert("Can't Add This Leave. Available Leave Count is Over!");
                } else {
                    let confirmAction = confirm("Are you sure this is correct?");

                    if (confirmAction) {

                        var url = "../Controller/emp_attendance.php?request=approveleave&employeeID=" + employeeID +
                            "&LeaveType=" + LeaveType + "&DaysValue=" + DaysValue + "&date=" + DATE + "&reason=" +
                            reason;

                        $.ajax({
                            type: 'POST',
                            url: url,
                            success: function(data) {

                                if (data == "1") {
                                    alert("Leave Added Successfully!");
                                    leaveCounter();
                                    $("#ltype").val("");
                                    $("#noofdays").val("");
                                    $("#lreason").val("");
                                } else if (data == "2") {
                                    alert("Leave Already Added In This Date!");
                                } else {
                                    alert("Leave Added Unsuccessfully!");
                                }

                            }
                        });
                    }
                }

            }
        } else {
            if (DaysValue == "") {
                alert("Please Enter All Details!");
            } else {
                let confirmAction = confirm("Are you sure this is correct?");

                if (confirmAction) {

                    var url = "../Controller/emp_attendance.php?request=approveleave&employeeID=" + employeeID +
                        "&LeaveType=" + LeaveType + "&DaysValue=" + DaysValue + "&date=" + DATE + "&reason=" + reason;

                    $.ajax({
                        type: 'POST',
                        url: url,
                        success: function(data) {

                            if (data == "1") {
                                alert("Leave Added Successfully!");
                                leaveCounter();
                                $("#ltype").val("");
                                $("#noofdays").val("");
                                $("#lreason").val("");
                            } else if (data == "2") {
                                alert("Leave Already Added In This Date!");
                            } else {
                                alert("Leave Added Unsuccessfully!");
                            }

                        }
                    });
                }
            }

        }

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

<body id="body" class="body" style="background-color: white;">
    <?php include("../Contains/titlebar_dboard.php"); ?>
    <div class="container" style="margin-left: 5px; margin-top: 15px;">
        <!--  Manage Attendance & Leave Part -->
        <div class="row">
            <div class="col-md-12">
                <div class="x_title" style="margin-left: 5px;">
                    <h3>Employee Attendance <small>Manage employee attendance</small></h3>
                </div>
                <div style="display: table;">
                    <div style="display: table-cell;">
                        <form action="../Controller/emp_attendance.php" method="POST" enctype="multipart/form-data">
                            <table>
                                <tr>
                                    <td>
                                        <div class="col-md-9">
                                            <h4 style="text-decoration: underline;">Import Attendance</h4>
                                            <p>Import attendance from your finger print machine</p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <table style="margin-left: 10px;">
                                            <tr>
                                                <td><input type="file" name="attfile" id="attfile"
                                                        class="input-file;btn btn-default submit" /></td>
                                                <td><a href="#" id="btnupld" class="btn btn-info input-file"
                                                        onclick="UploadProcess()"
                                                        style="width: 150px; text-decoration: none; margin-left: 0px;">Upload</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td id="status" style="color: black;">
                                                    <div>
                                                        <strong>
                                                            <?php
                                                            if (isset($_GET["state"])) {
                                                                echo $_GET["state"];
                                                            }
                                                            ?>
                                                        </strong>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                        <br />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <hr />
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="col-md-9">
                                            <h4 style="text-decoration: underline;">Custom Attendance Enter</h4>
                                            <p>Insert attendance details manualy</p>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <h4></h4>
                                        <table style="margin-left: 10px;">
                                            <tr>
                                                <td height="35px;" width="200px;">Employee </td>
                                                <td><select id="ceemp" name="ceemp" class="form-control"
                                                        style="width: 200px;">
                                                        <?php
                                                        $query = "select jobcode,uid,fname,lname,epfno from user where isactive='1' and uid != '2' order by length(jobcode),jobcode ASC";
                                                        $res = Search($query);
                                                        while ($result = mysqli_fetch_assoc($res)) {
                                                        ?>
                                                        <option value="<?php echo $result["jobcode"]; ?>">
                                                            <?php echo $result["jobcode"]; ?>: &nbsp;
                                                            <?php echo $result["fname"]; ?> </option>
                                                        <?php } ?>
                                                    </select> </td>
                                            </tr>
                                            <tr>
                                                <td height="35px;">In Date</td>
                                                <td><input type="date" name="cedate" id="date" class="form-control"
                                                        style="width:200px; margin-top: 5px;" /></td>
                                            </tr>
                                            <tr>
                                                <td height="35px;">In Time </td>
                                                <td><input type="time" name="ceintime" class="form-control"
                                                        style="width: 200px; margin-top: 5px;" /></td>
                                            </tr>
                                            <tr>
                                                <td height="35px;">Out Date</td>
                                                <td><input type="date" name="cedateout" id="cedateout"
                                                        class="form-control" style="width:200px; margin-top: 5px;" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="35px;">Out Time </td>
                                                <td><input type="time" name="ceouttime" class="form-control"
                                                        style="width: 200px; margin-top: 5px;" /></td>
                                            </tr>

                                            <tr>
                                                <td></td>
                                                <td align='right'><input type="submit" value="Enter Attendance"
                                                        name="submit" class="btn btn-primary"
                                                        style="margin-top: 10px; width: 150px"></td>
                                            </tr>
                                        </table>

                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <hr />
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <div class="col-md-9">
                                            <h4 style="text-decoration: underline;">Leave Enter</h4>
                                            <p>Insert leave records manualy</p>
                                        </div>
                                    </td>
                                </tr>

                                <table>
                                    <tr>
                                        <td>
                                            <div class="form-group">
                                                <label for="exampleInputEmail1" id="totalLeave"></label>
                                                <label for="exampleInputEmail1" id="getLeave_1"></label>
                                                <label for="exampleInputEmail1" id="AvailableLeave_1"></label>
                                                <label for="exampleInputEmail1" id="getLeave_2"></label>
                                                <label for="exampleInputEmail1" id="AvailableLeave_2"></label>
                                                <label for="exampleInputEmail1" id="get_medical"></label>
                                                <label for="exampleInputEmail1" id="available_medical"></label>
                                                <label for="exampleInputEmail1" id="gethalfday"></label>
                                                <label for="exampleInputEmail1" id="getshortleaves"></label>
                                                <label for="exampleInputEmail1" id="availableshortleaves"></label>
                                                <label for="exampleInputEmail1" id="nopayleaves"></label>
                                                <label for="exampleInputEmail1" id="liueleaves"></label>
                                                <label for="exampleInputEmail1" id="duty_leaves"></label>
                                                <label for="exampleInputEmail1" id="maternity_leaves"></label>
                                                <label for="exampleInputEmail1" id="parental_leaves"></label>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <input type="text" id="leavecountdata" hidden="hidden">
                                <input type="text" id="leavecountdata2" hidden="hidden">
                                <input type="text" id="leavecountdata3" hidden="hidden">
                                <table style="margin-left: 10px;">

                                    <tr>
                                        <td height="35px;" width="200px;">Employee </td>
                                        <td><select id="leemp" name="leemp" class="form-control" style="width: 200px"
                                                onchange="leaveCounter(); showLeaves();">
                                                <?php
                                                $query = "select jobcode,epfno,uid,fname,lname from user where isactive='1' and uid != '2' order by length(jobcode),jobcode ASC";
                                                $res = Search($query);
                                                while ($result = mysqli_fetch_assoc($res)) {
                                                ?>
                                                <option value="<?php echo $result["uid"]; ?>">
                                                    <?php echo $result["jobcode"]; ?>: &nbsp;
                                                    <?php echo $result["fname"]; ?> </option>
                                                <?php } ?>
                                            </select> </td>
                                    </tr>
                                    <tr>
                                        <td height="35px;">Date</td>
                                        <td><input type="date" name="ledate" id="ledate"
                                                onchange="leaveCounter();showLeaves();" class="form-control"
                                                style="width:200px; margin-top: 5px;" /></td>
                                    </tr>
                                    <tr>
                                        <td height="35px;">Leave Type </td>
                                        <td>
                                            <select class="form-control" id="ltype" name="ltype"
                                                style="width: 200px; margin-top: 5px;" onchange="leavetypeChange()">
                                                <option value=""></option>
                                                <option value="Annual Leave">Annual Leave</option>
                                                <option value="Casual Leave">Casual Leave</option>
                                                <option value="Halfday Morning Leave">Halfday Morning Leave</option>
                                                <option value="Halfday Evening Leave">Halfday Evening Leave</option>
                                                <option value="Short Morning Leave">Short Morning Leave</option>
                                                <option value="Short Evening Leave">Short Evening Leave</option>
                                                <option value="Medical Leave">Medical Leave</option>
                                                <option value="Nopay Full Day Leave">Nopay Full Day Leave</option>
                                                <option value="Nopay Morning Leave">Nopay Morning Leave</option>
                                                <option value="Nopay Evening Leave">Nopay Evening Leave</option>
                                                <option value="Lieu Leave">Lieu Leave</option>
                                                <option value="Duty Full Day Leave">Duty Full Day Leave</option>
                                                <option value="Duty Morning Leave">Duty Morning Leave</option>
                                                <option value="Duty Evening Leave">Duty Evening Leave</option>
                                                <option value="Maternity Leave">Maternity Leave</option>
                                                <option value="Parental Leave">Parental Leave</option>
                                            </select>
                                        </td>
                                    </tr>

                                    <!-- <tr id="nopay_slot">
                                            <td height="35px;">Number of Days</td>
                                            <td><select class="form-control" id="select_noofdays" name="select_noofdays" onchange="leavetypeChange()" style="width: 200px; margin-top: 5px;">
                                                    <option value=""></option>
                                                    <option value="1">1</option>
                                                    <option value="0.5">0.5</option>
                                                </select>
                                            </td>                                    
                                        </tr> -->

                                    <tr id="other_slot">
                                        <td height="35px;">Number of Days</td>
                                        <td><input type="text" name="noofdays" id="noofdays" readonly="readonly"
                                                class="form-control" style="width: 200px; margin-top: 5px;" /></td>
                                    </tr>

                                    <tr>
                                        <td height="35px;">Reason</td>
                                        <td><input type="text" name="lreason" id="lreason" class="form-control"
                                                style="width: 200px; margin-top: 5px;" /></td>
                                    </tr>

                                    <!-- <tr id="half_slot">
                                            <td height="35px;">Time Slot</td>
                                            <td> <select class="form-control" id="tslot_Half" name="tslot_Half" style="width: 200px; margin-top: 5px;">
                                                  <option value=""></option>
                                                </select>      
                                            </td>                                    
                                        </tr>

                                        <tr id="short_slot">
                                            <td height="35px;">Time Slot</td>
                                            <td><select class="form-control" id="tslot_Short" name="tslot_Short" style="width: 200px; margin-top: 5px;">
                                                  <option value=""></option>
                                                </select>
                                            </td>                                    
                                        </tr> -->
                                    <tr>
                                        <td></td>
                                        <td align='right'><input type="button" id="btnadd" value="Enter Leave"
                                                class="btn btn-primary" style="margin-top: 10px; width: 150px"
                                                onclick="EnterLeave()"></td>
                                    </tr>

                                </table>
                                </br>

                                <h5 style="margin-left: 10px;"><u>Leave List</u></h5>
                                <table id="viewleaves" style="margin-left: 10px;">


                                </table>

                            </table>
                        </form>
                    </div>
                    <div style="display: table-cell;width: 10%">

                    </div>

                </div>
            </div>
        </div>

        <!--  View Attendance Part -->
        <div class="row">
            <div class="col-md-12">
                <div class="row x_title" style="margin-left: 5px;">
                    <h3>View Attendance <small>Search attendance details</small></h3>
                </div>
            </div><br>
            <div id="print0" style="display: table;margin-left: 20px;">
                <div style="display:table-cell;">
                    Employee :
                    <select id="emp" name="emp" class="select-basic" style="width: 190px; height: 23px;">
                        <option value="%">All</option>
                        <?php
                        $query = "select uid,fname,lname,epfno,jobcode from user where isactive='1' and uid != '2' order by length(jobcode),jobcode ASC";
                        $res = Search($query);
                        while ($result = mysqli_fetch_assoc($res)) {
                        ?>
                        <option value="<?php echo $result["uid"]; ?>"> <?php echo $result["jobcode"]; ?>: &nbsp;
                            <?php echo $result["fname"]; ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div style="display:table-cell;">
                    &nbsp;&nbsp;&nbsp;&nbsp; EPF No : <input type="text" name="jcode" id="jcode"
                        value="<?php echo $result["jobcode"]; ?>" class="input-text" style="width: 190px" />
                </div>

                <div style="display:table-cell;">
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    Branch :
                    <select id="dept" name="dept" class="select-basic" style="width: 150px; height: 23px;">
                        <option value="%"></option>
                        <?php
                        $query = "select pid,name from position order by pid";
                        $res = Search($query);
                        while ($result = mysqli_fetch_assoc($res)) {
                        ?>
                        <option value="<?php echo $result["pid"]; ?>"><?php echo $result["name"]; ?> </option>
                        <?php } ?>
                    </select>
                </div>
                <div id="sfdate" style="display:table-cell;">
                    &nbsp;&nbsp;&nbsp;&nbsp; From Date :
                    <input type="date" name="fxdate" id="fxdate" class="input-text" />
                </div>
                <div id="sldate" style="display:table-cell;">
                    &nbsp;&nbsp;&nbsp;&nbsp; To Date :
                    <input type="date" name="lxdate" id="lxdate" class="input-text" />
                </div>
                <div style="display:table-cell;">
                    &nbsp;&nbsp;&nbsp;&nbsp;<img src="../Icons/search.png" onclick="loadAttendance()"
                        style="cursor: pointer" />
                </div>
            </div>

            <div class="row tile_count">
                <div class="col-md-2 col-sm-4 col-xs-6 tile_stats_count"
                    style="background-color: #DFDBDB; margin-left: 30px;border-radius: 10px;">
                    <br />
                    <span class="count_top"><i class="fa fa-user"></i> Total Attendance : <i class="green"><span
                                style="font-size: 20px;" id="totaview"></span></i></span>
                </div>
                <div style="display:table-cell;">
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input
                        type="button" value="Export Excel" class="btn btn-success" onclick="exportExcel()">&nbsp;&nbsp;
                    <b style="color: black;">The leave request was not approved</b> :&nbsp;&nbsp;<input type="text"
                        style="width: 40px; background-color: #deba6d; border: none;" readonly="true">
                </div>
            </div>

            <div id="print2" style="margin-right: 10px; margin-left: 20px;">
                <div id="attview"
                    style="width: 98%; height: 500px; overflow-y: scroll; float: left;border-radius: 2px;"></div>
            </div>
        </div>

        <!--  Edit Attendance Part -->
        <div class="row" style="margin-top: 20px;">
            <div class="col-md-4">
                <div class="row x_title" style="margin-left: 5px;">
                    <h3>Edit Record <small>Update attendance details</small></h3>
                </div>
                <div id="attedit" style="display: table-cell;">
                    <table style="margin-left: 20px;">
                        <tr>
                            <td class="form-label" height="35px;" width="200px;">In Time</td>
                            <td><input type="time" id="int" class="form-control" onchange="AutoGenAtt()" /></td>
                        </tr>
                        <tr>
                            <td class="form-label" height="35px;">Out Time</td>
                            <td><input type="time" id="outt" class="form-control" style="margin-top: 5px;"
                                    onchange="AutoGenAtt()" /></td>
                        </tr>
                        <tr>
                            <td class="form-label" height="35px;">Total Hours</td>
                            <td><input type="text" id="hrs" class="form-control" style="margin-top: 5px;" /></td>
                        </tr>
                        <tr>
                            <td class="form-label" height="35px;">O.T. Hours</td>
                            <td><input type="text" id="othrs" class="form-control" style="margin-top: 5px;" /></td>
                        </tr>
                        <tr hidden="hidden">
                            <td class="form-label" height="35px;">D.O.T. Hours</td>
                            <td><input type="text" id="dothrs" class="form-control" style="margin-top: 5px;" /></td>
                        </tr>
                        <tr>
                            <td class="form-label" height="35px;">Half Day</td>
                            <td><input type="text" id="hd" class="form-control" style="margin-top: 5px;" /></td>
                        </tr>
                        <tr>
                            <td class="form-label" height="35px;">Short Leave</td>
                            <td><input type="text" id="sl" class="form-control" style="margin-top: 5px;" /></td>
                        </tr>

                        <tr>
                            <td class="form-label" height="35px;">Late Minutes</td>
                            <td><input type="text" id="lm" class="form-control" style="margin-top: 5px;" /></td>
                        </tr>
                        <tr hidden="hidden">
                            <td class="form-label" height="35px;"></td>
                            <td><input type="checkbox" id="rmLate" name="rmLate" style="margin-top: 5px;"
                                    onchange="RemoveLate()">Remove Late Minutes</td>
                        </tr>
                        <tr hidden="hidden">
                            <td class="form-label" height="35px;">Morning OT Hours</td>
                            <td><input type="text" id="mot" class="form-control" style="margin-top: 5px;" /></td>
                        </tr>
                        <tr hidden="hidden">
                            <td class="form-label" height="35px;"></td>
                            <td><input type="checkbox" id="morOT" name="morOT" style="margin-top: 5px;"
                                    onchange="AutoGenAtt()">Allow Morning OT</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td align="right"><input id="upbtn" type="button" value="Update Record"
                                    class="btn btn-warning" style="margin-top: 10px; width: 150px"
                                    onclick="updateRecord()" /></td>
                        </tr>
                    </table>
                </div>
            </div>
            <div class="col-md-7">
                <div class="x_title" style="margin-left: 10px;">
                    <h3>Summary</h3>
                </div>
                <div id="print1" style="margin-left: 10px;">
                    <ul class="list-unstyled top_profiles scroll-view">
                        <li class="media event">
                            <a class="pull-left border-aero profile_thumb"><i class="fa fa-users aero"></i></a>
                            <div class="media-body">
                                <a class="title" href="#">Total Attendance</a>
                                <p><strong><span id="tota"></span></strong> (Count) </p>
                            </div>
                        </li>
                        <li class="media event">
                            <a class="pull-left border-green profile_thumb"><i class="fa fa-user-plus green"></i></a>
                            <div class="media-body">
                                <a class="title" href="#">Total O.T. Hours</a>
                                <p><strong><span id="totot"></span></strong> (Hours) </p>
                            </div>
                        </li>
                        <li class="media event">
                            <a class="pull-left border-green profile_thumb"><i class="fa fa-user-plus green"></i></a>
                            <div class="media-body">
                                <a class="title" href="#">Total Double O.T. Hours</a>
                                <p><strong><span id="tdotot"></span></strong> (Hours) </p>
                            </div>
                        </li>
                        <li class="media event">
                            <a class="pull-left border-blue profile_thumb"><i class="fa fa-hourglass-end blue"></i></a>
                            <div class="media-body">
                                <a class="title" href="#">Total Hours</a>
                                <p><strong><span id="totl"></span></strong> (Hours) </p>
                            </div>
                        </li>
                        <li class="media event">
                            <a class="pull-left border-blue profile_thumb"><i class="fa fa-hourglass-end blue"></i></a>
                            <div class="media-body">
                                <a class="title" href="#">Total Late Min.</a>
                                <p><strong><span id="tlm"></span></strong> (Minutes) </p>
                            </div>
                        </li>
                        <li class="media event">
                            <a class="pull-left border-aero profile_thumb"><i class="fa fa-hourglass-half aero"></i></a>
                            <div class="media-body">
                                <a class="title" href="#">Total Half Days</a>
                                <p><strong><span id="tothd"></span></strong> (Count) </p>
                            </div>
                        </li>
                        <li class="media event">
                            <a class="pull-left border-gray profile_thumb"><i class="fa fa-user-md gray"></i></a>
                            <div class="media-body">
                                <a class="title" href="#">Total Short Leaves</a>
                                <p><strong><span id="totsl"></span></strong> (Count) </p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!--  Manage Holiday Part -->
        <div class="row" style="margin-top: 130px;">
            <div class="col-md-8">
                <div class="x_title" style="margin-left: 5px;">
                    <h3>Enter Holidays &nbsp; <small>Holidays Register</small></h3>
                </div>
                <div>
                    <table style="margin-left: 10px; margin-top: 10px;" cellspacing="50">
                        <tr>
                            <td height="35px">
                                <table>
                                    <tr>
                                        <td></td>
                                        <td>Date</td>
                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                        <td>Description</td>
                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                        <td></td>
                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td><input type="hidden" id="pdid"></td>
                                        <td> <input type="date" class="form-control" id="holidaydc" name="holidaydate"
                                                style="width: 200px;" /></td>
                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                        <td> <input type="text" class="form-control" id="holidaydis"
                                                name="holidaydiscription" style="width: 200px;" /></td>
                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                        <td><input type="button" id="addholiday" value="Add" onclick="insertholiday()"
                                                class="btn btn-primary"></td>
                                        <td>&nbsp;&nbsp;&nbsp;</td>
                                        <td><input type="button" id="upholiday" value="Update" onclick="updateholiday()"
                                                class="btn btn-warning"></td>
                                    </tr>
                                </table><br />
                                <table>
                                    <tr>
                                        <td>
                                            <h5>Import Holidays</h5>
                                            <p>Create holiday excel file using this heading names (Date,Holiday Name)
                                            </p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <table>
                                                <tr>
                                                    <td><input type="file" name="holifile" id="holifile"
                                                            class="input-file;btn btn-default submit" /></td>
                                                    <td><a href="#" id="btnholiupld" class="btn btn-info input-file"
                                                            onclick="UploadHolidayData()"
                                                            style="width: 120px; text-decoration: none; margin-left: 0px;">Upload</a>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">Download holiday register template here
                                                        &nbsp;&nbsp;<a id="holifiles" title="Download Excel File"
                                                            class="btn btn-success"
                                                            onclick="DownloadExcelTemplatesFiles('holiday_template.xlsx')"
                                                            style="width: 50px; text-decoration: none; margin-left: 0px;"><i
                                                                class="fa fa-file-excel-o"
                                                                style="float: next; cursor: pointer"></i></a></td>
                                                </tr>
                                            </table>
                                            <br />
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <div style="overflow:scroll; height:400px; margin-left: 10px;" id=holidaytable></div>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <?php include("../Contains/footer.php"); ?>
    </div>
</body>

</html>