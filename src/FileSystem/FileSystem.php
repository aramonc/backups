<?php

namespace Ils\FileSystem;


use InvalidArgumentException;
use League\Flysystem\Filesystem as Fs;
use League\Flysystem\Util;

class FileSystem extends Fs
{
    public function writeStream($path, $file, $config = null)
    {
        $path = Util::normalizePath($path);
        $this->assertAbsent($path);
        $config = Util::ensureConfig($config);
        $config->setFallback($this->getConfig());

        if (!($file instanceof \SplFileInfo)) {
            throw new InvalidArgumentException(__METHOD__ . ' expects argument #2 to be an SplFileInfo object.');
        }

        if (!$object = $this->adapter->writeStream($path, $file->getRealPath(), $config)) {
            return false;
        }

        $this->cache->updateObject($path, $object, true);
        $this->cache->ensureParentDirectories($path);

        return true;
    }
} 