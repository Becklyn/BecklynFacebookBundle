<?php

namespace Becklyn\FacebookBundle\Data;

/**
 * Value object to hold the page related data
 *
 * @package Becklyn\FacebookBundle\Data
 */
class Page extends DataHolder
{
     /**
     * Returns, whether the user has liked the page
     *
     * @return boolean
     */
    public function isLikedByUser ()
    {
        return $this->getByKey("liked");
    }



    /**
     * Returns the id of the page
     *
     * @return string
     */
    public function getPageId ()
    {
        return $this->getByKey("id");
    }



    /**
     * Returns, whether the user is an admin of the page
     *
     * @return boolean
     */
    public function containsUserAsAdmin ()
    {
        return $this->getByKey("admin");
    }
}