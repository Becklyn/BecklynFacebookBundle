<?php

namespace Becklyn\FacebookBundle\Model;

use Guzzle\Http\Client;
use Guzzle\Http\Exception\ClientErrorResponseException;

class FacebookGraphModel
{
    /**
     * @var \Guzzle\Http\Client
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
     * @param \Facebook $facebook
     * @param string $code
     * @param string $redirectUri the redirect uri with which the login url was generated
     *
     * @return bool
     */
    public function confirmIdentityWithCode (\Facebook $facebook, $code, $redirectUri)
    {
        $request = $this->client->get("/oauth/access_token");
        $request->getQuery()
            ->set("client_id"     , $facebook->getAppId())
            ->set("redirect_uri"  , $redirectUri)
            ->set("client_secret" , $facebook->getAppSecret())
            ->set("code"          , $code);

        try {
            $response = $request->send();
            parse_str($response->getBody(true), $data);

            if (isset($data["access_token"]))
            {
                $facebook->setAccessToken($data["access_token"]);
                return true;
            }

            return false;
        }
        catch (ClientErrorResponseException $e)
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
        $request = $this->client->get("/debug_token");
        $request
            ->getQuery()
            ->set("input_token", $tokenToInspect)
            ->set("access_token", $appToken);

        try {
            $response = $request->send();
            parse_str($response->getBody(true), $data);
            return $data;
        }
        catch (ClientErrorResponseException $e)
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
     * @param \Facebook $facebook
     *
     * @return null
     */
    public function getAppToken (\Facebook $facebook)
    {
        $request = $this->client->get("/oauth/access_token");
        $request
            ->getQuery()
            ->set("client_id", $facebook->getAppId())
            ->set("client_secret", $facebook->getAppSecret())
            ->set("grant_type", "client_credentials");

        try {
            $response = $request->send();
            parse_str($response->getBody(true), $data);
            return $data;
        }
        catch (ClientErrorResponseException $e)
        {
            return null;
        }
    }



    /**
     * Generates a simple app access token
     *
     * @link https://developers.facebook.com/docs/facebook-login/access-tokens/#apptokens
     *
     * @param \Facebook $facebook
     *
     * @return string
     */
    public function getSimpleAppToken (\Facebook $facebook)
    {
        return "{$facebook->getAppId()}|{$facebook->getAppSecret()}";
    }
}