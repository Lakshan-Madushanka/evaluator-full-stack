<?php

namespace App\Actions\Setup;

use Illuminate\Support\Facades\Artisan;

class CreateSymlinkAction
{
    public function execute(): bool
    {
        $code = Artisan::call('storage:link');

        if ($code !== 0) {
            return false;
        }

        return true;
    }
}
