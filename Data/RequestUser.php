<?php

namespace Becklyn\FacebookBundle\Data;

/**
 * Value object containing the user data, one automatically retrieves from the (signed) request
 *
 * @package Becklyn\Entity
 */
class RequestUser extends DataHolder
{
    /**
     * Returns the age settings for the user
     *
     * @return null|string
     */
    public function getAge ()
    {
        return $this->get("age");
    }



    /**
     * Returns the country of the user, for example "de"
     *
     * @return null|string
     */
    public function getCountry ()
    {
        return $this->get("country");
    }



    /**
     * Returns the locale of the user, for example "de_DE"
     *
     * @return null|string
     */
    public function getLocale ()
    {
        return $this->get("locale");
    }
}