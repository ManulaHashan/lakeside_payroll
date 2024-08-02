<?php
error_reporting(0);
include("../Contains/header.php");
include '../DB/DB.php';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Month Wise Salary Average | Appex Payroll</title>
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

    if($('#hmon').length !== 0 || $('#fmon').length !== 0){
      $('#tomonth').val($('#hmon').val());
      $('#frommonth').val($('#fmon').val());
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

      var url = "../Controller/emp_manage.php?request=setSession";
      $.ajax({
        type: 'POST',
        url: url,
        data: { 'data': dataz, 'filename': 'ETF_6_Month_Rep' },
        success: function(data) {

                    // alert(data);
 
                    var page = "../Model/excel_export.php";   

                    window.location = encodeURI(page); 
                }
            });
    }

 function selectMonth(){
  $("#tomonth").val($("#tomonth").val());
  $("#frommonth").val($("#frommonth").val());
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

                <table width="130%">
                 <tr>
                  <form action="#" method="get">
                    <td><h3>Salary Average <br/> <small>Genarate Salary Average Report</small></h3></td>
                    <td>&nbsp;</td>
                    <td width="50">
                        Year : 
                        <select id="year" name="year" class="select-basic" style="width: 70px;">
                          <option value="2019">2019</option>
                          <option value="2020">2020</option>
                          <option value="2021">2021</option>
                          <option value="2022">2022</option>
                          <option value="2023">2023</option>
                          <option value="2024">2024</option>
                          <option value="2025">2025</option>
                          <option value="2026">2026</option>
                        </select>
                    </td>
                    <td>&nbsp;</td>    
                    <td width="50"> 
                      From Month :
                      <select id="tomonth" name="tomonth" class="select-basic" onchange="selectMonth()">                  
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
                    <td>&nbsp;</td>
                    <td width="50"> 
                      To Month :
                      <select id="frommonth" name="frommonth" class="select-basic" onchange="selectMonth()">                  
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
                      <td width="100">
                      Emp.Status : 
                      <select id="emp_status" name="emp_status" class="select-basic">
                        <option value="1">Active</option>
                        <option value="0">Not Active</option>
                        <option value="%">All</option>
                      </td>
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
                 <h3>Month Wise Salary Average</h3>
                 <p>Year : <?php echo $_GET["year"];?> | From Month : <?php echo $_GET["tomonth"]; ?> | To Month : <?php echo $_GET["frommonth"]; ?></p>
                 <p><small>Printed Date : <?php echo date("Y/m/d"); ?></small></p>
               </center>
               <hr/>
             <br>
             <div id="tdatax" style="width:90%;">
             <table border="1" width="100%" class="table table-bordered" style="border-collapse: collapse;">
               <thead >
                <tr>
                   <th>EPF No</th>
                   <th>Employee's Name</th>
                  <?php

                   
                      for ($b=$_GET["tomonth"]; $b <= $_GET["frommonth"] ; $b++) 
                      {
                          if($b == 1)
                           {
                              $monthname = "January";
                           }
                           elseif ( $b == 2) 
                           {
                              $monthname = "February";
                           }
                           elseif ( $b == 3) 
                           {
                              $monthname = "March";                               
                           }
                           elseif ( $b == 4) 
                           {
                             $monthname = "April";
                           }
                           elseif ( $b == 5) 
                           {
                             $monthname = "May"; 
                           }
                           elseif ( $b == 6) 
                           {
                              $monthname = "June";
                           }
                           elseif ( $b == 7) 
                           {
                              $monthname = "July";
                           }
                           elseif ( $b == 8) 
                           {
                              $monthname = "August";
                           }
                           elseif ( $b == 9) 
                           {
                              $monthname = "September";
                           }
                           elseif ( $b == 10) 
                           {
                              $monthname = "October";
                           }
                           elseif ( $b == 11) 
                           {
                              $monthname = "November";
                           }
                           elseif ( $b == 12) 
                           {
                              $monthname = "December";
                           }
                         
                           
                           echo "<th style='text-align: center'>Salary Of " . $monthname . "</th>";
                      } 
                   
                  ?>
                  <th>Total Salary</th>
                  <th>Salary Average</th>
                </tr>

              </thead>
              <tbody>
                <?php
                    
                    $tomonth = $_GET["tomonth"];
                    if(count($_GET["tomonth"]) == 1){
                      $tomonth = "0".$tomonth;
                    }

                    $frommonth = $_GET["frommonth"];
                    if(count($_GET["frommonth"]) == 1){
                      $frommonth = "0".$frommonth;
                    }

                    $resalx = Search("select u.uid,u.fname as emp,u.nic,u.epfno from user u,salarycomplete sal where u.uid = sal.uid and u.isactive like '".$_GET["emp_status"]."' group by u.uid order by cast(u.epfno as unsigned) ASC");

                    while ($resultalx = mysqli_fetch_assoc($resalx)) {

                     echo "<tr>";
                     echo "<td><center>". $resultalx["epfno"] . "<center></td>";
                     echo "<td>".$resultalx["emp"] . "</td>";
                     
                     $tot_sal_mo = 0;
                     $count = 0;
                     $gross = 0;
                     for ($m=$_GET["tomonth"]; $m <=$_GET["frommonth"]; $m++) 
                     {  

                         $resalxT = Search("select s.basic as basicsalary, s.br1 as BR1, s.br2 as BR2, s.nopay as Nopay, s.att1 as attendenceallow, s.travl as travelallow, s.att_incen as attIncentive, s.ot as NormalOt from salarycomplete s, user u where s.uid=u.uid and s.month = '".$m."' and s.year = '".$_GET["year"]."' and u.uid = '" . $resultalx["uid"] . "'");

                        if ($resultalxA = mysqli_fetch_assoc($resalxT)) 
                        { 
                            $Basic_Salary = $resultalxA["basicsalary"];
                            $Br1 = $resultalxA["BR1"];
                            $Br2 = $resultalxA["BR2"];
                            $Nopay_deduction = $resultalxA["Nopay"];
                            $Att_Allow = $resultalxA["attendenceallow"];
                            $Trvl_Allow = $resultalxA["travelallow"];
                            $Att_Incentive = $resultalxA["attIncentive"];
                            $Normal_OT = $resultalxA["NormalOt"];
                        }
                        else
                        {
                            $Basic_Salary = 0;
                            $Br1 = 0;
                            $Br2 = 0;
                            $Nopay_deduction = 0;
                            $Att_Allow = 0;
                            $Trvl_Allow = 0;
                            $Att_Incentive = 0;
                            $Normal_OT = 0;
                        }

                        $resProduct = Search("select a.amount as TotalProduct from user_has_allowances a, allowances b, salarycomplete c,user u where a.alwid = b.alwid and lower(b.name) ='Production Incentive' and a.uid = c.uid and u.uid=c.uid and u.uid = '" . $resultalx["uid"] . "' and c.month = '".$m."' and c.year = '".$_GET["year"]."'");
                         if ($resultProduct = mysqli_fetch_assoc($resProduct)) 
                         {
                            $Produ =  $resultProduct["TotalProduct"];     
                         }
                         else
                         {
                            $Produ =  0;
                         }

                         $resOtherAllow = Search("select a.amount as TotalOther from user_has_allowances a, allowances b, salarycomplete c,user u where a.alwid = b.alwid and a.uid = c.uid and u.uid=c.uid and u.uid = '" . $resultalx["uid"] . "' and c.month = '".$m."' and c.year = '".$_GET["year"]."' and lower(b.name) ='Other Allowances'");
                         if ($resultOtherAllow = mysqli_fetch_assoc($resOtherAllow)) 
                         {
                            $Other =  $resultOtherAllow["TotalOther"];        
                         }
                         else
                         {
                            $Other =  0;
                         }

                        $resTeamLead = Search("select a.amount as TotalTeamLead from user_has_allowances a, allowances b,salarycomplete c,user u  where a.uid = c.uid and u.uid=c.uid and u.uid = '" . $resultalx["uid"] . "' and a.alwid = b.alwid and lower(b.name) ='Team Leader Incentive' and c.month = '".$m."' and c.year = '".$_GET["year"]."'");
                        if ($resultTeamLead = mysqli_fetch_assoc($resTeamLead)) 
                        {
                          $TeamLead =  $resultTeamLead["TotalTeamLead"];
                        }
                        else
                        {
                          $TeamLead =  0;
                        }
                        
                        $gross = $Basic_Salary+$Br1+$Br2-$Nopay_deduction+$Att_Allow+$Trvl_Allow+$Produ+$Other+$Att_Incentive+$Normal_OT+$TeamLead;

                        echo "<td align='right'>" . number_format($gross, 2) . "</td>";
                        
                        $tot_sal_mo += $gross;
                        $count++;
                     }

                     echo "<td align='right'>" . number_format($tot_sal_mo, 2) . "</td>";
                     echo "<td align='right'>" . number_format($tot_sal_mo/$count, 2) . "</td>";

                     echo "</tr>";

                   }

               ?>
               

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

  echo "<input type='hidden' id='hmon' value='".$_REQUEST["tomonth"]."'>";
  echo "<input type='hidden' id='fmon' value='".$_REQUEST["frommonth"]."'>";
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