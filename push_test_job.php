<?php

require_once __DIR__ . '/vendor/autoload.php';

use Framework\Queue\Queue;
use App\Jobs\TestJob;

$queue = new Queue(dirname(__DIR__) . '/queue_jobs');

$queue->push(TestJob::class, ['message' => 'Hello from TestJob!']);

echo "TestJob pushed to queue.\n";

