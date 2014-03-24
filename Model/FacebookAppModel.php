<?php

namespace Becklyn\FacebookBundle\Model;

use Becklyn\FacebookBundle\Data\ApiUser;
use Becklyn\FacebookBundle\Data\CombinedFacebookData;
use Becklyn\FacebookBundle\Data\Page;
use Becklyn\FacebookBundle\Data\RequestUser;
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
     * @var \Facebook
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
     * @param \Facebook $facebook
     * @param SessionInterface $session
     * @param RouterInterface $router
     * @param string $fanPageUrl
     * @param array $requiredPermissions
     * @param string $sessionIdentifier
     */
    public function __construct (\Facebook $facebook, SessionInterface $session, RouterInterface $router,
                                 $fanPageUrl, array $requiredPermissions = array("email"), $sessionIdentifier = "app")
    {
        $this->facebook            = $facebook;
        $this->session             = $session;
        $this->router              = $router;
        $this->fanPageUrl          = $fanPageUrl;
        $this->requiredPermissions = $requiredPermissions;
        $this->sessionIdentifier   = (string) $sessionIdentifier;

        // force initialization of the facebook session to fix session related errors in conjunction with the Facebook SDK
        $this->session->start();

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
        return $this->facebook->getLoginUrl(
            array(
                'scope'        => implode(', ', $this->requiredPermissions),
                'redirect_uri' => $this->router->generate($redirectRoute, $redirectRouteParameters, true)
            )
        );
    }



    /**
     * Returns the facebook app data
     *
     * @return mixed
     */
    public function getAppData ()
    {
        $signedRequest = $this->facebook->getSignedRequest();

        return (!is_null($signedRequest) && isset($signedRequest["app_data"]))
            ? $signedRequest['app_data']
            : null;
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
     * @param CombinedFacebookData $facebookData
     *
     * @return null|array
     */
    private function setDataFromCurrentRequest (CombinedFacebookData $facebookData)
    {
        $signedRequest = $this->facebook->getSignedRequest();

        // still use the data from the session, if it is set
        if (is_null($signedRequest))
        {
            return;
        }

        if (isset($signedRequest["page"]))
        {
            $facebookData->setPage( new Page($signedRequest["page"]) );
        }

        if (isset($signedRequest["user"]))
        {
            $facebookData->setRequestUser( new RequestUser($signedRequest["user"]) );
        }

        try
        {
            $me = $this->facebook->api('/me');
            $facebookData->setApiUser( new ApiUser($me) );
        }
        catch (\Exception $e)
        {
            // remove data from session - we don't have the permissions anymore, we should not keep the data
            $facebookData->setApiUser(null);

            return null;
        }
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
        return $this->facebook->getAppId();
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
        $signedRequest = $this->facebook->getSignedRequest();
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