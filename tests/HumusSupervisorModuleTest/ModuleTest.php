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
 * and is licensed under the MIT license
 */

namespace HumusSupervisorModuleTest;

use HumusSupervisorModule\Module;
use Zend\Console\Adapter\AdapterInterface as ConsoleAdapter;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;

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

    /**
     * @var ConsoleAdapter
     */
    private $consoleAdapter;

    public function setUp()
    {
        $this->application = $this->getMock('Zend\Mvc\Application', array('getServiceManager'), array(), '', false);
        $this->event = $this->getMock(
            'Zend\EventManager\EventInterface',
            array(
                'getApplication',
                'getName',
                'getTarget',
                'getParams',
                'getParam',
                'setName',
                'setTarget',
                'setParams',
                'setParam',
                'stopPropagation',
                'propagationIsStopped'
            )
        );
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

        $this->consoleAdapter = $this->getMock('Zend\Console\Adapter\AdapterInterface');
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

    public function testGetConsoleUsage()
    {
        $module = new Module();

        $usage = $module->getConsoleUsage($this->consoleAdapter);
        $this->assertInternalType('array', $usage);
    }

    public function testBootstrap()
    {
        $this->serviceManager = new ServiceManager();
        $this->serviceManager->setService('Config', array(
            'humus_supervisor_module' => array(
                'test-supervisor' => array(
                    'host' => 'localhost',
                    'username' => 'user',
                    'password' => '123'
                )
            )
        ));
        $this->application = $this->getMock('Zend\Mvc\Application', array('getServiceManager'), array(), '', false);
        $this->event = $this->getMock(
            'Zend\EventManager\EventInterface',
            array(
                'getApplication',
                'getName',
                'getTarget',
                'getParams',
                'getParam',
                'setName',
                'setTarget',
                'setParams',
                'setParam',
                'stopPropagation',
                'propagationIsStopped'
            )
        );
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

        $this->serviceManager->setService('Application', $this->application);

        $module = new Module();

        $module->onBootstrap($this->event);

        $supervisor = $this->serviceManager->get('test-supervisor');

        $this->assertInstanceOf('Indigo\Supervisor\Supervisor', $supervisor);
    }
}
