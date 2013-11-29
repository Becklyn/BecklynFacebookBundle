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
    public function likeButton ($url, array $options = array())
    {
        $defaultOptions = array(
            "href"       => rawurlencode($url),
            "send"       => "false",
            "layout"     => "button_count",
            "width"      => 90,
            "show-faces" => "false"
        );
        $options = array_merge($defaultOptions, $options);

        $attributes = array();
        foreach ($options as $key => $value)
        {
            if (is_bool($value))
            {
                $sanitized = $value ? "true" : "false";
            }
            else
            {
                $sanitized = (string) $value;
            }

            $attributes[] = "data-{$key}=\"{$sanitized}\"";
        }

        return '<div class="fb-like" ' . implode(' ', $attributes) . '></div>';
    }



    /**
     * Returns the profile picture URL
     *
     * @param string $facebookId
     *
     * @return string
     */
    public function fbProfileImage ($facebookId)
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
     * Returns the truncated like description text
     *
     * @param string $text
     * @param int $length
     *
     * @return string
     */
    public function truncateLikeDescriptionText ($text, $length = 80)
    {
        return FacebookService::truncateLikeDescriptionText($text, $length);
    }



    /**
     * Returns the defined methods
     *
     * @return \Twig_Function[]
     */
    public function getFunctions ()
    {
        return array(
            new \Twig_SimpleFunction('likeButton',                  array($this, 'likeButton'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('fbProfileImage',              array($this, 'fbProfileImage')),
            new \Twig_SimpleFunction('fbProfileUrl',                array($this, 'fbProfileUrl')),
            new \Twig_SimpleFunction('truncateLikeDescriptionText', array($this, 'truncateLikeDescriptionText')),
        );
    }



    /**
     * {@inheritdoc}
     */
    public function getName ()
    {
        return __CLASS__;
    }
}