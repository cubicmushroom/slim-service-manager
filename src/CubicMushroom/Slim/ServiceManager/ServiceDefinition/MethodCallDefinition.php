<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 13/02/15
 * Time: 12:20
 */

namespace CubicMushroom\Slim\ServiceManager\ServiceDefinition;

use CubicMushroom\Slim\ServiceManager\Exception\Config\InvalidServiceCallConfigException;
use CubicMushroom\Slim\ServiceManager\Exception\InvalidServiceException;
use CubicMushroom\Slim\ServiceManager\ServiceDefinition;

class MethodCallDefinition
{

    /**
     * @var ServiceDefinition
     */
    protected $serviceDefinition;

    /**
     * Method to be called
     *
     * @var string
     */
    protected $method;

    /**
     * Array of arguments to pass to method
     *
     * @var array
     */
    protected $arguments;


    /**
     * Splits and stores the method and arguments
     *
     * $callDefinition should contain a string for the method to call as the first argument
     * $callDefinition may contain an array as the second
     *
     * @param ServiceDefinition $serviceDefinition The service definition that this belongs to
     * @param array             $callDefinition    Call definition
     */
    function __construct(ServiceDefinition $serviceDefinition, array $callDefinition)
    {
        $this->setServiceDefinition($serviceDefinition);
        $this->setMethod($callDefinition[0]);

        if (isset($callDefinition[1])) {
            $this->setArguments($callDefinition[1]);
        }
    }


    // -----------------------------------------------------------------------------------------------------------------
    // Invoker
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Calls the method of this call definition on the passed Service object
     *
     * @param mixed $service
     *
     * @throws InvalidServiceException if the service passed is invalid
     * @throws InvalidServiceCallConfigException if the requested method does not exist
     */
    public function __invoke($service)
    {
        if (!is_object($service)) {
            throw InvalidServiceException::build([], ['service' => $service]);
        }

        $callable = [$service, $this->getMethod()];
        if (!is_callable($callable)) {
            throw InvalidServiceCallConfigException::build([], ['methodName' => $this->getMethod()]);
        }

        $args = $this->getArguments();
        foreach ($args as $arg_i => $arg) {
            if ('@' === substr($arg, 0, 1)) {
                $args[$arg_i] = $this->getService($arg);
            }
        }

        call_user_func_array($callable, $args);
    }


    // -----------------------------------------------------------------------------------------------------------------
    // Helper methods
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @param $arg
     *
     * @return mixed
     */
    protected function getService($arg)
    {
        return $this->getServiceDefinition()->getServiceManager()->getService($arg);
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Getters and Setters
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return ServiceDefinition
     */
    public function getServiceDefinition()
    {
        return $this->serviceDefinition;
    }


    /**
     * @param ServiceDefinition $serviceDefinition
     */
    public function setServiceDefinition($serviceDefinition)
    {
        $this->serviceDefinition = $serviceDefinition;
    }


    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }


    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
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
    public function setArguments($arguments)
    {
        $this->arguments = $arguments;
    }
}