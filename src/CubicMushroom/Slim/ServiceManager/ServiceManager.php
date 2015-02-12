<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 12/02/15
 * Time: 11:52
 */

namespace CubicMushroom\Slim\ServiceManager;

use Slim\Slim;

/**
 * Class ServiceManager
 *
 * Loads services into a Slim Framework application, based on the contents of the 'services' app config
 *
 * @package CubicMushroom\Slim\ServiceManager
 */
class ServiceManager
{

    /**
     * @var Slim
     */
    protected $app;

    // -----------------------------------------------------------------------------------------------------------------
    // Constructor
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Loads services into Slim App based on the content of the 'services' config settings
     *
     * @param Slim $app [optional] Slim application object to load services for
     */
    public function __construct(Slim $app = null)
    {
        if (!is_null($app)) {
            $this->setApp($app);

            $this->setupServices();
        }
    }


    /**
     *
     */
    public function setupServices()
    {
        $app = $this->getApp();

        $settings = $app->container->get('settings');

        if (!empty($settings['services'])) {
            foreach ($settings['services'] as $service => $config) {
                $app->container->singleton(
                    $service,
                    function () use ($config) {
                        $class = $config['class'];
                        $args  = (!empty($config['arguments']) ? $config['arguments'] : []);

                        $reflectionClass = new \ReflectionClass($class);
                        $service         = $reflectionClass->newInstanceArgs($args);

                        if (!empty($config['calls'])) {
                            foreach ($config['calls'] as $call) {
                                $method = $call[0];
                                call_user_func_array([$service, $method], $call[1]);
                            }
                        }

                        return $service;
                    }
                );
            }
        }
    }


    // -----------------------------------------------------------------------------------------------------------------
    // Getters and Setters
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return Slim
     */
    public function getApp()
    {
        return $this->app;
    }


    /**
     * @param Slim $app
     */
    public function setApp(Slim $app)
    {
        $this->app = $app;
    }
}