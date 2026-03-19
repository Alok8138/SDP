<?php

namespace App\Helpers;

class Validator
{
    private array $errors = [];

    public function required(mixed $value, string $field): self
    {
        if ($value === null || $value === '') {
            $this->errors[] = "{$field} is required";
        }
        return $this;
    }

    public function string(mixed $value, string $field): self
    {
        if ($value !== null && !is_string($value)) {
            $this->errors[] = "{$field} must be a string";
        }
        return $this;
    }

    public function numeric(mixed $value, string $field): self
    {
        if ($value !== null && !is_numeric($value)) {
            $this->errors[] = "{$field} must be numeric";
        }
        return $this;
    }

    public function inArray(mixed $value, array $allowed, string $field): self
    {
        if ($value !== null && !in_array($value, $allowed, true)) {
            $this->errors[] = "{$field} must be one of: " . implode(', ', $allowed);
        }
        return $this;
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
