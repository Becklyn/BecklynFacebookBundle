<?php


namespace OAGM\FacebookBundle\Service;

/**
 *
 */
use Symfony\Component\DependencyInjection\Container;

class FacebookService
{
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
     * The URL to the fanpage
     *
     * @var string
     */
    private $fbPage;


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
     * The service container
     *
     * @var \Symfony\Component\DependencyInjection\Container
     */
    private $serviceContainer;


    /**
     * @var \Facebook
     */
    private $facebook;


    /**
     * The necessary permissions
     *
     * @var string[]
     */
    private $neededPermissions;


    /**
     * Flag, whether the use has liked the page
     *
     * @var bool
     */
    private $hasLikedPage = false;


    /**
     * The bundle prefix
     *
     * @var string
     */
    private $bundlePrefix;



    /**
     * Constructs a new facebook service
     *
     * @param \Symfony\Component\DependencyInjection\Container $serviceContainer
     * @param string $bundlePrefix
     */
    public function __construct (Container $serviceContainer, $bundlePrefix)
    {
        $this->bundlePrefix = $bundlePrefix;

        $this->appId             = $serviceContainer->getParameter("{$this->bundlePrefix}.facebook.app_id");
        $this->appSecret         = $serviceContainer->getParameter("{$this->bundlePrefix}.facebook.app_secret");
        $this->fbPage            = $serviceContainer->getParameter("{$this->bundlePrefix}.facebook.page_url");
        $this->neededPermissions = $serviceContainer->getParameter("{$this->bundlePrefix}.facebook.required_permissions");

        $this->serviceContainer = $serviceContainer;

        // force initialization of the facebook session to fix session related errors in conjunction with the Facebook SDK
        $this->serviceContainer->get('session')->start();

        $this->facebook = new \Facebook(
            array(
                'appId'  => $this->appId,
                'secret' => $this->appSecret
            )
        );

        $this->initialize();
    }



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

        try {
            $me = $this->facebook->api('/me');

            return array(
                'id' => $me['id'],
                'name' => $me['name'],
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
        return $this->get('session')->get("{$this->bundlePrefix}.facebookData", null);
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
            $this->userId = $loadedData['id'];
            $this->userName = $loadedData['name'];
            $this->email = $loadedData['email'];
            $this->hasPermissions = true;
        }

        $this->storeInSession($loadedData);
    }



    /**
     * Fetches the "has liked page" data
     *
     * @return bool
     */
    private function initializeLikedPage ()
    {
        /** @var $session \Symfony\Component\HttpFoundation\Session */
        $session = $this->get('session');
        $signedRequest = $this->facebook->getSignedRequest();


        if (!empty($signedRequest) && isset($signedRequest['page']['liked']))
        {
            $hasLikedPage = (1 == $signedRequest['page']['liked']);
        }
        else
        {
            $hasLikedPage = $session->get("{$this->bundlePrefix}.facebookDataHasLiked", false);
        }

        $session->set("{$this->bundlePrefix}.facebookDataHasLiked", $hasLikedPage);
        $this->hasLikedPage = $hasLikedPage;
    }



    /**
     * Stores the data in the session
     *
     * @param $data
     */
    private function storeInSession ($data)
    {
        /** @var $session \Symfony\Component\HttpFoundation\Session */
        $session = $this->get('session');

        if (!is_null($data))
        {
            $session->set("{$this->bundlePrefix}.facebookData", $data);
        }
        else
        {
            $session->remove("{$this->bundlePrefix}.facebookData");
        }
    }



    /**
     * Returns the service
     *
     * @param string $name
     * @return object
     */
    public function get ($name)
    {
        return $this->serviceContainer->get($name);
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

        return "{$this->fbPage}?sk=app_{$this->appId}{$appDataPart}";
    }



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
    public function getFacebookUserId ()
    {
        return $this->userId;
    }



    /**
     * Returns the facebook user name
     *
     * @return null|string
     */
    public function getFacebookUserName ()
    {
        return $this->userName;
    }



    /**
     * Returns the user's email address
     *
     * @return null|string
     */
    public function getEmail ()
    {
        return $this->email;
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
     * Returns, whether the user has the required permissions
     *
     * @return bool
     */
    public function hasPermissions ()
    {
        return $this->hasPermissions;
    }



    /**
     * Returns the permissions request url
     *
     * @param array $pathArguments
     *
     * @return string
     */
    public function getPermissionsRequestUrl ($pathArguments = array())
    {
        /** @var $router \Symfony\Component\Routing\Router */
        $router = $this->get('router');

        return $this->facebook->getLoginUrl(
            array(
                'scope' => implode(', ', $this->neededPermissions),
                'redirect_uri' => $router->generate("{$this->bundlePrefix}_fb_permissions_callback", $pathArguments, true)
            )
        );
    }



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



    /**
     * Truncates the like description text
     *
     * @param string $text
     * @param int $length
     *
     * @return string
     */
    public function truncateLikeDescriptionText ($text, $length = 80)
    {
        $text = strip_tags($text);
        $text = html_entity_decode($text, null, 'UTF-8');
        $truncated = substr($text, 0, $length);
        $truncated .= (strlen($truncated) < strlen($text)) ? "..." : "";

        return $truncated;
    }



    /**
     * Returns a facebook URL
     *
     * @param string $name
     * @param array $parameters
     * @return string
     */
    public function generateUrl ($name, array $parameters = array())
    {
        /** @var $router \Symfony\Bundle\FrameworkBundle\Routing\Router */
        $router = $this->get('router');

        /** @var $request \Symfony\Component\HttpFoundation\Request */
        $request = $this->get('request');

        $route = $router->generate($name, $parameters, false);
        $baseUrl = $request->getBaseUrl();

        if (0 === stripos($route, $baseUrl))
        {
            $route = substr($route, strlen($baseUrl));
        }

        return $this->getPageTabUrl($route);
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



    /**
     * Returns the facebook path
     *
     * @return string|null
     */
    public function getFacebookPermalink ()
    {
        $signedRequest = $this->facebook->getSignedRequest();

        if (is_null($signedRequest))
        {
            return "/";
        }

        return $signedRequest['app_data'];
    }
}