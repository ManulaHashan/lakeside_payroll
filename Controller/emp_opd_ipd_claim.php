<?php
error_reporting(0);
session_start();
date_default_timezone_set('Asia/Colombo');

include '../DB/DB.php';

$DB = new Database();

if (isset($_REQUEST["request"])) {
    $out = "";
    // *******************Load data to table******************************
    if ($_REQUEST["request"] == "getAllDetails") {

        $json_object = json_decode($_REQUEST["searchData"], true);

        $From = $json_object["From"];
        $To = $json_object["To"];
        $SearchSection = $json_object["SearchSection"];
        $Empdata = $json_object["Empdata"];
        $Claimstatus = $json_object["Claimstatus"];

        $output = "<table class='table table-bordered table-hover table-striped'>
                <thead style='position: sticky; top : 0; z-index: 0; background-color: #9eafba; color: black;'>
                <tr>
                        <th>Emp Name</th>
                         <th>Date</th>
                        <th align='center'>Section</th>
                        <th align='right'>Amount</th>
                        <th align='center'>Status</th>
                        <th>Reason</th></thead><tbody>";

        //$search_query = "select a.lname,a.jobcode,b.id,b.date,b.section,b.amount,b.reason,b.status from user a,opd_ipd_claim_bill b where a.uid = b.user_id order by b.date ASC";
        $search_query = "select a.lname, a.jobcode, b.id, b.date, b.section, b.amount, b.reason, b.status from user a, opd_ipd_claim_bill b where a.uid = b.user_id and b.date between '" . $From . "' and '" . $To . "' and b.section like '" . $SearchSection . "' and b.user_id like '" . $Empdata . "' and b.status like '" . $Claimstatus . "' order by b.date ASC;
";

        // echo $search_query;
        $tbl_data = Search($search_query);

        while ($row = mysqli_fetch_assoc($tbl_data)) {

            if ($row["status"] == "1") {
                $status = "Active";
            } else {
                $status = "Not Active";
            }
            if ($row["section"] == "1") {
                $Section = "OPD";
            } else {
                $Section = "IPD";
            }
            //id, date, section, user_id, amount, status, is_paid, reason

            $output .= "<tr onclick = 'loadSelectedRecord(" . $row["id"] . ");'>";
            $output .= "<td >" . $row["jobcode"] . " - " . $row["lname"] . "</td>";
            $output .= "<td >" . $row["date"] . "</td>";
            $output .= "<td align='center'>" . $Section . "</td>";
            $output .= "<td align='right'>" . $row["amount"] . "</td>";
            $output .= "<td align='center'>" .  $status . "</td>";
            $output .= "<td>" . $row["reason"] . "</td>";
            $output .= "</tr>";
        }

        $output .= "</tbody></table>";
        echo $output;
    }

    // *******************Load data to text feilds******************************
    if ($_REQUEST["request"] == "getAllDetailsByEmpID") {
        // echo $_REQUEST["EmployeeID"];

        $search_query = "select * from opd_ipd_claim_bill where id='" . $_REQUEST["EmpID"] . "'";
        $user_data = Search($search_query);
        while ($row = mysqli_fetch_assoc($user_data)) {
            $output = implode("#", $row);
        }
        echo $output;
    }


    // *******************Load claim data to Right side Table******************************
    if ($_REQUEST["request"] == "getAllClaimDetailsByEmpID") {
        $empID = $_REQUEST["EmpID"];


        $user_query = "select opd_claim_value, ipd_claim_value FROM user WHERE uid = '$empID'";
        $user_data = Search($user_query);
        $user_row = mysqli_fetch_assoc($user_data);


        $claim_query = "select section, SUM(amount) as total_taken FROM opd_ipd_claim_bill WHERE user_id = '" . $empID . "' and YEAR(date) = '" . date('Y') . "' GROUP BY section";
        $claim_data = Search($claim_query);

        $opd_taken = $ipd_taken = 0;

        while ($row = mysqli_fetch_assoc($claim_data)) {
            if ($row['section'] == '1') {
                $opd_taken = $row['total_taken'];
            } elseif ($row['section'] == '2') {
                $ipd_taken = $row['total_taken'];
            }
        }


        $response = array(
            'opd_limit' => (float)$user_row['opd_claim_value'],
            'ipd_limit' => (float)$user_row['ipd_claim_value'],
            'opd_taken' => (float)$opd_taken,
            'ipd_taken' => (float)$ipd_taken,
        );


        echo json_encode($response);
    }


    // *******************Save data to Data Base******************************
    if ($_REQUEST["request"] == "SaveClaimData") {
        $json_object = json_decode($_REQUEST["claimData"], true);

        $Empid = $json_object["Empid"];
        $Date = $json_object["Date"];
        $Section = $json_object["Section"];
        $Amount = $json_object["Amount"];
        $Reason = $json_object["Reason"];

        // echo $Empid . " " . $Date . " " . $Section . " " . $Amount . " " . $Reason;


        $query = "select id from opd_ipd_claim_bill WHERE date='" . $Date . "' AND user_id='" . $Empid . "' AND section='" . $Section . "' AND amount='" . $Amount . "'";
        $res = Search($query);
        if ($result = mysqli_fetch_assoc($res)) {
            $output = 0;
        } else {
            $query = "insert into opd_ipd_claim_bill( date, section, user_id, amount, status, is_paid, reason) values('" . $Date . "','" . $Section . "','" . $Empid . "','" . $Amount . "','1','0','" . $Reason . "')";
            $res = SUD($query);

            if ($res == 1) {
                $output = 1;
            } else {
                $output = 2;
            }
        }
        echo $output;
    }


    // **********************Update Data ***********************************

    if ($_REQUEST["request"] == "updateRecords") {
        $json_object = json_decode($_REQUEST["claimData"], true);

        $Claimid = $json_object["Claim_id"];
        $Empid = $json_object["Empid"];
        $Date = $json_object["Date"];
        $Section = $json_object["Section"];
        $Amount = $json_object["Amount"];
        $Reason = $json_object["Reason"];

        // echo "Entered Data: " . $Empid . "##" . $Date . "##" . $Section . "##" . $Amount . "##" . $Reason;

        $update_query = "update opd_ipd_claim_bill set date = '" . $Date . "', section = '" . $Section . "', amount = '" . $Amount . "', reason = '" . $Reason . "' where id = '" . $Claimid . "'";

        $result = SUD($update_query);
        if ($result == "1") {
            echo "1";  // Success
        } else {
            echo "2";  // Error
        }
    }
    echo $out;

    // **********************Delete Data ***********************************
    if ($_REQUEST["request"] == "deleteRecords") {
        // echo $_REQUEST["claimData"];

        $json_object = json_decode($_REQUEST["claimID"], true);

        $Claimid = $json_object["Claim_id"];
        $IsPaid = $json_object["Is_paid"];

        // echo "Entered Data: " . $Empid . "##" . $Date . "##" . $Section . "##" . $Amount . "##" . $Reason;

        $delete_query = "delete from opd_ipd_claim_bill where id = '" . $Claimid . "' and is_paid = '0'";


        $result = SUD($delete_query);
        if ($result == "1") {
            $error = "1";
        } else {
            $error = "2";
        }
        echo $error;
    }
}
