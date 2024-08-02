<?php
error_reporting(0);
include("../Contains/header.php");
include '../DB/DB.php';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Month Wise Absent Report | Apex Payroll</title>
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

      var dataz = document.getElementById('report').innerHTML;

      // alert(dataz);

      var url = "../Controller/emp_manage.php?request=setSession";
      $.ajax({
        type: 'POST',
        url: url,
        data: { 'data': dataz, 'filename': 'Month_Wise_Absent' },
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

var totrem = 0.00;
function CalRemitance()
{
  var cont = $("#contri").html();
  var serch = $("#surchrg").html();

  if (serch == "") 
  {
     serch = 0;
  }
  
  totrem = parseFloat(cont) - parseFloat(serch);

  $("#remit").html(totrem.toFixed(2));
  
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

                <table width="120%">
                 <tr>
                  <form action="#" method="get">
                    <td><h3>Month Wise Absent Reporting<br/> <small>Genarate Month Wise Absent Report</small></h3></td>
                    <td>&nbsp;</td>
                    <td width="50"> 
                      Month :
                      <select id="month" name="month" class="select-basic" onchange="selectMonth()" style="height: 25px;">                  
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
                        <select id="year" name="year" class="select-basic" style="height: 25px;">
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
                        <td width="100">
                        Emp.Status : 
                        <select id="emp_status" name="emp_status" class="select-basic" style="height: 25px;">
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
                 <h3>Month Wise Absent Report</h3>
                 <p>Month : <?php echo $_GET["month"]; ?> | Year : <?php echo $_GET["year"]; ?> </p>
                 <p><small>Printed Date : <?php echo date("Y/m/d"); ?></small></p>
                  <table>
                       <tr>
                        <td>Poya Days :&nbsp;&nbsp;<input type="text" style="width: 40px; background-color: #e6e600; border: none;" readonly="true">&nbsp;&nbsp;</td>
                        <td>Sundays :&nbsp;&nbsp;<input type="text" style="width: 40px; background-color: #00e64d; border: none;" readonly="true">&nbsp;&nbsp;</td>
                        <td>Saturdays :&nbsp;&nbsp;<input type="text" style="width: 40px; background-color: #EF9A9A; border: none;" readonly="true">&nbsp;&nbsp;</td>
                        <td>Absent Days :&nbsp;&nbsp;<input type="text" style="width: 40px; background-color: #edb3f5; border: none;" readonly="true">&nbsp;&nbsp;</td>  
                      </tr>
                  </table>
               </center>
               <hr/>

             <div id="tdatax" style="width:90%;">
             <table border="1" width="100%" class="table table-bordered" style="border-collapse: collapse;">
               <thead >
                <tr>
                  <th>EPF No.</th>
                  <th>Employee's Name</th>
                  <th>Date</th>
                  <th>Reason</th>
                </tr>
              </thead>
              <tbody>
                <?php
                
                $month = $_GET["month"];
                if(count($_GET["month"]) == 1){
                  $month = "0".$month;
                }




                $resalx = Search("select u.uid,u.fname as emp,u.epfno from user u,attendance att where att.User_uid = u.uid and u.isactive like '".$_GET["emp_status"]."' and u.uid != '2' and u.uid != '7' group by u.uid order by cast(u.epfno as unsigned) ASC");
                while ($resultalx = mysqli_fetch_assoc($resalx)) {
                 
                 echo "<tr style='background-color:#f0edda;'>";
                 echo "<td><center>". $resultalx["epfno"] . "<center></td>";
                 echo "<td>".$resultalx["emp"] . "</td>";
                 echo "<td colspan='2'></td></tr>";

                 $thisMonth = cal_days_in_month(CAL_GREGORIAN, $_GET["month"], $_GET["year"]);

                 for ($i=1; $i <= $thisMonth; $i++) 
                 { 
                    
                     $resalz = Search("select type,date from employee_leave where date='" . $_GET["year"] . "-" . $_GET["month"] . "-".$i."'and uid = '" . $resultalx["uid"] . "'");

                     if ($resultalz = mysqli_fetch_assoc($resalz)) 
                     {
                        
                       echo "<tr><td colspan='2'></td><td align='center'>".$resultalz["date"] . "</td><td>".$resultalz["type"]. "</td></tr>";
                     }
                     else
                     {
                         $resalxr = Search("select aid from attendance where date='" . $_GET["year"] . "-" . $_GET["month"] . "-".$i."'and User_uid = '" . $resultalx["uid"] . "' and intime IS NOT NULL and intime IS NOT NULL");

                         if ($resultalxr = mysqli_fetch_assoc($resalxr)) 
                         {
                            
                           
                         }
                         else
                         {

                              $querp = "select name from poyadays where date = '".date("Y-m-d", strtotime($_GET["year"]."-".$_GET["month"] ."-".$i))."'";
                              $resp = Search($querp);
                              if ($resulp = mysqli_fetch_assoc($resp)) 
                              {
                                  echo "<tr><td colspan='2'></td><td align='center'>".date("Y-m-d", strtotime($_GET["year"]."-".$_GET["month"] ."-".$i))."</td><td style='background-color: #e6e600; color: black;'>".$resulp["name"]."</td></tr>";
                              }
                              else
                              {
                                  $DAYNAME = strtotime(date("Y-m-d", strtotime($_GET["year"]."-".$_GET["month"] ."-".$i)));
                                  $DAYNAME = date('l', $DAYNAME);
                                  //if date is sunday 
                                  if ($DAYNAME == "Sunday") 
                                  {
                                      echo "<tr><td colspan='2'></td><td align='center'>".date("Y-m-d", strtotime($_GET["year"]."-".$_GET["month"] ."-".$i))."</td><td style='background-color: #00e64d; color: black;'>Sunday</td></tr>";
                                  }
                                  else
                                  {
                                      //if date is Saturday 
                                      if ($DAYNAME == "Saturday") 
                                      {
                                          echo "<tr><td colspan='2'></td><td align='center'>".date("Y-m-d", strtotime($_GET["year"]."-".$_GET["month"] ."-".$i))."</td><td style='background-color: #EF9A9A; color: black;'>Saturday</td></tr>";
                                      }
                                      else
                                      {
                                          echo "<tr><td colspan='2'></td><td align='center'>".date("Y-m-d", strtotime($_GET["year"]."-".$_GET["month"] ."-".$i))."</td><td style='background-color: #edb3f5; color: black;'>Absent</td></tr>";
                                      }
                                  }

                                  
                              }

                             
                         }
                     }
                    
                 } 

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