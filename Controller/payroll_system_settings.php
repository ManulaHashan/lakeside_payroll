<?php
session_start();
include '../DB/DB.php';
$DB = new Database();
date_default_timezone_set('Asia/Colombo');

if (isset($_REQUEST["request"])) 
{
  $out = "";
  if ($_REQUEST["request"] == "getSettings") 
  {
      $out .= "<table style='width: 700px;' class='table table-striped table-bordered'><thead class='thead-dark' style='position : sticky; top : 0;  z-index: 0; background-color: #ffffff;'>
        <tr>
        <th>Title</th>
        <th>Time</th>
        </tr>
        </thead>";

        

        $query = "select * from settings_working_times where isactive = '1'";

        $res = Search($query);
        while ($result = mysqli_fetch_assoc($res)) 
        {
            $out .= "<tr onclick='selectSettingsData(".$result["swtid"].")' style='cursor:pointer;'>
                      <td>Working Time (Week Days)</td>
                      <td>".$result["intime"] . " AM - " . $result["outtime"] . " PM</td>
                   </tr>";

            $out .= "<tr onclick='selectSettingsData(".$result["swtid"].")' style='cursor:pointer;'>
                      <td>Late Calculate Time For Working Time (Week Days)</td>
                      <td>".$result["weekdays_late"] . " AM</td>
                   </tr>";
                   
            $out .= "<tr onclick='selectSettingsData(".$result["swtid"].")' style='cursor:pointer;'>
                      <td>OT Calculate Time For Working Time (Week Days)</td>
                      <td>".$result["weekdays_ot"] . " PM</td>
                   </tr>";              

            $out .= "<tr onclick='selectSettingsData(".$result["swtid"].")' style='cursor:pointer;'>
                      <td>Working Time (Weekends)</td>
                      <td>".$result["satintime"] . " AM - " . $result["satouttime"] . " PM</td>
                   </tr>";

            $out .= "<tr onclick='selectSettingsData(".$result["swtid"].")' style='cursor:pointer;'>
                      <td>Late Calculate For Working Time (Weekends)</td>
                      <td>".$result["weekends_late"] . " AM</td>
                   </tr>";

            $out .= "<tr onclick='selectSettingsData(".$result["swtid"].")' style='cursor:pointer;'>
                      <td>OT Calculate Time For Working Time (Weekends)</td>
                      <td>".$result["weekends_ot"] . " PM</td>
                   </tr>";                     

            $out .= "<tr onclick='selectSettingsData(".$result["swtid"].")' style='cursor:pointer;'>
                      <td>Halfday Morning Slot</td>
                      <td>".$result["half_slot_morning"] . "</td>
                   </tr>";

            $out .= "<tr onclick='selectSettingsData(".$result["swtid"].")' style='cursor:pointer;'>
                      <td>Late Calculate For Halfday Morning Slot</td>
                      <td>".$result["half_m_late"] . " PM</td>
                   </tr>";       

            $out .= "<tr onclick='selectSettingsData(".$result["swtid"].")' style='cursor:pointer;'>
                      <td>Halfday Evening Slot</td>
                      <td>".$result["half_slot_evening"] . "</td>
                   </tr>";

            $out .= "<tr onclick='selectSettingsData(".$result["swtid"].")' style='cursor:pointer;'>
                      <td>Late Calculate For Halfday Evening Slot</td>
                      <td>".$result["half_e_late"] . " PM</td>
                   </tr>";        
                   
            $out .= "<tr onclick='selectSettingsData(".$result["swtid"].")' style='cursor:pointer;'>
                      <td>Short Leave Morning Slot</td>
                      <td>".$result["short_morning"] . "</td>
                   </tr>";

            $out .= "<tr onclick='selectSettingsData(".$result["swtid"].")' style='cursor:pointer;'>
                      <td>Late Calculate For Short Leave Morning Slot</td>
                      <td>".$result["short_m_late"] . " AM</td>
                   </tr>";        
                   
            $out .= "<tr onclick='selectSettingsData(".$result["swtid"].")' style='cursor:pointer;'>
                      <td>Short Leave Evening Slot</td>
                      <td>".$result["short_evening"] . "</td>
                   </tr>";

            $out .= "<tr onclick='selectSettingsData(".$result["swtid"].")' style='cursor:pointer;'>
                      <td>Late Calculate For Short Leave Evening Slot</td>
                      <td>".$result["short_e_late"] . " PM</td>
                   </tr>";       

            $queryUser = "select fname from user where uid = '".$result["update_user"]."'";

            $resUser = Search($queryUser);
            if ($resultUser = mysqli_fetch_assoc($resUser)) 
            {
              $out .= "<tr style='cursor:pointer;'>
                      <td>Modified User</td>
                      <td>".$resultUser["fname"] . "</td>
                   </tr>";
            }

            $out .= "<tr style='cursor:pointer;'>
                      <td>Modified Date</td>
                      <td>".$result["date"] . "</td>
                   </tr>";       

                                                
           
        }
           
          $out .="</tr>";  
        
        
        $out .= "</table>";

        echo $out;
  }
  else if ($_REQUEST["request"] == "SaveSettings") 
  {

     $half_Morning = $_REQUEST["halfMIntime"] . " AM - " . $_REQUEST["halfMOuttime"] . " PM";
     $half_Evening = $_REQUEST["halfEIntime"] . " PM - " . $_REQUEST["halfEOuttime"] . " PM";
     $Short_Morning = $_REQUEST["shortMIntime"] . " AM - " . $_REQUEST["shortMOuttime"] . " AM";
     $Short_Evening = $_REQUEST["shortEIntime"] . " PM - " . $_REQUEST["shortEOuttime"] . " PM";


     $querySettings = "insert into settings_working_times(intime, outtime, half_slot_morning, half_slot_evening, short_morning, short_evening, date, isactive, update_user, satintime, satouttime, weekdays_late, weekdays_ot, weekends_late, weekends_ot, half_m_late, half_e_late, short_m_late, short_e_late) values('" . $_REQUEST["intime"] . "','". $_REQUEST["outtime"] ."','".$half_Morning."','".$half_Evening."','".$Short_Morning."','".$Short_Evening."','".date("Y-m-d")."','1','".$_SESSION["uid"]."','".$_REQUEST["SatIntime"]."','".$_REQUEST["SatOuttime"]."','".$_REQUEST["wrkLate"]."','".$_REQUEST["wrkOT"]."','".$_REQUEST["wrkEndLate"]."','".$_REQUEST["wrkEndOT"]."','".$_REQUEST["halfMLate"]."','".$_REQUEST["halfELate"]."','".$_REQUEST["shrtMLate"]."','".$_REQUEST["shrtELate"]."')";
 
      $ret = SUD($querySettings);

      if ($ret == 1) 
      {
         $out .= "Time Settings Saved Successfully!";
      }
      else
      {
         $out .= "Error";
      }

      echo $out;
  }
  else if ($_REQUEST["request"] == "SelectSettings") 
  {

      $query = "select * from settings_working_times where swtid = '".$_REQUEST["workingTimeID"]."'";

      $res = Search($query);
      while ($resultss = mysqli_fetch_assoc($res)) 
      {
         $out .= implode("#", $resultss);
      }

      echo $out;
  }
  else if ($_REQUEST["request"] == "UpdateSettings") 
  {

     $half_Morning = $_REQUEST["halfMIntime"] . " AM - " . $_REQUEST["halfMOuttime"] . " PM";
     $half_Evening = $_REQUEST["halfEIntime"] . " PM - " . $_REQUEST["halfEOuttime"] . " PM";
     $Short_Morning = $_REQUEST["shortMIntime"] . " AM - " . $_REQUEST["shortMOuttime"] . " AM";
     $Short_Evening = $_REQUEST["shortEIntime"] . " PM - " . $_REQUEST["shortEOuttime"] . " PM";

     $queryUpdateSettings = "Update settings_working_times set intime='" . $_REQUEST["intime"] . "',outtime='". $_REQUEST["outtime"] ."',half_slot_morning='".$half_Morning."',half_slot_evening='".$half_Evening."',short_morning='".$Short_Morning."',short_evening='".$Short_Evening."',date='" . date("Y-m-d") . "',update_user='".$_SESSION["uid"]."',satintime='".$_REQUEST["SatIntime"]."', satouttime='".$_REQUEST["SatOuttime"]."',weekdays_late='".$_REQUEST["wrkLate"]."',weekdays_ot='".$_REQUEST["wrkOT"]."',weekends_late='".$_REQUEST["wrkEndLate"]."',weekends_ot='".$_REQUEST["wrkEndOT"]."',half_m_late='".$_REQUEST["halfMLate"]."',half_e_late='".$_REQUEST["halfELate"]."',short_m_late='".$_REQUEST["shrtMLate"]."',short_e_late='".$_REQUEST["shrtELate"]."' where swtid='" . $_REQUEST["SWTID"] . "'";
 
      $ret = SUD($queryUpdateSettings);

      if ($ret == 1) 
      {
         $out .= "Time Settings Update Successfully!";
      }
      else
      {
         $out .= "Error";
      }

      echo $out;
  }
  else if ($_REQUEST["request"] == "CheckSettings") 
  {

      $query = "select swtid from settings_working_times where isactive = '1'";
      $res = Search($query);
      if ($Result = mysqli_fetch_assoc($res)) 
      {
        $out .= "OK";
      } 
      else 
      {
        $out .= "NO";
      }

      echo $out;
  }
  




}
    
?>