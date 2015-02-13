<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 13/02/15
 * Time: 11:56
 */

namespace CubicMushroom\Slim\ServiceManager;

class ServiceDefinition
{

    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    private $serviceName;


    /**
     * Stores config
     *
     * @param string $serviceName
     * @param array  $config
     */
    function __construct($serviceName, array $config)
    {
        $this->setServiceName($serviceName);
        $this->setConfig($config);
    }


    /**
     * Returns the constructed service
     */
    function __invoke()
    {
        $config = $this->config;

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


    // -----------------------------------------------------------------------------------------------------------------
    // Getters and Setters
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }


    /**
     * @param string $serviceName
     */
    public function setServiceName($serviceName)
    {
        $this->serviceName = $serviceName;
    }


    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }


    /**
     * @param array $config
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }
}