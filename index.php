<?php
require(__DIR__.'\utilities\sessionCheck.php');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require('head.php');?>
</head>
<body>
    <?php
        require('header.php');
        require('nav.php');
    ?>
    <maincontent>
        <h1>Welcome to SPX Cinemas</h1>
        <div >
            <img class="mainImg" src="img/coverImage.webp"></img>
        </div>
    </maincontent>
    <?php
        require('footer.php');
    ?>
</body>
</html>