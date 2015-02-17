<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 17/02/15
 * Time: 00:02
 */

namespace CubicMushroom\Slim\ServiceManager\Exception\Config;

use CubicMushroom\Slim\ServiceManager\Exception\AbstractException;

/**
 * Class InvalidTagException
 *
 * Exception thrown when expecting a valid tag, but an invalid one is received
 *
 * @package CubicMushroom\Slim\ServiceManager\Exception\Config
 */
class InvalidTagException extends AbstractException
{

    /**
     * @var mixed
     */
    protected $invalidTag;


    /**
     * {@inheritdoc}
     */
    protected static function getDefaultMessage(array $additionalProperties)
    {
        return 'Invalid tag';
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Getters and Setters
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * @return mixed
     */
    public function getInvalidTag()
    {
        return $this->invalidTag;
    }


    /**
     * @param mixed $invalidTag
     */
    public function setInvalidTag($invalidTag)
    {
        $this->invalidTag = $invalidTag;
    }
}