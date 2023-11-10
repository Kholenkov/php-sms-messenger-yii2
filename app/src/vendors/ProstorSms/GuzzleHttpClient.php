<?php

declare(strict_types=1);

namespace app\vendors\ProstorSms;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Kholenkov\ProstorSmsSdk\Configuration\ApiAccess;
use Kholenkov\ProstorSmsSdk\Helper\UrlHelper;
use Kholenkov\ProstorSmsSdk\Interfaces\HttpClient;
use Psr\Http\Message\ResponseInterface;

class GuzzleHttpClient implements HttpClient
{
    public function newInstance(array $config = []): Client
    {
        return new Client($config);
    }

    public function post(ApiAccess $apiAccess, string $path, array $data = []): ResponseInterface
    {
        $url = rtrim($apiAccess->baseUrl, '/') . $path;
        $data = array_merge(
            $data,
            [
                'login' => $apiAccess->login,
                'password' => $apiAccess->password,
            ],
        );

        $httpClient = $this->newInstance(
            [
                'base_uri' => UrlHelper::getSchemeAndAuthority($url),
            ]
        );

        return $httpClient->post(
            UrlHelper::getPath($url),
            [
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::JSON => $data,
            ],
        );
    }
}
