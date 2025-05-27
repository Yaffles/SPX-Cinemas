<?php

require_once("Database.php");
require_once("Session.php");

CLASS Movie EXTENDS Database {

    private $movieId = null;
    private $movieName = null;
    private $posterFile = null;
    private $movieDescription = null;
    private $trailerName = null;
    private ?array $sessions = null;

    private static $tableName = "movies";
    private static $entityName = "Movies";
    private static array $fieldNames = array('movieId', 'movieName', 'posterFile', 'movieDescription', 'trailerName');
    private static string $pk = "movieId";
    /**
     * Constructor.
     *
     * @param all the fields in Movie - defaulting to null if not provided
     *
     */
    public function __construct(
        ?int $movieId = null,
        ?string $movieName = null,
        ?string $posterFile = null,
        ?string $movieDescription = null,
        ?string $trailerName = null,
        bool $dbGet = True
    ) {
        parent::__construct(); // gets a database connection
        $this->setMovieId($movieId);
        $this->setMovieName($movieName);
        $this->setPosterFile($posterFile);
        $this->setMovieDescription($movieDescription);
        $this->setTrailerName($trailerName);

        IF ($this->exists()) {
            $this->getMovie(dbGet : $dbGet);
        }
    }

    // GETS and SETS Methods

    // GET Methods
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
    public function getMovie(?bool $dbGet=False) {
        IF ($this->getMovieId()) {
            IF ($dbGet){
                $sql = "SELECT ".implode(', ',self::$fieldNames)." FROM ".self::$tableName." WHERE ".self::$pk." = ?";
                // echo($sql." ".$this->getMovieId());

                $results = $this->query($sql,[$this->getMovieId()]);

                FOREACH($results AS $result) {
                    $this->setMovieName($result['movieName']);
                    $this->setPosterFile($result['posterFile']);
                    $this->setMovieDescription($result['movieDescription']);
                    $this->setTrailerName($result['trailerName']);
                }
            }
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
        return $movies;

    }

    // Business Functions

} // END CLASS
?>