<?php
error_reporting(0);
session_start();
include '../DB/DB.php';
$DB = new Database();
date_default_timezone_set('Asia/Colombo');

//search 
if (isset($_REQUEST["request"])) {
    $out;
    if ($_REQUEST["request"] == "getEmpsbyID") {
        $query = "select * from user where uid = '" . $_REQUEST["uid"] . "'";
        $res = Search($query);
        if ($result = mysqli_fetch_assoc($res)) {
            $out = implode("#", $result);

            //search allowances
            $outals = "";
            $resal = Search("select a.amount from user_has_allowances a, allowances b where a.alwid = b.alwid and a.uid = '" . $_REQUEST["uid"] . "' order by b.alwid");
            while ($resultal = mysqli_fetch_assoc($resal)) {
                $outals .= $resultal["amount"] . "//";
            }
        } else {
            $out = "usernotfound";
        }
        echo $out . "#" . $outals;
    } else if ($_REQUEST["request"] == "getAddress") {
        $query = "select address from address where aid = '" . $_REQUEST["aid"] . "'";
        $res = Search($query);
        if ($result = mysqli_fetch_assoc($res)) {
            $out = $result["address"];
        }
        echo $out;
    } else if ($_REQUEST["request"] == "getDepartment") {
        $query = "select name from emp_department where did = '" . $_REQUEST["did"] . "'";
        $res = Search($query);
        if ($result = mysqli_fetch_assoc($res)) {
            $out = $result["name"];
        }
        echo $out;
    } else if ($_REQUEST["request"] == "setSession") {

        $_SESSION["exportdata"] = $_POST["data"];

        echo $_SESSION["exportdata"];
    } else if ($_REQUEST["request"] == "getPOSIandGradefromID") {

        $query = "select position_pid,grade_gid from emppost where id = '" . $_REQUEST["id"] . "'";
        $res = Search($query);
        if ($result = mysqli_fetch_assoc($res)) {

            $query1 = "select name as emptype from employeetype where etid = '" . $_REQUEST["emptyid"] . "'";
            $res1 = Search($query1);
            if ($result1 = mysqli_fetch_assoc($res1)) {
                $etypename = $result1["emptype"];
            }

            $query2 = "select name as gname from grade where gid = '" . $result["grade_gid"] . "'";
            $res2 = Search($query2);
            if ($result2 = mysqli_fetch_assoc($res2)) {
                $grade = $result2["gname"];
            }

            $query3 = "select name as pos from position where pid = '" . $result["position_pid"] . "'";
            $res3 = Search($query3);
            if ($result3 = mysqli_fetch_assoc($res3)) {
                $dept = $result3["pos"];
            }

            $query4 = "select name as mar from maritalstatus where idMaritalStatus = '" . $_REQUEST["maritalid"] . "'";
            $res4 = Search($query4);
            if ($result4 = mysqli_fetch_assoc($res4)) {
                $marital = $result4["mar"];
            }

            $out = $grade . "#" . $dept . "#" . $etypename . "#" . $marital;
        }
        echo $out;
    } else if ($_REQUEST["request"] == "getnotification") {

        $out = "<table class='table table-striped'>";

        $query;

        $query = "select * from notification where n_user = '" . $_SESSION["uid"] . "' order by n_date DESC,n_time DESC limit 4";

        $res = Search($query);
        while ($result = mysqli_fetch_assoc($res)) {

            if ($result["n_status"] == "0") {

                $out .= "<tr id='" . $result["nid"] . "' style='background-color:#d6d6c2; cursor: pointer;' onclick='getrowid(id)'>";
                $out .= "<td><img src='../img/pending.png' width='30px'>&nbsp; Your request accepted in " . $result["n_date"] . "</td>";
                $out .= "</tr>";
            } else if ($result["n_status"] == "2") {
                $out .= "<tr id='" . $result["nid"] . "' style='background-color:#d6d6c2; cursor: pointer;' onclick='getrowid(id)'>";
                $out .= "<td><img src='../img/pending.png' width='30px'>&nbsp; Your request declined in " . $result["n_date"] . "</td>";
                $out .= "</tr>";
            } else if ($result["n_status"] == "3") {
                $out .= "<tr style='background-color:#ffffff; cursor: pointer;'>";
                $out .= "<td><img src='../img/reject.png' width='30px'>&nbsp; Your Request declined in " . $result["n_date"] . "</td>";
                $out .= "</tr>";
            } else {
                $out .= "<tr style='background-color:#ffffff; cursor: pointer;'>";
                $out .= "<td><img src='../img/correct.png' width='30px'>&nbsp; Your Request accepted in " . $result["n_date"] . "</td>";
                $out .= "</tr>";
            }
        }

        $out .= "</table>";

        echo $out;
    } else if ($_REQUEST["request"] == "viewnotification") {

        $query = "select * from notification where nid='" . $_REQUEST["notid"] . "'";
        $res = Search($query);
        if ($result = mysqli_fetch_assoc($res)) {

            if ($result["n_status"] == "2") {
                $querys = "Update notification set n_status='3' where nid='" . $_REQUEST["notid"] . "'";

                $ret = SUD($querys);

                if ($ret == 1) {
                    echo "1";
                } else {
                    echo "0";
                }
            } else {
                $querys = "Update notification set n_status='1' where nid='" . $_REQUEST["notid"] . "'";
                $ret = SUD($querys);

                if ($ret == 1) {
                    echo "1";
                } else {
                    echo "0";
                }
            }
        }
        echo $out;
    } else if ($_REQUEST["request"] == "getnotificationcount") {

        $query = "select count(nid) as noticount from notification where n_user = '" . $_SESSION["uid"] . "' and (n_status = '0' or n_status = '2')";
        $res = Search($query);
        if ($result = mysqli_fetch_assoc($res)) {

            $notic = $result["noticount"];
        } else {

            $notic = "0";
        }
        echo $notic;
    } else if ($_REQUEST["request"] == "getleave") {

        $out = "<table class='table table-striped table-bordered'><thead class='thead-dark' style='position : sticky; top : 0;  z-index: 0; background-color: #ffffff;'>
        <tr>
        <th>Leave Request</th>
        <th>Confirm Status</th>
        </tr></thead>";

        $query;

        $query = "select * from emp_leave_request";

        $res = Search($query);
        while ($result = mysqli_fetch_assoc($res)) {

            $out .= "<tr>";

            if ($result["reqstatus"] == "0") {

                $out .= "<td style='background-color: #DAA520; color: black;'><b>Leave Type : </b>" . $result["leave_type"] . " |  <b>Leave are Requested From Date :</b> " . $result["fromdate"] . " - <b>To Date :</b> " . $result["todate"] . "</br><b>Date of Requested for Leave :</b> " . $result["request_date"] . " |  <b>Decision : Pending</b></td>";
            } else {
                $out .= "<td><b>Leave Type : </b>" . $result["leave_type"] . " |  <b>Leave are Requested From Date :</b> " . $result["fromdate"] . " - <b>To Date :</b> " . $result["todate"] . " </br><b>Date of Requested for Leave :</b> " . $result["request_date"] . " |  <b>Decision : Pending</b></td>";
            }

            if ($result["app_status"] == "0") {

                $out .= "<td></td>";
            } else {
                $out .= "<td style='background-color: #47b833; color: black;'><b>Approved Date :</b> " . $result["app_date"] . " |  <b>Approved Time :</b> " . $result["app_time"] . " |  <b>Decision : Confirm</b></td>";
            }

            $out .= "</tr>";
        }

        $out .= "</table>";

        echo $out;
    } else if ($_REQUEST["request"] == "updateuser") {

        $querys = "Update user set email='" . $_REQUEST["email"] . "' where uid = '" . $_REQUEST["id"] . "'";

        $ret = SUD($querys);


        if ($ret == 1) {
            echo "1";
        } else {
            echo "0";
        }

        echo $out;
    }

    //other methods
}


