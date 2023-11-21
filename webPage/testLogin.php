<?php
session_start();

include 'conn.php';

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$logged_in = false;
$userExist = false;
$verificationCodeSent = false;

function generate_verification_code()
{
    return strval(rand(1000, 9999));
}

function send_verification_email($email, $code)
{
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->SMTPDebug = 2; // Enable verbose debug output
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com'; // Gmail SMTP server
    $mail->Port = 587; // TLS port
    $mail->SMTPAuth = true;
    $mail->Username = 'brandon.riveravenegas@sjsu.edu'; // Your Gmail address
    $mail->Password = 'BankNoir5'; // App password generated in your Google Account
    $mail->SMTPSecure = 'tls';

    $mail->setFrom('brandon.riveravenegas@sjsu.edu', 'NOIR CAPITAL BANK');
    $mail->addAddress($email);
    $mail->Subject = 'Verification Code for Your App';
    $mail->Body = "Your verification code is: $code";

    if (!$mail->send()) {
        echo 'Message could not be sent.';
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    } else {
        echo 'Verification code has been sent to your email.';
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // create connection
    $conn = mysqli_connect("localhost", "root", "", "bankregistration");

    // check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // select user's password and verification status from the database
    $sql = "SELECT * FROM bank_users WHERE username = '$username'";
    $results = mysqli_query($conn, $sql);

    if ($results) {
        $row = mysqli_fetch_assoc($results);
        if ($row["password1"] === $password) {
            if ($row["verified"]) {
                $logged_in = true;
                // User is not verified, send verification email
                $verificationCode = generate_verification_code();
                send_verification_email($row["email"], $verificationCode);
                $verificationCodeSent = true;
                // ... rest of your login logic ...
            } else {

                $logged_in = false;
            }
        } else {
            $userExist = true;
        }
    }

    mysqli_close($conn); // close connection
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NOIR - Login</title>
    <link rel="stylesheet" type="text/css" href="home.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400&display=swap" rel="stylesheet">
</head>

<body>
    <header>
        <a href="homepage.html" class="logo">NOIR CAPITAL BANK</a>
        <ul class="navlist">
            <li><a href="homepage.html">Home</a></li>
            <li><a href="login.php">Sign In</a></li>
            <li><a href="contact1.php">Contact</a></li>
            <li><a href="about.html">About</a></li>
            <li><a href="atm.html">ATM</a></li>
        </ul>
        <div class="bx bx-menu" id="menu-icon"></div>
    </header>

    <section class="bank">
        <div class="bank-text">
            <h5>Sign On To Manage Your Accounts</h5>
            <h4>How Can We Assist You?</h4>
            <h1>Keep Track Of All Your Financials, Smarter</h1>
            <p>Open A Checkings Or Savings Account Today.</p>
        </div>
        <div class="bank-login">
            <form action="/login.php" method="post">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>

                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>

                <button type="submit">Log In</button>
                <?php
                if ($logged_in) {
                    header("Location: dashboard.php");
                    exit();
                } else if ($userExist) {
                    echo '<script type="text/javascript">window.onload = function () { alert("Username or Password is incorrect!!"); } </script>';
                } else if ($verificationCodeSent) {
                    echo '<script type="text/javascript">window.onload = function () { alert("Verification code has been sent to your email."); } </script>';
                }
                ?>
                <button onclick="openPopOutWindow()">No Account? Sign Up Now</button>
            </form>
        </div>
    </section>
    <div class="icons">
    </div>

    <script>
        function openPopOutWindow() {
            var url = 'bankregistration.html'; // Replace with the actual URL
            var width = 550;
            var height = 550;
            var left = (screen.width - width) / 2;
            var top = (screen.height - height) / 2 + -60;

            // Open a new pop-out window
            window.open(url, 'PopOutWindow', 'width=' + width + ',height=' + height + ',left=' + left + ',top=' + top);
        }
    </script>

    <script src="https://unpkg.com/scrollreveal"></script>
    <script src="home.js"></script>
    <footer>
        &copy; 2023 NOIR CAPITAL BANK. All rights reserved.
    </footer>
</body>

</html>