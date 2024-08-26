<?php
error_reporting(0);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
session_start();
include '../DB/DB.php';
$DB = new Database();

if (isset($_POST["submit"])) {
    if ($_POST["submit"] == "Save") {

        $customSal = "0";
        if (isset($_POST["cussal"])) {
            $customSal = $_POST["psal"];
        }

        if ($_POST["psal"] == "") {
            $customSal = "0";
        }

        $query = "select uid from user where nic = '" . $_POST["nic"] . "' and isactive='1'";
        $res = Search($query);
        if ($result = mysqli_fetch_assoc($res)) {
            header("Location: ../Views/emp_manage.php?error=1");
        } else {

            //address
            $aid;
            $queryA = "select aid from address where address = '" . $_POST["add"] . "'";
            $resA = Search($queryA);
            if ($result = mysqli_fetch_assoc($resA)) {
                $aid = $result["aid"];
            } else {
                $queryAd = "insert into address(address) values('" . $_POST["add"] . "')";
                SUD($queryAd);
                $aid = $DB->getConnection()->insert_id;
            }

            //Permanent address
            $paid;
            $queryA = "select aid from address where address = '" . $_POST["padd"] . "'";
            $resAp = Search($queryA);
            if ($result = mysqli_fetch_assoc($resAp)) {
                $paid = $result["aid"];
            } else {
                $queryAdp = "insert into address(address) values('" . $_POST["padd"] . "')";
                SUD($queryAdp);
                $paid = $DB->getConnection()->insert_id;
            }

            //get uploaded file. Scan for newest uploaded file.
            $imageURL = "";
            if (isset($_POST['userpic']) && $_POST['userpic'] == 'true') {
                $directoryPath = '../Images/UserPhotos/';
                $imageURL = "";
                $directoryPath = rtrim($directoryPath, '/');
                $max = ['path' => null, 'timestamp' => 0];
                foreach (scandir($directoryPath, SCANDIR_SORT_NONE) as $file) {
                    $path = $directoryPath . '/' . $file;
                    if (!is_file($path)) {
                        continue;
                    }
                    $timestamp = filemtime($path);
                    if ($timestamp > $max['timestamp']) {
                        $max['path'] = $path;
                        $max['timestamp'] = $timestamp;
                    }
                }
                $imageURL = $max['path'];
            }

            $emp_post = str_replace("getbasicfrompandg", "", $_POST["postid"]);
            $emp_post = $_POST["postid"];

            $resApxAB = Search("select grade_gid from emppost where grade_gid = '" . $_POST["grade"] . "' and position_pid = '" . $_POST["posi"] . "'");
            if ($resultxAB = mysqli_fetch_assoc($resApxAB)) {
            } else {
                $insertQueryX = "insert into emppost (basicsal, grade_gid, position_pid) values('" . $customSal . "','" . $_POST["grade"] . "','" . $_POST["posi"] . "')";
                $returnX = SUD($insertQueryX);
            }

            $queryAx = "SELECT id FROM emppost where grade_gid = '" . $_POST["grade"] . "' and position_pid = '" . $_POST["posi"] . "'";
            $resApx = Search($queryAx);
            if ($resultx = mysqli_fetch_assoc($resApx)) {
                $emp_post = $resultx["id"];
            }

            if (!empty($_POST["rdate"]) && $_POST["rdate"] != "0000-00-00") {
                $futureDate = date('Y-m-d', strtotime('+1 year', strtotime($_POST["rdate"])));
                $registeredDate = $_POST["rdate"];
            } else {
                $futureDate = "0000-00-00";
                $registeredDate = "0000-00-00";
            }

            if (empty($_POST["probdate"]) || $_POST["probdate"] == "0000-00-00") {
                $probEndDate = "0000-00-00";
            } else {
                $probEndDate = $_POST["probdate"];
            }

            if (empty($_POST["epf_entitle_date"]) || $_POST["epf_entitle_date"] == "0000-00-00") {
                $epfEntitleDate = "0000-00-00";
            } else {
                $epfEntitleDate = $_POST["epf_entitle_date"];
            }

            if (empty($_POST["dob"]) || $_POST["dob"] == "0000-00-00") {
                $DOBDate = "0000-00-00";
            } else {
                $DOBDate = $_POST["dob"];
            }

            //insert User
            $insertQuery = "insert into user(fname,mname,lname,nic,tpno,lpno,dob,email,school,gender,presentSalary,expectedSalery,registerdDate,address,permanentAddress,MaritalStatus_idMaritalStatus,EmployeeType_etid,imageURL,isactive,jobcode,emppost_id,epfno, bankno, bank, payeetax,skill_descrip,emp_act,prob_end_date,auth_person_id,sec_auth_person_id,work_typ,epf_entitle_date,dept_id,first_year_leave_end_date,priv_typ,opd_claim_value,ipd_claim_value)"
                . " values('" . $_POST["fn"] . "','" . $_POST["cn"] . "','" . $_POST["full_n"] . "','" . $_POST["nic"] . "','" . $_POST["tpno"] . "','" . $_POST["lpno"] . "','" . $DOBDate . "','" . $_POST["email"] . "','" . $_POST["school"] . "','" . $_POST["gender"] . "','" . $customSal . "','0','" . $registeredDate . "','" . $aid . "','" . $paid . "','" . $_POST["mstatus"] . "','" . $_POST["etype"] . "','" . $imageURL . "','1','" . $_POST["jc"] . "','" . $emp_post . "','" . $_POST["epf"] . "','" . $_POST["bno"] . "','" . $_POST["bbr"] . "','" . $_POST["ptax"] . "','','" . $_POST["empact"] . "','" . $probEndDate . "','" . $_POST["authperson"] . "','" . $_POST["sec_authperson"] . "','" . $_POST["wrk_typ"] . "','" . $epfEntitleDate . "','" . $_POST["emp_dip"] . "','" . $futureDate . "','" . $_POST["privtype"] . "','" . $_POST["opd_claim"] . "','" . $_POST["ipd_claim"] . "')";
            $return = SUD($insertQuery);

            //save allowances
            $lastUID = Search("select MAX(uid) as uid from user");
            if ($resultA = mysqli_fetch_assoc($lastUID)) {
                //save attendence allowance
                $x = SUD("insert into user_has_allowances(uid, alwid,amount) values('" . $resultA["uid"] . "',(select alwid from allowances where name = 'Fixed Allowance'), '" . $_POST["attal"] . "')");
                //save travelling allownces
                $y = SUD("insert into user_has_allowances(uid, alwid,amount) values('" . $resultA["uid"] . "',(select alwid from allowances where name = 'Vehicle Allowance'), '" . $_POST["tral"] . "')");
                //save Other allownces
                $y = SUD("insert into user_has_allowances(uid, alwid,amount) values('" . $resultA["uid"] . "',(select alwid from allowances where name = 'Other Allowances'), '" . $_POST["othal"] . "')");


                $newPOST = $_POST;
                unset($newPOST["uid"]);
                unset($newPOST["jc"]);
                unset($newPOST["epf"]);
                unset($newPOST["fn"]);
                unset($newPOST["cn"]);
                unset($newPOST["full_n"]);
                unset($newPOST["nic"]);
                unset($newPOST["tpno"]);
                unset($newPOST["lpno"]);
                unset($newPOST["dob"]);
                unset($newPOST["email"]);
                unset($newPOST["add"]);
                unset($newPOST["padd"]);
                unset($newPOST["school"]);
                unset($newPOST["mstatus"]);
                unset($newPOST["gender"]);
                unset($newPOST["posi"]);
                unset($newPOST["grade"]);
                unset($newPOST["postid"]);
                unset($newPOST["attal"]);
                unset($newPOST["tral"]);
                unset($newPOST["othal"]);
                unset($newPOST["ptax"]);
                unset($newPOST["bno"]);
                unset($newPOST["bbr"]);
                unset($newPOST["fleaves"]);
                unset($newPOST["cleaves"]);
                unset($newPOST["esal"]);
                unset($newPOST["etype"]);
                unset($newPOST["status"]);
                unset($newPOST["empact"]);
                unset($newPOST["probdate"]);
                unset($newPOST["rdate"]);
                unset($newPOST["picupdate"]);
                unset($newPOST["picupdatecheck"]);
                unset($newPOST["submit"]);
                unset($newPOST["file"]);
                unset($newPOST["psal"]);
                unset($newPOST["cussal"]);
                unset($newPOST["alowPR1"]);
                unset($newPOST["alowPR2"]);
                unset($newPOST["alowPR3"]);
                unset($newPOST["alowPR4"]);
                unset($newPOST["alowPR5"]);
                unset($newPOST["authperson"]);
                unset($newPOST["sec_authperson"]);
                unset($newPOST["wrk_typ"]);
                unset($newPOST["epf_entitle_date"]);
                unset($newPOST["emp_dip"]);
                unset($newPOST["privtype"]);
                unset($newPOST["opd_claim"]);
                unset($newPOST["ipd_claim"]);






                $newPOST2 = $_POST;
                unset($newPOST2["uid"]);
                unset($newPOST2["jc"]);
                unset($newPOST2["epf"]);
                unset($newPOST2["fn"]);
                unset($newPOST2["cn"]);
                unset($newPOST2["full_n"]);
                unset($newPOST2["nic"]);
                unset($newPOST2["tpno"]);
                unset($newPOST2["lpno"]);
                unset($newPOST2["dob"]);
                unset($newPOST2["email"]);
                unset($newPOST2["add"]);
                unset($newPOST2["padd"]);
                unset($newPOST2["school"]);
                unset($newPOST2["mstatus"]);
                unset($newPOST2["gender"]);
                unset($newPOST2["posi"]);
                unset($newPOST2["grade"]);
                unset($newPOST2["postid"]);
                unset($newPOST2["attal"]);
                unset($newPOST2["tral"]);
                unset($newPOST2["othal"]);
                unset($newPOST2["ptax"]);
                unset($newPOST2["bno"]);
                unset($newPOST2["bbr"]);
                unset($newPOST2["fleaves"]);
                unset($newPOST2["cleaves"]);
                unset($newPOST2["esal"]);
                unset($newPOST2["etype"]);
                unset($newPOST2["status"]);
                unset($newPOST2["empact"]);
                unset($newPOST2["probdate"]);
                unset($newPOST2["rdate"]);
                unset($newPOST2["picupdate"]);
                unset($newPOST2["picupdatecheck"]);
                unset($newPOST2["submit"]);
                unset($newPOST2["file"]);
                unset($newPOST2["psal"]);
                unset($newPOST2["cussal"]);
                unset($newPOST2["allowNa1"]);
                unset($newPOST2["allowNa2"]);
                unset($newPOST2["allowNa3"]);
                unset($newPOST2["allowNa4"]);
                unset($newPOST2["allowNa5"]);
                unset($newPOST2["authperson"]);
                unset($newPOST2["sec_authperson"]);
                unset($newPOST2["wrk_typ"]);
                unset($newPOST2["epf_entitle_date"]);
                unset($newPOST2["emp_dip"]);
                unset($newPOST2["privtype"]);
                unset($newPOST2["opd_claim"]);
                unset($newPOST2["ipd_claim"]);

                $keys = array_keys($newPOST);
                $keys2 = array_keys($newPOST2);

                for ($i = 0; $i < count($keys); $i++) {

                    if ($newPOST[$keys[$i]] != "") {

                        $queryA = "select alwid from allowances where name = '" . $newPOST[$keys[$i]] . "'";
                        $resA = Search($queryA);
                        if ($result = mysqli_fetch_assoc($resA)) {
                            $aidAllow = $result["alwid"];
                        } else {
                            $queryAd = "insert into allowances(name) values('" . $newPOST[$keys[$i]] . "')";
                            SUD($queryAd);
                            $aidAllow = $DB->getConnection()->insert_id;
                        }
                    }


                    if ($newPOST2[$keys2[$i]] != "") {

                        $querB = "select * from user_has_allowances where alwid = '" . $aidAllow . "' and uid = '" . $resultA["uid"] . "'";
                        $resB = Search($querB);
                        if ($results = mysqli_fetch_assoc($resB)) {
                        } else {
                            $queryAdV = "insert into user_has_allowances(uid, alwid, amount) values('" . $resultA["uid"] . "','" . $aidAllow . "','" . $newPOST2[$keys2[$i]] . "')";
                            SUD($queryAdV);
                        }
                    }
                }
            }


            if ($return == "1") {
                header("Location: ../Views/emp_manage.php?error=2");
            } else {
                header("Location: ../Views/emp_manage.php?error=3");
            }
        }
    } else if ($_POST["submit"] == "Update") {

        $customSal = "0";
        if (isset($_POST["cussal"])) {
            $customSal = $_POST["psal"];
        }

        if ($_POST["psal"] == "") {
            $customSal = "0";
        }

        //address
        $aid;
        $queryA = "select aid from address where address = '" . $_POST["add"] . "'";
        $resA = Search($queryA);
        if ($result = mysqli_fetch_assoc($resA)) {
            $aid = $result["aid"];
        } else {
            $queryAd = "insert into address(address) values('" . $_POST["add"] . "')";
            SUD($queryAd);
            $aid = $DB->getConnection()->insert_id;
        }

        //Permanent address
        $paid;
        $queryA = "select aid from address where address = '" . $_POST["padd"] . "'";
        $resAp = Search($queryA);
        if ($result = mysqli_fetch_assoc($resAp)) {
            $paid = $result["aid"];
        } else {
            $queryAdp = "insert into address(address) values('" . $_POST["padd"] . "')";
            SUD($queryAdp);
            $paid = $DB->getConnection()->insert_id;
        }

        $imageURL = $_POST["picupdate"];

        if (isset($_POST["picupdatecheck"]) && $_POST["picupdatecheck"] == "true") {
            //get uploaded file. Scan for newest uploaded file.
            $directoryPath = '../Images/UserPhotos/';
            $directoryPath = rtrim($directoryPath, '/');
            $max = ['path' => null, 'timestamp' => 0];
            foreach (scandir($directoryPath, SCANDIR_SORT_NONE) as $file) {
                $path = $directoryPath . '/' . $file;
                if (!is_file($path)) {
                    continue;
                }
                $timestamp = filemtime($path);
                if ($timestamp > $max['timestamp']) {
                    $max['path'] = $path;
                    $max['timestamp'] = $timestamp;
                }
            }
            $imageURL = $max['path'];
        }

        $resApxAB = Search("select grade_gid from emppost where grade_gid = '" . $_POST["grade"] . "' and position_pid = '" . $_POST["posi"] . "'");
        if ($resultxAB = mysqli_fetch_assoc($resApxAB)) {
        } else {
            $insertQueryX = "insert into emppost (basicsal, grade_gid, position_pid) values('" . $customSal . "','" . $_POST["grade"] . "','" . $_POST["posi"] . "')";
            $returnX = SUD($insertQueryX);
        }

        // $emp_post = str_replace("getbasicfrompandg","",$_POST["postid"]);
        $emp_post = "";

        $queryAx = "SELECT id FROM emppost where grade_gid = '" . $_POST["grade"] . "' and position_pid = '" . $_POST["posi"] . "'";
        $resApx = Search($queryAx);
        if ($resultx = mysqli_fetch_assoc($resApx)) {
            $emp_post = $resultx["id"];
        }


        if (!empty($_POST["rdate"]) && $_POST["rdate"] != "0000-00-00") {
            $futureDate = date('Y-m-d', strtotime('+1 year', strtotime($_POST["rdate"])));
            $registeredDate = $_POST["rdate"];
        } else {
            $futureDate = "0000-00-00";
            $registeredDate = "0000-00-00";
        }

        if (empty($_POST["probdate"]) || $_POST["probdate"] == "0000-00-00") {
            $probEndDate = "0000-00-00";
        } else {
            $probEndDate = $_POST["probdate"];
        }

        if (empty($_POST["epf_entitle_date"]) || $_POST["epf_entitle_date"] == "0000-00-00") {
            $epfEntitleDate = "0000-00-00";
        } else {
            $epfEntitleDate = $_POST["epf_entitle_date"];
        }

        if (empty($_POST["dob"]) || $_POST["dob"] == "0000-00-00") {
            $DOBDate = "0000-00-00";
        } else {
            $DOBDate = $_POST["dob"];
        }

        $updateQuery = "update user set fname='" . $_POST["fn"] . "',mname='" . $_POST["cn"] . "',lname='" . $_POST["full_n"] . "',nic='" . $_POST["nic"] . "',tpno='" . $_POST["tpno"] . "',lpno='" . $_POST["lpno"] . "',dob='" . $DOBDate . "',email='" . $_POST["email"] . "',school='" . $_POST["school"] . "',gender='" . $_POST["gender"] . "',presentSalary='" . $customSal . "',expectedSalery='0',registerdDate='" . $registeredDate . "',address='" . $aid . "',permanentAddress='" . $paid . "',emppost_id='" . $emp_post . "',MaritalStatus_idMaritalStatus='" . $_POST["mstatus"] . "',EmployeeType_etid='" . $_POST["etype"] . "',imageURL='" . $imageURL . "',isactive='" . $_POST["status"] . "',jobcode='" . $_POST["jc"] . "',epfno='" . $_POST["epf"] . "', bankno='" . $_POST["bno"] . "', bank = '" . $_POST["bbr"] . "', payeetax='" . $_POST["ptax"] . "', emp_act='" . $_POST["empact"] . "',prob_end_date='" . $probEndDate . "',auth_person_id='" . $_POST["authperson"] . "',sec_auth_person_id='" . $_POST["sec_authperson"] . "',work_typ='" . $_POST["wrk_typ"] . "',epf_entitle_date='" . $epfEntitleDate . "',dept_id='" . $_POST["emp_dip"] . "',first_year_leave_end_date='" . $futureDate . "',priv_typ='" . $_POST["privtype"] . "',opd_claim_value='" . $_POST["opd_claim"] . "',ipd_claim_value='" . $_POST["ipd_claim"] . "' where uid = '" . $_POST["uid"] . "'";
        $return = SUD($updateQuery);

        // remove all allowances
        SUD("delete from user_has_allowances where uid = '" . $_POST["uid"] . "'");

        //save attendence allowance
        $ok = SUD("insert into user_has_allowances(uid, alwid,amount) values('" . $_POST["uid"] . "',(select alwid from allowances where name = 'Fixed Allowance'), '" . $_POST["attal"] . "')");
        //save travelling allownces
        SUD("insert into user_has_allowances(uid, alwid,amount) values('" . $_POST["uid"] . "',(select alwid from allowances where name = 'Vehicle Allowance'), '" . $_POST["tral"] . "')");

        //save Other allownces
        SUD("insert into user_has_allowances(uid, alwid,amount) values('" . $_POST["uid"] . "',(select alwid from allowances where name = 'Other Allowances'), '" . $_POST["othal"] . "')");

        $newPOST = $_POST;
        unset($newPOST["uid"]);
        unset($newPOST["jc"]);
        unset($newPOST["epf"]);
        unset($newPOST["fn"]);
        unset($newPOST["cn"]);
        unset($newPOST["full_n"]);
        unset($newPOST["nic"]);
        unset($newPOST["tpno"]);
        unset($newPOST["lpno"]);
        unset($newPOST["dob"]);
        unset($newPOST["email"]);
        unset($newPOST["add"]);
        unset($newPOST["padd"]);
        unset($newPOST["school"]);
        unset($newPOST["mstatus"]);
        unset($newPOST["gender"]);
        unset($newPOST["posi"]);
        unset($newPOST["grade"]);
        unset($newPOST["postid"]);
        unset($newPOST["attal"]);
        unset($newPOST["tral"]);
        unset($newPOST["othal"]);
        unset($newPOST["ptax"]);
        unset($newPOST["bno"]);
        unset($newPOST["bbr"]);
        unset($newPOST["fleaves"]);
        unset($newPOST["cleaves"]);
        unset($newPOST["esal"]);
        unset($newPOST["etype"]);
        unset($newPOST["status"]);
        unset($newPOST["empact"]);
        unset($newPOST["probdate"]);
        unset($newPOST["rdate"]);
        unset($newPOST["picupdate"]);
        unset($newPOST["picupdatecheck"]);
        unset($newPOST["submit"]);
        unset($newPOST["file"]);
        unset($newPOST["psal"]);
        unset($newPOST["cussal"]);
        unset($newPOST["alowPR1"]);
        unset($newPOST["alowPR2"]);
        unset($newPOST["alowPR3"]);
        unset($newPOST["alowPR4"]);
        unset($newPOST["alowPR5"]);
        unset($newPOST["authperson"]);
        unset($newPOST["sec_authperson"]);
        unset($newPOST["wrk_typ"]);
        unset($newPOST["epf_entitle_date"]);
        unset($newPOST["emp_dip"]);
        unset($newPOST["privtype"]);
        unset($newPOST["opd_claim"]);
        unset($newPOST["ipd_claim"]);


        $newPOST2 = $_POST;
        unset($newPOST2["uid"]);
        unset($newPOST2["jc"]);
        unset($newPOST2["epf"]);
        unset($newPOST2["fn"]);
        unset($newPOST2["cn"]);
        unset($newPOST2["full_n"]);
        unset($newPOST2["nic"]);
        unset($newPOST2["tpno"]);
        unset($newPOST2["lpno"]);
        unset($newPOST2["dob"]);
        unset($newPOST2["email"]);
        unset($newPOST2["add"]);
        unset($newPOST2["padd"]);
        unset($newPOST2["school"]);
        unset($newPOST2["mstatus"]);
        unset($newPOST2["gender"]);
        unset($newPOST2["posi"]);
        unset($newPOST2["grade"]);
        unset($newPOST2["postid"]);
        unset($newPOST2["attal"]);
        unset($newPOST2["tral"]);
        unset($newPOST2["othal"]);
        unset($newPOST2["ptax"]);
        unset($newPOST2["bno"]);
        unset($newPOST2["bbr"]);
        unset($newPOST2["fleaves"]);
        unset($newPOST2["cleaves"]);
        unset($newPOST2["esal"]);
        unset($newPOST2["etype"]);
        unset($newPOST2["status"]);
        unset($newPOST2["empact"]);
        unset($newPOST2["probdate"]);
        unset($newPOST2["rdate"]);
        unset($newPOST2["picupdate"]);
        unset($newPOST2["picupdatecheck"]);
        unset($newPOST2["submit"]);
        unset($newPOST2["file"]);
        unset($newPOST2["psal"]);
        unset($newPOST2["cussal"]);
        unset($newPOST2["allowNa1"]);
        unset($newPOST2["allowNa2"]);
        unset($newPOST2["allowNa3"]);
        unset($newPOST2["allowNa4"]);
        unset($newPOST2["allowNa5"]);
        unset($newPOST2["authperson"]);
        unset($newPOST2["sec_authperson"]);
        unset($newPOST2["wrk_typ"]);
        unset($newPOST2["epf_entitle_date"]);
        unset($newPOST2["emp_dip"]);
        unset($newPOST2["privtype"]);
        unset($newPOST["opd_claim"]);
        unset($newPOST["ipd_claim"]);


        $keys = array_keys($newPOST);
        $keys2 = array_keys($newPOST2);

        for ($i = 0; $i < count($keys); $i++) {

            if ($newPOST[$keys[$i]] != "") {

                $queryA = "select alwid from allowances where name = '" . $newPOST[$keys[$i]] . "'";
                $resA = Search($queryA);
                if ($result = mysqli_fetch_assoc($resA)) {
                    $aid = $result["alwid"];
                } else {
                    $queryAd = "insert into allowances(name) values('" . $newPOST[$keys[$i]] . "')";
                    SUD($queryAd);
                    $aid = $DB->getConnection()->insert_id;
                }
            }


            if ($newPOST2[$keys2[$i]] != "") {

                $querB = "select * from user_has_allowances where alwid = '" . $aid . "' and uid = '" . $_POST["uid"] . "'";
                $resB = Search($querB);
                if ($results = mysqli_fetch_assoc($resB)) {
                    $updateQueryAlw = "update user_has_allowances set amount='" . $newPOST2[$keys2[$i]] . "' where uid = '" . $_POST["uid"] . "' and alwid = '" . $aid . "'";
                    SUD($updateQueryAlw);
                } else {
                    $queryAdV = "insert into user_has_allowances(uid, alwid, amount) values('" . $_POST["uid"] . "','" . $aid . "','" . $newPOST2[$keys2[$i]] . "')";
                    SUD($queryAdV);
                }
            }
        }


        // echo $queryAx;
        if ($return == "1") {
            // $ret = SUD("update add_employee_request_approve set request_status ='3' where subject_user='" . $_POST["uid"] . "' and request_type='1' and request_status='1' and requested_user='".$_SESSION["uid"]."'");
            header("Location: ../Views/emp_manage.php?error=4");
        } else {
            header("Location: ../Views/emp_manage.php?error=3");
        }
    } else if ($_POST["submit"] == "Terminate") {
        $deleteQuery = "update user set isactive = '0' where uid = '" . $_POST["uid"] . "'";
        $return = SUD($deleteQuery);
        if ($return == "1") {
            // $ret = SUD("update add_employee_request_approve set request_status ='3' where subject_user='" . $_POST["uid"] . "' and request_type='2' and request_status='1' and requested_user='".$_SESSION["uid"]."'");
            header("Location: ../Views/emp_manage.php?error=5");
        } else {
            header("Location: ../Views/emp_manage.php?error=3");
        }
    }
}
//search 
if (isset($_REQUEST["request"])) {
    $out;
    if ($_REQUEST["request"] == "getEmps") {
        $out = "<table class='table table-striped'><thead class='thead-dark' style='position : sticky; top : 0;  z-index: 0; background-color: #9eafba; color: black;'>
        <tr>
        <th>EMP No</th>
        <th>Name</th>
        <th>Calling Name</th>
        <th>Department</th>
        <th>Emp.Type</th>
        <th>Designation</th>
        <th>Status</th>
        </tr></thead>";

        $fn = $_REQUEST["fn"];
        $type = $_REQUEST["type"];
        // $posi = $_REQUEST["posi"];
        $status = $_REQUEST["active"];
        $system = $_REQUEST["system"];
        $epfno = $_REQUEST["epfno"];
        $dept = $_REQUEST["depdata"];


        $privs = array();
        $query = "select b.name from profile_wise_privileges a left join user c on a.prof_id = c.priv_typ, features b where a.priv_id = b.fid and c.uid = '" . $_SESSION["uid"] . "' and b.isactive='1'";
        $res = Search($query);
        while ($result = mysqli_fetch_assoc($res)) {
            array_push($privs, $result["name"]);
        }

        $emp_count = 0;
        // and d.pid like '" . $posi . "' 
        $res = Search("select *,d.name as position,b.name as emptypename,e.name as department from user a,employeetype b, emppost c, position d, emp_department e where c.position_pid = d.pid and a.emppost_id = c.id and a.EmployeeType_etid=b.etid and a.dept_id = e.did and a.lname like '%" . $fn . "%' and a.EmployeeType_etid like '" . $type . "' and a.isactive = '" . $status . "' and a.jobcode like '" . $epfno . "' and a.uid != '2' and a.dept_id like '" . $dept . "'  order by length(a.jobcode),a.jobcode ASC");
        while ($result = mysqli_fetch_assoc($res)) {
            $status;
            if ($result["isactive"] == "1") {
                $status = "Active";
            } else if ($result["isactive"] == "2") {
                if (in_array("Approve Details Change Request (Sub Privilege)", $privs)) {
                    $status = "<img src='../Icons/done.png' title='Approve user details' onclick='approve_User(" . $result["uid"] . ")'>&nbsp;&nbsp;<img src='../Icons/remove.png' title='Reject user details' onclick='decline_User(" . $result["uid"] . ")'>";
                } else {
                    $status = "Pending";
                }
            } else {
                $status = "Not-Active";
            }

            $out .= "<tr onclick='setUID(" . $result["uid"] . ")'>
            <td>" . $result["jobcode"] . "</td>
            <td>" . $result["lname"] . "</td>
            <td>" . $result["mname"] . "</td>
            <td>" . $result["department"] . "</td>
            <td>" . $result["emptypename"] . "</td>
            <td>" . $result["school"] . "</td>
            <td>" . $status . "</td>

            </tr>";
            ++$emp_count;
        }

        $out .= "</table>";

        $out .= "</br><p class='form-label' style='border-right:none; border-radius:0;font-size:14px;'>
            Total Employee Count :<b> <span>" . $emp_count . "</span></b>                    
            </p>";

        echo $out;
    } else if ($_REQUEST["request"] == "getEmpsbyID") {
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

            $query = "select a.amount,b.name as alw from user_has_allowances a, allowances b where a.alwid = b.alwid and a.uid = '" . $_REQUEST["uid"] . "' and b.name != 'Attendance Allowance' and b.name !='Travelling Allowance' and b.name !='Other Allowances' and b.name !='Fixed Allowance' and b.name != 'Vehicle Allowance' order by b.alwid";
            $res = Search($query);
            while ($resultss = mysqli_fetch_assoc($res)) {
                $outaaa .= $resultss["alw"] . "%%" . $resultss["amount"] . "%%";
            }

            // $req = Search("select request_type from add_employee_request_approve where requested_user = '" . $_SESSION["uid"] . "' and request_status = '1' and subject_user = '" . $_REQUEST["uid"] . "'");
            // while ($resultreq = mysqli_fetch_assoc($req)) {
            //     $outreq .= $resultreq["request_type"] . "/@/";
            // }
        } else {
            $out = "usernotfound";
        }
        echo $out . "#" . $outals . "#" . $outaaa;
    } else if ($_REQUEST["request"] == "getEmpsbyEPFNo") {
        $query = "select * from user where epfno = '" . $_REQUEST["epf"] . "'";
        $res = Search($query);
        if ($result = mysqli_fetch_assoc($res)) {
            $out = implode("#", $result);

            //search allowances
            $outals = "";
            $resal = Search("select a.amount from user_has_allowances a, allowances b where a.alwid = b.alwid and a.uid = '" . $result["uid"] . "' order by b.alwid");
            while ($resultal = mysqli_fetch_assoc($resal)) {
                $outals .= $resultal["amount"] . "//";
            }

            $query = "select a.amount,b.name as alw from user_has_allowances a, allowances b where a.alwid = b.alwid and a.uid = '" . $result["uid"] . "' and b.name != 'Attendance Allowance' and b.name !='Travelling Allowance' and b.name !='Other Allowances' and b.name !='Fixed Allowance' and b.name != 'Vehicle Allowance' order by b.alwid";
            $res = Search($query);
            while ($resultss = mysqli_fetch_assoc($res)) {
                $outaaa .= $resultss["alw"] . "%%" . $resultss["amount"] . "%%";
            }

            // $req = Search("select request_type from add_employee_request_approve wher requested_user = '" . $_SESSION["uid"] . "' and request_status = '1' and subject_user = '" . $result["uid"] . "'");
            // while ($resultreq = mysqli_fetch_assoc($req)) {
            //     $outreq .= $resultreq["request_type"] . "/@/";
            // }
        } else {
            $out = "usernotfound";
        }
        echo $out . "#" . $outals . "#" . $outaaa;
    } else if ($_REQUEST["request"] == "getAddress") {
        $query = "select address from address where aid = '" . $_REQUEST["aid"] . "'";
        $res = Search($query);
        if ($result = mysqli_fetch_assoc($res)) {
            $out = $result["address"];
        }
        echo $out;
    } else if ($_REQUEST["request"] == "saveLoginDetails") {
        $query = "select lgid from logindetails where User_uid = '" . $_REQUEST["uid"] . "'";
        $res = Search($query);
        if ($result = mysqli_fetch_assoc($res)) {
            $updateQuery = "update logindetails set username = '" . $_REQUEST["usr"] . "', password = '" . $_REQUEST["pwrd"] . "', ans1 = '" . $_REQUEST["qone"] . "', ans2 = '" . $_REQUEST["qtwo"] . "' where User_uid = '" . $_REQUEST["uid"] . "'";
            $return = SUD($updateQuery);
            if ($return == "1") {

                $ret = SUD("update add_employee_request_approve set request_status ='3' where subject_user='" . $_REQUEST["uid"] . "' and request_type='3' and request_status='1' and requested_user='" . $_SESSION["uid"] . "'");

                echo "Details Saved!";
            } else {
                echo "Details Saving Error!";
            }
        } else {
            $updateQuery = "insert into logindetails(username,password,User_uid,ans1,ans2,resetcode) values('" . $_REQUEST["usr"] . "','" . $_REQUEST["pwrd"] . "','" . $_REQUEST["uid"] . "','" . $_REQUEST["qone"] . "','" . $_REQUEST["qtwo"] . "','" . mt_rand() . "')";
            $return = SUD($updateQuery);
            if ($return == "1") {

                $ret = SUD("update add_employee_request_approve set request_status ='3' where subject_user='" . $_REQUEST["uid"] . "' and request_type='3' and request_status='1' and requested_user='" . $_SESSION["uid"] . "'");

                echo "Details Saved!";
            } else {
                echo "Details Saving Error!";
            }
        }
    } else if ($_REQUEST["request"] == "getprivs") {
        $out = "";
        $uid = $_REQUEST["uid"];
        $query = "select * from features where isactive='1'";
        $res = Search($query);
        while ($result = mysqli_fetch_assoc($res)) {
            $query2 = "select * from privillages where features_fid = '" . $result["fid"] . "' and user_uid = '" . $uid . "'";
            $res2 = Search($query2);
            if ($result2 = mysqli_fetch_assoc($res2)) {
                $out .= "<p style='font-size:16px;'><input type='checkbox' id='" . $result["name"] . "' checked>" . $result["name"] . " </p> ";
            } else {
                $out .= "<p style='font-size:16px;'><input type='checkbox' id='" . $result["name"] . "'>" . $result["name"] . "</p> ";
            }
        }
        echo $out;
    } else if ($_REQUEST["request"] == "saveprivs") {

        $uid = $_REQUEST["uid"];
        $query = "delete from privillages where user_uid='" . $uid . "'";
        $res = SUD($query);

        $query2 = "select * from features where isactive='1'";
        $res2 = Search($query2);
        while ($result = mysqli_fetch_assoc($res2)) {
            $newName = str_replace(" ", "_", $result["name"]);

            if ($_REQUEST[$newName] == "true") {
                $query3 = "insert into privillages(user_uid, features_fid) values('" . $uid . "','" . $result["fid"] . "')";
                $resx = SUD($query3);
            }
        }
        echo "Details Updated!";
    } else if ($_REQUEST["request"] == "getfiles") {

        $uid = $_REQUEST["empuid"];

        $path    = '../SkillsFiles/' . $uid;

        if (file_exists($path)) {
            $files = scandir($path);
            $files = array_diff(scandir($path), array('.', '..'));
            foreach ($files as $file) {

                echo $file . "#";
            }
        } else {
            echo "0";
        }
    } else if ($_REQUEST["request"] == "getemptypes") {

        $out = "<table  class='table table-striped'><tr><th>No</th> <th>Type</th></tr>";
        $query = "select * from employeetype";
        $res = Search($query);

        while ($result = mysqli_fetch_assoc($res)) {
            if (!$result["name"] == null) {
                $descrip = $result["name"];
            } else {
                $descrip = "-";
            }

            $details = $result["etid"] . "#" . $result["name"];

            $out .= "<tr style='cursor: pointer;' id='" . $details . "' onclick='select_type(id)'><td>" . $result["etid"] . "</td><td>" . $descrip . "</td></tr>";

            // <td><img src='../Icons/remove.png' onclick='deletetype(" . $result["etid"] . ")'></td>
        }
        $out .= "</table>";
        echo $out;
    } else if ($_REQUEST["request"] == "saveEmpType") {

        $query = "select * from employeetype where name='" . $_REQUEST["name"] . "'";
        $res = Search($query);

        if ($result = mysqli_fetch_assoc($res)) {

            $out = "Alredy Added !!!";
        } else {
            $query = "insert into employeetype(name) values('" . $_REQUEST["name"] . "')";
            SUD($query);
            $out = "Added !!!";
        }


        echo $out;
    } else if ($_REQUEST["request"] == "updateEmpType") {

        $query = "update employeetype set name='" . $_REQUEST["name"] . "' where etid='" . $_REQUEST["id"] . "'";
        SUD($query);

        $out = "Updated !!!";
        echo $out;
    } else if ($_REQUEST["request"] == "deleteEmpType") {

        $query = "delete from employeetype where etid='" . $_REQUEST["id"] . "'";
        SUD($query);
        $out = "Deleted !!!";
        echo $out;
    } else if ($_REQUEST["request"] == "resignEmployee") {

        $query = "delete from user where uid='" . $_REQUEST["user"] . "'";
        $res = SUD($query);

        if ($res == "1") {
            $out = "Record Delete Successfully";
        } else {
            $out = "Error";
        }

        echo $out;
    } else if ($_REQUEST["request"] == "getbranch") {

        $out = "<table  class='table table-striped'><tr><th>No</th> <th>Branch</th><th></th></tr>";
        $query = "select * from position order by pid";
        $res = Search($query);

        while ($result = mysqli_fetch_assoc($res)) {
            if (!$result["name"] == null) {
                $descrip = $result["name"];
            } else {
                $descrip = "-";
            }

            $details = $result["pid"] . "#" . $result["name"];

            $out .= "<tr style='cursor: pointer;' id='" . $details . "' onclick='select_branch(id)'><td>" . $result["pid"] . "</td><td>" . $descrip . "</td>
            
            </tr>";
        }
        $out .= "</table>";
        echo $out;
    } else if ($_REQUEST["request"] == "inbranch") {

        $query = "select * from position where name='" . $_REQUEST["name"] . "'";

        $res = Search($query);

        if ($result = mysqli_fetch_assoc($res)) {

            $out = "Alredy Added !!!";
        } else {
            $query = "insert into position (name) values('" . $_REQUEST["name"] . "')";
            $res = SUD($query);

            if ($res == "1") {
                $out = "Record Added Successfully";
            } else {
                $out = "Error";
            }
        }


        echo $out;
    } else if ($_REQUEST["request"] == "upbranch") {

        $query = "update position set name='" . $_REQUEST["name"] . "' where pid='" . $_REQUEST["id"] . "'";
        $res = SUD($query);

        if ($res == "1") {
            $out = "Record Updated Successfully";
        } else {
            $out = "Error";
        }

        echo  $out;
    } else if ($_REQUEST["request"] == "delbranch") {

        $query = "delete from position where pid='" . $_REQUEST["id"] . "'";
        $res = SUD($query);

        if ($res == "1") {
            $out = "Record Delete Successfully";
        } else {
            $out = "Error";
        }

        echo  $out;
    } else if ($_REQUEST["request"] == "getdepartment") {

        $out = "<table  class='table table-striped'><tr><th>No</th> <th>Department</th></tr>";
        $query = "select * from emp_department order by did";
        $res = Search($query);

        while ($result = mysqli_fetch_assoc($res)) {
            if (!$result["name"] == null) {
                $descrip = $result["name"];
            } else {
                $descrip = "-";
            }

            $details = $result["did"] . "#" . $result["name"];

            $out .= "<tr style='cursor: pointer;' id='" . $details . "' onclick='select_departments(id)'><td>" . $result["did"] . "</td><td>" . $descrip . "</td></tr>";

            // <td><img src='../Icons/remove.png' onclick='deleteDepartment(" . $result["did"] . ")'></td>
        }
        $out .= "</table>";
        echo $out;
    } else if ($_REQUEST["request"] == "indepartment") {

        $query = "select * from emp_department where name='" . $_REQUEST["name"] . "'";

        $res = Search($query);

        if ($result = mysqli_fetch_assoc($res)) {

            $out = "Alredy Added !!!";
        } else {
            $query = "insert into emp_department (name) values('" . $_REQUEST["name"] . "')";
            $res = SUD($query);

            if ($res == "1") {
                $out = "Record Added Successfully";
            } else {
                $out = "Error";
            }
        }


        echo $out;
    } else if ($_REQUEST["request"] == "updepartment") {

        $query = "update emp_department set name='" . $_REQUEST["name"] . "' where did='" . $_REQUEST["id"] . "'";
        $res = SUD($query);

        if ($res == "1") {
            $out = "Record Updated Successfully";
        } else {
            $out = "Error";
        }

        echo  $out;
    } else if ($_REQUEST["request"] == "deldepartment") {

        $query = "delete from emp_department where did='" . $_REQUEST["id"] . "'";
        $res = SUD($query);

        if ($res == "1") {
            $out = "Record Delete Successfully";
        } else {
            $out = "Error";
        }

        echo  $out;
    } else if ($_REQUEST["request"] == "getshift") {

        $out = "<table  class='table table-striped'><tr><th>Shift Name</th><th>In Time</th><th>Out Time</th><th></th></tr>";
        $query = "select * from working_shift_type order by name ASC";
        $res = Search($query);

        while ($result = mysqli_fetch_assoc($res)) {
            if (!$result["name"] == null) {
                $shift = $result["name"];
            } else {
                $shift = "-";
            }

            if ($result["intime"] == null) {
                $IN = "";
            } else {
                $IN = date("H:i A", strtotime($result["intime"]));
            }

            if ($result["outtime"] == null) {
                $OUT = "";
            } else {
                $OUT = date("H:i A", strtotime($result["outtime"]));
            }

            $shift_details = $result["wstid"] . "#" . $result["name"] . "#" . $result["intime"] . "#" . $result["outtime"];

            $out .= "<tr style='cursor: pointer;' id='" . $shift_details . "' onclick='select_ShiftTypes(id)'><td>" . $shift . "</td><td>" . $IN . "</td><td>" . $OUT . "</td><td><img src='../Icons/remove.png' onclick='deleteShiftTypes(" . $result["wstid"] . ")'></td></tr>";
        }
        $out .= "</table>";
        echo $out;
    } else if ($_REQUEST["request"] == "saveshift") {

        $query = "select * from working_shift_type where name like '" . $_REQUEST["name"] . "'";

        $res = Search($query);

        if ($result = mysqli_fetch_assoc($res)) {

            $out = "Shift alredy added!";
        } else {

            if (empty($_REQUEST["in"])) {
                $INTIME = NULL;
            } else {
                $INTIME = $_REQUEST["in"];
            }


            if (empty($_REQUEST["out"])) {
                $OUTTIME = NULL;
            } else {
                $OUTTIME = $_REQUEST["out"];
            }


            $query = "insert into working_shift_type (name, intime, outtime, isactive) values('" . $_REQUEST["name"] . "','" . $INTIME . "','" . $OUTTIME . "','1')";
            $res = SUD($query);

            if ($res == "1") {
                $out = "Record added successfully!";
            } else {
                $out = "Error!";
            }
        }


        echo $out;
    } else if ($_REQUEST["request"] == "updateshift") {

        if (empty($_REQUEST["in"])) {
            $INTIME = NULL;
        } else {
            $INTIME = $_REQUEST["in"];
        }


        if (empty($_REQUEST["out"])) {
            $OUTTIME = NULL;
        } else {
            $OUTTIME = $_REQUEST["out"];
        }

        $query = "update working_shift_type set name='" . $_REQUEST["name"] . "',intime='" . $INTIME . "', outtime='" . $OUTTIME . "' where wstid='" . $_REQUEST["id"] . "'";
        $res = SUD($query);

        if ($res == "1") {
            $out = "Record updated successfully!";
        } else {
            $out = "Error!";
        }

        echo  $out;
    } else if ($_REQUEST["request"] == "deleteshift") {

        $query = "delete from working_shift_type where wstid='" . $_REQUEST["id"] . "'";
        $res = SUD($query);

        if ($res == "1") {
            $out = "Record delete successfully!";
        } else {
            $out = "Error!";
        }

        echo  $out;
    } else if ($_REQUEST["request"] == "setSession") {

        $_SESSION["exportdata"] = $_POST["data"];

        echo $_SESSION["exportdata"];
    } else if ($_REQUEST["request"] == "SaveSettings") {

        $half_Morning = $_REQUEST["halfMIntime"] . " AM - " . $_REQUEST["halfMOuttime"] . " PM";
        $half_Evening = $_REQUEST["halfEIntime"] . " PM - " . $_REQUEST["halfEOuttime"] . " PM";
        $Short_Morning = $_REQUEST["shortMIntime"] . " AM - " . $_REQUEST["shortMOuttime"] . " AM";
        $Short_Evening = $_REQUEST["shortEIntime"] . " PM - " . $_REQUEST["shortEOuttime"] . " PM";

        $res = Search("select swtid from settings_working_times where update_user = '" . $_REQUEST["user_ID"] . "'");
        if ($resultss = mysqli_fetch_assoc($res)) {
            $out .= "Time settings already added in this employee!";
        } else {
            $querySettings = "insert into settings_working_times(intime, outtime, half_slot_morning, half_slot_evening, short_morning, short_evening, date, isactive, update_user, satintime, satouttime, weekdays_late, weekdays_ot, weekends_late, weekends_ot, half_m_late, half_e_late, short_m_late, short_e_late) values('" . $_REQUEST["intime"] . "','" . $_REQUEST["outtime"] . "','" . $half_Morning . "','" . $half_Evening . "','" . $Short_Morning . "','" . $Short_Evening . "','" . date("Y-m-d") . "','1','" . $_REQUEST["user_ID"] . "','" . $_REQUEST["SatIntime"] . "','" . $_REQUEST["SatOuttime"] . "','" . $_REQUEST["wrkLate"] . "','00:00:00','" . $_REQUEST["wrkEndLate"] . "','00:00:00','" . $_REQUEST["halfMLate"] . "','" . $_REQUEST["halfELate"] . "','" . $_REQUEST["shrtMLate"] . "','" . $_REQUEST["shrtELate"] . "')";

            $ret = SUD($querySettings);

            if ($ret == 1) {
                $ret = SUD("update add_employee_request_approve set request_status ='3' where subject_user='" . $_REQUEST["user_ID"] . "' and request_type='5' and request_status='1' and requested_user='" . $_SESSION["uid"] . "'");

                $out .= "Time settings saved successfully!";
            } else {
                $out .= "Error";
            }
        }

        echo $out;
    } else if ($_REQUEST["request"] == "SelectSettings") {

        $query = "select * from settings_working_times where update_user = '" . $_REQUEST["EMPID"] . "'";

        $res = Search($query);
        while ($resultss = mysqli_fetch_assoc($res)) {
            $out .= implode("#", $resultss);
        }

        echo $out;
    } else if ($_REQUEST["request"] == "UpdateSettings") {

        $half_Morning = $_REQUEST["halfMIntime"] . " AM - " . $_REQUEST["halfMOuttime"] . " PM";
        $half_Evening = $_REQUEST["halfEIntime"] . " PM - " . $_REQUEST["halfEOuttime"] . " PM";
        $Short_Morning = $_REQUEST["shortMIntime"] . " AM - " . $_REQUEST["shortMOuttime"] . " AM";
        $Short_Evening = $_REQUEST["shortEIntime"] . " PM - " . $_REQUEST["shortEOuttime"] . " PM";


        $res = Search("select update_user from settings_working_times where swtid = '" . $_REQUEST["SWTID"] . "'");
        if ($result_user_id = mysqli_fetch_assoc($res)) {
            $USER_ID = $result_user_id["update_user"];
        } else {
            $USER_ID = "0";
        }


        $queryUpdateSettings = "Update settings_working_times set intime='" . $_REQUEST["intime"] . "',outtime='" . $_REQUEST["outtime"] . "',half_slot_morning='" . $half_Morning . "',half_slot_evening='" . $half_Evening . "',short_morning='" . $Short_Morning . "',short_evening='" . $Short_Evening . "',date='" . date("Y-m-d") . "',satintime='" . $_REQUEST["SatIntime"] . "', satouttime='" . $_REQUEST["SatOuttime"] . "',weekdays_late='" . $_REQUEST["wrkLate"] . "',weekends_late='" . $_REQUEST["wrkEndLate"] . "',half_m_late='" . $_REQUEST["halfMLate"] . "',half_e_late='" . $_REQUEST["halfELate"] . "',short_m_late='" . $_REQUEST["shrtMLate"] . "',short_e_late='" . $_REQUEST["shrtELate"] . "' where swtid='" . $_REQUEST["SWTID"] . "'";

        $ret = SUD($queryUpdateSettings);

        if ($ret == 1) {
            $ret = SUD("update add_employee_request_approve set request_status ='3' where subject_user='" . $USER_ID . "' and request_type='5' and request_status='1' and requested_user='" . $_SESSION["uid"] . "'");

            $out .= "Time settings update successfully!";
        } else {
            $out .= "Error";
        }

        echo $out;
    } else if ($_REQUEST["request"] == "CheckSettings") {

        $query = "select swtid from settings_working_times where update_user='" . $_REQUEST["EMPID"] . "' and isactive = '1'";
        $res = Search($query);
        if ($Result = mysqli_fetch_assoc($res)) {
            $out .= "OK";
        } else {
            $out .= "NO";
        }

        echo $out;
    } else if ($_REQUEST["request"] == "sendRequest") {
        // if ($_REQUEST["req_msg"] == "1") 
        // {
        //     $query = "select raid from add_employee_request_approve where request_status='0' and requested_user='".$_REQUEST["req_USER"]."' and request_type='".$_REQUEST["req_TYP"]."'";
        //     $res = Search($query);
        //     if ($result = mysqli_fetch_assoc($res)) 
        //     {
        //        $out="Already you requested to change this option!";
        //     }
        //     else
        //     {
        //        $query="insert into add_employee_request_approve(requested_user, request_type, req_date) values('".$_REQUEST["req_USER"]."','".$_REQUEST["req_TYP"]."','".date('Y-m-d')."')";
        //        $req = SUD($query);

        //        if ($req == 1) 
        //        {
        //           $out="Your request added!";
        //        }
        //        else
        //        {
        //           $out="Error!";
        //        }   
        //     }
        // }
        // else
        // {
        $res_user = Search("select uid from user where uid ='" . $_REQUEST["subject_USER"] . "' and isactive !='1'");
        if ($resul = mysqli_fetch_assoc($res_user)) {
            $out = "This employee is a pending person or not-active person in the system!";
        } else {
            $query = "select raid from add_employee_request_approve where request_status='0' and requested_user='" . $_REQUEST["req_USER"] . "' and request_type='" . $_REQUEST["req_TYP"] . "' and subject_user='" . $_REQUEST["subject_USER"] . "'";
            $res = Search($query);
            if ($result = mysqli_fetch_assoc($res)) {
                $out = "Already you requested to change this option!";
            } else {
                $query = "insert into add_employee_request_approve(subject_user, requested_user, request_type, req_date) values('" . $_REQUEST["subject_USER"] . "','" . $_REQUEST["req_USER"] . "','" . $_REQUEST["req_TYP"] . "','" . date('Y-m-d') . "')";
                $reqt = SUD($query);

                if ($reqt == 1) {
                    $out = "Your request added!";
                } else {
                    $out = "Error!";
                }
            }
        }
        // }

        echo $out;
    } else if ($_REQUEST["request"] == "approveUser") {
        $ret = SUD("update user set isactive ='1' where uid='" . $_REQUEST["EMPID"] . "'");

        if ($ret == 1) {
            $out .= "1";
        } else {
            $out .= "0";
        }

        echo $out;
    } else if ($_REQUEST["request"] == "declineUser") {
        $ret_allow = SUD("delete from user_has_allowances where uid = '" . $_REQUEST["EMPID"] . "'");
        $ret_user = SUD("delete from user where uid = '" . $_REQUEST["EMPID"] . "'");

        if ($ret_user == 1) {
            $out .= "1";
        } else {
            $out .= "0";
        }

        echo $out;
    }
    //other methods
}
if (isset($_POST['skilldetails'])) //upload skills files and description
{

    $emp = $_POST["empno"];
    $desc = $_POST["skilldes"];
    $fileNames = array_filter($_FILES['filedata']['name']);


    if ($emp == "") {
        header("Location: ../Views/emp_manage.php?msg=1");
    } elseif (empty($fileNames)) {

        header("Location: ../Views/emp_manage.php?msg=2");
    } else {
        if (isset($_POST["empno"])) {

            $query = "update user set skill_descrip='" . $desc . "' where uid = '" . $emp . "'";

            $return = SUD($query);

            if ($return == "1") {
                $ret = SUD("update add_employee_request_approve set request_status ='3' where subject_user='" . $emp . "' and request_type='4' and request_status='1' and requested_user='" . $_SESSION["uid"] . "'");

                $folder_name = $_POST["empno"];
                $parth = '/../SkillsFiles/';
                $allowTypes = array('jpg', 'png', 'jpeg', 'pdf', 'txt', 'docx', 'doc', 'ppt', 'pptx', 'xls', 'xlsx');

                if (!is_dir(dirname(__FILE__) . $parth . $folder_name)) {
                    mkdir(dirname(__FILE__) . $parth . $folder_name, 0777, true);

                    foreach ($_FILES['filedata']['name'] as $key => $val) {
                        $target_dir = '../SkillsFiles/' . $folder_name . '/';
                        $fileName = basename($_FILES['filedata']['name'][$key]);
                        $target_file = $target_dir . $fileName;

                        $FileType = pathinfo($target_file, PATHINFO_EXTENSION);

                        if (in_array($FileType, $allowTypes)) {

                            // Check if image file is a actual image or fake image                    
                            if (move_uploaded_file($_FILES["filedata"]["tmp_name"][$key], $target_file)) {
                                header('Location:  ../Views/emp_manage.php?msg=3');
                            } else {
                                header('Location:  ../Views/emp_manage.php?msg=4');
                            }
                        } else {
                            header('Location:  ../Views/emp_manage.php?msg=5');
                        }
                    }
                } else {
                    header('Location:  ../Views/emp_manage.php?msg=6');
                }
            } else {
                header('Location:  ../Views/emp_manage.php?msg=4');
            }
        }
    }
}

