<?php

namespace HumusSupervisorModule;

use GuzzleHttp\Client;
use Indigo\Supervisor\Connector\GuzzleConnector;
use Indigo\Supervisor\Supervisor;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements AutoloaderProviderInterface, BootstrapListenerInterface, ConfigProviderInterface
{
    /**
     * Get config
     *
     * @return array|mixed|\Traversable
     */
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
            $serviceManager->setFactory($name, function() use ($connectionSettings) {
                $port = isset($connectionSettings['port']) ? ':' . $connectionSettings['port'] : '';

                $connector = new GuzzleConnector(new Client(array(
                    'base_url' => 'http://' . $connectionSettings['host'] . $port
                )));
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
