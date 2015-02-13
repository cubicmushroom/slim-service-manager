<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 12/02/15
 * Time: 11:52
 */

namespace CubicMushroom\Slim\ServiceManager;

use CubicMushroom\Slim\ServiceManager\Exception\Config\InvalidServiceCallConfigException;
use CubicMushroom\Slim\ServiceManager\Exception\Config\InvalidServiceConfigException;
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

    /**
     * Default for whether the ServiceManager automatically registers itself as a service
     */
    const DEFAULT_REGISTER_SERVICE = true;

    /**
     * Default value the ServiceManager registers itself as
     */
    const DEFAULT_SERVICE_NAME = 'service_manager';

    /**
     * Array of options
     *
     * @var array
     */
    protected $options = [];

    /**
     * Options supported
     *
     * @var array
     */
    protected $defaultOptions = [
        'registerAsService' => self::DEFAULT_REGISTER_SERVICE,
        'ownServiceName'    => self::DEFAULT_SERVICE_NAME,
    ];

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
     * @param array $options [optional] Array of options for the setup of the ServiceManagers
     */
    public function __construct(Slim $app = null, array $options = [])
    {
        $this->setOptions($this->mergeOptions($this->defaultOptions, $options));

        if (!is_null($app)) {
            $this->setApp($app);

            $this->setupServices();

            if ($this->getOption('registerAsService')) {
                $this->registerSelfAsService();
            }
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
            $this->setOption($key, $value);
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
            if (!is_array($settings['services'])) {
                throw InvalidServiceConfigException::build([], ['serviceConfig' => $settings['services']]);
            }

            foreach ($settings['services'] as $service => $config) {
                if (!is_array($config)) {
                    throw InvalidServiceConfigException::build(
                        [],
                        ['serviceName' => $service, 'serviceConfig' => $config]
                    );
                }
                $this->validateServiceConfig($config, $service);
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
    public function registerSelfAsService()
    {
        $this->getApp()->container->set($this->options['ownServiceName'], $this);
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


    /**
     *
     * @param array  $config      Config to be checked
     * @param string $serviceName Name of the service checking (for exception)
     *
     * @throws InvalidServiceConfigException if missing class parameters
     * @throws InvalidServiceCallConfigException if the call config is not valid
     */
    protected function validateServiceConfig(array $config, $serviceName)
    {
        $exceptionArgs = ['serviceName' => $serviceName, 'serviceConfig' => $config];

        if (!isset($config['class'])) {
            throw InvalidServiceConfigException::build(
                [],
                array_merge($exceptionArgs, ['missingParameter' => 'class'])
            );
        }

        if (isset($config['calls'])) {
            if (!is_array($config['calls'])) {
                throw InvalidServiceCallConfigException::build(
                    [],
                    array_merge(
                        $exceptionArgs,
                        ['callConfig' => $config['calls']]
                    )
                );
            }

            foreach ($config['calls'] as $callConfigIndex => $callConfig) {
                if (!is_array($callConfig)) {
                    throw InvalidServiceCallConfigException::build(
                        [],
                        array_merge(
                            $exceptionArgs,
                            ['callConfig' => $callConfig, 'callConfigIndex' => $callConfigIndex]
                        )
                    );
                }

                if (isset($callConfig[1]) && !is_array($callConfig[1])) {
                    throw InvalidServiceCallConfigException::build(
                        [],
                        array_merge(
                            $exceptionArgs,
                            [
                                'callConfig'       => $callConfig,
                                'callConfigIndex'  => $callConfigIndex,
                                'invalidArguments' => $callConfig[1]
                            ]
                        )
                    );
                }
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


    /**
     * @param $key
     *
     * @return string
     *
     * @throws InvalidOptionException if the option is now set
     */
    public function getOption($key)
    {
        if (!isset($this->options[$key])) {
            throw InvalidOptionException::build([], ['option' => $key]);
        }

        return $this->options[$key];
    }


    /**
     * @param string $key
     * @param mixed  $value
     *
     * @throws InvalidOptionException if attempting to set an unsupported option
     */
    public function setOption($key, $value)
    {
        if (!in_array($key, array_keys($this->defaultOptions))) {
            throw InvalidOptionException::build([], ['option' => $key]);
        }

        $this->options[$key] = $value;
    }
}