if (isset($_POST['skilldetailsupdate'])) //update skills files and description
{

    $emp = $_POST["empno"];
    $desc = $_POST["skilldes"];
    $fileNames = array_filter($_FILES['filedata']['name']);


    if ($emp == "") {
        header("Location: ../Views/emp_manage.php?msg=1");
    } elseif (empty($fileNames)) {

        $query = "update user set skill_descrip='" . $desc . "' where uid = '" . $emp . "'";

        $return = SUD($query);

        if ($return == "1") {
            header('Location:  ../Views/emp_manage.php?msg=7');
        } else {
            header('Location:  ../Views/emp_manage.php?msg=8');
        }
    } else {
        if (isset($_POST["empno"])) {

            $query = "update user set skill_descrip='" . $desc . "' where uid = '" . $emp . "'";

            $return = SUD($query);

            $ret = SUD("update add_employee_request_approve set request_status ='3' where subject_user='" . $emp . "' and request_type='4' and request_status='1' and requested_user='" . $_SESSION["uid"] . "'");

            if ($return == "1") {
                $folder_name = $_POST["empno"];
                $parth = '/../SkillsFiles/';
                $allowTypes = array('jpg', 'png', 'jpeg', 'pdf', 'txt', 'docx', 'doc', 'ppt', 'pptx', 'xls', 'xlsx');

                if (is_dir(dirname(__FILE__) . $parth . $folder_name)) {
                    mkdir(dirname(__FILE__) . $parth . $folder_name, 0777, true);

                    foreach ($_FILES['filedata']['name'] as $key => $val) {
                        $target_dir = '../SkillsFiles/' . $folder_name . '/';
                        $fileName = basename($_FILES['filedata']['name'][$key]);
                        $target_file = $target_dir . $fileName;

                        $FileType = pathinfo($target_file, PATHINFO_EXTENSION);

                        if (in_array($FileType, $allowTypes)) {

                            // Check if image file is a actual image or fake image                    
                            if (move_uploaded_file($_FILES["filedata"]["tmp_name"][$key], $target_file)) {
                                header('Location:  ../Views/emp_manage.php?msg=7');
                            } else {
                                header('Location:  ../Views/emp_manage.php?msg=8');
                            }
                        } else {
                            header('Location:  ../Views/emp_manage.php?msg=5');
                        }
                    }
                }
            } else {
                header('Location:  ../Views/emp_manage.php?msg=4');
            }
        }
    }
}

if (isset($_POST['downloadfiles'])) //update skills files and description
{

    $fileemp = $_POST["empno"];

    if ($fileemp == "") {
        header("Location: ../Views/emp_manage.php?msg=1");
    } else {

        $filedir = '../SkillsFiles/' . $fileemp;

        if (file_exists($filedir)) {
            $zipFile = "../ZipFolders/" . $fileemp . ".zip";

            // Initializing PHP class
            $zip = new ZipArchive();
            $zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

            $files = scandir('../SkillsFiles/' . $fileemp . '/');

            foreach ($files as $file) {
                if ($file == '.' || $file == '..') continue;
                $zip->addFile('../SkillsFiles/' . $fileemp . '/' . $file, $file);
            }

            $zip->close();

            //Force download a file in php
            if (file_exists($zipFile)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . basename($zipFile) . '"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($zipFile));
                readfile($zipFile);
                unlink($zipFile);
                exit;
            }
        } else {

            header('Location:  ../Views/emp_manage.php?msg=9');
        }
    }
}