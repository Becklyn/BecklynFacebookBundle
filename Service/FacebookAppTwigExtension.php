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
     * @var FacebookAppModel
     */
    private $facebookModel;

    /**
     * @var string
     */
    private $prefix;


    /**
     * @param FacebookAppModel $facebookAppModel
     * @param bool             $usePrefix Flag whether all defined functions should be prefixed
     */
    public function __construct (FacebookAppModel $facebookAppModel, $usePrefix = false)
    {
        $this->facebookModel = $facebookAppModel;
        $this->prefix = $usePrefix
            ? "fb_{$this->facebookModel->getSessionIdentifier()}_"
            : "fb_";
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
            "hasPermissions" => $this->facebookModel->hasPermissions(),
            "permissionsUrl" => $this->facebookModel->getPermissionsRequestUrl($redirectRoute, $redirectRouteParameters)
        );
    }


    /**
     * Returns the defined methods
     *
     * @return \Twig_Function[]
     */
    public function getFunctions ()
    {
        return array(
            new \Twig_SimpleFunction("{$this->prefix}permissionsData", [$this, "permissionsData"], ["is_safe" => ["html"]]),
            new \Twig_SimpleFunction("{$this->prefix}appId",           [$this->facebookModel, "getAppId"]),
            new \Twig_SimpleFunction("{$this->prefix}hasPermissions",  [$this->facebookModel, "hasPermissions"]),
            new \Twig_SimpleFunction("{$this->prefix}permissionsUrl",  [$this->facebookModel, "getPermissionsRequestUrl"]),
            new \Twig_SimpleFunction("{$this->prefix}hasLikedPage",    [$this->facebookModel, "hasLikedPage"]),
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
