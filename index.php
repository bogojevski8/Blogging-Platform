<?php 

require "functions.php";
check_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My website</title>
    <link rel="stylesheet" href="style.scss">
</head>
<body>
    <?php require_once "header.php"?>
    <div style="max-width: 600px; margin:auto;">
    <h2 style="text-align: center;">Timeline</h2>
        <?php
        

        $query= "select * from posts posts order by id desc";
        $result= mysqli_query($connection,$query);
       
        ?>

        <?php if(mysqli_num_rows($result) > 0):?>

            <?php while($row = mysqli_fetch_assoc($result)):?>
<?php 
// fetches the corresponding user's username and image from the users table using the user_id from the post.

     $user_id= $row['user_id'];

    $query = "select username,image from users where id = '$user_id' limit 1";
    $result2= mysqli_query($connection,$query);

    $user_row = mysqli_fetch_assoc($result2);
    ?>

<div class="posts"  >
    <div style="flex:1; ">
    <img src="<?=$user_row['image']?>"  style="border-radius:50%; margin: 10px; width:50px; height:50px; object-fit: cover;">

    </div>
    <div style="flex:8 ;">
    
    <div style="margin-bottom: 5px;"><span style="font-weight: bold;"><?=$user_row['username']?></span> •<span  style="margin-left:6px; color: #888;"><?=date("jS M", strtotime($row['date']))?></span></div>
<?php if(file_exists($row['image'])):?>
<div >

<img src="<?=$row['image']?>" style="width:100%; height:200px; object-fit: cover;">
</div>
<?php endif;?>
<div>
<?php echo nl2br(htmlspecialchars($row['post']))?>

</div>
</div>

</div>
            <?php endwhile;?>
            <?php endif;?>
    </div>
<?php require "footer.php"?>
</body>
</html>