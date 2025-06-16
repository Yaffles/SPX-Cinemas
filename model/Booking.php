<?php

require_once("Database.php");
require_once("Session.php");
require_once("member.php");
require_once("BasketItems.php");
require_once("BookingItems.php");

CLASS Booking EXTENDS Database {
    private ?Member $member = null;
    
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
        ?Member $member = null,
        ?int $bookingId = null,
        ?float $cost = null,
        ?string $bookingDate = null,
        bool $dbGet = True
    ) {
        parent::__construct(); // gets a database connection

        if ($member) {
            $this->setMember($member);
        }

        $this->setBookingId($bookingId);
        $this->setCost($cost);
        $this->setBookingDate($bookingDate);

        if ($dbGet and $this->getMember()) {
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

    public function getMember() {
        return $this->member;
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

    public function setMember(?Member $member) {
        $this->member = $member;
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





    public function save() {

        $sql = "INSERT INTO bookings (memberId, cost) VALUES (?, ?)";
        $res = $this->query($sql, [$this->getMember()->getMemberId(), $this->getCost()]);

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
        IF ($this->getBookingId()) {
            $this->setCost(0);
            $sql = "SELECT bookingItemId, sessionId, bookingId, seats, cost, date FROM bookingitems WHERE bookingId = ?";
            $results = $this->query($sql,[$this->getBookingId()]);
            FOREACH ($results AS $result) {
                $bookingItem = new BookingItem(
                    bookingItemId: $result['bookingItemId'],
                    sessionId: $result['sessionId'],
                    booking: $this,
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
            booking: $this,
            seats: $seats,
            date: $date,
            dbGet: False
        );

        $BookingItem->save();

        $this->basketItems[] = $basketItem;
        $this->setCost($this->getCost() + $basketItem->getCost());
        return 0;
    }

    public static function loadBookings(?Member $member=null) : array {
        $bookings = [];

        IF ($member) {
            $sql = "SELECT bookingId, cost, bookingDate FROM bookings WHERE memberId = ? ORDER BY bookingId DESC";       
            $db = new Database();
            $results = $db->query($sql, [$member->getMemberId()]);
            foreach ($results as $result) {
                $booking = new self(
                    member: $member,
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