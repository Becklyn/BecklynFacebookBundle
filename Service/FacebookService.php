<?php

namespace OAGM\FacebookBundle\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class FacebookService
{
    /**
     * The session service
     *
     * @var SessionInterface
     */
    private $session;


    /**
     * The facebook api
     *
     * @var \Facebook
     */
    private $facebook;


    /**
     * The FB app id
     *
     * @var string
     */
    private $appId;


    /**
     * The app secret
     *
     * @var string
     */
    private $appSecret;


    /**
     * The URL to the fan page
     *
     * @var string
     */
    private $fanPageUrl;


    /**
     * Flag, whether the required permissions are available
     *
     * @var bool
     */
    private $hasPermissions = false;


    /**
     * The user's id
     *
     * @var null|string
     */
    private $userId = null;


    /**
     * The user's name
     *
     * @var null
     */
    private $userName = null;


    /**
     * The user's email address
     *
     * @var null|string
     */
    private $email = null;


    /**
     * The necessary permissions
     *
     * @var string[]
     */
    private $requiredPermissions;


    /**
     * Flag, whether the use has liked the page
     *
     * @var bool
     */
    private $hasLikedPage = false;



    /**
     * Constructs a new facebook service
     *
     * @param SessionInterface $session
     * @param string $appId
     * @param string $appSecret
     * @param string $fanPageUrl
     * @param array $requiredPermissions
     */
    public function __construct (SessionInterface $session, $appId, $appSecret, $fanPageUrl, array $requiredPermissions)
    {
        $this->session             = $session;
        $this->appId               = $appId;
        $this->appSecret           = $appSecret;
        $this->fanPageUrl          = $fanPageUrl;
        $this->requiredPermissions = $requiredPermissions;

        // force initialization of the facebook session to fix session related errors in conjunction with the Facebook SDK
        $this->session->start();

        $this->facebook = new \Facebook(
            array(
                'appId'  => $this->appId,
                'secret' => $this->appSecret
            )
        );

        $this->initialize();
    }



    //region Initialization
    /**
     * Initializes the component
     */
    private function initialize ()
    {
        $loadedData = $this->loadFromCurrentRequest();

        if (is_null($loadedData))
        {
            $loadedData = $this->loadFromSession();
        }

        $this->initializeLoadedData($loadedData);
        $this->initializeLikedPage();
    }



    /**
     * Loads the facebook data from the current request
     *
     * @return null|array
     */
    private function loadFromCurrentRequest ()
    {
        $signedRequest = $this->facebook->getSignedRequest();

        if (is_null($signedRequest))
        {
            return null;
        }

        try
        {
            $me = $this->facebook->api('/me');

            return array(
                'id'    => $me['id'],
                'name'  => $me['name'],
                'email' => $me['email']
            );
        }
        catch (\Exception $e)
        {
            // remove data from session - we don't have the permissions anymore, we should not keep the data
            $this->storeInSession(null);

            return null;
        }
    }



    /**
     * Loads the facebook data from the session
     *
     * @return array|null
     */
    private function loadFromSession ()
    {
        return $this->session->get($this->getBaseDataSessionIdentifier(), null);
    }



    /**
     * Initialize the loaded data
     *
     * @param array|null $loadedData
     */
    private function initializeLoadedData ($loadedData)
    {
        if (!is_null($loadedData))
        {
            $this->userId         = $loadedData['id'];
            $this->userName       = $loadedData['name'];
            $this->email          = $loadedData['email'];
            $this->hasPermissions = true;
        }

        $this->storeInSession($loadedData);
    }



    /**
     * Stores the data in the session
     *
     * @param $data
     */
    private function storeInSession ($data)
    {
        if (!is_null($data))
        {
            $this->session->set($this->getBaseDataSessionIdentifier(), $data);
        }
        else
        {
            $this->session->remove($this->getBaseDataSessionIdentifier());
        }
    }



    /**
     * Fetches the "has liked page" data
     *
     * @return bool
     */
    private function initializeLikedPage ()
    {
        $signedRequest = $this->facebook->getSignedRequest();

        if (!empty($signedRequest) && isset($signedRequest['page']['liked']))
        {
            $hasLikedPage = (1 == $signedRequest['page']['liked']);
        }
        else
        {
            $hasLikedPage = $this->session->get($this->getLikedPageSessionIdentifier(), false);
        }

        $this->session->set($this->getLikedPageSessionIdentifier(), $hasLikedPage);
        $this->hasLikedPage = $hasLikedPage;
    }
    //endregion



