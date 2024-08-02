<?php

include '../../DB/DB.php'; 
$DB = new Database(); 

if (isset($_REQUEST["request"])) {
    $out = "";
    if ($_REQUEST["request"] == "getbasicfrompandg") {
        if ($_REQUEST["grade"] != "null") {
            $query = "select id,basicsal from emppost where position_pid = '" . $_REQUEST["posi"] . "' and grade_gid='" . $_REQUEST["grade"] . "'";
        } else {
            $query = "select id,basicsal from emppost where position_pid = '" . $_REQUEST["posi"] . "'";
        }
        $res = Search($query);
        if ($result = mysqli_fetch_assoc($res)) {
            $out = $result["id"] . "#" . $result["basicsal"];
        }

        $grades = "";
        $query = "SELECT * FROM `grade` where gid in (SELECT grade_gid FROM `emppost` where position_pid = '" . $_REQUEST["posi"] . "')";
        $res = Search($query);
        while ($result = mysqli_fetch_assoc($res)) {
            $grades .= "<option value=". $result["gid"]."> ". $result["name"] ." </option>";
        }

        $out .= "###/#".$grades;

    }
    if ($_REQUEST["request"] == "getPOSIandGradefromID") {
        $query = "select position_pid,grade_gid from emppost where id = '" . $_REQUEST["id"] . "'";
        $res = Search($query);
        if ($result = mysqli_fetch_assoc($res)) {
            $out = $result["position_pid"] . "#" . $result["grade_gid"];
        }
    }

    echo $out;
}
?>