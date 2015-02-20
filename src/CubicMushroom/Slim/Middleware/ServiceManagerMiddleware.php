<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 20/02/15
 * Time: 14:03
 */

namespace CubicMushroom\Slim\Middleware;

use CubicMushroom\Slim\ServiceManager\ServiceManager;
use Slim\Middleware;
use Slim\Slim;

class ServiceManagerMiddleware extends Middleware
{

    /**
     * Array of options
     *
     * @var array
     */
    protected $options = [];

    // -----------------------------------------------------------------------------------------------------------------
    // Constructor
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Loads services into Slim App based on the content of the 'services' config settings
     *
     * @param array $options [optional] Array of options for the setup of the ServiceManagers
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }


    // -----------------------------------------------------------------------------------------------------------------
    // Middleware methods
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Call
     *
     * Sets up the services as defined within the $container['settings']['services'] array
     */
    public function call()
    {
        new ServiceManager($this->getApp(), $this->getOptions());

        $this->next->call();
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
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }


    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }
}