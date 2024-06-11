<?php 
require "functions.php";
check_login();

if ($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST['action']) && $_POST['action'] == 'post_delete') {
    // Deleting a post
    $id = $_GET['id'] ?? 0;
    $user_id = $_SESSION['info']['id'];

    $stmt = $connection->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->bind_param('ii', $id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    // searches for a matching post
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (file_exists($row['image'])) {
            unlink($row['image']); //deletes the image
        }
    }
    $stmt->close();

    $stmt = $connection->prepare("DELETE FROM posts WHERE id = ? AND user_id = ? LIMIT 1");
    $stmt->bind_param('ii', $id, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: profile.php");
    die;

     // Editing a post
} elseif ($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST['action']) && $_POST['action'] == "post_edit") {

    $id = $_GET['id'] ?? 0;
    $user_id = $_SESSION['info']['id'];

    $image_added = false;
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] == 0 && $_FILES['image']['type'] == "image/jpeg") {
        // File is uploaded
        $folder = "uploads/";
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

//         Sets the image file path.
// Moves the uploaded file from the temporary location to the specified folder.
        $image = $folder . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], $image);

        $stmt = $connection->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ? LIMIT 1");
        $stmt->bind_param('ii', $id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (file_exists($row['image'])) {
                unlink($row['image']);
            }
        }
        $stmt->close();

        $image_added = true;
    }

    $post = $_POST['post'];