    //region Identifiers
    /**
     * Returns the session identifier for the base data
     *
     * @return string
     */
    protected function getBaseDataSessionIdentifier ()
    {
        return "facebookData";
    }



    /**
     * Returns the session identifier for the "has liked page" flag
     *
     * @return string
     */
    protected function getLikedPageSessionIdentifier ()
    {
        return "facebookDataHasLiked";
    }
    //endregion


    //region Simple Accessors
    /**
     * Returns the app id
     *
     * @return string
     */
    public function getAppId ()
    {
        return $this->appId;
    }



    /**
     * Returns the facebook user id
     *
     * @return null|string
     */
    public function getUserId ()
    {
        return $this->userId;
    }



    /**
     * Returns the facebook user name
     *
     * @return null|string
     */
    public function getUserName ()
    {
        return $this->userName;
    }



    /**
     * Returns the user's email address
     *
     * @return null|string
     */
    public function getUserEmail ()
    {
        return $this->email;
    }



    /**
     * Returns, whether the user has the required permissions
     *
     * @return bool
     */
    public function hasPermissions ()
    {
        return $this->hasPermissions;
    }



    /**
     * Returns, whether the user has liked the page
     *
     * @return bool
     */
    public function hasLikedPage ()
    {
        return $this->hasLikedPage;
    }
    //endregion



    //region Specialized Getters
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
     * Returns the permissions request url
     *
     * @param string $redirectRoute
     * @param array $redirectRouteParameters
     *
     * @return string
     */
    public function getPermissionsRequestUrl ($redirectRoute, $redirectRouteParameters = array())
    {
        /** @var $router \Symfony\Component\Routing\Router */
        $router = $this->get('router');

        return $this->facebook->getLoginUrl(
            array(
                'scope'        => implode(', ', $this->requiredPermissions),
                'redirect_uri' => $router->generate($redirectRoute, $redirectRouteParameters, true)
            )
        );
    }



    /**
     * Returns the country of the user
     *
     * @return null|string
     */
    public function getCountryOfUser ()
    {
        $signedRequest = $this->facebook->getSignedRequest() ?: array();
        return isset($signedRequest["user"]["country"]) ? $signedRequest["user"]["country"] : null;
    }



    /**
     * Returns the locale of the user
     *
     * @return null|string
     */
    public function getLocaleOfUser ()
    {
        $signedRequest = $this->facebook->getSignedRequest() ?: array();
        return isset($signedRequest["user"]["locale"]) ? $signedRequest["user"]["locale"] : null;
    }
    //endregion



    //region API calls
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

        return "{$this->fanPageUrl}?sk=app_{$this->appId}{$appDataPart}";
    }
    //endregion



    //region Utils
    /**
     * Truncates the like description text
     *
     * @param string $text
     * @param int $length
     *
     * @return string
     */
    public static function truncateLikeDescriptionText ($text, $length = 80)
    {
        $text = strip_tags($text);
        $text = html_entity_decode($text, null, 'UTF-8');
        $truncated = substr($text, 0, $length);
        $truncated .= (strlen($truncated) < strlen($text)) ? "..." : "";

        return $truncated;
    }
    //endregion



    //region Debug
    /**
     * Returns extensive debug output
     *
     * @return array
     */
    public function debug ()
    {
        var_dump($this->facebook);

        print_r(array(
            "signedRequest"              => $this->facebook->getSignedRequest(),
            "isInFacebookButNotInPage"   => $this->isInFacebookButNotInPage(),
            "hasPermissions"             => $this->hasPermissions(),
            "hasLiked"                   => $this->hasLikedPage(),
            "pageTabUrl"                 => $this->getPageTabUrl(),
            "appId"                      => $this->getAppId(),
            "userId"                     => $this->getUserId(),
            "userName"                   => $this->getUserName(),
            "userEmail"                  => $this->getUserEmail(),
            "userCountry"                => $this->getCountryOfUser(),
            "userLocale"                 => $this->getLocaleOfUser(),
            "app_data"                   => $this->getAppData(),
            "sessionData"                => $this->loadFromSession(),
            "baseDataSessionIdentifier"  => $this->getBaseDataSessionIdentifier(),
            "likedPageSessionIdentifier" => $this->getLikedPageSessionIdentifier(),
        ));
    }
    //endregion
}