<nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="play.php">Movies</a></li>
            <li><?php if (isset($_SESSION["member"])) { echo("<a href='logout.php'>Logout");} else {echo("<a href='login.php'>Login");} ?></a></li>
            <li><a href="memberRegistration.php"><img src="img/member.png">Member</a></li>
            <!-- <li><a href="basket.php"><img src="img/shoppingCart.png"> Basket</a></li> -->
        </ul>
    </nav>