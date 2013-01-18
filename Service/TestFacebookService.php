<?php

namespace OAGM\FacebookBundle\Service;


/**
 * Test service for debugging purposes
 */
class TestFacebookService extends FacebookService
{
    public function getFacebookUserId ()
    {
        return 143524653;
    }



    public function getFacebookUserName ()
    {
        return "Test User";
    }



    public function getEmail ()
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