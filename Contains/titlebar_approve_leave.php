<div class="titlebarDiv" style="width:100%; background: linear-gradient(90deg, rgba(204,204,204,1) 0%, rgba(54,125,148,1) 54%, rgba(31,32,32,1) 74%);">
    <table width="100%" style="font-size: 18px; font-family: Constantia, 'Lucida Bright', 'DejaVu Serif', 'Georgia', 'serif';">
        <tr valign="middle">
            <td width="1px;" class="" style="height: 60px;"><img src="../Images/letter/7.png" style="width:50px;margin-top:5px;cursor: pointer;" onclick="window.location.href = 'Home.php'"></td>
            <td width="">
                <h2 style="color: white; margin: 0px; font-size: 28px;">pex Payroll | Lakeside Adventist Hospital</h2>
            </td>
            
            <td align="right">
                <?php
                if (isset($_SESSION["uid"])) {
                    $query = "select fname,lname from user where uid = '" . $_SESSION["uid"] . "'";
                    $res = Search($query);
                    if ($result = mysqli_fetch_assoc($res)) {
                        echo "Welcome  " . $result["fname"] . " " . "&nbsp;&nbsp;&nbsp;";
                    } else {
                        echo "Please Login!";
                    }
                }
                ?>
            </td>
        </tr>
    </table>
</div>