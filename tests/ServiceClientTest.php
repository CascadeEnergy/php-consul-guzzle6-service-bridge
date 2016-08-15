<?php

namespace CascadeEnergy\Tests\ServiceDiscovery\Client\Guzzle6;

use CascadeEnergy\ServiceDiscovery\Client\Guzzle6\ServiceClient;
use PHPUnit_Framework_TestCase;

class ServiceClientTest extends PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $httpClient;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    private $discoveryClient;

    /** @var ServiceClient */
    private $serviceClient;

    public function setUp()
    {
        $this->httpClient = $this
            ->getMockBuilder('GuzzleHttp\Client')
            ->setMethods(['get','delete','put','post'])
            ->getMock();

        $this->discoveryClient = $this->createMock('CascadeEnergy\ServiceDiscovery\ServiceDiscoveryClientInterface');

        /** @noinspection PhpParamsInspection */
        $this->serviceClient = new ServiceClient(
            $this->httpClient,
            $this->discoveryClient,
            'serviceFoo',
            'versionBar'
        );
    }

    public function testItShouldExecuteAGetRequestOnAServiceUrl()
    {
        $data = json_encode(['foo' => 42]);

        $result = $this->createMock('Psr\Http\Message\ResponseInterface');
        $result->expects($this->once())->method('getBody')->willReturn($data);

        $this->discoveryClient
            ->expects($this->once())
            ->method('getServiceAddress')
            ->with('serviceFoo', 'versionBar')
            ->willReturn('uri:port');

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('http://uri:port/pathQux')
            ->willReturn($result);

        $this->assertEquals(json_decode($data), $this->serviceClient->get('pathQux'));
    }

    public function testItShouldAddAQueryStringToTheRequestWhenOneIsProvided()
    {
        $data = json_encode(['foo' => 42]);

        $result = $this->createMock('Psr\Http\Message\ResponseInterface');
        $result->expects($this->once())->method('getBody')->willReturn($data);

        $this->discoveryClient
            ->expects($this->once())
            ->method('getServiceAddress')
            ->with('serviceFoo', 'versionBar')
            ->willReturn('uri:port');

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('http://uri:port/pathQux?bagelQuery')
            ->willReturn($result);

        $this->assertEquals(json_decode($data), $this->serviceClient->get('pathQux', 'bagelQuery'));
    }

    public function testItShouldAllowTheRequestProtocolToBeChanged()
    {
        $data = json_encode(['foo' => 42]);

        $result = $this->createMock('Psr\Http\Message\ResponseInterface');
        $result->expects($this->once())->method('getBody')->willReturn($data);

        $this->discoveryClient
            ->expects($this->once())
            ->method('getServiceAddress')
            ->with('serviceFoo', 'versionBar')
            ->willReturn('uri:port');

        $this->httpClient
            ->expects($this->once())
            ->method('get')
            ->with('https://uri:port/pathQux')
            ->willReturn($result);

        $this->serviceClient->setProtocol('https');
        $this->assertEquals(json_decode($data), $this->serviceClient->get('pathQux'));
    }

    public function testItShouldExecuteADeleteRequest()
    {
        $data = json_encode(['foo' => 42]);
        $result = $this->createMock('Psr\Http\Message\ResponseInterface');
        $result->expects($this->once())->method('getBody')->willReturn($data);

        $this->discoveryClient
            ->expects($this->once())
            ->method('getServiceAddress')
            ->with('serviceFoo', 'versionBar')
            ->willReturn('uri:port');

        $this->httpClient
            ->expects($this->once())
            ->method('delete')
            ->with('http://uri:port/pathQux')
            ->willReturn($result);

        $this->assertEquals(json_decode($data), $this->serviceClient->delete('pathQux'));
    }

    public function testItShouldExecuteAPutRequest()
    {
        $data = json_encode(['foo' => 42]);
        $result = $this->createMock('Psr\Http\Message\ResponseInterface');
        $result->expects($this->once())->method('getBody')->willReturn($data);

        $this->discoveryClient
            ->expects($this->once())
            ->method('getServiceAddress')
            ->with('serviceFoo', 'versionBar')
            ->willReturn('uri:port');

        $this->httpClient
            ->expects($this->once())
            ->method('put')
            ->willReturn($result);

        $this->assertEquals(json_decode($data), $this->serviceClient->put('pathQux'));
    }

    public function testItShouldExecuteAPostRequest()
    {
        $data = json_encode(['foo' => 42]);
        $result = $this->createMock('Psr\Http\Message\ResponseInterface');
        $result->expects($this->once())->method('getBody')->willReturn($data);

        $this->discoveryClient
            ->expects($this->once())
            ->method('getServiceAddress')
            ->with('serviceFoo', 'versionBar')
            ->willReturn('uri:port');

        $this->httpClient
            ->expects($this->once())
            ->method('post')
            ->willReturn($result);

        $this->assertEquals(json_decode($data), $this->serviceClient->post('pathQux'));
    }
}
