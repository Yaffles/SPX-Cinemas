<?php
require_once("database.php");

class AuditLog extends Database {
    private $tableName = "auditlog";
    
    public function __construct() {
        parent::__construct(); // Initialize database connection
    }

    /**
     * Add an entry to the audit log
     * @param string $entity The entity being logged (e.g., "Member")
     * @param string $action The action performed (e.g., "Login Attempt")
     * @param string $entry Detailed log message
     * @return bool True on success, False on failure
     */
    public function addLog($entity, $action, $entry) {
        echo "Adding log entry: Entity: $entity, Action: $action, Entry: $entry\n"; // Debugging output  TODO get rid of
        try {
            $sql = "INSERT INTO {$this->tableName} (entity, action, entry) VALUES (?, ?, ?)";
            $stmt = $this->getConn()->prepare($sql);
            
            $stmt->bind_param("sss", $entity, $action, $entry);
            
            if (!$stmt->execute()) {
                throw new Exception("Audit log insert failed: " . $stmt->error);
            }
            
            $this->commit();
            return true;
            
        } catch (Exception $e) {
            // Log error to console for debugging
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Get recent audit log entries
     * @param int $limit Number of entries to retrieve
     * @return array Array of log entries
     */
    public function getLogs($limit = 100) {
        $logs = [];
        try {
            $sql = "SELECT timestamp, entity, action, entry 
                    FROM {$this->tableName} 
                    ORDER BY timestamp DESC 
                    LIMIT ?";
            $stmt = $this->getConn()->prepare($sql);
            $stmt->bind_param("i", $limit);
            $stmt->execute();
            
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $logs[] = $row;
            }
            
            return $logs;
            
        } catch (Exception $e) {
            error_log($e->getMessage());
            return [];
        }
    }
}
?>