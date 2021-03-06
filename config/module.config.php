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

return array(
    'humus_supervisor_module' => array(
        'supervisor_plugin_manager' => array(
            'abstract_factories' => array(
                __NAMESPACE__ . '\\SupervisorAbstractServiceFactory',
            )
        ),
        'listener_plugin_manager' => array(
        )
    ),
    'console' => array(
        'router' => array(
            'routes' => array(
                'humus_supervisor_module-supervisor' => array(
                    'options' => array(
                        'route'    => 'humus supervisor <name> (start|stop|processlist|pid|version|api|islocal|connection):action',
                        'defaults' => array(
                            'controller' => __NAMESPACE__ . '\\Controller\\Supervisor',
                        )
                    )
                ),
                'humus_supervisor_module-listener' => array(
                    'options' => array(
                        'route'    => 'humus supervisor listener <name>',
                        'defaults' => array(
                            'controller' => __NAMESPACE__ . '\\Controller\\Listener',
                            'action' => 'listen'
                        )
                    )
                ),
            )
        )
    ),
    'controllers' => array(
        'factories' => array(
            __NAMESPACE__ . '\\Controller\\Supervisor' => __NAMESPACE__ . '\\Service\\SupervisorControllerFactory',
            __NAMESPACE__ . '\\Controller\\Listener' => __NAMESPACE__ . '\\Service\\ListenerControllerFactory',
        )
    ),
);
