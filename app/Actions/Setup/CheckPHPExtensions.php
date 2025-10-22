<?php

namespace App\Actions\Setup;

class CheckPHPExtensions
{
    public function execute(): array
    {
        $extensions = config('setup.php_requirements.extensions');

        $status = [];

        foreach ($extensions as $extension) {
            $status[$extension] = extension_loaded($extension);
        }

        return $status;
    }
}
