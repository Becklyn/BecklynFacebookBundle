<?php

namespace OAGM\FacebookBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;


abstract class FacebookTwigExtension extends \Twig_Extension
{
    /**
     * Returns the facebook service
     *
     * @return FacebookService
     */
    protected function getFacebook ()
    {
        return $this->container->get("{$this->getPrefix()}.facebook");
    }



    /**
     * Returns the bundle prefix
     *
     * @return mixed
     */
    abstract protected function getPrefix();


    /**
     * Returns a JSON representation of the facebook data
     *
     * @return string
     */
    public function fbData ()
    {
        $facebook = $this->getFacebook();
        $permissionsCallbackRoute = $this->container->getParameter("{$this->getPrefix()}.facebook.permissions_callback_route");

        $data = array(
            'hasPermissions' => $facebook->hasPermissions(),
            'permissionsUrl' => $facebook->getPermissionsRequestUrl($permissionsCallbackRoute)
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
        return $this->getFacebook()->getAppId();
    }



    /**
     * Truncates the like description text
     *
     * @param string $text
     * @param int $length
     *
     * @return string
     */
    public function truncateLikeDescriptionText ($text, $length = 80)
    {
        return $this->getFacebook()->truncateLikeDescriptionText($text, $length);
    }



    /**
     * Returns the defined methods
     *
     * @return \Twig_Function[]
     */
    public function getFunctions ()
    {
        return array(
            "{$this->getPrefix()}_fbData"                      => new \Twig_Function_Method($this, 'fbData', array('is_safe' => array('html'))),
            "{$this->getPrefix()}_fbAppId"                     => new \Twig_Function_Method($this, 'fbAppId'),
            "{$this->getPrefix()}_truncateLikeDescriptionText" => new \Twig_Function_Method($this, 'truncateLikeDescriptionText'),
        );
    }


    //region Helpers
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;



    /**
     * @param ContainerInterface $container
     */
    public function __construct (ContainerInterface $container)
    {
        $this->container = $container;
    }



    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName ()
    {
        return "{$this->getPrefix()}_" . get_class($this);
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
        /** @var $twig \Symfony\Bridge\Twig\TwigEngine */
        $twig = $this->container->get("templating");

        return $twig->render($template, $variables);
    }
    //endregion
}