<?php

namespace Becklyn\FacebookBundle\Model;

use Becklyn\FacebookBundle\Data\ApiUser;
use Becklyn\FacebookBundle\Data\CombinedFacebookData;
use Becklyn\FacebookBundle\Data\Page;
use Becklyn\FacebookBundle\Data\RequestUser;
use Facebook\Facebook;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\RouterInterface;


/**
 * Model for usual interaction with a Facebook (page) app
 *
 * @package Becklyn\FacebookBundle\Model
 */
class FacebookAppModel
{
    /**
     * The facebook api
     *
     * @var Facebook
     */
    protected $facebook;


    /**
     * The session service
     *
     * @var SessionInterface
     */
    private $session;


    /**
     * @var RouterInterface
     */
    private $router;


    /**
     * The URL to the fan page
     *
     * @var string
     */
    private $fanPageUrl;


    /**
     * The necessary permissions
     *
     * @var string[]
     */
    private $requiredPermissions;


    /**
     * @var CombinedFacebookData
     */
    private $facebookData;


    /**
     * The session identifier to use for this model
     *
     * @var string
     */
    private $sessionIdentifier;



    /**
     * Constructs a new facebook service
     *
     * @param Facebook               $facebook
     * @param SessionInterface $session
     * @param RouterInterface  $router
     * @param string           $fanPageUrl
     * @param array            $requiredPermissions
     * @param string           $sessionIdentifier
     */
    public function __construct (Facebook $facebook, SessionInterface $session, RouterInterface $router,
                                 $fanPageUrl, array $requiredPermissions = array("email"), $sessionIdentifier = "app")
    {
        $this->facebook            = $facebook;
        $this->session             = $session;
        $this->router              = $router;
        $this->fanPageUrl          = $fanPageUrl;
        $this->requiredPermissions = $requiredPermissions;
        $this->sessionIdentifier   = (string) $sessionIdentifier;

        // force initialization of the facebook session to fix session related errors in conjunction with the Facebook SDK
        try
        {
            $this->session->start();
        }
        catch (\RuntimeException $e)
        {
            // is thrown, when the session was already started by PHP.
            // The error is ignored, since this is exactly what is desired (to just start a session)
        }


        $this->initialize();
    }



    //region Direct API calls
    /**
     * Posts the message of a diary entry to the wall
     *
     * @param string $facebookId
     * @param array $parameters
     *
     * Parameter keys can be:
     *   - link (! required)
     *   - name
     *   - message
     *
     * @throws \FacebookApiException
     */
    public function postToWall ($facebookId, array $parameters = array())
    {
        $parameters["type"] = "link";
        $parameters["application"] = array(
            "id" => $this->getAppId()
        );

        $this->facebook->api("/{$facebookId}/feed", "POST", $parameters);
    }
    //endregion



    //region App Data & URL generation
    /**
     * Returns the permissions request url
     *
     * @param string $redirectRoute
     * @param array $redirectRouteParameters
     *
     * @return string
     */
    public function getPermissionsRequestUrl ($redirectRoute, $redirectRouteParameters = array())
    {
        return $this->facebook->getRedirectLoginHelper()->getLoginUrl(
            $this->router->generate($redirectRoute, $redirectRouteParameters, true),
            $this->requiredPermissions
        );
    }



    /**
     * Returns the facebook app data
     *
     * @return mixed
     */
    public function getAppData ()
    {
        return $this->facebook->getCanvasHelper()->getAppData();
    }



    /**
     * Returns the page tab url
     *
     * @param null|string $appData
     *
     * @return string
     */
    public function getPageTabUrl ($appData = null)
    {
        $appDataPart = "";

        if (!is_null($appData))
        {
            $appDataPart = '&app_data=' . rawurlencode($appData);
        }

        return "{$this->fanPageUrl}?sk=app_{$this->getAppId()}{$appDataPart}";
    }
    //endregion



    //region Initialization
    /**
     * Initializes the component
     */
    private function initialize ()
    {
        // get combined data from session
        $facebookDataInSession = $this->session->get("facebook.{$this->sessionIdentifier}", null);

        $this->facebookData = ($facebookDataInSession instanceof CombinedFacebookData)
            ? $facebookDataInSession
            : new CombinedFacebookData();

        // overwrite with data from current request
        $this->setDataFromCurrentRequest($this->facebookData);

        // store current combined data in session
        $this->session->set("facebook.{$this->sessionIdentifier}", $this->facebookData);
    }



    /**
     * Loads the facebook data from the current request and sets it on the facebook data object
     *
     * @param CombinedFacebookData $facebookData     *
     */
    private function setDataFromCurrentRequest (CombinedFacebookData $facebookData)
    {
        $signedRequest = $this->facebook->getCanvasHelper()->getSignedRequest();

        // still use the data from the session, if it is set
        if (null === $signedRequest)
        {
            return;
        }

        $signedRequestData = $signedRequest->getPayload();

        if (isset($signedRequestData["page"]))
        {
            $facebookData->setPage( new Page($signedRequestData["page"]) );
        }

        if (isset($signedRequestData["user"]))
        {
            $facebookData->setRequestUser( new RequestUser($signedRequestData["user"]) );
        }

        $apiUser = null;
        $accessToken = $this->facebook->getCanvasHelper()->getAccessToken();

        if (null !== $accessToken)
        {
            $me = $this->facebook->get('/me?fields=first_name,gender,last_name,email,locale,name,timezone,updated_time,verified', $accessToken);
            $apiUser = new ApiUser($me->getDecodedBody());
        }

        $facebookData->setApiUser($apiUser);
    }
    //endregion



    //region Data getters
    /**
     * Returns the api user
     *
     * @return ApiUser|null
     */
    public function getApiUser ()
    {
        return $this->facebookData->getApiUser();
    }



    /**
     * Returns the request user
     *
     * @return RequestUser|null
     */
    public function getRequestUser ()
    {
        return $this->facebookData->getRequestUser();
    }



    /**
     * Returns the page
     *
     * @return Page|null
     */
    public function getPage ()
    {
        return $this->facebookData->getPage();
    }



    /**
     * Returns the app id
     *
     * @return string
     */
    public function getAppId ()
    {
        return $this->facebook->getApp()->getId();
    }



    /**
     * Returns whether the user has liked the page
     *
     * @return bool
     */
    public function hasLikedPage ()
    {
        $page = $this->getPage();

        return !is_null($page)
            ? $page->isLikedByUser()
            : false;
    }



    /**
     * Returns whether the user has the required permissions
     *
     * @return bool
     */
    public function hasPermissions ()
    {
        return !is_null($this->getApiUser());
    }



    /**
     * Returns, whether the user is inside of facebook, but not on the page, but in the app directly
     *
     * @return bool
     */
    public function isInFacebookButNotInPage ()
    {
        $signedRequest = $this->facebook->getCanvasHelper()->getSignedRequest();
        return !empty($signedRequest) && !isset($signedRequest['page']);
    }



    /**
     * Returns the used session identifier
     *
     * @return string
     */
    public function getSessionIdentifier ()
    {
        return $this->sessionIdentifier;
    }
    //endregion
}
