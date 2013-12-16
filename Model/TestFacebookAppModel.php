<?php

namespace Becklyn\FacebookBundle\Model;

use Becklyn\FacebookBundle\Data\ApiUser;
use Becklyn\FacebookBundle\Data\Page;
use Becklyn\FacebookBundle\Data\RequestUser;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;


/**
 * Test service for debugging purposes
 */
class TestFacebookAppModel extends DebugFacebookAppModel
{
    // Default values of dummy values
    private $country                  = "de";
    private $locale                   = "de_DE";
    private $age                      = array("min" => 21);
    private $userHasLiked             = true;
    private $userIsAdmin              = false;
    private $hasPermissions           = true;
    private $isInFacebookButNotInPage = false;



    /**
     * {@inheritdoc}
     */
    public function __construct (\Facebook $facebook, SessionInterface $session, RouterInterface $router)
    {
        parent::__construct(
            $facebook,
            $session,
            $router,
            ""
        );
    }



    //region Dummy Value Getters
    /**
     * Returns a dummy api user
     *
     * @return ApiUser
     */
    public function getApiUser ()
    {
        return new ApiUser(array(
            "id"         => 123456,
            "name"       => "Test User",
            "first_name" => "Test",
            "last_name"  => "User",
            "link"       => "http://www.google.de",
            "username"   => "test_user",
            "email"      => "test@becklyn.com",
            "locale"     => $this->locale
        ));
    }



    /**
     * Returns a dummy request user
     *
     * @return RequestUser
     */
    public function getRequestUser ()
    {
        return new RequestUser(array(
            "country" => $this->country,
            "locale"  => $this->locale,
            "age"     => $this->age
        ));
    }



    /**
     * Returns a dummy page
     *
     * @return Page
     */
    public function getPage ()
    {
        return new Page(array(
            "liked" => $this->userHasLiked,
            "id"    => 123456,
            "admin" => $this->userIsAdmin
        ));
    }



    /**
     * {@inheritdoc}
     */
    public function hasPermissions ()
    {
        return $this->hasPermissions;
    }



    /**
     * {@inheritdoc}
     */
    public function isInFacebookButNotInPage ()
    {
        return $this->isInFacebookButNotInPage;
    }



    /**
     * {@inheritdoc}
     */
    public function getAppData ()
    {
        return isset($_GET['app_data']) ? $_GET['app_data'] : null;
    }
    //endregion



    //region Setters for most important dummy values
    /**
     * @param array $age
     */
    public function setAge ($age)
    {
        $this->age = $age;
    }



    /**
     * @param string $country
     */
    public function setCountry ($country)
    {
        $this->country = $country;
    }



    /**
     * @param string $locale
     */
    public function setLocale ($locale)
    {
        $this->locale = $locale;
    }



    /**
     * @param boolean $userHasLiked
     */
    public function setUserHasLiked ($userHasLiked)
    {
        $this->userHasLiked = $userHasLiked;
    }



    /**
     * @param boolean $userIsAdmin
     */
    public function setUserIsAdmin ($userIsAdmin)
    {
        $this->userIsAdmin = $userIsAdmin;
    }



    /**
     * @param boolean $hasPermissions
     */
    public function setHasPermissions ($hasPermissions)
    {
        $this->hasPermissions = $hasPermissions;
    }



    /**
     * @param boolean $isInFacebookButNotInPage
     */
    public function setIsInFacebookButNotInPage ($isInFacebookButNotInPage)
    {
        $this->isInFacebookButNotInPage = $isInFacebookButNotInPage;
    }
    //endregion
}