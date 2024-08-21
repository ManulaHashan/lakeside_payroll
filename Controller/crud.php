<?php
error_reporting(0);
session_start();
include '../DB/DB.php';
session_write_close();
// $DB = new Database();

//*********************************Pure PHP Method Save Data*******************************************

// if ($_POST['savebtn'] == "save") {

//     // echo "Entered Data : " . $_POST['name'] . "##" . $_POST['address'] . "##" . $_POST['nic'] . "##" . $_POST['tel'] . "##" . $_POST['gender'] . "##" . $_POST['status'];

//     if (empty($_POST['name']) || empty($_POST['address']) || empty($_POST['nic']) || empty($_POST['tel'])) {
//         $error = "Location: ../Views/crud.php?message=0";
//     } else {
//         $search_query = "select id from crud where nic = '" . $_POST['nic'] . "' and status = '1'";
//         $search_result = Search($search_query);
//         if ($get_data = mysqli_fetch_assoc($search_result)) {
//             $error = "Location: ../Views/crud.php?message=1";
//         } else {
//             $insert_query = "insert into crud(name, address, nic, telephone, gender, status) values ('" . $_POST['name'] . "','" . $_POST['address'] . "','" . $_POST['nic'] . "','" . $_POST['tel'] . "','" . $_POST['gender'] . "','" . $_POST['status'] . "')";

//             $result = SUD($insert_query);
//             if ($result == "1") {
//                 $error = "Location: ../Views/crud.php?message=2";
//             } else {
//                 $error = "Location: ../Views/crud.php?message=3";
//             }
//         }
//     }
//     header($error);
// }


if ($_POST['updatebtn'] == "update") {
    echo $_POST['updatebtn'];
}
if ($_POST['deletebtn'] == "delete") {
    echo $_POST['deletebtn'];
}




//*********************Java Script Method********************/

if (isset($_REQUEST["request"])) {
    $output = "";
    if ($_REQUEST["request"] == "getAllDetails") {

        $output = "<table class='table table-bordered'>
                   <thead style='position: sticky; top : 0; z-index: 0; background-color: #9eafba; color: black;'>
                   <tr>
                   <th>Name</th>
                   <th>Address</th>
                   <th>NIC</th>
                   <th>Contact No</th>
                   <th>Gender</th>
                   <th>Status</th>
                   </tr></thead><tbody>";

        $search_query = "select * from crud order by name ASC";
        $tbl_data = Search($search_query);
        while ($row = mysqli_fetch_assoc($tbl_data)) {
            //get gender
            if ($row["gender"] == 0) {
                $gender = "Female";
            } else {
                $gender = "Male";
            }

            //get status
            if ($row["status"] == 0) {
                $status = "Not-active";
            } else {
                $status = "Active";
            }

            $output .= "<tr onclick = 'loadSelectedRecord(" . $row["id"] . ");'>";
            $output .= "<td>" . $row["name"] . "</td>";
            $output .= "<td>" . $row["address"] . "</td>";
            $output .= "<td align='center'>" . $row["nic"] . "</td>";
            $output .= "<td align='center'>" . $row["telephone"] . "</td>";
            $output .= "<td align='center'>" . $gender . "</td>";
            $output .= "<td align='center'>" . $status . "</td>";
            $output .= "</tr>";
        }

        $output .= "</tbody></table>";
        echo $output;
    }


    if ($_REQUEST["request"] == "getAllDetailsByUserID") {
        //  echo $_REQUEST["EmployeeID"];

        $search_query = "select * from crud where id='" . $_REQUEST["EmployeeID"] . "'";
        $user_data = Search($search_query);
        while ($row = mysqli_fetch_assoc($user_data)) {
            $output = implode("#", $row);
        }
        echo $output;
    }

    if ($_REQUEST["request"] == "saveUserRecords") {
        //  echo $_REQUEST["EmployeeID"];
        $json_object = json_decode($_REQUEST["userData"], true);

        $Name = $json_object["Name"];
        $Address = $json_object["Address"];
        $NIC = $json_object["NIC"];
        $Tel = $json_object["Telephone"];
        $Gender = $json_object["Gender"];
        $Status = $json_object["Status"];

        // echo "Entered Data : " . $Name . "##" . $Address . "##" . $NIC . "##" . $Tel . "##" . $Gender . "##" . $Status;

        $search_query = "select id from crud where nic = '" . $NIC . "' and status = '1'";
        $search_result = Search($search_query);
        if ($get_data = mysqli_fetch_assoc($search_result)) {
            $error = "0";
        } else {
            $insert_query = "insert into crud(name, address, nic, telephone, gender, status) values ('" . $Name . "','" . $Address . "','" . $NIC . "','" . $Tel . "','" . $Gender . "','" . $Status . "')";

            $result = SUD($insert_query);
            if ($result == "1") {
                $error = "1";
            } else {
                $error = "2";
            }
        }
        echo $error;
    }
}
