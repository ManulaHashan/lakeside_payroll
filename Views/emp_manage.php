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
    <!-- <script src="../JS/sweetalert2.js"></script> -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
    <link href="../Vendor/css/sweet-alert.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/nprogress/nprogress.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/animate.css/animate.min.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/css/custom.min.css" rel="stylesheet" type="text/css">
    <link href="../Vendor/iCheck/skins/flat/green.css" rel="stylesheet" type="text/css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.1/dist/js/bootstrap.bundle.min.js"></script>
    <style>
    #camera_wrapper,
    #show_saved_img {
        float: left;
        width: 250px;
    }
    </style>

    <script type="text/javascript" src="../JS/webcam.js"></script>
    <script>
    function capture() {
        $('#camera_wrapper').show();
        $('#userImg').hide();
        webcam.set_api_url('../Controller/webcamphotomanage.php');
        webcam.set_swf_url('../JS/webcam.swf'); //flash file (SWF) file path
        webcam.set_quality(100); // Image quality (1 - 100)
        webcam.set_shutter_sound(true); // play shutter click sound

        var camera = $('#camera');
        camera.html(webcam.get_html(250, 250)); //generate and put the flash embed code on page

        $('#capture_btn').click(function() {
            //take snap
            $('#picupdatecheck').val("true");
            webcam.snap();
        });
        //after taking snap call show image
        webcam.set_hook('onComplete', function(img) {
            //                    alert(img);
            //                    $('#userImg').html('<img src="' + img + '">');
            //reset camera for the next shot
            //webcam.reset();
            alert("Image Uploaded!");
            $('#userpic').val('true');
        });
    }
    </script>

    <script type="text/javascript">
    window.onload = function() {
        $('#myModal').hide();
        $('#loading').hide();

        // $('#req_upd').hide();
        // $('#req_trm').hide();
        // $('#req_ld').hide();
        // $('#req_sd').hide();
        // $('#req_tp').hide();

        $('#camera_wrapper').hide();
        $('#userImg').show();
        $('#logindetails').hide();
        $('#skilsdetailsupload').hide();
        $('#show_saved_img').hide();
        $('#etraAllow').hide();
        $("#psal").attr('disabled', 'disabled');
        getSalery(true);
        $('#Categ_Manage').hide();
        $('#casualleave').hide();
        $('#fixleave').hide();
        var date = new Date();
        document.getElementById('rdate').valueAsDate = date;

    };
    $(document).ready(function() {
        loadTable();
        loadEmployeeType();
        loadBranch();
        loadDepartment();
        // loadShiftTypes();
        changeWorkingTypeMeth();
    });
    $(document).ajaxStart(function() {
        $('#loading').show();
    }).ajaxStop(function() {
        $('#loading').hide();
    });

    function sweetalert(type, title, message) {
        Swal.fire({
            icon: type,
            title: title,
            text: message,
        })
    }

    function loadTable() {
        var fn = document.getElementById('sfn').value;
        // var ln = document.getElementById('sln').value;
        var type = document.getElementById('stype').value;
        // var posi = document.getElementById('sposi').value;
        var active = document.getElementById('active').value;
        var epfno = document.getElementById('epfno').value;
        var dept = document.getElementById('deptdata').value;
        // var system = document.getElementById('system').checked;
        var system = true;

        if (fn === "") {
            fn = "%";
        }
        // if (ln === "") {
        //     ln = "%";
        // }
        if (type === "") {
            type = "%";
        }
        // if (posi === "") {
        //     posi = "%";
        // }
        if (active === "") {
            active = "%";
        }
        if (epfno === "") {
            epfno = "%";
        }

        // var url = "../Controller/emp_manage.php?request=getEmps&fn=" + fn + "&ln=" + ln + "&type=" + type + "&posi=" + posi + "&active=" + active + "&system=" + system+ "&epfno=" + epfno;

        // var url = "../Controller/emp_manage.php?request=getEmps&fn=" + fn + "&type=" + type + "&posi=" + posi + "&active=" + active + "&system=" + system+ "&epfno=" + epfno + "&depdata=" + dept;

        var url = "../Controller/emp_manage.php?request=getEmps&fn=" + fn + "&type=" + type + "&active=" + active +
            "&system=" + system + "&epfno=" + epfno + "&depdata=" + dept;

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                $('#tdata').html(data);
            }
        });
    }

    function searchByID() {
        var id = document.getElementById('uid').value;

        if (id !== "") {
            $.ajax({
                type: 'POST',
                url: '../Controller/emp_manage.php?request=getEmpsbyID&uid=' + id,
                success: function(data) {
                    if (data !== "usernotfound") {
                        locID = 1;
                        $('#userImg').show();
                        $('#camera_wrapper').hide();
                        $('#savebtn').hide();

                        var arr = data.split("#");
                        $("#fn").val(arr[1]);
                        $("#cn").val(arr[2]);
                        $("#full_n").val(arr[3]);
                        $("#nic").val(arr[4]);
                        $("#tpno").val(arr[5]);
                        $("#lpno").val(arr[6]);
                        $("#dob").val(arr[7]);
                        $("#email").val(arr[8]);
                        $("#school").val(arr[9]);
                        $("#empno").val(id);
                        $("#gender").val(arr[10]);
                        // getskill(id);


                        $.ajax({
                            type: 'POST',
                            url: '../Model/HR/payroll.php?request=getPOSIandGradefromID&id=' + arr[
                                21],
                            success: function(data) {
                                // alert(data); 
                                var arr = data.split("#");
                                $("#posi").val(arr[0]);
                                $("#grade").val(arr[1]);
                            }
                        });


                        if (arr[11] !== "0") {
                            $("#psal").attr('disabled', false);
                            $("#psal").val(arr[11]);
                            $("#cussal").attr('checked', 'checked');
                        } else {

                            $("#psal").attr('disabled', 'disabled');
                            $("#cussal").attr('checked', false);
                        }

                        getSalery(false);

                        $("#rdate").val(arr[13]);

                        getAddress(arr[14]);
                        getPAddress(arr[15]);

                        getFiles();

                        $("#mstatus").val(arr[16]);
                        $("#status").val(arr[17]);
                        $("#etype").val(arr[18]);
                        $("#jc").val(arr[20]);
                        $("#epf").val(arr[22]);

                        var url = arr[19].split("/");
                        if (url == "") {
                            $('#userImg').html("<img src='../Images/userimg.png' width='200'>");
                        } else {
                            $('#userImg').html("<img src='../Images/UserPhotos/" + url[3] +
                                "' width='200'>");
                        }
                        $("#picupdate").val(arr[19]);

                        // after added
                        $("#bno").val(arr[23]);
                        $("#bbr").val(arr[24]);
                        $("#ptax").val(arr[25]);
                        $("#cleaves").val(arr[26]);
                        $("#fleaves").val(arr[27]);
                        $("#skilldes").val(arr[28]);
                        $("#empact").val(arr[29]);
                        $("#probdate").val(arr[30]);
                        $("#authperson").val(arr[32]);
                        $("#sec_authperson").val(arr[33]);
                        $("#wrk_typ").val(arr[34]);
                        $("#epf_entitle_date").val(arr[35]);
                        $("#emp_dip").val(arr[36]);
                        $("#privtype").val(arr[38]);

                        //get allowances
                        var arral = arr[39].split("//");
                        $("#attal").val(arral[1]);
                        $("#tral").val(arral[2]);
                        $("#othal").val(arral[0]);

                        //get allowances
                        var arrother = arr[40].split("%%");

                        if (arrother == "") {
                            $('#etraAllow').hide();
                        } else {
                            $("#allowNa1").val(arrother[0]);
                            $("#alowPR1").val(arrother[1]);
                            $("#allowNa2").val(arrother[2]);
                            $("#alowPR2").val(arrother[3]);
                            $("#allowNa3").val(arrother[4]);
                            $("#alowPR3").val(arrother[5]);
                            $("#allowNa4").val(arrother[6]);
                            $("#alowPR4").val(arrother[7]);
                            $("#allowNa5").val(arrother[8]);
                            $("#alowPR5").val(arrother[9]);

                            $('#etraAllow').show();
                        }

                        changeWorkingTypeMeth();

                        // //get edit types
                        // var reqdata = arr[41].split("/@/");

                        // if (reqdata == "" || reqdata == null) 
                        // {
                        //     $('#req_upd').hide();
                        //     $('#req_trm').hide();
                        //     $('#req_ld').hide();
                        //     $('#req_sd').hide();
                        //     $('#req_tp').hide();
                        // }
                        // else
                        // {
                        //     for (var i = 0; i < reqdata.length; i++) 
                        //     {
                        //         if (reqdata[i] == "1") 
                        //         {
                        //            $('#req_upd').show();
                        //         }
                        //         else if (reqdata[i] == "2") 
                        //         {
                        //             $('#req_trm').show();
                        //         }
                        //         else if (reqdata[i] == "3") 
                        //         {
                        //             $('#req_ld').show();
                        //         }
                        //         else if (reqdata[i] == "4") 
                        //         {
                        //             $('#req_sd').show();
                        //         }
                        //         else if (reqdata[i] == "5") 
                        //         {
                        //             $('#req_tp').show();
                        //         }
                        //     }
                        // }

                    } else {
                        $('#form')[0].reset();
                        $('#userImg').html("");
                        $('#savebtn').show();
                    }
                }
            });

        } else {
            alert("Please select Employee ID!");
        }
    }



    function searchByEPFNO() {

        var epfno = document.getElementById('epf').value;

        if (epfno !== "") {
            $.ajax({
                type: 'POST',
                url: '../Controller/emp_manage.php?request=getEmpsbyEPFNo&epf=' + epfno,
                success: function(data) {

                    if (data !== "usernotfound") {
                        locID = 1;
                        $('#userImg').show();
                        $('#camera_wrapper').hide();
                        $('#savebtn').hide();

                        var arr = data.split("#");
                        $("#fn").val(arr[1]);
                        $("#cn").val(arr[2]);
                        $("#full_n").val(arr[3]);
                        $("#nic").val(arr[4]);
                        $("#tpno").val(arr[5]);
                        $("#lpno").val(arr[6]);
                        $("#dob").val(arr[7]);
                        $("#email").val(arr[8]);
                        $("#school").val(arr[9]);
                        $("#empno").val(arr[0]);
                        $("#uid").val(arr[0]);
                        $("#gender").val(arr[10]);
                        // getskill(id);


                        $.ajax({
                            type: 'POST',
                            url: '../Model/HR/payroll.php?request=getPOSIandGradefromID&id=' + arr[
                                21],
                            success: function(data) {
                                // alert(data); 
                                var arr = data.split("#");
                                $("#posi").val(arr[0]);
                                $("#grade").val(arr[1]);
                            }
                        });


                        if (arr[11] !== "0") {
                            $("#psal").attr('disabled', false);
                            $("#psal").val(arr[11]);
                            $("#cussal").attr('checked', 'checked');
                        } else {

                            $("#psal").attr('disabled', 'disabled');
                            $("#cussal").attr('checked', false);
                        }

                        getSalery(false);

                        $("#rdate").val(arr[13]);

                        getAddress(arr[14]);
                        getPAddress(arr[15]);
                        getFiles();

                        $("#mstatus").val(arr[16]);
                        $("#status").val(arr[17]);
                        $("#etype").val(arr[18]);
                        $("#jc").val(arr[20]);
                        $("#epf").val(arr[22]);

                        var url = arr[19].split("/");
                        if (url == "") {
                            $('#userImg').html("<img src='../Images/userimg.png' width='200'>");
                        } else {
                            $('#userImg').html("<img src='../Images/UserPhotos/" + url[3] +
                                "' width='200'>");
                        }
                        $("#picupdate").val(arr[19]);

                        // after added
                        $("#bno").val(arr[23]);
                        $("#bbr").val(arr[24]);
                        $("#ptax").val(arr[25]);
                        $("#cleaves").val(arr[26]);
                        $("#fleaves").val(arr[27]);
                        $("#skilldes").val(arr[28]);
                        $("#empact").val(arr[29]);
                        $("#probdate").val(arr[30]);
                        $("#authperson").val(arr[32]);
                        $("#sec_authperson").val(arr[33]);
                        $("#wrk_typ").val(arr[34]);
                        $("#epf_entitle_date").val(arr[35]);
                        $("#emp_dip").val(arr[36]);
                        $("#privtype").val(arr[38]);

                        //get allowances
                        var arral = arr[39].split("//");
                        $("#attal").val(arral[1]);
                        $("#tral").val(arral[2]);
                        $("#othal").val(arral[0]);

                        //get allowances
                        var arrother = arr[40].split("%%");

                        if (arrother == "") {
                            $('#etraAllow').hide();
                        } else {
                            $("#allowNa1").val(arrother[0]);
                            $("#alowPR1").val(arrother[1]);
                            $("#allowNa2").val(arrother[2]);
                            $("#alowPR2").val(arrother[3]);
                            $("#allowNa3").val(arrother[4]);
                            $("#alowPR3").val(arrother[5]);
                            $("#allowNa4").val(arrother[6]);
                            $("#alowPR4").val(arrother[7]);
                            $("#allowNa5").val(arrother[8]);
                            $("#alowPR5").val(arrother[9]);

                            $('#etraAllow').show();
                        }

                        changeWorkingTypeMeth();

                        // //get edit types
                        //  var reqdata = arr[41].split("/@/");

                        //  if (reqdata == "" || reqdata == null) 
                        //  {
                        //      $('#req_upd').hide();
                        //      $('#req_trm').hide();
                        //      $('#req_ld').hide();
                        //      $('#req_sd').hide();
                        //      $('#req_tp').hide();
                        //  }
                        //  else
                        //  {
                        //      for (var i = 0; i < reqdata.length; i++) 
                        //      {
                        //          if (reqdata[i] == "1") 
                        //          {
                        //             $('#req_upd').show();
                        //          }
                        //          else if (reqdata[i] == "2") 
                        //          {
                        //              $('#req_trm').show();
                        //          }
                        //          else if (reqdata[i] == "3") 
                        //          {
                        //              $('#req_ld').show();
                        //          }
                        //          else if (reqdata[i] == "4") 
                        //          {
                        //              $('#req_sd').show();
                        //          }
                        //          else if (reqdata[i] == "5") 
                        //          {
                        //              $('#req_tp').show();
                        //          }
                        //      }
                        //  }

                    } else {
                        $('#form')[0].reset();
                        $('#userImg').html("");
                        $('#savebtn').show();
                    }
                }
            });

        } else {
            alert("Please enter EPF No!");
        }
    }


    function getFiles() {
        var uid = document.getElementById('empno').value;
        $("#filestable").html("");

        $.ajax({
            type: 'POST',
            url: '../Controller/emp_manage.php?request=getfiles&empuid=' + uid,
            success: function(data) {

                if (data == 0) {
                    $("#filestable").html("");
                } else {
                    var arr = data.split("#");

                    for (var i = 0; i < arr.length; ++i) {

                        var newLocTR = "<tr><td><a href='../SkillsFiles/" + uid + "/" + arr[i] +
                            "' target='_blank'>" + arr[i] + "</a></td></tr>";

                        $("#filestable").html($("#filestable").html() + newLocTR);

                    }
                }

            }
        });

    }

    function getAddress(id) {
        $.ajax({
            type: 'POST',
            url: '../Controller/emp_manage.php?request=getAddress&aid=' + id,
            success: function(data) {
                $("#add").val(data);
            }
        });
    }

    function getPAddress(id) {
        $.ajax({
            type: 'POST',
            url: '../Controller/emp_manage.php?request=getAddress&aid=' + id,
            success: function(data) {
                $("#padd").val(data);
            }
        });
    }

    function setUID(uid) {
        $('#uid').val(uid);
        searchByID();
    }

    function loadLDetails() {
        if ($('#logindetails').is(":visible")) {
            $('#logindetails').hide();
        } else {
            $('#logindetails').show();

        }
    }

    function skillsDetails() {
        if ($('#skilsdetailsupload').is(":visible")) {
            $('#skilsdetailsupload').hide();
        } else {
            $('#skilsdetailsupload').show();

        }
    }


    function saveLogins() {

        if ($('#uid').val() == "") {
            alert("Please Select Employee")
        } else {
            var url = "../Controller/emp_manage.php?request=saveLoginDetails&usr=" + $('#usr').val() + "&pwrd=" + $(
                    '#pwrd').val() + "&uid=" + $('#uid').val() + "&qone=" + $('#qone').val() + "&qtwo=" + $('#qtwo')
                .val();

            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    alert(data);
                    $('#usr').val("");
                    $('#pwrd').val("");
                    $('#qone').val("");
                    $('#qtwo').val("");
                    $('#logindetails').hide();
                    $('#req_ld').hide();
                }
            });
        }


    }

    function loadPrivs() {
        $('#show_saved_img').show();
        if ($('#uid').val() !== "") {
            $.ajax({
                type: 'POST',
                url: '../Controller/emp_manage.php?request=getprivs&uid=' + $('#uid').val(),
                success: function(data) {
                    $("#privs").html(data);
                    var targeted_popup_class = jQuery($('[data-popup-open]')).attr('data-popup-open');
                    $('[data-popup="' + targeted_popup_class + '"]').fadeIn(350);

                    e.preventDefault();

                    $('#loading').hide();
                }
            });



        } else {
            alert("Please select an employee!");
        }
    }

    function savePrivs() {
        var url = '../Controller/emp_manage.php?request=saveprivs&uid=' + $('#uid').val();
        $('#privs').find(':input').each(function() {
            url += "&" + $(this).attr('id') + "=" + $(this).is(':checked');
        });

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                alert(data);
                $('#loading').hide();
                closePrivs();
            }
        });
    }

    function closePrivs() {
        var targeted_popup_class = jQuery($('[data-popup-close]')).attr('data-popup-close');
        $('[data-popup="' + targeted_popup_class + '"]').fadeOut(350);

        e.preventDefault();
    }

    function refresh() {
        $('#userImg').html("");
        $("#dlocs").html("");
    }

    function getSalery(setSal) {
        var position = $('#posi').val();
        var grade = $('#grade').val();

        var url = '../Model/HR/payroll.php?request=getbasicfrompandg&posi=' + position + "&grade=" + grade;
        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                var arrx = data.split('###/#');
                var arr = arrx[0].split('#');
                $("#postid").val(arr[0]);

                if (setSal) {
                    $('#psal').val(arr[1]);
                    $('#grade').html(arrx[1]);
                }
            }
        });
    }

    function customSaleryEnter() {
        if ($('#cussal').is(':checked')) {
            $("#psal").attr('disabled', false);
        } else {
            $("#psal").attr('disabled', 'disabled');
            $("#psal").val("0");
        }
    }

    // var alowID = 0;
    function addDLoc() {

        // alowID += 1;

        // var newLocTR = "<tr><td height='35px;' width='200px;'><input id='alowNa"+alowID+"' type='text' name='alowNa"+alowID+"' class='form-label'/></td><td><input id='alowPR"+alowID+"' type='text' name='alowPR"+alowID+"' class='input-text'/></td></tr>";

        // $("#dlocs").html($("#dlocs").html() + newLocTR);

        if ($('#etraAllow').is(":visible")) {
            $('#etraAllow').hide();
        } else {
            $('#etraAllow').show();

        }

    }

    //Employee Type Management........................................
    function loadEmployeeType() {
        var url = "../Controller/emp_manage.php?request=getemptypes";

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                var arr = data.split("///");
                $('#emptypetable').html(arr[0]);
            }
        });

    }

    var selectedType = "";

    function select_type(detail) {

        var arr = detail.split("#");
        $("#tpid").val(arr[0]);
        $("#emptype").val(arr[1]);
        selectedType = arr[0];
    }

    function inserttype() {

        if ($("#emptype").val() != "") {

            var name = $("#emptype").val();

            var url = "../Controller/emp_manage.php?request=saveEmpType&name=" + name;
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    alert(data);
                    loadEmployeeType();
                    $("#emptype").val("");

                }
            });

        } else {
            alert("Cannot Save an Empty Value !");
        }
    }

    function updatetype() {

        if ($("#emptype").val() != "") {
            var name = $("#emptype").val();
            var selected = selectedType;
            var url = "../Controller/emp_manage.php?request=updateEmpType&name=" + name + "&id=" + selected;
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    alert(data);
                    loadEmployeeType();
                    $("#emptype").val("");

                }
            });

        } else {
            alert("Cannot Update as an Empty Value !");
        }
    }

    function deletetype(id) {

        let confirmAction = confirm("Do you want to delete this type?");

        if (confirmAction) {

            var selected = id;
            var url = "../Controller/emp_manage.php?request=deleteEmpType&id=" + selected;
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    alert(data);
                    loadEmployeeType();
                    $("#emptype").val("");

                }
            });

        }
    }


    function resignDetails() {


        if ($('#uid').val() !== "") {

            let confirmAction = confirm("Do you want to resign this employee?");

            if (confirmAction) {

                var User = $('#uid').val();

                var url = "../Controller/emp_manage.php?request=resignEmployee&user=" + User;
                $.ajax({
                    type: 'POST',
                    url: url,
                    success: function(data) {
                        alert(data);
                        loadTable();
                    }
                });

            }


        } else {
            alert("Please select an employee!");
        }



    }




    // Employee Branch Management......................
    function loadBranch() {


        var url = "../Controller/emp_manage.php?request=getbranch";

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                var arr = data.split("///");
                $('#branchtable').html(arr[0]);

            }
        });

    }

    function insertBranch() {

        if ($("#dipname").val() != "") {
            var name = $("#dipname").val();

            var url = "../Controller/emp_manage.php?request=inbranch&name=" + name;
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    alert(data);
                    loadBranch();

                    $("#dipname").val("");

                }
            });

        } else {
            alert("Cannot save an empty value !");
        }
    }

    var selecteddip = "";

    function select_branch(detail) {

        var arr = detail.split("#");
        $("#dipid").val(arr[0]);
        $("#dipname").val(arr[1]);
        selecteddip = arr[0];
    }

    function updateBranch() {

        if ($("#dipname").val() != "") {
            var name = $("#dipname").val();
            var selected = selecteddip;
            var url = "../Controller/emp_manage.php?request=upbranch&name=" + name + "&id=" + selected;
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    alert(data);
                    loadBranch();
                    $("#dipname").val("");

                }
            });

        } else {
            alert("Cannot Update as an empty value !");
        }
    }

    function deleteBranch(id) {

        let confirmAction = confirm("Do you want to delete this branch?");

        if (confirmAction) {

            var selected = id;
            var url = "../Controller/emp_manage.php?request=delbranch&id=" + selected;
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    alert(data);
                    loadBranch();
                    $("#dipname").val("");


                }
            });
        }

    }


    // Employee Department Management......................
    function loadDepartment() {

        var url = "../Controller/emp_manage.php?request=getdepartment";

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                var arr = data.split("///");
                $('#emp_dept_table').html(arr[0]);

            }
        });

    }

    function insertDepartment() {

        if ($("#emp_dipname").val() != "") {
            var name = $("#emp_dipname").val();

            var url = "../Controller/emp_manage.php?request=indepartment&name=" + name;
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    alert(data);
                    loadDepartment();

                    $("#emp_dipname").val("");

                }
            });

        } else {
            alert("Cannot save an empty value !");
        }
    }

    var selecteddip = "";

    function select_departments(detail) {

        var arr = detail.split("#");
        $("#empdid").val(arr[0]);
        $("#emp_dipname").val(arr[1]);
        selecteddip = arr[0];
    }

    function updateDepartment() {

        if ($("#emp_dipname").val() != "") {
            var name = $("#emp_dipname").val();
            var selected = selecteddip;
            var url = "../Controller/emp_manage.php?request=updepartment&name=" + name + "&id=" + selected;
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    alert(data);
                    loadDepartment();
                    $("#emp_dipname").val("");

                }
            });

        } else {
            alert("Cannot Update as an empty value !");
        }
    }

    function deleteDepartment(id) {

        let confirmAction = confirm("Do you want to delete this department?");

        if (confirmAction) {

            var selected = id;
            var url = "../Controller/emp_manage.php?request=deldepartment&id=" + selected;
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    alert(data);
                    loadDepartment();
                    $("#emp_dipname").val("");
                }
            });
        }

    }


    // Employee Shift Types Management......................
    function loadShiftTypes() {


        var url = "../Controller/emp_manage.php?request=getshift";

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                var arr = data.split("///");
                $('#shifttable').html(arr[0]);

            }
        });

    }

    function insertShiftTypes() {

        if ($("#shname").val() != "") {
            var name = $("#shname").val();
            var IN = $("#shin").val();
            var OUT = $("#shout").val();

            var url = "../Controller/emp_manage.php?request=saveshift&name=" + name + "&in=" + IN + "&out=" + OUT;
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    alert(data);
                    loadShiftTypes();

                    $("#shname").val("");
                    $("#shin").val("");
                    $("#shout").val("");

                }
            });

        } else {
            alert("Cannot save an empty value !");
        }
    }

    var selectedshift = "";

    function select_ShiftTypes(detail) {

        var arr = detail.split("#");
        $("#shiftid").val(arr[0]);
        $("#shname").val(arr[1]);
        $("#shin").val(arr[2]);
        $("#shout").val(arr[3]);
        selectedshift = arr[0];
    }

    function updateShiftTypes() {

        if ($("#shname").val() != "") {
            var name = $("#shname").val();
            var IN = $("#shin").val();
            var OUT = $("#shout").val();
            var selectedsh = selectedshift;
            var url = "../Controller/emp_manage.php?request=updateshift&name=" + name + "&in=" + IN + "&out=" + OUT +
                "&id=" + selectedsh;
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    alert(data);
                    loadShiftTypes();

                    $("#shname").val("");
                    $("#shin").val("");
                    $("#shout").val("");

                }
            });

        } else {
            alert("Cannot Update as an empty value !");
        }
    }

    function deleteShiftTypes(id) {

        let confirmAction = confirm("Do you want to delete this shift?");

        if (confirmAction) {

            var selectedsh = id;
            var url = "../Controller/emp_manage.php?request=deleteshift&id=" + selectedsh;
            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    alert(data);
                    loadShiftTypes();
                    $("#shname").val("");
                    $("#shin").val("");
                    $("#shout").val("");


                }
            });
        }

    }


    function ChangeCategoryDataManage() {
        if ($('#cat_manage').is(':checked')) {
            $('#Categ_Manage').show();
        } else {
            $('#Categ_Manage').hide();
        }
    }


    function changeWorkingTypeMeth() {
        if ($("#wrk_typ").val() == "1") {
            $('#time_profile').show();
        } else {
            $('#time_profile').hide();
        }
    }


    function loadTimeProf() {
        if ($('#uid').val() !== "") {
            selectSettingsData();
            $('#myModal').show();
        } else {
            alert("Please select an employee!");
        }
    }


    window.onclick = function(event) {

        var modal = document.getElementById("myModal");

        if (event.target == modal) {
            $('#myModal').hide();
        }
    }

    function CloseModel() {
        $('#myModal').hide();
    }

    function inputSettingsData() {

        var intime = $('#wrkintime').val();
        var outtime = $('#wrkouttime').val();
        var wrkLate = $('#wrkLate').val();
        var SatIntime = $('#wrkintimeSat').val();
        var SatOuttime = $('#wrkouttimeSat').val();
        var wrkEndLate = $('#wrkEndLate').val();
        var halfMIntime = $('#halfmorningstart').val();
        var halfMOuttime = $('#halfmorningend').val();
        var halfEIntime = $('#halfeveningstart').val();
        var halfEOuttime = $('#halfeveningend').val();
        var halfMLate = $('#halfMLate').val();
        var halfELate = $('#halfELate').val();
        var shortMIntime = $('#shortmorningstart').val();
        var shortMOuttime = $('#shortmorningend').val();
        var shortEIntime = $('#shorteveningstart').val();
        var shortEOuttime = $('#shorteveningend').val();
        var shrtMLate = $('#shrtMLate').val();
        var shrtELate = $('#shrtELate').val();
        var user = $('#uid').val();


        var url = "../Controller/emp_manage.php?request=SaveSettings&intime=" + intime + "&outtime=" + outtime +
            "&halfMIntime=" + halfMIntime + "&halfMOuttime=" + halfMOuttime + "&halfEIntime=" + halfEIntime +
            "&halfEOuttime=" + halfEOuttime + "&shortMIntime=" + shortMIntime + "&shortMOuttime=" + shortMOuttime +
            "&shortEIntime=" + shortEIntime + "&shortEOuttime=" + shortEOuttime + "&SatIntime=" + SatIntime +
            "&SatOuttime=" + SatOuttime + "&wrkLate=" + wrkLate + "&wrkEndLate=" + wrkEndLate + "&halfMLate=" +
            halfMLate + "&halfELate=" + halfELate + "&shrtMLate=" + shrtMLate + "&shrtELate=" + shrtELate +
            "&user_ID=" + user;

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {
                alert(data);
                checkSettingsData();
                CloseModel();
                $('#req_tp').hide();

                $('#wrkintime').val("");
                $('#wrkouttime').val("");
                $('#wrkLate').val("");
                $('#wrkintimeSat').val("");
                $('#wrkouttimeSat').val("");
                $('#wrkEndLate').val("");
                $('#halfmorningstart').val("");
                $('#halfmorningend').val("");
                $('#halfeveningstart').val("");
                $('#halfeveningend').val("");
                $('#halfMLate').val("");
                $('#halfELate').val("");
                $('#shortmorningstart').val("");
                $('#shortmorningend').val("");
                $('#shorteveningstart').val("");
                $('#shorteveningend').val("");
                $('#shrtMLate').val("");
                $('#shrtELate').val("");
            }
        });
    }


    function updateSettingsData() {
        var SWTID = $('#swtid').val();
        var intime = $('#wrkintime').val();
        var outtime = $('#wrkouttime').val();
        var wrkLate = $('#wrkLate').val();
        var SatIntime = $('#wrkintimeSat').val();
        var SatOuttime = $('#wrkouttimeSat').val();
        var wrkEndLate = $('#wrkEndLate').val();
        var halfMIntime = $('#halfmorningstart').val();
        var halfMOuttime = $('#halfmorningend').val();
        var halfEIntime = $('#halfeveningstart').val();
        var halfEOuttime = $('#halfeveningend').val();
        var halfMLate = $('#halfMLate').val();
        var halfELate = $('#halfELate').val();
        var shortMIntime = $('#shortmorningstart').val();
        var shortMOuttime = $('#shortmorningend').val();
        var shortEIntime = $('#shorteveningstart').val();
        var shortEOuttime = $('#shorteveningend').val();
        var shrtMLate = $('#shrtMLate').val();
        var shrtELate = $('#shrtELate').val();
        var user = $('#uid').val();


        if (SWTID == "") {
            alert("Please create a working time profile first!");
        } else {
            var url = "../Controller/emp_manage.php?request=UpdateSettings&intime=" + intime + "&outtime=" + outtime +
                "&halfMIntime=" + halfMIntime + "&halfMOuttime=" + halfMOuttime + "&halfEIntime=" + halfEIntime +
                "&halfEOuttime=" + halfEOuttime + "&shortMIntime=" + shortMIntime + "&shortMOuttime=" + shortMOuttime +
                "&shortEIntime=" + shortEIntime + "&shortEOuttime=" + shortEOuttime + "&SWTID=" + SWTID +
                "&SatIntime=" + SatIntime + "&SatOuttime=" + SatOuttime + "&wrkLate=" + wrkLate + "&wrkEndLate=" +
                wrkEndLate + "&halfMLate=" + halfMLate + "&halfELate=" + halfELate + "&shrtMLate=" + shrtMLate +
                "&shrtELate=" + shrtELate + "&user_ID=" + user;

            $.ajax({
                type: 'POST',
                url: url,
                success: function(data) {
                    alert(data);
                    checkSettingsData();
                    CloseModel();
                    $('#req_tp').hide();

                    $('#swtid').val("");
                    $('#wrkintime').val("");
                    $('#wrkouttime').val("");
                    $('#wrkLate').val("");
                    $('#wrkintimeSat').val("");
                    $('#wrkouttimeSat').val("");
                    $('#wrkEndLate').val("");
                    $('#halfmorningstart').val("");
                    $('#halfmorningend').val("");
                    $('#halfeveningstart').val("");
                    $('#halfeveningend').val("");
                    $('#halfMLate').val("");
                    $('#halfELate').val("");
                    $('#shortmorningstart').val("");
                    $('#shortmorningend').val("");
                    $('#shorteveningstart').val("");
                    $('#shorteveningend').val("");
                    $('#shrtMLate').val("");
                    $('#shrtELate').val("");
                }
            });
        }


    }


    function selectSettingsData() {

        var url = "../Controller/emp_manage.php?request=SelectSettings&EMPID=" + $('#uid').val();

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {

                if (data != "") {
                    var arr = data.split("#");
                    $('#swtid').val(arr[0]);
                    $('#wrkintime').val(arr[1]);
                    $('#wrkouttime').val(arr[2]);

                    var halfMorning = arr[3].split(" ");
                    $('#halfmorningstart').val(halfMorning[0]);
                    $('#halfmorningend').val(halfMorning[3]);

                    var halfEvening = arr[4].split(" ");
                    $('#halfeveningstart').val(halfEvening[0]);
                    $('#halfeveningend').val(halfEvening[3]);

                    var shortMorning = arr[5].split(" ");
                    $('#shortmorningstart').val(shortMorning[0]);
                    $('#shortmorningend').val(shortMorning[3]);

                    var shortEvening = arr[6].split(" ");
                    $('#shorteveningstart').val(shortEvening[0]);
                    $('#shorteveningend').val(shortEvening[3]);

                    $('#wrkintimeSat').val(arr[10]);
                    $('#wrkouttimeSat').val(arr[11]);

                    $('#wrkLate').val(arr[12]);

                    $('#wrkEndLate').val(arr[14]);

                    $('#halfMLate').val(arr[16]);
                    $('#halfELate').val(arr[17]);

                    $('#shrtMLate').val(arr[18]);
                    $('#shrtELate').val(arr[19]);

                    checkSettingsData();
                } else {
                    $('#swtid').val("");
                    $('#wrkintime').val("");
                    $('#wrkouttime').val("");
                    $('#wrkLate').val("");
                    $('#wrkintimeSat').val("");
                    $('#wrkouttimeSat').val("");
                    $('#wrkEndLate').val("");
                    $('#halfmorningstart').val("");
                    $('#halfmorningend').val("");
                    $('#halfeveningstart').val("");
                    $('#halfeveningend').val("");
                    $('#halfMLate').val("");
                    $('#halfELate').val("");
                    $('#shortmorningstart').val("");
                    $('#shortmorningend').val("");
                    $('#shorteveningstart').val("");
                    $('#shorteveningend').val("");
                    $('#shrtMLate').val("");
                    $('#shrtELate').val("");

                    checkSettingsData();
                }
            }
        });
    }

    function checkSettingsData() {

        var url = "../Controller/emp_manage.php?request=CheckSettings&EMPID=" + $('#uid').val();

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {

                if (data == "OK") {
                    $('#profsave').hide();
                    $('#profupdate').show();
                } else {
                    $('#profsave').show();
                    $('#profupdate').hide();
                }
            }
        });
    }


    function checkDateValidity() {
        var inputDate = new Date(document.getElementById("rdate").value);
        var currentDate = new Date();

        var beforeMonth = new Date(currentDate);
        beforeMonth.setMonth(beforeMonth.getMonth() - 1);

        if (inputDate < beforeMonth) {
            alert("You can't select a date less than one month from the current date.");
            document.getElementById('rdate').valueAsDate = currentDate;
        } else if (inputDate > currentDate) {
            alert("You can't select a date in the future.");
            document.getElementById('rdate').valueAsDate = currentDate;
        }
    }



    function approve_User(userID) {
        var url = "../Controller/emp_manage.php?request=approveUser&EMPID=" + userID;

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {

                if (data == "1") {
                    alert("Employee record approved!");
                    window.location.href = "../Views/emp_manage.php";
                } else {
                    alert("Error!");
                }
            }
        });
    }

    function decline_User(userID) {
        var url = "../Controller/emp_manage.php?request=declineUser&EMPID=" + userID;

        $.ajax({
            type: 'POST',
            url: url,
            success: function(data) {

                if (data == "1") {
                    alert("Employee record declined!");
                    window.location.href = "../Views/emp_manage.php";
                } else {
                    alert("Error!");
                }
            }
        });
    }
    </script>

    <style>
    /* The Modal (background) */
    .modal {
        position: fixed;
        /* Stay in place */
        z-index: 1;
        /* Sit on top */
        padding-top: 100px;
        /* Location of the box */
        left: 0;
        top: 0;
        width: 100%;
        /* Full width */
        height: 100%;
        /* Full height */
        overflow: auto;
        /* Enable scroll if needed */
        background-color: rgb(0, 0, 0);
        /* Fallback color */
        background-color: rgba(0, 0, 0, 0.4);
        /* Black w/ opacity */
    }

    /* Modal Content */
    .modal-content {
        background-color: #fefefe;
        margin: auto;
        padding: 20px;
        border: 1px solid #888;
        border-radius: 8px;
        width: 55%;
    }

    /* The Close Button */
    .close {
        color: #aaaaaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }
    </style>
</head>

<body id="body" class="nav-md" style="background-color: white;">
    <?php include("../Contains/titlebar_dboard.php"); ?>
    <div class="container body">
        <div class="main_container">


            <!-- page content -->
            <div class="" style="width: 100%; margin: 1%;" role="main">


                <div class="row">
                    <div class="col-md-12 col-sm-12 col-xs-12">


                        <div class="row x_title">
                            <div class="col-md-6">
                                <h3>Employee Management <small>Manage employee details</small></h3>
                            </div>
                            <div class="col-md-6">
                            </div>

                        </div>

                        <div class="col-md-9 col-sm-9 col-xs-12">


                        </div>
                        <!-- <div>
                  <div class="x_title">
                    <h2>Search Employee</h2>
                    <div class="clearfix"></div>
                  </div>
                <div class="clearfix"></div>
            </div> -->

                        <br />

                        <div style="margin: 15px;">
                            <table>
                                <tr>
                                    <td valign="top">
                                        <table width="80%" border="0" cellspacing="" cellpadding="0">
                                            <tr>
                                                <td>
                                                    <div id="userImg"
                                                        style="margin:10px; width: 200px; height: 200px;border-radius: 25px;background-color: #DFDBDB; background-repeat: no-repeat; background-position: center;">
                                                        <img height="200px;" width="200px;" src="../Images/userimg.png">
                                                    </div>

                                                    <div id="camera_wrapper" style="">
                                                        <div id="camera"></div>
                                                        <br />
                                                        <button id="capture_btn">Capture Photo</button>
                                                    </div>
                                                </td>

                                                <td valign="bottom">&nbsp;<i onclick="capture()"
                                                        style="cursor: pointer;" class="fa fa-camera"></i>
                                                    <input type="hidden" id="userpic" name="userpic" value="false">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <p></p>
                                                </td>
                                                <td></td>
                                            </tr>

                                            <form id="form" action="../Controller/emp_manage.php" method="POST"
                                                class="form-basic">
                                                <input type="hidden" id="imgfileurl" name="file" value="">
                                                <tr>
                                                    <td height="35px;" width="200px;">
                                                        <p class="form-label">ID</p>
                                                    </td>
                                                    <td><input type="text" id="uid" name="uid" readonly="readonly"
                                                            class="input-text" style="float: left; width: 160px">
                                                        &nbsp;&nbsp; <i class="fa fa-search" onclick="searchByID()"
                                                            style="float: next; cursor: pointer"></i></td>
                                                </tr>
                                                <tr>
                                                    <td height="35px;" width="200px;">
                                                        <p class="form-label">Employee No (Fingerprint)</p>
                                                    </td>
                                                    <td><input id="jc" type="text" name="jc" class="input-text"></td>
                                                </tr>
                                                <tr>
                                                    <td height="35px;" width="200px;">
                                                        <p class="form-label">E.P.F. Number</p>
                                                    </td>
                                                    <td><input id="epf" type="text" name="epf" class="input-text"
                                                            style="float: left; width: 160px">&nbsp;&nbsp; <i
                                                            class="fa fa-search" onclick="searchByEPFNO()"
                                                            style="float: next; cursor: pointer"></i></td>
                                                </tr>
                                                <tr>
                                                    <td height="35px;" width="200px;">
                                                        <p class="form-label">Full Name</p>
                                                    </td>
                                                    <td><input id="full_n" type="text" name="full_n" class="input-text"
                                                            required></td>
                                                </tr>
                                                <tr>
                                                    <td height="35px;" width="200px;">
                                                        <p class="form-label">Name With Initials</p>
                                                    </td>
                                                    <td><input id="fn" type="text" name="fn" class="input-text"
                                                            required></td>
                                                </tr>
                                                <tr>
                                                    <td height="35px;" width="200px;">
                                                        <p class="form-label">Calling Name</p>
                                                    </td>
                                                    <td><input id="cn" type="text" name="cn" class="input-text"></td>
                                                </tr>
                                                <tr>
                                                    <td height="35px;" width="200px;">
                                                        <p class="form-label">Gender</p>
                                                    </td>
                                                    <td><select id="gender" name="gender" class="select-basic"
                                                            style="width: 180px">
                                                            <option value="0"></option>
                                                            <option value="1">Male</option>
                                                            <option value="2">Female</option>
                                                        </select>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td height="35px;" width="200px;">
                                                        <p class="form-label">NIC / Passport</p>
                                                    </td>
                                                    <td><input id="nic" type="text" name="nic" class="input-text"
                                                            pattern="[A-Za-z0-9]{10,15}" title="Enter valid NIC number">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td height="35px;" width="200px;">
                                                        <p class="form-label">Mobile P.Number</p>
                                                    </td>
                                                    <td><input id="tpno" type="text" name="tpno" class="input-text"
                                                            pattern="[0-9]{10}" title="Enter valid phone number!"
                                                            required></td>
                                                </tr>
                                                <tr>
                                                    <td height="35px;" width="200px;">
                                                        <p class="form-label">Land P.Number</p>
                                                    </td>
                                                    <td><input id="lpno" type="text" name="lpno" class="input-text"
                                                            pattern="[0-9]{10}" title="Enter valid phone number!"></td>
                                                </tr>
                                                <tr>
                                                    <td height="35px;" width="200px;">
                                                        <p class="form-label">Date of Birth</p>
                                                    </td>
                                                    <td><input id="dob" type="date" name="dob" class="input-text"
                                                            style="width: 186px"></td>
                                                </tr>
                                                <tr>
                                                    <td height="35px;" width="200px;">
                                                        <p class="form-label">Email</p>
                                                    </td>
                                                    <td><input id="email" type="text" name="email" class="input-text"
                                                            pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$"
                                                            title="Enter valid email address"></td>
                                                </tr>
                                                <tr>
                                                    <td height="35px;" width="200px;">
                                                        <p class="form-label">Address</p>
                                                    </td>
                                                    <td><textarea id="add"
                                                            style="margin-bottom:5px;margin-top: 5px; width: 186px"
                                                            class="text-area" name="add" cols="22" rows="3"></textarea>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td height="35px;" width="200px;">
                                                        <p class="form-label">Permanent Address</p>
                                                    </td>
                                                    <td><textarea id="padd"
                                                            style="margin-bottom:5px;margin-top: 5px;width: 186px"
                                                            class="text-area" cols="22" name="padd" rows="3"></textarea>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td height="35px;" width="200px;">
                                                        <p class="form-label">Designation</p>
                                                    </td>
                                                    <td><input id="school" type="text" name="school" class="input-text">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td height="35px;" width="200px;">
                                                        <p class="form-label">Marital Status</p>
                                                    </td>
                                                    <td>
                                                        <select id="mstatus" name="mstatus" class="select-basic"
                                                            style="width: 186px">
                                                            <?php
                                                            $query = "select * from maritalstatus";
                                                            $res = Search($query);
                                                            while ($result = mysqli_fetch_assoc($res)) {
                                                            ?>
                                                            <option value="<?php echo $result["idMaritalStatus"]; ?>">
                                                                <?php echo $result["name"]; ?> </option>
                                                            <?php } ?>
                                                        </select>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td height="35px;" width="200px;">
                                                        <p class="form-label">Branch</p>
                                                    </td>
                                                    <td>
                                                        <select id="posi" name="posi" class="select-basic"
                                                            style="width: 186px">
                                                            <!-- <select id="posi" name="posi" class="select-basic" style="width: 186px" onchange="getSalery(true)"> -->
                                                            <?php
                                                            $query = "select * from position";
                                                            $res = Search($query);
                                                            while ($result = mysqli_fetch_assoc($res)) {
                                                            ?>
                                                            <option value="<?php echo $result["pid"]; ?>">
                                                                <?php echo $result["name"]; ?> </option>
                                                            <?php } ?>
                                                        </select>

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td height="35px;" width="200px;">
                                                        <p class="form-label">Department</p>
                                                    </td>
                                                    <td>
                                                        <select id="emp_dip" name="emp_dip" class="select-basic"
                                                            style="width: 186px">
                                                            <?php
                                                            $query = "select * from emp_department";
                                                            $res = Search($query);
                                                            while ($result = mysqli_fetch_assoc($res)) {
                                                            ?>
                                                            <option value="<?php echo $result["did"]; ?>">
                                                                <?php echo $result["name"]; ?> </option>
                                                            <?php } ?>
                                                        </select>

                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td height="35px;" width="200px;">
                                                        <p class="form-label">Grade</p>
                                                    </td>
                                                    <td>
                                                        <select id="grade" name="grade" class="select-basic"
                                                            style="width: 186px">
                                                            <!-- <select id="grade" name="grade" class="select-basic" style="width: 186px" onchange="getSalery(true)"> -->
                                                            <option value="null">None</option>
                                                            <?php
                                                            $query = "select * from grade";
                                                            $res = Search($query);
                                                            while ($result = mysqli_fetch_assoc($res)) {
                                                            ?>
                                                            <option value="<?php echo $result["gid"]; ?>">
                                                                <?php echo $result["name"]; ?> </option>
                                                            <?php } ?>
                                                        </select>

                                                        <input type="hidden" id="postid" name="postid" value="">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td height="35px;" width="200px;">
                                                        <p class="form-label">Basic Salary</p>
                                                    </td>
                                                    <td><input id="psal" type="text" name="psal" class="input-text">
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td height="35px;" width="200px;"></td>
                                                    <td><input type="checkbox" id="cussal" name="cussal"
                                                            onchange="customSaleryEnter()"> Custom Salary</td>
                                                </tr>

                                                <!-- Added Parameters -->

                                                <tr>
                                                    <td height="35px;" width="200px;">
                                                        <p class="form-label">Fixed Allowance</p>
                                                    </td>
                                                    <td><input id="attal" type="text" name="attal" class="input-text"
                                                            value="0"></td>
                                                </tr>

                                                <tr>
                                                    <td height="35px;" width="200px;">
                                                        <p class="form-label">Vehicle Allowance</p>
                                                    </td>
                                                    <td><input id="tral" type="text" name="tral" class="input-text"
                                                            value="0"></td>
                                                </tr>

                                                <tr>
                                                    <td height="35px;" width="200px;">
                                                        <p class="form-label">Other Allowances</p>
                                                    </td>
                                                    <td><input id="othal" type="text" name="othal" class="input-text"
                                                            value="0"></td>
                                                </tr>

                                                <tr>
                                                    <td height="35px;" width="200px;"><input type="button"
                                                            onclick="addDLoc()" class="input-text"
                                                            value="New Allowances"
                                                            style="margin-left: 20px; width: 120px;"></td>
                                                    <td>
                                                    </td>
                                                </tr>
                                        </table>

                                        <div id="etraAllow">
                                            <table>
                                                <tr>
                                                    <td height='35px;' width='200px;'><input id='allowNa1' type='text'
                                                            name='allowNa1' class='form-label' /></td>
                                                    <td><input id='alowPR1' type='text' name='alowPR1'
                                                            class='input-text' /></td>
                                                </tr>
                                                <tr>
                                                    <td height='35px;' width='200px;'><input id='allowNa2' type='text'
                                                            name='allowNa2' class='form-label' /></td>
                                                    <td><input id='alowPR2' type='text' name='alowPR2'
                                                            class='input-text' /></td>
                                                </tr>
                                                <tr>
                                                    <td height='35px;' width='200px;'><input id='allowNa3' type='text'
                                                            name='allowNa3' class='form-label' /></td>
                                                    <td><input id='alowPR3' type='text' name='alowPR3'
                                                            class='input-text' /></td>
                                                </tr>
                                                <tr>
                                                    <td height='35px;' width='200px;'><input id='allowNa4' type='text'
                                                            name='allowNa4' class='form-label' /></td>
                                                    <td><input id='alowPR4' type='text' name='alowPR4'
                                                            class='input-text' /></td>
                                                </tr>
                                                <tr>
                                                    <td height='35px;' width='200px;'><input id='allowNa5' type='text'
                                                            name='allowNa5' class='form-label' /></td>
                                                    <td><input id='alowPR5' type='text' name='alowPR5'
                                                            class='input-text' /></td>
                                                </tr>
                                            </table>

                                        </div>
                                        <table>
                                            <tr>
                                                <td height="35px;" width="225px;">
                                                    <p class="form-label">PAYEE Tax</p>
                                                </td>
                                                <td><input id="ptax" type="text" name="ptax" class="input-text"
                                                        value="0"></td>
                                            </tr>

                                            <tr>
                                                <td height="35px;" width="225px;">
                                                    <p class="form-label">Bank Name With Branch</p>
                                                </td>
                                                <td><input id="bbr" type="text" name="bbr" class="input-text"></td>
                                            </tr>

                                            <tr>
                                                <td height="35px;" width="225px;">
                                                    <p class="form-label">Bank Acc. NO</p>
                                                </td>
                                                <td><input id="bno" type="text" name="bno" class="input-text"></td>
                                            </tr>

                                            <tr id="fixleave">
                                                <td height="35px;" width="225px;">
                                                    <p class="form-label">Fix Leaves</p>
                                                </td>
                                                <td><input id="fleaves" type="text" name="fleaves" class="input-text"
                                                        value="0"></td>
                                            </tr>

                                            <tr id="casualleave">
                                                <td height="35px;" width="225px;">
                                                    <p class="form-label">Casual Leaves</p>
                                                    +
                                                </td>
                                                <td><input id="cleaves" type="text" name="cleaves" class="input-text"
                                                        value="0"></td>
                                            </tr>

                                            <tr>
                                                <td height="35px;" width="225px;">&nbsp;</td>
                                                <td></td>
                                            </tr>

                                            <tr>
                                                <td height="35px;" width="225px;">
                                                    <p class="form-label">Employee Type</p>
                                                </td>
                                                <td>
                                                    <select id="etype" name="etype" class="select-basic"
                                                        style="width: 186px">
                                                        <?php
                                                        $query = "select * from employeetype";
                                                        $res = Search($query);
                                                        while ($result = mysqli_fetch_assoc($res)) {
                                                        ?>
                                                        <option value="<?php echo $result["etid"]; ?>">
                                                            <?php echo $result["name"]; ?> </option>
                                                        <?php } ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="35px;" width="225px;">
                                                    <p class="form-label">Authorized Person</p>
                                                </td>
                                                <td>
                                                    <select id="authperson" name="authperson" class="select-basic"
                                                        style="width: 186px">
                                                        <option value="0"></option>
                                                        <option value="5">Mrs. Jayalauxmi Dias</option>
                                                        <option value="4">Mr. Patabandige Supun Indira Peter</option>
                                                        <option value="57">Mr. Wilson Joseph</option>
                                                        <option value="66">Mrs. Mala Seelawansa</option>
                                                        <option value="73">Mr. Mahendra Kumara Bogamuwa</option>
                                                        <option value="75">Mrs. Damayanthi Attanayake</option>
                                                        <option value="80">Mr. Jothi Pitchai</option>
                                                        <option value="53">Mr. Sangili Samin</option>
                                                        <option value="96">Miss. Naidasinghe Mudiyanselage Anusha
                                                            Dilhani Abeykoon</option>
                                                        <option value="51">Mr. Kadirnesan Chandra Kumar</option>

                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="35px;" width="225px;">
                                                    <p class="form-label">2<sup>nd</sup>Authorized Person</p>
                                                </td>
                                                <td>
                                                    <select id="sec_authperson" name="sec_authperson"
                                                        class="select-basic" style="width: 186px">
                                                        <option value="0"></option>
                                                        <option value="5">Mrs. Jayalauxmi Dias</option>
                                                        <option value="4">Mr. Patabandige Supun Indira Peter</option>
                                                        <option value="57">Mr. Wilson Joseph</option>
                                                        <option value="66">Mrs. Mala Seelawansa</option>
                                                        <option value="73">Mr. Mahendra Kumara Bogamuwa</option>
                                                        <option value="75">Mrs. Damayanthi Attanayake</option>
                                                        <option value="80">Mr. Jothi Pitchai</option>
                                                        <option value="53">Mr. Sangili Samin</option>
                                                        <option value="96">Miss. Naidasinghe Mudiyanselage Anusha
                                                            Dilhani Abeykoon</option>
                                                        <option value="51">Mr. Kadirnesan Chandra Kumar</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="35px;" width="225px;">
                                                    <p class="form-label">Employee Status</p>
                                                </td>
                                                <td><select id="status" name="status" class="select-basic"
                                                        style="width: 186px">
                                                        <option value="1">Active</option>
                                                        <option value="0">Not-Active</option>
                                                    </select></td>
                                            </tr>
                                            <tr>
                                                <td height="35px;" width="225px;">
                                                    <p class="form-label">Employee Act</p>
                                                </td>
                                                <td><select name="empact" id="empact" class="select-basic"
                                                        style="width: 186px">
                                                        <option value="Shop and Office">Shop and Office</option>
                                                        <option value="Wages Board">Wages Board</option>
                                                        <option value="Driver Wages Board">Driver Wages Board</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td height="35px;" width="225px;">
                                                    <p class="form-label">Reg. Date</p>
                                                </td>
                                                <td><input id="rdate" type="date" name="rdate" class="input-text"
                                                        style="width: 186px"></td>
                                            </tr> <!-- onchange="checkDateValidity()"  2024-05-08 thihan requested -->
                                            <tr>
                                                <td height="35px;" width="225px;">
                                                    <p class="form-label">Probation Period End Date</p>
                                                </td>
                                                <td><input id="probdate" type="date" name="probdate" class="input-text"
                                                        style="width: 186px"></td>
                                            </tr>

                                            <tr>
                                                <td height="35px;" width="225px;">
                                                    <p class="form-label">EPF/ETF Entitle Date</p>
                                                </td>
                                                <td><input id="epf_entitle_date" type="date" name="epf_entitle_date"
                                                        class="input-text" style="width: 186px"></td>
                                            </tr>

                                            <tr>
                                                <td height="35px;" width="225px;">
                                                    <p class="form-label">Working Type</p>
                                                </td>
                                                <td><select name="wrk_typ" id="wrk_typ" class="select-basic"
                                                        style="width: 186px" onchange="changeWorkingTypeMeth()">
                                                        <option value="1">Hourly Basis</option>
                                                        <option value="2">Shift Basis</option>
                                                    </select>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td height="35px;" width="225px;">
                                                    <p class="form-label">Privileges Type</p>
                                                </td>
                                                <td>
                                                    <select id="privtype" name="privtype" class="select-basic"
                                                        style="width: 186px">
                                                        <?php
                                                        $query = "select * from feature_category where isactive='1'";
                                                        $res = Search($query);
                                                        while ($result = mysqli_fetch_assoc($res)) {
                                                        ?>
                                                        <option value="<?php echo $result["fcid"]; ?>">
                                                            <?php echo $result["name"]; ?> </option>
                                                        <?php } ?>
                                                        <option value="0">No Privileges</option>
                                                    </select>
                                                </td>
                                            </tr>

                                        </table>

                                        <table
                                            style="margin-top: 10px;margin-left: 30px; float: right;margin-bottom: 10px;">
                                            <tr>
                                                <td>
                                                    <input type="hidden" name="picupdate" id="picupdate">
                                                    <input type="hidden" name="picupdatecheck" id="picupdatecheck">
                                                    <input type="submit" name="submit" id="savebtn" style="width: 100px"
                                                        class="btn btn-primary" value="Save" />
                                                </td>
                                                <td id="req_upd">
                                                    <input type="submit" name="submit" style="width: 100px"
                                                        class="btn btn-warning" value="Update" />
                                                </td>
                                                <td id="req_trm">
                                                    <input type="submit" name="submit" style="width: 100px"
                                                        class="btn btn-danger" value="Terminate" />
                                                </td>

                                            </tr>
                                            <tr>
                                                <!-- <td>
                                        <input type="reset" style="width: 100px" class="btn btn-success" value="Refresh" onclick="refresh()"/>
                                    </td> -->
                                                <!-- <td>
                                        <input type="button" style="width: 100px" data-popup-open="popup-1" class="btn btn-success" value="Privileges" onclick="loadPrivs()"/>
                                    </td> -->
                                                <td id="req_ld">
                                                    <input type="button" style="width: 100px" class="btn btn-success"
                                                        value="Login Details" onclick="loadLDetails()" />
                                                </td>

                                                <td id="req_sd">
                                                    <input type="button" style="width: 100px" class="btn btn-success"
                                                        value="Skills Details" onclick="skillsDetails()" />
                                                </td>
                                            </tr>
                                            <tr id="req_tp">

                                                <!-- 
                                                <td colspan="2" id="time_profile">
                                                    <input type="button" style="width: 230px" class="btn btn-dark"
                                                        value="Manage Time Profile" onclick="loadTimeProf()" />
                                                </td> -->
                                                <!-- <td>
                                        <input type="button" style="width: 100px" class="btn btn-danger" value="Resign" onclick="resignDetails()"/>
                                    </td> -->
                                            </tr>
                                        </table>
                                        </form>

                                        <div class="popup" data-popup="popup-1">
                                            <div class="popup-inner" style="height: 650px; overflow-y: scroll;">
                                                <div id="show_saved_img" align="right">
                                                    <a style="float: right;margin-right: 30px; margin-top: 30px;"
                                                        class="popup-close" data-popup-close="popup-1"
                                                        onclick="closePrivs()" href="#">x</a>
                                                </div>
                                                <h2>Employee Privileges</h2>
                                                <p>Please select features that can be granted by this employee.</p>

                                                <div id="privs">
                                                    <?php
                                                    $query = "select fid from features where isactive='1'";
                                                    $res = Search($query);
                                                    while ($result = mysqli_fetch_assoc($res)) {
                                                    ?>
                                                    <input type="checkbox" id="<?php echo $result["fid"]; ?>"
                                                        <?php echo $result["name"]; ?>><br />
                                                    <?php } ?>
                                                </div>

                                                <p><button class="btn btn-default submit" style="float: right;"
                                                        onclick="savePrivs()">Save </button></p></br>

                                            </div>
                                        </div>


                                        <!-- The Modal -->
                                        <div id="myModal" class="modal">

                                            <!-- Modal content -->
                                            <div class="modal-content">
                                                <span class="close" onclick="CloseModel()">&times;</span>

                                                <table>
                                                    <tr>
                                                        <td colspan="4" align="center">
                                                            <h2>Employee Time Profile</h2>
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td colspan="4" align="center">
                                                            <p>Please fill this time records.</p>
                                                        </td>
                                                    </tr>

                                                    <tr style="border-bottom: 1px solid black;">
                                                        <td>Working Time <b>(WEEK DAYS)</b></td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td height="35px;" width="200px;">
                                                            <p class="form-label">Working In Time</p>
                                                        </td>
                                                        <td><input id="wrkintime" type="time" name="wrkintime"
                                                                class="input-text" style="width: 182px"></td>
                                                        <td height="35px;" width="200px;">
                                                            <p class="form-label">Working Out Time</p>
                                                        </td>
                                                        <td><input id="wrkouttime" type="time" name="wrkouttime"
                                                                class="input-text" style="width: 182px">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr style="border-bottom: 1px solid black;">
                                                        <td>Late Calculate Time For Working Time <b>(WEEK DAYS)</b></td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td height="35px;" width="200px;">
                                                            <p class="form-label">Late Time</p>
                                                        </td>
                                                        <td><input id="wrkLate" type="time" name="wrkLate"
                                                                class="input-text" style="width: 182px"></td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr style="border-bottom: 1px solid black;">
                                                        <td>Working Time <b>(WEEKENDS)</b></td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td height="35px;" width="200px;">
                                                            <p class="form-label">Working In Time</p>
                                                        </td>
                                                        <td><input id="wrkintimeSat" type="time" name="wrkintimeSat"
                                                                class="input-text" style="width: 182px"></td>
                                                        <td height="35px;" width="200px;">
                                                            <p class="form-label">Working Out Time</p>
                                                        </td>
                                                        <td><input id="wrkouttimeSat" type="time" name="wrkouttimeSat"
                                                                class="input-text" style="width: 182px">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr style="border-bottom: 1px solid black;">
                                                        <td>Late Calculate Time For Working Time <b>(WEEKENDS)</b></td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td height="35px;" width="200px;">
                                                            <p class="form-label">Late Time</p>
                                                        </td>
                                                        <td><input id="wrkEndLate" type="time" name="wrkEndLate"
                                                                class="input-text" style="width: 182px"></td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr style="border-bottom: 1px solid black;">
                                                        <td>Half Day Time</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td height="35px;" width="200px;">
                                                            <p class="form-label">Half Day Start (Morning)</p>
                                                        </td>
                                                        <td><input id="halfmorningstart" type="time"
                                                                name="halfmorningstart" class="input-text"
                                                                style="width: 182px"></td>
                                                        <td height="35px;" width="200px;">
                                                            <p class="form-label">Half Day End (Morning)</p>
                                                        </td>
                                                        <td><input id="halfmorningend" type="time" name="halfmorningend"
                                                                class="input-text" style="width: 182px"></td>
                                                    </tr>

                                                    <tr>
                                                        <td height="35px;" width="200px;">
                                                            <p class="form-label">Half Day Start (Evening)</p>
                                                        </td>
                                                        <td><input id="halfeveningstart" type="time"
                                                                name="halfeveningstart" class="input-text"
                                                                style="width: 182px"></td>
                                                        <td height="35px;" width="200px;">
                                                            <p class="form-label">Half Day End (Evening)</p>
                                                        </td>
                                                        <td><input id="halfeveningend" type="time" name="halfeveningend"
                                                                class="input-text" style="width: 182px"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr style="border-bottom: 1px solid black;">
                                                        <td>Late Calculate Time For Halfday Morning</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>Late Calculate Time For Halfday Evening</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td height="35px;" width="200px;">
                                                            <p class="form-label">Late Time</p>
                                                        </td>
                                                        <td><input id="halfMLate" type="time" name="halfMLate"
                                                                class="input-text" style="width: 182px"></td>
                                                        <td height="35px;" width="200px;">
                                                            <p class="form-label">Late Time</p>
                                                        </td>
                                                        <td><input id="halfELate" type="time" name="halfELate"
                                                                class="input-text" style="width: 182px">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr style="border-bottom: 1px solid black;">
                                                        <td>Short Leave Time</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td height="35px;" width="200px;">
                                                            <p class="form-label">Short Leave Start (Morning)</p>
                                                        </td>
                                                        <td><input id="shortmorningstart" type="time"
                                                                name="shortmorningstart" class="input-text"
                                                                style="width: 182px"></td>
                                                        <td height="35px;" width="200px;">
                                                            <p class="form-label">Short Leave End (Morning)</p>
                                                        </td>
                                                        <td><input id="shortmorningend" type="time"
                                                                name="shortmorningend" class="input-text"
                                                                style="width: 182px"></td>
                                                    </tr>

                                                    <tr>
                                                        <td height="35px;" width="200px;">
                                                            <p class="form-label">Short Leave Start (Evening)</p>
                                                        </td>
                                                        <td><input id="shorteveningstart" type="time"
                                                                name="shorteveningstart" class="input-text"
                                                                style="width: 182px"></td>
                                                        <td height="35px;" width="200px;">
                                                            <p class="form-label">Short Leave End (Evening)</p>
                                                        </td>
                                                        <td><input id="shorteveningend" type="time"
                                                                name="shorteveningend" class="input-text"
                                                                style="width: 182px"></td>
                                                    </tr>
                                                    <tr>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr style="border-bottom: 1px solid black;">
                                                        <td>Late Calculate Time For Short Leave Morning</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>Late Calculate Time For Short Leave Evening</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td height="35px;" width="200px;">
                                                            <p class="form-label">Late Time</p>
                                                        </td>
                                                        <td><input id="shrtMLate" type="time" name="shrtMLate"
                                                                class="input-text" style="width: 182px"></td>
                                                        <td height="35px;" width="200px;">
                                                            <p class="form-label">Late Time</p>
                                                        </td>
                                                        <td><input id="shrtELate" type="time" name="shrtELate"
                                                                class="input-text" style="width: 182px">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                    </tr>
                                                    <tr>
                                                        <td id="profsave"><button
                                                                style="margin-top: 10px; width: 150px;" value="Save"
                                                                id="settingsAdd" onclick="inputSettingsData()"
                                                                class="btn btn-success">Save</button></td>
                                                        <td id="profupdate"><button
                                                                style="margin-top: 10px; width: 150px;" value="Update"
                                                                id="settingsUpdate" onclick="updateSettingsData()"
                                                                class="btn btn-warning">Update</button></td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td>&nbsp;&nbsp;</td>
                                                    </tr>
                                                </table>
                                                <input id="swtid" type="text" name="swtid" class="input-text"
                                                    hidden="hidden">
                                            </div>

                                        </div>

                                        <?php
                                        if (isset($_GET["error"])) {
                                            // echo "<p>" . $_GET["error"] . "</p>";

                                            if ($_GET["error"] == "1") {
                                        ?> <script>
                                        alert("Employee Exist. You Can't Add Same NIC Again!");
                                        </script><?php
                                                        }

                                                        if ($_GET["error"] == "2") {
                                                            ?> <script>
                                        alert("Employee Successfully Saved!");
                                        window.location.href = "../Views/emp_manage.php";
                                        </script><?php
                                                        }

                                                        if ($_GET["error"] == "3") {
                                                            ?> <script>
                                        alert("Error!");
                                        window.location.href = "../Views/emp_manage.php";
                                        </script><?php
                                                        }

                                                        if ($_GET["error"] == "4") {
                                                            ?> <script>
                                        alert("Employee Successfully Updated!");
                                        window.location.href = "../Views/emp_manage.php";
                                        </script><?php
                                                        }

                                                        if ($_GET["error"] == "5") {
                                                            ?> <script>
                                        alert("Employee Successfully Terminated!");
                                        window.location.href = "../Views/emp_manage.php";
                                        </script><?php
                                                        }
                                                    }
                                                            ?>


                                        <div id="logindetails">
                                            </br>
                                            <h2>Manage Login Details</h2>
                                            <p>Please,fill the following details</p>
                                            <table>
                                                <tr>
                                                    <td height="35px;" width="200px;">
                                                        <p class="form-label">Username</p>
                                                    </td>
                                                    <td><input id="usr" type="text" name="usr" class="input-text"></td>
                                                </tr>
                                                <tr>
                                                    <td height="35px;" width="200px;">
                                                        <p class="form-label">Password</p>
                                                    </td>
                                                    <td><input id="pwrd" type="password" name="pwrd" class="input-text">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td height="35px;" width="200px;">
                                                        <p class="form-label">Favorite color</p>
                                                    </td>
                                                    <td><input id="qone" type="text" name="qone" class="input-text">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td height="35px;" width="200px;">
                                                        <p class="form-label">Birth City</p>
                                                    </td>
                                                    <td><input id="qtwo" type="text" name="qtwo" class="input-text">
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td height="35px;" width="200px;"></td>
                                                    <td><input type="button" class="btn btn-primary"
                                                            style="margin-top: 10px; width: 150px; float: right;"
                                                            name="logindetails" onclick="saveLogins()"
                                                            value="Save Details"></td>
                                                </tr>

                                            </table>
                                        </div>


                                        <form id="form2" action="../Controller/emp_manage.php" method="POST"
                                            enctype="multipart/form-data">
                                            <div id="skilsdetailsupload">
                                                </br>
                                                <h2>Add Employee Skills Details</h2>
                                                <p>Please,add skills details</p>
                                                <table>
                                                    <tr>
                                                        <td height="35px;" width="200px;">
                                                            <p class="form-label">Skills Description</p>
                                                        </td>
                                                        <td><textarea id="skilldes"
                                                                style="margin-bottom:5px;margin-top: 5px;width: 186px"
                                                                class="text-area" cols="22" name="skilldes"
                                                                rows="3"></textarea></td>
                                                    </tr>
                                                    <tr>
                                                        <td height="35px;" width="200px;">
                                                            <p class="form-label">File Upload</p>
                                                        </td>
                                                        <td><input type="file" name="filedata[]" class="input-text"
                                                                style="width: 180px;" id="filedata" multiple></td>
                                                        <td><button name="downloadfiles" id="downloadfiles"
                                                                title="Download Files"
                                                                style=" border-radius: 6px; background-color: #ffccff;"><i
                                                                    class="fa fa-download"
                                                                    style="float: next; cursor: pointer"></i></button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><input type="submit" class="btn btn-primary"
                                                                style="margin-top: 10px; width: 150px; float: right;"
                                                                name="skilldetails" value="Save"></td>
                                                        <td><input type="submit" class="btn btn-warning"
                                                                style="margin-top: 10px; width: 150px; float: right;"
                                                                name="skilldetailsupdate" value="Update"></td>
                                                    </tr>

                                                    <tr>
                                                        <td><input type="text" name="empno" id="empno" hidden="hidden">
                                                        </td>
                                                    </tr>

                                                </table></br>

                                                <table class='table table-striped'>
                                                    <tr>
                                                        <th>Files</th>
                                                    </tr>
                                                    <tbody id="filestable"></tbody>
                                                </table>

                                                <?php

                                                if (isset($_GET['msg']) && $_GET['msg'] == 1) {
                                                ?> <script>
                                                alert("Please Select Employee...");
                                                </script><?php
                                                            } else if (isset($_GET['msg']) && $_GET['msg'] == 2) {
                                                                ?> <script>
                                                alert("Please Add Files..");
                                                </script><?php
                                                            } else if (isset($_GET['msg']) && $_GET['msg'] == 3) {
                                                                ?> <script>
                                                alert("Data Insert Successfully...");
                                                window.location.href = "../Views/emp_manage.php";
                                                </script><?php
                                                            } else if (isset($_GET['msg']) && $_GET['msg'] == 4) {
                                                                ?> <script>
                                                alert("Data Insert Unsuccessfully...");
                                                window.location.href = "../Views/emp_manage.php";
                                                </script><?php
                                                            } else if (isset($_GET['msg']) && $_GET['msg'] == 5) {
                                                                ?> <script>
                                                alert("Files Have Unknown File Formats...");
                                                </script><?php
                                                            } else if (isset($_GET['msg']) && $_GET['msg'] == 6) {
                                                                ?> <script>
                                                alert("Files Already Added...");
                                                </script><?php
                                                            } else if (isset($_GET['msg']) && $_GET['msg'] == 7) {
                                                                ?> <script>
                                                alert("Data Update Successfully...");
                                                window.location.href = "../Views/emp_manage.php";
                                                </script><?php
                                                            } else if (isset($_GET['msg']) && $_GET['msg'] == 8) {
                                                                ?> <script>
                                                alert("Data Update Unsuccessfully...");
                                                window.location.href = "../Views/emp_manage.php";
                                                </script><?php
                                                            } else if (isset($_GET['msg']) && $_GET['msg'] == 9) {
                                                                ?> <script>
                                                alert("No Files Have Been Added To This Employee");
                                                </script><?php
                                                            }

                                                                ?>
                                            </div>
                                        </form>
                                    </td>
                                    <td style="color: white">AAA</td>
                                    <td style="background-color: silver; width: 2px; border: 2px solid #edeff2;"></td>
                                    <td style="color: white">AAA</td>
                                    <td valign="top">
                                        <p class="form-label" style="border-right:none; border-radius:0;">
                                            Name : <input type="text" name="sfn" id="sfn" style="width: 100px"
                                                class="input-text" />
                                            &nbsp;&nbsp;
                                            <!-- Last Name : <input type="text" name="sln" id="sln" style="width: 100px" class="input-text">
                                &nbsp;&nbsp; -->
                                            EMP NO : <input type="text" name="epfno" id="epfno" style="width: 100px"
                                                class="input-text">
                                            &nbsp;&nbsp;
                                            Emp.Type :
                                            <select id="stype" class="select-basic" style="width: 100px;">
                                                <option value="%">All</option>
                                                <?php
                                                $query = "select * from employeetype";
                                                $res = Search($query);
                                                while ($result = mysqli_fetch_assoc($res)) {
                                                ?>
                                                <option value="<?php echo $result["etid"]; ?>">
                                                    <?php echo $result["name"]; ?> </option>
                                                <?php } ?>
                                            </select>

                                            <!-- &nbsp;&nbsp;
                                Branch : 
                                <select id="sposi" class="select-basic" style="width: 100px;">
                                    <option value="%">All</option>
                                    <?php
                                    $query = "select * from position group by name";
                                    $res = Search($query);
                                    while ($result = mysqli_fetch_assoc($res)) {
                                    ?>
                                        <option value="<?php echo $result["pid"]; ?>"> <?php echo $result["name"]; ?> </option>
                                    <?php } ?>
                                </select> -->

                                            &nbsp;&nbsp;
                                            Department :
                                            <select id="deptdata" class="select-basic" style="width: 100px;">
                                                <option value="%">All</option>
                                                <?php
                                                $query = "select * from emp_department order by did";
                                                $res = Search($query);
                                                while ($result = mysqli_fetch_assoc($res)) {
                                                ?>
                                                <option value="<?php echo $result["did"]; ?>">
                                                    <?php echo $result["name"]; ?> </option>
                                                <?php } ?>
                                            </select>
                                            &nbsp;&nbsp;
                                            <br />
                                            <br />

                                            Status :
                                            <select id="active" class="select-basic" style="width: 100px;">
                                                <option value="1">Active</option>
                                                <option value="0">Not-Active</option>
                                            </select>
                                            &nbsp;&nbsp;
                                            Search <img src="../Icons/search.png" onclick="loadTable()"
                                                style="cursor: pointer">

                                        </p>

                                        <p>&nbsp;</p>
                                        <div id="tdata" style="height: 950px; overflow-y: scroll;width: 950px;">


                                        </div>
                                        </br>
                                        <hr>
                                        <!--  <div>
                        <table style="margin-top: 10px;margin-bottom: 10px;">
                            <tr>
                                <td colspan="2"><h5 style="font-weight: bold;">- Details Change Request Section -</h5></td>
                            </tr>
                            <tr>
                                <td height="35px;" width="200px;"><p class="form-label">Request Type</p></td>
                                <td><select name="req_typ" id="req_typ" class="select-basic" style="width: 186px">
                                        <option value="1">Update</option>
                                        <option value="2">Terminate</option>
                                        <option value="3">Add Login Details</option>
                                        <option value="4">Add Skills Details</option>
                                        <option value="5">Manage Time Profile</option> -->
                                        <!-- <option value="6">Manage Employee Types</option>
                                        <option value="7">Manage Employee Branches</option>
                                        <option value="8">Manage Employee Departments</option> -->
                                        <!-- </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <input type="button" id="reqbtn" style="width: 230px" class="btn btn-info" value="Request To Change Records" onclick="send_Request()" />
                                </td>
                            </tr>
                        </table>
                    </div> -->
                                        </br>

                                        <?php if (in_array("Category Management Section (Sub Privilege)", $privs)) { ?>
                                        <p style="color: black; font-weight: bold; font-size: 16px;"><input
                                                type="checkbox" name="cat_manage" style="width: 18px; height: 18px;"
                                                id="cat_manage"
                                                onchange="ChangeCategoryDataManage()">&nbsp;&nbsp;Category Management
                                            Section</p>
                                        <?php } ?>

                                        </br>
                                        <div id="Categ_Manage">
                                            <h3>Enter Categories &nbsp; <small>Category Management</small></h3>

                                            <table style="margin-left: 20px;" cellspacing="50">
                                                <tr>
                                                    <td>
                                                        <h2><small>Manage Employee Types</small></h2>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td height="15px">
                                                        <table>
                                                            <tr>
                                                                <td><input type="hidden" id="tpid"></td>
                                                                <td> <input type="text" id="emptype" name="employeetype"
                                                                        style="width: 200px;" /></td>
                                                                <td>&nbsp;&nbsp;&nbsp;</td>
                                                                <td> </td>
                                                                <td>&nbsp;&nbsp;&nbsp;</td>
                                                                <td><input type="button" id="addtype" value="Add"
                                                                        onclick="inserttype()" class="btn btn-primary">
                                                                </td>
                                                                <td>&nbsp;&nbsp;&nbsp;</td>
                                                                <td><input type="button" id="updatetype" value="Update"
                                                                        onclick="updatetype()" class="btn btn-warning">
                                                                </td>
                                                            </tr>
                                                        </table>
                                                        <table>
                                                            <tr height="35px">
                                                                <td>
                                                                    <div style="overflow:scroll; height:200px; width: 350px;"
                                                                        id=emptypetable></div>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <hr />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <h2><small>Manage Employee Branches</small></h2>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td height="15px">
                                                        <table>
                                                            <tr>
                                                                <td><input type="hidden" id="dipid"></td>
                                                                <td> <input type="text" id="dipname"
                                                                        name="dipartmentname" style="width: 200px;" />
                                                                </td>
                                                                <td>&nbsp;&nbsp;&nbsp;</td>
                                                                <td> </td>
                                                                <td>&nbsp;&nbsp;&nbsp;</td>
                                                                <td><input type="button" id="adddip" value="Add"
                                                                        onclick="insertBranch()"
                                                                        class="btn btn-primary"></td>
                                                                <td>&nbsp;&nbsp;&nbsp;</td>
                                                                <!-- <td><input type="button" id="updip" value="Update"
                                                                        onclick="updateBranch()"
                                                                        class="btn btn-warning"></td> -->
                                                            </tr>
                                                        </table>
                                                        <table>
                                                            <tr height="35px">
                                                                <td>
                                                                    <div style="overflow:scroll; height:200px; width: 350px;"
                                                                        id=branchtable></div>

                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <hr />
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <h2><small>Manage Employee Departments</small></h2>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td height="15px">
                                                        <table>
                                                            <tr>
                                                                <td><input type="hidden" id="empdid"></td>
                                                                <td> <input type="text" id="emp_dipname"
                                                                        name="emp_dipname" style="width: 200px;" /></td>
                                                                <td>&nbsp;&nbsp;&nbsp;</td>
                                                                <td> </td>
                                                                <td>&nbsp;&nbsp;&nbsp;</td>
                                                                <td><input type="button" id="add_emp_did" value="Add"
                                                                        onclick="insertDepartment()"
                                                                        class="btn btn-primary"></td>
                                                                <td>&nbsp;&nbsp;&nbsp;</td>
                                                                <td><input type="button" id="upd_emp_did" value="Update"
                                                                        onclick="updateDepartment()"
                                                                        class="btn btn-warning"></td>
                                                            </tr>
                                                        </table>
                                                        <table>
                                                            <tr height="35px">
                                                                <td>
                                                                    <div style="overflow:scroll; height:200px; width: 350px;"
                                                                        id=emp_dept_table></div>

                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <!-- <tr><td><hr/></td></tr>
                            <tr>
                                <td>
                                    <h2><small>Manage Employee's Shift Records</small></h2>
                                </td>
                            </tr>
                            <tr>
                                <td height="15px"> 
                                    <table >
                                        <tr>
                                            <td><input type="hidden" id="shiftid" ></td>  
                                            <td> <input type="text" id="shname" name="shname" style="width: 110px;"/>&nbsp;&nbsp;</td>                   
                                            <td><input type="time" id="shin" name="shin" style="width: 100px;"/>&nbsp;&nbsp;</td>
                                            <td><input type="time" id="shout" name="shout" style="width: 100px;"/></td>
                                            <td>&nbsp;&nbsp;&nbsp;</td>
                                            <td><input type="button" id="addshift" value="Add" onclick="insertShiftTypes()" class="btn btn-primary" ></td>
                                            <td>&nbsp;&nbsp;&nbsp;</td>
                                            <td><input type="button" id="upshift" value="Update" onclick="updateShiftTypes()" class="btn btn-warning" ></td>
                                        </tr>
                                    </table>
                                    <table >
                                        <tr height="35px">  
                                            <td >
                                               <div style="overflow:scroll; height:200px; width: 350px;" id=shifttable></div>  

                                            </td>                         
                                        </tr>
                                    </table>
                                </td>
                            </tr> -->
                                            </table>
                                        </div>


                                    </td>
                                </tr>
                            </table>

                        </div>
                    </div>

                </div>
            </div>
        </div>

</body>
<?php include("../Contains/footer.php");
?>

</html>