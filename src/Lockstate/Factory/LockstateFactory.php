<?php
namespace Lockstate\Factory;

use Lockstate\Service\Lockstate;
use Zend\Http\Client;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class LockstateFactory implements FactoryInterface
{
    /**
     * Create, configure and return Lockstate API wrapper service.
     *
     * @see FactoryInterface::createService()
     * @throws \Exception
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');

        $requiredConfigs = ['app_id', 'app_secret', 'auth_url', 'base_url'];

        foreach ($requiredConfigs as $requiredConfig) {
            if (empty($config['lockstate'][$requiredConfig])) {
                throw new \Exception(
                    'Config required in order to create Lockstate service. ' .
                    'Required config: $config["lockstate"]["' . $requiredConfig . '"].'
                );
            }
        }

        $client = new Client($config['lockstate']['base_url'], [
            'adapter'   => 'Zend\Http\Client\Adapter\Curl',
        ]);
        $client->setEncType('application/json');

        return new Lockstate(
            $client,
            $config['lockstate']['base_url'],
            $config['lockstate']['auth_url'],
            $config['lockstate']['app_id'],
            $config['lockstate']['app_secret']
        );
    }

}