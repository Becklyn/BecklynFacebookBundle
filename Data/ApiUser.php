<?php

namespace Becklyn\FacebookBundle\Data;

/**
 * Value object to hold the user data provided by the /me api
 *
 * @package Becklyn\Entity
 */
class ApiUser extends DataHolder
{
    const GENDER_MALE   = "male";
    const GENDER_FEMALE = "female";



    /**
     * Returns the facebook id of the user
     *
     * @return string
     */
    public function getId ()
    {
        return $this->get("id");
    }



    /**
     * Returns the full name of the user
     *
     * @return string
     */
    public function getName ()
    {
        return $this->get("name");
    }



    /**
     * Returns the first name of the user
     *
     * @return string
     */
    public function getFirstName ()
    {
        return $this->get("first_name");
    }



    /**
     * Returns the last name of the user
     *
     * @return string
     */
    public function getLastName ()
    {
        return $this->get("last_name");
    }



    /**
     * Returns the link to the profile
     *
     * @return string
     */
    public function getLink ()
    {
        return $this->get("link");
    }



    /**
     * Returns the facebook username
     *
     * @return string
     */
    public function getUsername ()
    {
        return $this->get("username");
    }



    /**
     * Returns the birthday, if the permissions are given
     *
     * @return \DateTime|null
     */
    public function getBirthday ()
    {
        if (!is_null($birthday = $this->get("birthday")))
        {
            return \DateTime::createFromFormat("m/d/Y", $birthday);
        }

        return null;
    }



    /**
     * Returns the gender
     *
     * @return string
     */
    public function getGender ()
    {
        return $this->get("gender");
    }



    /**
     * Returns the email (if the permissions are given)
     *
     * @return null|string
     */
    public function getEmail ()
    {
        return $this->get("email");
    }



    /**
     * Returns the locale time
     *
     * @return string
     */
    public function getLocale ()
    {
        return $this->get("locale");
    }
}