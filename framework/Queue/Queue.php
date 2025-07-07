<?php

namespace Framework\Queue;

class Queue
{
    protected $queuePath;

    public function __construct(string $queuePath)
    {
        $this->queuePath = rtrim($queuePath, '/') . '/';
        if (!is_dir($this->queuePath)) {
            mkdir($this->queuePath, 0777, true);
        }
    }

    public function push(string $jobClass, array $data = []): void
    {
        $jobPayload = [
            'job' => $jobClass,
            'data' => $data,
            'queued_at' => time(),
        ];
        $filename = uniqid('job_') . '.json';
        file_put_contents($this->queuePath . $filename, json_encode($jobPayload));
    }

    public function pop(): ?array
    {
        $files = glob($this->queuePath . '*.json');
        if (empty($files)) {
            return null;
        }

        // Get the oldest job (simple FIFO)
        $oldestFile = null;
        $oldestTime = PHP_INT_MAX;

        foreach ($files as $file) {
            $fileTime = filemtime($file);
            if ($fileTime < $oldestTime) {
                $oldestTime = $fileTime;
                $oldestFile = $file;
            }
        }

        if ($oldestFile) {
            $payload = json_decode(file_get_contents($oldestFile), true);
            unlink($oldestFile); // Remove job from queue
            return $payload;
        }

        return null;
    }

    public function processNextJob(): void
    {
        $job = $this->pop();
        if ($job) {
            $jobClass = $job['job'];
            $jobData = $job['data'];

            if (class_exists($jobClass)) {
                $jobInstance = new $jobClass();
                if (method_exists($jobInstance, 'handle')) {
                    $jobInstance->handle($jobData);
                    echo "Processed job: {$jobClass}\n";
                } else {
                    error_log("Job class {$jobClass} does not have a handle method.");
                }
            }
            else {
                error_log("Job class {$jobClass} not found.");
            }
        }
    }
}
