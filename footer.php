<footer>
        <div>
        <?php
            IF (ISSET($_SESSION['footer'])) {echo($_SESSION['footer']);}
            ELSE {echo("Current Member: Not Logged In - (c) SPX Cinemas 2025");}
        ?>
        </div>
    </footer>