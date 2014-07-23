<?php

namespace HumusSupervisorModuleTest;

use HumusSupervisorModule\SupervisorManager;
use Indigo\Supervisor\Supervisor;
use PHPUnit_Framework_TestCase as TestCase;

class SupervisorManagerTest extends TestCase
{
    public function testValidatePluginOkay()
    {
        $plugin = $this->getMock('Indigo\Supervisor\Supervisor', array(), array(), '', false);
        $manager = new SupervisorManager();
        $manager->validatePlugin($plugin);
    }

    /**
     * @expectedException HumusSupervisorModule\Exception\RuntimeException
     */
    public function testInvalidPlugin()
    {
        $plugin = 'foobar';
        $manager = new SupervisorManager();
        $manager->validatePlugin($plugin);
    }
}
