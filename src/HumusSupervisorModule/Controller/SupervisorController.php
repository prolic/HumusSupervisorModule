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

namespace HumusSupervisorModule\Controller;

use Indigo\Supervisor\Supervisor;
use HumusSupervisorModule\Exception;
use Zend\Console\ColorInterface;
use Zend\Mvc\Controller\AbstractConsoleController;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;

class SupervisorController extends AbstractConsoleController
{
    /**
     * @var AbstractPluginManager
     */
    protected $supervisorPluginManager;

    /**
     * {@inheritdoc}
     */

    public function connectionAction()
    {
        $this->getSupervisor(); // checks for existence
        $config = $this->getServiceLocator()->get('Config');
        $connectionConfig = $config['humus_supervisor_module'][$this->getRequest()->getParam('name')];
        echo 'host: ' . $connectionConfig['host'] . PHP_EOL;
        if (isset($connectionConfig['port'])) {
            echo 'port: ' . $connectionConfig['port'] . PHP_EOL;
        }
        echo 'username: ' . $connectionConfig['username'] . PHP_EOL;
        echo 'password: ' . $connectionConfig['password'] . PHP_EOL;
    }

    /**
     * @return void
     */
    public function startAction()
    {
        $this->getSupervisor()->startAllProcesses();
    }

    /**
     * @return void
     */
    public function stopAction()
    {
        $this->getSuperVisor()->stopAllProcesses();
    }

    /**
     * @return void
     */
    public function processlistAction()
    {
        $processes = $this->getSuperVisor()->getAllProcesses();

        $table = new \Zend\Text\Table\Table(array('columnWidths' => array(40, 9, 14, 7, 20)));

        $row = new \Zend\Text\Table\Row();
        $row->createColumn('Process name', array('align' => 'center'));
        $row->createColumn('State', array('align' => 'center'));
        $row->createColumn('memory usage', array('align' => 'center'));
        $row->createColumn('PID', array('align' => 'center'));
        $row->createColumn('uptime', array('align' => 'center'));
        $table->appendRow($row);
        $table->setPadding(1);
        foreach ($processes as $process) {
            $payload = $process->getPayload();
            $row = new \Zend\Text\Table\Row();
            $row->createColumn($process->getName());
            $row->createColumn($payload['statename']);
            $row->createColumn((string) $process->getMemUsage());
            $row->createColumn((string) $payload['pid']);
            $row->createColumn((string) $payload['description']);
            $table->appendRow($row);
        }
        echo $table;
    }

    /**
     * @return void
     */
    public function pidAction()
    {
        echo $this->getSuperVisor()->getPID();
    }

    /**
     * @return void
     */
    public function versionAction()
    {
        echo $this->getSuperVisor()->getVersion();
    }

    /**
     * @return void
     */
    public function apiAction()
    {
        echo $this->getSuperVisor()->getAPIVersion();
    }

    /**
     * @return void
     */
    public function isLocalAction()
    {
        if ($this->getSuperVisor()->isLocal()) {
            echo 'local';
        } else {
            echo 'remote';
        }
    }

    /**
     * @param AbstractPluginManager $pluginManager
     */
    public function setSupervisorPluginManager(AbstractPluginManager $pluginManager)
    {
        $this->supervisorPluginManager = $pluginManager;
    }

    /**
     * @return Supervisor
     * @throws Exception\RuntimeException
     */
    protected function getSupervisor()
    {
        $name = $this->getRequest()->getParam('name');

        var_dump($this->supervisorPluginManager); die;

        if (!$this->supervisorPluginManager->has($name)) {
            throw new Exception\RuntimeException(
                $name . ' not found in SupervisorPluginManager'
            );
        }

        return $this->supervisorPluginManager->get($name);
    }
}
