<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 12/02/15
 * Time: 20:28
 */

namespace CubicMushroom\Slim\ServiceManager\Exception\Config;

use CubicMushroom\Slim\ServiceManager\Exception\AbstractException;

class InvalidServiceConfigException extends AbstractException
{

    /**
     * @var string
     */
    protected $serviceName;

    /**
     * The problem config
     *
     * @var mixed
     */
    protected $serviceConfig;

    /**
     * Missing parameter, if provided
     *
     * @var string
     */
    protected $missingParameter;


    /**
     * {@inheritdoc}
     */
    protected static function getDefaultMessage(array $additionalProperties)
    {
        if (!isset($additionalProperties['serviceName'])) {
            $message = 'Service config not a valid array of service definitions';
        } else {
            $message = "Invalid config for '{$additionalProperties['serviceName']}' service";

            if (isset($additionalProperties['missingParameter'])) {
                $message .= " - Missing '{$additionalProperties['missingParameter']}' parameter'";
            }
        }

        return $message;
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
     * @return mixed
     */
    public function getServiceConfig()
    {
        return $this->serviceConfig;
    }


    /**
     * @param mixed $serviceConfig
     */
    public function setServiceConfig($serviceConfig)
    {
        $this->serviceConfig = $serviceConfig;
    }


    /**
     * @return string
     */
    public function getMissingParameter()
    {
        return $this->missingParameter;
    }


    /**
     * @param string $missingParameter
     */
    public function setMissingParameter($missingParameter)
    {
        $this->missingParameter = $missingParameter;
    }
}