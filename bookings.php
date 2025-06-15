<?php
require(__DIR__.'\utilities\sessionCheck.php');
require_once("model/member.php");
require_once("model/Basket.php");
require_once("model/Booking.php");

$method  = $_SERVER["REQUEST_METHOD"];

$member = unserialize($_SESSION["member"]);

IF ($method=="POST") { 
    $message = "Invalid Login, Try Again";


    // create a basket
    $basket = new Basket(memberId: $member->getMemberId(), dbGet: True);
    $retCode = $basket->checkout();


    SWITCH ($retCode) {
        CASE 0:
            $message = " Successful";
            break;
        CASE 1:
            $message = "Error. Try again";
            break;
    }
    $bookings = Booking::loadBookings($member->getMemberId());
}
    
else IF ($method=="GET") {
    $bookings = Booking::loadBookings($member->getMemberId());
}
else {
    $message = "Invalid Request";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require('head.php');?>
    <link rel="stylesheet" href="css/bookings.css">
</head>
<body>
    <?php
        require('header.php');
        require('nav.php');
    ?>

    <maincontent>
        <h1>Bookings</h1>
        <div>
            <?php
                IF (isset($message)) {
                    echo "<div class='alert alert-danger'>$message</div>";
                }

            ?>
            <div class="bookingsContainer">

                <div class="bookingRow">
                    <?php
                        if ($bookings) {
                            foreach ($bookings as $booking) {

                                echo "<div class='booking'>";
                                echo "<h2>Booking ID: " . $booking->getBookingId() . "</h2>";
                                echo "<h2>Booking Date: " . $booking->getBookingDate() . "</h2>";
                                echo "<h2>Cost: $" . $booking->getCost() . "</h2>";
                                $bookingItems = $booking->getBookingItems();
                                if ($bookingItems) {
                                    foreach ($bookingItems as $bookingItem) {
                                        echo "<div class='bookingItem'>";
                                        echo "<h2>" . $bookingItem->getMovieName() . "</h2>";
                                        echo "<h3>Seats: " . $bookingItem->getSeats() . "</h2>";
                                        echo "<h3>Date: " . $bookingItem->getDate() . "</h2>";
                                        echo "<h3>Time: " . $bookingItem->getTime() . "</h2>";
                                        echo "</div>";
                                    }
                                } else {
                                    echo("<h2>No Booking Items</h2>");
                                }
                                

                                echo "</div>";
                            }
                        } else {
                            echo("<h2 style='color: red;'>No Bookings</h2>");
                        }

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