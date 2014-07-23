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
