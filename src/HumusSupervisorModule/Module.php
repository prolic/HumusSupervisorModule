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

namespace HumusSupervisorModule;

use GuzzleHttp\Client;
use Indigo\Supervisor\Connector\GuzzleConnector;
use Indigo\Supervisor\Connector\ZendConnector;
use Indigo\Supervisor\Supervisor;
use Zend\Console\Adapter\AdapterInterface;
use Zend\EventManager\EventInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;

class Module implements
    AutoloaderProviderInterface,
    BootstrapListenerInterface,
    ConfigProviderInterface,
    ConsoleUsageProviderInterface
{
    /**
     * Get config
     *
     * @return array|mixed|\Traversable
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    /**
     * Bootstrap the module / build supervisors
     *
     * @param EventInterface $e
     * @return array
     */
    public function onBootstrap(EventInterface $e)
    {
        $serviceManager = $e->getApplication()->getServiceManager();

        $config = $serviceManager->get('Config');
        $moduleConfig = $config['humus_supervisor_module'];

        foreach ($moduleConfig as $name => $connectionSettings) {
            $serviceManager->setFactory($name, function() use ($connectionSettings) {
                $port = isset($connectionSettings['port']) ? ':' . $connectionSettings['port'] : '';


                $connector = new GuzzleConnector(new Client(array(
                    'base_url' => 'http://' . $connectionSettings['host'] . $port
                )));


                /*
                $connector = new ZendConnector(new \Zend\XmlRpc\Client($connectionSettings['host'] . $port));
                */
                $connector->setCredentials($connectionSettings['username'], $connectionSettings['password']);

                $supervisor = new Supervisor($connector);
                return $supervisor;
            });
        }
    }

    /**
     * Return an array for passing to Zend\Loader\AutoloaderFactory.
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/../../autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }

    /**
     * Returns an array or a string containing usage information for this module's Console commands.
     * The method is called with active Zend\Console\Adapter\AdapterInterface that can be used to directly access
     * Console and send output.
     *
     * If the result is a string it will be shown directly in the console window.
     * If the result is an array, its contents will be formatted to console window width. The array must
     * have the following format:
     *
     *     return array(
     *                'Usage information line that should be shown as-is',
     *                'Another line of usage info',
     *
     *                '--parameter'        =>   'A short description of that parameter',
     *                '-another-parameter' =>   'A short description of another parameter',
     *                ...
     *            )
     *
     * @param AdapterInterface $console
     * @return array|string|null
     */
    public function getConsoleUsage(AdapterInterface $console)
    {
        return array(
            // Describe available commands
            'humus supervisor <command>'    => '',

            'Available commands:',
            array(
                '(start|stop|processlist|pid|version|api|islocal)',
                'start/ stop the supervisor, list all processes, get supervisor pid, "
                . "get supervisor version, get api version'
            )
        );
    }
}
