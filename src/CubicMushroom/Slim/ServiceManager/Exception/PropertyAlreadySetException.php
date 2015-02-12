<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 12/02/15
 * Time: 19:19
 */

namespace CubicMushroom\Slim\ServiceManager\Exception;

/**
 * Class PropertyAlreadySetException
 *
 * @package CubicMushroom\Slim\ServiceManager\Exception
 */
class PropertyAlreadySetException extends AbstractException
{

    /**
     * Property in question
     *
     * @var string
     */
    protected $property;


    /**
     * @param array $additionalProperties
     *
     * @return string
     */
    protected static function getDefaultMessage(array $additionalProperties)
    {
        return "Property '{$additionalProperties['property']}' can only be set once, and is already set";
    }


    // -----------------------------------------------------------------------------------------------------------------
    // Getters and Setters
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return string
     */
    public function getProperty()
    {
        return $this->property;
    }


    /**
     * @param string $property
     */
    public function setProperty($property)
    {
        $this->property = $property;
    }
}