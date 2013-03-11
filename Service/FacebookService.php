<?php


namespace OAGM\FacebookBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class FacebookService
 *
 * @package OAGM\FacebookBundle\Service
 */
class FacebookService extends BaseFacebookService
{
    /**
     * The bundle prefix
     *
     * @var string
     */
    private $bundlePrefix;



    /**
     * Constructs a new facebook service with a given bundle prefix
     *
     * @param ContainerInterface$container
     * @param string $bundlePrefix
     */
    public function __construct (ContainerInterface $container, $bundlePrefix)
    {
        $this->bundlePrefix = $bundlePrefix;

        parent::__construct(
            $container,
            $container->getParameter("{$this->bundlePrefix}.facebook.app_id"),
            $container->getParameter("{$this->bundlePrefix}.facebook.app_secret"),
            $container->getParameter("{$this->bundlePrefix}.facebook.page_url"),
            $container->getParameter("{$this->bundlePrefix}.facebook.required_permissions")
        );
    }



    /**
     * {@inheritDoc}
     */
    public function getPermissionsRequestUrl ($pathArguments = array())
    {
        return parent::getPermissionsRequestUrl("{$this->bundlePrefix}_fb_permissions_callback", $pathArguments);
    }



    /**
     * {@inheritDoc}
     */
    protected function getBaseDataSessionIdentifier ()
    {
        return "{$this->bundlePrefix}." . parent::getBaseDataSessionIdentifier();
    }



    /**
     * {@inheritDoc}
     */
    protected function getLikedPageSessionIdentifier ()
    {
        return "{$this->bundlePrefix}." . parent::getLikedPageSessionIdentifier();
    }
}