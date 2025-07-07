<?php

namespace Framework\Core;

use Framework\Core\Controller;

class ApiController extends Controller
{
    protected function json($data, int $status = 200, array $headers = []): void
    {
        header('Content-Type: application/json');
        foreach ($headers as $key => $value) {
            header("$key: $value");
        }
        http_response_code($status);
        echo json_encode($data);
        exit;
    }
}
