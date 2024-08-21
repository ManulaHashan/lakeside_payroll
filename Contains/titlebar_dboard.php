<div class="titlebarDiv" style="width:100%; background: linear-gradient(90deg, rgba(204,204,204,1) 0%, rgba(54,125,148,1) 54%, rgba(31,32,32,1) 74%);">
    <table width="100%" style="font-size: 18px; font-family: Constantia, 'Lucida Bright', 'DejaVu Serif', 'Georgia', 'serif';">
        <tr valign="middle">
            <td width="1px;" class="" style="height: 60px;"><img src="../Images/letter/7.png" style="width:50px;margin-top:5px;cursor: pointer;" onclick="window.location.href = 'Home.php'"></td>
            <td width="">
                <h2 style="color: white; margin: 0px; font-size: 28px;">pex Payroll | Lakeside Adventist Hospital</h2>
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
                    <input type="submit" style="background-image: url(../Images/logout.png);background-size:100% 100%;background-color: transparent;background-repeat: no-repeat;background-position: 0px 0px;border: none;cursor: pointer;height: 40px;width:30px;vertical-align: middle;" name="logout" value="" />
                </form>
            </td>
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
                        #naviPanel {
                            position: absolute;
                            right: 0.1px;
                            top: 61px;
                            display: none;
                            z-index: 10;
                        }

                        .oppanel_Title {
                            font-family: "Palatino Linotype", "Book Antiqua", Palatino, serif;
                            font-size: 16px;
                            color: #ffffff;

                            background: -moz-linear-gradient(top, rgb(8, 8, 8) 0%, rgb(8, 8, 8) 15%, rgb(8, 8, 8) 34%, rgb(10, 5, 5) 71%, rgbrgb(8, 8, 8) 88%, rgb(8, 8, 8) 100%);
                            /* FF3.6-15 */
                            background: -webkit-linear-gradient(top, rgb(8, 8, 8) 0%, rgb(8, 8, 8) 15%, rgb(8, 8, 8) 34%, rgb(0, 0, 0) 71%, rgbrgb(8, 8, 8) 88%, rgb(8, 8, 8) 100%);
                            /* Chrome10-25,Safari5.1-6 */
                            background: linear-gradient(to bottom, rgb(8, 8, 8) 0%, rgb(10, 10, 10) 15%, rgb(0, 0, 0) 34%, rgb(0, 0, 0) 71%, rgb(0, 0, 0) 88%, rgb(0, 0, 0)00%);
                            /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
                            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#1e5799', endColorstr='#1e5799', GradientType=0);
                            padding: 10px;
                            text-decoration: none;
                            transition: all 0.2s linear 0s;
                            -webkit-transition: all 0.2s linear 0s;
                            -o-transition: all 0.2s linear 0s;
                        }

                        .oppanel_Title:hover {
                            background-color: #a3caff;
                        }

                        #panelitem,
                        #panelitem2,
                        #panelitem3,
                        #panelitem4,
                        #panelitem5,
                        #panelitem6,
                        #panelitem7,
                        #panelitem8,
                        #panelitem9,
                        #panelitem10,
                        #panelitem11 {
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

                        .link {
                            display: block;
                            font-size: 16px;
                            text-decoration: none;
                            color: #2f67b4;
                            background-color: #CCC8C7;
                            width: 100%;
                            padding: 5px;
                            margin: 0px;

                            transition: all 0.2s linear 0s;
                            -webkit-transition: all 0.2s linear 0s;
                            -o-transition: all 0.2s linear 0s;
                        }

                        .link:hover {
                            color: white;
                            width: 100%;
                            background-color: #CCC8C8;
                        }

                        .notification {
                            background-color: #555;
                            color: white;
                            text-decoration: none;
                            padding: 15px 26px;
                            position: relative;
                            display: inline-block;
                            border-radius: 2px;
                        }

                        .notification:hover {
                            background: red;
                        }

                        .notification .badge {
                            position: absolute;
                            top: -10px;
                            right: -10px;
                            padding: 5px 10px;
                            border-radius: 50%;
                            background-color: red;
                            color: white;
                        }
                    </style>

                    <script type="text/javascript">
                        function LeaveCount() {
                            var url = "../Controller/login.php?request=leavecount";

                            $.ajax({
                                type: 'POST',
                                url: url,
                                success: function(data) {
                                    $('#lcount').html(data);
                                }
                            });
                        }
                    </script>

                    <?php
                    $privs = array();
                    $query = "select b.name from profile_wise_privileges a left join user c on a.prof_id = c.priv_typ, features b where a.priv_id = b.fid and c.uid = '" . $_SESSION["uid"] . "' and b.isactive='1'";
                    $res = Search($query);
                    while ($result = mysqli_fetch_assoc($res)) {
                        array_push($privs, $result["name"]);
                    }

                    ?>
                    <div id="naviPanel">

                        <div id="oppanel_button" class="oppanel_Title"><a href="../Views/Home.php" style="color:white;"><img src="../Images/dashboard.png" width="35px">&nbsp Dashboard</a>
                        </div>
                        <div id="panelitem" style="background-color: #CCC8C8;"></div>

                        <?php if (in_array("Employee Management (Main Privilege)", $privs)) { ?>

                            <div id="oppanel_button" class="oppanel_Title" onClick="naviDrop('panelitem2', '200px'); LeaveCount();"><img src="../Images/employee.png" width="35px">&nbsp Employee Management</div>

                            <div id="panelitem2" style="background-color: #CCC8C8;">
                                <blockquote style="border: none; background-color: #CCC8C8; padding-top: 0px; padding-left: 10px; margin:0;">
                                    <br>
                                    <?php if (in_array("Add Employee (Sub Privilege)", $privs) || in_array("Approve Details Change Request (Sub Privilege)", $privs)) { ?>
                                        <small style="font-weight: bold; color: gray;"><u>Manage Employees</u></small>
                                    <?php } ?>

                                    <?php if (in_array("Add Employee (Sub Privilege)", $privs)) { ?>
                                        <a class="link" href="../Views/emp_manage.php">* Add Employee</a>
                                    <?php } ?>

                                    <?php if (in_array("Add Employee (Sub Privilege)", $privs)) { ?>
                                        <a class="link" href="../Views/crud.php">* CRUD</a>
                                    <?php } ?>

                                    <?php
                                    /* if (in_array("Approve Details Change Request (Sub Privilege)", $privs)) { ?>
                                <a class="link" href="../Views/approve_change_req.php">* Approve Details Change Request
                                </a>
                                <?php } */ ?>


                                    <?php if (in_array("View Employee Details Report (Sub Privilege)", $privs)) { ?>
                                        <small style="font-weight: bold; color: gray;"><u>Reports</u></small>
                                        <a class="link" href="../Views/emp_detail_report.php">* View Employee Details</a>
                                    <?php } ?>
                                </blockquote>
                            </div>
                        <?php } ?>


                        <?php if (in_array("Attendance and Leave Management (Main Privilege)", $privs)) { ?>

                            <div id="oppanel_button" class="oppanel_Title" onClick="naviDrop('panelitem3', '420px'); LeaveCount();"><img src="../Images/attendance.png" width="35px">&nbsp Attendance & Leave</div>

                            <div id="panelitem3" style="background-color: #CCC8C8;">
                                <blockquote style="border: none; background-color: #CCC8C8; padding-top: 0px; padding-left: 10px; margin:0;">
                                    <br>
                                    <?php if (in_array("Manage Attendance (Sub Privilege)", $privs) || in_array("Add Roster Profiles (Sub Privilege)", $privs) || in_array("Handle Roster Plans (Sub Privilege)", $privs) || in_array("Approve Leave (Sub Privilege)", $privs)) { ?>
                                        <small style="font-weight: bold; color: gray;"><u>Manage Attendance & Leave</u></small>
                                    <?php } ?>

                                    <?php if (in_array("Manage Attendance (Sub Privilege)", $privs)) { ?>
                                        <a class="link" href="../Views/emp_attendance.php">* Manage Attendance</a>
                                    <?php } ?>

                                    <?php if (in_array("Add Roster Profiles (Sub Privilege)", $privs)) { ?>
                                        <a class="link" href="../Views/emp_shift_profile.php">* Add Roster Profiles</a>
                                    <?php } ?>

                                    <?php if (in_array("Handle Roster Plans (Sub Privilege)", $privs)) { ?>
                                        <a class="link" href="../Views/emp_shift_handle.php">* Handle Roster Plans</a>
                                    <?php } ?>

                                    <?php if (in_array("Approve Leave (Sub Privilege)", $privs)) { ?>
                                        <a class="link" href="../Views/approve_author.php">* Approve Leave</a>
                                    <?php } ?>

                                    <!-- <a class="link" href="../Views/emp_approve_leave.php" class="notification"><span>* Handle Leave Request</span>&nbsp;<span class="badge" style="background-color: red; color: white; border-radius: 50%;" id="lcount"></span></a> -->
                                    <br>


                                    <?php if (in_array("Leave Report (Sub Privilege)", $privs) || in_array("Late Report (Sub Privilege)", $privs) || in_array("Month Wise Absent Report (Sub Privilege)", $privs) || in_array("Leave Balance Report (Sub Privilege)", $privs) || in_array("Roster Report (Sub Privilege)", $privs)) { ?>
                                        <small style="font-weight: bold; color: gray;"><u>Reports</u></small>
                                    <?php } ?>

                                    <?php if (in_array("Leave Report (Sub Privilege)", $privs)) { ?>
                                        <a class="link" href="../Views/leave_report.php">* Leave Report</a>
                                    <?php } ?>

                                    <?php if (in_array("Late Report (Sub Privilege)", $privs)) { ?>
                                        <a class="link" href="../Views/late_report.php">* Late Report</a>
                                    <?php } ?>

                                    <?php if (in_array("Month Wise Absent Report (Sub Privilege)", $privs)) { ?>
                                        <a class="link" href="../Views/month_wise_absent_report.php">* Month Wise Absent
                                            Report</a>
                                    <?php } ?>

                                    <?php if (in_array("Leave Balance Report (Sub Privilege)", $privs)) { ?>
                                        <a class="link" href="../Views/leave_bal_report.php">* Leave Balance Report</a>
                                    <?php } ?>

                                    <?php if (in_array("Roster Report (Sub Privilege)", $privs)) { ?>
                                        <a class="link" href="../Views/emp_roster_report.php">* Roster Report</a>
                                    <?php } ?>

                                </blockquote>
                            </div>
                        <?php } ?>



                        <?php if (in_array("Payroll Management (Main Privilege)", $privs)) { ?>

                            <div id="oppanel_button" class="oppanel_Title" onClick="naviDrop('panelitem4', '430px'); LeaveCount();"><img src="../Images/salary.png" width="35px">&nbsp Payroll Management</div>

                            <div id="panelitem4" style="background-color: #CCC8C8;">
                                <blockquote style="border: none; background-color: #CCC8C8; padding-top: 0px; padding-left: 10px; margin:0;">
                                    <br>
                                    <?php if (in_array("Generate Salary (Sub Privilege)", $privs)) { ?>
                                        <small style="font-weight: bold; color: gray;"><u>Manage Payroll</u></small>
                                        <a class="link" href="../Views/emp_payroll.php">* Generate Salary</a>
                                    <?php } ?>

                                    <?php if (in_array("Loan and Deductions (Sub Privilege)", $privs)) { ?>
                                        <small style="font-weight: bold; color: gray;"><u>Manage Deductions & Loans</u></small>
                                        <a class="link" href="../Views/emp_sal_deductins.php">* Loan & Deductions</a>
                                    <?php } ?>

                                    <br>

                                    <?php if (in_array("Salary Report (Sub Privilege)", $privs) || in_array("EPF Report (Sub Privilege)", $privs) || in_array("ETF Report (Sub Privilege)", $privs) || in_array("ETF 6 Month Report (Sub Privilege)", $privs) || in_array("Salary Advance Report (Sub Privilege)", $privs) || in_array("Loan Report (Sub Privilege)", $privs)) { ?>
                                        <small style="font-weight: bold; color: gray;"><u>Reports</u></small>
                                    <?php } ?>

                                    <?php if (in_array("Salary Report (Sub Privilege)", $privs)) { ?>
                                        <a class="link" href="../Views/emp_reports.php">* Salary Report</a>
                                    <?php } ?>

                                    <?php if (in_array("EPF Report (Sub Privilege)", $privs)) { ?>
                                        <a class="link" href="../Views/emp_epf.php">* EPF Report</a>
                                    <?php } ?>

                                    <?php if (in_array("ETF Report (Sub Privilege)", $privs)) { ?>
                                        <a class="link" href="../Views/emp_etf.php">* ETF Report</a>
                                    <?php } ?>

                                    <?php if (in_array("ETF 6 Month Report (Sub Privilege)", $privs)) { ?>
                                        <a class="link" href="../Views/emp_etf_six_month.php">* ETF 6 Month Report</a>
                                    <?php } ?>

                                    <?php if (in_array("Salary Advance Report (Sub Privilege)", $privs)) { ?>
                                        <a class="link" href="../Views/emp_salary_advance.php">* Salary Advance Report</a>
                                    <?php } ?>

                                    <?php if (in_array("Loan Report (Sub Privilege)", $privs)) { ?>
                                        <a class="link" href="../Views/emp_loan.php">* Loan Report</a>
                                    <?php } ?>



                                    <!-- <a class="link" href="../Views/emp_bank_transfer.php">* Bank Transfer Report</a>
                                     <a class="link" href="../Views/emp_cash_payment.php">* Cash Payment Report</a> -->
                                    <!-- <a class="link" href="../Views/over_ot_summery.php">* Excess Payment Report</a>
                                     <a class="link" href="../Views/total_salary_summery.php">* Salary Summery Report</a>
                                     <a class="link" href="../Views/previouseOtDot.php">* OT & DOT Before Pay Salary Report</a>
                                     <a class="link" href="../Views/month_wise_salary_avg_report.php">* Month Wise Salary Average Report</a> -->
                                </blockquote>
                            </div>
                        <?php } ?>

                        <!-- Payroll System Settings -->
                        <?php if (in_array("Payroll System Settings (Main Privilege)", $privs)) { ?>
                            <div id="oppanel_button" class="oppanel_Title" onClick="naviDrop('panelitem5', '100px')"><img src="../Images/settings.png" width="35px">&nbsp System Settings</div>

                            <div id="panelitem5" style="background-color: #CCC8C8;">
                                <blockquote style="border: none; background-color: #CCC8C8; padding-top: 0px; padding-left: 10px; margin:0;">
                                    <br>
                                    <small style="font-weight: bold; color: gray;"><u>Manage Privilege Profiles</u></small>
                                    <a class="link" href="../Views/priv_settings.php">* Manage Privileges</a>

                                    <!-- <a class="link" href="../Views/EmailLog.php">Email Log</a>
                                    <small style="font-weight: bold; color: gray;"><u>Manage Working Time</u></small>
                                    <a class="link" href="../Views/payroll_system_settings.php">* Set Working Times</a> -->
                                </blockquote>
                                <!-- </div>  -->
                            <?php } ?>
                            </div>
                    </div>
            </td>
        </tr>
    </table>
</div>