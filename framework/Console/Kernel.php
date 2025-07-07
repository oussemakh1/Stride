<?php

namespace Framework\Console;

use Framework\Support\Config;

class Kernel
{
    protected $commands = [
        // Register your commands here
        Commands\GenerateCommand::class,
        Commands\MigrateCommand::class,
        Commands\RollbackCommand::class,
        Commands\QueueWorkCommand::class,
        Commands\MakeMigrationCommand::class,
    ];

    public function handle(array $argv)
    {
        // Manually load .env file and populate $_ENV
        $envFile = dirname(__DIR__, 2) . '/.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos($line, '#') === 0) {
                    continue;
                }
                list($key, $value) = explode('=', $line, 2);
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }

        // Load application configuration
        $appConfig = require __DIR__ . '/../../config/app.php';
        $cacheConfig = require __DIR__ . '/../../config/cache.php';
        $databaseConfig = require __DIR__ . '/../../config/database.php';

        // Load application configuration into the Config class
        Config::load(array_merge($appConfig, ['cache' => $cacheConfig, 'database' => $databaseConfig, 'sidebar' => require __DIR__ . '/../../config/sidebar.php']));
        $commandName = $argv[1] ?? null;

        if (!$commandName) {
            echo "Usage: php stride <command> [arguments]
";
            echo "Available commands:\n";
            foreach ($this->commands as $commandClass) {
                $command = new $commandClass();
                echo "  " . $command->getName() . "\t\t" . $command->getDescription() . "\n";
            }
            return;
        }

        foreach ($this->commands as $commandClass) {
            $command = new $commandClass();
            if ($command->getName() === $commandName) {
                $command->execute(array_slice($argv, 2));
                return;
            }
        }

        echo "Command '{$commandName}' not found.\n";
    }
}
