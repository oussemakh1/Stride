<?php

namespace Framework\Http;

class Request
{
    private $params;

    public function __construct()
    {
        $this->params = $this->sanitize($_REQUEST); // Sanitize all request parameters
    }

    public function get($key, $default = null)
    {
        return $this->params[$key] ?? $default;
    }

    public function set($key, $value)
    {
        $this->params[$key] = $value;
    }

    public function all(): array
    {
        return $this->params;
    }

    public function except(array $keys): array
    {
        $params = $this->params;

        foreach ($keys as $key) {
            unset($params[$key]);
        }
        return $params;
    }

    private function sanitize($input)
    {
        if (is_array($input)) {
            foreach ($input as $key => $value) {
                $input[$key] = $this->sanitize($value);
            }
            return $input;
        } elseif (is_string($input)) {
            return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
        } else {
            return $input;
        }
    }
}