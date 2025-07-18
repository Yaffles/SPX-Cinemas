<?php

require_once("Database.php");
require_once("Session.php");
require_once("member.php");
require_once("BasketItems.php");
require_once("auditLog.php");

require_once("BookingItems.php");
require_once("Booking.php");



CLASS Basket EXTENDS Database {
    private ?Member $member = null;

    private ?array $basketItems = [];
    private float $totalCost = 0.0;

    private $auditLog;

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
        $this->auditLog = new AuditLog();

        if ($memberId) {
            $this->member = new Member(memberId: $memberId, dbGet: True);
        }

        if ($dbGet and $this->getMember()) {
            // fill in the rest of the variables from the database with one function for all
            $this->getBasket();
        }
    }

    // GETS and SETS Methods

    // GET Methods
    public function getMember() {
        return $this->member;
    }
    public function getTotalCost() {
        return $this->totalCost;
    }




    public function getBasketItems(): ?array {
        return $this->basketItems;
    }

    // SET Methods
    public function setMember($member) {
        $this->member = $member;
    }

    public function setTotalCost($totalCost) {
        $this->totalCost = $totalCost;
    }



    /**
     * Get Movie from database based on movieId
     */
    public function getBasket() {
        IF ($this->getMember()) {
            $sql = "SELECT basketItemId, sessionId, memberId, seats, cost, date FROM basketItems WHERE memberId = ?";
            $results = $this->query($sql,[$this->getMember()->getMemberId()]);
            FOREACH ($results AS $result) {
                $basketItem = new BasketItem(
                    basketItemId: $result['basketItemId'],
                    sessionId: $result['sessionId'],
                    member: $this->getMember(),
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
            if ($basketItem->getSession()->getSessionId() == $sessionId && $basketItem->getDate() == $date) {
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
            member: $this->getMember(),
            seats: $seats,
            date: $date,
            dbGet: False
        );

        $basketItem->save();

        $this->basketItems[] = $basketItem;
        $this->setTotalCost($this->getTotalCost() + $basketItem->getTotalCost());


        $this->auditLog->addLog(
            entity: "Basket",
            action: "Add Item",
            entry: "Added sessionId {$sessionId} at {$session->getCinema()->getCinemaName()} with {$seats} seats to basket for memberId {$this->getMember()->getMemberId()}"
        );
        return 0;
    }

    public function removeItem(?int $sessionId) {
        if ($this->basketItems == null) {
            return 1; // no items in basket
        }
        // find the basket item
        foreach ($this->basketItems as $index => $basketItem) {
            if ($basketItem->getSession()->getSessionId() == $sessionId) {
                // remove it from the array
                unset($this->basketItems[$index]);
                $this->setTotalCost($this->getTotalCost() - $basketItem->getTotalCost());
                // delete it from the database
                $basketItem->delete();

                $this->auditLog->addLog(
                    entity: "Basket",
                    action: "Remove Item",
                    entry: "Removed sessionId {$sessionId} from basket for memberId {$this->getMember()->getMemberId()}"
                );
                return 0;
            }
        }
    }

    public function updateItem(?int $sessionId, ?int $seats) {
        if ($this->basketItems == null) {
            return 1; // no items in basket
        }
        // find the basket item
        foreach ($this->basketItems as $index => $basketItem) {
            if ($basketItem->getSession()->getSessionId() == $sessionId) {
                // update it in the array
                $originalCost = $basketItem->getTotalCost();

                $basketItem->setSeats($seats);
                $basketItem->calculateTotalCost();
            
                $this->setTotalCost($this->getTotalCost() + $basketItem->getTotalCost() - $originalCost);
                // update it in the database
                $basketItem->save();

                $this->auditLog->addLog(
                    entity: "Basket",
                    action: "Update Item",
                    entry: "Updated sessionId {$sessionId} with {$seats} seats and total cost " .  $basketItem->getTotalCost() . " for memberId {$this->getMember()->getMemberId()}"
                );
                return 0;
            }
        }
    }

    public function checkout() {
        if ($this->basketItems == null) {
            return 1; // no items in basket
        }

        $booking = new Booking(
            member: $this->getMember(),
            cost: $this->getTotalCost(),
            dbGet: False
        );
        $booking->save();

        // find the basket item
        foreach ($this->basketItems as $index => $basketItem) {
            // update it in the array
            $bookingItem = new BookingItem(
                sessionId: $basketItem->getSession()->getSessionId(),
                booking: $booking,
                seats: $basketItem->getSeats(),
                cost: $basketItem->getTotalCost(),
                date: $basketItem->getDate(),
                dbGet: False
            );
            $bookingItem->save();
        }
        // delete all items in the basket
        foreach ($this->basketItems as $index => $basketItem) {
            // delete it from the database
            $basketItem->delete();
        }

        $this->auditLog->addLog(
            entity: "Basket",
            action: "Checkout",
            entry: "Checked out basket for memberId {$this->getMember()->getMemberId()} () with total cost {$this->getTotalCost() } and bookingId {$booking->getBookingId()}"
        );

        $this->basketItems = [];
        $this->setTotalCost(0);

        
        return 0;
    }

}
?>