<?php

namespace Becklyn\FacebookBundle\Model;
use Becklyn\FacebookBundle\Data\ApiUser;

/**
 * Debug Facebook App Model
 *
 * @package Becklyn\FacebookBundle\Model
 */
class DebugFacebookAppModel extends FacebookAppModel
{
    /**
     * Returns extensive debug output
     *
     * @param bool $echo flag, whether the debug output should be echoed
     *
     * @return bool|string
     */
    public function debug ($echo = true)
    {
        print_r(
            array(
                "signedRequest"            => $this->facebook->getSignedRequest(),
                "isInFacebookButNotInPage" => $this->isInFacebookButNotInPage(),
                "hasPermissions"           => $this->hasPermissions(),
                "hasLiked"                 => $this->hasLikedPage(),
                "pageTabUrl"               => $this->getPageTabUrl(),
                "appId"                    => $this->getAppId(),
                "requestUser"              => $this->getRequestUser(),
                "page"                     => $this->getPage(),
                "apiUser"                  => $this->getApiUser(),
                "app_data"                 => $this->getAppData(),
                "sessionIdentifier"        => $this->getSessionIdentifier(),
            ),
            !$echo
        );
    }
}