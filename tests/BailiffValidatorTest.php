<?php

declare(strict_types=1);

use BailiffPanel\Services\BailiffValidator;

require __DIR__ . '/../vendor/autoload.php';

$validator = new BailiffValidator();

assertSame([], $validator->validate([
    'firstName' => 'Anna',
    'lastName' => 'Nowak',
    'courtName' => 'Sąd Rejonowy w Krakowie',
    'officeAddress' => 'ul. Karmelicka 12, 31-128 Kraków',
    'email' => 'anna.nowak@example.pl',
    'phone' => '+48 502 345 678',
    'status' => 'active',
]));

$errors = $validator->validate([
    'firstName' => '',
    'lastName' => 'N',
    'courtName' => 'SR',
    'officeAddress' => 'x',
    'email' => 'wrong',
    'phone' => 'abc',
    'status' => 'archived',
]);

foreach (['firstName', 'lastName', 'courtName', 'officeAddress', 'email', 'phone', 'status'] as $field) {
    assertTrue(array_key_exists($field, $errors), sprintf('Expected %s validation error.', $field));
}

echo "BailiffValidatorTest passed.\n";

function assertSame(mixed $expected, mixed $actual): void
{
    if ($expected !== $actual) {
        throw new RuntimeException('Assertion failed: values are not identical.');
    }
}

function assertTrue(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}
