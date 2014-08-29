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

use Indigo\Supervisor\Event\ListenerInterface;
use HumusSupervisorModule\Exception;
use Zend\Console\ColorInterface;
use Zend\Mvc\Controller\AbstractConsoleController;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;

class ListenerController extends AbstractConsoleController
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $listenerPluginManager;

    /**
     * {@inheritdoc}
     */
    public function listenAction()
    {
        $request = $this->getRequest();
        /* @var $request \Zend\Console\Request */
        $name = $request->getParam('name');

        if (!$this->listenerPluginManager->has($name)) {
            $this->getConsole()->writeLine($name . ' not found in ListenerManager',ColorInterface::RED);
            return $this->getResponse()->setErrorLevel(1);
        }

        $listener = $this->listenerPluginManager->get($name);
        $listener->listen();
    }

    /**
     * @param ServiceLocatorInterface $manager
     */
    public function setListenerManager(ServiceLocatorInterface $manager)
    {
        $this->listenerPluginManager = $manager;
    }
}
