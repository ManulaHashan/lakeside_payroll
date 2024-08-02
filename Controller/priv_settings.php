<?php
error_reporting(0);
session_start();
date_default_timezone_set('Asia/Colombo');

include '../DB/DB.php';

$DB = new Database();

if (isset($_REQUEST["request"])) 
{
    $out = "";
    
    if( $_REQUEST["request"]=="updatePriv")
    {
        $obj = json_decode($_REQUEST["privdata"], true);
        $obj_prof_id = $obj["Priv_Prof_ID"];
        $obj_priv_data = $obj["Priv_Data"];

        $count_Priv = count($obj_priv_data);
        
        $return = SUD("delete from profile_wise_privileges where prof_id = '".$obj_prof_id."'");
        
        $data_count = 0;
        foreach ($obj_priv_data as $privitems) 
        {
            $arrayPrivData = explode("@", $privitems);
            $priv_id = $arrayPrivData[0];

            $insertPrivDataQuery = "insert into profile_wise_privileges(prof_id, priv_id) values('" . $obj_prof_id . "','" . $priv_id . "')"; 

            $return_PrivData = SUD($insertPrivDataQuery);

            if ($return_PrivData == 1) 
            {
               $data_count++;
            }
            else
            {
               $data_count = "0";
            }       
        }

        if ($count_Priv == $data_count) 
        {
           echo "1";
        }
        else
        {
           echo "0";
        }     
    }

    if( $_REQUEST["request"]=="viewPrivData")
    { 
        $PROF_ID = $_REQUEST["prof_id"];

        $PrivData = array();

        $res = Search("select a.priv_id,b.name from profile_wise_privileges a, features b where a.priv_id = b.fid and a.prof_id = '".$PROF_ID."' and b.isactive='1'");
        while ($result = mysqli_fetch_assoc($res)) 
        {
            if ($result["priv_id"] == "" || $result["priv_id"] == null) 
            {
              # code...
            }
            else
            {
               $priv_phase = $result["priv_id"]."@".$result["name"];
               array_push($PrivData, $priv_phase);
            }    
        }


        $count_Priv = count($PrivData);

        if ($count_Priv == "0") 
        {
           echo "0";
        }
        else
        {
          $privs = array(
            "Privileges_Data" => $PrivData
          );

          $json_data = json_encode($privs);
          echo $json_data;  
        }  
    }


    if( $_REQUEST["request"]=="addProf")
    { 
        $PROF_NAME = $_REQUEST["profName"];

        $res = Search("select fcid from feature_category where LOWERCASE(name) = LOWERCASE('".$PROF_NAME."') and isactive='1'");
        if ($result = mysqli_fetch_assoc($res)) 
        {
              echo "2";  
        }
        else
        {
            $return_ProfData = SUD("insert into feature_category(name, isactive) values('" . $PROF_NAME . "','1')");
            if ($return_ProfData == 1) 
            {
               echo "1";
            }
            else
            {
               echo "0";
            }
        }    
    }

    if( $_REQUEST["request"]=="deleteProf")
    { 
        $PROF_ID = $_REQUEST["profID"];
        
        $return_UpdProfData = SUD("update feature_category set isactive = '0' where fcid = '".$PROF_ID."'");
        if ($return_UpdProfData == 1) 
        {
           echo "1";
        }
        else
        {
           echo "0";
        }    
    }

    if( $_REQUEST["request"]=="viewProf")
    { 
        $res = Search("select fcid,name from feature_category where isactive='1'");
        while ($result = mysqli_fetch_assoc($res)) 
        {
            $out.="<tr><td>".$result["name"]."</td><td align='center'><img src='../Icons/remove.png' style='cursor: pointer;' onclick='deleteProfiles(" . $result["fcid"] . ")'></td></tr>";    
        }

        echo $out;    
    }

    if( $_REQUEST["request"]=="getProf")
    { 
        $res = Search("select fcid,name from feature_category where isactive='1'");
        while ($result = mysqli_fetch_assoc($res)) 
        {
            $out.="<option value=".$result["fcid"]."> ".$result["name"]."</option>";    
        }

        echo $out;    
    }
}      