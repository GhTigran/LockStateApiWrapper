<?php

namespace Lockstate;

use Lockstate\Factory\LockstateFactory;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;

class Module implements AutoloaderProviderInterface
{
    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\ClassMapAutoloader' => [
                __DIR__ . '/autoload_classmap.php',
            ],
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ],
            ],
        ];
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return [
            'invokables' => [

            ],
            'factories' => [
                // Services
                'Lockstate\Service\Lockstate' => function($sm) {
                    $as = new LockstateFactory();
                    $as = $as->createService($sm);
                    return $as;
                },
            ],
            'aliases'=> [
                'service_lockstate_lockstate' => 'Lockstate\Service\Lockstate',
            ],

        ];
    }
}
