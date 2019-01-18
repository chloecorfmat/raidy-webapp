<?php

namespace LiveBundle\Service;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class TwitterService.
 *
 * This service is inspired from https://github.com/J7mbo/twitter-api-php
 */
class TwitterService
{
    private $container;
    private $oauthAccessToken;
    private $oauthAccessTokenSecret;
    private $consumerKey;
    private $consumerSecret;
    private $getfield;
    private $postfields;

    /**
     * TwitterService constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->oauthAccessToken = $this->container->getParameter('app.twitter.oauth_access_token');
        $this->oauthAccessTokenSecret = $this->container->getParameter('app.twitter.oauth_access_token_secret');
        $this->consumerKey = $this->container->getParameter('app.twitter.consumer_key');
        $this->consumerSecret = $this->container->getParameter('app.twitter.consumer_secret');

        // @TODO : Check if curl extension is enable.
        // @TODO : throw exception if some parameters are empty.
    }

    /**
     * @param string $string
     *
     * @return TwitterService $this
     */
    public function setGetfield($string)
    {
        $this->getfield = $string;

        return $this;
    }

    /**
     * Set postfields array, example: array('screen_name' => 'J7mbo').
     *
     * @param array $array Array of parameters to send to API
     *
     * @return TwitterService this
     */
    public function setPostfields(array $array)
    {
        if (!is_null($this->getGetfield())) {
            throw new Exception('You can only choose get OR post fields.');
        }

        if (isset($array['status']) && '@' === substr($array['status'], 0, 1)) {
            $array['status'] = sprintf("\0%s", $array['status']);
        }

        $this->postfields = $array;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGetfield()
    {
        return $this->getfield;
    }

    /**
     * @return mixed
     */
    public function getPostfields()
    {
        return $this->postfields;
    }

    /**
     * @param string $url
     * @param string $requestMethod
     *
     * @return TwitterService $this
     */
    public function buildOauth($url, $requestMethod)
    {
        $oauth = [
            'oauth_consumer_key' => $this->consumerKey,
            'oauth_nonce' => time(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_token' => $this->oauthAccessToken,
            'oauth_timestamp' => time(),
            'oauth_version' => '1.0',
        ];

        $getfield = $this->getGetfield();

        if (!is_null($getfield)) {
            $getfields = str_replace('?', '', explode('&', $getfield));
            foreach ($getfields as $g) {
                $split = explode('=', $g);
                $oauth[$split[0]] = $split[1];
            }
        }

        $baseInfo = $this->buildBaseString($url, $requestMethod, $oauth);
        $compositeKey = rawurlencode($this->consumerSecret) . '&' . rawurlencode($this->oauthAccessTokenSecret);
        $oauthSignature = base64_encode(hash_hmac('sha1', $baseInfo, $compositeKey, true));
        $oauth['oauth_signature'] = $oauthSignature;

        $this->url = $url;
        $this->oauth = $oauth;

        return $this;
    }

    /**
     * Perform the actual data retrieval from the API.
     *
     * @param bool $return if true, returns data
     *
     * @return string json If $return param is true, returns json data
     */
    public function performRequest($return = true)
    {
        if (!is_bool($return)) {
            throw new Exception('performRequest parameter must be true or false');
        }

        $header = array($this->buildAuthorizationHeader($this->oauth), 'Expect:');

        $getfield = $this->getGetfield();
        $postfields = $this->getPostfields();

        $options = [
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_HEADER => false,
            CURLOPT_URL => $this->url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
        ];

        if (!is_null($postfields)) {
            $options[CURLOPT_POSTFIELDS] = $postfields;
        } else {
            if ('' !== $getfield) {
                $options[CURLOPT_URL] .= $getfield;
            }
        }

        $feed = curl_init();
        curl_setopt_array($feed, $options);
        $json = curl_exec($feed);
        curl_close($feed);

        if ($return) {
            return $json;
        }
    }

    /**
     * Private method to generate the base string used by cURL.
     *
     * @param string $baseURI
     * @param string $method
     * @param array  $params
     *
     * @return string Built base string
     */
    private function buildBaseString($baseURI, $method, $params)
    {
        $return = array();
        ksort($params);

        foreach ($params as $key => $value) {
            $return[] = "$key=" . $value;
        }

        return $method . '&' .
            rawurlencode($baseURI) . '&' .
            rawurlencode(implode('&', $return));
    }

    /**
     * Private method to generate authorization header used by cURL.
     *
     * @param array $oauth Array of oauth data generated by buildOauth()
     *
     * @return string $return Header used by cURL for request
     */
    private function buildAuthorizationHeader($oauth)
    {
        $return = 'Authorization: OAuth ';
        $values = array();

        foreach ($oauth as $key => $value) {
            $values[] = "$key=\"" . rawurlencode($value) . '"';
        }

        $return .= implode(', ', $values);

        return $return;
    }
}
