<?php

require_once("Database.php");
require_once("Session.php");
require_once("Booking.php");

CLASS BookingItem EXTENDS Database {
    // from db
    private ?Booking $booking = null;
    private $bookingItemId = null;
    private $seats = null;
    private $cost = null;
    private $date = null;
    private $time = null;

    private ?Session $session = null;
    


    /**
     * Constructor.
     *
     * @param all the fields in Movie - defaulting to null if not provided
     *
     */
    public function __construct(
        ?int $bookingItemId = null,
        ?int $sessionId = null,
        ?Booking $booking = null,
        ?int $seats = null,
        ?float $cost = null,
        ?string $date = null,
        ?bool $dbGet = True
    ) {
        parent::__construct(); // gets a database connection

        $this->setBookingItemId($bookingItemId);
        if ($booking) {
            $this->setBooking($booking);
        }
        
        $this->setSeats($seats);
        $this->setDate($date);
        $this->setCost($cost);

        $this->findSession($sessionId);;
    }

    // GETS and SETS Methods

    // GET Methods
    public function getSession() {
        return $this->session;
    }
    public function getDate() {
        return $this->date;
    }

    public function getTime() {
        return $this->time;
    }

    public function getBookingItemId() {
        return $this->bookingItemId;
    }

    public function getCost() {
        return $this->cost;
    }

    public function getBooking() {
        return $this->booking;
    }

    public function getSeats() {
        return $this->seats;
    }

    // SET Methods
    public function setSeatCost($seatCost) {
        $this->seatCost = $seatCost;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function setBookingItemId($bookingItemId) {
        $this->bookingItemId = $bookingItemId;
    }

    public function setBooking(?Booking $booking) {
        $this->booking = $booking;
    }

    public function setSeats($seats) {
        $this->seats = $seats;
    }


    public function setSession(?Session $session) {
        $this->session = $session;
    }

    public function setCost($cost) {
        $this->cost = $cost;
    }

    public function calculateTotalCost() {

        if ($this->seats === null || $this->seatCost === null) {
            return;
        }
        $this->cost = $this->seats * $this->seatCost;
    }


    public function findSession($sessionId) {
        if ($sessionId) {
            $this->session = new Session(sessionId: $sessionId, dbGet: True);
        }
    }

    // Object Relational Mapping Methods
    /**
     * Check if this Session record exists in database
     */
    public function exists() {
        $exists = False;

        IF ($this->getBookingItemId()) {
            $sql = "SELECT COUNT(*) AS numRows FROM bookingItems WHERE bookingItemId = ?";

            $results = $this->query($sql,[$this->getBookingItemId()]);

            FOREACH($results AS $result) {
                $numRows    = $result['numRows']; //num_rows;
            }
            $exists = $numRows==1;
        }
        RETURN $exists;
    }


    public function save() {
        // Check if the record already exists
        IF ($this->exists()) {
            // Update the existing record
            $sql = "UPDATE bookingItems SET sessionId = ?, bookingId = ?, seats = ?, cost = ?
                      WHERE bookingItemId = ?";
            $params = [$this->getSession()->getSessionId(), $this->getBooking()->getBookingId(), $this->getSeats(), $this->getCost(), $this->getBookingItemId()];

        } else {
            // Insert a new record
            $sql = "INSERT INTO bookingItems (sessionId, bookingId, seats, cost, date)
                      VALUES (?, ?, ?, ?, ?)";
            $params = [$this->getSession()->getSessionId(), $this->getBooking()->getBookingId(), $this->getSeats(), $this->getCost(), $this->getDate()];
        }

        // Execute the query
        $res = $this->query($sql, $params);

        IF ($res) {
            // If it's an insert, get the last inserted ID
            IF ($this->bookingItemId == null) {
                $this->bookingItemId = $this->getConn()->insert_id;
            }
            return True;
        } ELSE {
            return False;
        }
  }

    /**
     * Delete the record from the database
     */
    public function delete() {
        IF ($this->exists()) {
            $sql = "DELETE FROM bookingItems WHERE bookingItemId = ?";
            $params = [$this->bookingItemId];
            $res = $this->query($sql, $params);
            return True;
        } ELSE {
            return False;
        }
    }

   

    // Business Functions

} // END CLASS
?>