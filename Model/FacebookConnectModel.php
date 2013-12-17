<?php

namespace Becklyn\FacebookBundle\Model;

use Becklyn\FacebookBundle\Data\ApiUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

class FacebookConnectModel
{
    /**
     * @var \Facebook
     */
    private $facebook;


    /**
     * @var RouterInterface
     */
    private $router;


    /**
     * @var FacebookGraphModel
     */
    private $graphModel;


    /**
     * The list of required permissions
     *
     * @var string[]
     */
    private $requiredPermissions;



    /**
     * @param \Facebook $facebook
     * @param FacebookGraphModel $graphModel
     * @param RouterInterface $router
     * @param string[] $requiredPermissions
     */
    public function __construct (\Facebook $facebook, FacebookGraphModel $graphModel, RouterInterface $router, array $requiredPermissions = array("email"))
    {
        $this->facebook            = $facebook;
        $this->graphModel          = $graphModel;
        $this->router              = $router;
        $this->requiredPermissions = $requiredPermissions;
    }


    /**
     * Returns the permissions request url
     *
     * @param string $redirectRoute
     * @param array $redirectRouteParameters
     *
     * @return string
     */
    public function getLoginUrl ($redirectRoute, $redirectRouteParameters = array())
    {
        $redirectUri = $this->router->generate($redirectRoute, $redirectRouteParameters, true);

        return $this->facebook->getLoginUrl(
            array(
                'scope'        => implode(', ', $this->requiredPermissions),
                'redirect_uri' => $redirectUri
            )
        );
    }



    /**
     * Handles the login, sets the access token, if it can be retrieved from the request
     *
     * @param Request $request
     * @param string $redirectRoute
     * @param array $redirectRouteParameters
     *
     * @return bool
     */
    public function handleLogin (Request $request, $redirectRoute, $redirectRouteParameters = array())
    {
        $redirectUri = $this->router->generate($redirectRoute, $redirectRouteParameters, true);
        $code        = $request->query->get("code", null);

        if (empty($code))
        {
            return false;
        }

        return $this->graphModel->confirmIdentityWithCode($this->facebook, $code, $redirectUri);
    }



    /**
     * Returns the api user
     *
     * @return ApiUser|null
     */
    public function getApiUser ()
    {
        try {
            $me = $this->facebook->api('/me');
            return new ApiUser($me);
        }
        catch (\Exception $e)
        {
            return null;
        }
    }



    /**
     * Sets the access token
     *
     * @param string $accessToken
     */
    public function setAccessToken ($accessToken)
    {
        $this->facebook->setAccessToken($accessToken);
    }



    /**
     * Returns the access token
     *
     * @return string
     */
    public function getAccessToken ()
    {
        return $this->facebook->getAccessToken();
    }
}