<?php

namespace App\Actions\Setup;

use Illuminate\Database\SQLiteConnection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class CheckDBAction
{
    public function execute(): int
    {
        return Artisan::call('migrate', ['--force' => true]);
    }

    public function getInfo(): array
    {
        return [
            'connection' => DB::connection()->getName(),
            'name' => DB::connection()->getDatabaseName(),
            'config' => DB::connection()->getConfig(),
        ];
    }

    public function checkConnection(): array
    {
        $info = [
            'status' => '',
            'has_database_created' => false,
            'errors' => null,
        ];

        if (DB::connection() instanceof SQLiteConnection) {
            if (! $this->createSqliteDB()) {
                $info['has_database_created'] = false;
                $info['status'] = 'fail';

                return $info;
            } else {
                $info['has_database_created'] = true;
            }
        }

        try {
            DB::connection()->getPDO();
            $info['has_database_created'] = true;
            $info['status'] = 'success';
        } catch (\Exception $e1) {
            try {
                $info['has_database_created'] = $this->createMySqlDB();
                $info['status'] = 'success';
            }catch (\Exception $e2) {
                $info['status'] = 'fail';
                $info['errors'] = $e2->getMessage();
            }
        }

        return $info;
    }

    public function createSqliteDB(): bool
    {
        $database = DB::connection()->getDatabaseName();

        if (! file_exists($database)) {
            return touch(base_path($database));
        }

        return true;
    }

    public function createMySqlDB(): bool
    {
        $database = DB::getConfig()['database'];

        $database = preg_replace('/[^A-Za-z0-9_]+/', '', $database);

        $sql = "CREATE DATABASE `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";

        return DB::statement($sql);
    }
}
