<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 12/02/15
 * Time: 11:52
 */

namespace CubicMushroom\Slim\ServiceManager;

use CubicMushroom\Slim\ServiceManager\Exception\InvalidOptionException;
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

    const DEFAULT_SERVICE_NAME = 'service_manager';

    /**
     * Name the service manager registers itself as a service under
     *
     * @var string
     */
    protected $ownServiceName;

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
     * @param Slim  $app     [optional] Slim application object to load services for
     * @param array $options [optional] Array of options for the setup of the ServiceManager
     */
    public function __construct(Slim $app = null, array $options = [])
    {
        $defaultOptions = [
            'ownServiceName' => self::DEFAULT_SERVICE_NAME,
        ];

        $this->setOptions($this->mergeOptions($defaultOptions, $options));

        if (!is_null($app)) {
            $this->setApp($app);

            $this->setupServices();

            $this->registerSelfAsService();
        }
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Setup methods
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Sets object properties based on options passed
     *
     * @param array $options
     *
     * @throws InvalidOptionException if an invalid options is passed
     */
    protected function setOptions(array $options)
    {
        foreach ($options as $key => $value) {
            switch ($key) {
                case 'ownServiceName':
                    $this->setOwnServiceName($value);
                    break;

                default:
                    throw InvalidOptionException::build([], ['option' => $key]);
            }

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
                                call_user_func_array([$service, $call[0]], $call[1]);
                            }
                        }

                        return $service;
                    }
                );
            }
        }
    }


    /**
     * Registers the service manager itself as a service for the app
     */
    protected function registerSelfAsService()
    {
        $this->getApp()->container->set($this->ownServiceName, $this);
    }


    // -----------------------------------------------------------------------------------------------------------------
    // Helper methods
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Works out a full set to
     *
     * @param array $defaults Default options
     * @param array $options  Passed options to override defaults
     *
     * @return array
     */
    protected function mergeOptions($defaults, $options)
    {
        return array_merge($defaults, $options);
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


    /**
     * @return mixed
     */
    public function getOwnServiceName()
    {
        return $this->ownServiceName;
    }


    /**
     * @param mixed $ownServiceName
     */
    public function setOwnServiceName($ownServiceName)
    {
        if (isset($this->ownServiceName)) {
            throw PropertyAlreadySetException::build([], ['property' => 'ownServiceName']);
        }

        $this->ownServiceName = $ownServiceName;
    }
}