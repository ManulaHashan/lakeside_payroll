<?php
error_reporting(0);
include("../Contains/header.php");
include '../DB/DB.php';
?>
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Deductions & Loan Details | Apex Payroll</title>
        <!-- Favicons -->
        <link rel="apple-touch-icon" sizes="180x180" href="../Images/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="../Images/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="../Images/favicon/favicon-16x16.png">
        <!-- <link href="../Styles/Stylie.css" rel="stylesheet" type="text/css"> -->
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
                document.getElementById('indate').valueAsDate = new Date();

                getDeductions();
                getLoans();
                dedTypeChange(); //new dev 2023-10-30

                $('#lamount').val("0");//new dev 2023-10-30 
                $('#lintr').val("0");
                $('#lints').val("1");
                $('#didcount').val("1");
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

            function getDeductions() {
                var uid = $('#emp').val();
                var status = $('#status').val();

                var url = '../Controller/emp_sal_deductins.php?request=getDeductions&uid=' + uid + "&status=" + status;
                $.ajax({
                    type: 'POST',
                    url: url,
                    success: function(data) {
                        $('#data').html(data);
                    }
                });
            }
            function addDeduction() {

                if ($('#emp').val() == "") 
                {
                    alert("Please Select Employee");
                }
                else
                {
                    var uid = $('#emp').val();
                    var date = $('#indate').val();
                    var reason = $('#inreason').val();
                    var amount = $('#inamount').val();
                    var month = $('#dmonth').val();
                    var year = $('#dyear').val();
                    var DidCount = $('#didcount').val();
                    var did_typ = $('#ded_typ').val(); //new dev 2023-10-30
                    var did_remark = $('#ded_remark').val();

                    var url = '../Controller/emp_sal_deductins.php?request=addDeductions&uid=' + uid + "&date=" + date + "&des=" + reason + "&amount=" + amount + "&month=" + month + "&year=" + year + "&DidCount=" + DidCount + "&diduction_typ=" + did_typ + "&did_remark=" + did_remark; //new dev 2023-10-30
                    $.ajax({
                        type: 'POST',
                        url: url,
                        success: function(data) {
                            alert(data);
                            getDeductions();

                            $('#date').val("");
                            $('#inreason').val("");
                            $('#inamount').val("");
                            $('#didcount').val("1");
                            $('#ded_typ').val("1"); //new dev 2023-10-30
                            $('#ded_remark').val("");
                            dedTypeChange(); //new dev 2023-10-30
                        }
                    });
                }
                
            }
            function removeDeduction(id) {
                var x = confirm("Are you sure you want to delete this record?");
                if (x) {
                    var url = '../Controller/emp_sal_deductins.php?request=deleteDeductions&id=' + id;
                    $.ajax({
                        type: 'POST',
                        url: url,
                        success: function(data) {
                            alert(data);
                            getDeductions();
                        }
                    });
                }
            }

            function addLoan() {

                if ($('#emp').val() == "") 
                {
                    alert("Please Select Employee");
                }
                else
                {
                    var uid = $('#emp').val();
                    var date = $('#lsdate').val();
                    var amount = $('#lamount').val();
                    var interest = $('#lintr').val();
                    var ints = $('#lints').val();
                    var liamount = $('#lintamount').val();
                    var month = $('#lmonth').val();
                    var year = $('#lyear').val();
                    var loan_rmrk = $('#loan_remark').val();

                    var url = '../Controller/emp_sal_deductins.php?request=addLoan&uid=' + uid + "&date=" + date + "&amount=" + amount + "&interest=" + interest + "&ints=" + ints + "&lia=" + liamount + "&month=" + month + "&year=" + year + "&loan_rmrk=" + loan_rmrk;

                    $.ajax({
                        type: 'POST',
                        url: url,
                        success: function(data) {
                            alert(data);
                            getLoans();

                            $('#lamount').val("0");//new dev 2023-10-30 
                            $('#lintr').val("0");
                            $('#lints').val("1");
                            $('#lintamount').val("");
                            $('#loan_remark').val("");
                        }
                    });
                }

                
            }

            function UpdateLoan() {

                if ($('#emp').val() == "") 
                {
                    alert("Please Select Employee");
                }
                else
                {
                    var uid = $('#emp').val();
                    var date = $('#lsdate').val();
                    var amount = $('#lamount').val();
                    var interest = $('#lintr').val();
                    var ints = $('#lints').val();
                    var liamount = $('#lintamount').val();
                    var status = $('#lstate').val();
                    var lid = $('#loanid').val();

                    var url = '../Controller/emp_sal_deductins.php?request=updateLoan&uid=' + uid + "&date=" + date + "&amount=" + amount + "&interest=" + interest + "&ints=" + ints + "&lia=" + liamount + "&status=" + status + "&lid=" + lid;
                    $.ajax({
                        type: 'POST',
                        url: url,
                        success: function(data) {
                            alert(data);
                            getLoans();
                        }
                    });
                }  
            }

            function removeLoan(id) {
                var x = confirm("Are you sure you want to delete this record?");
                if (x) {
                    var url = '../Controller/emp_sal_deductins.php?request=deleteLoand&id=' + id;
                    $.ajax({
                        type: 'POST',
                        url: url,
                        success: function(data) {
                            alert(data);
                            getLoans();
                        }
                    });
                }
            }

            function getLoans() {
                var uid = $('#emp').val();
                var status = $('#slstatus').val();

                var url = '../Controller/emp_sal_deductins.php?request=getLoans&uid=' + uid + "&status=" + status;
                $.ajax({
                    type: 'POST',
                    url: url,
                    success: function(data) {
                        $('#ldata').html(data);
                    }
                });
            }

            function calculateLoanDetails() {
                var amount = $('#lamount').val();
                var interest = $('#lintr').val();
                var ints = $('#lints').val();

                if (amount === "") {
                    amount = 0;
                }
                if (interest === "") {
                    interest = 0;
                }
                if (ints === "") {
                    ints = 0;
                }

                amount = parseFloat(amount);
                interest = parseFloat(interest);
                ints = parseFloat(ints);

                var totalVal = amount * ((100 + interest) / 100);
                var installment = totalVal / ints;
                $('#lintamount').val(installment.toFixed(2));
            }

            function setLoan(lid) {
                var url = '../Controller/emp_sal_deductins.php?request=getLoan&lid=' + lid;
                $.ajax({
                    type: 'POST',
                    url: url,
                    success: function(data) {
                        var arr = data.split("#");
                        $('#lsdate').val(arr[1]);
                        $('#lamount').val(arr[2]);
                        $('#lintr').val(arr[3]);
                        $('#lints').val(arr[4]);
                        $('#lintamount').val(arr[5]);
                        $('#lstate').val(arr[6]);
                        $('#loanid').val(arr[0]);
                    }
                });
            }

            //new dev 2023-10-30
            function dedTypeChange()
            {
                var d_type = document.getElementById("ded_typ").value;

                if (d_type == "1") 
                {
                    $("#reason_tr").hide();
                    $('#inreason').val("Insurance");
                }
                else
                {
                    $('#inreason').val("Other Deduction");
                    $("#reason_tr").show();
                    $('#inreason').focus(); 
                }
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

                <div class="row x_title" >
                  <div class="col-md-6">
                    <h3>Employee Salary Deductions <small>Manage employee salary deductions and loans</small></h3>
                   <!--  <td><div id="loading" style="float: right; vertical-align: middle;">
                    <img height="24px" src="../Images/load.gif"/> Loading...
                    </div></td> -->
                  </div>
                </div>
            
            

            Select Employee : 
            <select id="emp" name="emp" class="select-basic" onchange="getDeductions();getLoans();" style="height: 23px;">
                <option value=""></option>
                <?php
                $query = "select uid,fname,lname,epfno,jobcode from user where isactive='1' and uid != '2' order by length(jobcode),jobcode ASC";
                $res = Search($query);
                while ($result = mysqli_fetch_assoc($res)) {
                    ?>
                    <option value="<?php echo $result["uid"]; ?>"> <?php echo $result["jobcode"] . " - " .$result["fname"]; ?> </option>
                <?php } ?>
            </select>

            <br/>
            <br/>

            <table>
                <tr>
                    <td width="40%">

                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <h4>Deduct Salary</h4><hr/>
                        </div>
                        
                            <table style="margin-left: 10px;">
                                <tr>
                                    <td height="35px;" width="200px;">Date</td>
                                    <td><input type="date" id="indate" class="input-text" style="width: 200px"></td>
                                </tr>
                                <tr>
                                    <td height="35px;">Deduction Type</td>
                                    <td><select id="ded_typ" class="input-text" style="width: 200px; height: 25px;" onchange="dedTypeChange()">
                                            <option value="1">Insurance</option>
                                            <option value="2">Other Deduction</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr id="reason_tr">
                                    <td height="35px;">Reason</td>
                                    <td><input type="text" id="inreason" class="input-text" style="width: 200px"></td>
                                </tr>
                                <tr>
                                    <td height="35px;">Amount Rs. &nbsp;</td>
                                    <td><input type="number" id="inamount" class="input-text" style="width: 200px"></td>
                                </tr>
                                <tr>
                                    <td height="35px;">Deduction Count </td>
                                    <td><input type="number" id="didcount" class="input-text" style="width: 200px"></td>
                                </tr>
                                <tr>
                                    <td height="35px;">Deduct Month</td>
                                    <td><select id="dmonth" name="dmonth" class="select-basic" style="width: 200px; height: 25px;">
                                            <option value="1">January</option>
                                            <option value="2">February</option>
                                            <option value="3">March</option>
                                            <option value="4">April</option>
                                            <option value="5">May</option>
                                            <option value="6">June</option>
                                            <option value="7">July</option>
                                            <option value="8">August</option>
                                            <option value="9">September</option>
                                            <option value="10">October</option>
                                            <option value="11">November</option>
                                            <option value="12">December</option>                                      
                                        </select></td>
                                </tr>
                                <tr>
                                    <td height="35px;">Deduct Year</td>
                                    <td><select id="dyear" name="dyear" class="select-basic" style="width: 200px; height: 25px;">
                                        <?php  //new dev 2023-10-30  Add this to all parts
                                          $year2 = 2019;
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

                                        </select></td>
                                </tr>
                                <tr>
                                    <td height="35px;">Remark</td>
                                    <td><input type="text" id="ded_remark" class="input-text" style="width: 200px"></td>
                                </tr>
                                <tr>
                                    <td height="35px;"></td>
                                    <td align="right"><input type="button" value="Add Deduction" style="margin-top: 5px;" class="btn btn-primary" onclick="addDeduction()"></td>
                                </tr>
                            </table>                    
                        <br/>
                        <br/>  
                    </td>

                    <td>
                        <p>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                            <h4>View Deductions &nbsp;&nbsp;&nbsp;</h4>
                            </div>
                            Status : 
                            <select class="select-basic" id="status" onclick="getDeductions()" style="height: 25px;">
                                <option value="1">Ongoing</option>    
                                <option value="0">Completed</option>    
                            </select>
                        </p>
                        <div id="data" style="height: 300px; width: 800px; overflow-y: scroll;">

                        </div>
                    </td>
                </tr>

                <tr valign="top">
                    <td>
                        <div class="col-md-9 col-sm-9 col-xs-12">
                            <h4>Employee Loan</h4><hr/>
                        </div>
                        
                            <table style="margin-left: 10px;">
                                <tr >
                                    <td height="35px;" width="200px;">Date</td>
                                    <td><input type="date" id="lsdate" class="input-text" style="width: 200px"></td>
                                </tr>
                                <tr>
                                    <td height="35px;">Loan Amount Rs.</td>
                                    <td><input type="number" id="lamount" class="input-text" style="width: 200px" width="200px;" onkeyup="calculateLoanDetails()"></td>
                                </tr>
                                <tr>
                                    <td height="35px;">Interest %</td>
                                    <td><input type="number" id="lintr" class="input-text" style="width: 200px" onkeyup="calculateLoanDetails()"></td>
                                </tr>
                                <tr>
                                    <td height="35px;">Installments Count </td>
                                    <td><input type="number" id="lints" class="input-text" style="width: 200px" onkeyup="calculateLoanDetails()"></td>
                                </tr>
                                <tr>
                                    <td height="35px;">Installment Amount Rs.</td>
                                    <td><input type="number" id="lintamount" style="width: 200px" class="input-text"></td>
                                </tr>
                                <tr>
                                    <td height="35px;">Loan Start Year & Month</td>
                                    <td>
                                        <select id="lyear" name="lyear" class="select-basic" style="width: 95px; height: 25px;">
                                        <?php  //new dev 2023-10-30  Add this to all parts
                                          $year2 = 2019;
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

                                        <select id="lmonth" name="lmonth" class="select-basic" style="width: 100px; height: 25px;">
                                            <option value="1">January</option>
                                            <option value="2">February</option>
                                            <option value="3">March</option>
                                            <option value="4">April</option>
                                            <option value="5">May</option>
                                            <option value="6">June</option>
                                            <option value="7">July</option>
                                            <option value="8">August</option>
                                            <option value="9">September</option>
                                            <option value="10">October</option>
                                            <option value="11">November</option>
                                            <option value="12">December</option>                                      
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="35px;">Remark</td>
                                    <td><input type="text" id="loan_remark" class="input-text" style="width: 200px"></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td align="right"><input style="margin-top: 5px;" type="button" value="Add Loan" class="btn btn-primary" onclick="addLoan()" style="margin-left:0px;"></td>
                                </tr>
                            </table>
                    </td>
                    <td valign="top">
                        <br/>
                        <p>
                            <div class="col-md-9 col-sm-9 col-xs-12">
                            <h4>View Employee Loans &nbsp;&nbsp;&nbsp;</h4>
                            </div>
                            Status : 
                            <select class="select-basic" id="slstatus" onclick="getLoans()" style="height: 23px;">
                                <option value="1">Ongoing</option>    
                                <option value="0">Completed</option>    
                            </select>
                        </p>
                        <div id="ldata" style="height: 300px; width: 800px; overflow-y: scroll;">

                        </div>                        
                    </td>
                </tr>
            </table>

            </div>
            </div>

            



        
        </div>
        <div id="space"></div>
        <?php include("../Contains/footer.php"); ?>
        </div>
        </div>
    </body>
</html>