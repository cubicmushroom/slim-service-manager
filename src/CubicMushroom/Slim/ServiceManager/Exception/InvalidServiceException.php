<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 16/02/15
 * Time: 13:37
 */

namespace CubicMushroom\Slim\ServiceManager\Exception;

class InvalidServiceException extends AbstractException
{

    /**
     * Service in question
     *
     * @var mixed
     */
    protected $service;


    /**
     * {@inheritdoc}
     */
    protected static function getDefaultMessage(array $additionalProperties)
    {
        return 'The requested service is invalid';
    }


    // -----------------------------------------------------------------------------------------------------------------
    // Getters and Setters
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function getService()
    {
        return $this->service;
    }


    /**
     * @param mixed $service
     */
    public function setService($service)
    {
        $this->service = $service;
    }
}