<?php
error_reporting(0);
include("../Contains/header.php");
include '../DB/DB.php';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Employee Roster Report | Apex Payroll</title>
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

 function exportExcel(){

      var dataz = document.getElementById('report').innerHTML;

      // alert(dataz);

      var url = "../Controller/emp_manage.php?request=setSession";
      $.ajax({
        type: 'POST',
        url: url,
        data: { 'data': dataz, 'filename': 'Roster_report' },
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
                    <td><h3>Employee Roster Report<br/> <small>Genarate Roster Reports</small></h3></td>
                  </tr>
                </table>

                <table width="120%">
                 <tr>
                  <form action="emp_roster_report.php" method="get">
                    
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
                            $query = "select uid,fname,lname,jobcode,epfno from user where isactive='1' and work_typ = '2' and uid != '2' order by length(jobcode),jobcode ASC";
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
                 <h3>Employee Monthly Rosters</h3>
                 <p>Month : <?php echo $MONTH;?> | Year : <?php echo $_GET["year"]; ?> | Branch : <?php echo $branch_Name;?> | Department : <?php echo $dept_Name; ?></p>
               </center>
               <hr/>
               <div>
                 <h4 style="float: right;">Printed Date : <?php echo date("Y/m/d"); ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</h4>
               </div>

             </br>
             <br>
             <br>
             <div id="tdatax" style="overflow-x: scroll;">
             <table border="1" width="90%" class="table table-bordered" style="border-collapse: collapse; color: black;">
               <thead >
                <tr>
                  <th rowspan="2"></th>
                  <th rowspan="2">Name</th>
                  <?php
                      $selected_month_day_count = cal_days_in_month(CAL_GREGORIAN, $_GET["month"], $_GET["year"]);

                      for ($i=1; $i <= $selected_month_day_count; $i++) 
                      {?> 
                        <th><?php echo $i; ?></th><?php
                      }
                  ?>
                </tr>
                <tr>
                   <?php
                      for ($j=1; $j <= $selected_month_day_count; $j++) 
                      {
                            if (date('D', strtotime($_GET["year"]."-".$_GET["month"]."-".$j)) == "Sat") 
                            {
                              $cell_clr = "#ebeb73";
                            }
                            else if (date('D', strtotime($_GET["year"]."-".$_GET["month"]."-".$j)) == "Sun") 
                            {
                              $cell_clr = "#deb1ce";
                            }
                            else
                            {
                              $cell_clr = "";
                            }

                        ?> 
                        <td style="background-color: <?php echo $cell_clr; ?> "><?php echo date('D', strtotime($_GET["year"]."-".$_GET["month"]."-".$j)); ?></td><?php
                      }
                  ?>
                </tr>

              </thead>
              <tbody>
                <?php
                
                $count = 1;

                $resalx = Search("select a.fname,a.uid from user a, emppost b, position c where b.position_pid = c.pid and a.emppost_id = b.id and a.uid like '".$_GET["employee"]."' and c.pid like '".$_GET["dept"]."' and a.isactive like '".$_GET["empstatus"]."' and a.dept_id like '".$_GET["pmethod"]."' and a.work_typ='2' order by jobcode ASC");

                
                while ($resultalx = mysqli_fetch_assoc($resalx)) 
                {
                   echo "<tr>";   
                   echo "<td>".$count . "</td>";
                   echo "<td>".$resultalx["fname"]."</td>";

                   for ($k=1; $k <= $selected_month_day_count; $k++) 
                   {
                      $res_rs_data = Search("select b.name,b.clr_code from emp_has_shift a LEFT JOIN shift_working_time_profile_settings b ON a.espid = b.swtpsid where YEAR(a.date)='".$_GET["year"]."' and MONTH(a.date)='".$_GET["month"]."' and a.user_uid='".$resultalx["uid"]."' and DAY(a.date)='".$k."' order by a.date ASC");

                      if ($result_rs_data = mysqli_fetch_assoc($res_rs_data)) 
                      {
                          if ($result_rs_data["clr_code"] == "#000000") 
                          {
                             $action = "style='background-color:".$result_rs_data["clr_code"]."; color:white;'";
                          }
                          else
                          {
                             $action = "style='background-color:".$result_rs_data["clr_code"].";'";
                          }
                          echo "<td align='center' ".$action.">".$result_rs_data["name"]."</td>";
                      }
                      else
                      {
                          echo "<td></td>";
                      }
                   }
                   
                   echo "</tr>";
                   $count++;
                }
                ?>
             </tbody>
           </table>
           </br>
            <table style="border-collapse: collapse; color: black; font-weight: bold;">
              <tbody>
                <?php
                $res_rs_footer = Search("select name,sh_intime,sh_outtime,clr_code from shift_working_time_profile_settings order by swtpsid ASC");
                $cellCount = 0;

                // Start the first row
                echo "<tr>";

                while ($result_rs_footer = mysqli_fetch_assoc($res_rs_footer)) 
                {
                    if ($result_rs_footer["clr_code"] == "#000000") 
                    {
                      $action = "style='background-color:".$result_rs_footer["clr_code"]."; color:white; border: 1px solid black; padding: 5px; border-collapse: collapse;'";
                    } 
                    else 
                    {
                      $action = "style='background-color:".$result_rs_footer["clr_code"]."; border: 1px solid black; padding: 5px; border-collapse: collapse;'";
                    }

                    // Start a new row after four cells
                    if ($cellCount % 5 == 0 && $cellCount != 0) 
                    {
                        echo "</tr><tr>";
                    }

                    // Create cell
                    echo "<td align='center' ".$action.">".$result_rs_footer["name"]."</td><td>&nbsp;: ".$result_rs_footer["sh_intime"]." - ".$result_rs_footer["sh_outtime"]."&nbsp;&nbsp;</td>";

                    $cellCount++;
                }

                // Complete the last row if it doesn't have four cells
                while ($cellCount % 5 != 0) 
                {
                    echo "<td></td>";
                    $cellCount++;
                }

                // Close the last row
                echo "</tr>";
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
  echo "<input type='hidden' id='empstat' value='".$_REQUEST["empstatus"]."'>";
}
?>

<div id="space"></div>

<?php include("../Contains/footer.php"); ?>
</div>
</div>
</body>
</html>