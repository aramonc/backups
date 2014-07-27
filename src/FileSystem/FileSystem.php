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

    public function updateStream($path, $file)
    {
        $path = Util::normalizePath($path);
        $this->assertPresent($path);

        if (!($file instanceof \SplFileInfo)) {
            throw new InvalidArgumentException(__METHOD__ . ' expects argument #2 to be an SplFileInfo object.');
        }

        if ( ! $object = $this->adapter->updateStream($path, $file->getRealPath())) {
            return false;
        }

        $this->cache->updateObject($path, $object, true);
        $this->cache->ensureParentDirectories($path);

        return true;
    }

    public function putStream($path, $resource, $config = null)
    {
        $path = Util::normalizePath($path);

        if ($this->has($path)) {
            return $this->updateStream($path, $resource);
        }

        return $this->writeStream($path, $resource, $config);
    }
} 