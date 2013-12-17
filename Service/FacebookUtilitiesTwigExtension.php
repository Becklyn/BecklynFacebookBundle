<?php

namespace Becklyn\FacebookBundle\Service;

/**
 * Provides generic functionality for applications which work with Facebook
 *
 * @package Becklyn\FacebookBundle\Service
 */
class FacebookUtilitiesTwigExtension extends \Twig_Extension
{
    /**
     * @var UtilitiesService
     */
    private $utilitiesService;



    /**
     * @param UtilitiesService $utilitiesService
     */
    public function __construct (UtilitiesService $utilitiesService)
    {
        $this->utilitiesService = $utilitiesService;
    }



    /**
     * Returns the defined methods
     *
     * @return \Twig_Function[]
     */
    public function getFunctions ()
    {
        return array(
            new \Twig_SimpleFunction('fb_likeButton',                  array($this->utilitiesService, 'likeButton'), array('is_safe' => array('html'))),
            new \Twig_SimpleFunction('fb_profileImage',                array($this->utilitiesService, 'fbProfileImage')),
            new \Twig_SimpleFunction('fb_profileUrl',                  array($this->utilitiesService, 'fbProfileUrl')),
            new \Twig_SimpleFunction('fb_truncateLikeDescriptionText', array($this->utilitiesService, 'truncateLikeDescriptionText')),
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