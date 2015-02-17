<?php
/**
 * Created by PhpStorm.
 * User: toby
 * Date: 16/02/15
 * Time: 23:59
 */

namespace CubicMushroom\Slim\ServiceManager\ServiceDefinition;

use CubicMushroom\Slim\ServiceManager\Exception\Config\InvalidTagException;
use Slim\Helper\Set;

/**
 * Class TagCollection
 *
 * @package CubicMushroom\Slim\ServiceManager\ServiceDefinition
 */
class TagSet extends Set
{
    /**
     * @param string $key
     * @param null   $default
     *
     * @return Tag
     */
    public function get($key, $default = null)
    {
        return parent::get($key, $default);
    }


    /**
     * Add data to set
     *
     * @param Tag[] $tags Key-value array of data to append to this set
     *
     * @throws InvalidTagException if a non-Tag item is passed in
     */
    public function replace($tags)
    {
        foreach ($tags as $tag) {
            if (!$tag instanceof Tag) {
                throw InvalidTagException::build([], ['invalidTag' => $tag]);
            }
        }

        parent::replace($tags);
    }


    /**
     * @param string $key
     * @param Tag    $tag
     *
     * @throws InvalidTagException if the passed $value is not a Tag
     */
    public function set($key, $tag)
    {
        if ($tag instanceof Tag) {
            throw InvalidTagException::build([], ['invalidTag' => $tag]);
        }

        parent::set($key, $tag);
    }
}