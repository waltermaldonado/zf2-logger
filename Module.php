<?php
namespace EddieJaoude\Zf2Logger;

use EddieJaoude\Zf2Logger\Listener\Request;
use EddieJaoude\Zf2Logger\Listener\Response;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module
{
    /**
     * @param MvcEvent $e
     */
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $serviceManager      = $e->getApplication()->getServiceManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $request = $serviceManager->get('RequestListener');
        $request->setLog($serviceManager->get('EddieJaoude\Zf2Logger'));
        $request->attach($eventManager);

        $config = $e->getApplication()->getServiceManager()->get('Config');
        $moduleConfig = $config['EddieJaoude\Zf2Logger'];

        $response = $serviceManager->get('ResponseListener');
        $response->setLog($serviceManager->get('EddieJaoude\Zf2Logger'));
        $mediaTypes = empty($moduleConfig['doNotLog']['mediaTypes']) ? array() : $moduleConfig['doNotLog']['mediaTypes'];
        $response->setIgnoreMediaTypes($mediaTypes);
        $response->attach($eventManager);

        return;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    /**
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src',
                ),
            ),
        );
    }

    /**
     * @return array
     */
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'EddieJaoude\Zf2Logger' => 'EddieJaoude\Zf2Logger\Factory\Zf2Logger'
            ),
            'invokables' => array(
                'RequestListener' => 'EddieJaoude\Zf2Logger\Listener\Request',
                'ResponseListener' => 'EddieJaoude\Zf2Logger\Listener\Response'
            )
        );
    }

}
