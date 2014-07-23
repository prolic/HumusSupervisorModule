<?php

namespace HumusSupervisorModuleTest;

use HumusSupervisorModule\SupervisorAbstractServiceFactory;
use HumusSupervisorModule\SupervisorManager;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\ServiceManager\ServiceManager;

class SupervisorAbstractServiceFactoryTest extends TestCase
{
    /**
     * @var ServiceManager
     */
    protected $services;

    /**
     * @var SupervisorManager
     */
    protected $supervisorManager;

    /**
     * @var SupervisorAbstractServiceFactory
     */
    protected $components;

    public function setUp()
    {
        $services = $this->services = new ServiceManager();
        $services->setAllowOverride(true);

        $services->setService('Config', array(
            'humus_supervisor_module' => array(
                'test-supervisor' => array(
                    'host' => 'localhost',
                    'username' => 'user',
                    'password' => '123'
                ),
            )
        ));

        $compontents = $this->components = new SupervisorAbstractServiceFactory();

        $supervisorManager = new SupervisorManager();
        $supervisorManager->setServiceLocator($services);
        $supervisorManager->addAbstractFactory($compontents);
        $this->supervisorManager = $supervisorManager;
    }

    public function testMissingConfigServiceIndicatesCannotCreateInstance()
    {
        $this->assertFalse($this->components->canCreateServiceWithName($this->supervisorManager, 'foo', 'foo'));
    }

    public function testMissingSupervisorServicePrefixIndicatesCannotCreateInstance()
    {
        $this->services->setService('Config', array());
        $this->assertFalse($this->components->canCreateServiceWithName($this->supervisorManager, 'foo', 'foo'));
    }

    public function testMissingConfigIndicatesCannotCreateInstance()
    {
        $this->services->setService('Config', null);
        $this->assertFalse($this->components->canCreateServiceWithName($this->supervisorManager, 'foo', 'foo'));
    }

    public function testInvalidConfigIndicatesCannotCreateInstance()
    {
        $this->services->setService('Config', array('humus_supervisor_module' => 'string'));
        $this->assertFalse($this->components->canCreateServiceWithName($this->supervisorManager, 'foo', 'foo'));
    }

    public function testEmptySupervisorConfigIndicatesCannotCreateConsumer()
    {
        $this->services->setService('Config', array('humus_supervisor_module' => array()));
        $this->assertFalse(
            $this->components->canCreateServiceWithName($this->supervisorManager, 'test-consumer', 'test-consumer')
        );
    }

    public function testSupervisorFactory()
    {
        $this->assertTrue(
            $this->components->canCreateServiceWithName(
                $this->supervisorManager,
                'test-supervisor',
                'test-supervisor'
            )
        );

        $supervisor = $this->components->createServiceWithName(
            $this->supervisorManager,
            'test-supervisor',
            'test-supervisor'
        );
        $this->assertInstanceOf('Indigo\Supervisor\Supervisor', $supervisor);

        $this->assertTrue(
            $this->components->canCreateServiceWithName(
                $this->supervisorManager,
                'test-supervisor',
                'test-supervisor'
            )
        );

        $supervisor2 = $this->components->createServiceWithName(
            $this->supervisorManager,
            'test-supervisor',
            'test-supervisor'
        );

        $this->assertSame($supervisor, $supervisor2);
    }

    public function testMissingSpecIndicatesCannotCreateSupervisor()
    {
        $this->services->setService('Config', array(
            'humus_supervisor_module' => array(
                'test-supervisor' => array(
                ),
            ),
        ));
        $this->assertFalse(
            $this->components->canCreateServiceWithName(
                $this->supervisorManager,
                'test-supervisor',
                'test-supervisor'
            )
        );
    }
}
