<?php

namespace HumusSupervisorModule\Service;

use HumusSupervisorModule\Controller\SupervisorController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class SupervisorControllerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sm = $serviceLocator->getServiceLocator();

        $pluginManager = $sm->get('HumusSupervisorModule\SupervisorManager');

        $controller = new SupervisorController();
        $controller->setSupervisorPluginManager($pluginManager);

        return $controller;
    }
}
