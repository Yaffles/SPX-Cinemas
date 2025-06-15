<?php
require(__DIR__.'\utilities\sessionCheck.php');
require("model/member.php");
require("model/Basket.php");

$method  = $_SERVER["REQUEST_METHOD"];

$member = unserialize($_SESSION["member"]);

IF ($method=="POST") {
    $type = $_POST["_method"];
    if ($type == "POST") {
        $movieId = $_POST["movieId"];
        $cinemaId = $_POST["cinemaId"];
        $sessionId = $_POST["sessionId"];
        $seats = $_POST["seats"];
        $totalCost = $_POST["totalCost"];
        $date = $_POST["date"];
    
        // create a basket
        $basket = new Basket(memberId: $member->getMemberId(), dbGet: True);
        $retCode = $basket->addItem($sessionId, $seats, $date);
        
    
        SWITCH ($retCode) {
            CASE 0:
                $message = " Successful";
                break;
            CASE 1:
                $message = "Error. Try again";
                break;
        }
    }
    else if ($type == "DELETE") {
        $sessionId = $_POST["sessionId"];
        $basket = new Basket(memberId: $member->getMemberId(), dbGet: True);
        $retCode = $basket->removeItem($sessionId);
        SWITCH ($retCode) {
            CASE 0:
                $message = "Item Removed";
                break;
            CASE 1:
                $message = "Error. Try again";
                break;
        }
    }
    else if ($type == "UPDATE") {
        $sessionId = $_POST["sessionId"];
        $seats = $_POST["seats"];
        $totalCost = $_POST["totalCost"];
        // 
        $basket = new Basket(memberId: $member->getMemberId(), dbGet: True);
        $retCode = $basket->updateItem($sessionId, $seats);
        SWITCH ($retCode) {
            CASE 0:
                $message = "Item Updated";
                break;
            CASE 1:
                $message = "Error. Try again";
                break;
        }
    }
    

}
else IF ($method=="GET") {
    $basket = new Basket(memberId: $member->getMemberId(), dbGet: True);
}
else {
    $message = "Invalid Request";
}
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
        <h1>Basket</h1>
        <div>
            <?php
                IF (isset($message)) {
                    echo "<div class='alert alert-danger'>$message</div>";
                }

            ?>
            <div class="basketContainer">

                <div class="row">
                    <?php
                        $items = $basket->getBasketItems();
                        if ($items) {
                            foreach ($items as $basketItem) {
                                $date = $basketItem->getDate() ?? date("Y-m-d");
                               // convert from 20/05 to 20 May
                                $date = date("d F", strtotime($date));
                                ?>

                                <div class='basketItem'>
                                    <div class='basketLeft'>
                                        <img src='<?= htmlspecialchars($basketItem->getPosterFile()) ?>' alt='<?= htmlspecialchars($basketItem->getMovieName()) ?>'>
                                        <div class='basketinfo'>
                                            <h2><?= htmlspecialchars($basketItem->getMovieName()) ?></h2>
                                            <h3><?= htmlspecialchars($basketItem->getTime()) ?></h3>
                                            <h2><?php //htmlspecialchars($basketItem->getCinemaName()) ?></h2>
                                            <h2><?php //htmlspecialchars($basketItem->getLocationName()) ?></h2>
                                            
                                        </div> <!-- basketinfo -->
                                    </div> <!-- basketLeft -->

                                    <div class='basketRight'>
                                        <form id='updateForm' method='POST' action='basket.php'>
                                            <label for='seats'>Seats:</label>
                                            <input name='seats' type='number' min='1' max='10' value='<?= htmlspecialchars($basketItem->getSeats()) ?>'>
                                            <p>Total Cost: $<?= number_format($basketItem->getTotalCost(), 2) ?></p>
                                            <input type='hidden' name='_method' value='UPDATE'>
                                            <input type='hidden' name='sessionId' value='<?= htmlspecialchars($basketItem->getSessionId()) ?>'>
                                            <input type='hidden' name='totalCost' value='<?= htmlspecialchars($basketItem->getTotalCost()) ?>'>
                                            <button type='submit' class='btn btn-primary'>Update</button>
                                        </form>

                                        <form method='POST' action='basket.php'>
                                            <input type='hidden' name='_method' value='DELETE'>
                                            <input type='hidden' name='sessionId' value='<?= htmlspecialchars($basketItem->getSessionId()) ?>'>
                                            <button type='submit' class='btn btn-danger'>Remove</button>
                                        </form>
                                    </div> <!-- basketRight -->

                                </div> <!-- basketItem -->
                                
                                <?php } ?> <!-- end foreach -->

                            <div class='basketTotal'>
                                    <h2>Basket Cost: $<?= number_format($basket->getTotalCost(), 2) ?></h2>
                                    <form method='POST' action='bookings.php'>
                                        <input type='hidden' name='totalCost' value='<?= htmlspecialchars($basket->getTotalCost()) ?>'>
                                        <input type='hidden' name='_method' value='POST'>
                                        <button type='submit' class='btn btn-success'>Checkout</button>
                                    </form>
                            </div> <!-- basketTotal -->

                        <?php } else { ?>
                            <div class='basketItem'>
                                <h3>No Items in Basket</h3>
                            </div>
                        <?php } ?>
                    </div> <!-- end row -->
                </div> <!-- end basketContainer -->
            </div> <!-- end basket -->

    </maincontent>
    <?php
        require('footer.php');
    ?>
</body>
</html>