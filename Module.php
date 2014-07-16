<?php
/**
 * This file is placed here for compatibility with Zendframework 2's ModuleManager.
 * It allows usage of this module even without composer.
 * The original Module.php is in 'src/HumusRabbitMqModule' in order to respect PSR-0
 */
require_once __DIR__ . '/src/HumusSupervisorModule/Module.php';
