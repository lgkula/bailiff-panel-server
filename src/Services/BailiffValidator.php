<?php

declare(strict_types=1);

namespace BailiffPanel\Services;

use BailiffPanel\Models\Bailiff;

final class BailiffValidator
{
    public function validate(array $payload): array
    {
        $errors = [];

        $this->validateText($payload, $errors, 'firstName', 2, 50);
        $this->validateText($payload, $errors, 'lastName', 2, 80);
        $this->validateText($payload, $errors, 'courtName', 3, 120);
        $this->validateText($payload, $errors, 'officeAddress', 5, 200);

        if (!isset($payload['email']) || trim((string) $payload['email']) === '') {
            $errors['email'] = 'Field is required.';
        } elseif (!filter_var(trim((string) $payload['email']), FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Invalid email format.';
        }

        if (!isset($payload['phone']) || trim((string) $payload['phone']) === '') {
            $errors['phone'] = 'Field is required.';
        } elseif (!preg_match('/^\+?[0-9][0-9\s-]{6,20}$/', trim((string) $payload['phone']))) {
            $errors['phone'] = 'Invalid phone format.';
        }

        if (!isset($payload['status']) || trim((string) $payload['status']) === '') {
            $errors['status'] = 'Field is required.';
        } elseif (!in_array(trim((string) $payload['status']), Bailiff::STATUSES, true)) {
            $errors['status'] = 'Status must be one of: active, suspended, inactive.';
        }

        return $errors;
    }

    public function sanitize(array $payload): array
    {
        return [
            'firstName' => trim((string) $payload['firstName']),
            'lastName' => trim((string) $payload['lastName']),
            'courtName' => trim((string) $payload['courtName']),
            'officeAddress' => trim((string) $payload['officeAddress']),
            'email' => trim((string) $payload['email']),
            'phone' => trim((string) $payload['phone']),
            'status' => trim((string) $payload['status']),
        ];
    }

    private function validateText(array $payload, array &$errors, string $field, int $min, int $max): void
    {
        if (!isset($payload[$field]) || trim((string) $payload[$field]) === '') {
            $errors[$field] = 'Field is required.';
            return;
        }

        $length = mb_strlen(trim((string) $payload[$field]));

        if ($length < $min || $length > $max) {
            $errors[$field] = sprintf('Field must contain between %d and %d characters.', $min, $max);
        }
    }
}
