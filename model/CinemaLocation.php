<?php

require_once("Database.php");
require_once("Cinema.php");

/**
 * CLASS - CinemaLocation
 * INHERITS FROM - Database Class
 *
 * stores information about each location.
 */
CLASS CinemaLocation EXTENDS Database {

    private ?int $locationId;
    private ?string $locationName;
    private $GPS = null;
    private $address = null;
    private array $cinemas = []; // Aggregation of cinemas

    private static $tableName = "cinemaLocations";

    public function __construct(
        ?int $locationId=null,
        ?string $locationName=null,
        ?string $GPS=null,
        ?string $address=null,
        bool $dbGet=False
    ) {
        parent::__construct(); // gets a database connection
        $this->setLocationId($locationId);
        $this->setLocationName($locationName);
        $this->setGPS($GPS);
        $this->setAddress($address);

        IF ($this->exists()) {
            $this->getCinemaLocation(dbGet:$dbGet);   // Load from database
        }

    }

    // GETS and SETS
    // ?Type means nullable type declaration

    public function getLocationId() : ?int {
        return $this->locationId;
    }
    public function getLocationName() : ?string {
        return $this->locationName;
    }
    public function getGPS() : ?string {
        return ($this->GPS);
    }
    public function getAddress() : ?string {
        return $this->address;
    }
    public function getCinemas() : ?array {
            //Call Cinema Static Method to load Cinemas for this cinemaLocation
        if (empty($this->cinemas) && $this->getLocationId() !== null) {
            $this->cinemas = Cinema::loadCinemas(cinemaLocation: $this);
        }
        return $this->cinemas;
    }

    public function setLocationId(?int $locationId=null) {
        IF ($locationId) {
            $this->locationId = $locationId;
        }
    }
    public function setLocationName(?string $locationName=null) {
        IF ($locationName) {
            $this->locationName = $locationName;
        }
    }
    public function setGPS(?string $GPS=null) {
        IF ($GPS) {
            $this->GPS = $GPS;
        }
    }
    public function setAddress(?string $address=null) {
        IF ($address) {
            $this->address = $address;
        }
    }

    public function setCinemas(?array $cinemas=null) {
        IF ($cinemas) {
            $this->cinemas = $cinemas;
        }
    }

    // Object-Relational Mapping Methods

    private function exists() : bool {
        $exists = False;

        IF ($this->getLocationId()) {
            $sql = "SELECT COUNT(*) AS numRows FROM ".self::$tableName." WHERE locationId = ?";

            $results = $this->query($sql,[$this->getLocationId()]);

            FOREACH($results AS $result) {
                $numRows    = $result['numRows']; //num_rows;
            }
            $exists = $numRows==1;
        }
        RETURN $exists;
    }
    /**
     * Retrieve a specific Location where the locationId was stored in the object attribute
     */
    private function getCinemaLocation(bool $dbGet=True) {

        IF ($this->getLocationId()) {
            IF ($dbGet) {
                $sql = "SELECT locationId, locationName, GPS, address FROM ".self::$tableName." WHERE locationId = ?";

                $results = $this->query($sql, [$this->getLocationId()]);

                FOREACH ($results AS $result) {
                    $this->setLocationName($result['locationName']);
                    $this->setGPS($result['GPS']);
                    $this->setAddress($result['address']);
                }
            }
            // echo("Get Location: ".$this->getLocationId()."<br/>");

        }

    }
    /**
     * Static Method - Returns a List of CinemaLocations
     */

    public static function loadCinemaLocations() : array {

        $cinemaLocations = [];

        $sql = "SELECT locationId, locationName, GPS, address FROM ".self::$tableName." ORDER BY locationId";

        $db = new Database();
        $results = $db->query($sql);

        FOREACH ($results AS $result) {
            $cinemaLocation = new self(
                locationId : $result['locationId'],
                locationName : $result['locationName'],
                GPS : $result['GPS'],
                address : $result['address'],
                dbGet : False
            );
            // echo("Loading Location: ".$cinemaLocation->getLocationName()."<br/>");
            $cinemaLocations[] = $cinemaLocation; // appending each location to my list of locations
        }
        RETURN $cinemaLocations;
    }

    // Business Functions

    /**
     * Diagnotic Function - helps with testing
     */
    public function display() {

        echo("Location: (".$this->getLocationId().") ".$this->getLocationName()." - ".$this->getGPS()." - ".$this->getAddress()."<br/>");

        $cinemas = $this->getCinemas();
        FOREACH($cinemas AS $cinema) {
            $cinema->display();
        }
    }


} // END CLASS


// function test() {
//     $loc = new CinemaLocation(locationId : 1);
//     echo("Location: ".$loc->getLocationName()."<br/>");
//     // $loc->display();

//     $locs = CinemaLocation::loadCinemaLocations();
//     foreach($locs AS $loc) {
//         $loc->display();
//         echo("Location: ".$loc->getLocationName()."<br/>");
//     };
// }

// test();
?>