<?php
include '../DB/DB.php';
date_default_timezone_set('Asia/Colombo');
error_reporting(-1);
ini_set('display_errors', 'On');
set_error_handler("var_dump");

$emailErr = "";
if ($_REQUEST["request"] == "sendMail") {
    $email = $_REQUEST["to"];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
    } else {
        $to = $_REQUEST['to'];
        $subject = $_REQUEST['subject'];
        $message = $_REQUEST['message'];
        

        $headers[] = "From: system@satloi.com";
        $headers[] = "Reply-To: system@satloi.com";
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=iso-8859-1';

        $result = mail($to, $subject, $message, implode("\r\n", $headers));
        
        if ($result) {
            $emailErr = 'Mail Sent!';

            // $query = "insert into emaillog(recever,type,subject,email,date,time,status) values('" . $to . "','Birthday','Upcoming Birthdays','Upcoming Birthday List','" . date("Y/m/d") . "','" . date("H:i:s") . "','Sent')";
            // $res = SUD($query);

            // $emailErr = $res;
        } else {
            $emailErr = 'Sending Failed';
        }
    }

    echo $emailErr;
}

?>