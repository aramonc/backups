<?php

namespace Ils\Exception;


class ParserNotDetected extends \Exception
{
    public function __construct($path = null)
    {
        $message = sprintf("Could not detect which parser to use from given path (%s). Please make sure the path includes a config file extension", $path);
        parent::__construct($message, '40402');
    }
} 