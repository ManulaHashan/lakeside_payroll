<?php
error_reporting(0);
include("../Contains/header.php");
include '../DB/DB.php';
?>
<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Privileges Profiles | Apex Payroll</title>
    <!-- <link href="../Styles/Stylie.css" rel="stylesheet" type="text/css"> -->
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
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.13.5/xlsx.full.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.13.5/jszip.js"></script>

    <script src="../JS/jquery-3.1.0.js"></script>
    <script src="../JS/numeral.min.js"></script>

    <script type="text/javascript">
    $(document).ready(function() {
        $('#loading').hide();
        setSpace();
        loadProfilesForSelectionPart();
        loadProfilesForTable();
    });

    $(document).ajaxStart(function() {
        $('#loading').show();
    }).ajaxStop(function() {
        $('#loading').hide();
    });

    function setSpace() {
        var wheight = $(window).height();
        var bheight = $('#body').height();

        $('#space').height(180);
    }


    var itemListPriv = [];

    function addItemToPrivTable() {
        var priv = $('#privname').val();
        var priv_part = priv.split(":");
        const pattern = /^\d+:.+$/; // Pattern: Any digits, colon, and then any text
        if (pattern.test(priv)) {
            var privData = priv_part[0] + "@" + priv_part[1].replaceAll("_", " ");
            var x = itemListPriv.indexOf(privData);
            if (x == -1) {
                itemListPriv.push(privData);
                var tr = "<tbody><tr id='tblPrivtr" + priv_part[0] + "'><td>" + priv_part[1].replaceAll("_", " ") +
                    "</td>";
                tr += "<td><center><img src='../Icons/remove.png' onclick='removePrivTableItem(" + priv_part[0] +
                    ", \"" + privData + "\")' style='cursor:pointer; width:25px;'></center></td></tr></tbody>";
                $('#tbl_lb').append(tr);
            } else {
                alert("This privilege already exists in the table!");
            }
        } else {
            alert("Please select the privilege first!");
        }
    }

    function removePrivTableItem(pid, ArrData) {
        var index = itemListPriv.indexOf(ArrData);
        if (index !== -1) {
            itemListPriv.splice(index, 1);
        }

        $('#tblPrivtr' + pid).remove();
    }


    function viewPrivData() {
        var prof_id = $('#proftype').val();
        $("#tbl_lb tbody").remove();
        var url = "../Controller/priv_settings.php?request=viewPrivData&prof_id=" + prof_id;
        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {

                if (data == "0") {
                    itemListPriv = [];
                    $("#tbl_lb tbody").remove();
                } else {
                    var obj = JSON.parse(data);
                    var obj_privData = obj["Privileges_Data"];
                    itemListPriv = [];
                    // Privileges Data
                    obj_privData.forEach(function(privitems) {

                        var arrayPRIVData = privitems.split("@");
                        var priv_ID = arrayPRIVData[0];
                        var priv_NAME = arrayPRIVData[1];

                        var privData = priv_ID + "@" + priv_NAME.replaceAll("_", " ");
                        var x = itemListPriv.indexOf(privData);
                        if (x == -1) {
                            itemListPriv.push(privData);
                            var tr = "<tbody><tr id='tblPrivtr" + priv_ID + "'><td>" + priv_NAME
                                .replaceAll("_", " ") + "</td>";
                            tr +=
                                "<td><center><img src='../Icons/remove.png' onclick='removePrivTableItem(" +
                                priv_ID + ", \"" + privData +
                                "\")' style='cursor:pointer; width:25px;'></center></td></tr></tbody>";
                            $('#tbl_lb').append(tr);
                        }
                    });
                }
            }
        });
    }

    function updatePrivData() {
        var priv_prof = $('#proftype').val();
        var priv_DATA = {
            "Priv_Prof_ID": priv_prof,
            "Priv_Data": itemListPriv
        }

        var json_data = JSON.stringify(priv_DATA);

        var url = "../Controller/priv_settings.php?request=updatePriv&privdata=" + json_data;
        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {

                if (data == "1") {
                    alert("Privileges added successfully!");
                    window.location.href = "../Views/priv_settings.php";
                } else {
                    alert("Error!");
                }
            }
        });
    }

    function addProfiles() {
        if ($('#profname').val() == "") {
            alert("Please type profile name!");
        } else {
            var url = "../Controller/priv_settings.php?request=addProf&profName=" + $('#profname').val();
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {

                    if (data == "1") {
                        alert("Profile added successfully!");
                        $('#profname').val("");
                        loadProfilesForTable();
                        loadProfilesForSelectionPart();
                    } else {
                        alert("Error!");
                    }
                }
            });
        }
    }

    function deleteProfiles(profid) {
        let confirmAction = confirm("Do you want to delete this profile?");

        if (confirmAction) {
            var url = "../Controller/priv_settings.php?request=deleteProf&profID=" + profid;
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {

                    if (data == "1") {
                        alert("Profile deleted successfully!");
                        loadProfilesForTable();
                        loadProfilesForSelectionPart();
                    } else {
                        alert("Error!");
                    }
                }
            });
        }

    }

    function loadProfilesForTable() {
        var url = "../Controller/priv_settings.php?request=viewProf";
        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                $('#prof_tablebody').html(data);
            }
        });
    }

    function loadProfilesForSelectionPart() {
        var url = "../Controller/priv_settings.php?request=getProf";
        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                $('#proftype').html(data);
                viewPrivData();
            }
        });
    }
    </script>

