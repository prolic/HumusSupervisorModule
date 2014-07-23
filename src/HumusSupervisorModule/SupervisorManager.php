<?php

namespace HumusSupervisorModule;

use Indigo\Supervisor\Supervisor;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;

class SupervisorManager extends AbstractPluginManager
{
    /**
     * Validate the plugin
     *
     * Checks that the filter loaded is either a valid callback or an instance
     * of FilterInterface.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\RuntimeException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof Supervisor) {
            // we're okay
            return;
        }

        throw new Exception\RuntimeException(sprintf(
            'Plugin of type %s is invalid; must be an instance of Indigo\Supervisor\Supervisor',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin))
        ));
    }
}
