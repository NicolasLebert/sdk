<?php

/**
 * Build an url with query strings
 *
 * @param string $url
 * @param array $params
 * @return string
 */
function makeUrl(string $url, array $params = [])
{
    return $url . (!empty($params) ? '?' . http_build_query($params) : '');
}

/**
 * Perform a HTTP Request
 *
 * @param string $url
 * @param resource|null $context using stream_context_create function
 * @return array
 */
function httpRequest(string $url, $context = null)
{
    $response = @file_get_contents($url, false, $context);
    return $response ? json_decode($response, true) : ['error' => true];
}

/**
 * @param string $method
 * @param string|array $headers
 * @return resource
 */
function createStreamContext(string $method, $headers)
{
    return stream_context_create([
        'http' => [
            'method' => $method,
            'header' => $headers
        ]
    ]);
}