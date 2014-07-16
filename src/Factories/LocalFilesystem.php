<?php
namespace Ils\Factories;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LocalFilesystem implements FactoryInterface
{

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var Local $fsAdapter */
        $fsAdapter = $serviceLocator->get('FsAdapterLocal');
        return new Filesystem($fsAdapter);
    }
}