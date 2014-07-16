<?php

namespace HumusSupervisorModule;

use Indigo\Supervisor\Connector\ZendConnector;
use Indigo\Supervisor\Supervisor;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\XmlRpc\Client;

class Module implements AutoloaderProviderInterface, BootstrapListenerInterface, ConfigProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    /**
     * Bootstrap the module / build supervisors
     *
     * @param EventInterface $e
     * @return array
     */
    public function onBootstrap(EventInterface $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();

        $config = $serviceManager->get('Config');
        $moduleConfig = $config['humus_supervisor_module'];

        foreach ($moduleConfig as $name => $connectionSettings) {
            $serviceManager->setFactory($name, function($sm) use ($connectionSettings) {
                $port = isset($connectionSettings['port']) ? ':' . $connectionSettings['port'] : '';
                $client = new Client($connectionSettings['host'] . $port);
                $connector = new ZendConnector($client);
                $connector->setCredentials($connectionSettings['username'], $connectionSettings['password']);

                $supervisor = new Supervisor($connector);
                return $supervisor;
            });
        }
    }

    /**
     * Return an array for passing to Zend\Loader\AutoloaderFactory.
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/../../autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }
}
