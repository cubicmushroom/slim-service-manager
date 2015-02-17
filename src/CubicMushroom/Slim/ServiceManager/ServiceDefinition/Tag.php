<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 16/02/15
 * Time: 23:16
 */

namespace CubicMushroom\Slim\ServiceManager\ServiceDefinition;

class Tag
{

    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $arguments;


    function __construct(array $tagConfig)
    {
        $this->setName($tagConfig[0]);

        if (isset($tagConfig[1])) {
            $this->arguments = $tagConfig[1];
        }
    }




    // -----------------------------------------------------------------------------------------------------------------
    // Getters and Setters
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
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
}