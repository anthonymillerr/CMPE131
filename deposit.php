<?php
session_start();
$localhost = 'localhost';
$username = 'root';
$password  = '';
$database_name  = 'bankregistration';
$user_num = $_SESSION['user_id'];
$same_user = false;
$match_error = false;


$conn = mysqli_connect($localhost, $username, $password, $database_name);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deposit'])) {
    $account_id = $_POST['account_id'];
    $account_type = $_POST['account_type'];
    $amount = $_POST['amount'];

    $checkingResult = mysqli_query($conn, "SELECT * FROM checking_accounts WHERE account_id = '$account_id' AND user_id = '$user_num'");
    $savingsResult = mysqli_query($conn, "SELECT * FROM savings_accounts WHERE account_id = '$account_id' AND user_id = '$user_num'");
    
    $checkingRow = mysqli_fetch_assoc($checkingResult);
    $savingsRow = mysqli_fetch_assoc($savingsResult);
    if(!$checkingRow && ($account_type == 'checking')){
      $match_error = true;
    }else if(!$savingsRow && ($account_type == 'savings')){
      $match_error = true;
    }


    if ($checkingRow || $savingsRow) {
        if (isset($_FILES['image1']['name']) && isset($_FILES['image2']['name'])) {
            $image1 = $_FILES['image1']['name'];
            $image2 = $_FILES['image2']['name'];
            $same_user = true;
            $image1FileType = pathinfo($image1, PATHINFO_EXTENSION);
            $image2FileType = pathinfo($image2, PATHINFO_EXTENSION);

            if ($image1FileType == 'jpeg' && $image2FileType == 'jpeg') {
                echo '';
            } else {
                echo '';
            }
        } else {
            echo '';
        }
    } else {
        echo '';
    }
}

