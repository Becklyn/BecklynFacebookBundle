<?php

namespace Becklyn\FacebookBundle\Model;

use Facebook\Facebook;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class FacebookGraphModel
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $client;



    /**
     *
     */
    public function __construct ()
    {
        $this->client = new Client("https://graph.facebook.com");
    }



    /**
     * Exchanges the code for a valid access token
     *
     * @link https://developers.facebook.com/docs/facebook-login/manually-build-a-login-flow/#confirm
     *
     * @param Facebook $facebook
     * @param string $code
     * @param string $redirectUri the redirect uri with which the login url was generated
     *
     * @return bool
     */
    public function confirmIdentityWithCode (Facebook $facebook, $code, $redirectUri)
    {
        $request = $this->client->createRequest("GET", "/oauth/access_token");
        $query = $request->getQuery();

        $query->set("client_id"     , $facebook->getApp()->getId());
        $query->set("redirect_uri"  , $redirectUri);
        $query->set("client_secret" , $facebook->getApp()->getSecret());
        $query->set("code"          , $code);

        try
        {
            $response = $this->client->send($request);
            parse_str($response->getBody(true), $data);

            if (isset($data["access_token"]))
            {
                $facebook->setDefaultAccessToken($data["access_token"]);
                return true;
            }

            return false;
        }
        catch (ClientException $e)
        {
            return false;
        }
    }



    /**
     * Lets you inspect a access token of a user
     *
     * @link https://developers.facebook.com/docs/facebook-login/manually-build-a-login-flow/#checktoken
     *
     * @param string $tokenToInspect
     * @param string $appToken
     *
     * @return array|null
     */
    public function inspectToken ($tokenToInspect, $appToken)
    {
        $request = $this->client->createRequest("GET", "/debug_token");
        $query = $request->getQuery();

        $query->set("input_token", $tokenToInspect);
        $query->set("access_token", $appToken);

        try
        {
            $response = $this->client->send($request);
            parse_str($response->getBody(true), $data);
            return $data;
        }
        catch (ClientException $e)
        {
            return null;
        }
    }



    /**
     * Generates an app access token.
     * You can also use getSimpleAppToken
     *
     * @see FacebookGraphModel::getSimpleAppToken()
     * @link https://developers.facebook.com/docs/facebook-login/access-tokens/#apptokens
     *
     * @param Facebook $facebook
     *
     * @return null
     */
    public function getAppToken (Facebook $facebook)
    {
        $request = $this->client->createRequest("GET", "/oauth/access_token");
        $query = $request->getQuery();

        $query->set("client_id", $facebook->getApp()->getId());
        $query->set("client_secret", $facebook->getApp()->getSecret());
        $query->set("grant_type", "client_credentials");

        try
        {
            $response = $this->client->send($request);
            parse_str($response->getBody(true), $data);
            return $data;
        }
        catch (ClientException $e)
        {
            return null;
        }
    }



    /**
     * Generates a simple app access token
     *
     * @link https://developers.facebook.com/docs/facebook-login/access-tokens/#apptokens
     *
     * @param Facebook $facebook
     *
     * @return string
     */
    public function getSimpleAppToken (Facebook $facebook)
    {
        return "{$facebook->getApp()->getId()}|{$facebook->getApp()->getSecret()}";
    }
}
