<?php

namespace App\Actions\Setup;

use Illuminate\Support\Facades\Artisan;

class OptimizeAction
{
    public function execute(): bool
    {
        $code = Artisan::call('optimize');

        if ($code !== 0) {
            return false;
        }

        return  true;
    }
}
