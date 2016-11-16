<?php

namespace Becklyn\FacebookBundle\Service;

use Becklyn\FacebookBundle\Model\FacebookAppModel;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Facebook twig extension that bundles the most frequently used functionality of a facebook app model
 *
 * @package Becklyn\FacebookBundle\Service
 */
class FacebookAppTwigExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    private $prefix;

    /**
     * @var ContainerInterface
     */
    private $container;



    /**
     * @param ContainerInterface $container
     * @param bool               $usePrefix Flag whether all defined functions should be prefixed
     */
    public function __construct (ContainerInterface $container, $usePrefix = false)
    {
        $this->prefix = $usePrefix ? "fb_{$facebook->getSessionIdentifier()}_" : "fb_";
        $this->container = $container;
    }



    /**
     * Returns a JSON representation of the facebook data
     *
     * @param string $redirectRoute
     * @param array $redirectRouteParameters
     *
     * @return array
     */
    public function permissionsData ($redirectRoute, array $redirectRouteParameters = array())
    {
        $facebook = $this->container->get("brax.facebook");

        return array(
            "hasPermissions" => $facebook->hasPermissions(),
            "permissionsUrl" => $facebook->getPermissionsRequestUrl($redirectRoute, $redirectRouteParameters)
        );
    }



    /**
     * Returns the facebook app id
     *
     * @return string
     */
    public function appId ()
    {
        return $this->container->get("brax.facebook")->getAppId();
    }



    /**
     * Returns whether the user has already given permissions for the app
     *
     * @return bool
     */
    public function hasPermissions ()
    {
        return $this->container->get("brax.facebook")->hasPermissions();
    }



    /**
     * Returns the request URL for the permissions login
     *
     * @param string $redirectRoute
     * @param array  $redirectRouteParameters
     *
     * @return string
     */
    public function getPermissionsRequestUrl ($redirectRoute, $redirectRouteParameters = array())
    {
        return $this->container->get("brax.facebook")->getPermissionsRequestUrl($redirectRoute, $redirectRouteParameters);
    }



    /**
     * Returns whether the user has liked the page
     *
     * @return bool
     */
    public function hasLikedPage ()
    {
        return $this->container->get("brax.facebook")->hasLikedPage();
    }



    /**
     * Returns the defined methods
     *
     * @return \Twig_Function[]
     */
    public function getFunctions ()
    {
        return array(
            new \Twig_SimpleFunction("{$this->prefix}permissionsData", array($this, "permissionsData"), array("is_safe" => array("html"))),
            new \Twig_SimpleFunction("{$this->prefix}appId",           array($this, "appId")),
            new \Twig_SimpleFunction("{$this->prefix}hasPermissions",  array($this, "hasPermissions")),
            new \Twig_SimpleFunction("{$this->prefix}permissionsUrl",  array($this, "getPermissionsRequestUrl")),
            new \Twig_SimpleFunction("{$this->prefix}hasLikedPage",    array($this, "hasLikedPage")),
        );
    }



    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName ()
    {
        $class = get_class($this);
        return "{$this->prefix}_{$class}";
    }
}
