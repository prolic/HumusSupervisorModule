<?php

namespace HumusSupervisorModuleTest\Controller;

use HumusSupervisorModule\Controller\SupervisorController;
use HumusSupervisorModuleTest\ServiceManagerTestCase;
use Zend\Console\Request;
use Zend\Console\Response;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\Console\RouteMatch;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\Parameters;

class SupervisorControllerTest extends ServiceManagerTestCase
{
    /**
     * @var SupervisorController
     */
    protected $controller;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var RouteMatch
     */
    protected $routeMatch;

    /**
     * @var MvcEvent
     */
    protected $event;

    protected $console;

    public function setUp()
    {
        $serviceManager = $this->getServiceManager();
        $this->controller = new SupervisorController();
        $this->request    = new Request();
        $this->request->setParams(new Parameters(array('name' => 'test-supervisor')));
        $this->routeMatch = new RouteMatch(array('controller' => 'HumusSupervisorModule\Controller\Supervisor'));
        $this->event      = new MvcEvent();
        $this->event->setRouteMatch($this->routeMatch);
        $this->controller->setEvent($this->event);
        $this->controller->setServiceLocator($serviceManager);
        $this->console = $this->getMock('Zend\Console\Adapter\AdapterInterface');
        $this->controller->setConsole($this->console);
    }

    public function testFetchingOfSupervisors()
    {
        $this->request->getParams()->set('action', 'connection');
        $resonse = new Response();
        $this->controller->dispatch($this->request, $resonse);
    }
}
