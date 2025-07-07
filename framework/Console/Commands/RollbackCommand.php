<?php

namespace Framework\Console\Commands;

use Framework\Console\Command;
use Framework\Database\Migrator;

class RollbackCommand extends Command
{
    protected $name = 'rollback';
    protected $description = 'Rolls back the last batch of database migrations.';

    public function execute(array $args)
    {
        $migrator = new Migrator(__DIR__ . '/../../../migrations');
        $messages = $migrator->rollback();
        foreach ($messages as $message) {
            echo $message . "\n";
        }
    }
}
