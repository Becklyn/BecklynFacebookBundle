<?php

namespace OAGM\FacebookBundle\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;


/**
 * Test service for debugging purposes
 */
class TestFacebookService extends FacebookService
{
    public function __construct (SessionInterface $session)
    {
        parent::__construct(
            $session,
            $this->getAppId(),
            "123",
            "https://www.facebook.com/onanygivenmonday",
            array()
        );
    }


    public function getUserId ()
    {
        return 143524653;
    }



    public function getUserName ()
    {
        return "Test User";
    }



    public function getUserEmail ()
    {
        return 'test@test.de';
    }



    public function hasPermissions ()
    {
        return true;
    }



    public function hasLikedPage ()
    {
        return true;
    }



    public function getAppData ()
    {
        return isset($_GET['app_data']) ? $_GET['app_data'] : null;
    }



    public function getCountryOfUser ()
    {
        return "de_DE";
    }



    public function getLocaleOfUser ()
    {
        return "de";
    }
}