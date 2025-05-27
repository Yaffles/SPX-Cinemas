<?php
/**
 * First Initialise Sessions by destroying previous content and restart
 */
session_start();

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
    
    
    
        $message = "Invalid Login, Try Again";
    
    
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
        $basket = new Basket(memberId: $member->getMemberId(), dbGet: True);
        $retCode = $basket->updateItem($sessionId, $seats, $totalCost);
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
        <div class="border border-dark">
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
                            foreach ($basket->getBasketItems() as $basketItem) {
                                $date = $basketItem->getDate() ?? date("Y-m-d");
                               // convert from 20/05 to 20 May
                                $date = date("d F", strtotime($date));
                                


                            

                                echo "<div class='basketItem'>";
                                
                                
                                echo "<div class='basketLeft'>";
                                    echo "<img src='" . $basketItem->getPosterFile() . "' alt='" . $basketItem->getMovieName() . "'>";
                                    echo "<div class='basketinfo'>";
                                        echo "<h2>" . $basketItem->getMovieName() . "</h3>";
                                        echo "<h3>" . $date . " · " . $basketItem->getTime() . "</h3>";
                                    echo "</div>"; // basketinfo
                                echo "</div>"; // basketLeft
                               

                                echo "<div class='basketRight'>";


                                    echo "<form id='updateForm' method='POST' action='basket.php'>";
                                    echo "<label for='seats'>Seats:</label>";
                                    echo "<input name='seats' type='number' value='" . $basketItem->getSeats() . "'>";
                                    echo "<p>Total Cost: $" . number_format($basketItem->getTotalCost(), 2) . "</p>";
                                    echo "<input type='hidden' name='_method' value='UPDATE'>";
                                    echo "<input type='hidden' name='sessionId' value='" . $basketItem->getSessionId() . "'>";
                                    echo "<input type='hidden' name='totalCost' value='" . $basketItem->getTotalCost() . "'>";
                                    echo "<button type='submit' class='btn btn-primary'>Update</button>";
                                    echo "</form>";

                                    echo "<form method='POST' action='basket.php'>";
                                    echo "<input type='hidden' name='_method' value='DELETE'>";
                                    echo "<input type='hidden' name='sessionId' value='" . $basketItem->getSessionId() . "'>";
                                    echo "<button type='submit' class='btn btn-danger'>Remove</button>";
                                    echo "</form>";
                                echo "</div>"; // basketRight

                                echo "</div>";
                            }
                        } else {
                            echo("<h2>No Items in Basket</h2>");

                            return 0;
                        }
                        // total and confirm order
                        echo "<div class='basketTotal'>";
                        echo "<h2>Total Cost: $" . number_format($basket->getTotalCost(), 2) . "</h2>";
                        echo "<form method='POST' action='bookings.php'>";
                            echo "<input type='hidden' name='totalCost' value='" . $basket->getTotalCost() . "'>";
                            echo "<input type='hidden' name='_method' value='POST'>";
                            echo "<button type='submit' class='btn btn-success'>Checkout</button>";
                        echo "</form>";     
                        echo "</div>"; // basketTotal                  
                        
                    ?>
                </div>
            </div>
        </div>

    </maincontent>
    <?php
        require('footer.php');
    ?>
</body>
</html>