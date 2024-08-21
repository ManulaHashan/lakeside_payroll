<?php
error_reporting(0);
session_start();
include '../DB/DB.php';
$DB = new Database();
date_default_timezone_set('Asia/Colombo');

if (isset($_REQUEST["request"])) {
  $out = "";
  if ($_REQUEST["request"] == "getSettings") {
    $out .= "<table style='width: 100%;' class='table table-striped table-bordered'><thead class='thead-dark' style='position : sticky; top : 0;  z-index: 0; background-color: #ffffff;'>
        <tr>
        <th>Roster Code</th>
        <th>Roster Name</th>
        <th width='5%'>In Time</th>
        <th width='5%'>Out Time</th>
        <th>Late Calculation Time</th>
        <th width='10%'>Halfday Morning</th>
        <th>Halfday Morning Late Calculation Time</th>
        <th width='10%'>Halfday Evening</th>
        <th>Halfday Evening Late Calculation Time</th>
        <th width='10%'>Short Leave Morning</th>
        <th>Short Leave Morning Late Calculation Time</th>
        <th width='10%'>Short Leave Evening</th>
        <th>Short Leave Evening Late Calculation Time</th>
        <th>Color Code</th>
        </tr>
        </thead><tbody>";



    $query = "select * from shift_working_time_profile_settings order by name ASC";

    $res = Search($query);
    while ($result = mysqli_fetch_assoc($res)) {
      if ($result["sh_intime"] == "00:00:00" || $result["sh_intime"] == "") {
        $INTIME = "";
      } else {
        $INTIME = date("H:i A", strtotime($result["sh_intime"]));
      }

      if ($result["sh_outtime"] == "00:00:00" || $result["sh_outtime"] == "") {
        $OUTIME = "";
      } else {
        $OUTIME = date("H:i A", strtotime($result["sh_outtime"]));
      }

      if ($result["sh_working_late"] == "00:00:00" || $result["sh_working_late"] == "") {
        $WLATEIME = "";
      } else {
        $WLATEIME = date("H:i A", strtotime($result["sh_working_late"]));
      }

      if ($result["sh_half_m_late"] == "00:00:00" || $result["sh_half_m_late"] == "") {
        $H_M_LATEIME = "";
      } else {
        $H_M_LATEIME = date("H:i A", strtotime($result["sh_half_m_late"]));
      }


      if ($result["sh_half_e_late"] == "00:00:00" || $result["sh_half_e_late"] == "") {
        $H_E_LATETIME = "";
      } else {
        $H_E_LATETIME = date("H:i A", strtotime($result["sh_half_e_late"]));
      }


      if ($result["sh_short_m_late"] == "00:00:00" || $result["sh_short_m_late"] == "") {
        $S_M_LATETIME = "";
      } else {
        $S_M_LATETIME = date("H:i A", strtotime($result["sh_short_m_late"]));
      }

      if ($result["sh_short_e_late"] == "00:00:00" || $result["sh_short_e_late"] == "") {
        $S_E_LATETIME = "";
      } else {
        $S_E_LATETIME = date("H:i A", strtotime($result["sh_short_e_late"]));
      }




      $out .= "<tr onclick='selectSettingsData(" . $result["swtpsid"] . ")' style='cursor:pointer;'>
                      <td align='center'>" . $result["rost_code"] . "</td>
                      <td align='center'>" . $result["name"] . "</td>
                      <td align='center'>" . $INTIME . "</td>
                      <td align='center'>" . $OUTIME . "</td>
                      <td align='center'>" . $WLATEIME . "</td>
                      <td>" . $result["sh_half_slot_morning"] . "</td>
                      <td align='center'>" . $H_M_LATEIME . "</td>
                      <td>" . $result["sh_half_slot_evening"] . "</td>
                      <td align='center'>" . $H_E_LATETIME . "</td>
                      <td>" . $result["sh_short_morning"] . "</td>
                      <td align='center'>" . $S_M_LATETIME . "</td>
                      <td>" . $result["sh_short_evening"] . "</td>
                      <td align='center'>" . $S_E_LATETIME . "</td>
                      <td style='background-color:" . $result["clr_code"] . ";'></td>
                   </tr>";
    }

    $out .= "</tbody></table>";

    echo $out;
  } else if ($_REQUEST["request"] == "SaveSettings") {

    if (empty($_REQUEST["halfMIntime"]) && empty($_REQUEST["halfMOuttime"])) {
      $half_Morning = "";
    } else {
      $half_Morning = $_REQUEST["halfMIntime"] . " AM - " . $_REQUEST["halfMOuttime"] . " PM";
    }

    if (empty($_REQUEST["halfEIntime"]) && empty($_REQUEST["halfEOuttime"])) {
      $half_Evening = "";
    } else {
      $half_Evening = $_REQUEST["halfEIntime"] . " PM - " . $_REQUEST["halfEOuttime"] . " PM";
    }


    if (empty($_REQUEST["shortMIntime"]) && empty($_REQUEST["shortMOuttime"])) {
      $Short_Morning = "";
    } else {
      $Short_Morning = $_REQUEST["shortMIntime"] . " AM - " . $_REQUEST["shortMOuttime"] . " AM";
    }


    if (empty($_REQUEST["shortEIntime"]) && empty($_REQUEST["shortEOuttime"])) {
      $Short_Evening = "";
    } else {
      $Short_Evening = $_REQUEST["shortEIntime"] . " PM - " . $_REQUEST["shortEOuttime"] . " PM";
    }


    $res = Search("select swtpsid from shift_working_time_profile_settings where name = '" . $_REQUEST["wrkname"] . "'");
    if ($resultss = mysqli_fetch_assoc($res)) {
      $out .= "This Profile Is Already Exists!";
    } else {
      if (empty($_REQUEST["intime"]) && empty($_REQUEST["outtime"]) && empty($_REQUEST["wrkLate"]) && empty($_REQUEST["halfMLate"]) && empty($_REQUEST["halfELate"]) && empty($_REQUEST["shrtMLate"]) && empty($_REQUEST["shrtELate"])) {
        $querySettings = "insert into shift_working_time_profile_settings(name, sh_intime, sh_outtime, sh_half_slot_morning, sh_half_slot_evening, sh_short_morning, sh_short_evening, sh_working_late, sh_half_m_late, sh_half_e_late, sh_short_m_late, sh_short_e_late, clr_code,rost_code) values('" . $_REQUEST["wrkname"] . "',NULL,NULL,'" . $half_Morning . "','" . $half_Evening . "','" . $Short_Morning . "','" . $Short_Evening . "',NULL,NULL,NULL,NULL,NULL,'" . $_REQUEST["clrcode"] . "' ,'" . $_REQUEST["wrkCode"] . "')";
      } else {
        $querySettings = "insert into shift_working_time_profile_settings(name, sh_intime, sh_outtime, sh_half_slot_morning, sh_half_slot_evening, sh_short_morning, sh_short_evening, sh_working_late, sh_half_m_late, sh_half_e_late, sh_short_m_late, sh_short_e_late, clr_code,rost_code) values('" . $_REQUEST["wrkname"] . "','" . $_REQUEST["intime"] . "','" . $_REQUEST["outtime"] . "','" . $half_Morning . "','" . $half_Evening . "','" . $Short_Morning . "','" . $Short_Evening . "','" . $_REQUEST["wrkLate"] . "','" . $_REQUEST["halfMLate"] . "','" . $_REQUEST["halfELate"] . "','" . $_REQUEST["shrtMLate"] . "','" . $_REQUEST["shrtELate"] . "','" . $_REQUEST["clrcode"] . "','" . $_REQUEST["wrkCode"] . "')";
      }

      $ret = SUD($querySettings);
      if ($ret == 1) {
        $out .= "Time Settings Saved Successfully!";
      } else {
        $out .= "Error";
      }
    }
    echo $out;
  } else if ($_REQUEST["request"] == "SelectSettings") {
    $res = Search("select * from shift_working_time_profile_settings where swtpsid = '" . $_REQUEST["workingTimeID"] . "'");
    while ($resultss = mysqli_fetch_assoc($res)) {
      $out .= implode("@", $resultss);
    }

    echo $out;
  } else if ($_REQUEST["request"] == "UpdateSettings") {

    if (empty($_REQUEST["halfMIntime"]) && empty($_REQUEST["halfMOuttime"])) {
      $half_Morning = "";
    } else {
      $half_Morning = $_REQUEST["halfMIntime"] . " AM - " . $_REQUEST["halfMOuttime"] . " PM";
    }

    if (empty($_REQUEST["halfEIntime"]) && empty($_REQUEST["halfEOuttime"])) {
      $half_Evening = "";
    } else {
      $half_Evening = $_REQUEST["halfEIntime"] . " PM - " . $_REQUEST["halfEOuttime"] . " PM";
    }


    if (empty($_REQUEST["shortMIntime"]) && empty($_REQUEST["shortMOuttime"])) {
      $Short_Morning = "";
    } else {
      $Short_Morning = $_REQUEST["shortMIntime"] . " AM - " . $_REQUEST["shortMOuttime"] . " AM";
    }


    if (empty($_REQUEST["shortEIntime"]) && empty($_REQUEST["shortEOuttime"])) {
      $Short_Evening = "";
    } else {
      $Short_Evening = $_REQUEST["shortEIntime"] . " PM - " . $_REQUEST["shortEOuttime"] . " PM";
    }

    if (empty($_REQUEST["intime"]) && empty($_REQUEST["outtime"]) && empty($_REQUEST["wrkLate"]) && empty($_REQUEST["halfMLate"]) && empty($_REQUEST["halfELate"]) && empty($_REQUEST["shrtMLate"]) && empty($_REQUEST["shrtELate"])) {
      $queryUpdateSettings = "Update shift_working_time_profile_settings set name='" . $_REQUEST["wrkname"] . "',sh_intime=NULL,sh_outtime=NULL,sh_half_slot_morning='" . $half_Morning . "',sh_half_slot_evening='" . $half_Evening . "',sh_short_morning='" . $Short_Morning . "',sh_short_evening='" . $Short_Evening . "',sh_working_late=NULL,sh_half_m_late=NULL,sh_half_e_late=NULL,sh_short_m_late=NULL,sh_short_e_late=NULL,clr_code='" . $_REQUEST["clrcode"] . "', rost_code ='" . $_REQUEST["wrkCode"] . "' where swtpsid='" . $_REQUEST["SWTID"] . "'";
    } else {
      $queryUpdateSettings = "Update shift_working_time_profile_settings set name='" . $_REQUEST["wrkname"] . "',sh_intime='" . $_REQUEST["intime"] . "',sh_outtime='" . $_REQUEST["outtime"] . "',sh_half_slot_morning='" . $half_Morning . "',sh_half_slot_evening='" . $half_Evening . "',sh_short_morning='" . $Short_Morning . "',sh_short_evening='" . $Short_Evening . "',sh_working_late='" . $_REQUEST["wrkLate"] . "',sh_half_m_late='" . $_REQUEST["halfMLate"] . "',sh_half_e_late='" . $_REQUEST["halfELate"] . "',sh_short_m_late='" . $_REQUEST["shrtMLate"] . "',sh_short_e_late='" . $_REQUEST["shrtELate"] . "',clr_code='" . $_REQUEST["clrcode"] . "' , rost_code ='" . $_REQUEST["wrkCode"] . "' where swtpsid='" . $_REQUEST["SWTID"] . "'";
    }

    $ret = SUD($queryUpdateSettings);

    if ($ret == 1) {
      $out .= "Time Settings Update Successfully!";
    } else {
      $out .= "Error";
    }

    echo $out;
  } else if ($_REQUEST["request"] == "CheckSettings") {
    $query = "select swtpsid from shift_working_time_profile_settings where LOWER(name)= LOWER('" . $_REQUEST["WRKNAME"] . "')";
    $res = Search($query);
    if ($Result = mysqli_fetch_assoc($res)) {
      $out .= "OK";
    } else {
      $out .= "NO";
    }

    echo $out;
  }
}
