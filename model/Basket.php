<?php

require_once("Database.php");
require_once("Session.php");
require_once("member.php");
require_once("BasketItems.php");

require_once("BookingItems.php");
require_once("Booking.php");

CLASS Basket EXTENDS Database {

    private $memberId = null;
    private ?array $basketItems = [];
    private float $totalCost = 0.0;

    /**
     * Constructor.
     *
     * @param all the fields in Movie - defaulting to null if not provided
     *
     */
    public function __construct(
        ?int $memberId = null,
        bool $dbGet = True
    ) {
        parent::__construct(); // gets a database connection

        if ($memberId) {
            $this->setMemberId($memberId);
        }

        if ($dbGet and $memberId) {
            // fill in the rest of the variables from the database with one function for all
            $this->getBasket();
        }
    }

    // GETS and SETS Methods

    // GET Methods
    public function getMemberId() {
        return $this->memberId;
    }
    public function getTotalCost() {
        return $this->totalCost;
    }




    public function getBasketItems(): ?array {
        return $this->basketItems;
    }

    // SET Methods
    public function setMemberId($memberId) {
        $this->memberId = $memberId;
    }

    public function setTotalCost($totalCost) {
        $this->totalCost = $totalCost;
    }

    public function setMovieId($movieId) {
        $this->movieId = $movieId;
    }

    public function setMovieName($movieName) {
        $this->movieName = $movieName;
    }

    public function setPosterFile($posterFile) {
        $this->posterFile = $posterFile;
    }

    public function setMovieDescription($movieDescription) {
        $this->movieDescription = $movieDescription;
    }

    public function setTrailerName($trailerName) {
        $this->trailerName = $trailerName;
    }



    // Object Relational Mapping Methods
    /**
     * Check if this Session record exists in database
     */
    public function exists() {
        $exists = False;

        IF ($this->getMovieId()) {
            $sql = "SELECT COUNT(*) AS numRows FROM ".self::$tableName." WHERE movieId = ?";

            $results = $this->query($sql,[$this->getMovieId()]);

            FOREACH($results AS $result) {
                $numRows    = $result['numRows']; //num_rows;
            }
            $exists = $numRows==1;
        }
        RETURN $exists;
    }




    /**
     * Get Movie from database based on movieId
     */
    public function getBasket() {
        IF ($this->getMemberId()) {
            $sql = "SELECT basketItemId, sessionId, memberId, seats, cost, date FROM basketItems WHERE memberId = ?";
            $results = $this->query($sql,[$this->getMemberId()]);
            FOREACH ($results AS $result) {
                $basketItem = new BasketItem(
                    basketItemId: $result['basketItemId'],
                    sessionId: $result['sessionId'],
                    memberId: $result['memberId'],
                    seats: $result['seats'],
                    totalCost: $result['cost'],
                    date: $result['date'],
                    dbGet: False
                );

                $this->basketItems[] = $basketItem;
                $this->setTotalCost($this->getTotalCost() + $basketItem->getTotalCost());
            }
            
        }
    }



    // Business Functions

    public function addItem(?int $sessionId, ?int $seats, ?string $date) {
        $session = new Session(sessionId: $sessionId, dbGet: True);

        // check if session already exists in basket
        if ($this->basketItems == null) {
            $this->basketItems = [];
        }
        foreach ($this->basketItems as $basketItem) {
            if ($basketItem->getSessionId() == $sessionId) {
                $basketItem->setSeats($basketItem->getSeats() + $seats);
                $basketItem->save();
                // update total cost, making sure too add too much
                $difference = $seats - $basketItem->getSeats();
                $this->setTotalCost($this->getTotalCost() + $difference * $session->getSeatCost());
                return 0;
            }
        }

        $basketItem = new BasketItem(
            sessionId: $session->getSessionId(),
            memberId: $this->getMemberId(),
            seats: $seats,
            date: $date,
            dbGet: False
        );

        $basketItem->save();

        $this->basketItems[] = $basketItem;
        $this->setTotalCost($this->getTotalCost() + $basketItem->getTotalCost());
        return 0;
    }

    public function removeItem(?int $sessionId) {
        if ($this->basketItems == null) {
            return 1; // no items in basket
        }
        // find the basket item
        foreach ($this->basketItems as $index => $basketItem) {
            if ($basketItem->getSessionId() == $sessionId) {
                // remove it from the array
                unset($this->basketItems[$index]);
                $this->setTotalCost($this->getTotalCost() - $basketItem->getTotalCost());
                // delete it from the database
                $basketItem->delete();
                return 0;
            }
        }
    }

    public function updateItem(?int $sessionId, ?int $seats, ?float $totalCost) {
        if ($this->basketItems == null) {
            return 1; // no items in basket
        }
        // find the basket item
        foreach ($this->basketItems as $index => $basketItem) {
            if ($basketItem->getSessionId() == $sessionId) {
                // update it in the array
                $basketItem->setSeats($seats);
                $basketItem->calculateTotalCost();
                $this->setTotalCost($this->getTotalCost() + $basketItem->getTotalCost());
                // update it in the database
                $basketItem->save();
                return 0;
            }
        }
    }

    public function checkout() {
        if ($this->basketItems == null) {
            return 1; // no items in basket
        }

        $booking = new Booking(
            memberId: $this->getMemberId(),
            cost: $this->getTotalCost(),
            dbGet: False
        );
        $booking->save();

        // find the basket item
        foreach ($this->basketItems as $index => $basketItem) {
            // update it in the array
            $bookingItem = new BookingItem(
                sessionId: $basketItem->getSessionId(),
                bookingId: $booking->getBookingId(),
                seats: $basketItem->getSeats(),
                cost: $basketItem->getTotalCost(),
                date: $basketItem->getDate(),
                time: $basketItem->getTime(),
                dbGet: False
            );
            $bookingItem->save();
        }
        // delete all items in the basket
        foreach ($this->basketItems as $index => $basketItem) {
            // delete it from the database
            $basketItem->delete();
        }
        $this->basketItems = [];
        $this->setTotalCost(0);
        return 0;
    }

}
?>