<?php

namespace Ils\Exception;


class ConfigFileNotFound extends \Exception
{
    public function __construct($path = null)
    {
        $message = sprintf("The given path (%s) is not a file.", $path);
        parent::__construct($message, '40401');
    }
} 