<?php

namespace Becklyn\FacebookBundle\Data;

use Becklyn\FacebookBundle\Data\ApiUser;
use Becklyn\FacebookBundle\Data\Page;
use Becklyn\FacebookBundle\Data\RequestUser;

/**
 * Bundles the combined data of the facebook app
 *
 * Used to easily store all relevant data somewhere (like in the session)
 *
 * @package Becklyn\FacebookBundle\Data
 */
class CombinedFacebookData
{
    //region Fields
    /**
     * Contains the data related for the page
     *
     * @var Page
     */
    private $page;


    /**
     * Contains the data related to the user, based on the request
     *
     * @var RequestUser
     */
    private $requestUser;


    /**
     * Contains the data related to the user, based on an api call
     *
     * @var ApiUser
     */
    private $apiUser;
    //endregion



    //region Accessors
    /**
     * @param ApiUser|null $apiUser
     */
    public function setApiUser (ApiUser $apiUser = null)
    {
        $this->apiUser = $apiUser;
    }



    /**
     * @return ApiUser|null
     */
    public function getApiUser ()
    {
        return $this->apiUser;
    }



    /**
     * @param Page|null $page
     */
    public function setPage (Page $page = null)
    {
        $this->page = $page;
    }



    /**
     * @return Page|null
     */
    public function getPage ()
    {
        return $this->page;
    }



    /**
     * @param RequestUser|null $requestUser
     */
    public function setRequestUser (RequestUser $requestUser = null)
    {
        $this->requestUser = $requestUser;
    }



    /**
     * @return RequestUser|null
     */
    public function getRequestUser ()
    {
        return $this->requestUser;
    }
    //endregion
}