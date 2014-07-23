<?php

namespace HumusSupervisorModuleTest\Controller;

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
