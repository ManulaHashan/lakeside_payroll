<?php

session_start();
date_default_timezone_set('Asia/Colombo');


require_once '../DB/DB.php';
require_once '../Controller/emp_attendance_auto.php';
$DB = new Database();


$time1 = date("H:i:s", strtotime("08:30 AM"));
$time2 = date("H:i:s", strtotime("05:30 PM"));

$date1 = "2013-03-15";
$date2 = "2013-03-15";

$start = date_create($date1 . " " . $time1);
$end = date_create($date2 . " " . $time2);

$diff = date_diff($end, $start); 
$hour = $diff->format('%h');
$minutes = $diff->format('%i');

if(isset($_REQUEST["request"]))
{ 
	if($_REQUEST["request"]=="fpclient")
	{
		$user=$_REQUEST["user"];
		$date=$_REQUEST["date"];
		$time=$_REQUEST["time"];
		$data=$user." ".$date." ".$time;
		$Status = "";
		$Action = "C/In";

		$queryus = "select uid from user where jobcode = '".$user."' and isactive='1'";  //add isactive part
		$resus = Search($queryus);
		if ($resultus = mysqli_fetch_assoc($resus)) 
		{
			$userid=$resultus["uid"];
		}

		//check existance
		$resu = Search("select aid,intime,date from attendance where date = '" . $date . "' and user_uid ='".$userid."'");
		if ($resultu = mysqli_fetch_assoc($resu)) 
		{
			SUD("Insert into `status` (`status`) values('search')");

			$Action = "C/Out";
			$intime=$resultu["intime"];
			$savedDate=$resultu["date"];
			echo $intime."<br/>";

            //check time diffarence
			$time1 = date("H:i:s", strtotime($intime));
			$time2 = date("H:i:s", strtotime($time));

			$date1 = $savedDate;
			$date2 = $date;
			$start = date_create($date1 . " " . $time1);
			$end = date_create($date2 . " " . $time2);
			$diff = date_diff($end, $start); 
			$hour = $diff->format('%h');
			$minutes = $diff->format('%i');
			$seconds = strtotime($date1." ".$intime) - strtotime($date2." ".$time);
			$minutes = floor((($seconds - ($days * 86400) - ($hours * 3600))/60)*-1);
			SUD("Insert into `status` (`status`) values('".$minutes."')");

			if( $minutes >= 5)
			{
				SUD("Insert into `status` (`status`) values('5min')");
				updateUserAttendanceRecord($user, $Status, $Action, $user, $date, $time);
			}         

		}
		else
		{
			// echo "AWA-02";
			SUD("Insert into `status` (`status`) values('1st for user')");
			updateUserAttendanceRecord($user, $Status, $Action, $user, $date, $time);
		}
	}
}

?>