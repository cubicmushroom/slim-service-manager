<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 13/02/15
 * Time: 12:20
 */

namespace CubicMushroom\Slim\ServiceManager\ServiceDefinition;

class MethodCallDefinition
{

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
     * @param array $callDefinition Call definition
     *
     * @internal param $method
     * @internal param array $arguments
     */
    function __construct(array $callDefinition)
    {
        $this->setMethod($callDefinition[0]);

        if (isset($callDefinition[1])) {
            $this->setArguments($callDefinition[1]);
        }
    }


    // -----------------------------------------------------------------------------------------------------------------
    // Getters and Setters
    // -----------------------------------------------------------------------------------------------------------------

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