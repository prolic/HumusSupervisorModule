<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace HumusSupervisorModuleTest\Controller;

use HumusSupervisorModule\Controller\SupervisorController;
use Zend\Console\Request;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Console\RouteMatch;
use Zend\ServiceManager\ServiceManager;
use Zend\Test\PHPUnit\Controller\AbstractConsoleControllerTestCase;

class SupervisorControllerTest extends AbstractConsoleControllerTestCase
{
    /**
     * @var ServiceManager
     */
    protected $services;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $manager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $supervisor;

    /**
     * @var SupervisorController
     */
    protected $controller;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var MvcEvent
     */
    protected $event;

    /**
     * @var RouteMatch
     */
    protected $routeMatch;

    protected function setUp()
    {
        parent::setUp();
        $this->setApplicationConfig(
            include __DIR__ . '/../../TestConfiguration.php.dist'
        );
        $this->services = $services = $this->getApplicationServiceLocator();
        $services->setAllowOverride(true);
        $services->setService('Config', array(
            'humus_supervisor_module' => array(
                'supervisor_plugin_manager' => array(
                ),
                'test-supervisor' => array(
                    'host' => 'localhost',
                    'port' => 3432,
                    'username' => 'user',
                    'password' => '123'
                )
            )
        ));

        $this->supervisor = $supervisor = $this->getMock('Indigo\Supervisor\Supervisor', array(), array(), '', false);

        $this->manager = $manager = $this->getMock('HumusSupervisorModule\SupervisorManager');
        $manager
            ->expects($this->any())
            ->method('get')
            ->with('test-supervisor')
            ->willReturn($supervisor);

        $manager
            ->expects($this->any())
            ->method('has')
            ->with('test-supervisor')
            ->willReturn(true);

        $this->controller = new SupervisorController();
        $this->routeMatch = new RouteMatch(array(
            'controller' => 'HumusSupervisorModule\Controller\Supervisor',
            'name' => 'test-supervisor'
        ));
        $this->request = new Request();
        $this->event = new MvcEvent();
        $this->event->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
        $this->controller->setServiceLocator($this->services);
        $this->controller->setSupervisorPluginManager($this->manager);
    }

    public function testInvalidSupervisorTogetherWithCompleteMvcDispatchCycle()
    {
        $this->setUseConsoleRequest(true);

        $this->dispatch('humus supervisor invalid-supervisor connection');

        $this->assertResponseStatusCode(1);
        $this->assertConsoleOutputContains('HumusSupervisorModule\Exception\RuntimeException');
        $this->assertConsoleOutputContains('invalid-supervisor not found in SupervisorManager');
    }

    public function testConnection()
    {
        $this->routeMatch->setParam('action', 'connection');

        ob_start();
        $this->controller->dispatch($this->request);
        $result = ob_get_clean();

        $this->assertNotFalse(strpos($result, 'host: localhost'));
        $this->assertNotFalse(strpos($result, 'port: 3432'));
        $this->assertNotFalse(strpos($result, 'username: user'));
        $this->assertNotFalse(strpos($result, 'password: 123'));
    }

    public function testStartSupervisor()
    {
        $this->routeMatch->setParam('action', 'start');
        $this->supervisor
            ->expects($this->once())
            ->method('__call')
            ->with('startAllProcesses', array());

        $this->controller->dispatch($this->request);
    }

    public function testStopSupervisor()
    {
        $this->routeMatch->setParam('action', 'stop');
        $this->supervisor
            ->expects($this->once())
            ->method('__call')
            ->with('stopAllProcesses', array());

        $this->controller->dispatch($this->request);
    }

    public function testPidSupervisor()
    {
        $this->routeMatch->setParam('action', 'pid');
        $this->supervisor
            ->expects($this->once())
            ->method('__call')
            ->with('getPID', array());

        $this->controller->dispatch($this->request);
    }

    public function testVersionSupervisor()
    {
        $this->routeMatch->setParam('action', 'version');
        $this->supervisor
            ->expects($this->once())
            ->method('__call')
            ->with('getVersion', array());

        $this->controller->dispatch($this->request);
    }

    public function testApiSupervisor()
    {
        $this->routeMatch->setParam('action', 'api');
        $this->supervisor
            ->expects($this->once())
            ->method('__call')
            ->with('getAPIVersion', array());

        $this->controller->dispatch($this->request);
    }

    public function testIsLocalSupervisor()
    {
        $this->routeMatch->setParam('action', 'islocal');
        $this->supervisor
            ->expects($this->once())
            ->method('isLocal');

        ob_start();
        $this->controller->dispatch($this->request);
        ob_get_clean();
    }

    public function testProcesslistSupervisor()
    {
        $process = $this->getMock('stdClass', array('getPayload', 'getName', 'getMemUsage'));
        $process
            ->expects($this->once())
            ->method('getPayload')
            ->willReturn(array(
                'statename' => 'RUNNING',
                'pid' => 123,
                'description' => 'foobar'
            ));
        $process
            ->expects($this->once())
            ->method('getMemUsage')
            ->willReturn(1000);
        $process
            ->expects($this->once())
            ->method('getName')
            ->willReturn('test');

        $this->routeMatch->setParam('action', 'processlist');
        $this->supervisor
            ->expects($this->once())
            ->method('getAllProcesses')
            ->willReturn(array($process));

        ob_start();
        $this->controller->dispatch($this->request);
        $result = ob_get_clean();

        $this->assertNotFalse(strpos($result, 'test'));
        $this->assertNotFalse(strpos($result, 'RUNNING'));
        $this->assertNotFalse(strpos($result, '1000'));
        $this->assertNotFalse(strpos($result, '123'));
        $this->assertNotFalse(strpos($result, 'foobar'));
    }
}
