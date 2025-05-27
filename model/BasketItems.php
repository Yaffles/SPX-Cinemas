<?php

require_once("Database.php");
require_once("Session.php");

CLASS BasketItem EXTENDS Database {

    private $sessionId = null;
    private $memberId = null;
    private $seats = null;
    private $totalCost = null;
    private $date = null;

    private $seatCost = null;
    private $time = null;
    private $movieId = null;
    private $cinemaId = null;
    
    // old
    private $movieName = null;
    private $posterFile = null;
    private $movieDescription = null;
    private $trailerName = null;


    /**
     * Constructor.
     *
     * @param all the fields in Movie - defaulting to null if not provided
     *
     */
    public function __construct(
        ?int $basketItemId = null,
        ?int $sessionId = null,
        ?int $memberId = null,
        ?int $seats = null,
        ?bool $dbGet = True,
        ?float $totalCost = null,
        ?string $date = null,
    ) {
        parent::__construct(); // gets a database connection
        $this->setBasketItemId($basketItemId);
        $this->setMemberId($memberId);
        $this->setSessionId($sessionId);
        $this->setSeats($seats);
        $this->setDate($date);
        

        $this->findSession();

        $this->setTotalCost($totalCost);
        $this->calculateTotalCost();
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

    public function getBasketItemId() {
        return $this->basketItemId;
    }

    public function getTotalCost() {
        return $this->totalCost;
    }

    public function getMemberId() {
        return $this->memberId;
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

    public function setBasketItemId($basketItemId) {
        $this->basketItemId = $basketItemId;
    }

    public function setMemberId($memberId) {
        $this->memberId = $memberId;
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

    public function setTotalCost($totalCost) {
        $this->totalCost = $totalCost;
    }

    public function calculateTotalCost() {

        if ($this->seats === null || $this->seatCost === null) {
            return;
        }
        $this->totalCost = $this->seats * $this->seatCost;
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
            $sql = "SELECT COUNT(*) AS numRows FROM basketItems WHERE basketItemId = ?";

            $results = $this->query($sql,[$this->getBasketItemId()]);

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
            $sql = "UPDATE basketItems SET sessionId = ?, memberId = ?, seats = ?, cost = ?
                      WHERE basketItemId = ?";
            $params = [$this->sessionId, $this->memberId, $this->seats, $this->totalCost, $this->basketItemId];

            
        } ELSE {
            // Insert a new record
            $sql = "INSERT INTO basketItems (sessionId, memberId, seats, cost, date)
                      VALUES (?, ?, ?, ?, ?)";
            $params = [$this->sessionId, $this->memberId, $this->seats, $this->totalCost, $this->date];
        }

        // Execute the query
        $res = $this->query($sql, $params);

        IF ($res) {
            // If it's an insert, get the last inserted ID
            IF ($this->basketItemId == null) {
                $this->basketItemId = $res;
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
            $sql = "DELETE FROM basketItems WHERE basketItemId = ?";
            $params = [$this->basketItemId];
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