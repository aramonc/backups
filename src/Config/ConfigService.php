<?php
namespace Ils\Config;


class ConfigService
{

    protected $filesystem;

    public function getConfig($config)
    {
        if (is_array($config)) {
            return $config;
        }


    }
} 