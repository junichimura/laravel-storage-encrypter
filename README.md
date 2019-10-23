# Laravel Storage Encrypter

## Install

#### Composer
* For Laravel: run `composer require junichimura/laravel-encrypt-storage-driver` in your project folder.

#### After Composer require
* `php artisan vendor:publish --tag=junichimura-encrypt_storage`


## After Installation

#### file: config/filesystem.php
```
<?php
return [

    'disks' => [

        'encrypt_local' => [
            'driver' => 'encrypt_local', // <- driver type enctypt_local
            'root' => storage_path('app'),
        ],
        
];
```

## Example

#### PUT

```
$plainContents = 'content';
\Storage::driver('encrypt_local')->put('/store/path', $plainContents)
```

#### GET

```
$plainContents = \Storage::driver('encrypt_local')->get('/store/path');
```

#### FILE UPLOAD

```
$file = request()->file('fileName');
$file->store('/store/path', 'encrypt_local');
// or
$file->storeAs('store/path', 'filename.extention', 'encrypt_local')
```