<?php

namespace HumusSupervisorModuleTest;

use HumusSupervisorModule\Module;

class ModuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Zend\Mvc\Application
     */
    private $application;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Zend\EventManager\EventInterface
     */
    private $event;


    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Zend\ServiceManager\ServiceManager
     */
    private $serviceManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|\Indigo\Supervisor\Supervisor
     */
    private $supervisor;

    public function setUp()
    {
        $this->application = $this->getMock('Zend\Mvc\Application', array('getServiceManager'), array(), '', false);
        $this->event = $this->getMock('Zend\EventManager\EventInterface', array('getApplication', 'getName', 'getTarget', 'getParams', 'getParam', 'setName', 'setTarget', 'setParams', 'setParam', 'stopPropagation', 'propagationIsStopped'));
        $this->serviceManager = $this->getMock('Zend\ServiceManager\ServiceManager');
        $this->supervisor = $this->getMock('Indigo\Supervisor\Supervisor', array(), array(), '', false);

        $this
            ->application
            ->expects($this->any())
            ->method('getServiceManager')
            ->will($this->returnValue($this->serviceManager));

        $this
            ->event
            ->expects($this->any())
            ->method('getTarget')
            ->will($this->returnValue($this->application));

        $this
            ->event
            ->expects($this->any())
            ->method('getApplication')
            ->will($this->returnValue($this->application));

        $this
            ->serviceManager
            ->expects($this->any())
            ->method('get')
            ->will($this->onConsecutiveCalls(
                array(
                    'humus_supervisor_module' => array(
                        'test-supervisor' => array(
                            'host' => 'localhost',
                            'username' => 'user',
                            'password' => '123'
                        )
                    )
                ),
                $this->supervisor
            ));
    }

    public function testGetConfig()
    {
        $module = new Module();

        $config = $module->getConfig();

        $this->assertInternalType('array', $config);
        $this->assertSame($config, unserialize(serialize($config)));
    }

    public function testGetAutoloaderConfig()
    {
        $module = new Module();

        $config = $module->getAutoloaderConfig();

        $this->assertInternalType('array', $config);
        $this->assertSame($config, unserialize(serialize($config)));
    }

    public function testBootstrap()
    {
        $module = new Module();

        $module->onBootstrap($this->event);

        $supervisor = $this->serviceManager->get('test-supervisor');
        $this->assertSame($this->supervisor, $supervisor);
    }
}
