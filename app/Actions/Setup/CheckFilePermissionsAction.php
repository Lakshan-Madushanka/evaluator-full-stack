<?php

namespace App\Actions\Setup;

class CheckFilePermissionsAction
{
    private array $permissions = ['permissions' => [], 'hasErrors' => null];

    public function execute(): array
    {
        $files = config('setup.file_permissions', []);

        foreach ($files as $file => $permission) {
            $currentPermission = (int) $this->getPermission($file);
            $requiredPermission = (int) $permission;

            if (! ($currentPermission >= $requiredPermission)) {
                $this->addFile($file, $currentPermission, $requiredPermission, false);
            } else {
                $this->addFile($file, $currentPermission, $requiredPermission, true);
            }
        }

        $this->permissions['hasErrors'] = ! collect($this->permissions['permissions'])->every(fn ($v) => $v['isSet']);

        return $this->permissions;
    }

    private function getPermission($folder): string|false
    {
        return substr(sprintf('%o', fileperms(base_path($folder))), -4);
    }

    private function addFile(string $folder, string $currentPermission, string $requiredPermission, $isSet): void
    {
        $this->permissions['permissions'][] = [
            'path' => $folder,
            'current_permission' => $currentPermission,
            'required_permission' => $requiredPermission,
            'isSet' => $isSet,
        ];
    }
}
