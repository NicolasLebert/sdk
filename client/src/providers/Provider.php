<?php

abstract class Provider
{
    protected string $client_id;
    protected string $client_secret;
    protected string $auth_url;
    protected string $api_url;
    protected string $access_token_url;
    protected string $redirect_uri;
    protected string $app_name;
    protected array $options;

    protected function __construct(string $client_id, string $client_secret, string $redirect_uri, array $options, string $app_name = "")
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->redirect_uri = $redirect_uri;
        $this->options = $options;
        $this->app_name = $app_name;
    }

    /**
     * Fetch user data from the provider's API
     *
     * @param string $code authorization code received from OAuth process
     * @return array|false
     */
    public function getUser(string $code)
    {
        $access_token = $this->getAccessToken($code);
        $result = httpRequest($this->api_url, createStreamContext('GET', "Authorization: Bearer ${access_token}"));
        if ($result['error']) die("Votre token d'accès n'est pas valide ou l'URL est inaccessible");

        return $result;
    }

    /**
     * Request the access token with the authorization code received
     *
     * @param string $code authorization code
     * @param bool $is_post POST or GET request
     * @return array|null
     */
    protected function getAccessToken(string $code, bool $is_post = false)
    {
        $context = $is_post ? createStreamContext('POST', ['Content-Type: application/x-www-form-urlencoded', 'Content-Length: 0', 'Accept: application/json']) : null;
        $url = makeUrl($this->access_token_url, [
            'code' => $code,
            'client_id' => $this->client_id,
            'client_secret' => $this->client_secret,
            'redirect_uri' => $this->redirect_uri,
            'grant_type' => 'authorization_code',
        ]);

        $res = httpRequest($url, $context);
        if ($res['error']) die($res['error_description'] ?? 'Credentials invalide');

        return $res['access_token'];
    }

    /**
     * Generate link for authentification/authorization
     *
     * @return string
     */
    public function getAuthorizationUrl()
    {
        return makeUrl($this->auth_url, array_merge([
            'response_type' => 'code',
            'redirect_uri' => $this->redirect_uri,
            'client_id' => $this->client_id,
        ], $this->options));
    }
}
