<?php
error_reporting(0);
include("../Contains/header.php");
include '../DB/DB.php';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Monthly Excess Payment Report | Apex Payroll</title>
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

  <script src="../JS/jquery-3.1.0.js"></script>
  <script src="../JS/numeral.min.js"></script>

  <script type="text/javascript">
   $(document).ready(function() {
    $('#loading').hide();
    setSpace(); 

    if($('#hmon').length !== 0){
      $('#month').val($('#hmon').val());
      $('#year').val($('#hyer').val());
      $('#emp_status').val($('#emp_stat').val());
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

 function exportExcel(){

      var dataz = document.getElementById('tdatax').innerHTML;

      // alert(dataz);

      var url = "../Controller/emp_manage.php?request=setSession";
      $.ajax({
        type: 'POST',
        url: url,
        data: { 'data': dataz, 'filename': 'Bank_Transfer' },
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

                <table width="150%">
                 <tr>
                  <form action="#" method="get">
                    <td><h3>Monthly Excess Payment Reporting<br/> <small>Genarate Monthly Excess Payment Report</small></h3></td>
                    <td width="100">&nbsp;</td>
                    <td width="50"> 
                      Month :
                      <select id="month" name="month" class="select-basic" onchange="selectMonth()">                  
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
                        <select id="year" name="year" class="select-basic">
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
                         </select>
                        </td>
                        <td>&nbsp;</td>
                        <td width="100">
                        Emp.Status : 
                        <select id="emp_status" name="emp_status" class="select-basic">
                          <option value="1">Active</option>
                          <option value="0">Not Active</option>
                          <option value="%">All</option>
                        </td>
                        <td>&nbsp;&nbsp;</td>
                        <td></br>&nbsp;<input type="submit" name="submit" value="Generate" class="btn btn-primary"></td>
                        <td></br>&nbsp;<input type="button" value="Print Report" class="btn btn-dark" onclick="print()"></td>
                        <td></br>&nbsp;<input type="button" value="Export Excel" class="btn btn-success" onclick="exportExcel()"></td>
                      </form>
                    </tr>
                  </table>
                </div>
              </div>



              <div id="report">

                <center>
                 <h3>Excess Payment Report</h3>
                 <p>Month : <?php echo $_GET["month"]; ?> | Year : <?php echo $_GET["year"]; ?> </p>
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
             <div id="tdatax" style="width:75%;">
             <table border="1" class="table table-bordered" style="border-collapse: collapse;">
               <thead>
                <tr>
                  <th>EPF No</th>
                  <th>Employee's Name</th>
                  <th>Over OT Hours</th>
                  <th>OT Amount Rs.</th>
                  <th>DOT Hours</th> 
                  <th>DOT Amount Rs.</th>
                  <th>Remaining Meal Amount</th>
                </tr>
              </thead>
              <tbody>
                <?php
                

                $totOTHRS = 0;
                $totDOTHRS = 0;
                $totAMNOT = 0;
                $totAMNTDOT = 0;
                $totRemMeal = 0;
                $BR1 = 0;
                $BR2 = 0;
               
                $employerNO = "123456";

                $month = $_GET["month"];
                if(count($_GET["month"]) == 1){
                  $month = "0".$month;
                }




                // $resalx = Search("select uid,fname as emp,emp_act,presentSalary,epfno from user where isactive like '".$_GET["emp_status"]."' order by cast(epfno as unsigned) ASC");

                $resalx = Search("select u.uid,u.fname as emp,u.emp_act,u.presentSalary,u.epfno from user u,salarycomplete sal where u.uid = sal.uid and u.isactive like '".$_GET["emp_status"]."' group by sal.uid order by cast(u.epfno as unsigned) ASC");

                while ($resultalx = mysqli_fetch_assoc($resalx)) {
                 
                 echo "<tr>";
                 echo "<td>".$resultalx["epfno"] . "</td>";
                 echo "<td>".$resultalx["emp"] . "</td>"; 

                  $querBR1 = "select uha.amount,a.name from allowances a,user_has_allowances uha where a.alwid = uha.alwid and a.name = 'Budgetary Relief Allowance 1' and uha.uid = '".$resultalx["uid"]."'";

                  $resBR1 = Search($querBR1);
                  if ($resultBR1 = mysqli_fetch_assoc($resBR1)) 
                  {
                      $BR1 = $resultBR1["amount"];
                  }
                  else
                  {
                      $BR1 = 0;
                  }

                  $querBR2 = "select uha.amount,a.name from allowances a,user_has_allowances uha where a.alwid = uha.alwid and a.name = 'New Budgetary Relief Allowance' and uha.uid = '".$resultalx["uid"]."'";

                  $resBR2 = Search($querBR2);
                  if ($resultBR2 = mysqli_fetch_assoc($resBR2)) 
                  {
                      $BR2 = $resultBR2["amount"];
                  }
                  else
                  {
                      $BR2 = 0;
                  }

                $TOT_OTH = 0;
                $TOT_OTM = 0;

                $TOT_DOTH = 0;
                $TOT_DOTM = 0;

                $TOT_WORKH = 0;
                $TOT_WORKM = 0;

                $resOTandDOT = Search("select othours,dothours from attendance where YEAR(date) = '".$_GET["year"]."' and MONTH(date) = '".$_GET["month"]."' and DAY(date) between '1' and '31' and User_uid='".$resultalx["uid"]."'");
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
                  $OTH =  $DataOT - 60;
              }
              else
              {
                  $OTH =  0;
              }  

              if ($resultalx["emp_act"] == "Shop and Office") 
                 {
                      //OT and DOT 
                      $OTPAYPH = (($resultalx["presentSalary"]+$BR1+$BR2)/240) * 1.5;
                      $DOTPAYPH = (($resultalx["presentSalary"]+$BR1+$BR2)/240) * 2; 
                      $OTallowance = $OTH * $OTPAYPH;
                      $DOTallowance = $DataDOT * $DOTPAYPH;
                 }
                 else
                 {
                      //OT and DOT 
                      $OTPAYPH = (($resultalx["presentSalary"]+$BR1+$BR2)/200) * 1.5;
                      $DOTPAYPH = (($resultalx["presentSalary"]+$BR1+$BR2)/200) * 2;
                      $OTallowance = $OTH * $OTPAYPH;
                      $DOTallowance = $DataDOT * $DOTPAYPH;
                 }
                 
                 $totOTHRS += $OTH;
                 $totDOTHRS += $DataDOT;
                 $totAMNTOT += $OTallowance;
                 $totAMNTDOT += $DOTallowance; 





                 $resalRem = Search("select sum(remainingamount) as TOTREM from mealremaining where YEAR(date) = '".$_GET["year"]."' and MONTH(date) = '".$_GET["month"]."' and uid = '".$resultalx["uid"]."'");
                 if ($resultRem = mysqli_fetch_assoc($resalRem)) 
                 {
                     $REMDATA = $resultRem["TOTREM"]; 
                 }
                 else
                 {
                     $REMDATA = 0;
                 }

                 $totRemMeal += $REMDATA;

                 echo "<td><center>".$OTH."</center></td>";
                 echo "<td style='text-align: right'>".number_format($OTallowance,2) . "</td>";
                 echo "<td><center>".$DataDOT."</center></td>";
                 echo "<td style='text-align: right'>".number_format($DOTallowance,2) . "</td>";
                 echo "<td style='text-align: right'>".number_format($REMDATA,2) . "</td>";
                 echo "</tr>";

               }
                
                  echo "<tr style='border-top: 2px solid black;'>";    
                  echo "<td colspan='2' style='text-align: right'><b>Total</b></td>";
                  echo "<td style='text-align: center'>".number_format($totOTHRS)."</td>";
                  echo "<td style='text-align: right'>".number_format($totAMNTOT,2)."</td>";
                  echo "<td style='text-align: center'>".number_format($totDOTHRS)."</td>";
                  echo "<td style='text-align: right'>".number_format($totAMNTDOT,2)."</td>";
                  echo "<td style='text-align: right'>".number_format($totRemMeal,2)."</td>";
                  echo "</tr>";
               ?>
               

             </tbody>
           </table>
           </br>
           <h2><u>Summery</u></h2>
           </br>

           <table border="1" class="table table-bordered" style="border-collapse: collapse; width:40%;">
             <tr>
               <th></th>
               <th>Total Over OT Hours</th>
               <th>Total DOT Hours</th>
               <th>Total OT Amount Rs.</th>
               <th>Total DOT Amount Rs.</th>
               <th>Total Remaining Meal Amount Rs.</th>
             </tr>
            <?php
                  echo "<tr>";
                  echo "<td><b>TOTAL</b></td>";
                  echo "<td style='text-align: center'>".number_format($totOTHRS)."</td>";
                  echo "<td style='text-align: center'>".number_format($totDOTHRS)."</td>";
                  echo "<td style='text-align: right'>".number_format($totAMNTOT,2)."</td>";
                  echo "<td style='text-align: right'>".number_format($totAMNTDOT,2)."</td>";
                  echo "<td style='text-align: right'>".number_format($totRemMeal,2)."</td>";  
                  echo "</tr>";

            ?>
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
  echo "<input type='hidden' id='emp_stat' value='".$_REQUEST["emp_status"]."'>";
}
?>

<div id="space"></div>

<?php include("../Contains/footer.php"); ?>

<?php
  
  function getSurName($name){
    $initials = "";
    $words = explode(' ', $name);
    for ($i=0; $i < count($words)-1; $i++) { 
      $initials .= strtoupper(substr($words[$i], 0,1))." ";  
    }

    return rtrim($initials);
  }

?>

</div>
</div>
</body>
</html>