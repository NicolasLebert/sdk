<?php

class Github extends Provider
{

    public function __construct(string $client_id, string $client_secret, string $redirect_uri, array $options = [], string $app_name = "")
    {
        parent::__construct($client_id, $client_secret, $redirect_uri, $options, $app_name);
        $this->access_token_url = 'https://github.com/settings/tokens';
        $this->auth_url = 'https://github.com/login/oauth/authorize';
        $this->api_url = 'https://api.github.com/user';
    }

    public function getUser(string $code)
    {
        $access_token = $this->getAccessToken($code, true);
        $result = httpRequest($this->api_url, createStreamContext('GET', ["Authorization: Bearer ${access_token}", "User-Agent: $this->app_name"]));
        if ($result['error']) die("access token invalide");

        return [
            'id' => $result['id'],
            'name' => $result['login'],
            'email' => $result['email']
        ];
    }
}
