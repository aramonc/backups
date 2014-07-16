<?php
namespace Ils\Factories;

use Zend\Config\Reader;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class YamlParser implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new Reader\Yaml(array('Spyc','YAMLLoadString'));
    }
}