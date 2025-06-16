<?php

require_once("Database.php");
require_once("Session.php");
require_once("Movie.php");

CLASS BasketItem EXTENDS Database {
    private $basketItemId = null;

    private $sessionId = null;
    private $memberId = null;
    private $seats = null;
    private $totalCost = null;
    private $date = null;

    private ?Session $session = null;


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
        $this->setSeats($seats);
        $this->setDate($date);
        

        $this->findSession($sessionId);

        $this->setTotalCost($totalCost);
        $this->calculateTotalCost();
    }

    // GETS and SETS Methods

    // GET Methods
    public function getSession() {
        return $this->session;
    }

    public function getDate() {
        return $this->date;
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



    // SET Methods
    public function setDate($date) {
        $this->date = $date;
    }

    public function setSeatCost($seatCost) {
        $this->seatCost = $seatCost;
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

    public function setSession(?Session $session) {
        $this->session = $session;
    }

    public function setTotalCost($totalCost) {
        $this->totalCost = $totalCost;
    }

    public function calculateTotalCost() {

        if ($this->seats === null || $this->session->getSeatCost() === null) {
            return;
        }
        $this->setTotalCost($this->seats * $this->session->getSeatCost());
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

        IF ($this->getBasketItemId()) {
            $sql = "SELECT COUNT(*) AS numRows FROM basketItems WHERE basketItemId = ?";

            $results = $this->query($sql,[$this->getBasketItemId()]);

            FOREACH($results AS $result) {
                $numRows    = $result['numRows']; //num_rows;
            }
            $exists = $numRows==1;
        }
        return $exists;
    }


    public function save() {
        // Check if the record already exists
        IF ($this->exists()) {
            // Update the existing record
            $sql = "UPDATE basketItems SET sessionId = ?, memberId = ?, seats = ?, cost = ?
                      WHERE basketItemId = ?";
            $params = [$this->getSession()->getSessionId(), $this->memberId, $this->seats, $this->totalCost, $this->basketItemId];

        } else {
            // Insert a new record
            $sql = "INSERT INTO basketItems (sessionId, memberId, seats, cost, date)
                      VALUES (?, ?, ?, ?, ?)";
            $params = [$this->getSession()->getSessionId(), $this->memberId, $this->seats, $this->totalCost, $this->date];
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
} // END CLASS
?>