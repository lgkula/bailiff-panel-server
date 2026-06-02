<?php

declare(strict_types=1);

use BailiffPanel\Services\BailiffRepository;

require __DIR__ . '/../vendor/autoload.php';

$directory = sys_get_temp_dir() . '/bailiff-panel-' . uniqid('', true);
mkdir($directory);

try {
    file_put_contents($directory . '/seed.json', json_encode([
        [
            'id' => 'bailiff-001',
            'firstName' => 'Jan',
            'lastName' => 'Kowalski',
            'courtName' => 'Sąd Rejonowy',
            'officeAddress' => 'ul. Testowa 1',
            'email' => 'jan@example.pl',
            'phone' => '+48 501 234 567',
            'status' => 'active',
        ],
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    $repository = new BailiffRepository($directory . '/data.json', $directory . '/seed.json');

    assertCountValue(1, $repository->findAll());
    assertCountValue(1, $repository->findAll('kowalski'));
    assertCountValue(0, $repository->findAll('missing'));

    $created = $repository->create([
        'firstName' => 'Anna',
        'lastName' => 'Nowak',
        'courtName' => 'Sąd Rejonowy w Krakowie',
        'officeAddress' => 'ul. Karmelicka 12',
        'email' => 'anna@example.pl',
        'phone' => '+48 502 345 678',
        'status' => 'active',
    ]);

    assertSame('bailiff-002', $created['id']);

    $updated = $repository->update($created['id'], [
        'firstName' => 'Anna',
        'lastName' => 'Nowak',
        'courtName' => 'Sąd Rejonowy w Krakowie',
        'officeAddress' => 'ul. Karmelicka 12',
        'email' => 'anna@example.pl',
        'phone' => '+48 502 345 678',
        'status' => 'suspended',
    ]);

    assertSame('suspended', $updated['status'] ?? null);
    echo "BailiffRepositoryTest passed.\n";
} finally {
    foreach (glob($directory . '/*') ?: [] as $file) {
        unlink($file);
    }

    rmdir($directory);
}

function assertSame(mixed $expected, mixed $actual): void
{
    if ($expected !== $actual) {
        throw new RuntimeException(sprintf('Expected %s, got %s.', var_export($expected, true), var_export($actual, true)));
    }
}

function assertCountValue(int $expected, array $actual): void
{
    assertSame($expected, count($actual));
}
