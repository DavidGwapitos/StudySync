<?php
/**
 * Database Configuration and Connection Class
 * Handles all database connections and prepared statements
 * 
 * @package StudySync
 * @subpackage Config
 */

class Database {
    // Database credentials - CONFIGURE THESE FOR YOUR ENVIRONMENT
    private $host = 'localhost';
    private $db_name = 'studysync';
    private $db_user = 'root';
    private $db_password = '';
    private $connection;

    /**
     * Connect to database
     * Uses MySQLi for secure prepared statements
     * 
     * @return mysqli|null Database connection or null on failure
     */
    public function connect() {
        // Turn off mysqli exceptions so we can handle errors gracefully
        mysqli_report(MYSQLI_REPORT_OFF);

        // Suppress warnings while we handle connection errors manually
        $this->connection = @new mysqli($this->host, $this->db_user, $this->db_password, $this->db_name);

        // If the database does not exist, attempt to create it and initialize schema
        if ($this->connection->connect_errno === 1049) {
            error_log("Database Connection Error: " . $this->connection->connect_error);
            if ($this->createDatabase() && $this->initializeSchema()) {
                return $this->connect();
            }
            return null;
        }

        // Check other connection errors
        if ($this->connection->connect_error) {
            error_log("Database Connection Error: " . $this->connection->connect_error);
            return null;
        }

        // Set charset to utf8mb4
        $this->connection->set_charset("utf8mb4");

        return $this->connection;
    }

    /**
     * Create the configured database if it does not exist
     *
     * @return bool True if created or already exists, false otherwise
     */
    private function createDatabase() {
        $tempConnection = new mysqli($this->host, $this->db_user, $this->db_password);
        if ($tempConnection->connect_error) {
            error_log("Database Setup Error: " . $tempConnection->connect_error);
            return false;
        }

        $sql = "CREATE DATABASE IF NOT EXISTS `" . $this->db_name . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
        if (!$tempConnection->query($sql)) {
            error_log("Database Creation Error: " . $tempConnection->error);
            $tempConnection->close();
            return false;
        }

        $tempConnection->close();
        return true;
    }

    /**
     * Initialize database schema from the SQL file
     *
     * @return bool True if schema initialized successfully, false otherwise
     */
    private function initializeSchema() {
        $sqlFile = __DIR__ . '/../database/database.sql';
        if (!file_exists($sqlFile)) {
            error_log("Database Schema Error: SQL file not found at " . $sqlFile);
            return false;
        }

        $sql = file_get_contents($sqlFile);
        if ($sql === false) {
            error_log("Database Schema Error: Unable to read SQL file.");
            return false;
        }

        $schemaConnection = new mysqli($this->host, $this->db_user, $this->db_password, $this->db_name);
        if ($schemaConnection->connect_error) {
            error_log("Database Schema Connection Error: " . $schemaConnection->connect_error);
            return false;
        }

        if (!$schemaConnection->multi_query($sql)) {
            error_log("Database Schema Execution Error: " . $schemaConnection->error);
            $schemaConnection->close();
            return false;
        }

        while ($schemaConnection->more_results() && $schemaConnection->next_result()) {
            // Consume all results
        }

        $schemaConnection->close();
        return true;
    }

    /**
     * Get current database connection
     * 
     * @return mysqli Current database connection
     */
    public function getConnection() {
        if ($this->connection === null) {
            $this->connect();
        }
        return $this->connection;
    }

    /**
     * Close database connection
     * 
     * @return void
     */
    public function closeConnection() {
        if ($this->connection) {
            $this->connection->close();
        }
    }

    /**
     * Update database credentials
     * Call this method to set custom database configuration
     * 
     * @param string $host Database host
     * @param string $db_name Database name
     * @param string $db_user Database user
     * @param string $db_password Database password
     * @return void
     */
    public function setCredentials($host, $db_name, $db_user, $db_password) {
        $this->host = $host;
        $this->db_name = $db_name;
        $this->db_user = $db_user;
        $this->db_password = $db_password;
    }
}
?>
