Humus Supervisor Module
=======================

[![Build Status](https://travis-ci.org/prolic/HumusSupervisorModule.svg?branch=master)](https://travis-ci.org/prolic/HumusSupervisorModule)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/prolic/HumusSupervisorModule/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/prolic/HumusSupervisorModule/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/prolic/HumusSupervisorModule/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/prolic/HumusSupervisorModule/?branch=master)
[![Dependency Status](https://www.versioneye.com/user/projects/53c670c8a54f97c98f000002/badge.svg?style=flat)](https://www.versioneye.com/user/projects/53c670c8a54f97c98f000002)

Humus Supervisor Module is a module for Zend Framework 2 based on supervisord.

Dependencies
------------

 - PHP 5.4.0
 - [Indigo Supervisor](https://github.com/indigophp/supervisor)
 - [supervisord](http://www.supervisdord.com)
 - [GuzzleHttp 4.1.0](https://github.com/guzzle/guzzle)

Installation
------------

 1.  Add `"prolic/humus-supervisor-module": "dev-master"` to your `composer.json`
 2.  Run `php composer.phar install`
 3.  Enable the module in your `config/application.config.php` by adding `HumusSupervisorModule` to `modules`

Configuration
-------------

Change your application configuration like this:

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
$supervisor = $serviceManager->get('my-supervisor');
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
