<?php

namespace HumusSupervisorModuleTest;

use HumusSupervisorModule\SupervisorAbstractServiceFactory;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\ServiceManager\ServiceManager;

class SupervisorAbstractServiceFactoryTest extends TestCase
{
    /**
     * @var ServiceManager
     */
    protected $services;

    /**
     * @var SupervisorAbstractServiceFactory
     */
    protected $components;

    public function setUp()
    {
        $config = array(
            'humus_supervisor_module' => array(
                'test-supervisor' => array(
                    'host' => 'localhost',
                    'username' => 'user',
                    'password' => '123'
                ),
                'test-supervisor-2' => array(
                    'host' => 'localhost',
                    'username' => 'user',
                    'password' => '123'
                )
            )
        );

        $services    = $this->services = new ServiceManager();
        $services->setAllowOverride(true);

        $services->setService('Config', $config);

        $compontents = $this->components = new SupervisorAbstractServiceFactory();
        $services->addAbstractFactory($compontents);
    }

    public function testMissingConfigServiceIndicatesCannotCreateInstance()
    {
        $this->assertFalse($this->components->canCreateServiceWithName($this->services, 'foo', 'foo'));
    }

    public function testMissingSupervisorServicePrefixIndicatesCannotCreateInstance()
    {
        $this->services->setService('Config', array());
        $this->assertFalse($this->components->canCreateServiceWithName($this->services, 'foo', 'foo'));
    }

    public function testInvalidConfigIndicatesCannotCreateInstance()
    {
        $this->services->setService('Config', array('humus_amqp_module' => 'string'));
        $this->assertFalse($this->components->canCreateServiceWithName($this->services, 'foo', 'foo'));
    }

    public function testEmptySupervisorConfigIndicatesCannotCreateConsumer()
    {
        $this->services->setService('Config', array('humus_amqp_module' => array()));
        $this->assertFalse(
            $this->components->canCreateServiceWithName($this->services, 'test-consumer', 'test-consumer')
        );
    }

    public function testSupervisorFactory()
    {
        $supervisor = $this->components->createServiceWithName(
            $this->services,
            'test-supervisor',
            'test-supervisor'
        );
        $this->assertInstanceOf('Indigo\Supervisor\Supervisor', $supervisor);

        $this->assertTrue(
            $this->components->canCreateServiceWithName(
                $this->services,
                'test-supervisor',
                'test-supervisor'
            )
        );

        $supervisor2 = $this->components->createServiceWithName(
            $this->services,
            'test-supervisor',
            'test-supervisor'
        );

        $this->assertSame($supervisor, $supervisor2);
    }

}
