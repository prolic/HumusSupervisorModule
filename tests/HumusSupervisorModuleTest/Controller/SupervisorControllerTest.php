<?php

namespace HumusSupervisorModuleTest\Controller;

use HumusSupervisorModule\Controller\SupervisorController;
use HumusSupervisorModule\SupervisorManager;
use Zend\Console\Request;
use Zend\Console\Response;
use Zend\Mvc\Controller\ControllerManager;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Console\RouteMatch;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Parameters;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class SupervisorControllerTest extends AbstractHttpControllerTestCase
{
    protected function setUp()
    {
        $this->setApplicationConfig(
            include __DIR__ . '/../../TestConfiguration.php.dist'
        );
        parent::setUp();
        $this->setUseConsoleRequest(true);
    }

    public function testFetchingOfSupervisors()
    {
        $supervisor = $this->getMock('Indigo\Supervisor\Supervisor', array(), array(), '', false);

        $manager = $this->getMock('HumusSupervisorModule\SupervisorManager');

        $manager
            ->expects($this->any())
            ->method('get')
            ->with('test-supervisor')
            ->willReturn($supervisor);


        $services = $this->getApplicationServiceLocator();
        $services->setAllowOverride(true);
        $services->setService('Config', array(
            'humus_supervisor_module' => array(
                'supervisor_plugin_manager' => array(
                    'abstract_factories' => array(
                        'HumusSupervisorModule\SupervisorAbstractServiceFactory'
                    )
                ),
                'test-supervisor' => array(
                    'host' => 'localhost',
                    'port' => 3432,
                    'username' => 'user',
                    'password' => '123'
                )
            )
        ));
        ob_start();
        $this->dispatch('humus supervisor test-supervisor connection');
        $result = ob_get_clean();
        $this->assertResponseStatusCode(0);
        $this->assertControllerName('HumusSupervisorModule\Controller\Supervisor');
        $this->assertActionName('connection');

        $this->assertNotFalse(strpos($result, 'host: localhost'));
        $this->assertNotFalse(strpos($result, 'port: 3432'));
        $this->assertNotFalse(strpos($result, 'username: user'));
        $this->assertNotFalse(strpos($result, 'password: 123'));
    }
}
