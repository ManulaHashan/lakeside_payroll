<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Forget Password | Apex Payroll</title>
        <!-- Favicons -->
         <link rel="apple-touch-icon" sizes="180x180" href="../Images/favicon/apple-touch-icon.png">
         <link rel="icon" type="image/png" sizes="32x32" href="../Images/favicon/favicon-32x32.png">
         <link rel="icon" type="image/png" sizes="16x16" href="../Images/favicon/favicon-16x16.png"> 
        <!-- <link href="../Styles/Stylie.css" rel="stylesheet" type="text/css"> -->
        <link href="../Styles/contains.css" rel="stylesheet" type="text/css">
        <script src="../JS/jquery-3.1.0.js"></script>

        <script type="text/javascript">
            window.onload = function() {
                document.getElementById('footerspace').style.height = (window.innerHeight - 350) + "px";
                $('#step1').hide();
                $('#step2').hide();
                $('#loading').hide();
            };

            $(document).ajaxStart(function() {
                $('#loading').show();
            }).ajaxStop(function() {
                $('#loading').hide();
            });

            var resetCode;
            function sendResetCode() {
                var one = document.getElementById('ansone').value;
                var two = document.getElementById('anstwo').value;
                var un = document.getElementById('un').value;
                $.ajax({
                    type: 'POST',
                    url: '../Controller/emp_sendResetCode.php?type=getrc&ansone=' + one + '&anstwo=' + two + '&un=' + un,
                    success: function(data) {
                        alert(data);
                        if (data !== "No") {
                            resetCode = data;
                            $('#step1').show();
                        } else {
                            alert("Invalied Details! Please try again...");
                            $('#step1').hide();
                            resetCode = "";
                        }
                    }
                });

            }

            function resetPwFields() {
                var fieldcode = document.getElementById('rcode').value;
                if (resetCode === fieldcode) {
                    $('#step2').show();
                } else {
                    alert("Invalied Reset Code! Try again...");
                    $('#step2').hide();
                }
            }

            function resetPw() {
                var ps = document.getElementById('ps').value;
                var ps2 = document.getElementById('ps2').value;
                var rcode = document.getElementById('rcode').value;
                if (ps === ps2) {
                    $.ajax({
                        type: 'POST',
                        url: '../Controller/emp_sendResetCode.php?type=reset&ps=' + ps + '&rcode=' + rcode ,
                        success: function(data) {
                            if (data === "OK") {
                                alert("Password changed successfully!");
                                window.location.href = "../index.php";
                            } else {
                                alert("Password changing error! Try again...");
                            }
                        }
                    });
                } else {
                    alert("Password doest not match!");
                }
            }
        </script>
    </head>

    <body>
        <blockquote>
            <center>
            <h2>Password Reset</h2>

            <p>Please answer for following questions for reset your password.</p>

            <table>
                <tr>
                    <td><p class="form-label">Username</p></td>
                    <td></td>
                    <td><input id="un" type="text" name="un" class="input-text"></td>
                </tr>            
                <tr>
                    <td><p class="form-label">What is your favorite color? &nbsp;</p></td>
                    <td></td>
                    <td><input id="ansone" type="text" name="ansone" class="input-text"></td>
                </tr>
                <tr>
                    <td><p class="form-label">What is your birth city?</p></td>
                    <td></td>
                    <td><input id="anstwo" type="text" name="anstwo" class="input-text"></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td><input type="button" name="submit" class="btn" value="Get Reset Code" onclick="sendResetCode()"></td>
                </tr>
            </table>
            </br>
                <div id="step1">
                    <p>You will receive your reset code through your email. Please get the reset code and enter below.</p>
                    <h3>Reset Code</h3>
                    <input id="rcode" type="text" name="rcode" class="input-text"/>
                    <input type="button" name="submit" class="btn" value="Get Reset Code" onclick="resetPwFields()">
                </div>
            </br>
                <div id="step2">
                    <table>
                        <tr>
                            <td><p class="form-label">New Password</p></td>
                            <td></td>
                            <td><input id="ps" type="password" name="ps" class="input-text"></td>
                        </tr>            
                        <tr>
                            <td><p class="form-label">Confirm Password</p></td>
                            <td></td>
                            <td><input id="ps2" type="password" name="ps2" class="input-text"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td></td>
                            <td><input type="button" name="submit" class="btn" value="Reset Password" onclick="resetPw()"></td>
                        </tr>
                    </table>
                </div>

            </center>

        </blockquote>
        <div id="loading">
            <p><img src="../Images/load.gif"/> Loading... Please Wait!</p>
        </div>

        <?php include("../Contains/footer.php"); ?>
    </body>
</html>