<?php
error_reporting(0);
include("../Contains/header.php");
include '../DB/DB.php';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Leave Balance Report | Apex Payroll</title>
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
    if($('#hyer').length !== 0){
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
                    <td><h3>Leave Balance Reporting<br/> <small>Genarate Leave Balance Report</small></h3></td>
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
                        <td></br><input type="submit" name="submit" value="Generate" class="btn btn-primary"></td>
                        <td></br><input type="button" value="Print Report" class="btn btn-dark" onclick="print()"></td>
                        <td></br><input type="button" value="Export Excel" class="btn btn-success" onclick="exportExcel()"></td>
                      </form>
                    </tr>
                  </table>
                </div>
              </div>



              <div id="report">

                <center>
                 <h3>Leave Balance Report</h3>
                 <p>Year : <?php echo $_GET["year"]; ?> </p>
                 <p><small>Printed Date : <?php echo date("Y/m/d"); ?></small></p>
               </center>
               <hr/>

             <div id="tdatax" style="width:95%;">
             <table border="1" width="100%" class="table table-bordered" style="border-collapse: collapse;">
               <thead >
                <tr>
                  <th colspan="3"></th>
                  <th><center>1st Year</center></th>
                  <th colspan="6" style="background-color: #ccbefa; color: black;"><center>2nd Year</center></th>
                  <th colspan="6" style="background-color: #85d6a9; color: black;"><center>3rd Year</center></th>
                </tr>
                <tr>
                  <th colspan="3"></th>
                  <th></th>
                  <th colspan="3" style="background-color: #ccbefa; color: black;"><center>Annual Leave</center></th>
                  <th colspan="3" style="background-color: #ccbefa; color: black;"><center>Casual Leave</center></th>
                  <th colspan="3" style="background-color: #85d6a9; color: black;"><center>Annual Leave</center></th>
                  <th colspan="3" style="background-color: #85d6a9; color: black;"><center>Casual Leave</center></th>
                </tr>
                <tr>
                  <th>EPF No.</th>
                  <th>Employee's Name</th>
                  <th>Months</th>
                  <th></th>
                  <th style="background-color: #ccbefa; color: black;"><center>Taken Leave</center></th>
                  <th style="background-color: #ccbefa; color: black;"><center>Available Leave</center></th>
                  <th style="background-color: #ccbefa; color: black;"><center>Applicable Leave</center></th>
                  <th style="background-color: #ccbefa; color: black;"><center>Taken Leave</center></th>
                  <th style="background-color: #ccbefa; color: black;"><center>Available Leave</center></th>
                  <th style="background-color: #ccbefa; color: black;"><center>Applicable Leave</center></th>
                  <th style="background-color: #85d6a9; color: black;"><center>Taken Leave</center></th>
                  <th style="background-color: #85d6a9; color: black;"><center>Available Leave</center></th>
                  <th style="background-color: #85d6a9; color: black;"><center>Applicable Leave</center></th>
                  <th style="background-color: #85d6a9; color: black;"><center>Taken Leave</center></th>
                  <th style="background-color: #85d6a9; color: black;"><center>Available Leave</center></th>
                  <th style="background-color: #85d6a9; color: black;"><center>Applicable Leave</center></th>
                </tr>
              </thead>
              <tbody>
                <?php
                
                $resalx = Search("select u.uid,u.fname as emp,u.epfno from user u,attendance att where att.User_uid = u.uid and u.isactive like '".$_GET["emp_status"]."' and u.uid != '2' and u.uid != '7' group by u.uid order by cast(u.epfno as unsigned) ASC");
                while ($resultalx = mysqli_fetch_assoc($resalx)) {
                 
                 echo "<tr style='background-color:#f0edda;'>";
                 echo "<td><center>". $resultalx["epfno"] . "<center></td>";
                 echo "<td colspan='2'>".$resultalx["emp"] . "</td>";
                 echo "<td colspan='13'></td>";
                 echo "</tr>";

                 // $YearDiff = 0;
                     $queryYears = "select registerdDate from user where uid = '" . $resultalx["uid"] . "'";
                     $resYears = Search($queryYears);

                     if ($resultYears = mysqli_fetch_assoc($resYears)) 
                     {
                         $joinDate = $resultYears["registerdDate"];
                     }

                     $YearDiff = $_GET["year"] - $joinDate;
                     $date1 = $joinDate;
                     $date2 = $_GET["year"];

                     $diff = abs(strtotime($date2) - strtotime($date1));

                     $years = floor($diff / (365*60*60*24));
                     $months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
                     $days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
               

                     if ($years >= "0" && $years < "2") 
                     {

                        if ($years == "0" && $months == "0") 
                        {
                            $casual_leaves = 0;
                            $annual_leaves = 0;

                        }
                        else if ($years == "0" && $months >= "1" && $months <= "12") 
                        {
                            if (date('m', strtotime($joinDate)) == "07" || date('m', strtotime($joinDate)) == "08" || date('m', strtotime($joinDate)) == "09" || date('m', strtotime($joinDate)) == "10" || date('m', strtotime($joinDate)) == "11" || date('m', strtotime($joinDate)) == "12") 
                            {
                                if ($years == "0" && $months >= "6")
                                {
                                    if (date('m', strtotime($joinDate)) == "01" || date('m', strtotime($joinDate)) == "02" || date('m', strtotime($joinDate)) == "03") 
                                    {
                                        $casual_leaves = 7;
                                        $annual_leaves = 14;
                                    }
                                    else if (date('m', strtotime($joinDate)) == "04" || date('m', strtotime($joinDate)) == "05" || date('m', strtotime($joinDate)) == "06") 
                                    {
                                        $casual_leaves = 7;
                                        $annual_leaves = 10;
                                    }
                                    else if (date('m', strtotime($joinDate)) == "07" || date('m', strtotime($joinDate)) == "08" || date('m', strtotime($joinDate)) == "09") 
                                    {
                                        $casual_leaves = 7;
                                        $annual_leaves = 7;
                                    }
                                     else if (date('m', strtotime($joinDate)) == "10" || date('m', strtotime($joinDate)) == "11" || date('m', strtotime($joinDate)) == "12")
                                    {
                                        $casual_leaves = 7;
                                        $annual_leaves = 4;
                                    }
                                }
                            }
                            else
                            {
                              $casual_leaves = 0;
                              $annual_leaves = 0;
                            }
                        }
                        else
                        {
                           if (date('m', strtotime($joinDate)) == "01" || date('m', strtotime($joinDate)) == "02" || date('m', strtotime($joinDate)) == "03") 
                           {
                               $casual_leaves = 7;
                               $annual_leaves = 14;
                           }
                           else if (date('m', strtotime($joinDate)) == "04" || date('m', strtotime($joinDate)) == "05" || date('m', strtotime($joinDate)) == "06") 
                           {
                               $casual_leaves = 7;
                               $annual_leaves = 10;
                           }
                           else if (date('m', strtotime($joinDate)) == "07" || date('m', strtotime($joinDate)) == "08" || date('m', strtotime($joinDate)) == "09") 
                           {
                               $casual_leaves = 7;
                               $annual_leaves = 7;
                           }
                           else
                           {
                               $casual_leaves = 7;
                               $annual_leaves = 4;
                           }
                               
                        }
                
                     }
                     else
                     {
                         $casual_leaves = 7;
                         $annual_leaves = 14;    
                     }


                     $GetCasual = 0;
                     $GetAnnual = 0;
                     $Annual_Taken = 0;
                     $Taken = 0;
                     $Total = 0;

                     $Casual_Available = $casual_leaves;
                     $Annual_Available = $annual_leaves;
                     
                     for ($i=1; $i <= 12; $i++) 
                     { 
                        echo "<tr><td colspan='2'></td><td align='center'>".date('F', mktime(0, 0, 0, $i, 10))."</td><td>No A/L</td>";

                         if ($years >= "0" && $years < "2") 
                         {
                            if ($years == "0" && $months == "0") 
                            {
                                echo "<td align='center' style='background-color: #ccbefa; color: black;'>0</td>";
                                echo "<td align='center' style='background-color: #ccbefa; color: black;'>0</td>";
                                echo "<td align='center' style='background-color: #ccbefa; color: black;'>0</td>";
                                echo "<td align='center' style='background-color: #ccbefa; color: black;'>0</td>";
                                echo "<td align='center' style='background-color: #ccbefa; color: black;'>0</td>";
                                echo "<td align='center' style='background-color: #ccbefa; color: black;'>0</td>";
                                echo "<td align='center' style='background-color: #85d6a9; color: black;'>0</td>";
                                echo "<td align='center' style='background-color: #85d6a9; color: black;'>0</td>";
                                echo "<td align='center' style='background-color: #85d6a9; color: black;'>0</td>";
                                echo "<td align='center' style='background-color: #85d6a9; color: black;'>0</td>";
                                echo "<td align='center' style='background-color: #85d6a9; color: black;'>0</td>";
                                echo "<td align='center' style='background-color: #85d6a9; color: black;'>0</td>";

                            }
                            // else if ($years == "0" && $months >= "1" && $months <= "6") 
                            // {
                            //     echo "<td align='center' style='background-color: #ccbefa; color: black;'>0</td>";
                            //     echo "<td align='center' style='background-color: #ccbefa; color: black;'>0</td>";
                            //     echo "<td align='center' style='background-color: #ccbefa; color: black;'>0</td>";
                            //     echo "<td align='center' style='background-color: #ccbefa; color: black;'>0</td>";
                            //     echo "<td align='center' style='background-color: #ccbefa; color: black;'>0</td>";
                            //     echo "<td align='center' style='background-color: #ccbefa; color: black;'>0</td>";
                            //     echo "<td align='center' style='background-color: #85d6a9; color: black;'>0</td>";
                            //     echo "<td align='center' style='background-color: #85d6a9; color: black;'>0</td>";
                            //     echo "<td align='center' style='background-color: #85d6a9; color: black;'>0</td>";
                            //     echo "<td align='center' style='background-color: #85d6a9; color: black;'>0</td>";
                            //     echo "<td align='center' style='background-color: #85d6a9; color: black;'>0</td>";
                            //     echo "<td align='center' style='background-color: #85d6a9; color: black;'>0</td>";
                            // }
                            else
                            {

                                $query = "select sum(days) as totalLeaves from employee_leave where uid = '" . $resultalx["uid"] . "' AND YEAR(date) = '".$_GET["year"]."' AND MONTH(date) = '".$i."' and type != 'Short Morning Leave' and type != 'Short Evening Leave' and type != 'Duty Full Day Leave' and type != 'Duty Morning Leave' and type != 'Duty Evening Leave' and type != 'Maternity Leave' and type != 'Parental Leave' and type != 'Liue Leave' and type != 'Nopay Full Day Leave' and type != 'Nopay Morning Leave' and type != 'Nopay Evening Leave' ";
                                $res = Search($query);

                                if ($result = mysqli_fetch_assoc($res)) {

                                  $Taken = $result["totalLeaves"];

                                  if ($result["totalLeaves"] == null) 
                                  {
                                      if ($Casual_Available == 0) 
                                      {
                                          $Annual_Available;
                                          $Casual_Available;
                                          $GetAnnual = 0;
                                          $GetCasual = 0;
                                      }
                                      else
                                      {
                                          $Casual_Available = $Casual_Available - $Taken;
                                          $Annual_Available = $Annual_Available - $Taken;
                                          $GetAnnual = 0;
                                          $GetCasual = 0;                
                                      }   
                                  }
                                  else
                                  {   

                                      $Total += $result["totalLeaves"];

                                      if ($Total >= $casual_leaves)
                                      {
                                          if ($Casual_Available == 0)
                                          {
                                              $GetAnnual = $Taken;
                                              $Annual_Available = $annual_leaves - ($Total - $casual_leaves);
                                                  
                                              if ($GetAnnual <= 0) {
                                                $GetAnnual = 0;
                                              }

                                              if ($Annual_Available <= 0) {
                                                $Annual_Available = 0;
                                              }
                                                 
                                                  $Casual_Available;
                                                  $GetCasual = 0;
                                          }
                                          else
                                          {
                                              $GetCasual = $Casual_Available;
                                              $Other_Available = $Casual_Available - $Taken;
                                              $GetAnnual = $Other_Available * (-1);

                                              if ($GetAnnual == -0) {
                                                $GetAnnual = 0;
                                              }

                                              if ($Casual_Available < 0) 
                                              {
                                                  $Annual_Available = $annual_leaves - $GetAnnual;
                                                  $Casual_Available = 0;
                                              }
                                              else
                                              {
                                                  $Annual_Available = $annual_leaves - $GetAnnual;
                                                  $Casual_Available = 0;
                                              }

                                          }
                                              
                                      }
                                      else
                                      {  
                                         $Casual_Available = $casual_leaves - $Total;
                                         $GetCasual = $Taken;

                                          if ($GetCasual <= 0) {
                                              $GetCasual = 0;
                                          }

                                          if ($Casual_Available <= 0) {
                                              $Casual_Available = 0;
                                          }
                                             
                                          $Annual_Available = $annual_leaves;
                                          $GetAnnual = 0;
                                      }

                                  }
      
                                  echo "<td align='center' style='background-color: #ccbefa; color: black;'>".$GetAnnual . "</td>";
                                  echo "<td align='center' style='background-color: #ccbefa; color: black;'>".$Annual_Available . "</td>";
                                  echo "<td align='center' style='background-color: #ccbefa; color: black;'>".$annual_leaves."</td>";
                                  echo "<td align='center' style='background-color: #ccbefa; color: black;'>".$GetCasual . "</td>";
                                  echo "<td align='center' style='background-color: #ccbefa; color: black;'>".$Casual_Available . "</td>";
                                  echo "<td align='center' style='background-color: #ccbefa; color: black;'>".$casual_leaves."</td>";
                                  echo "<td align='center' style='background-color: #85d6a9; color: black;'>0</td>";
                                  echo "<td align='center' style='background-color: #85d6a9; color: black;'>0</td>";
                                  echo "<td align='center' style='background-color: #85d6a9; color: black;'>0</td>";
                                  echo "<td align='center' style='background-color: #85d6a9; color: black;'>0</td>";
                                  echo "<td align='center' style='background-color: #85d6a9; color: black;'>0</td>";
                                  echo "<td align='center' style='background-color: #85d6a9; color: black;'>0</td>";

                              }

                            }
                    
                         }
                         else
                         {
                              $query = "select sum(days) as totalLeaves from employee_leave where uid = '" . $resultalx["uid"] . "' AND YEAR(date) = '".$_GET["year"]."' AND MONTH(date) = '".$i."' and type != 'Short Morning Leave' and type != 'Short Evening Leave' and type != 'Duty Full Day Leave' and type != 'Duty Morning Leave' and type != 'Duty Evening Leave' and type != 'Maternity Leave' and type != 'Parental Leave' and type != 'Liue Leave' and type != 'Nopay Full Day Leave' and type != 'Nopay Morning Leave' and type != 'Nopay Evening Leave'";
                              $res = Search($query);

                              if ($result = mysqli_fetch_assoc($res)) {

                                $Taken = $result["totalLeaves"];

                                if ($result["totalLeaves"] == null) 
                                {
                                    if ($Casual_Available == 0) 
                                    {
                                        $Annual_Available;
                                        $Casual_Available;
                                        $GetAnnual = 0;
                                        $GetCasual = 0;
                                    }
                                    else
                                    {
                                        $Casual_Available = $Casual_Available - $Taken;
                                        $Annual_Available = $Annual_Available - $Taken;
                                        $GetAnnual = 0;
                                        $GetCasual = 0;                
                                    }   
                                }
                                else
                                {   

                                    $Total += $result["totalLeaves"];

                                    if ($Total >= $casual_leaves)
                                    {
                                        if ($Casual_Available == 0)
                                        {
                                            $GetAnnual = $Taken;
                                            $Annual_Available = $annual_leaves - ($Total - $casual_leaves);
                                                
                                            if ($GetAnnual <= 0) {
                                              $GetAnnual = 0;
                                            }

                                            if ($Annual_Available <= 0) {
                                              $Annual_Available = 0;
                                            }
                                               
                                                $Casual_Available;
                                                $GetCasual = 0;
                                        }
                                        else
                                        {
                                            $GetCasual = $Casual_Available;
                                            $Other_Available = $Casual_Available - $Taken;
                                            $GetAnnual = $Other_Available * (-1);

                                            if ($GetAnnual == -0) {
                                              $GetAnnual = 0;
                                            }

                                            if ($Casual_Available < 0) 
                                            {
                                                $Annual_Available = $annual_leaves - $GetAnnual;
                                                $Casual_Available = 0;
                                            }
                                            else
                                            {
                                                $Annual_Available = $annual_leaves - $GetAnnual;
                                                $Casual_Available = 0;
                                            }

                                        }
                                            
                                    }
                                    else
                                    {  
                                       $Casual_Available = $casual_leaves - $Total;
                                       $GetCasual = $Taken;

                                        if ($GetCasual <= 0) {
                                            $GetCasual = 0;
                                        }

                                        if ($Casual_Available <= 0) {
                                            $Casual_Available = 0;
                                        }
                                           
                                        $Annual_Available = $annual_leaves;
                                        $GetAnnual = 0;
                                    }

                                }

                              
                                
                                echo "<td align='center' style='background-color: #ccbefa; color: black;'>0</td>";
                                echo "<td align='center' style='background-color: #ccbefa; color: black;'>0</td>";
                                echo "<td align='center' style='background-color: #ccbefa; color: black;'>0</td>";
                                echo "<td align='center' style='background-color: #ccbefa; color: black;'>0</td>";
                                echo "<td align='center' style='background-color: #ccbefa; color: black;'>0</td>";
                                echo "<td align='center' style='background-color: #ccbefa; color: black;'>0</td>";
                                echo "<td align='center' style='background-color: #85d6a9; color: black;'>".$GetAnnual . "</td>";
                                echo "<td align='center' style='background-color: #85d6a9; color: black;'>".$Annual_Available . "</td>";
                                echo "<td align='center' style='background-color: #85d6a9; color: black;'>".$annual_leaves."</td>";
                                echo "<td align='center' style='background-color: #85d6a9; color: black;'>".$GetCasual . "</td>";
                                echo "<td align='center' style='background-color: #85d6a9; color: black;'>".$Casual_Available . "</td>";
                                echo "<td align='center' style='background-color: #85d6a9; color: black;'>".$casual_leaves."</td>";
                                
                         }

                         echo "</tr>";
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
  echo "<input type='hidden' id='hyer' value='".$_REQUEST["year"]."'>";
  echo "<input type='hidden' id='emp_stat' value='".$_REQUEST["emp_status"]."'>";
}
?>

<div id="space"></div>

<?php include("../Contains/footer.php"); ?>

</div>
</div>
</body>
</html>




