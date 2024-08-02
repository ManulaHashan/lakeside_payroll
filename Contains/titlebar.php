<div class="titlebarDiv" style="width:100%">
    <table width="100%">
        <tr valign="middle">
            <td width="1px;" class="" style=""><img src="../Images/appex_a.png" style="width:50px;margin-top:5px;cursor: pointer;" onclick="window.location.href = 'Home.php'"></td>
            <td width="">
                <h2 style="color: silver; margin: 0px;">pex Payroll</h2>
            </td>
            
            <td align="right">
                <?php
                if (isset($_SESSION["uid"])) {
                    $query = "select fname,lname from user where uid = '" . $_SESSION["uid"] . "'";
                    $res = Search($query);
                    if ($result = mysqli_fetch_assoc($res)) {
                        echo "Welcome  " . $result["fname"] . " " . "&nbsp;&nbsp;&nbsp;";
                    } else {
                        echo "Please Login!";
                    }
                }
                ?>
            </td>
            
            <td width="5px">
            <form action="../Controller/logout.php">
                    <input type="submit" style="background-image: url(../Images/logout.png);background-size:100% 100%;background-color: transparent;background-repeat: no-repeat;background-position: 0px 0px;border: none;cursor: pointer;height: 40px;width:30px;vertical-align: middle;" name="logout" value=""/>
            </form></td>
            <td></td>
            <td width="50px">

                <div class="titlebarNaviBtn">
                    <table>
                        <tr onclick="naviLoad()">
                            <td><span>&nbsp;&nbsp;Navigation</span></td>
                            <td><img src="../Icons/NaviImg.png" style="margin:5px;cursor: pointer;"></td>
                        </tr>
                    </table>

                    <script type="text/javascript">
                        function naviDrop(x, y) {
                            var panel = document.getElementById(x);
                            var maxHeight = y;
                            if (panel.style.height === maxHeight) {
                                panel.style.height = "0px";

                            } else {
                                panel.style.height = maxHeight;
                            }
                        }
                        function naviLoad() {
                            if ($('#naviPanel').is(":visible")) {
                                $('#naviPanel').hide('slow');
                            } else {
                                $('#naviPanel').show('fast');
                            }
                        }
                    </script>

                    <style type="text/css">
                        #naviPanel{
                            position: absolute;
                            right: 0.1px;
                            top: 61px;
                            display: none;
                            z-index: 10;
                        }
                        .oppanel_Title {
                            font-family: Constantia, "Lucida Bright", "DejaVu Serif", Georgia, serif;
                            font-size: 16px;
                            color: #ffffff;

                            background: -moz-linear-gradient(top, rgb(8, 8, 8) 0%, rgb(8, 8, 8) 15%, rgb(8, 8, 8) 34%, rgb(10, 5, 5) 71%, rgbrgb(8, 8, 8) 88%, rgb(8, 8, 8) 100%); /* FF3.6-15 */
                            background: -webkit-linear-gradient(top, rgb(8, 8, 8) 0%, rgb(8, 8, 8) 15%, rgb(8, 8, 8) 34%, rgb(0, 0, 0) 71%, rgbrgb(8, 8, 8) 88%, rgb(8, 8, 8) 100%); /* Chrome10-25,Safari5.1-6 */
                            background: linear-gradient(to bottom, rgb(8, 8, 8) 0%,rgb(10, 10, 10) 15%,rgb(0, 0, 0) 34%,rgb(0, 0, 0) 71%,rgb(0, 0, 0) 88%,rgb(0, 0, 0)00%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
                            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#1e5799', endColorstr='#1e5799',GradientType=0 );
                            padding: 10px;
                            text-decoration: none;
                            transition: all 0.2s linear 0s;
                            -webkit-transition: all 0.2s linear 0s;
                            -o-transition: all 0.2s linear 0s;
                        }
                        .oppanel_Title:hover {
                            background-color: #a3caff;
                        }
                        #panelitem, #panelitem2, #panelitem3, #panelitem4, #panelitem5, #panelitem6, #panelitem7, #panelitem8, #panelitem9, #panelitem10, #panelitem11{
                            height: 0px;
                            overflow: hidden;
                            transition: height 0.1s linear 0s;
                            background-color: #ffffff;
                            border-left: 1px;
                            border-left-color: #f5f6f8;
                            border-left-style: solid;
                            border-right: 1px;
                            border-right-color: #f5f6f8;
                            border-right-style: solid;
                            margin-bottom: 5px

                        }  
                        .link{
                            display: block;
                            font-size: 16px;
                            text-decoration: none;
                            color: #2f67b4;
                            background-color:#CCC8C7;
                            width:100%;
                            padding: 5px;
                            margin: 0px;

                            transition: all 0.2s linear 0s;
                            -webkit-transition: all 0.2s linear 0s;
                            -o-transition: all 0.2s linear 0s;
                        }
                        .link:hover {
                            color: white;
                            width:100%;
                            background-color: #CCC8C8;
                        }
                    </style>

                    <?php
                    $privs = array();
                    $query = "select name from features where fid in (select features_fid from privillages where User_uid='" . $_SESSION["uid"] . "')";
                    $res = Search($query);
                    while ($result = mysqli_fetch_assoc($res)) {
                        array_push($privs, $result["name"]);
                    }
                    ?>
                    <div id="naviPanel">
                        <?php if (in_array("Employee Management", $privs)) { ?>

                            <div id="oppanel_button" class="oppanel_Title" onClick="naviDrop('panelitem', '200px')"><img src="../Images/menu-alt-512.png" width="15.5px" height="">&nbsp Employee management</div> 

                            <div id="panelitem" style="background-color: #CCC8C8;"> 
                                <blockquote >
                                    <a class="link" href="../Views/emp_manage.php">Manage Employees</a>
                                    <a class="link" href="../Views/emp_attendance.php">Attendance</a>
                                    <a class="link" href="../Views/emp_sal_deductins.php">Salary Deductions</a>
                                    <a class="link" href="../Views/emp_payroll.php">Payroll Details</a>
                                    <a class="link" href="../Views/emp_reports.php">Salary Report</a>
                                </blockquote>
                            </div> 
                        <?php } ?>

                        

                        <?php if (in_array("System Settings", $privs)) { ?>
                            <div id="oppanel_button" class="oppanel_Title" onClick="naviDrop('panelitem11', '100px')"><img src="../Images/settings.png" width="40" height="40">&nbsp System Settings</div> 

                            <div id="panelitem11"> 
                                <blockquote>
                                    <a class="link" href="../Views/EmailLog.php">Email Log</a>
                             </blockquote>
                            </div> 
                        <?php } ?>
                    </div>
                </div>
            </td>
        </tr>
    </table>
</div>