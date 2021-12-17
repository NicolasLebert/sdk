<?php

require 'src/includes/dotenv.php';
require 'src/helpers/helpers.php';
require 'src/providers/Provider.php';
require 'src/providers/Google.php';
require 'src/providers/Github.php';

function handleResponse(Provider $provider, array $request)
{
    if (!$request['code']) die('Accès refusé');

    $data = $provider->getUser($request['code']);

    echo "Bonjour {$data['name']} vous êtes connecté à " . ucfirst($request['provider']) . "avec l'id {$data['id']}";
}

function displayHome(array $providers)
{
    foreach ($providers as $provider) {
        echo getOAuthLink($provider['instance']->getAuthorizationUrl(), $provider['link_label']);
    }
}

function getOAuthLink(string $link, string $label, array $options = [])
{
    $html = "<p><a href=${link}>${label}</a></p>";

    return $html;
}


function getAllProviders()
{
    return [
        'google' => [
            'link_label' => 'Se connecter via Google',
            'instance' => new Google(GOOGLE_CLIENT_ID, GOOGLE_SECRET, "http://localhost:8081/auth?provider=google", ['scope' => 'https://www.googleapis.com/auth/userinfo.profile  https://www.googleapis.com/auth/userinfo.email'])
        ],
        'github' => [
            'link_label' => 'Se connecter via Github',
            'instance' => new Github(GITHUB_CLIENT_ID, GITHUB_SECRET, "http://localhost:8081/auth?provider=github", [], GITHUB_APP)
        ],
    ];
}

loadDotEnv('.env');
$providers = getAllProviders();
$route = strtok($_SERVER["REQUEST_URI"], '?');

switch ($route) {
    case '/':
        displayHome($providers);
        break;
    case '/auth':
        if (!$provider = $providers[$_GET['provider']]['instance']) die("Une erreur est survenue : le provider {$_GET['provider']} n'est pas reconnu");
        handleResponse($provider, $_GET);
        break;
    default:
        http_response_code(404);
}
