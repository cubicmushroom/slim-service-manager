<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 12/02/15
 * Time: 20:33
 */

namespace CubicMushroom\Slim\ServiceManager\Exception\Config;

class InvalidServiceCallConfigException extends InvalidServiceConfigException
{

    /**
     * The problem service 'call' config
     *
     * @var mixed
     */
    protected $callConfig;

    /**
     * @var mixed
     */
    protected $callConfigIndex;

    /**
     * Method being passed
     *
     * @var string
     */
    protected $invalidArguments;


    /**
     * {@inheritdoc}
     */
    protected static function getDefaultMessage(array $additionalProperties)
    {
        $message = "Invalid 'call' config for '{$additionalProperties['serviceName']}' service";

        if (isset($additionalProperties['callConfigIndex'])) {
            $message .= " - Call config index '{$additionalProperties['callConfigIndex']}'";
        }

        if (isset($additionalProperties['invalidArguments'])) {
            $message .= " - Arguments must be an array";
        }

        return $message;
    }


    // -----------------------------------------------------------------------------------------------------------------
    // Getters and Setters
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function getCallConfig()
    {
        return $this->callConfig;
    }


    /**
     * @param mixed $callConfig
     */
    public function setCallConfig($callConfig)
    {
        $this->callConfig = $callConfig;
    }


    /**
     * @return mixed
     */
    public function getCallConfigIndex()
    {
        return $this->callConfigIndex;
    }


    /**
     * @param mixed $callConfigIndex
     */
    public function setCallConfigIndex($callConfigIndex)
    {
        $this->callConfigIndex = $callConfigIndex;
    }


    /**
     * @return string
     */
    public function getInvalidArguments()
    {
        return $this->invalidArguments;
    }


    /**
     * @param string $invalidArguments
     */
    public function setInvalidArguments($invalidArguments)
    {
        $this->invalidArguments = $invalidArguments;
    }
}