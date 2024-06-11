<?php 
require "functions.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = addslashes($_POST['username']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = addslashes($_POST['password']);
    $date = date('Y-m-d H:i:s');

    // Use prepared statements to prevent SQL injection
    $stmt = $connection->prepare("INSERT INTO users (username, email, password, date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $date);
    $stmt->execute();
    $stmt->close();

    header("Location: login.php");
    die;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up</title>
    <link rel="stylesheet" href="style.scss">
</head>
<body>
    <?php require "header.php"?>
    <div style="margin:auto; max-width: 600px">
        <h2 style="text-align: center;">Sign up</h2>
        <form class="signup-form" method="post">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button>Sign up</button>
        </form>
    </div>
    <footer style="position: fixed;"> Copyright © <?php echo date("Y")?> </footer>
</body>
</html>
