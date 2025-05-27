<?php

require_once("Database.php");
require_once("Session.php");

CLASS BookingItem EXTENDS Database {
    // from db
    private $sessionId = null;
    private $bookingId = null;
    private $seats = null;
    private $cost = null;
    private $date = null;
    private $time = null;

    // from session
    private $seatCost = null;
    private $movieId = null;
    private $cinemaId = null;
    
    // old
    private $movieName = null;
    private $posterFile = null;
    private $movieDescription = null;
    private $trailerName = null;

    private $memberId = null;

    /**
     * Constructor.
     *
     * @param all the fields in Movie - defaulting to null if not provided
     *
     */
    public function __construct(
        ?int $bookingItemId = null,
        ?int $sessionId = null,
        ?int $bookingId = null,
        ?int $seats = null,
        ?float $cost = null,
        ?string $date = null,
        ?string $time = null,
        ?bool $dbGet = True
    ) {
        parent::__construct(); // gets a database connection
        $this->setBookingItemId($bookingItemId);
        $this->setBookingId($bookingId);
        $this->setSessionId($sessionId);
        $this->setSeats($seats);
        $this->setDate($date);
        $this->setTime($time);
        $this->setCost($cost);
        

        $this->findSession();
        $this->findMovie();

        // IF ($this->exists()) {
        //     $this->getMovie(dbGet : $dbGet);
        // }
    }

    // GETS and SETS Methods

    // GET Methods
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

    public function getBookingId() {
        return $this->bookingId;
    }

    public function getSeats() {
        return $this->seats;
    }

    public function getSessionId() {
        return $this->sessionId;
    }

    public function getMovieId() {
        return $this->movieId;
    }

    public function getMovieName() {
        return $this->movieName;
    }

    public function getPosterFile() {
        return $this->posterFile;
    }

    public function getMovieDescription() {
        return $this->movieDescription;
    }

    public function getTrailerName() {
        return $this->trailerName;
    }

    public function getSessions(): ?array {
        // If sessions haven't been loaded yet, load them
        if (empty($this->sessions) && $this->getMovieId() !== null) {
            // echo("Loading sessions on demand for Cinema: ".$this->getMovieId()."<br/>");
            $this->sessions = Session::loadSessions(movie: $this);
        }
        return $this->sessions;
    }

    // SET Methods
    public function setDate($date) {
        $this->date = $date;
    }

    public function setSeatCost($seatCost) {
        $this->seatCost = $seatCost;
    }

    public function setTime($time) {
        $this->time = $time;
    }

    public function setbookingItemId($bookingItemId) {
        $this->bookingItemId = $bookingItemId;
    }

    public function setBookingId($bookingId) {
        $this->bookingId = $bookingId;
    }

    public function setSeats($seats) {
        $this->seats = $seats;
    }

    public function setSessionId($sessionId) {
        $this->sessionId = $sessionId;
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


    public function findMovie() {
        if ($this->sessionId) {
            $sql = "SELECT m.movieId, m.movieName, m.posterFile, m.movieDescription, m.trailerName
                    FROM sessions AS s
                    JOIN movies AS m ON s.movieId = m.movieId
                    WHERE s.sessionId = ?";
            $results = $this->query($sql, [$this->sessionId]);
            FOREACH($results AS $result) {
                $this->setMovieId($result['movieId']);
                $this->setMovieName($result['movieName']);
                $this->setPosterFile($result['posterFile']);
                $this->setMovieDescription($result['movieDescription']);
                $this->setTrailerName($result['trailerName']);
            }
        }
    }

    public function findSession() {
        if ($this->sessionId) {
            // seatCost and time
            $sql = "SELECT seatCost, time FROM sessions WHERE sessionId = ?";
            $results = $this->query($sql, [$this->sessionId]);
            FOREACH($results AS $result) {
                $this->seatCost = $result['seatCost'];
                // convert H:i:s to H:i to a string
                
                $time = DateTime::createFromFormat('H:i:s', $result['time']);
                // convert to string
                $time = $time ? $time->format('H:i') : null;


                $this->time = $time;
            }
        }
    }

    // Object Relational Mapping Methods
    /**
     * Check if this Session record exists in database
     */
    public function exists() {
        $exists = False;

        IF ($this->getMovieId()) {
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
            $params = [$this->sessionId, $this->memberId, $this->seats, $this->cost, $this->bookingItemId];

            
        } ELSE {
            // Insert a new record
            $sql = "INSERT INTO bookingItems (sessionId, bookingId, seats, cost, date, time)
                      VALUES (?, ?, ?, ?, ?, ?)";
            $params = [$this->sessionId, $this->bookingId, $this->seats, $this->cost, $this->date, $this->time];
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

    /**
     * Static Method to Load Movie from the database for a Session
     */
    public static function loadMovies(?Session $session=null) : array {
        $movies = [];

        IF ($session) {
            $sql = "SELECT ".implode(', ',self::fieldList)." FROM ".self::$tableName." AS m, sessions AS s WHERE s.movieId = m.movieId AND s.sessionId = ?";
            // echo("load Movies: ".$sql."<br/>");
            $db = Database();
            $results = $db->query($sql, [$session->getSessionId()]);
            FOREACH($results AS $result) {
                $movie = new self(
                    movieId:    $result['movieId'],
                    movieName:  $result['movieName'],
                    posterFile: $result['posterFile'],
                    movieDescription: $result['movieDescription'],
                    trailerName: $result['trailerName'],
                    dbGet : False
                );
                $movies[] = $movie;
            }
        }
        RETURN $movies;

    }

    // Business Functions

} // END CLASS
?>