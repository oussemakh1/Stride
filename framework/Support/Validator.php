<?php

namespace Framework\Support;

use Framework\Database\Connection;

class Validator
{
    private $data;
    private $errors = [];
    private $customMessages = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function setMessages(array $messages): void
    {
        $this->customMessages = $messages;
    }

    public function validate(array $rules): array
    {
        foreach ($rules as $field => $fieldRules) {
            $value = $this->data[$field] ?? null;

            foreach (explode('|', $fieldRules) as $rule) {
                $ruleParts = explode(':', $rule, 2);
                $ruleName = $ruleParts[0];
                $param = $ruleParts[1] ?? null;

                $methodName = lcfirst(str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $ruleName))));

                if (method_exists($this, $methodName)) {
                    $this->$methodName($field, $value, $param);
                } else {
                    throw new \Exception("Validation rule '{$ruleName}' not found.");
                }
            }
        }

        return $this->errors;
    }

    private function addError(string $field, string $rule, ?string $param = null): void
    {
        $message = $this->customMessages["{$field}.{$rule}"]
            ?? $this->customMessages[$rule]
            ?? $this->getDefaultMessage($field, $rule, $param);

        if (!isset($this->errors[$field])) {
            $this->errors[$field] = [];
        }
        $this->errors[$field][] = $message;
    }

    private function getDefaultMessage(string $field, string $rule, ?string $param = null): string
    {
        switch ($rule) {
            case 'required':
                return "The {$field} field is required.";
            case 'email':
                return "The {$field} must be a valid email address.";
            case 'min':
                return "The {$field} must be at least {$param} characters.";
            case 'max':
                return "The {$field} may not be greater than {$param} characters.";
            case 'unique':
                return "The {$field} has already been taken.";
            case 'exists':
                return "The selected {$field} is invalid.";
            case 'min_length':
                return "The {$field} must be at least {$param} characters long.";
            case 'max_length':
                return "The {$field} may not be greater than {$param} characters long.";
            case 'numeric':
                return "The {$field} must be a number.";
            case 'alpha':
                return "The {$field} may only contain letters.";
            case 'alpha_dash':
                return "The {$field} may only contain letters, numbers, dashes and underscores.";
            case 'confirmed':
                return "The {$field} confirmation does not match.";
            default:
                return "The {$field} is invalid.";
        }
    }

    // --- Validation Rules ---

    private function required(string $field, $value): void
    {
        if (empty($value) && $value !== '0') {
            $this->addError($field, 'required');
        }
    }

    private function email(string $field, $value): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->addError($field, 'email');
        }
    }

    private function min(string $field, $value, string $min): void
    {
        if (strlen((string)$value) < (int)$min) {
            $this->addError($field, 'min', $min);
        }
    }

    private function max(string $field, $value, string $max): void
    {
        if (strlen((string)$value) > (int)$max) {
            $this->addError($field, 'max', $max);
        }
    }

    private function unique(string $field, $value, string $param): void
    {
        list($table, $column) = explode(',', $param);
        $column = $column ?? $field;

        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM {$table} WHERE {$column} = :value");
        $stmt->execute(['value' => $value]);

        if ($stmt->fetchColumn() > 0) {
            $this->addError($field, 'unique');
        }
    }

    private function exists(string $field, $value, string $param): void
    {
        list($table, $column) = explode(',', $param);
        $column = $column ?? $field;

        $pdo = Connection::getInstance();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM {$table} WHERE {$column} = :value");
        $stmt->execute(['value' => $value]);

        if ($stmt->fetchColumn() === 0) {
            $this->addError($field, 'exists');
        }
    }

    private function minLength(string $field, $value, string $min): void
    {
        if (strlen((string)$value) < (int)$min) {
            $this->addError($field, 'min_length', $min);
        }
    }

    private function maxLength(string $field, $value, string $max): void
    {
        if (strlen((string)$value) > (int)$max) {
            $this->addError($field, 'max_length', $max);
        }
    }

    private function numeric(string $field, $value): void
    {
        if (!is_numeric($value)) {
            $this->addError($field, 'numeric');
        }
    }

    private function alpha(string $field, $value): void
    {
        if (!preg_match('/^[a-zA-Z]+$/', (string)$value)) {
            $this->addError($field, 'alpha');
        }
    }

    private function alphaDash(string $field, $value): void
    {
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', (string)$value)) {
            $this->addError($field, 'alpha_dash');
        }
    }

    private function confirmed(string $field, $value): void
    {
        $confirmationField = $field . '_confirmation';
        if (!isset($this->data[$confirmationField]) || $value !== $this->data[$confirmationField]) {
            $this->addError($field, 'confirmed');
        }
    }

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}