// Function to deposit funds
function deposit($account_id, $amount, $account_type) {
    global $conn;
    
    
    // Update the balance based on the account type
    $table = ($account_type == 'checking') ? 'checking_accounts' : 'savings_accounts';
    $query = "UPDATE $table SET balance = balance + $amount WHERE account_id = '$account_id'";
    
    mysqli_query($conn, $query);
}
// Function to insert a transaction record
function insertTransaction($user_id, $transaction_id, $transaction_type, $amount, $status, $details, $accountType, $account_number) {
    global $conn;

    // Store image data in the database (you may need to adjust the column types and sizes)
    $image1Data = file_get_contents($_FILES['image1']['tmp_name']);
    $image2Data = file_get_contents($_FILES['image2']['tmp_name']);

    // Escape the binary data to prevent SQL injection
    $escapedImage1 = mysqli_real_escape_string($conn, $image1Data);
    $escapedImage2 = mysqli_real_escape_string($conn, $image2Data);

    // Insert transaction with image data
    $sql = "INSERT INTO transactions (user_id, transaction_id, transaction_type, amount, status, details, account_type, account_number) VALUES ('$user_id', '$transaction_id', '$transaction_type', '$amount', '$status', '$details', '$accountType', '$account_number')";
    $result = mysqli_query($conn, $sql);

    return $result;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOIR - Check Deposit </title>
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400&display=swap" rel="stylesheet">
    <style>

.container {
  margin: 100px auto;
  max-width: 750px;
  background-color: #808080;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  color: white;
  text-align: center;
  text-transform: uppercase;
}

h2 {
  color: white;
}

label {
  display: block;
  margin-top: 10px;
  color: white;
}

input {
  width: 100%;
  padding: 10px;
  margin: 5px 0 20px;
  box-sizing: border-box;
  border: 1px solid #ccc;
  border-radius: 4px;
}

button {
  justify-content: center;
  min-width:200px;
  color: white;
  transition: all .55s ease;
  text-transform: uppercase;
  border:1px white;
  background-color: #555;
  color: #fff;
  padding: 0.5em;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

button:hover {
  background: #3cb043;
  border: 1px solid white;
  transform: translateY(-2px);
}

.message {
  margin-top: 20px;
  padding: 10px;
  border-radius: 4px;
}

.success {
  background-color: #4caf50;
  color: #fff;
}

.error {
  background-color: #f44336;
  color: #fff;
}
*{
padding:0;
margin: 0;
box-sizing:border-box;
font-family: 'Poppins', sans-serif;
list-style:none;
text-decoration: none;
}
header{
position:fixed;
right: 0;
top: 0;
z-index:1000;
width :100%;
display: flex;
align-items: center;
justify-content: space-between;
padding: 33px 9%;
background: #808080;
}

.logo{
font-size: 30px;
font-weight: 700;
color: white; 
}
.navlist{
display:flex;
}
.navlist a{
color: white;
margin-left: 60px;
font-size:15px;
font-weight: 600;
border-bottom: 2px solid transparent;
transition: all .55s ease;
}
.navlist a:hover{
border-bottom: 2px solid white;
}
#menu.icon{
color:white;
font-size: 30px;
z-index: 10001;
cursor: pointer;
display:none;
}
.bank{
height: 100%;
width: 100%;
min-height:100vh;
background: linear-gradient(245.59deg, #555 0%, #333 28.53%, #222 75.52%);
position:relative;
display:grid;
grid-template-columns: repeat(1,1fr);
align-items:center;
gap: 2rem;
}
section{
padding: 0 19%;

}
.bank-text h5{
font-size: 14px;
font-weight: 400;
color:white;
margin-bottom: 10px;
margin-top: 80px;
}
.bank-text h1{
font-size: 70px;
line-height:1;
color:white;
margin: 0 0 45px;
margin-top: 100px;
}
.bank-text h4{
font-size: 18px;
font-weight: 600;
color: white;
margin-bottom: 10px;
}
.bank-text p{
color: white;
font-size:15px;
line-height: 1.9;
margin-bottom: 40px;
}
.bank-img img{
margin-top: 50px;
width: 600px;
height: auto;
}
.bank-login form{
margin-top: 5px;
width: 600px;
height: auto;
}
.bank-text a{
display: incline-block;
color: white;
background: #333;
border: 1px solid transparent;
padding: 12px 30px;
line-height: 1.4;
font-size: 14px;
font-weight: 500;
border-radius: 30px;
text-transform:uppercase;
transition: all .55s ease;
}
.bank-text a:hover{
background: transparent;
border: 1px solid white;
transform: translateX(8px);
}
.bank-text a.ctaa{
background: transparent;
border: 1 px solid white;
margin-left: 20px; 
}
.bank-text a.ctaa i{
vertical-align: middle;
margin-right: 5px;
}
.icons i{
display: block;
margin: 26px 0;
font-size: 24px;
color: white;
transition: all .50s ease;
}
.icons i:hover{
color: #555;
transform: translateY(-5px);
}
.scroll-down{
position: absolute;
bottom: 6%;
right: 9%;
}
.scroll-down i{
display: block;
padding: 12px;
font-size: 25px;
color: white;
background: #555;
border-radius: 30px;
transition: all .50s ease
}
.scroll-down i:hover{
transform: translate(-5px);
}
.bank-login{
text-align: center;
}
.bank-login form {
display: flex;
max-width: 100px;
margin-top: -35px;
color: white;
border-radius: 8px;
justify-content: center;
} 
.bank-login form label {
margin-bottom: 0.5em;
}

.bank-login form input {
padding: 0.5em;
margin-bottom: 1em;
border: 1px solid #ccc;
border-radius: 4px;
}

.bank-login form button {
background-color: #555;
color: #fff;
padding: 0.5em;
border: none;
border-radius: 4px;
cursor: pointer;
margin-top: 20px;
margin-bottom:-40px;
transition: all .55s ease;
margin-left: 10px;
}

.bank-login form button:hover {
background: #990f02;
border: 1px solid white;
transform: translateY(-2px);
}
@media(max-width: 1535px){
header{
padding: 15px 3%;
transition: .2s;
}
.icons{
padding: 0 3%;
transition: .2s;
}
.scroll-down{
right: 3%;
transition: .2s;
}
}
@media (max-width: 1460px){
section{
padding: 0 12%;
transition: .2s;
}
}
@media (max-width: 1340px){
.bank-img img{
width:100%;
height: auto;
}
.bank-login form{
width:100%;
height: auto;
}
.bank-text h1{
font-size: 75px;
margin: 0 0 30px;
}
.bank-text h5{
margin-bottom: 25px;
}
}
@media(max-width:1195px){
section{
padding: 0 3%;
transition: .2s;
}
.bank-text{
padding-top: 0px;
}
.bank-img{
text-align: center;
}
.bank-img img{
width: 560px;
height: auto;
}
.bank-login{
text-align: center;
}
.bank-login form{
width: 560px;
height: auto;
}
.bank{
height: 100%;
gap: 1rem;
grid-template-columns: 1fr;
}
.bank-text-dashboard{
margin-left: -45px;
margin-top: -90px;
}
.icons{
display: none;
}
.scroll-down{
display: none;
}
}
@media (max-width:990px){
#menu-icon{
display: block;
}
.navlist{
position: absolute;
top: 100%;
right: -100%;
width: 200px;
height: 30vh;
background: #707070;
display: flex;
align-items:center;
flex-direction: column;
padding: 30px 20px;
border-top-left-radius: 10px;
border-bottom-left-radius: 10px;
transition: all .55s ease;
}
.navlist a{
display: block;
margin: 7px 0;
margin-left: 0;
margin-top: -5px;
}
.navlist.open{
right:0;
}

}
@media (max-width:680px){
.bank-img img{
margin-top: 5px;
width: 100%;
height: auto;
}
.bank-login form{
margin-top: 5px;
width: 100%;
height: auto;
}

}
.bank-account {
color: white;
background-color: #808080;
padding: 20px;
border-radius: 8px;
box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
margin-bottom: 20px;
max-width: 600px;
}
.bank-account h4, .bank-account p {
margin-bottom: 0px;
margin-top: -10px;
}

.bank-text-dashboard a{
color: white;
background: #808080;
border: 1px solid transparent;
padding: 12px 30px;
line-height: 1.4;
font-size: 14px;
font-weight: 500;
border-radius: 30px;
text-transform:uppercase;
transition: all .25s ease;
margin-left: 180px;
max-width: 300px;
text-align:center;
justify-content: center;
}
.bank-text-dashboard a:hover{
background: transparent;
border: 1px solid white;
transform: translateX(8px);
}
footer {
background-color: #808080;
color: #fff;
padding: 1em;
text-align: center;
bottom: 0;
width: 100%;
}
table {
border-collapse: collapse;
width: 100%;
color: white;
}

th, td {
border: 1px solid #ddd;
padding: 10px;
color: white;
background-color: #808080;
max-height: 20px;
text-align: center;
justify-content: center;
}
h2 {
color: white;
} 
form{
margin-top:20px;
color: white;
text-align:center;
font-size: 13px;
}


.bank-text-dashboard{
margin-top: 280px;
}
.bank-text-dashboard form button:hover{
background: #3cb043;
border: 1px solid white;
transform: translateY(-2px);
}

    @media screen and (max-width: 1300px) {
      .bank-login {
        margin-top: -60px; 
      }
    }
    @media screen and (max-width: 1100px) {
      .bank-login {
        margin-top: -60px; 
      }
    }
    @media screen and (max-width: 990px) {
        .navlist {
        height: 180px;
      }
    }
    @media screen and (max-width: 750px) {
      .bank-login {
        margin-top: -60px;
      }
      .navlist {
        height: 180px;
      }
    }

  </style>

</head>
<body>
<header>
 <a href "#" class="logo">NOIR CAPITAL BANK</a>
 <ul class="navlist">
      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="atm.php">ATM</a></li>
      <li><a href="contact1.php">Contact</a></li>
      <li><a href="about.html">About</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
    <div class="bx bx-menu" id="menu-icon"></div>
</header>
<section class="bank">
    <div class="bank-text" style="margin-top: 50px;">
    <div class="container">
        <h2>Deposit Funds</h2>
    <form action="deposit.php" method="post" enctype="multipart/form-data">
        <label for="account_id">Account ID:</label>
        <input type="text" name="account_id" id="account_id" required placeholder="Account Number(Inside Manage Accounts)" pattern="[0-9]{1,12}" title="Enter Valid Account ID">

        <label for="amount">Amount In $:</label>
        <input type="text" name="amount" required placeholder="Amount" pattern="[0-9]+(\.[0-9]{1,2})?" title="Enter Valid Amount">

        <label for="account_type">Account Type:</label>
        <select name="account_type" required>
            <option value="checking">Checking</option>
            <option value="savings">Savings</option>
        </select>
        <br>
        <br>

        <label for="image1">Front Of Check:</label>
        <input type="file" name="image1" accept="image/jpeg" required>

        <label for="image2">Back Of Check:</label>
        <input type="file" name="image2" accept="image/jpeg" required>

        <button type="submit" name="deposit">Deposit</button>
    </form>


    <?php
    // Display success or error message
    if (isset($_POST['deposit']) && $same_user && !$match_error) {
        $checkingResult = mysqli_query($conn, "SELECT * FROM checking_accounts WHERE account_id = '$account_id'");
        $savingsResult = mysqli_query($conn, "SELECT * FROM savings_accounts WHERE account_id = '$account_id'");
        
        $checkingRow = mysqli_fetch_assoc($checkingResult);
        $savingsRow = mysqli_fetch_assoc($savingsResult);
    
        if (($checkingRow || $savingsRow)) {
            // Check if two images (jpg) are submitted
            if (isset($_FILES['image1']['name']) && isset($_FILES['image2']['name'])) {
                $image1 = $_FILES['image1']['name'];
                $image2 = $_FILES['image2']['name'];
    
                $image1FileType = pathinfo($image1, PATHINFO_EXTENSION);
                $image2FileType = pathinfo($image2, PATHINFO_EXTENSION);
    
                if ($image1FileType == 'jpeg' && $image2FileType == 'jpeg') {
                    
                    $user_id = ($account_type == 'checking') ? $checkingRow['user_id'] : $savingsRow['user_id'];

                    $transaction_type = 'Deposit';
                    $status = 'Pending'; // Change to 'Pending'
                    $account_number = filter_input(INPUT_POST, 'account_id');

                
                    $amount = filter_input(INPUT_POST, 'amount', FILTER_VALIDATE_FLOAT);

                    // Check if the amount is 0 or negative
                    if ($amount <= 0) {
                        echo '<div class="message error">Invalid Deposit Amount</div>';
                        exit();
                    } else {
                        $transaction_id = rand(10000000, 999999999);
                        $details = "Check Deposit For $$amount";
                        // Call the insertTransaction function with 'Pending' status
                        insertTransaction($user_id, $transaction_id, $transaction_type, $amount, $status, $details, $account_type, $account_number);
                        echo '<div class="message success">Deposit Is Now Pending Approval.</div>';
                    }
                } else {
                    echo '<div class="message error">Please upload valid JPG images.</div>';
                }
            } else {
                echo '<div class="message error">Please upload both front and back images of the check.</div>';
            }
        } else {
            echo '<div class="message error">Account not found for debitcard</div>';
        }
    }else if(isset($_POST['deposit']) && !$same_user){
      echo '<div class="message error">Account not found for debit card: ' . $user_num. '</div>';
    }
?>
    </div>
    </div>
</section>
<script src="home.js"></script>
<footer>
&copy; 2023 NOIR CAPITAL BANK. All rights reserved.
</footer>
</body>
</html>