<?php
/**
 * Define Object dependencies
 */

//return array(
//    'yaml_parser' => new \Ils\Config\YamlParser(),
//    'fs_adapter_local' => new \League\Flysystem\Adapter\Local('/'),
//    'fs_local' => new \Ils\Factories\LocalFilesystem(),
//    'config' => new \Ils\Factories\ConfigService()
//);

return array(
    'factories' => array(
        "\\League\\Flysystem\\Filesystem" => "\\Ils\\Factories\\LocalFilesystem",
        '\\Zend\\Config\\Reader\\Yaml' => "\\Ils\\Factories\\YamlParser",
    ),
    'services' => array(
        '\\Ils\\Component\\MySQLDump' => new \Ils\Component\MySQLDump(),
        '\\League\\Flysystem\\Adapter\\Local' => new \League\Flysystem\Adapter\Local('/'),
        '\\Zend\\Config\\Reader\\Ini' => new \Zend\Config\Reader\Ini(),
        '\\Zend\\Config\\Reader\\Xml' => new \Zend\Config\Reader\Xml(),
        '\\Zend\\Config\\Reader\\Json' => new \Zend\Config\Reader\Json,
    ),
    'aliases' => array(
        'FsAdapterLocal' => "\\League\\Flysystem\\Adapter\\Local",
        'FsLocal' => "\\League\\Flysystem\\Filesystem",
        'YamlParser' => "\\Zend\\Config\\Reader\\Yaml",
        'IniParser' => "\\Zend\\Config\\Reader\\Ini",
        'XmlParser' => "\\Zend\\Config\\Reader\\Xml",
        'JsonParser' => "\\Zend\\Config\\Reader\\Json",
        'mysql' => "\\Ils\\Component\\MySQLDump",
    ),
);