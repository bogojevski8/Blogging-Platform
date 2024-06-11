<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<header>
    <div><a href="index.php" class="<?= $current_page == 'index.php' ? 'active-link' : '' ?>">Home</a></div>
    <div><a href="profile.php" class="<?= $current_page == 'profile.php' ? 'active-link' : '' ?>">Profile</a></div>
    <?php if (empty($_SESSION['info'])): ?>
        <div><a href="login.php" class="<?= $current_page == 'login.php' ? 'active-link' : '' ?>">Log in</a></div>
        <div><a href="signup.php" class="<?= $current_page == 'signup.php' ? 'active-link' : '' ?>">Sign up</a></div>
    <?php else: ?>
        <div><a href="logout.php">Log out</a></div>
    <?php endif; ?>
</header>