</head>

<body id="body" class="nav-md" style="background-color: white;">
    <?php include("../Contains/titlebar_dboard.php"); ?>
    <div class="container body">
        <div class="main_container">
            <div class="" style="width: 100%; margin: 1%;" role="main">

                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="row x_title">
                            <div class="col-md-6">
                                <h3>Handle Privileges <small>Manage privilege profiles</small></h3>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-5">
                                <div>
                                    <h4 style="text-decoration: underline;">Add Profiles</h4>
                                </div>
                                <div>
                                    <table style="margin-left: 10px;">
                                        <tr>
                                            <td height="35px;" width="200px;">Profile Name : </td>
                                            <td>
                                                <input type="text" id="profname" name="profname"
                                                    style="width: 180px; height: 26px;">
                                            </td>
                                            <td align="right">
                                                &nbsp;&nbsp;&nbsp;<a id="btsave" class="btn btn-primary"
                                                    onclick="addProfiles()"
                                                    style="width: 100px; text-decoration: none; margin-left: 0px;">Save</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">&nbsp;&nbsp;&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <div style="overflow:scroll; height:180px; width: 400px;">
                                                    <table id='prof_table' class='table table-bordered'>
                                                        <thead
                                                            style='background-color: #9eafba; position : sticky; top : 0; z-index: 0; color: black;'>
                                                            <tr>
                                                                <th width='30%'>Profile Name</th>
                                                                <th width='5%'></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="prof_tablebody"></tbody>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">&nbsp;&nbsp;&nbsp;</td>
                                        </tr>
                                    </table>
                                </div>
                                <div>
                                    <h4 style="text-decoration: underline;">Add Privileges</h4>
                                </div>
                                <div>
                                    <table style="margin-left: 10px;">
                                        <tr>
                                            <td height="35px;" width="200px;">Profile Name : </td>
                                            <td>
                                                <select id="proftype" name="proftype" class="select-basic"
                                                    style="width: 300px; height: 26px;" onchange="viewPrivData()">
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td height="35px;" width="200px;">Privilege</td>
                                            <td>
                                                <select id="privname" name="privname" class="select-basic"
                                                    style="width: 300px; height: 26px;">
                                                    <?php
                                                    $query = "select * from features where isactive='1'";
                                                    $res = Search($query);
                                                    while ($result = mysqli_fetch_assoc($res)) {
                                                    ?>
                                                    <option
                                                        value="<?php echo $result["fid"] . ":" . str_replace(" ", "_", $result["name"]); ?>">
                                                        <?php echo $result["name"]; ?> </option>
                                                    <?php } ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;&nbsp;&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td></td>
                                            <td align="right">
                                                <a id="btnadd" class="btn btn-success" onclick="addItemToPrivTable()"
                                                    style="width: 150px; text-decoration: none; margin-left: 0px;">Add</a>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <div class="col-md-7" style="border-left: 1px solid black;">
                                <div style="text-decoration: underline;">
                                    <h4>Privileges Table &nbsp; <small>The privileges list belonging to the privileges
                                            profile</small></h4>
                                </div>
                                <div>
                                    <table>
                                        <tr>
                                            <td><a id="btnupd" class="btn btn-warning" onclick="updatePrivData()"
                                                    style="width: 150px; text-decoration: none; margin-left: 0px;">Update</a>
                                            </td>
                                        </tr>
                                    </table></br>
                                    <div style="overflow:scroll; height:400px; width: 500px;">
                                        <table id='tbl_lb' class='table table-bordered'>
                                            <thead
                                                style='background-color: #9eafba; position : sticky; top : 0; z-index: 0; color: black;'>
                                                <tr>
                                                    <th width='30%'>Privilege Name</th>
                                                    <th width='5%'></th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </br>
    <?php include("../Contains/footer.php"); ?>
    </div>
    </div>
</body>

</html>