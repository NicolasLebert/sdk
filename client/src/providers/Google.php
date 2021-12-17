<?php

class Google extends Provider
{

    public function __construct(string $client_id, string $client_secret, string $redirect_uri, array $options = [])
    {
        parent::__construct($client_id, $client_secret, $redirect_uri, $options);
        $this->access_token_url = 'https://oauth2.googleapis.com/token';
        $this->auth_url = 'https://accounts.google.com/o/oauth2/v2/auth';
        $this->api_url = 'https://www.googleapis.com/oauth2/v1/userinfo';
    }

    public function getUser(string $code)
    {
        $access_token = $this->getAccessToken($code, true);
        $result = httpRequest($this->api_url, createStreamContext('GET', "Authorization: Bearer ${access_token}"));
        if ($result['error']) die("access token invalide");

        return [
            'id' => $result['id'],
            'name' => $result['name'],
            'email' => $result['email']
        ];
    }
}
