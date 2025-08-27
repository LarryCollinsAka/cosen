<?php

// database/migrations-runner.php

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\Schema;

// Load Composer's autoloader
require __DIR__ . '/../../vendor/autoload.php';

// Load database configuration
$dbConfig = require __DIR__ . '/../../src/backend/config/database.php';

// Create a new database connection instance
try {
    $connection = DriverManager::getConnection($dbConfig);
    echo "Successfully connected to the database.\n";
} catch (\Exception $e) {
    die("Could not connect to the database: " . $e->getMessage());
}

// Create a new schema manager instance
$schemaManager = $connection->createSchemaManager();

// Create a new schema blueprint
$schema = new Schema();
$table = $schema->createTable('incidents');

// Define table columns
$table->addColumn('id', 'string', ['length' => 36]); // UUID stored as a string
$table->setPrimaryKey(['id']);
$table->addColumn('title', 'string', ['length' => 255]);
$table->addColumn('description', 'text');
$table->addColumn('type', 'string', ['length' => 50]);
$table->addColumn('status', 'string', ['length' => 50, 'default' => 'reported']);
$table->addColumn('latitude', 'string', ['length' => 20, 'notnull' => false]);
$table->addColumn('longitude', 'string', ['length' => 20, 'notnull' => false]);
$table->addColumn('photo_url', 'string', ['notnull' => false]);
$table->addColumn('user_id', 'string', ['length' => 36, 'notnull' => false]);
$table->addColumn('created_at', 'datetime_immutable');
$table->addColumn('updated_at', 'datetime_immutable');

// Get the SQL queries needed to update the schema
$queries = $schemaManager->getDatabasePlatform()->getCreateTableSQL($table);

// Execute the queries
foreach ($queries as $query) {
    echo "Executing query: " . $query . "\n";
    $connection->executeQuery($query);
}

echo "Migration for 'incidents' table completed successfully!\n";