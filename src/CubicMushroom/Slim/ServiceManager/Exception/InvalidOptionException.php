<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 12/02/15
 * Time: 19:09
 */

namespace CubicMushroom\Slim\ServiceManager\Exception;

class InvalidOptionException extends AbstractException
{

    /**
     * Name of the option in question
     *
     * @var string
     */
    protected $option;


    /**
     * @param array $additionalProperties
     *
     * @return string|void
     */
    protected static function getDefaultMessage(array $additionalProperties)
    {
        return "Invalid options '{$additionalProperties['option']}' passed";
    }


    // -----------------------------------------------------------------------------------------------------------------
    // Getters and Setters
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return string
     */
    public function getOption()
    {
        return $this->option;
    }


    /**
     * @param string $option
     */
    public function setOption($option)
    {
        $this->option = $option;
    }
}