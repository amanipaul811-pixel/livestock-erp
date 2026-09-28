<?php

namespace App\Concerns;

trait GeneratesSequentialCode
{
    // Counts existing rows already carrying this prefix and returns the next
    // number after it, so codes stay dense and readable (PO-2026-0001, ...)
    // instead of the random-looking values a uniqid()/typed-in code produces.
    protected static function nextCodeWithPrefix(string $column, string $prefix, int $padLength): string
    {
        $count = static::where($column, 'like', $prefix.'%')->count();

        return $prefix.str_pad((string) ($count + 1), $padLength, '0', STR_PAD_LEFT);
    }
}
