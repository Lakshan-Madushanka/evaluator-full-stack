<?php

namespace App\Services;

use Illuminate\Support\Collection;

class PrettyIdGenerator
{
    private static $prettyIds;

    private static $nextId;

    private function init(string $table)
    {
        self::$prettyIds = self::getPrettyIds($table);
        self::$nextId = self::getLastInsertedId($table) + 1;
    }

    public static function generate(string $table, string $prefix, int $length = 12): string
    {
        (new self)->init($table);

        $nextId = self::getLastInsertedId($table) + 1;

        $prettyId = self::generatePrettyId($prefix, $length);

        while (self::checkPrettyIdExists($prettyId, 'answers')) {
            $prettyId = self::generatePrettyId($prefix, $length);
        }

        return $prettyId;
    }

    public static function getLastInsertedId(string $table): int
    {
        $data = \DB::table($table)->orderByDesc('id')->select('id')->first();

        return ! is_null($data) ? $data->id : 0;
    }

    public static function generatePrettyId(string $prefix, int $length): string
    {
        $randomNum = random_int(10, 1000);

        $suffix = (string) ($randomNum + self::$nextId);

        $remainingLength = $length - strlen($suffix.$prefix);

        $suffix = str_repeat('0', $remainingLength).$suffix;

        return $prefix.$suffix;
    }

    private static function checkPrettyIdExists(string $id, string $table): bool
    {
        return self::$prettyIds->contains(fn (string $prettyId) => $id === $prettyId);
    }

    public static function getPrettyIds(string $table): Collection
    {
        return \DB::table($table)->pluck('pretty_id');
    }
}