if (isset($_POST['btnupload'])) //update skills files and description
{
    $fileNames = $_FILES['file-input']['name'];

    if (empty($fileNames)) {

        header('Location:  ../Views/emp_profile.php?msg=1');
    } else {
        if (isset($_SESSION["uid"])) {

            $folder_name = $_SESSION["uid"];
            $parth = '/../UserImages/';
            $allowTypes = array('jpg', 'png', 'jpeg');



            if (!is_dir(dirname(__FILE__) . $parth . $folder_name)) {

                mkdir(dirname(__FILE__) . $parth . $folder_name, 0777, true);

                $target_dir = '../UserImages/' . $folder_name . '/';
                $fileName = basename($_FILES['file-input']['name']);
                $target_file = $target_dir . $fileName;

                $FileType = pathinfo($target_file, PATHINFO_EXTENSION);

                if (in_array($FileType, $allowTypes)) {
                    // Check if image file is a actual image or fake image                    
                    if (move_uploaded_file($_FILES["file-input"]["tmp_name"], $target_file)) {

                        $query = "update user set emp_img_URL='" . $target_file . "' where uid = '" . $_SESSION["uid"] . "'";

                        $return = SUD($query);

                        if ($return == "1") {
                            header('Location:  ../Views/emp_profile.php?msg=2');
                        } else {
                            header('Location:  ../Views/emp_profile.php?msg=3');
                        }
                    } else {
                        header('Location:  ../Views/emp_profile.php?msg=3');
                    }
                } else {
                    header('Location:  ../Views/emp_profile.php?msg=4');
                }
            } else {
                $target_dir = '../UserImages/' . $folder_name . '/';
                $fileName = basename($_FILES['file-input']['name']);
                $target_file = $target_dir . $fileName;

                $FileType = pathinfo($target_file, PATHINFO_EXTENSION);

                if (in_array($FileType, $allowTypes)) {
                    // Check if image file is a actual image or fake image                    
                    if (move_uploaded_file($_FILES["file-input"]["tmp_name"], $target_file)) {

                        $query = "update user set emp_img_URL='" . $target_file . "' where uid = '" . $_SESSION["uid"] . "'";

                        $return = SUD($query);

                        if ($return == "1") {
                            header('Location:  ../Views/emp_profile.php?msg=2');
                        } else {
                            header('Location:  ../Views/emp_profile.php?msg=3');
                        }
                    } else {
                        header('Location:  ../Views/emp_profile.php?msg=3');
                    }
                } else {
                    header('Location:  ../Views/emp_profile.php?msg=4');
                }
            }
        }
    }
}
