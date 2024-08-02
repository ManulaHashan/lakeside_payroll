<?php
error_reporting(0);
include("../Contains/header.php");
include '../DB/DB.php';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Employee Salary Reports | Apex Payroll</title>
  <!-- Favicons -->
  <link rel="apple-touch-icon" sizes="180x180" href="../Images/favicon/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="../Images/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="../Images/favicon/favicon-16x16.png">
  <!-- <link href="../Styles/Stylie.css" rel="stylesheet" type="text/css"> -->
  <link href="../Styles/contains.css" rel="stylesheet" type="text/css">
  <link href="../Styles/appStyles.css" rel="stylesheet" type="text/css">
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
   $(document).ready(function() {
    $('#loading').hide();
    setSpace(); 

    if($('#hmon').length !== 0){
      $('#month').val($('#hmon').val());
      $('#year').val($('#hyer').val());
      $('#employee').val($('#user').val());
      $('#dept').val($('#depart').val());
      $('#pmethod').val($('#pmeth').val());
      $('#empstatus').val($('#empstat').val());
    }

  });
   $(document).ajaxStart(function() {
    $('#loading').show();
  }).ajaxStop(function() {
    $('#loading').hide();
  });

  function setSpace() {
    var wheight = $(window).height();
    var bheight = $('#body').height();
    if (wheight > bheight) {
      var x = wheight - bheight - 30;
      $('#space').height(x);
    }
  }

  function print(){
   var divToPrint0 = document.getElementById('report');
   var newWin = window.open('', 'Print-Window');
   newWin.document.open();
   newWin.document.write(divToPrint0.innerHTML + "<hr/><p>System By Appex Solutions ~ www.appexsl.com</p>");
   newWin.print();

 }

 function ViewSlip(id){

    var arr = id.split(":");
    var url = '../Views/emp_payroll.php?slipID='+arr[0]+"&uid="+arr[1];
    window.open(url, "_blank");
 }

 function PrintSlip(id){

    var url = '../Model/salary_slip.php?slipID='+id;
    window.open(url, "_blank");
 }

 function PrintBulkSlip(){
    var month = document.getElementById('month').value;
    var year = document.getElementById('year').value;

    var url = '../Model/bulk_slip.php?Month='+month+'&Year='+year;
    window.open(url, "_blank");
 }

 function exportExcel(){

      var dataz = document.getElementById('report').innerHTML;

      // alert(dataz);

      var url = "../Controller/emp_manage.php?request=setSession";
      $.ajax({
        type: 'POST',
        url: url,
        data: { 'data': dataz, 'filename': 'Salary_report' },
        success: function(data) {

                    // alert(data);
 
                    var page = "../Model/excel_export.php";   

                    window.location = encodeURI(page); 
                }
            });
    }

 function selectMonth(){
  $("#month").val($("#month").val());
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

                <table>
                  <tr>
                    <td><h3>Employee Salary Report<br/> <small>Genarate Salary Reports</small></h3></td>
                  </tr>
                </table>

                <table width="120%">
                 <tr>
                  <form action="emp_reports.php" method="get">
                    
                    <td width="100">&nbsp;</td>
                    <td width="50"> 
                      Month :
                      <select id="month" name="month" class="select-basic" onchange="selectMonth()" style="height: 23px;">                  
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
                      </select> </td>
                      <td>&nbsp;</td>
                      <td width="50">
                        Year : 
                        <select id="year" name="year" class="select-basic" style="height: 23px;">
                          <option value="2019">2019</option>
                          <option value="2020">2020</option>
                          <option value="2021">2021</option>
                          <option value="2022">2022</option>
                          <option value="2023">2023</option>
                          <option value="2024">2024</option>
                          <option value="2025">2025</option>
                          <option value="2026">2026</option>
                          <option value="2027">2027</option>
                          <option value="2028">2028</option>
                          <option value="2029">2029</option>
                          <option value="2030">2030</option>
                          <option value="2031">2031</option>
                          <option value="2032">2032</option>
                          <option value="2033">2033</option>
                          <option value="2034">2034</option>
                          <option value="2035">2035</option>
                          <option value="2036">2036</option>
                          <option value="2037">2037</option>
                          <option value="2038">2038</option>
                          <option value="2039">2039</option>
                          <option value="2040">2040</option>
                        </td>
                        <td>&nbsp;</td>
                      <td width="50">
                        Employee : 
                        <select id="employee" name="employee" class="select-basic" style="width: 150px; height: 23px;">
                            <option value="%">All</option>
                            <?php
                            $query = "select uid,fname,lname,jobcode,epfno from user where isactive='1' and uid != '2' order by length(jobcode),jobcode ASC";
                            $res = Search($query);
                            while ($result = mysqli_fetch_assoc($res)) {
                                ?>
                                <option value="<?php echo $result["uid"]; ?>"> <?php echo $result["jobcode"]; ?> - <?php echo $result["fname"]; ?> </option>
                            <?php } ?>
                        </select>
                      </td> 
                      <td>&nbsp;</td>
                      <td width="50">
                        Branch : 
                        <select id="dept" name="dept" class="select-basic" style="width: 150px; height: 23px;">
                            <option value="%">All</option>
                            <?php
                            $query = "select pid,name from position order by pid";
                            $res = Search($query);
                            while ($result = mysqli_fetch_assoc($res)) {
                                ?>
                                <option value="<?php echo $result["pid"]; ?>"><?php echo $result["name"]; ?> </option>
                            <?php } ?>
                        </select>
                      </td>
                      <td>&nbsp;</td>
                      <td width="50">
                        Department : 
                        <select id="pmethod" name="pmethod" class="select-basic" style="width: 150px; height: 23px;">
                          <option value="%">All</option>
                            <?php
                            $query = "select did,name from emp_department order by did";
                            $res = Search($query);
                            while ($result = mysqli_fetch_assoc($res)) {
                                ?>
                                <option value="<?php echo $result["did"]; ?>"><?php echo $result["name"]; ?> </option>
                            <?php } ?>
                      </td>
                      <td>&nbsp;</td>
                      <td width="50">
                        Employee Status : 
                        <select id="empstatus" name="empstatus" class="select-basic" style="width: 150px; height: 23px;">
                          <option value="1">Active</option>
                          <option value="0">Not-Active</option>
                          <option value="%">All</option> 
                        </td>  
                      <td>&nbsp;</td> 
                        <td>&nbsp;&nbsp;<input type="submit" name="submit" value="Generate" class="btn btn-primary"></td>
                        <td>&nbsp;&nbsp;<input type="button" value="Print Report" class="btn btn-dark" onclick="print()"></td>
                        <td>&nbsp;&nbsp;<input type="button" value="Export Excel" class="btn btn-success" onclick="exportExcel()"></td>
                        <td>&nbsp;&nbsp;<input type="button" value="Bulk Slip Print" class="btn btn-dark" onclick="PrintBulkSlip()"></td>
                      </form>
                    </tr>
                  </table>
                </div>
              </div>

              <?php
                      //Department
                      $res_dept = Search("select name from emp_department where did = '".$_GET["pmethod"]."'");
                      if ($result_dept = mysqli_fetch_assoc($res_dept)) 
                      {
                          $dept_Name = $result_dept["name"];
                      }
                      else
                      { 
                          $dept_Name = "All";
                      }
                      
                      //Branch
                      $res_branch = Search("select name from position where pid = '".$_GET["dept"]."'");
                      if ($result_branch = mysqli_fetch_assoc($res_branch)) 
                      {
                          $branch_Name = $result_branch["name"];
                      }
                      else
                      { 
                          $branch_Name = "All";
                      }

                      if ($_GET["month"] == "" || $_GET["month"] == null) 
                      {
                         $MONTH = "";
                      }
                      else
                      {
                         $MONTH = date("F", mktime(0, 0, 0, $_GET["month"], 10));
                      }

               ?>



              <div id="report">

                <center>
                 <h3>Salary Payment Summery</h3>
                 <p>Month : <?php echo $MONTH;?> | Year : <?php echo $_GET["year"]; ?> | Branch : <?php echo $branch_Name;?> | Department : <?php echo $dept_Name; ?></p>
               </center>
               <hr/>
               <div>
                 <h4 style="float: right;">Printed Date : <?php echo date("Y/m/d"); ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h4>
               </div>

             </br>

             <?php
          //       $resal = Search("select etid,name from employeetype");
            // while ($resultal = mysqli_fetch_assoc($resal)) {

            //  echo "Department : ".$resultal["name"]. "<br/>";

             ?>
             <br>
             <br>
             <div id="tdatax" style="overflow-x: scroll;">
             <table border="1" width="90%" class="table table-bordered" style="border-collapse: collapse;">
               <thead >
                <tr>
                  <th colspan="5"></th>
                  <th colspan="11"></th>
                  <th colspan="7"><center>Deduction</center></th>
                  <th colspan="2"></th>
                  <th colspan="2"><center>Employer Contribution</center></th>
                  <th></th>
                </tr>
                <tr>
                <th></th>
                <th>Emp. NO</th>
                <th>EPF NO</th>
                <th>Emp. Name</th>
                <th>Basic Salary</th>
                <!-- <th>Budgetary Relief Allowance 1</th>
                <th>Budgetary Relief Allowance 2</th> -->
                <th>Leave Days</th>
                <th>Nopay Days</th>
                <th>Nopay Deduction</th>
                <th>Basic Salary for EPF</th>
                <th>Fixed Allowance</th>
                <th>Vehicle Allowance</th>
                <!-- <th>Production Incentive</th> -->
                <th>Other Allowance</th>
                <!-- <th>Team Leader Incentive</th> -->
                <!-- <th>Meal Amount</th> -->
                <!-- <th>Attendance Incentive</th> -->
                <th>Normal OT Rate</th>
                <th>OT Hrs</th>
                <th>Normal OT Payment</th>
                <!-- <th>DOT Rate</th>
                <th>DOT Hrs</th> -->
                <!-- <th>Double OT Payment</th> -->
                <th>Gross Salary</th>
                <th>Salary Advance</th>
                <th>Loan Repayment</th>
                <!-- <th>Insurance</th> -->
                <th>Late Hrs</th>
                <th>Late Deduction</th>
                <th>Other Deduction</th>
                <th>EPF Employee (8%)</th>
                <th>PAYEE Tax Amount</th>
                <th>Total Deduction</th>
                <th>Net Salary</th>
                <th>EPF Employer (12%)</th>
                <th>ETF Employer (3%)</th>
                <th>Total EPF Contribution (20%)</th>
                <!-- <th>Total Salary Payment</th> -->
                </tr>

              </thead>
              <tbody>
                <?php
                $count = 0;
                $daysT = 0;
                $dayRT = 0;
                $amountT = 0;
                $amountBT = 0;

                $totEPF12 = 0;
                $totEPF8 = 0;
                $totEtF = 0;

                $totAttAl = 0;
                $totTrvlAl= 0;
                $totOthAl= 0;
                $totAttIncn= 0;
                $totBonus= 0;

                $totLate = 0;
                $totshort = 0;
                $tothalf = 0;
                $totNopay = 0;
                $totTaxAmt = 0;
                $totOtherDed = 0;

                $otT = 0;
                $otrT = 0;
                $otamT = 0;
                $arrT = 0;
                $att1T = 0;
                $att2T = 0;
                $totT = 0;
                $totAdvT = 0;
                $totLoan = 0;
                $totEPF = 0;
                $totDed = 0;
                $totAdd = 0;

                $totGross = 0;
                $totRecs = 0;
                $totPaid = 0;
                $totleave = 0;
                $Gettotleave = 0;
                $OTPAYPH = 0;
                $DOTPAYPH = 0; 
                $OTallowance = 0;
                $DOTallowance = 0;

                // $resalx = Search("select a.*, b.fname,b.lname,b.epfno,a.basic+a.ball as sal2 from salarycomplete a, user b where a.uid = b.uid and b.EmployeeType_etid = '".$resultal["etid"]."' and a.month = '".$_GET["month"]."' and a.year = '".$_GET["year"]."'");
                // if ($_GET["pmethod"] == "0") 
                // {
                //    $resalx = Search("select a.*,a.id as salslipid, b.fname,b.mname,b.lname,b.epfno,b.jobcode,b.emp_act,a.basic+a.ball as sal2 from salarycomplete a, user b, emppost c, position d where a.uid = b.uid and c.position_pid = d.pid and b.emppost_id = c.id and a.month = '".$_GET["month"]."' and a.year = '".$_GET["year"]."' and a.uid like '".$_GET["employee"]."' and d.pid like '".$_GET["dept"]."' and b.isactive like '".$_GET["empstatus"]."' and b.bankno = ''  order by cast(b.epfno as unsigned) ASC");
                // }
                // elseif ($_GET["pmethod"] == "1") 
                // {
                //    $resalx = Search("select a.*,a.id as salslipid, b.fname,b.mname,b.lname,b.epfno,b.jobcode,b.emp_act,a.basic+a.ball as sal2 from salarycomplete a, user b, emppost c, position d where a.uid = b.uid and c.position_pid = d.pid and b.emppost_id = c.id and a.month = '".$_GET["month"]."' and a.year = '".$_GET["year"]."' and a.uid like '".$_GET["employee"]."' and d.pid like '".$_GET["dept"]."' and b.isactive like '".$_GET["empstatus"]."' and b.bankno !=''  order by cast(b.epfno as unsigned) ASC");
                // }
                // else 
                // {
                //    $resalx = Search("select a.*,a.id as salslipid, b.fname,b.mname,b.lname,b.epfno,b.jobcode,b.emp_act,a.basic+a.ball as sal2 from salarycomplete a, user b, emppost c, position d where a.uid = b.uid and c.position_pid = d.pid and b.emppost_id = c.id and a.month = '".$_GET["month"]."' and a.year = '".$_GET["year"]."' and a.uid like '".$_GET["employee"]."' and d.pid like '".$_GET["dept"]."' and b.isactive like '".$_GET["empstatus"]."' and b.bankno like '%'  order by cast(b.epfno as unsigned) ASC");
                // }


                $resalx = Search("select a.*,a.id as salslipid,b.fname,b.mname,b.lname,b.epfno,b.jobcode,b.emp_act,a.basic+a.ball as sal2 from salarycomplete a, user b, emppost c, position d where a.uid = b.uid and c.position_pid = d.pid and b.emppost_id = c.id and a.month = '".$_GET["month"]."' and a.year = '".$_GET["year"]."' and a.uid like '".$_GET["employee"]."' and d.pid like '".$_GET["dept"]."' and b.isactive like '".$_GET["empstatus"]."' and b.dept_id like '".$_GET["pmethod"]."' and b.bankno like '%'  order by cast(b.epfno as unsigned) ASC");

                
                while ($resultalx = mysqli_fetch_assoc($resalx)) {
                 echo "<tr>";  
                 echo "<td><center><table><tr><td><img src='../Images/edit.png' title='Edit Slip' id='".$resultalx["salslipid"].":".$resultalx["uid"]."' style='width: 28px; cursor: pointer;' onclick='ViewSlip(id)'></td><td>&nbsp;</td><td><img src='../Images/printer.png' title='Print Slip' id='".$resultalx["salslipid"]."' onclick='PrintSlip(id)' style='width: 28px; cursor: pointer;'></td></tr></table></center></td>";

                 echo "<td>".$resultalx["jobcode"] . "</td>";

                 echo "<td>".$resultalx["epfno"] . "</td>";

                 echo "<td>".$resultalx["fname"]."</td>";  

                 // echo "<td>". $resultalx["wdays"] . "</td>";

                 echo "<td align='right'>". number_format($resultalx["basic"],2) . "</td>";

                 // echo "<td align='right'>". number_format($resultalx["br1"],2) . "</td>";
                 // echo "<td align='right'>". number_format($resultalx["br2"],2) . "</td>";
                 
                 
                 $work = $resultalx["wdays"];
                 $totwork = $resultalx["totwdays"];
                 $totleave = $totwork - $work;

                 $query = "select sum(days) as totleaves from employee_leave where uid = '" . $resultalx["uid"] . "' and type != 'Nopay Leave' and type != 'Liue Leave' and type != 'Short Leave' and type != 'Parental Leave' and type != 'Maternity Leave' and type != 'Duty Leave' and MONTH(date) = '".$_GET["month"]."' and YEAR(date) = '".$_GET["year"]."'";
                  $res = Search($query);
                  if ($result = mysqli_fetch_assoc($res)) {

                      if ($result["totleaves"] == "") 
                      {
                          $Gettotleave = 0;
                      }
                      else
                      {
                          $Gettotleave = $result["totleaves"];
                      }
                      echo "<td><center>".$Gettotleave."</center></td>";
                  }
                  

                 
                 echo "<td><center>".$resultalx["nopaydays"]."</center></td>";
                 echo "<td align='right'>". number_format($resultalx["nopay"],2) . "</td>";
                 echo "<td align='right'>". number_format($resultalx["basic"]+$resultalx["br1"]+$resultalx["br2"]-$resultalx["nopay"],2) . "</td>";
                 echo "<td align='right'>".number_format($resultalx["att1"] ,2). "</td>";
                 echo "<td align='right'>".number_format($resultalx["travl"] ,2). "</td>";

                 // $resProduct = Search("select sum(a.amount) as TotalProduct from user_has_allowances a, allowances b where a.alwid = b.alwid and a.uid='".$resultalx["uid"]."' and lower(b.name) ='Production Incentive'");
                 // if ($resultProduct = mysqli_fetch_assoc($resProduct)) {

                 //    $Produ =  $resultProduct["TotalProduct"];
                       
                 //   echo "<td align='right'>".number_format($resultProduct["TotalProduct"] ,2). "</td>";
                 // }
                 // else
                 // {
                 //   $Produ =  0;
                 //   echo "<td align='right'>".number_format($Produ,2). "</td>";
                 // }

                 $resOtherAllow = Search("select sum(a.amount) as TotalOther from user_has_allowances a, allowances b where a.alwid = b.alwid and a.uid='".$resultalx["uid"]."' and lower(b.name) ='Other Allowances'");
                 if ($resultOtherAllow = mysqli_fetch_assoc($resOtherAllow)) {

                    $Other =  $resultOtherAllow["TotalOther"];
                       
                   echo "<td align='right'>".number_format($resultOtherAllow["TotalOther"] ,2). "</td>";
                 }
                 else
                 {
                   $Other =  0;
                   echo "<td align='right'>".number_format($Other,2). "</td>";
                 }


                 // $resTeamLead = Search("select sum(a.amount) as TotalTeamLead from user_has_allowances a, allowances b where a.alwid = b.alwid and a.uid='".$resultalx["uid"]."' and lower(b.name) ='Team Leader Incentive'");
                 // if ($resultTeamLead = mysqli_fetch_assoc($resTeamLead)) {

                 //    $TeamLead =  $resultTeamLead["TotalTeamLead"];
                       
                 //   echo "<td align='right'>".number_format($resultTeamLead["TotalTeamLead"] ,2). "</td>";
                 // }
                 // else
                 // {
                 //   $TeamLead =  0;
                 //   echo "<td align='right'>".number_format($TeamLead,2). "</td>";
                 // }


                 // $resMealAmount = Search("select remainingamount from mealremaining where uid='".$resultalx["uid"]."' and MONTH(date) = '".$_GET["month"]."' and YEAR(date) = '".$_GET["year"]."'");
                 // if ($resultMealAmount = mysqli_fetch_assoc($resMealAmount)) {

                 //    $MealAmount =  $resultMealAmount["remainingamount"];
                       
                 //   echo "<td align='right'>".number_format($resultMealAmount["remainingamount"] ,2). "</td>";
                 // }
                 // else
                 // {
                 //   echo "<td align='right'>".number_format("0",2). "</td>";
                 // }

                 // echo "<td align='right'>".number_format($resultalx["att_incen"] ,2). "</td>";

                 
                  $TOT_OTH = 0;
                  $TOT_OTM = 0;

                  $TOT_DOTH = 0;
                  $TOT_DOTM = 0;

                  $TOT_WORKH = 0;
                  $TOT_WORKM = 0;

                  $resOTandDOT = Search("select othours,dothours from attendance where date between '" . $resultalx["datefrom"] . "' and '" . $resultalx["dateto"] . "' and User_uid='".$resultalx["uid"]."'");
                  while ($resultOTandDOT = mysqli_fetch_assoc($resOTandDOT)) {


                          if($resultOTandDOT["othours"] == ""){
                              $oth = $resultOTandDOT["othours"];
                          }else{
                              $oth = number_format($resultOTandDOT["othours"],2);

                              $othAR = explode(".", $oth);

                              $TOT_OTH += $othAR[0];
                              $TOT_OTM += $othAR[1];
                          }

                          if($resultOTandDOT["dothours"] == ""){
                              $doth = $resultOTandDOT["dothours"];
                          }else{
                              $doth = number_format($resultOTandDOT["dothours"],2);

                              $othARD = explode(".", $doth);

                              $TOT_DOTH += $othARD[0];
                              $TOT_DOTM += $othARD[1];
                          }

                          if($result["hours"] == ""){
                              $hours = $resultOTandDOT["hours"];
                          }else{
                              $hours = number_format($resultOTandDOT["hours"],2);

                              $othARWork = explode(".", $hours);

                              $TOT_WORKH += $othARWork[0];
                              $TOT_WORKM += $othARWork[1];
                          }


                }

                //Calculate OT Value
                $TOT_MinsOT = ($TOT_OTH*60) + $TOT_OTM;
                $TOT_OTvalue = floor($TOT_MinsOT/60);
                $total_OTMin  = floor($TOT_MinsOT % 60);
                $DataOT = $TOT_OTvalue.".".$total_OTMin;

                //Calculate DOT Value
                $TOT_Mins_DOT = ($TOT_DOTH*60) + $TOT_DOTM;
                $TOT_DOTvalue = floor($TOT_Mins_DOT/60);
                $total_DOTMin  = floor($TOT_Mins_DOT % 60);
                $DataDOT = $TOT_DOTvalue.".".$total_DOTMin;

                //Calculate WORK Value
                $TOT_Mins_WORK = ($TOT_WORKH*60) + $TOT_WORKM;
                $TOT_WORKvalue = floor($TOT_Mins_WORK/60);
                $total_WRKMin  = floor($TOT_Mins_WORK % 60);
                $DataWORK = $TOT_WORKvalue.".".$total_WRKMin; 


                if ($DataOT > 60) 
                {
                    $OTH =  60;
                }
                else
                {
                    $OTH =  $DataOT;
                }  


                if ($resultalx["emp_act"] == "Shop and Office") 
                {
                    //OT and DOT 
                    $OTPAYPH = (($resultalx["basic"]+$resultalx["br1"]+$resultalx["br2"])/240) * 1.5;
                    $DOTPAYPH = (($resultalx["basic"]+$resultalx["br1"]+$resultalx["br2"])/240) * 2; 

                    $OTallowance = $OTH * $OTPAYPH;
                    $DOTallowance = $DataDOT * $DOTPAYPH;
                }
                else
                {
                    //OT and DOT 
                    $OTPAYPH = (($resultalx["basic"]+$resultalx["br1"]+$resultalx["br2"])/200) * 1.5;
                    $DOTPAYPH = (($resultalx["basic"]+$resultalx["br1"]+$resultalx["br2"])/200) * 2; 

                    $OTallowance = $OTH * $OTPAYPH;
                    $DOTallowance = $DataDOT * $DOTPAYPH;
                }

                  echo "<td align='right'>". number_format($OTPAYPH,2) . "</td>";
                  echo "<td><center>".number_format($OTH,2)."</center></td>";
                  echo "<td align='right'>". number_format($OTallowance,2) . "</td>";
                  // echo "<td align='right'>". number_format($DOTPAYPH,2) . "</td>";
                  // echo "<td><center>".number_format($DataDOT,2)."</center></td>";
                  // echo "<td align='right'>". number_format($DOTallowance,2) . "</td>";
                  
                  $GROSS = $resultalx["basic"]+$resultalx["br1"]+$resultalx["br2"]-$resultalx["nopay"]+$resultalx["att1"]+$resultalx["travl"]+$Produ+$Other+$TeamLead+$resultalx["att_incen"]+$OTallowance;

                  echo "<td align='right'>". number_format($GROSS,2) . "</td>";
                  echo "<td align='right'>".number_format($resultalx["advance"] ,2). "</td>";

                 $resLoan = Search("select sum(installment) as totloan from factoryloan where User_uid = '".$resultalx["uid"]."' and status = '0' and year = '".$_GET["year"]."' and month = '".$_GET["month"]."'"); // Changed By 2023-11-08
                 if ($resultLoan = mysqli_fetch_assoc($resLoan)) {

                    $Loan =  $resultLoan["totloan"];
                       
                   echo "<td align='right'>".number_format($resultLoan["totloan"] ,2). "</td>";
                 }
                 else
                 {
                   $Loan =  0;
                   echo "<td align='right'>".number_format($Loan,2). "</td>";
                 }

                 //$resInsuarance = Search("select total as Insur from salerydeductions where user_uid = '".$resultalx["uid"]."' and year = '".$_GET["year"]."' and lower(description) = 'Insurance' and isactive = '1'"); change by 2023/02/07

                 // $resInsuarance = Search("select total as Insur from salerydeductions where user_uid = '".$resultalx["uid"]."' and lower(description) = 'Insurance' and isactive = '0' and year = '".$_GET["year"]."' and month = '".$_GET["month"]."'"); // Changed By 2023-11-08
                 // if ($resultInsuarance = mysqli_fetch_assoc($resInsuarance)) {

                 //    $Insuarance =  $resultInsuarance["Insur"];
                       
                 //   echo "<td align='right'>".number_format($resultInsuarance["Insur"] ,2). "</td>";
                 // }
                 // else
                 // {
                 //   $Insuarance =  0;
                 //   echo "<td align='right'>".number_format($Insuarance,2). "</td>";
                 // }
                 

                 $resLate = Search("select sum(late_att_min) as latemin from attendance where MONTH(date) = '".$_GET["month"]."' and YEAR(date) = '".$_GET["year"]."' and User_uid='".$resultalx["uid"]."'");
                 if ($resultLate = mysqli_fetch_assoc($resLate)) {

                    $TOT_LateH = floor($resultLate["latemin"]/60);
                    $TOT_LateM  = floor($resultLate["latemin"] % 60);
                    $hours = $TOT_LateH.".".$TOT_LateM;
                 }
                 else
                 {
                     $hours = "0.00";
                 }
                 
                 echo "<td><center>".number_format($hours,2)."</center></td>";
                 echo "<td align='right'>". number_format($resultalx["late"],2) . "</td>";
                
                 //$resOtherDed = Search("select total as OtherDED from salerydeductions where user_uid = '".$resultalx["uid"]."' and year = '".$_GET["year"]."' and lower(description) != 'Insurance' and isactive = '1'"); change by 2023/02/07


                 $resOtherDed = Search("select sum(total) as OtherDED from salerydeductions where user_uid = '".$resultalx["uid"]."' and lower(description) != 'Insurance' and isactive = '0' and year = '".$_GET["year"]."' and month = '".$_GET["month"]."'"); // Changed By 2023-11-08
                 if ($resultOtherDed = mysqli_fetch_assoc($resOtherDed)) {

                    $OtherDedS =  $resultOtherDed["OtherDED"];
                       
                   echo "<td align='right'>".number_format($resultOtherDed["OtherDED"] ,2). "</td>";
                 }
                 else
                 {
                   $OtherDedS =  0;
                   echo "<td align='right'>".number_format($OtherDedS,2). "</td>";
                 }

                 echo "<td align='right'>". number_format($resultalx["epf"],2) . "</td>";

                 if ($resultalx["payee_tax"] == "") 
                 {
                   $PAYEE = 0;
                 }
                 else
                 {
                   $PAYEE = $resultalx["payee_tax"];
                 }

                 echo "<td align='right'>". number_format($PAYEE,2) . "</td>";

                 $TOTDED = $resultalx["advance"]+$Loan+$resultalx["late"]+$OtherDedS+$resultalx["epf"]+$PAYEE;
                 $NET = $GROSS-$TOTDED;

                 echo "<td align='right'>". number_format($TOTDED,2) . "</td>";
                 // echo "<td align='right'>".number_format($GROSS-$TOTDED ,2). "</td>";
                 echo "<td align='right'>".number_format($NET ,2). "</td>";
                 echo "<td align='right'>". number_format($resultalx["epf12"],2) . "</td>";
                 echo "<td align='right'>". number_format($resultalx["etf3"],2) . "</td>";
                 echo "<td align='right'>". number_format($resultalx["epf12"]+$resultalx["epf"],2) . "</td>";
                 // echo "<td align='right'>".number_format($resultalx["paid"] ,2). "</td>";
                 
                 echo "</tr>";

                 $count++;
                 $basicT += $resultalx["basic"];
                 // $BR1T += $resultalx["br1"];
                 // $BR2T += $resultalx["br2"];
                 $LeaveDT += $Gettotleave;
                 $NopayDT += $resultalx["nopaydays"];
                 $NopayDedT += $resultalx["nopay"];
                 $BSalEPFT += $resultalx["basic"]+$resultalx["br1"]+$resultalx["br2"]-$resultalx["nopay"];
                 $AttAllowT += $resultalx["att1"];
                 $TravAllowT += $resultalx["travl"];
                 // $ProductionIncT += $Produ;
                 $OtherAllowT += $Other;
                 // $TeamLeaderT += $TeamLead;
                 // $MealAmountT += $MealAmount;
                 // $AttendenceInc += $resultalx["att_incen"];
                 $NormalOTRateT += $OTPAYPH;
                 $OThrsT += $OTH;
                 $NormalOTPayT += $OTallowance;
                 // $DOTRt += $DOTPAYPH;
                 // $DOThrsT += $DataDOT;
                 // $DOTPayT += $DOTallowance;
                 $GrossSalT += $GROSS;
                 $SalAdvT += $resultalx["advance"];
                 $StaffLonT += $Loan;
                 // $InsuranceT += $resultInsuarance["Insur"];
                 $LateHrsT += $hours;
                 $LateDedT += $resultalx["late"];
                 $OtherDedT += $resultOtherDed["OtherDED"];
                 $EPF8T += $resultalx["epf"];
                 $totTaxAmt += $resultalx["payee_tax"];
                 $ToTDed += $TOTDED;
                 $NetSalT += $NET;
                 // $NetSalT += $resultalx["paid"];
                 $EPF12T += $resultalx["epf12"];
                 $ETF3T += $resultalx["etf3"];
                 $TotEmpConT += $resultalx["epf12"]+$resultalx["epf"];
                 // $TotSalPayT += $resultalx["paid"];

               }
               ?>
               <tr style="font-weight: bold;">
                 <td></td> 
                 <td></td>
                 <td></td> 
                 <td>TOTAL</td> 
                 <td><?php echo number_format($basicT,2);?></td>
                 <!-- <td align='right'><?php echo number_format($BR1T,2);?></td>
                 <td align='right'><?php echo number_format($BR2T,2);?></td> -->
                 <td align='center'><?php echo $LeaveDT;?></td> 
                 <td align='center'><?php echo $NopayDT;?></td> 
                 <td align='right'><?php echo number_format($NopayDedT,2);?></td> 
                 <td align='right'><?php echo number_format($BSalEPFT,2);?></td> 
                 <td align='right'><?php echo number_format($AttAllowT,2);?></td>
                 <td align='right'><?php echo number_format($TravAllowT,2);?></td>
                 <!-- <td align='right'><?php echo number_format($ProductionIncT,2);?></td> -->
                 <td align='right'><?php echo number_format($OtherAllowT,2);?></td>
                 <!-- <td align='right'><?php echo number_format($TeamLeaderT,2);?></td> -->
                 <!-- <td align='right'><?php echo number_format($MealAmountT,2);?></td>  -->  
                 <!-- <td align='right'><?php echo number_format($AttendenceInc,2);?></td> -->
                 <td align='right'><?php echo number_format($NormalOTRateT,2);?></td>
                 <td align='center'><?php echo $OThrsT;?></td>
                 <td align='right'><?php echo number_format($NormalOTPayT,2);?></td>
                 <!-- <td align='right'><?php echo number_format($DOTRt,2);?></td>
                 <td align='center'><?php echo $DOThrsT;?>
                 <td align='right'><?php echo number_format($DOTPayT,2);?></td> -->
                 <td align='right'><?php echo number_format($GrossSalT,2);?></td>
                 <td align='right'><?php echo number_format($SalAdvT,2);?></td>
                 <td align='right'><?php echo number_format($StaffLonT,2);?></td>
                 <!-- <td align='right'><?php echo number_format($InsuranceT,2);?></td> -->
                 <td align='center'><?php echo number_format($LateHrsT,2);?>
                 <td align='right'><?php echo number_format($LateDedT,2);?></td>
                 <td align='right'><?php echo number_format($OtherDedT,2);?></td>
                 <td align='right'><?php echo number_format($EPF8T,2);?></td>
                 <td align='right'><?php echo number_format($totTaxAmt,2);?></td>
                 <td align='right'><?php echo number_format($ToTDed,2);?></td>
                 <td align='right'><?php echo number_format($NetSalT,2);?></td>
                 <td align='right'><?php echo number_format($EPF12T,2);?></td>
                 <td align='right'><?php echo number_format($ETF3T,2);?></td>
                 <td align='right'><?php echo number_format($TotEmpConT,2);?></td>
                 <!-- <td align='right'><?php echo number_format($TotSalPayT,2);?></td> -->
               </tr>

             </tbody>
           </table>
         </div>
         </br> 
         <?php

            // }
         ?>




       </div>

     </div>



   </div> 

 </div>    
</div>        
</div>

<?php
if(isset($_REQUEST["submit"])){

  echo "<input type='hidden' id='hmon' value='".$_REQUEST["month"]."'>";
  echo "<input type='hidden' id='hyer' value='".$_REQUEST["year"]."'>";
  echo "<input type='hidden' id='user' value='".$_REQUEST["employee"]."'>";
  echo "<input type='hidden' id='depart' value='".$_REQUEST["dept"]."'>";
  echo "<input type='hidden' id='pmeth' value='".$_REQUEST["pmethod"]."'>";
  echo "<input type='hidden' id='empstat' value='".$_REQUEST["empstatus"]."'>";
}
?>

<div id="space"></div>

<?php include("../Contains/footer.php"); ?>
</div>
</div>
</body>
</html>