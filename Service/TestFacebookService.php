<?php

namespace OAGM\FacebookBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Test service for debugging purposes
 */
class TestFacebookService extends BaseFacebookService
{
    public function __construct (ContainerInterface $container)
    {
        parent::__construct(
            $container,
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



    public function getFacebookPermalink ()
    {
        return isset($_GET['app_data']) ? $_GET['app_data'] : null;
    }
}