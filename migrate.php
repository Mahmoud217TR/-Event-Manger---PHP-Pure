<?php

require __DIR__ . '/config.php';

$sqlFile = 'database.sql';

try {
    global $pdo;
    
    $queries = file_get_contents($sqlFile);

    if ($queries === false) {
        throw new Exception("Could not read the SQL file.");
    }

    $pdo->exec($queries);

    echo "Migration completed successfully.\n";
} catch (PDOException $exception) {
    echo "Database error: " . $exceptione->getMessage() . "\n";
} catch (Exception $exception) {
    echo "Error: " . $exception->getMessage() . "\n";
}
