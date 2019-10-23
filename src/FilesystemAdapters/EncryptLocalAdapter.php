<?php
namespace Junichimura\LaravelEncryptStorageDriver\FilesystemAdapters;

use Illuminate\Encryption\Encrypter;
use Junichimura\LaravelEncryptStorageDriver\StorageEncrypterProvider;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Config;

class EncryptLocalAdapter extends Local
{

    /**
     * @var Encrypter
     */
    private $encrypter;

    public function __construct($root, $writeFlags = LOCK_EX, $linkHandling = self::DISALLOW_LINKS, array $permissions = [])
    {
        $this->encrypter = app(StorageEncrypterProvider::APP_NAME);
        parent::__construct($root, $writeFlags, $linkHandling, $permissions);
    }

    private function encryptContents($contents)
    {
        return $this->encrypter->encryptString($contents);
    }

    public function decryptContents($contents)
    {
        return $this->encrypter->decryptString($contents);
    }

    public function read($path)
    {
        if ($return = parent::read($path)) {
            $return['contents'] = $this->decryptContents($return['contents']);
        }
        return $return;
    }

    public function write($path, $contents, Config $config)
    {
        return parent::write($path, $this->encryptContents($contents), $config);
    }

    public function writeStream($path, $resource, Config $config)
    {
        $ret = parent::writeStream($path, $resource, $config);
        if ($ret) {
            $fileDetail = parent::read($path);
            $ret = $this->write($path, $fileDetail['contents'], $config);
        }
        return $ret;
    }

    public function update($path, $contents, Config $config)
    {
        return parent::update($path, $this->encryptContents($contents), $config);
    }

}
