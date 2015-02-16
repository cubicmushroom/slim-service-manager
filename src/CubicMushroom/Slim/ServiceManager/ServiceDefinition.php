<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 13/02/15
 * Time: 11:56
 */

namespace CubicMushroom\Slim\ServiceManager;

use CubicMushroom\Slim\ServiceManager\Exception\Config\InvalidServiceCallConfigException;
use CubicMushroom\Slim\ServiceManager\Exception\Config\InvalidServiceConfigException;
use CubicMushroom\Slim\ServiceManager\ServiceDefinition\MethodCallDefinition;

class ServiceDefinition
{

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var string
     */
    protected $serviceName;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var array
     */
    protected $arguments;

    /**
     * @var MethodCallDefinition[]
     */
    protected $methodCallDefinitions;

    /**
     * @var array
     */
    protected $config;


    /**
     * Stores config
     *
     * @param ServiceManager $serviceManager
     * @param string         $serviceName
     * @param array          $config
     *
     * @throws InvalidServiceCallConfigException
     * @throws InvalidServiceConfigException
     */
    function __construct(ServiceManager $serviceManager, $serviceName, array $config)
    {
        $this->validateServiceConfig($config, $serviceName);

        $this->setServiceManager($serviceManager);

        $this->setServiceName($serviceName);
        $this->setConfig($config);

        $this->setClass($config['class']);

        if (isset($config['arguments'])) {
            $this->setArguments($config['arguments']);
        }

        if (isset($config['calls'])) {
            foreach ($config['calls'] as $call_i => $call) {
                $this->addCallDefinition($call_i, new MethodCallDefinition($this, $call));
            }
        }
        $this->serviceManager = $serviceManager;
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


    /**
     * Returns the constructed service
     */
    function __invoke()
    {
        $config = $this->config;

        $class = $config['class'];
        $args  = (!empty($config['arguments']) ? $config['arguments'] : []);

        foreach ($args as $arg_i => $arg) {
            if ('@' === substr($arg, 0, 1)) {
                $args[$arg_i] = $this->getServiceManager()->getService($arg);
            }
        }

        $reflectionClass = new \ReflectionClass($class);
        $service         = $reflectionClass->newInstanceArgs($args);

        $methodCalls = $this->getMethodCallDefinitions();
        if (!empty($methodCalls)) {
            foreach ($methodCalls as $methodCall) {
                $methodCall($service);
            }
        }

        return $service;
    }


    // -----------------------------------------------------------------------------------------------------------------
    // Getters and Setters
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        return $this->serviceManager;
    }


    /**
     * @param ServiceManager $serviceManager
     */
    public function setServiceManager($serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }


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


    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }


    /**
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }


    /**
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }


    /**
     * @param array $arguments
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;
    }


    /**
     * @return MethodCallDefinition[]
     */
    public function getMethodCallDefinitions()
    {
        return $this->methodCallDefinitions;
    }


    /**
     * @param MethodCallDefinition[] $methodCallDefinitions
     */
    public function setMethodCallDefinitions(array $methodCallDefinitions)
    {
        $this->methodCallDefinitions = $methodCallDefinitions;
    }


    /**
     * @param int                  $index                The array index to store this definition under
     *                                                   This is used so that the index matches the service definition
     *                                                   order
     * @param MethodCallDefinition $methodCallDefinition Definition of the method call
     */
    public function addCallDefinition($index, MethodCallDefinition $methodCallDefinition)
    {
        $definitions         = $this->getMethodCallDefinitions();
        $definitions[$index] = $methodCallDefinition;

        $this->setMethodCallDefinitions($definitions);
    }
}