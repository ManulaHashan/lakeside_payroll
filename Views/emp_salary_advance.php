<?php
error_reporting(0);
include("../Contains/header.php");
include '../DB/DB.php';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Salary Advance Reports | Apex Payroll</title>
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
                    <td><h3>Salary Advance Reporting<br/> <small>Genarate Salary Advance Reports</small></h3></td>
                  </tr>
                </table>

                <table width="120%">
                 <tr>
                  <form action="#" method="get">
                    
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
                        </td>
                        <td>&nbsp;</td>
                      <td width="50">
                        Employee : 
                        <select id="employee" name="employee" class="select-basic" style="width: 150px; height: 23px;">
                            <option value="%"></option>
                            <?php
                            $query = "select uid,fname,lname,jobcode,epfno from user where isactive='1' and uid != '2' order by length(jobcode),jobcode ASC";
                            $res = Search($query);
                            while ($result = mysqli_fetch_assoc($res)) {
                                ?>
                                <option value="<?php echo $result["uid"]; ?>"> <?php echo $result["jobcode"]; ?> : &nbsp; <?php echo $result["fname"]; ?> </option>
                            <?php } ?>
                        </select>
                      </td> 
                      <td>&nbsp;</td>
                      <td width="50">
                        Department : 
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
                      </td>
                      <td>&nbsp;</td>
                      <td width="50">
                        Payment method : 
                        <select id="pmethod" name="pmethod" class="select-basic" style="width: 150px; height: 23px;">
                          <option value="%"></option>
                          <option value="1">Bank Transfer</option>
                          <option value="0">Cash</option>
                        </td>
                      <td>&nbsp;</td>
                        <td width="100">
                        Emp.Status : 
                        <select id="emp_status" name="emp_status" class="select-basic" style="height: 23px;">
                          <option value="1">Active</option>
                          <option value="0">Not Active</option>
                          <option value="%">All</option>
                        </td>   
                      <td>&nbsp;</td> 
                        <td>&nbsp;&nbsp;<input type="submit" name="submit" value="Generate" class="btn btn-primary"></td>
                        <td>&nbsp;&nbsp;<input type="button" value="Print Report" class="btn btn-dark" onclick="print()"></td>
                        <td>&nbsp;&nbsp;<input type="button" value="Export Excel" class="btn btn-success" onclick="exportExcel()"></td>
                      </form>
                    </tr>
                  </table>
                </div>
              </div>



              <div id="report">

                <center>
                 <h3>Salary Advance Summery</h3>
                 <p>Month : <?php echo $_GET["month"]; ?> | Year : <?php echo $_GET["year"]; ?> | Payment Method : 

                    <?php

                        if ($_GET["pmethod"] == "0") 
                        {
                           echo "Cash";
                        }
                        elseif ($_GET["pmethod"] == "1") 
                        {
                           echo "Bank Transfer";
                        }
                        else 
                        {
                           echo "All";
                        }
                      
                     ?>

                 </p>
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
             <div id="tdatax" style="width:90%;">
             <table border="1" class="table table-bordered" style="border-collapse: collapse;">
              <thead> 
               <?php

                  if ($_GET["pmethod"] == "0") 
                  {?>
                    <th>EPF No</th>
                    <th>Employee's Name</th>
                    <th>Advance Amount</th>

                    <?php
                  }
                  elseif ($_GET["pmethod"] == "1") 
                  {?>
                    <th>EPF No</th>
                    <th>Employee's Name</th>
                    <th>Account No</th>
                    <th>Bank</th>
                    <th>Branch</th>
                    <th>Advance Amount</th>

                     <?php
                  }
                  else 
                  {?>
                    <th>EPF No</th>
                    <th>Employee's Name</th>
                    <th>Account No</th>
                    <th>Bank</th>
                    <th>Branch</th>
                    <th>Advance Amount</th>

                     <?php
                  }
                
               ?>
              </thead>
              <tbody>
                <?php
                
                $totPaid = 0;

                if ($_GET["pmethod"] == "0") 
                  {
                     $resalx = Search("select a.*, b.fname,b.epfno,b.uid,b.bankno,b.bank from salarypayment a, user b, emppost c, position d where a.User_uid = b.uid and c.position_pid = d.pid and b.emppost_id = c.id and MONTH(a.date) = '".$_GET["month"]."' and YEAR(a.date) = '".$_GET["year"]."' and a.User_uid like '".$_GET["employee"]."' and d.pid like '".$_GET["dept"]."' and b.isactive like '".$_GET["emp_status"]."' and b.bankno = '' and b.bank = '' and b.uid != '2'  order by cast(b.epfno as unsigned) ASC");

                     while ($resultalx = mysqli_fetch_assoc($resalx)) {
                       echo "<tr>"; 
                       echo "<td><center>".$resultalx["epfno"] . "</center></td>";  
                       echo "<td>".$resultalx["fname"]."</td>";
                       echo "<td align='right'>".number_format($resultalx["tot"],2). "</td>";
                       echo "</tr>";

                       $totPaid += $resultalx["tot"];

                     }?>

                     <tr style="font-weight: bold;">
                       <td colspan="2" align="right">TOTAL</td> 
                       <td align='right'><?php echo number_format($totPaid,2);?></td>
                     </tr>

                     <?php

                  }
                  elseif ($_GET["pmethod"] == "1") 
                  {
                     $resalx = Search("select a.*, b.fname,b.epfno,b.uid,b.bankno,b.bank from salarypayment a, user b, emppost c, position d where a.User_uid = b.uid and c.position_pid = d.pid and b.emppost_id = c.id and MONTH(a.date) = '".$_GET["month"]."' and YEAR(a.date) = '".$_GET["year"]."' and a.User_uid like '".$_GET["employee"]."' and d.pid like '".$_GET["dept"]."' and b.isactive like '".$_GET["emp_status"]."' and b.bankno != '' and b.bank != '' and b.uid != '2'  order by cast(b.epfno as unsigned) ASC");

                     while ($resultalx = mysqli_fetch_assoc($resalx)) {
                       echo "<tr>"; 
                       echo "<td><center>".$resultalx["epfno"] . "</center></td>";  
                       echo "<td>".$resultalx["fname"]."</td>";
                       echo "<td><center>".$resultalx["bankno"] . "<center></td>";
                       $valArr = explode("-",$resultalx["bank"]);
                       echo "<td><center>". $valArr[0] . "<center></td>";
                       echo "<td><center>". $valArr[1] . "<center></td>";
                       echo "<td align='right'>".number_format($resultalx["tot"],2). "</td>";
                       echo "</tr>";

                       $totPaid += $resultalx["tot"];

                     }?>
                    
                    <tr style="font-weight: bold;">
                     <td colspan="5" align="right">TOTAL</td> 
                     <td align='right'><?php echo number_format($totPaid,2);?></td>
                   </tr>

                   <?php

                  }
                  else
                  {
                     $resalx = Search("select a.*, b.fname,b.epfno,b.uid,b.bankno,b.bank from salarypayment a, user b, emppost c, position d where a.User_uid = b.uid and c.position_pid = d.pid and b.emppost_id = c.id and MONTH(a.date) = '".$_GET["month"]."' and YEAR(a.date) = '".$_GET["year"]."' and a.User_uid like '".$_GET["employee"]."' and d.pid like '".$_GET["dept"]."' and b.isactive like '".$_GET["emp_status"]."' and b.uid != '2' order by cast(b.epfno as unsigned) ASC");

                     while ($resultalx = mysqli_fetch_assoc($resalx)) {
                       echo "<tr>"; 
                       echo "<td><center>".$resultalx["epfno"] . "</center></td>";  
                       echo "<td>".$resultalx["fname"]."</td>";
                       echo "<td><center>".$resultalx["bankno"] . "<center></td>";
                       $valArr = explode("-",$resultalx["bank"]);
                       echo "<td><center>". $valArr[0] . "<center></td>";
                       echo "<td><center>". $valArr[1] . "<center></td>";
                       echo "<td align='right'>".number_format($resultalx["tot"],2). "</td>";
                       echo "</tr>";

                       $totPaid += $resultalx["tot"];

                     }?>
                    
                    <tr style="font-weight: bold;">
                     <td colspan="5" align="right">TOTAL</td> 
                     <td align='right'><?php echo number_format($totPaid,2);?></td>
                   </tr>

                   <?php

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
  echo "<input type='hidden' id='user' value='".$_REQUEST["employee"]."'>";
  echo "<input type='hidden' id='depart' value='".$_REQUEST["dept"]."'>";
  echo "<input type='hidden' id='pmeth' value='".$_REQUEST["pmethod"]."'>";
  echo "<input type='hidden' id='emp_stat' value='".$_REQUEST["emp_status"]."'>";
}
?>

<div id="space"></div></br></br>

<?php include("../Contains/footer.php"); ?>
</div>
</div>
</body>
</html>