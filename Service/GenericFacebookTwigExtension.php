<?php

namespace OAGM\FacebookBundle\Service;

class GenericFacebookTwigExtension extends \Twig_Extension
{
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
            'fbProfilePicture'     => new \Twig_Function_Method($this, 'fbProfilePicture'),
            'fbProfileUrl' => new \Twig_Function_Method($this, 'fbProfileUrl'),
        );
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
}