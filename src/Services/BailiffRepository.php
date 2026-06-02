<?php

declare(strict_types=1);

namespace BailiffPanel\Services;

final class BailiffRepository
{
    public function __construct(
        private readonly string $dataPath,
        private readonly string $seedPath,
    ) {
        $this->ensureDataFile();
    }

    public function findAll(?string $search = null): array
    {
        $bailiffs = $this->read();

        if ($search === null) {
            return $bailiffs;
        }

        $needle = mb_strtolower($search);

        return array_values(array_filter($bailiffs, static function (array $bailiff) use ($needle): bool {
            foreach (['firstName', 'lastName', 'courtName', 'officeAddress', 'email', 'phone', 'status'] as $field) {
                if (str_contains(mb_strtolower((string) ($bailiff[$field] ?? '')), $needle)) {
                    return true;
                }
            }

            return false;
        }));
    }

    public function findById(string $id): ?array
    {
        foreach ($this->read() as $bailiff) {
            if (($bailiff['id'] ?? null) === $id) {
                return $bailiff;
            }
        }

        return null;
    }

    public function create(array $payload): array
    {
        $bailiffs = $this->read();
        $payload['id'] = $this->nextId($bailiffs);
        $bailiff = $this->ordered($payload);
        $bailiffs[] = $bailiff;
        $this->write($bailiffs);

        return $bailiff;
    }

    public function update(string $id, array $payload): ?array
    {
        $bailiffs = $this->read();

        foreach ($bailiffs as $index => $bailiff) {
            if (($bailiff['id'] ?? null) === $id) {
                $payload['id'] = $id;
                $updated = $this->ordered($payload);
                $bailiffs[$index] = $updated;
                $this->write($bailiffs);

                return $updated;
            }
        }

        return null;
    }

    private function ensureDataFile(): void
    {
        if (is_file($this->dataPath)) {
            return;
        }

        $directory = dirname($this->dataPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $seed = is_file($this->seedPath) ? file_get_contents($this->seedPath) : '[]';
        file_put_contents($this->dataPath, $seed === false ? '[]' : $seed, LOCK_EX);
    }

    private function read(): array
    {
        $this->ensureDataFile();
        $contents = file_get_contents($this->dataPath);
        $decoded = json_decode($contents === false ? '[]' : $contents, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function write(array $bailiffs): void
    {
        $json = json_encode($bailiffs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        if ($json === false) {
            throw new \RuntimeException('Unable to encode bailiff data.');
        }

        file_put_contents($this->dataPath, $json . PHP_EOL, LOCK_EX);
    }

    private function nextId(array $bailiffs): string
    {
        $max = 0;

        foreach ($bailiffs as $bailiff) {
            if (preg_match('/^bailiff-(\d+)$/', (string) ($bailiff['id'] ?? ''), $matches) === 1) {
                $max = max($max, (int) $matches[1]);
            }
        }

        return sprintf('bailiff-%03d', $max + 1);
    }

    private function ordered(array $payload): array
    {
        return [
            'id' => (string) $payload['id'],
            'firstName' => (string) $payload['firstName'],
            'lastName' => (string) $payload['lastName'],
            'courtName' => (string) $payload['courtName'],
            'officeAddress' => (string) $payload['officeAddress'],
            'email' => (string) $payload['email'],
            'phone' => (string) $payload['phone'],
            'status' => (string) $payload['status'],
        ];
    }
}
