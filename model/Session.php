<?php

require_once("Database.php");
require_once("Cinema.php");
require_once("Movie.php");

CLASS Session EXTENDS Database {

    private ?int $sessionId = null;
    private ?Cinema $cinema = null; // Stores a reference to the Cinema object
    private ?Movie $movie = null;
    private ?string $time = null;
    private ?float $seatCost = null;

    private static array $fieldNames = array('sessionId', 'cinemaId', 'movieId', 'time', 'seatCost');
    private static string $pk = "sessionId";
    private static string $tableName = "sessions";
    /**
     * Constructor Method
     */
    public function __construct(
        ?int $sessionId = null,
        ?Cinema $cinema = null, // Accept a Cinema object or null
        ?Movie $movie = null,
        ?string $time = null,
        ?float $seatCost = null,
        bool $dbGet=True // Keep dbGet to control initial data loading
    ) {

        parent::__construct(); // gets a database connection

        $this->setSessionId($sessionId);
        $this->setCinema($cinema); // Set the provided Cinema object
        $this->setMovie($movie);
        $this->setTime($time);
        $this->setSeatCost($seatCost);


        // Only attempt to get full data from DB if dbGet is true and ID exists
        IF ($this->getSessionId() && $dbGet) {
            $this->getSession(); // load from database
        }

    }

    // GETS and SETS Methods

    // GET Methods
    public function getSessionId(): ?int {
        return $this->sessionId;
    }

    public function getCinema(): ?Cinema {
        // If the cinema object is not set but we have a session ID,
        // we could potentially load it here on demand, similar to how
        // Cinema loads Sessions. However, the current structure loads
        // cinema_id in getSession, so it's already handled there.
        return $this->cinema;
    }

    public function getMovie(): ?Movie {
        return $this->movie;
    }

    public function getTime(): ?string {
        if (!$this->time) {
            return null;
        }
    
        $time = DateTime::createFromFormat('H:i:s', $this->time);
        return $time ? $time->format('H:i') : $this->time;
    }

    public function getSeatCost(): ?float {
        return $this->seatCost;
    }

    public function getTotalCost(int $seats): ?float {
        if ($this->seatCost === null) {
            return null;
        }
        return $this->seatCost * $seats;
    }

    // SET Methods
    public function setSessionId(?int $sessionId) {
        $this->sessionId = $sessionId;
    }

    public function setCinema(?Cinema $cinema) {
        $this->cinema = $cinema;
    }

    public function setMovie(?Movie $movie) {
        $this->movie = $movie;
    }

    public function setTime(?string $time) {
        $this->time = $time;
    }

    public function setSeatCost(?float $seatCost) {
        $this->seatCost = $seatCost;
    }

    // Object-Relational Mapping Methods

    // exists() method remains the same
    public function exists() {
        $exists = False;

        IF ($this->getSessionId()) {
            $sql = "SELECT COUNT(*) AS numRows FROM ".self::$tableName." WHERE sessionId = ?";

            $results = $this->query($sql,[$this->getSessionId()]);

            FOREACH($results AS $result) {
                $numRows    = $result['numRows']; //num_rows;
            }
            $exists = $numRows==1;
        }
        RETURN $exists;
    }

    public function getSession(?bool $dbGet=True){
        IF ($this->getSessionId()) {
            IF ($dbGet) {
                $sql = "SELECT ".implode(', ',self::$fieldNames)." FROM ".self::$tableName." WHERE ".self::$pk." = ?";

                // echo("Get Session data from DB: ".$sql." ".$this->getSessionId()."<br/>" );

                $results = $this->query($sql,[$this->getSessionId()]);

                FOREACH($results AS $result) {
                    // When creating the Cinema object here, pass dbGet: False
                    // This prevents the Cinema constructor from immediately trying to load sessions
                    $this->setCinema(new Cinema(cinemaId: (int) $result['cinemaId'], dbGet: False));
                    $this->setMovie(new Movie(movieId:$result['movieId'])); // Assuming Movie doesn't cause recursion
                    $this->setTime($result['time']);
                    $this->setSeatCost($result['seatCost']);
                }
            }
        }
    }
    /**
     * Static Method that loads an array of Sessions for a Cinema or a Movie
     * This method is called by Cinema::getSessions() (on demand) or potentially elsewhere.
     */
    public static function loadSessions(?Cinema $cinema=null, ?Movie $movie=null ) : array {
        $sessions = [];
        $sql = "SELECT sessionId, cinemaId, movieId, time, seatCost FROM " . self::$tableName;

        $params = [];
        if ($cinema && $movie) {
            $sql .= " WHERE cinemaId = ? AND movieId = ?";
            $params[] = $cinema->getCinemaId();
            $params[] = $movie->getMovieId();
        } elseif ($cinema) {
            $sql .= " WHERE cinemaId = ?";
            $params[] = $cinema->getCinemaId();
        } elseif ($movie) {
            $sql .= " WHERE movieId = ?";
            $params[] = $movie->getMovieId();
        } else {
            // Optional: return empty if neither is provided
            return [];
        }

        // echo("Load Sessions: ".$sql." ".implode(',', $params)."<br/>");
        $sql .= " ORDER BY time";
        $db = new Database();
        $results = $db->query($sql,$params); // Pass parameters as an array
        FOREACH($results AS $result) {
            // echo("Found Session#: ".$result['sessionId']."<br/>");
            // When creating the Session object, pass dbGet: True to load its own data
            // but ensure the Cinema object created within it uses dbGet: False
            $session = new self(
                sessionId : $result['sessionId'],
                // Pass the existing Cinema object if available, or create a stub with dbGet: False
                cinema : $cinema ?? new Cinema(cinemaId: (int) $result['cinemaId']),
                movie :  $movie ?? new Movie(movieId: (int) $result['movieId']), // Assuming Movie doesn't cause recursion
                time : $result['time'],
                seatCost : $result['seatCost'],
                dbGet : True // Set dbGet to True to load session details from DB
            );
            // echo("Adding Session# to list: ".$session->getSessionId()."<br/>");
            $sessions[] = $session;
        }
        RETURN $sessions;
    }

    // Business Functions
    // Add a display method for sessions if needed
    public function display() {
        echo "Session ID: " . $this->getSessionId() . "<br/>";
        echo "  Time: " . $this->getTime() . "<br/>";
        echo "  Cost: " . $this->getSeatCost() . "<br/>";
        // Display movie details if available
        if ($this->getMovie()) {
            // Assuming Movie has a display method or getName method
            echo "  Movie: " . $this->getMovie()->getName() . "<br/>";
        }
        // Display cinema details (just the name to avoid deep nesting)
        if ($this->getCinema()) {
             echo "  Cinema: " . $this->getCinema()->getCinemaName() . "<br/>";
        }
        echo "<br/>";
    }


} // END CLASS
?>