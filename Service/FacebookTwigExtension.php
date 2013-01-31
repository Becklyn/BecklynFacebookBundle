<?php

namespace OAGM\FacebookBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;


class FacebookTwigExtension extends \Twig_Extension
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;


    /**
     * Returns the HTML for a like button
     *
     * @param string $url the url to like
     * @param array $options
     *
     * @return string
     */
    public function likeButton ($url, $options = array())
    {
        $width = isset($options["width"]) ? $options["width"] : 90;
        return '<div class="fb-like" data-href="' . rawurlencode($url) . '" data-send="false" data-layout="button_count" data-width="' . $width . '" data-show-faces="false"></div>';
    }



    /**
     * Returns a JSON representation of the facebook data
     *
     * @return string
     */
    public function fbData ()
    {
        /** @var $facebook FacebookService */
        $facebook = $this->container->get('facebook');

        $data = array(
            'hasPermissions' => $facebook->hasPermissions(),
            'permissionsUrl' => $facebook->getPermissionsRequestUrl()
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
        /** @var $facebook FacebookService */
        $facebook = $this->container->get('facebook');
        return $facebook->getAppId();
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
        /** @var $facebook FacebookService */
        $facebook = $this->container->get('facebook');
        return $facebook->truncateLikeDescriptionText($text, $length);
    }



    /**
     * Returns the profile picture URL
     *
     * @param string $facebookId
     *
     * @return string
     */
    public function fbProfilePicture ($facebookId)
    {
        return "https://graph.facebook.com/{$facebookId}/picture";
    }



    /**
     * Returns the facebook profile url
     *
     * @param $facebookId
     *
     * @return string
     */
    public function fbProfileUrl ($facebookId)
    {
        return "https://www.facebook.com/profile.php?id={$facebookId}";
    }

    /**
     * Returns the defined methods
     *
     * @return \Twig_Function[]
     */
    public function getFunctions ()
    {
        return array(
            'likeButton' => new \Twig_Function_Method($this, 'likeButton', array('is_safe' => array('html'))),
            'fbData'     => new \Twig_Function_Method($this, 'fbData', array('is_safe' => array('html'))),
            'fbAppId'     => new \Twig_Function_Method($this, 'fbAppId'),
            'fbProfilePicture'     => new \Twig_Function_Method($this, 'fbProfilePicture'),
            'fbProfileUrl' => new \Twig_Function_Method($this, 'fbProfileUrl'),
            'truncateLikeDescriptionText' => new \Twig_Function_Method($this, 'truncateLikeDescriptionText'),
        );
    }



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
        return __CLASS__;
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
}