<?php
namespace Weasty\Similar\Cache;

use Doctrine\Common\Cache\FilesystemCache as BaseFilesystemCache;

/**
 * Class FilesystemCache
 * @package Weasty\Similar\Cache
 */
class FilesystemCache extends BaseFilesystemCache {

    /**
     * @param string $id
     * @return string
     */
    protected function getFilename($id)
    {
        return $this->directory . DIRECTORY_SEPARATOR . md5($id) . $this->extension;
    }

    /**
     * @param string $id
     * @return bool|string
     */
    protected function doFetch($id)
    {
        $data     = '';
        $lifetime = -1;
        $filename = $this->getFilename($id);

        if ( ! is_file($filename)) {
            return false;
        }

        $resource = fopen($filename, "r");

        if (false !== ($line = fgets($resource))) {
            $lifetime = (integer) $line;
        }

        if ($lifetime !== 0 && $lifetime < time()) {
            fclose($resource);

            return false;
        }

        while (false !== ($line = fgets($resource))) {
            $data .= $line;
        }

        fclose($resource);

        return $data;

    }

    /**
     * @param string $id
     * @param string $data
     * @param int $lifeTime
     * @return bool
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {

        if ($lifeTime > 0) {
            $lifeTime = time() + $lifeTime;
        }

        $filename   = $this->getFilename($id);
        $filepath   = pathinfo($filename, PATHINFO_DIRNAME);

        if ( ! is_dir($filepath)) {
            if (false === @mkdir($filepath, 0777, true) && !is_dir($filepath)) {
                return false;
            }
        } elseif ( ! is_writable($filepath)) {
            return false;
        }

        $tmpFile = tempnam($filepath, basename($filename));

        if ((file_put_contents($tmpFile, $lifeTime . PHP_EOL . $data) !== false) && @rename($tmpFile, $filename)) {
            @chmod($filename, 0666 & ~umask());

            return true;
        }

        return false;

    }


}