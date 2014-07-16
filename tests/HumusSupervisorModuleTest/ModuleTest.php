<?php

namespace HumusSupervisorModuleTest;

use HumusSupervisorModule\Module;

class ModuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\Zend\Mvc\Application
     */
    private $application;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\Zend\Mvc\MvcEvent
     */
    private $event;


    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\Zend\ServiceManager\ServiceManager
     */
    private $serviceManager;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject|\Indigo\Supervisor\Supervisor
     */
    private $supervisor;

    public function setUp()
    {
        $this->application = $this->getMock('Zend\Mvc\Application', array(), array(), '', false);
        $this->event = $this->getMock('Zend\Mvc\MvcEvent');
        $this->serviceManager = $this->getMock('Zend\ServiceManager\ServiceManager');
        $this->supervisor = $this->getMock('Indigo\Supervisor\Supervisor', array(), array(), '', false);

        $this
            ->serviceManager
            ->expects($this->any())
            ->method('get')
            ->with('test-supervisor')
            ->will($this->returnValue($this->supervisor));

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
        $modile = new Module();

        $config = $modile->getAutoloaderConfig();

        $this->assertInternalType('array', $config);
        $this->assertSame($config, unserialize(serialize($config)));
    }


}
