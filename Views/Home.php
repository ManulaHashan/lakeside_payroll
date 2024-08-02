<?php
error_reporting(0);
include '../Contains/header.php';
include '../DB/DB.php';
?>
<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Home | Apex Payroll</title>
        <!-- <link rel="shortcut icon" href="../Images/titleLogo.png" /> -->
        <link href="../Styles/contains.css" rel="stylesheet" type="text/css"/>
        <link href="../Vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
        <!-- Favicons -->
        <link rel="apple-touch-icon" sizes="180x180" href="../Images/favicon/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="../Images/favicon/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="../Images/favicon/favicon-16x16.png">
        
        <script src="../JS/jquery-3.1.0.js"></script>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript" src="../JS/clock/jquery-1.2.6.min.js"></script>
        <link href='https://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>

        <!-- Google Font: Source Sans Pro -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="../New_Dashboard_Data/plugins/fontawesome-free/css/all.min.css">
        <!-- Ionicons -->
        <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
        <!-- Tempusdominus Bootstrap 4 -->
        <link rel="stylesheet" href="../New_Dashboard_Data/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
        <!-- iCheck -->
        <link rel="stylesheet" href="../New_Dashboard_Data/plugins/icheck-bootstrap/icheck-bootstrap.min.css">
        <!-- JQVMap -->
        <link rel="stylesheet" href="../New_Dashboard_Data/plugins/jqvmap/jqvmap.min.css">
        <!-- Theme style -->
        <link rel="stylesheet" href="../New_Dashboard_Data/dist/css/adminlte.min.css">
        <!-- overlayScrollbars -->
        <link rel="stylesheet" href="../New_Dashboard_Data/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
        <!-- Daterange picker -->
        <link rel="stylesheet" href="../New_Dashboard_Data/plugins/daterangepicker/daterangepicker.css">
        <!-- summernote -->
        <link rel="stylesheet" href="../New_Dashboard_Data/plugins/summernote/summernote-bs4.min.css">

        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>

        <script type="text/javascript">
        
        window.onload = function() {
                
                document.getElementById("year").value = new Date().getFullYear();
                document.getElementById("month").value = new Date().getMonth() + 1;
                CountUsers();
                monthAttChart();
                monthAttChartDATA();
                dailyAbsentChartDATA();
                getBdayDetails();
                setSpace();
  
            };

            function setSpace() {
                var wheight = $(window).height();
                var bheight = $('#body').height();
                if (wheight > bheight) {
                    var x = wheight - bheight - 30;
                    $('#space').height(x);
                }
            }


            function CountUsers()
            {
              var dept = document.getElementById('dept').value;

                $.ajax({
                    type: 'POST',
                    url: "../Controller/dashboard.php?request=empcount&dept_data=" + dept,
                    success: function(data) {

                        var arr = data.split("#");

                        $('#total_emp').html(arr[1]);
                        $('#total_admin_emp').html(arr[2]);
                        $('#total_manage_emp').html(arr[3]);
                        $('#total_mo_emp').html(arr[4]);

                    }

                });
            }

            function getBdayDetails()
            {
                var Month_data = document.getElementById('month').value;
                var dept = document.getElementById('dept').value;

                $.ajax({
                        type: 'POST',
                        url: "../Controller/dashboard.php?request=bdaydetails&Month_data=" + Month_data + "&dept_data=" + dept,
                        success: function(data) {

                            $('#birthday').html(data);
                        }

                });
            }

            function monthAttChart()
            {
                var Year_data = document.getElementById('year').value;
                var dept = document.getElementById('dept').value;
             
                var url = "../Controller/dashboard.php?request=getmonthlyattcount&Year_data=" + Year_data +"&dept="+dept;

                $.ajax({
                    type: 'POST',
                    url: url,
                    success: function(data) {
                       
                        var arr = data.split("#");

                        var xValues = ["January", "February", "March", "April", "May", "June", "July", "Auguest", "September", "October", "November", "December"];

                        var yValues = [arr[0],arr[1],arr[2],arr[3],arr[4],arr[5],arr[6],arr[7],arr[8],arr[9],arr[10],arr[11]];
                        var barColors = [
                          "#b91d47",
                          "#00aba9",
                          "#2b5797",
                          "#e8c3b9",
                          "#1e7145",
                          "#ffcc00",
                          "#3333ff",
                          "#ff0000",
                          "#cc3399",
                          "#996633",
                          "#669999",
                          "#99cc00"
                        ];

                        new Chart("myChart", {
                          type: "doughnut",
                          data: {
                            labels: xValues,
                            datasets: [{
                              backgroundColor: barColors,
                              data: yValues
                            }]
                          },
                          options: {
                            title: {
                              display: true,
                              text: "Monthly Attendance Count In "+Year_data
                            }
                          }
                        });


                        
                    }
                });   
            }

            function monthAttChartDATA()
            {
                var Year_data = document.getElementById('year').value;
                var Month_data = document.getElementById('month').value;
                var dept = document.getElementById('dept').value;

                var url = "../Controller/dashboard.php?request=dailyLateCount&Year_data=" + Year_data +"&Month_data="+Month_data +"&dept="+dept;

                $.ajax({
                    type: 'POST',
                    url: url,
                    success: function(data) {
                       
                        var arrM = data.split("#");

                        var arrF = data.split("@");

                        var areaChartData = {
                        labels  : ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31'],
                        datasets: [
                          {
                            label               : 'Male',
                            backgroundColor     : 'rgba(60,141,188,0.9)',
                            borderColor         : 'rgba(60,141,188,0.8)',
                            pointRadius          : false,
                            pointColor          : '#3b8bba',
                            pointStrokeColor    : 'rgba(60,141,188,1)',
                            pointHighlightFill  : '#fff',
                            pointHighlightStroke: 'rgba(60,141,188,1)',
                            data                : [arrM[0],arrM[1],arrM[2],arrM[3],arrM[4],arrM[5],arrM[6],arrM[7],arrM[8],arrM[9],arrM[10],arrM[11],arrM[12],arrM[13],arrM[14],arrM[15],arrM[16],arrM[17],arrM[18],arrM[19],arrM[20],arrM[21],arrM[22],arrM[23],arrM[24],arrM[25],arrM[26],arrM[27],arrM[28],arrM[29],arrM[30]]
                          },
                          {
                            label               : 'Female',
                            backgroundColor     : 'rgba(210, 214, 222, 1)',
                            borderColor         : 'rgba(210, 214, 222, 1)',
                            pointRadius         : false,
                            pointColor          : 'rgba(210, 214, 222, 1)',
                            pointStrokeColor    : '#c1c7d1',
                            pointHighlightFill  : '#fff',
                            pointHighlightStroke: 'rgba(220,220,220,1)',
                            data                : [arrF[0],arrF[1],arrF[2],arrF[3],arrF[4],arrF[5],arrF[6],arrF[7],arrF[8],arrF[9],arrF[10],arrF[11],arrF[12],arrF[13],arrF[14],arrF[15],arrF[16],arrF[17],arrF[18],arrF[19],arrF[20],arrF[21],arrF[22],arrF[23],arrF[24],arrF[25],arrF[26],arrF[27],arrF[28],arrF[29],arrF[30]]
                          },
                        ]
                      }

                      var barChartCanvas = $('#barChart').get(0).getContext('2d')
                      var barChartData = $.extend(true, {}, areaChartData)
                      var temp0 = areaChartData.datasets[0]
                      var temp1 = areaChartData.datasets[1]
                      barChartData.datasets[0] = temp1
                      barChartData.datasets[1] = temp0

                      var barChartOptions = {
                        responsive              : true,
                        maintainAspectRatio     : false,
                        datasetFill             : false,

                        scales: {
                          xAxes: [{
                            scaleLabel: {
                              display: true,
                              labelString: 'Day'
                            },
                            gridLines: {
                                display: true
                            }
                          }],
                          yAxes: [{
                            scaleLabel: {
                              display: true,
                              labelString: 'Employee Count'
                            },
                            ticks: {
                                stepSize: 1
                            },
                            gridLines: {
                                display: true
                            }
                          }]
                        }
                      }

                      new Chart(barChartCanvas, {
                        type: 'bar',
                        data: barChartData,
                        options: barChartOptions
                      })


                        
                    }
                });
   
            }

            function dailyAbsentChartDATA()
            {
                var Year_data = document.getElementById('year').value;
                var Month_data = document.getElementById('month').value;
                var dept = document.getElementById('dept').value;

                var url = "../Controller/dashboard.php?request=dailyAbsentCount&Year_data=" + Year_data +"&Month_data="+Month_data +"&dept="+dept;

                $.ajax({
                    type: 'POST',
                    url: url,
                    success: function(data) {
                       
                      var arrM = data.split("#");
                      var arrF = data.split("@");

                      // var areaChartCanvas = $('#areaChart').get(0).getContext('2d')

                      var areaChartData = {
                        labels  : ['01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31'],
                        datasets: [
                          {
                            label               : 'Male',
                            backgroundColor     : '#b91d47',
                            borderColor         : '#b91d47',
                            pointRadius          : false,
                            pointColor          : '#b91d47',
                            pointStrokeColor    : '#b91d47',
                            pointHighlightFill  : '#fff',
                            pointHighlightStroke: '#b91d47',
                            data                : [arrM[0],arrM[1],arrM[2],arrM[3],arrM[4],arrM[5],arrM[6],arrM[7],arrM[8],arrM[9],arrM[10],arrM[11],arrM[12],arrM[13],arrM[14],arrM[15],arrM[16],arrM[17],arrM[18],arrM[19],arrM[20],arrM[21],arrM[22],arrM[23],arrM[24],arrM[25],arrM[26],arrM[27],arrM[28],arrM[29],arrM[30]]
                          },
                          {
                            label               : 'Female',
                            backgroundColor     : '#1e7145',
                            borderColor         : '#1e7145',
                            pointRadius         : false,
                            pointColor          : '#1e7145',
                            pointStrokeColor    : '#1e7145',
                            pointHighlightFill  : '#fff',
                            pointHighlightStroke: '#1e7145',
                            data                : [arrF[0],arrF[1],arrF[2],arrF[3],arrF[4],arrF[5],arrF[6],arrF[7],arrF[8],arrF[9],arrF[10],arrF[11],arrF[12],arrF[13],arrF[14],arrF[15],arrF[16],arrF[17],arrF[18],arrF[19],arrF[20],arrF[21],arrF[22],arrF[23],arrF[24],arrF[25],arrF[26],arrF[27],arrF[28],arrF[29],arrF[30]]
                          },
                        ]
                      }

                      var barChartCanvas = $('#areaChart').get(0).getContext('2d')
                      var barChartData = $.extend(true, {}, areaChartData)
                      var temp0 = areaChartData.datasets[0]
                      var temp1 = areaChartData.datasets[1]
                      barChartData.datasets[0] = temp1
                      barChartData.datasets[1] = temp0

                      var barChartOptions = {
                        responsive              : true,
                        maintainAspectRatio     : false,
                        datasetFill             : false,

                        scales: {
                          xAxes: [{
                            scaleLabel: {
                              display: true,
                              labelString: 'Day'
                            },
                            gridLines: {
                                display: true
                            }
                          }],
                          yAxes: [{
                            scaleLabel: {
                              display: true,
                              labelString: 'Employee Count'
                            },
                            ticks: {
                                stepSize: 1
                            },
                            gridLines: {
                                display: true
                            }
                          }]
                        }
                      }

                      new Chart(barChartCanvas, {
                        type: 'bar',
                        data: barChartData,
                        options: barChartOptions
                      })
   
                    }
                });   
            }

        </script>
    </head>

    <body id="body" class="hold-transition sidebar-mini layout-fixed">
        <?php include("../Contains/titlebar_dboard.php"); ?>
        <div class="wrapper">

  <!-- Preloader -->
  <div class="preloader flex-column justify-content-center align-items-center">
    <img class="animation__shake" src="../Images/appex_logo.png" alt="AdminLTELogo" height="60" width="60">
  </div>

  <!-- Content Wrapper. Contains page content -->
  <div class="wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0">Dashboard</h1>
          </div><!-- /.col -->
          <div class="col-sm-6"> 
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Dashboard</li>
            </ol>
            
              <div class="row">
                <!-- Date -->
                  <div class="form-group">
                    <label>Year:</label>
                      <select id="year" name="year" class="form-control select2" style="width: 120px">
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
                  </div>&nbsp;&nbsp;

                  <div class="form-group">
                    <label>Month:</label>
                      <select id="month" name="month" class="form-control select2" style="width: 120px">
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
                  </div>&nbsp;&nbsp;

                  <div class="form-group">
                  <label>Branch:</label>
                  <select id="dept" name="dept" class="form-control select2" style="width: 186px">
                  <option value="%">All</option>
                      <?php
                      $query = "select * from position";
                      $res = Search($query);
                      while ($result = mysqli_fetch_assoc($res)) {
                          ?>
                          <option value="<?php echo $result["pid"]; ?>"> <?php echo $result["name"]; ?> </option>
                      <?php } ?>
                  </select>
                </div>&nbsp;&nbsp;

                <div class="form-group">
                  <br>
                  <a class="btn bg-secondary" style="height: 44px;" onclick="CountUsers();monthAttChart();monthAttChartDATA();dailyAbsentChartDATA();getBdayDetails();"><center>
                    <i class="fas fa-search"></i>&nbsp;Search</center></a>
                </div>







                
              </div>
            
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3 id="total_emp"></h3>

                <p>Total Employees</p>
              </div>
              <div class="icon">
                <i class="fa fa-users"></i>
              </div>
              <!-- <a href="../Views/emp_manage.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3 id="total_admin_emp"></h3>

                <p>Male Employees</p>
              </div>
              <div class="icon">
                <i class="fa fa-male"></i>
              </div>
              <!-- <a href="../Views/emp_manage.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3 id="total_manage_emp"></h3>

                <p>Female Employees</p>
              </div>
              <div class="icon">
                <i class="fa fa-female"></i>
              </div>
              <!-- <a href="../Views/emp_manage.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3 id="total_mo_emp"></h3>

                <p>Employees Who Are Absent Today</p>
              </div>
              <div class="icon">
                <i class="fa fa-clock-o"></i>
              </div>
              <!-- <a href="../Views/emp_manage.php" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a> -->
            </div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->
        <!-- Main row -->
        <div class="row">
          <!-- Left col -->
          <section class="col-lg-7 connectedSortable">
            <!-- Custom tabs (Charts with tabs)-->
            <div class="card card-danger">
              <div class="card-header">
                <h3 class="card-title">Daily Late Employees Count</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div class="chart">
                  <canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <!-- solid sales graph -->
            <div class="card card-primary">
              <div class="card-header">
                <h3 class="card-title">Daily Absent Employees Count</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <div class="chart">
                  <canvas id="areaChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </section>
          <!-- /.Left col -->
          <!-- right col (We are only adding the ID to make the widgets sortable)-->
          <section class="col-lg-5 connectedSortable">

            
            <div class="card card-success">
              <div class="card-header">
                <h3 class="card-title">Monthly Attendance Count</h3>

                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <div class="card-body">
                <canvas id="myChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->

            <div class="card card-warning">
              <div class="card-header">
                <h3 class="card-title">Monthly Birthdays List</h3>
              </div>
              <!-- /.card-header -->
              <div class="card-body p-0">
                <div id="birthday" class="table table-striped"></div>
              </div>
              <!-- /.card-body -->
            </div>
            <!-- /.card -->
          </section>
          <!-- right col -->
        </div>
        <!-- /.row (main row) -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
  <?php include("../Contains/footer.php"); ?>
  <!-- Control Sidebar -->
  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->
</div>
        
        
    </body>
</html>


<!-- jQuery -->
<script src="../New_Dashboard_Data/plugins/jquery/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="../New_Dashboard_Data/plugins/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button)
</script>
<!-- Bootstrap 4 -->
<script src="../New_Dashboard_Data/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- ChartJS -->
<script src="../New_Dashboard_Data/plugins/chart.js/Chart.min.js"></script>
<!-- Sparkline -->
<script src="../New_Dashboard_Data/plugins/sparklines/sparkline.js"></script>
<!-- jQuery Knob Chart -->
<script src="../New_Dashboard_Data/plugins/jquery-knob/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="../New_Dashboard_Data/plugins/moment/moment.min.js"></script>
<script src="../New_Dashboard_Data/plugins/daterangepicker/daterangepicker.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="../New_Dashboard_Data/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Summernote -->
<script src="../New_Dashboard_Data/plugins/summernote/summernote-bs4.min.js"></script>
<!-- overlayScrollbars -->
<script src="../New_Dashboard_Data/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
<!-- AdminLTE App -->
<script src="../New_Dashboard_Data/dist/js/adminlte.js"></script>


