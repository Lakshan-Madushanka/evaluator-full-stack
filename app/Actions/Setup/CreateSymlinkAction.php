<?php

namespace App\Actions\Setup;

use Illuminate\Support\Facades\Artisan;

class CreateSymlinkAction
{
    public function execute(): bool
    {
        try {
            $code = Artisan::call('storage:link');
        }catch (\Exception $exception){
            return false;
        }

        if ($code !== 0) {
            return false;
        }

        return true;
    }
}
