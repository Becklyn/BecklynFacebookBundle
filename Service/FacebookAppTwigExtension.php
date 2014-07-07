<?php

namespace Becklyn\FacebookBundle\Service;

use Becklyn\FacebookBundle\Model\FacebookAppModel;


/**
 * Facebook twig extension that bundles the most frequently used functionality of a facebook app model
 *
 * @package Becklyn\FacebookBundle\Service
 */
class FacebookAppTwigExtension extends \Twig_Extension
{
    /**
     * @var FacebookAppModel
     */
    protected $facebook;



    /**
     * @param FacebookAppModel $facebook
     * @param bool $usePrefix Flag whether all defined functions should be prefixed
     */
    public function __construct (FacebookAppModel $facebook, $usePrefix = false)
    {
        $this->prefix   = $usePrefix ? "fb_{$facebook->getSessionIdentifier()}_" : "fb_";
        $this->facebook = $facebook;
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
        return array(
            "hasPermissions" => $this->facebook->hasPermissions(),
            "permissionsUrl" => $this->facebook->getPermissionsRequestUrl($redirectRoute, $redirectRouteParameters)
        );
    }



    /**
     * Returns the facebook app id
     *
     * @return string
     */
    public function appId ()
    {
        return $this->facebook->getAppId();
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
            new \Twig_SimpleFunction("{$this->prefix}hasPermissions",  array($this->facebook, "hasPermissions")),
            new \Twig_SimpleFunction("{$this->prefix}permissionsUrl",  array($this->facebook, "getPermissionsRequestUrl")),
            new \Twig_SimpleFunction("{$this->prefix}hasLikedPage",    array($this->facebook, "hasLikedPage")),
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
