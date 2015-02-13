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
     * @param string $serviceName
     * @param array  $config
     */
    function __construct($serviceName, array $config)
    {
        $this->validateServiceConfig($config, $serviceName);

        $this->setServiceName($serviceName);
        $this->setConfig($config);

        $this->setClass($config['class']);

        if (isset($config['arguments'])) {
            $this->setArguments($config['arguments']);
        }

        if (isset($config['calls'])) {
            foreach ($config['calls'] as $call_i => $call) {
                $this->addCallDefinition($call_i, new MethodCallDefinition($call));
            }
        }
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