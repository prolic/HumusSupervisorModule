<?php

namespace HumusSupervisorModule;

use ArrayAccess;
use GuzzleHttp\Client;
use Indigo\Supervisor\Connector\GuzzleConnector;
use Indigo\Supervisor\Supervisor;
use Traversable;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\ArrayUtils;

class SupervisorAbstractServiceFactory implements AbstractFactoryInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var string Top-level configuration key indicating supervisor configuration
     */
    protected $configKey = 'humus_supervisor_module';

    /**
     * @var array
     */
    protected $instances = array();

    /**
     * Determine if we can create a service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if (isset($this->instances[$requestedName])) {
            return true;
        }

        $config = $this->getConfig($serviceLocator);
        if (empty($config)) {
            return false;
        }

        return (isset($config[$requestedName]) && is_array($config[$requestedName]) && !empty($config[$requestedName]));
    }

    /**
     * Create service with name
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return mixed
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        if (isset($this->instances[$requestedName])) {
            return $this->instances[$requestedName];
        }

        /* @var $serviceLocator \Zend\ServiceManager\ServiceManager */
        $config  = $this->getConfig($serviceLocator);

        $params = $config[$requestedName];

        $port = isset($params['port']) ? ':' . $params['port'] : '';


        $connector = new GuzzleConnector(new Client(array(
            'base_url' => 'http://' . $params['host'] . $port
        )));


        /*
        $connector = new ZendConnector(new \Zend\XmlRpc\Client($connectionSettings['host'] . $port));
        */
        $connector->setCredentials($params['username'], $params['password']);

        $this->instances[$requestedName] = $supervisor = new Supervisor($connector);

        return $supervisor;
    }

    /**
     * Get amqp configuration, if any
     *
     * @param  ServiceLocatorInterface $services
     * @return array
     */
    protected function getConfig(ServiceLocatorInterface $services)
    {
        if ($this->config !== null) {
            return $this->config;
        }

        if (!$services->has('Config')) {
            $this->config = array();
            return $this->config;
        }

        $config = $services->get('Config');
        if (!isset($config[$this->configKey])
            || !is_array($config[$this->configKey])
        ) {
            $this->config = array();
            return $this->config;
        }

        $this->config = $config[$this->configKey];
        return $this->config;
    }
}
