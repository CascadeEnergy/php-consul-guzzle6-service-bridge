<?php

namespace CascadeEnergy\ServiceDiscovery\Client\Guzzle6;

use CascadeEnergy\ServiceDiscovery\Client\ServiceClientInterface;
use CascadeEnergy\ServiceDiscovery\ServiceDiscoveryClientInterface;
use GuzzleHttp\Client;

class ServiceClient implements ServiceClientInterface
{
    const DEFAULT_PROTOCOL = 'http';

    /** @var Client */
    private $guzzleClient;

    /** @var ServiceDiscoveryClientInterface */
    private $serviceDiscoveryClient;

    /** @var string */
    private $serviceName;

    /** @var string|null */
    private $serviceVersion = null;

    /** @var string */
    private $protocol = self::DEFAULT_PROTOCOL;

    public function __construct(
        Client $guzzleClient,
        ServiceDiscoveryClientInterface $serviceDiscoveryClient,
        $serviceName,
        $serviceVersion = null
    ) {
        $this->guzzleClient = $guzzleClient;
        $this->serviceDiscoveryClient = $serviceDiscoveryClient;
        $this->serviceName = $serviceName;
        $this->serviceVersion = $serviceVersion;
    }

    public function setProtocol($protocol)
    {
        $this->protocol = $protocol;
    }

    public function get($path, $query = null)
    {
        return $this->makeRequest('GET', $path, $query);
    }

    public function delete($path, $query = null)
    {
        return $this->makeRequest('DELETE', $path, $query);
    }

    public function put($path, $query = null, $payload = null)
    {
        return $this->makeRequest('PUT', $path, $query, $payload);
    }
    
    public function post($path, $query = null, $payload = null)
    {
        return $this->makeRequest('POST', $path, $query, $payload);
    }

    private function createUri($path, $query)
    {
        $baseUri = $this->serviceDiscoveryClient->getServiceAddress($this->serviceName, $this->serviceVersion);

        $uri = "{$this->protocol}://$baseUri/$path";
        if (!empty($query)) {
            $uri .= "?" . urlencode($query);
        }

        return $uri;
    }

    private function makeRequest($method, $path, $query = null, $payload = null) {
        $uri = $this->createUri($path, $query);

        $options = [];
        if (!empty($payload)) {
            $options['body'] = $payload;
        }

        $result = $this->guzzleClient->request($method, $uri, $options);

        return json_decode(strval($result->getBody()));
    }
}
