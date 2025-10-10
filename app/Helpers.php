<?php

namespace App;

use Vinkla\Hashids\Facades\Hashids;

class Helpers
{
    /**
     * @param  string[]  $hashIds
     * @return int[]
     */
    public static function getModelIdsFromHashIds(array $hashIds): array
    {
        $modelIds = [];

        foreach ($hashIds as $id) {
            $modelIds[] = Hashids::decode($id)[0];
        }

        return $modelIds;
    }

    /**
     * @param  int[]  $modelIds
     * @return string[]
     */
    public static function getHashIdsFromModelIds(array $modelIds): array
    {
        $hashIds = [];

        foreach ($modelIds as $id) {
            $hashIds[] = Hashids::encode($id);
        }

        return $hashIds;
    }

    public static function checkValueIsTrue(mixed $value): mixed
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
