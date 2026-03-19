<?php

namespace app\repositories;

use PDO;

/** Base repository providing a secure PDO connection to be used by child repositories.
 * Configured with:
 * - Exception mode for proper error handling
 * - Real prepared statements (not emulated) for SQL injection prevention
 * - Consistent associative array fetch mode */
abstract class BaseRepository
{
    protected PDO $connection;

    function __construct()
    {
        $dsn = "{$_ENV['DB_TYPE']}:host={$_ENV['DB_HOST']};port={$_ENV['DB_PORT']};dbname={$_ENV['DB_DATABASE']}";
        $this->connection = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);

        // Security & Performance
        $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Catch PDOExceptions and throw general exceptions for better error handling
        $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false); // Use real prepared statements
        $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Consistent fetch mode
    }
}