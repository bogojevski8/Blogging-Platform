<?php 
require "functions.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Use prepared statements to prevent SQL injection
    $stmt = $connection->prepare("SELECT * FROM users WHERE email = ? AND password = ? LIMIT 1");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    // checks if the user exists
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Log the user in
        $_SESSION['info'] = $row;
        header("Location: profile.php");
        die;
    } else {
        $error = "Invalid email or password.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.scss">
</head>
<body>
    <?php require "header.php"?>
    <div style="margin:auto; max-width: 600px">
        <h2 style="text-align: center;">Login</h2>
        <form class="signup-form" method="post">
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <?php 
            // If the variable is not empty echo 
            if (!empty($error)) {
                echo "<div>".$error."</div>";
            }
            ?>
            <button>Log in</button>
        </form>
    </div>
    <footer style="position: fixed;"> Copyright © <?php echo date("Y")?> </footer></body>
</html>
