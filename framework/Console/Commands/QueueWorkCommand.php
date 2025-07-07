<?php

namespace Framework\Console\Commands;

use Framework\Console\Command;
use Framework\Queue\Queue;

class QueueWorkCommand extends Command
{
    protected $name = 'queue:work';
    protected $description = 'Processes jobs from the queue.';

    public function execute(array $args)
    {
        $queue = new Queue(__DIR__ . '/../../../queue_jobs'); // Directory to store job files

        echo "Starting queue worker... Press Ctrl+C to stop.\n";

        while (true) {
            $queue->processNextJob();
            sleep(1); // Wait for 1 second before checking for new jobs
        }
    }
}
