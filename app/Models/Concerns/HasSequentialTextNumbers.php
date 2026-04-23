<?php

namespace App\Models\Concerns;

trait HasSequentialTextNumbers
{
    public static function nextSequentialNumberFor(string $column): int
    {
        foreach (
            static::query()
                ->whereNotNull($column)
                ->orderByDesc((new static())->getKeyName())
                ->select([$column])
                ->cursor() as $record
        ) {
            $number = static::extractSequentialNumber($record->{$column});

            if ($number !== null) {
                return $number + 1;
            }
        }

        return 1;
    }

    protected static function extractSequentialNumber(mixed $value): ?int
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (preg_match('/(\d+)(?!.*\d)/', $value, $matches) !== 1) {
            return null;
        }

        return (int) $matches[1];
    }
}
