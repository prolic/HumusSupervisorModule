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
