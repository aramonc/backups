<?php

namespace Ils\FileSystem;

use League\Flysystem\Adapter\Sftp;
use League\Flysystem\Util;

class SftpAdapter extends Sftp
{

    public function writeStream($path, $contents, $config = null)
    {
        return $this->uploadStream($path, $contents, $config);
    }

    public function updateStream($path, $contents, $config = null)
    {
        return $this->uploadStream($path, $contents, $config);
    }

    protected function uploadStream($path, $contents, $config = null)
    {
        $connection = $this->getConnection();
        $this->ensureDirectory(Util::dirname($path));
        $config = Util::ensureConfig($config);

        if ( ! $connection->put($path, $contents, NET_SFTP_LOCAL_FILE)) {
            return false;
        }

        if ($config && $visibility = $config->get('visibility')) {
            $this->setVisibility($path, $visibility);
        }

        return compact('contents', 'visibility');
    }
}
