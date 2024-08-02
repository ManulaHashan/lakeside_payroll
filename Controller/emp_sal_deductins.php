<?php
error_reporting(0);
include '../DB/DB.php';
$DB = new Database();

if (isset($_REQUEST["request"])) {
    $out = "";
    if ($_REQUEST["request"] == "getDeductions") {
        $out = "<table class='table table-striped' width='100%'>
                                <tr>
                                    <th>Added Date</th>
                                    <th>Description</th>
                                    <th>Amount Rs.</th>
                                    <th>Deduction Count</th>
                                    <th>Deduction Month</th>
                                    <th>Deduction Year</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>";


        $query = "select * from salerydeductions where user_uid = '" . $_REQUEST["uid"] . "' and isactive = '" . $_REQUEST["status"] . "' order by sdid DESC";
        $res = Search($query);
        while ($result = mysqli_fetch_assoc($res)) {
            
            if ($result["isactive"] == "1") //new dev 2023-10-30
            {
                $Status = "Ongoing";

                $out .= "<tr><td>" . $result["date"] . "</td><td>" . $result["description"] . "</td><td>" . $result["total"] . "</td><td align='center'>" . $result["did_count"] . "</td><td align='center'>" . date("F", mktime(0, 0, 0, $result["month"], 10)) . "</td><td align='center'>" . $result["year"] . "</td><td>" . $Status . "</td><td><img src='../Icons/remove.png' onclick='removeDeduction(" . $result["sdid"] . ")'></td></tr>"; //new dev 2023-10-30
            }
            else
            {
                $Status = "Completed";

                $out .= "<tr><td>" . $result["date"] . "</td><td>" . $result["description"] . "</td><td>" . $result["total"] . "</td><td align='center'>" . $result["did_count"] . "</td><td align='center'>" . date("F", mktime(0, 0, 0, $result["month"], 10)) . "</td><td align='center'>" . $result["year"] . "</td><td>" . $Status . "</td><td></td></tr>"; //new dev 2023-10-30
            }
  
        }
        $out .= "</table>";
    }
    if ($_REQUEST["request"] == "addDeductions") {  //new dev 2023-10-30
        $date = $_REQUEST["date"];
        if($_REQUEST["date"] == "none"){
            $date = date('Y-m-d');
        }
        
         // Loop through the months in the range
        for ($i = 0; $i < $_REQUEST["DidCount"]; $i++) 
        { 
          // $currentMonth = $_REQUEST["month"] + $i;
          // $currentYear = $_REQUEST["year"];

          //   if ($currentMonth > 12) {
          //       $currentMonth -= 12;
          //       $currentYear++;
          //   }

            $currentMonth = ($_REQUEST["month"] + $i) % 12;
            $currentYear = $_REQUEST["year"] + floor(($_REQUEST["month"] + $i - 1) / 12);
            // Adjust for zero-based modulus
            $currentMonth = ($currentMonth == 0) ? 12 : $currentMonth;

            $deductionMonth = sprintf("%d-%02d", $currentYear, $currentMonth);

            $deductData = explode("-", $deductionMonth);

            $query = "insert into salerydeductions(User_uid, date,total,description,isactive,month,year,did_count,did_typ,remark) values('" . $_REQUEST["uid"] . "','" . $date . "','" . $_REQUEST["amount"] . "','" . $_REQUEST["des"] . "','1','" . $deductData[1] . "','" . $deductData[0] . "','1','".$_REQUEST["diduction_typ"]."','" . $_REQUEST["did_remark"] . "')";
            $res = SUD($query);
        }

        if ($res = "1") {
            $out = "Record Added!";
        } else {
            $out = "Record Adding Error!";
        }
    }
    if ($_REQUEST["request"] == "deleteDeductions") {
        // $query = "update salerydeductions set isactive='0' where sdid = '" . $_REQUEST["id"] . "'";
        $query = "delete from salerydeductions where sdid = '" . $_REQUEST["id"] . "'"; //new dev 2023-10-30

        $res = SUD($query);
        if ($res = "1") {
            $out = "Record Deleted!";
        } else {
            $out = "Record Deleting Error!";
        }
    }

    if ($_REQUEST["request"] == "addLoan") {

        $query = "select * from factoryloan where User_uid = '" . $_REQUEST["uid"] . "' and date = '" . $_REQUEST["date"] . "' and amount = '" . $_REQUEST["amount"] . "'";
        $res = Search($query);
        if ($result = mysqli_fetch_assoc($res)) 
        {
            $out = "Loan Already Exsist!";
        } 
        else 
        {
            // Loop through the months in the range
            for ($i = 0; $i < $_REQUEST["ints"]; $i++) 
            { 
              // $currentMonth = $_REQUEST["month"] + $i;
              // $currentYear = $_REQUEST["year"];

              //   if ($currentMonth > 12) {
              //       $currentMonth -= 12;
              //       $currentYear++;
              //   }

                $currentMonth = ($_REQUEST["month"] + $i) % 12;
                $currentYear = $_REQUEST["year"] + floor(($_REQUEST["month"] + $i - 1) / 12);
                // Adjust for zero-based modulus
                $currentMonth = ($currentMonth == 0) ? 12 : $currentMonth;

                $loanMonths = sprintf("%d-%02d", $currentYear, $currentMonth);

                $loanData = explode("-", $loanMonths);

                $query = "insert into factoryloan(date, amount, interest, installments, installment, status, User_uid, year, month, real_installments, remark) values('" . $_REQUEST["date"] . "','" . $_REQUEST["amount"] . "','" . $_REQUEST["interest"] . "','1','" . $_REQUEST["lia"] . "','1','" . $_REQUEST["uid"] . "','" . $loanData[0] . "','" . $loanData[1] . "','" . $_REQUEST["ints"] . "','" . $_REQUEST["loan_rmrk"] . "')";
                $res = SUD($query);
            }

            if ($res == "1") {
                $out = "Loan Added!";
            } else {
                $out = "Record Adding Error!";
            }
        }
    }


    if ($_REQUEST["request"] == "deleteLoand") {//new dev 2023-10-30

        $query = "delete from factoryloan where flid = '" . $_REQUEST["id"] . "'"; //new dev 2023-10-30

        $res = SUD($query);
        if ($res = "1") {
            $out = "Record Deleted!";
        } else {
            $out = "Record Deleting Error!";
        }
    }



    if ($_REQUEST["request"] == "updateLoan") {
        $query = "update factoryloan set User_uid='" . $_REQUEST["uid"] . "',date='" . $_REQUEST["date"] . "',amount='" . $_REQUEST["amount"] . "',interest='" . $_REQUEST["interest"] . "',installments='" . $_REQUEST["ints"] . "',installment='" . $_REQUEST["lia"] . "',status='" . $_REQUEST["status"] . "' where flid = '" . $_REQUEST["lid"] . "'";
        $res = SUD($query);
        if ($res == "1") {

             $restt = Search("select status from factoryloan where flid = '" . $_REQUEST["lid"] . "'");
             if ($resultz = mysqli_fetch_assoc($restt)) {
                if($resultz["status"] == 0)
                {
                    $querys = "update factoryloan set complete_date='" . date("Y-m-d") . "' where flid = '" . $_REQUEST["lid"] . "'";
                    $rest = SUD($querys);
                }
             }

             $out = "Loan Updated!";

        } else {
            $out = "Record Updating Error!";
        }
    }


    if ($_REQUEST["request"] == "getLoans") {
        $out = "<table class='table table-striped' width='100%'>
                                <tr>
                                    <th>Added Date</th>
                                    <th>Loan Amount Rs.</th>
                                    <th>Installment Rs.</th>
                                    <th>Installment Count</th>
                                    <th>Loan Year & Month</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>";


        $query = "select * from factoryloan where User_uid='" . $_REQUEST["uid"] . "' and status = '" . $_REQUEST["status"] . "'";
        $res = Search($query);
        while ($result = mysqli_fetch_assoc($res)) {

            if ($result["status"] == "1") //new dev 2023-10-30
            {
                $Status = "Ongoing";

                $out .= "<tr><td>" . $result["date"] . "</td><td>" . $result["amount"] . "</td><td>" . $result["installment"] . "</td><td align='center'>" . $result["installments"] . "</td><td>" . $result["year"] . " - " . date("F", mktime(0, 0, 0, $result["month"], 10)) . "</td><td>" . $Status . "</td><td><img src='../Icons/remove.png' onclick='removeLoan(" . $result["flid"] . ")'></td></tr>"; //new dev 2023-10-30
            }
            else
            {
                $Status = "Completed";

                $out .= "<tr><td>" . $result["date"] . "</td><td>" . $result["amount"] . "</td><td>" . $result["installment"] . "</td><td align='center'>" . $result["installments"] . "</td><td>" . $result["year"] . " - " . date("F", mktime(0, 0, 0, $result["month"], 10)) . "</td><td>" . $Status . "</td><td></td></tr>"; //new dev 2023-10-30
            }

        }
        $out .= "</table>";
    }
    if ($_REQUEST["request"] == "getLoan") {
        $query = "select * from factoryloan where flid='" . $_REQUEST["lid"] . "'";
        $res = Search($query);
        if ($result = mysqli_fetch_assoc($res)) {
            $out = implode("#", $result);
        } else {
            $out = "Loan not found!";
        }
    }

    echo $out;
}