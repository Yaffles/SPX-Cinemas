<?php

require_once("Database.php");
require_once("CinemaLocation.php");
require_once("Session.php");

/**
 * CLASS - Cinema
 * INHERITS FROM - Database Class
 *
 * stores information about each location.
 */
CLASS Cinema EXTENDS Database {

    private $cinemaId = null;
    private $cinemaName = null;
    private $cinemaLocation = null; // Parent Object
    private $sessions = []; // Initialize as empty array

    private static $tableName = "cinemas";


    public function __construct (
        ?int $cinemaId=null,
        ?string $cinemaName=null,
        ?CinemaLocation $cinemaLocation=null,
        bool $dbGet=True // Keep dbGet to control initial data loading
    ) {
        parent::__construct(); // gets a database connection
        // echo("Constructing Cinema: ".$cinemaName);

        $this->setCinemaId($cinemaId);
        $this->setCinemaName($cinemaName);
        $this->setCinemaLocation($cinemaLocation);

        // Only attempt to get full data from DB if dbGet is true and ID exists
        IF ($this->getCinemaId() && $dbGet) {
            $this->getCinema(); // Load from database, but don't load sessions here
        }
    }


    // GETS and SETS
    // ?Type means nullable type declaration

    public function getCinemaId() {
        return $this->cinemaId;
    }
    public function getCinemaName() {
        return $this->cinemaName;
    }
    public function getCinemaLocation() {
        return $this->cinemaLocation;
    }

    // Modified getSessions to load sessions on demand
    public function getSessions() : array {
        // If sessions haven't been loaded yet, load them
        if (empty($this->sessions) && $this->getCinemaId() !== null) {
            // echo("Loading sessions on demand for Cinema: ".$this->getCinemaId()."<br/>");
            $this->sessions = Session::loadSessions(cinema: $this);
        }
        return $this->sessions;
    }

    public function getMovies() : array {
        $movies = [];
        // Load sessions first to ensure we have the latest data
        $sessions = $this->getSessions();
        // Loop through sessions and get movies
        foreach ($sessions as $session) {
            $movie = $session->getMovie();
            if ($movie && !in_array($movie, $movies)) {
                $movies[] = $movie;
            }
        }
        return $movies;
    }

    public function setCinemaId(?int $cinemaId=null) {
        IF ($cinemaId) {
            $this->cinemaId = $cinemaId;
        }
    }
    public function setCinemaName(?string $cinemaName=null) {
        IF ($cinemaName) {
            $this->cinemaName = $cinemaName;
        }
    }
    public function setCinemaLocation(?CinemaLocation $cinemaLocation=null) {
        IF ($cinemaLocation) {
            $this->cinemaLocation = $cinemaLocation;
        }
    }

    // Removed setSessions as sessions are now loaded via getSessions()

    // Object-Relational Mapping Methods

    // exists() method remains the same
    private function exists() : bool {
        $exists = False;

        IF ($this->getCinemaId()) {
            $sql = "SELECT COUNT(*) AS numRows FROM ".self::$tableName." WHERE cinemaId = ?";

            $results = $this->query($sql,[$this->getCinemaId()]);

            FOREACH($results AS $result) {
                $numRows = $result['numRows']; //num_rows;
            }
            $exists = $numRows==1;
        }
        RETURN $exists;
    }


    private function getCinema() {
        IF ($this->getCinemaId()) {
            $sql = "SELECT cinemaId, cinemaName, locationId FROM ".self::$tableName." WHERE cinemaId = ?";
            $results = $this->query($sql,[$this->getCinemaId()]);
            FOREACH ($results AS $result) {
                $this->setCinemaId($result['cinemaId']);
                $this->setCinemaName($result['cinemaName']);
                // When creating CinemaLocation, we might want to load it fully (dbGet: True)
                // or just create a stub (dbGet: False) depending on requirements.
                // Loading fully here might cause recursion if CinemaLocation loads Cinemas.
                // For now, let's assume CinemaLocation doesn't load Cinemas in its constructor.
                $this->setCinemaLocation(new CinemaLocation(locationId:$result['locationId']));
            }
            // echo("Get Cinema data from DB: ".$this->getCinemaId()."<br/>");
        }
    }


    public static function loadCinemas(?CinemaLocation $cinemaLocation) {
        $cinemas = [];

        IF ($cinemaLocation) {
            $db = new Database();
            $sql = "SELECT cinemaId, cinemaName FROM ".self::$tableName." WHERE locationId = ?";
            // echo("Loading Cinemas for location: ".$cinemaLocation->getLocationId()."<br/>");

            $results = $db->query($sql,[$cinemaLocation->getLocationId()]);
            FOREACH ($results AS $result) {
                // Create Cinema objects with dbGet: True to load their basic data
                $cinema = new self(
                    cinemaId:$result['cinemaId'],
                    cinemaName: $result['cinemaName'],
                    cinemaLocation: $cinemaLocation,
                    dbGet: True // Set dbGet to True to load cinema details from DB
                );
                // echo("Loaded Cinema: ".$cinema->getCinemaId())."<br/>";
                $cinemas[] = $cinema;  // Append cinema to list
            }
        }
        RETURN $cinemas;
    }

    // BUSINESS METHODS

    public function display() {
        echo("Cinema: (".$this->getCinemaId().") ".$this->getCinemaName()." at <br/>");
        // You might want to loop through sessions here and display them
        // foreach ($this->getSessions() as $session) {
        //     $session->display(); // Assuming Session class has a display method
        // }
    }

} // END CLASS
?>