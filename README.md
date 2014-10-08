Humus Supervisor Module
=======================

[![Build Status](https://travis-ci.org/prolic/HumusSupervisorModule.svg?branch=master)](https://travis-ci.org/prolic/HumusSupervisorModule)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/prolic/HumusSupervisorModule/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/prolic/HumusSupervisorModule/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/prolic/HumusSupervisorModule/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/prolic/HumusSupervisorModule/?branch=master)
[![License](https://poser.pugx.org/prolic/humus-supervisor-module/license.svg)](https://packagist.org/packages/prolic/humus-supervisor-module)
[![Latest Stable Version](https://poser.pugx.org/prolic/humus-supervisor-module/v/stable.svg)](https://packagist.org/packages/prolic/humus-supervisor-module)
[![Latest Unstable Version](https://poser.pugx.org/prolic/humus-supervisor-module/v/unstable.svg)](https://packagist.org/packages/prolic/humus-supervisor-module)
[![Total Downloads](https://poser.pugx.org/prolic/humus-supervisor-module/downloads.svg)](https://packagist.org/packages/prolic/humus-supervisor-module)
[![Dependency Status](https://www.versioneye.com/php/prolic:humus-supervisor-module/0.2.0/badge.svg)](https://www.versioneye.com/php/prolic:humus-supervisor-module)

Humus Supervisor Module is a module for Zend Framework 2 based on supervisord.

Dependencies
------------

 - PHP 5.3.23
 - [Indigo Supervisor](https://github.com/indigophp/supervisor)
 - [supervisord](http://supervisord.org/)
 - [Zend-Servicemanager 2.3.0](https://github.com/zendframework/zf2/tree/master/library/Zend/ServiceManager)
 - [Zend-Modulemanager 2.3.0](https://github.com/zendframework/zf2/tree/master/library/Zend/ModuleManager)
 - [Zend-Mvc 2.3.0](https://github.com/zendframework/zf2/tree/master/library/Zend/Mvc)
 - [Zend-XmlRpc 2.3.0](https://github.com/zendframework/zf2/tree/master/library/Zend/XmlRpc)
 - [ZendXml 1.0.0](https://github.com/zendframework/ZendXml)

Installation
------------

 1.  Add `"prolic/humus-supervisor-module": "dev-master"` to your `composer.json`
 2.  Run `php composer.phar install`
 3.  Enable the module in your `config/application.config.php` by adding `HumusSupervisorModule` to `modules`

Configuration
-------------

Add this to your module configuration:

``` php
'humus_supervisor_module' => array(
    'my-supervisor' => array(
        'host' => 'localhost',
        'port' => 19005,
        'username' => 'user',
        'password' => '123'
    )
)
```

Usage
-----

Make use of your supervisor:

``` php
$manager = $serviceManager->get('HumusSupervisorModule\SupervisorManager');
$supervisor = $manager->get('demo-supervisor');
$supervisor->isRunning();
```

Supervisord Installation
------------------------

    wget https://bootstrap.pypa.io/ez_setup.py -O - | sudo python
    sudo easy_install supervisor

For configuration of supervisord see: http://supervisord.org/configuration.html

start with

    supervisord

or to run no-daemon

    supervisord -n
