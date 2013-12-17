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
    public function get ($key, $default = null)
    {
        $data = $this->data;
        $keys = explode(".", $key);

        foreach ($keys as $key)
        {
            if (is_array($data) && array_key_exists($key, $data))
            {
                $data = $data[$key];
            }
            else
            {
                return $default;
            }
        }

        return $data;
    }
}