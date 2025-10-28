<?php

return [
    /**
     * PHP Requirements
     */
    'php_requirements' => [
        'min_version' => '8.0.0',
        'extensions' => [
            'bcmath',
            'ctype',
            'fileinfo',
            'json',
            'mbstring',
            'openssl',
            'cURL',
            'dom',
            'filter',
            'hash',
            'openssl',
            // 'pcre ',
            'pdo',
            'session',
            'tokenizer',
            'xml',
        ],
    ],

    /**
     * File Permissions
     */
    'file_permissions' => [
        'storage/framework/' => '775',
        'storage/logs/' => '775',
        'bootstrap/cache/' => '775',
    ],
];
