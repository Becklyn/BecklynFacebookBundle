<?php

namespace OAGM\FacebookBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Templating\EngineInterface;


class FacebookTwigExtension extends \Twig_Extension
{
    //region Helpers
    /**
     * @var FacebookService
     */
    protected $facebook;


    /**
     * The templating service
     *
     * @var EngineInterface
     */
    protected $templating;



    /**
     * @param ContainerInterface $container
     * @param FacebookService $facebook
     */
    public function __construct (ContainerInterface $container, FacebookService $facebook)
    {
        $this->prefix     = ($facebook instanceof PrefixedFacebookService) ? "{$facebook->getPrefix()}_" : "";
        $this->facebook   = $facebook;
        $this->templating = $container->get("templating");
    }



    /**
     * Returns a JSON representation of the facebook data
     *
     * @param string $redirectRoute
     * @param array $redirectRouteParameters
     *
     * @return string
     */
    public function fbData ($redirectRoute, array $redirectRouteParameters = array())
    {
        $data = array(
            'hasPermissions' => $this->facebook->hasPermissions(),
            'permissionsUrl' => $this->facebook->getPermissionsRequestUrl($redirectRoute, $redirectRouteParameters)
        );

        return json_encode($data);
    }



    /**
     * Returns the facebook app id
     *
     * @return string
     */
    public function fbAppId ()
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
            "{$this->prefix}fbData"  => new \Twig_Function_Method($this, 'fbData', array('is_safe' => array('html'))),
            "{$this->prefix}fbAppId" => new \Twig_Function_Method($this, 'fbAppId'),
        );
    }



    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName ()
    {
        return "{$this->prefix}_" . get_class($this);
    }



    /**
     * Renders a given template
     *
     * @param string $template
     * @param array $variables
     *
     * @return string
     */
    protected function render ($template, array $variables = array())
    {
        return $this->templating->render($template, $variables);
    }
    //endregion
}