<?php
include '../DB/DB.php';
$emailErr = "";
if ($_REQUEST["request"] == "sendMail") {
    $email = $_REQUEST["to"];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
    } else {
        $to = $_REQUEST['to'];
        $PONumber = $_REQUEST['massage'];
        $subject = $_REQUEST['sub'];
        $company = $_REQUEST['company'];
        $txt = $_REQUEST['massage'];
        $url = "http://dandynet.com/Model/PO&GrnRep.php?id=" . $txt;
        $txt = file_get_contents($url);

        $headers[] = "From: system@dandynet.com";
        $headers[] = "Reply-To: naveen@dandynet.com";
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=iso-8859-1';

        $result = mail($to, $subject, $txt, implode("\r\n", $headers));
        $result = mail("dandyapp@gmail.com", $subject, $txt, implode("\r\n", $headers));
        if ($result) {
            $emailErr = 'Mail Sent!';
            $query = "insert into emaillog(recever,type,subject,email,date,time,status) values('" . $to . "','PO','PurchaseOrder','P.O.Number " . $PONumber . "','" . date("Y/m/d") . "','" . date("h:i:sa") . "','Sent')";
            $res = SUD($query);

            $emailErr = $res;
        } else {
            $emailErr = 'Sending Failed';
        }
    }
}

if ($_REQUEST["request"] == "sendWorkSheetMail") {
    $email = $_REQUEST["to"];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
    } else {
        $to = $_REQUEST['to'];
        $subject = $_REQUEST['sub'];
        $company = $_REQUEST['company'];
        $oid = $_REQUEST['oid'];
        $style = $_REQUEST['style'];

        $url = "http://dandynet.com/Model/WorkSheet.php?oid=" . $oid . "&style=" . $style . "&email=true";
//        echo $url;
        $txt = file_get_contents($url);
//        $txt = "new Mail";
//
        $headers[] = "From: system@dandynet.com";
        $headers[] = "Reply-To: naveen@dandynet.com";
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=utf-8';
//
        $result = mail($to, $subject, $txt, implode("\r\n", $headers));
        $result = mail("dandyapp@gmail.com", $subject, $txt, implode("\r\n", $headers));
//        mail("samadhiprg@gmail.com","test","okokok");

//echo $txt;
        if ($result) {
            $query = "insert into emaillog(recever,type,subject,email,date,time,status) values('" . $to . "','PO','Work Sheet','Order ID " . $oid . "','" . date("Y/m/d") . "','" . date("h:i:sa") . "','Sent')";
            $res = SUD($query);
            $emailErr = $res;
        } else {
            $emailErr = 'Sending Failed';
        }
    }
}

if ($_REQUEST["request"] == "sendGPassMail") {
    $email = $_REQUEST["to"];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
    } else {
        $to = $_REQUEST['to'];
        $subject = $_REQUEST['sub'];
        $company = $_REQUEST['company'];
        $gpid = $_REQUEST['gpid'];
        $message = $_REQUEST['massage'];

        $headers[] = "From: system@dandynet.com";
        $headers[] = "Reply-To: naveen@dandynet.com";
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=utf-8';

        $result = mail($to, $subject, $message, implode("\r\n", $headers));
        $result = mail("dandyapp@gmail.com", $subject, $message, implode("\r\n", $headers));
        if ($result) {
            $query = "insert into emaillog(recever,type,subject,email,date,time,status) values('" . $to . "','GP','Gate Pass','Pass ID " . $gpid . "','" . date("Y/m/d") . "','" . date("h:i:sa") . "','Sent')";
            $res = SUD($query);
            $emailErr = $res;
        } else {
            $emailErr = 'Sending Failed';
        }
    }
}

if ($_REQUEST["request"] == "sendCustomGatePass") {
    $email = $_REQUEST["to"];
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailErr = "Invalid email format";
    } else {
        $to = $_REQUEST['to'];
        $subject = $_REQUEST['sub'];
        $cgpidfd = $_REQUEST['cgpidfd'];

        $url = "http://dandynet.com/Model/CustomGatePass.php?cgpid=" . $cgpidfd . "&email=true";
        $txt = file_get_contents($url);

        $headers[] = "From: system@dandynet.com";
        $headers[] = "Reply-To: naveen@dandynet.com";
        $headers[] = 'MIME-Version: 1.0';
        $headers[] = 'Content-type: text/html; charset=utf-8';

        $result = mail($to, $subject, $txt, implode("\r\n", $headers));
        $result = mail("dandyapp@gmail.com", $subject, $txt, implode("\r\n", $headers));
        if ($result) {
            $query = "insert into emaillog(recever,type,subject,email,date,time,status) values('" . $to . "','CGP','Custom Gate Pass','Pass ID " . $cgpidfd . "','" . date("Y/m/d") . "','" . date("h:i:sa") . "','Sent')";
            $res = SUD($query);
            $emailErr = $res;
        } else {
            $emailErr = 'Sending Failed';
        }
    }
}

echo $emailErr;
?>