// making sure we only edit our own post 
    if ($image_added) {
        $stmt = $connection->prepare("UPDATE posts SET post = ?, image = ? WHERE id = ? AND user_id = ? LIMIT 1");
        $stmt->bind_param('ssii', $post, $image, $id, $user_id);
    } else {
        $stmt = $connection->prepare("UPDATE posts SET post = ? WHERE id = ? AND user_id = ? LIMIT 1");
        $stmt->bind_param('sii', $post, $id, $user_id);
    }
    $stmt->execute();
    $stmt->close();

    header("Location: profile.php");
    die;

     // Deleting the profile
} elseif ($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST['action']) && $_POST['action'] == 'delete') {
   
    $id = $_SESSION['info']['id'];
    $stmt = $connection->prepare("DELETE FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();

    // deletes profile img
    if (file_exists($_SESSION['info']['image'])) {
        unlink($_SESSION['info']['image']);
    }

    // delete together with the post
    $stmt = $connection->prepare("DELETE FROM posts WHERE user_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
// redirecting to logout
    header("Location: logout.php");
    die;

     // Profile edit
} elseif ($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST['username'])) {
   
    $image_added = false;
    // checks if a file was uploaded and ensures there was no error during upload and checks that the uploaded file is a JPEG image
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] == 0 && $_FILES['image']['type'] == "image/jpeg") {
        // File is uploaded
//         Constructs the path for the new image and
// moves the uploaded file to the designated folder and
// deletes the old image file if it exists.
        $folder = "uploads/";
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        $image = $folder . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], $image);

        if (file_exists($_SESSION['info']['image'])) {
            unlink($_SESSION['info']['image']);
        }

        $image_added = true;
    }
    // Retrieves the updated username, email, and password from the form.
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $id = $_SESSION['info']['id'];

    // if an img was uploaded it includes if not only username, pass and email
    if ($image_added) {
        $stmt = $connection->prepare("UPDATE users SET username = ?, email = ?, password = ?, image = ? WHERE id = ? LIMIT 1");
        $stmt->bind_param('ssssi', $username, $email, $password, $image, $id);
    } else {
        $stmt = $connection->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ? LIMIT 1");
        $stmt->bind_param('sssi', $username, $email, $password, $id);
    }
    $stmt->execute();
    $stmt->close();

    $stmt = $connection->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['info'] = $result->fetch_assoc();
    }
    $stmt->close();

    header("Location: profile.php");
    die;

    // Adding post
} elseif ($_SERVER['REQUEST_METHOD'] == "POST" && !empty($_POST['post'])) {
    
    $image = "";
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] == 0 && $_FILES['image']['type'] == "image/jpeg") {
        // File is uploaded
        $folder = "uploads/";
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        $image = $folder . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    $post = addslashes($_POST['post']);
    $user_id = $_SESSION['info']['id'];
    $date = date('Y-m-d H:i:s');

    $stmt = $connection->prepare("INSERT INTO posts (user_id, post, image, date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param('isss', $user_id, $post, $image, $date);
    $stmt->execute();
    $stmt->close();

    header("Location: profile.php");
    die;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="style.scss">
</head>
<body>
    <?php require "header.php" ?>
    <div style="margin:auto; max-width: 600px">
        <?php if (!empty($_GET['action']) && $_GET['action'] == 'post_delete' && !empty($_GET['id'])): ?>
            <?php 
            $id = (int)$_GET['id'];
            $stmt = $connection->prepare("SELECT * FROM posts WHERE id = ? LIMIT 1");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            ?>

            <?php if ($result->num_rows > 0): ?>
                <?php $row = $result->fetch_assoc(); ?>
                <h3>Are you sure you want to delete this post?</h3>
                <form class="signup-form" enctype="multipart/form-data" method="post">
                    <div><?= htmlspecialchars($row['post']) ?></div><br>
                    <input type="hidden" name="action" value="post_delete">
                    <button style="background-color: red; color:white">Delete</button>
                    <a href="profile.php"><button type="button">Cancel</button></a>
                </form>
            <?php endif; ?>
<!-- Editing post -->
        <?php elseif (!empty($_GET['action']) && $_GET['action'] == 'post_edit' && !empty($_GET['id'])): ?>
            <?php 
            $id = (int)$_GET['id'];
            $stmt = $connection->prepare("SELECT * FROM posts WHERE id = ? LIMIT 1");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            ?>

            <?php if ($result->num_rows > 0): ?>
                <?php $row = $result->fetch_assoc(); ?>
                <h5>Edit a post</h5>
                <form method="post" enctype="multipart/form-data" style="margin: auto;padding:10px;">
                    <img src="<?= $row['image'] ?>" style="width:100%;height:200px;object-fit: cover;"><br>
                    Image: <input type="file" name="image"><br>
                    <textarea name="post" rows="8" style="width:600px;"><?= htmlspecialchars($row['post']) ?></textarea><br>
                    <input type="hidden" name="action" value="post_edit">
                    <button>Save</button>
                    <a href="profile.php"><button type="button">Cancel</button></a>
                </form>
            <?php endif; ?>
            <!-- Edit profile -->

        <?php elseif (!empty($_GET['action']) && $_GET['action'] == 'edit'): ?>
            
            <h2 style="text-align: center;">Edit profile</h2>
            <form method="post" enctype="multipart/form-data" class="signup-form">
                <img class="profile-img" src="<?= $_SESSION['info']['image'] ?>" alt="" style="object-fit:cover; margin:auto; display:block;">
                <input type="file" name="image" /><br>
                Username:
                <input value="<?= htmlspecialchars($_SESSION['info']['username']) ?>" type="text" name="username" placeholder="Username" required><br>
                Email:
                <input value="<?= htmlspecialchars($_SESSION['info']['email']) ?>" type="email" name="email" placeholder="Email" required><br>
                Password:
                <input value="<?= htmlspecialchars($_SESSION['info']['password']) ?>" type="text" name="password" placeholder="Password" required><br>
                <button>Save</button>
                <a href="profile.php"><button type="button">Cancel</button></a>
            </form>

<!-- Deleting the profile -->
        <?php elseif (!empty($_GET['action']) && $_GET['action'] == 'delete'): ?>
            <h2 style="text-align: center;">Are you sure you want to delete your profile?</h2>
            <div style="margin:auto; max-width:px;text-align:center;">
                <form method="post" class="signup-form">
                    <img class="profile-img" src="<?= $_SESSION['info']['image'] ?>" alt="" style="width: 100px; height:100px; object-fit:cover; margin:auto; display:block;">
                    <div><?= htmlspecialchars($_SESSION['info']['username']) ?></div>
                    <div><?= htmlspecialchars($_SESSION['info']['email']) ?></div>
                    <input type="hidden" name="action" value="delete">
                    <button style="background-color: red; color:white">Delete</button>
                    <a href="profile.php"><button type="button">Cancel</button></a>
                </form>
            </div>

        <?php else: ?>
            <!-- What we see on the profile page  -->
            <h2 style="text-align: center;">User Profile</h2>
            <div style="margin:auto; max-width:px;text-align:center;">
                <div>
                    <img class="profile-img" src="<?= $_SESSION['info']['image'] ?>" alt="">
                </div>
                <div><?= htmlspecialchars($_SESSION['info']['username']) ?></div>
                <div><?= htmlspecialchars($_SESSION['info']['email']) ?></div>
                <a href="profile.php?action=edit"><button>Edit profile</button></a>
                <a href="profile.php?action=delete"><button>Delete profile</button></a>
            </div>
            <hr style="margin-bottom: 10px;">
            <h5 class="create-post">Create post</h5>
            <form class="signup-form" enctype="multipart/form-data" method="post">
                <input type="file" name="image" /><br>
                <textarea name="post" rows="8"></textarea>
                <button>Post</button>
            </form>
            <hr>
            <div>
                <?php
                $id = $_SESSION['info']['id'];
                $stmt = $connection->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY id DESC");
                $stmt->bind_param('i', $id);
                $stmt->execute();
                $result = $stmt->get_result();
                ?>

                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php 
                        $user_id = $row['user_id'];
                        $stmt2 = $connection->prepare("SELECT username, image FROM users WHERE id = ? LIMIT 1");
                        $stmt2->bind_param('i', $user_id);
                        $stmt2->execute();
                        $result2 = $stmt2->get_result();
                        $user_row = $result2->fetch_assoc();
                        ?>

                        <div class="posts">
                            <div style="flex:1;">
                                <img src="<?= $user_row['image'] ?>" style="border-radius:50%; margin: 10px; width:50px; height:50px; object-fit: cover;">
                            </div>
                            <div style="flex:8;">
                                <div style="margin-bottom: 5px;">
                                    <span style="font-weight: bold;"><?= htmlspecialchars($user_row['username']) ?></span> •
                                    <span style="margin-left:6px; color: #888;"><?= date("jS M", strtotime($row['date'])) ?></span>
                                </div>
                                <?php if (file_exists($row['image'])): ?>
                                    <div>
                                        <img src="<?= $row['image'] ?>" style="width:100%; height:200px; object-fit: cover;">
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <!-- this html special characters helps against hacking so no javascript code can be executed  -->
                                    <?= nl2br(htmlspecialchars($row['post'])) ?>
                                    <br><br>
                                    <a href="profile.php?action=post_edit&id=<?= $row['id'] ?>"><button>Edit</button></a>
                                    <a href="profile.php?action=post_delete&id=<?= $row['id'] ?>"><button>Delete</button></a>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php require "footer.php" ?>
</body>
</html>
