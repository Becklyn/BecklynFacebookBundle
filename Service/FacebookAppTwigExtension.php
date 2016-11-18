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
    private $usePrefix;


    /**
     * @var string
     */
    private $facebookService;

    /**
     * @var ContainerInterface
     */
    private $container;



    /**
     * @param ContainerInterface $container
     * @param string             $facebookService
     * @param bool               $usePrefix Flag whether all defined functions should be prefixed
     */
    public function __construct (ContainerInterface $container, $facebookService, $usePrefix = false)
    {
        $this->container = $container;
        $this->facebookService = $facebookService;
        $this->usePrefix = $usePrefix;
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
        $facebook = $this->getFacebookService();

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
        return $this->getFacebookService()->getAppId();
    }



    /**
     * Returns whether the user has already given permissions for the app
     *
     * @return bool
     */
    public function hasPermissions ()
    {
        return $this->getFacebookService()->hasPermissions();
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
        return $this->getFacebookService()->getPermissionsRequestUrl($redirectRoute, $redirectRouteParameters);
    }



    /**
     * Returns whether the user has liked the page
     *
     * @return bool
     */
    public function hasLikedPage ()
    {
        return $this->getFacebookService()->hasLikedPage();
    }



    /**
     * Returns the defined methods
     *
     * @return \Twig_Function[]
     */
    public function getFunctions ()
    {
        $prefix = $this->getPrefix();

        return array(
            new \Twig_SimpleFunction("{$prefix}permissionsData", array($this, "permissionsData"), array("is_safe" => array("html"))),
            new \Twig_SimpleFunction("{$prefix}appId",           array($this, "appId")),
            new \Twig_SimpleFunction("{$prefix}hasPermissions",  array($this, "hasPermissions")),
            new \Twig_SimpleFunction("{$prefix}permissionsUrl",  array($this, "getPermissionsRequestUrl")),
            new \Twig_SimpleFunction("{$prefix}hasLikedPage",    array($this, "hasLikedPage")),
        );
    }



    /**
     * @return string
     */
    private function getPrefix ()
    {
        return $this->usePrefix ? "fb_{$this->getFacebookService()->getSessionIdentifier()}_" : "fb_";
    }



    /**
     * @return FacebookAppModel
     */
    private function getFacebookService ()
    {
        return $this->container->get($this->facebookService);
    }



    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName ()
    {
        $class = get_class($this);
        return "{$this->getPrefix()}_{$class}";
    }
}
