<?php

namespace Becklyn\FacebookBundle\Data;

/**
 * Abstract data holder for usage in facebook data objects
 *
 * @package Becklyn\FacebookBundle\Entity
 */
class DataHolder
{
    /**
     * @var array
     */
    private $data;



    /**
     * @param array $data
     */
    public function __construct (array $data)
    {
        $this->data = $data;
    }



    /**
     * Returns the data by key
     *
     * @param string $key
     * @param mixed $default the default value
     *
     * @return string|null
     */
    public function getByKey ($key, $default = null)
    {
        return array_key_exists($key, $this->data)
            ? $this->data[$key]
            : $default;
    }
}