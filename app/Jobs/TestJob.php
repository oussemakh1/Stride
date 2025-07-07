<?php

namespace App\Jobs;

class TestJob
{
    public function handle(array $data)
    {
        $logFile = __DIR__ . '/../../storage/logs/queue.log';
        $message = 'TestJob processed at ' . date('Y-m-d H:i:s') . ' with data: ' . json_encode($data) . "\n";
        file_put_contents($logFile, $message, FILE_APPEND);
    }
}

