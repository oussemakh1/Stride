<?php

namespace Framework\Database;

use PDO;
use PDOException;

class Migrator
{
    protected $pdo;
    protected $migrationPath;

    public function __construct(string $migrationPath)
    {
        $this->pdo = Connection::getInstance();
        $this->migrationPath = $migrationPath;
        $this->ensureMigrationsTableExists();
    }

    protected function ensureMigrationsTableExists(): void
    {
        try {
            $this->pdo->exec("
                CREATE TABLE IF NOT EXISTS migrations (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    migration VARCHAR(255) NOT NULL,
                    batch INT NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ");
        } catch (PDOException $e) {
            // Log error or throw a more specific exception
            echo "Error creating migrations table: " . $e->getMessage() . "\n";
        }
    }

    public function run(): array
    {
        $messages = [];
        $files = glob($this->migrationPath . '/*.php');
        sort($files); // Ensure migrations run in order

        $ranMigrations = $this->getRanMigrations();
        $batch = $this->getNextBatchNumber();

        foreach ($files as $file) {
            $migrationName = basename($file, '.php');
            if (!in_array($migrationName, $ranMigrations)) {
                require_once $file;
                // Get the class name from the file
                $classes = get_declared_classes();
                $migrationClass = null;
                foreach ($classes as $class) {
                    if (is_subclass_of($class, 'Framework\Database\Migration')) {
                        $reflection = new \ReflectionClass($class);
                        if ($reflection->getFileName() === realpath($file)) {
                            $migrationClass = $class;
                            break;
                        }
                    }
                }

                if (!$migrationClass) {
                    $messages[] = "Error: Could not find migration class in {$migrationName}.";
                    continue; // Skip this migration
                }

                $migration = new $migrationClass();

                try {
                    $migration->up($this->pdo);
                    $this->recordMigration($migrationName, $batch);
                    $messages[] = "Migrated: {$migrationName}";
                } catch (PDOException $e) {
                    $messages[] = "Error migrating {$migrationName}: " . $e->getMessage();
                    break; // Stop on first error
                }
            }
        }

            // Auto-create tables for models with schema() method if they don't exist
            $modelFiles = glob(__DIR__ . '/../../app/Models/*.php');
            foreach ($modelFiles as $modelFile) {
                $modelName = basename($modelFile, '.php');
                $modelClass = 'App\\Models\\' . $modelName; // Fixed escaping
                if (method_exists($modelClass, 'schema')) {
                    $instance = new $modelClass();
                    $tableName = $instance->table;
                    try {
                        $stmt = $this->pdo->query("SHOW TABLES LIKE '{$tableName}'");
                        if ($stmt->rowCount() === 0) {
                            $modelClass::createTable();
                        }
                    } catch (PDOException $e) {
                        $messages[] = "Error checking or creating table for model {$modelName}: " . $e->getMessage();
                    } catch (\Exception $e) {
                        $messages[] = "Error with model schema for {$modelName}: " . $e->getMessage();
                    }
                }
            }
            return $messages;
        }

        public function rollback(): array
        {
            $messages = [];
            $lastBatch = $this->getLastBatchNumber();
            if ($lastBatch === 0) {
                $messages[] = "No migrations to rollback.";
                return $messages;
            }

            $migrationsToRollback = $this->getMigrationsInBatch($lastBatch);

            foreach (array_reverse($migrationsToRollback) as $migrationName) {
                $file = $this->migrationPath . '/' . $migrationName . '.php';
                if (file_exists($file)) {
                    require_once $file;
                    $classes = get_declared_classes();
                    $migrationClass = null;
                    foreach ($classes as $class) {
                        if (is_subclass_of($class, 'Framework\Database\Migration')) {
                            $reflection = new \ReflectionClass($class);
                            if ($reflection->getFileName() === realpath($file)) {
                                $migrationClass = $class;
                                break;
                            }
                        }
                    }

                    if (!$migrationClass) {
                        $messages[] = "Error: Could not find migration class in {$migrationName}.";
                        continue; // Skip this migration
                    }

                    $migration = new $migrationClass();

                    try {
                        $migration->down($this->pdo);
                        $this->deleteMigrationRecord($migrationName);
                        $messages[] = "Rolled back: {$migrationName}";
                    } catch (PDOException $e) {
                        $messages[] = "Error rolling back {$migrationName}: " . $e->getMessage();
                        break; // Stop on first error
                    }
                } else {
                    $messages[] = "Migration file not found for {$migrationName}.";
                }
            }
            return $messages;
        }

    protected function getRanMigrations(): array
    {
        $stmt = $this->pdo->query("SELECT migration FROM migrations ORDER BY batch ASC, migration ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    protected function getNextBatchNumber(): int
    {
        $stmt = $this->pdo->query("SELECT MAX(batch) FROM migrations");
        return (int) $stmt->fetchColumn() + 1;
    }

    protected function getLastBatchNumber(): int
    {
        $stmt = $this->pdo->query("SELECT MAX(batch) FROM migrations");
        return (int) $stmt->fetchColumn();
    }

    protected function getMigrationsInBatch(int $batch): array
    {
        $stmt = $this->pdo->prepare("SELECT migration FROM migrations WHERE batch = :batch ORDER BY migration DESC");
        $stmt->execute(['batch' => $batch]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    protected function recordMigration(string $migrationName, int $batch): void
    {
        $stmt = $this->pdo->prepare("INSERT INTO migrations (migration, batch) VALUES (:migration, :batch)");
        $stmt->execute(['migration' => $migrationName, 'batch' => $batch]);
    }

    protected function deleteMigrationRecord(string $migrationName): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM migrations WHERE migration = :migration");
        $stmt->execute(['migration' => $migrationName]);
    }
}