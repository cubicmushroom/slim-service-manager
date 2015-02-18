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
use CubicMushroom\Slim\ServiceManager\ServiceDefinition;
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
     * Default for whether the ServiceManager registers itself as
     */
    const DEFAULT_SERVICE_NAME = 'service_manager';

    /**
     * Default for whether the ServiceManager registers services automatically
     */
    const DEFAULT_AUTOLOAD = true;

    /**
     * Default for whether the service registers the app itself as a service (with the '@app' key)
     */
    const DEFAULT_REGISTER_APP = true;

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
        'autoload'          => self::DEFAULT_AUTOLOAD,
        'registerApp'       => self::DEFAULT_REGISTER_APP,
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

            if ($this->getOption('registerApp')) {
                $this->registerApp();
            }

            if ($this->getOption('autoload')) {
                $this->setupServices();
            }

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
     * Registers the app itself as a service
     */
    public function registerApp()
    {
        $app = $this->getApp();
        $app->container->set('@app', $app);
    }


    /**
     * Registers the services with the application
     */
    public function setupServices()
    {
        $settings = $this->getApp()->container->get('settings');

        if (!empty($settings['services'])) {
            if (!is_array($settings['services'])) {
                throw InvalidServiceConfigException::build([], ['serviceConfig' => $settings['services']]);
            }

            foreach ($settings['services'] as $service => $config) {
                $this->setupService($service, $config);
            }
        }
    }


    /**
     * @param string $service Service name
     * @param array  $config  Service definition array
     *
     * @throws InvalidServiceConfigException
     * @throws InvalidServiceCallConfigException
     */
    public function setupService($service, array $config)
    {
        $app = $this->getApp();

        $app->container->set(
            '@' . $service,
            new ServiceDefinition($this, $service, $config)
        );
    }


    /**
     * Registers the service manager itself as a service for the app
     */
    public function registerSelfAsService()
    {
        $this->getApp()->container->set('@' . $this->options['ownServiceName'], $this);
    }


    /**
     * @param $serviceName
     *
     * @return string
     */
    public function getServiceName($serviceName)
    {
        return '@' . ltrim($serviceName, '@');
    }


    /**
     * Returns a service using the service name given
     *
     * The service name will be prefixed with an '@' if it's not already
     *
     * @param string $serviceName Service name, with or without the '@' prefix
     *
     * @return mixed
     */
    public function getService($serviceName)
    {
        $serviceName = $this->getServiceName($serviceName);

        return $this->getApp()->container->get($serviceName);
    }


    /**
     * @param $tagName
     *
     * @return ServiceDefinition[]
     */
    public function getTaggedServices($tagName)
    {
        $taggedServices = [];
        foreach ($this->getApp()->container->all() as $serviceName => $serviceDefinition) {
            if (!$serviceDefinition instanceof ServiceDefinition) {
                continue;
            }

            if (in_array($tagName, $serviceDefinition->getTags()->keys())) {
                $taggedServices['@' . $serviceDefinition->getServiceName()] = $serviceDefinition;
            }
        }

        return $taggedServices;
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