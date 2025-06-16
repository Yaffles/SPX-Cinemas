<?php

require_once("Database.php");
require_once("Session.php");
require_once("member.php");
require_once("BasketItems.php");
require_once("BookingItems.php");

CLASS Booking EXTENDS Database {

    private $memberId = null;
    
    private $bookingId = null;
    private float $cost = 0.0;
    private ?string $bookingDate = null;


    private ?array $bookingItems = [];

    /**
     * Constructor.
     *
     * @param all the fields in Movie - defaulting to null if not provided
     *
     */
    public function __construct(
        ?int $memberId = null,
        ?int $bookingId = null,
        ?float $cost = null,
        ?string $bookingDate = null,
        bool $dbGet = True
    ) {
        parent::__construct(); // gets a database connection

        if ($memberId) {
            $this->setMemberId($memberId);
        }

        $this->setBookingId($bookingId);
        $this->setCost($cost);
        $this->setBookingDate($bookingDate);

        if ($dbGet and $memberId) {
            // fill in the rest of the variables from the database with one function for all
            $this->getBooking();
        }
    }

    // GETS and SETS Methods

    // GET Methods
    public function getBookingId() {
        return $this->bookingId;
    }

    public function getBookingDate() {
        return $this->bookingDate;
    }

    public function getMemberId() {
        return $this->memberId;
    }
    public function getCost() {
        return $this->cost;
    }

    public function getBookingItems(): ?array {
        return $this->bookingItems;
    }

    // SET Methods
    public function setBookingId($bookingId) {
        $this->bookingId = $bookingId;
    }

    public function setBookingDate($bookingDate) {
        $this->bookingDate = $bookingDate;
    }

    public function setMemberId($memberId) {
        $this->memberId = $memberId;
    }

    public function setCost($cost) {
        $this->cost = $cost;
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

    public function save() {

        $sql = "INSERT INTO bookings (memberId, cost) VALUES (?, ?)";
        $res = $this->query($sql, [$this->getMemberId(), $this->getCost()]);

        IF ($res) {
            // If it's an insert, get the last inserted ID
            IF ($this->bookingId == null) {

                $this->bookingId = $res;
            }
            return True;
        } ELSE {
            return False;
        }
    }




    /**
     * Get Movie from database based on movieId
     */
    public function getBooking() {
        IF ($this->getMemberId()) {
            $sql = "SELECT bookingItemId, sessionId, bookingId, seats, cost, date FROM bookingitems WHERE bookingId = ?";
            $results = $this->query($sql,[$this->getBookingId()]);
            FOREACH ($results AS $result) {
                $bookingItem = new BookingItem(
                    bookingItemId: $result['bookingItemId'],
                    sessionId: $result['sessionId'],
                    bookingId: $result['bookingId'],
                    seats: $result['seats'],
                    cost: $result['cost'],
                    date: $result['date'],
                    dbGet: False
                );

                $this->bookingItems[] = $bookingItem;
                $this->setCost($this->getCost() + $bookingItem->getCost());
            }
            
        }
    }



    // Business Functions

    public function addItem(?int $sessionId, ?int $seats, ?string $date ) {
        $session = new Session(sessionId: $sessionId, dbGet: True);


        foreach ($this->bookingItems as $bookingItem) {
            if ($bookingItem->getSessionId() == $sessionId) {
                echo "Session already exists";
                return 0;
            }
        }

        $BookingItem = new BookingItem(
            sessionId: $session->getSessionId(),
            bookingId: $this->getBookingId(),
            seats: $seats,
            date: $date,
            dbGet: False
        );

        $BookingItem->save();

        $this->basketItems[] = $basketItem;
        $this->setCost($this->getCost() + $basketItem->getCost());
        return 0;
    }

    public static function loadBookings(?int $memberId=null) : array {
        $bookings = [];

        IF ($memberId) {
            $sql = "SELECT bookingId, cost, bookingDate FROM bookings WHERE memberId = ? ORDER BY bookingId DESC";       
            $db = new Database();
            $results = $db->query($sql, [$memberId]);
            foreach ($results as $result) {
                $booking = new self(
                    memberId: $memberId,
                    bookingId: $result['bookingId'],
                    cost: $result['cost'],
                    bookingDate: $result['bookingDate'],
                    dbGet : True
                );
                $bookings[] = $booking;
            }
        }
        return $bookings;
    }
}
?>