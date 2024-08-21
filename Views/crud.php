<?php
include("../Contains/header.php");
include '../DB/DB.php';

$privs = array();
$query = "select b.name from profile_wise_privileges a left join user c on a.prof_id = c.priv_typ, features b where a.priv_id = b.fid and c.uid = '" . $_SESSION["uid"] . "' and b.isactive='1'";
$res = Search($query);
while ($result = mysqli_fetch_assoc($res)) {
    array_push($privs, $result["name"]);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Employee Management | Apex Payroll</title>
    <link href="../Styles/Stylie.css" rel="stylesheet" type="text/css">
    <link href="../Styles/contains.css" rel="stylesheet" type="text/css">
    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="../Images/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../Images/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../Images/favicon/favicon-16x16.png">

    <script src="../JS/jquery-3.1.0.js"></script>
    <script src="../JS/photobooth_min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
    <link href="../Vendor/css/sweet-alert.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/nprogress/nprogress.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/animate.css/animate.min.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/css/custom.min.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/iCheck/skins/flat/green.css" rel="stylesheet" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../JS/sweetalert2.js"></script>
    <script>
        window.onload = function() {
            loadRecordTable();
        }

        function sweetalert(type, title, message) {
            Swal.fire({
                icon: type,
                title: title,
                text: message,
            })
        }

        function loadRecordTable() {
            var request_url = "../Controller/crud.php?request=getAllDetails";
            $.ajax({
                type: "GET",
                url: request_url,
                success: function(tbl_records) {
                    // alert(tbl_records);
                    $('#record_tbl').html(tbl_records);
                }
            });
        }

        function loadSelectedRecord(userID) {
            // alert(userID);

            var request_url = "../Controller/crud.php?request=getAllDetailsByUserID&EmployeeID=" + userID;
            $.ajax({
                type: "GET",
                url: request_url,
                success: function(user_records) {
                    // alert(user_records);
                    // id, name, address, nic, telephone, status, gender
                    var data_array = user_records.split("#");
                    $('#userid').val(data_array[0]);
                    $('#name').val(data_array[1]);
                    $('#address').val(data_array[2]);
                    $('#nic').val(data_array[3]);
                    $('#tel').val(data_array[4]);
                    $('#gender').val(data_array[5]);
                    $('#status').val(data_array[6]);
                }
            });
        }

        function saveRecords() {
            var name = $('#name').val();
            var address = $('#address').val();
            var nic = $('#nic').val();
            var tel = $('#tel').val();
            var gender = $('#gender').val();
            var status = $('#status').val();

            if (name == "" || address == "" || nic == "" || tel == "") {
                sweetalert("warning", "Warning", "Please fill all records!");
            } else {
                var data_record = {
                    "Name": name,
                    "Address": address,
                    "NIC": nic,
                    "Telephone": tel,
                    "Gender": gender,
                    "Status": status,
                }

                var json_data = JSON.stringify(data_record);

                $.ajax({
                    type: "POST",
                    url: "../Controller/crud.php?request=saveUserRecords&userData=" + json_data,
                    success: function(response) {
                        if (response == "1") {
                            sweetalert("success", "Success", "Data saved successful!");
                            clearDataFields();
                            loadRecordTable();
                        } else if (response == "2") {
                            sweetalert("error", "Error", "Data saved unsuccessful!");
                        } else {
                            sweetalert("warning", "Warning", "Data already exist!");
                        }
                    }
                });

            }
        }

        function clearDataFields() {
            $('#name').val("");
            $('#address').val("");
            $('#nic').val("");
            $('#tel').val("");
            $('#gender').val(1);
            $('#status').val(1);
        }
    </script>




</head>

<body id="body" class="nav-md" style="background-color: white;">
    <?php include("../Contains/titlebar_dboard.php"); ?>
    <div class="container body">
        <div class="main_container">
            <!-- page content -->
            <div class="" style="width: 100%; margin: 1%;" role="main">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name">
                    </div>
                    <div class="col-md-6">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address">
                    </div>
                    <div class="col-md-6">
                        <label for="nic" class="form-label">NIC</label>
                        <input type="text" class="form-control" id="nic" name="nic">
                    </div>
                    <div class="col-md-6">
                        <label for="tel" class="form-label">Telephone</label>
                        <input type="text" class="form-control" id="tel" name="tel">
                    </div>
                    <div class="col-md-4">
                        <label for="gender" class="form-label">Gender</label>
                        <select id="gender" name="gender" class="form-select">
                            <option selected value="1">Male</option>
                            <option value="0">Female</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option selected value="1">Active</option>
                            <option value="0">Not Active</option>
                        </select>
                    </div>

                    <input type="hidden" id="userid" name="userid">

                    <div class="col-12">
                        <button type="submit" class="btn btn-primary" name="savebtn" id="savebtn" onclick="saveRecords()">Save</button>
                        <button type="submit" class="btn btn-primary" name="updatebtn" id="updatebtn">Update</button>
                        <button type="submit" class="btn btn-primary" name="deletebtn" id="deletebtn">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <hr>
        <div style="height:500px; overflow-y: scroll;" id="record_tbl"></div>



        <?php

        if (isset($_GET['message'])) {
            if ($_GET['message'] == "0") { ?>
                <script>
                    alert("Please fill all details!")
                </script><?php
                        }

                        if ($_GET['message'] == "1") { ?>
                <script>
                    alert("Data already exist!")
                </script><?php
                        }

                        if ($_GET['message'] == "2") { ?>
                <script>
                    alert("Data Saved!")
                </script><?php
                        }

                        if ($_GET['message'] == "3") { ?>
                <script>
                    alert("Error!")
                </script><?php
                        }

                        if ($_GET['message'] == "4") { ?>
                <script>
                    alert("Data already exist!")
                </script><?php
                        }
                    }

                            ?>

    </div><br><br><br><br><br><br><br><br>
    <?php include("../Contains/footer.php"); ?>
</body>

</html>