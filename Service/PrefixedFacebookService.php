<?php


namespace OAGM\FacebookBundle\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class PrefixedFacebookService
 *
 * @package OAGM\FacebookBundle\Service
 */
class PrefixedFacebookService extends FacebookService
{
    /**
     * The bundle prefix
     *
     * @var string
     */
    private $prefix;



    /**
     * Constructs a new facebook service with a given bundle prefix
     *
     * @param SessionInterface $session
     * @param string $prefix
     * @param string $appId
     * @param string $appSecret
     * @param string $pageUrl
     * @param array $requiredPermissions
     */
    public function __construct (SessionInterface $session, $prefix, $appId, $appSecret, $pageUrl, array $requiredPermissions)
    {
        $this->prefix = $prefix;

        parent::__construct(
            $session,
            $appId,
            $appSecret,
            $pageUrl,
            $requiredPermissions
        );
    }



    /**
     * {@inheritDoc}
     */
    protected function getBaseDataSessionIdentifier ()
    {
        return "{$this->prefix}." . parent::getBaseDataSessionIdentifier();
    }



    /**
     * {@inheritDoc}
     */
    protected function getLikedPageSessionIdentifier ()
    {
        return "{$this->prefix}." . parent::getLikedPageSessionIdentifier();
    }



    /**
     * @return string
     */
    public function getPrefix ()
    {
        return $this->prefix;
    }
}