<nav>
    <ul>
        <?php if (isset($_SESSION["member"])): ?>
            <li><a href="index.php">Home</a></li>
            <li><a href="logout.php">Logout</a></li>
            <li><a href="memberRegistration.php"><img src="img/member.png" alt=""> Member</a></li>
            <li><a href="movies.php"><img src="img/movies.png" alt=""> Movies</a></li>
            <li><a href="basket.php"><img src="img/shoppingCart.png" alt=""> Basket</a></li>
            <li><a href="bookings.php"><img src="img/bookings.png" alt=""> Bookings</a></li>
        <?php else: ?>
            <li><a href="login.php">Login</a></li>
            <li><a href="memberRegistration.php"><img src="img/member.png" alt=""> Register</a></li>
        <?php endif; ?>
    </ul>
</nav>
