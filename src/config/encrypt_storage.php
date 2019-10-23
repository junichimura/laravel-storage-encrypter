<?php
return [
    // key of encryption
    'key' => env('ENCRYPT_STORAGE_KEY', env('APP_KEY')),
    // cipher of encryption
    'cipher' => 'AES-256-CBC',
];
