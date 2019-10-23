<?php
namespace Junichimura\LaravelEncryptStorageDriver;


use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Junichimura\LaravelEncryptStorageDriver\FilesystemAdapters\EncryptLocalAdapter;
use League\Flysystem\Filesystem;
use RuntimeException;

class StorageEncrypterProvider extends ServiceProvider
{

    const APP_NAME = 'storage_encrypter';

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(static::APP_NAME, function ($app) {
            $config = $app->make('config')->get('encrypt_storage');

            $key = $this->key($config);

            // If the key starts with "base64:", we will need to decode the key before handing
            // it off to the encrypter. Keys may be base-64 encoded for presentation and we
            // want to make sure to convert them back to the raw bytes before encrypting.
            if (Str::startsWith($key, 'base64:')) {
                $key = base64_decode(substr($key, 7));
            }

            $cipher = $this->cipher($config);

            return new Encrypter($key, $cipher);
        });
    }

    /**
     * Extract the encryption key from the given configuration.
     *
     * @param  array  $config
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function key(array $config)
    {
        return tap($config['key'], function ($key) {
            if (empty($key)) {
                throw new RuntimeException(
                    'No application encryption key has been specified.'
                );
            }
        });
    }

    /**
     * Extract the encryption key from the given configuration.
     *
     * @param  array  $config
     * @return string
     *
     * @throws \RuntimeException
     */
    protected function cipher(array $config)
    {
        return tap($config['cipher'], function ($cipher) {
            if (empty($cipher)) {
                throw new RuntimeException(
                    'No application encryption cipher has been specified.'
                );
            }
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/encrypt_storage.php' => config_path('encrypt_storage.php'),
        ], 'junichimura-encrypt_storage');
        
        Storage::extend('encrypt_local', function ($app, $config) {
            return new Filesystem(new EncryptLocalAdapter($config['root']));
        });
    }

}
