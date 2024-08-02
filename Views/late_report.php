<?php
error_reporting(0);
include("../Contains/header.php");
include '../DB/DB.php';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Late Report | Apex Payroll</title>
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

    if($('#dfrom').length !== 0){
      $('#datefrom').val($('#dfrom').val());
      $('#dateto').val($('#dto').val());
      $('#employee').val($('#user').val());
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

 function print2(){
   var divToPrint0 = document.getElementById('ABC');
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
        data: { 'data': dataz, 'filename': 'EPF_C_Form' },
        success: function(data) {

                    // alert(data);
 
                    var page = "../Model/excel_export.php";   

                    window.location = encodeURI(page); 
                }
            });
    }

  function exportExcel2(){

      var dataz = document.getElementById('ABC').innerHTML;

      // alert(dataz);

      var url = "../Controller/emp_manage.php?request=setSession";
      $.ajax({
        type: 'POST',
        url: url,
        data: { 'data': dataz, 'filename': 'EPF_C_Form' },
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
                <h3>Late Reporting<br/> <small>Genarate Late Report</small></h3>
                <table width="120%">
                 <tr>
                  <form action="#" method="get">
                    <td width="50"> 
                      Date From :
                      <input id="datefrom" type="date" name="datefrom" class="input-text" style="width: 150px"> </td>
                      <td>&nbsp;</td>
                      <td width="50">
                        To : 
                        <input id="dateto" type="date" name="dateto" class="input-text" style="width: 150px">
                        </td>
                        <td>&nbsp;</td>
                      <td width="50">
                        Employee : 
                        <select id="employee" name="employee" class="select-basic" style="width: 150px; height: 25px;">
                            <option value="%"></option>
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
                        <td width="100">
                        Emp.Status : 
                        <select id="emp_status" name="emp_status" class="select-basic" style="width: 100px; height: 25px;">
                          <option value="1">Active</option>
                          <option value="0">Not Active</option>
                          <option value="%">All</option>
                        </td>
                        <td>&nbsp;&nbsp;</td>
                        <td></br>&nbsp;<input type="submit" name="submit" value="Generate" class="btn btn-primary"></td>
                        <td></br>&nbsp;<input type="button" value="Print Daily Late Report" class="btn btn-dark" onclick="print()"></td>
                        <td></br>&nbsp;<input type="button" value="Print Total Late Summery" class="btn btn-dark" onclick="print2()"></td>
                        <td></br>&nbsp;<input type="button" value="Export Excel Daily Late Report" class="btn btn-success" onclick="exportExcel()"></td>
                        <td></br>&nbsp;<input type="button" value="Export Excel Total Late Report" class="btn btn-success" onclick="exportExcel2()"></td>
                      </form>
                    </tr>
                  </table>
                </div>
              </div>

              <div id="report">

                <center>
                 <h3>Late Report</h3>
                 <p><b>Date From :</b> <?php echo $_GET["datefrom"]; ?>  |  <b>Date To :</b> <?php echo $_GET["dateto"]; ?> </p>
               </center>
               <hr/>
               <div>
                 <h4 style="float: right;">Printed Date : <?php echo date("Y/m/d"); ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h4>
               </div>

             </br>

             <div id="tdatax" style="width:90%;">
             <table border="1" class="table table-bordered" style="border-collapse: collapse;">
               <thead >

                <tr>
                  <th>EPF No</th>
                  <th>Employee's Name</th>
                  <th>Department</th>
                  <th>Date</th>
                  <th>Late Minutes</th>
                </tr>

              </thead>
              <tbody>
                <?php
                $totAdd = 0;

                $totCons = 0;
                $totEM = 0;
                $totEMPE = 0;
                $totEarn = 0;
                $tot_earn = 0;
                $epf8 = 0;
                $epf12 = 0;
                
                $resalx = Search("select u.epfno,u.fname as emp,dept.name as department,att.date,att.late_att_min from attendance att,user u,position dept , emppost post where post.position_pid = dept.pid and att.User_uid = u.uid and u.emppost_id = post.id and u.isactive like '".$_GET["emp_status"]."' and att.date between '" . $_GET["datefrom"] . "' and '" . $_GET["dateto"] . "' and u.uid like '".$_GET["employee"]."' and att.late_att_min != '0' and att.late_att_min != '' order by cast(u.epfno as unsigned) ASC,att.date");


                while ($resultalx = mysqli_fetch_assoc($resalx)) {
                 
                 echo "<tr>";
                 echo "<td style='text-align: center'>".$resultalx["epfno"] . "</td>";
                 echo "<td>".$resultalx["emp"] . "</td>";  
                 echo "<td>".$resultalx["department"] . "</td>";   
                 echo "<td><center>". $resultalx["date"] . "<center></td>";
                 echo "<td><center>".number_format($resultalx["late_att_min"],2) . "</center></td>";
                 echo "</tr>"; 

                }

                    
               ?>
               

             </tbody>
           </table>

          </br></br>
         </div>
       </div>
         
          <div id="ABC">
          </br></br>
             <center>
             <h3>Total Late Summery</h3>
             <p><b>Date From :</b> <?php echo $_GET["datefrom"]; ?>  |  <b>Date To :</b> <?php echo $_GET["dateto"]; ?> </p>
             </center>

             <hr/>
             <div>
                 <h4 style="float: right;">Printed Date : <?php echo date("Y/m/d"); ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h4>
               </div>
           
           </br>

           <table border="1" class="table table-bordered" style="border-collapse: collapse; width:90%;">
            <thead>
             <tr>
               <th>EPF No</th>
               <th>Employee Name</th>
               <th>Department</th>
               <th>Total Minutes</th>
             </tr>
            </thead> 
            <?php
                 
                $totMinutes = 0; 

                $resalxD = Search("select u.epfno,u.fname as emp,dept.name as department,sum(att.late_att_min) as totmins from attendance att,user u,position dept , emppost post where post.position_pid = dept.pid and att.User_uid = u.uid and u.emppost_id = post.id and u.isactive like '".$_GET["emp_status"]."' and att.date between '" . $_GET["datefrom"] . "' and '" . $_GET["dateto"] . "' and u.uid like '".$_GET["employee"]."' and att.late_att_min != '0' and att.late_att_min != '' group by u.uid order by cast(u.epfno as unsigned) ASC");
                while ($resultalxD = mysqli_fetch_assoc($resalxD)) {

                    $EpfNo = $resultalxD["epfno"];
                    $EmpName = $resultalxD["emp"];
                    $DEPT = $resultalxD["department"];
                    $TOTALM = $resultalxD["totmins"];

                      echo "<tr>";
                      echo "<td style='text-align: center'>".$EpfNo."</td>";
                      echo "<td>".$EmpName."</td>";
                      echo "<td>".$DEPT."</td>";
                      echo "<td style='text-align: center'>".number_format($TOTALM,2, '.', '')."</td>";
                      echo "</tr>";

                      $totMinutes += $TOTALM;
                }

                echo "<tr style='border-top: 2px solid black;'>";   
                echo "<td colspan='3' style='text-align: right'><b>Total</b></td>";
                echo "<td style='text-align: center'>".number_format($totMinutes,2, '.', '')."</td>";
                echo "</tr>";

            ?>
           </table></br></br>
      </div>

     </div>
      
   </div> 

 </div>    
</div>        
</div>

<?php
if(isset($_REQUEST["submit"])){

  echo "<input type='hidden' id='dfrom' value='".$_REQUEST["datefrom"]."'>";
  echo "<input type='hidden' id='dto' value='".$_REQUEST["dateto"]."'>";
  echo "<input type='hidden' id='user' value='".$_REQUEST["employee"]."'>";
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