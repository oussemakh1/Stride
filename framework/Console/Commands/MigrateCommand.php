<?php

namespace Framework\Console\Commands;

use Framework\Console\Command;
use Framework\Database\Migrator;

class MigrateCommand extends Command
{
    protected $name = 'migrate';
    protected $description = 'Runs database migrations.';

    public function execute(array $args)
    {
        $migrator = new Migrator(__DIR__ . '/../../../migrations');
        $messages = $migrator->run();
        foreach ($messages as $message) {
            echo $message . "\n";
        }
    }
}
