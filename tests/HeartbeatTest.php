<?php

namespace BramR\Stack;

use Symfony\Component\HttpFoundation\Response;

class HeartbeatTest extends \PHPUnit_Framework_TestCase
{

    public function testStillRunsApp()
    {
        $mockApp = $this->getMock('Symfony\\Component\\HttpKernel\\HttpKernelInterface');
        $mockApp->expects($this->once())
            ->method('handle')
            ->will($this->returnValue(new Response('yolo')));
        $mockRequest = $this->getMock('Symfony\Component\HttpFoundation\Request', array('getPathInfo'));
        $mockRequest->expects($this->once())
            ->method('getPathInfo')
            ->will($this->returnValue('/not.heartbeat'));

        $heartbeat = new Heartbeat($mockApp);
        $response = $heartbeat->handle($mockRequest);
        $expected = new Response('yolo');

        $this->assertEquals(
            $expected,
            $response,
            'When not using heartbeat route, should return the application result.'
        );
    }

    public function testDefaultBeat()
    {
        $mockApp = $this->getMock('Symfony\\Component\\HttpKernel\\HttpKernelInterface');

        $mockRequest = $this->getMock('Symfony\Component\HttpFoundation\Request', array('getPathInfo'));
        $mockRequest->expects($this->once())
            ->method('getPathInfo')
            ->will($this->returnValue('/heartbeat.check'));

        $heartbeat = new Heartbeat($mockApp);
        $response = $heartbeat->handle($mockRequest);
        $expected = new Response('OK');
        $expected->headers->set('Content-Type', 'text/plain');

        $this->assertEquals(
            $expected,
            $response,
            'When requesting default heartbeat route, the default message should be returned'
        );
    }

    public function testBeatWithCustomRouteAndHandler()
    {
        $mockApp = $this->getMock('Symfony\\Component\\HttpKernel\\HttpKernelInterface');

        $heartbeat = new Heartbeat($mockApp, '/custom.check', function () {
            return new Response('CUSTOM');
        });

        $mockRequest = $this->getMock('Symfony\Component\HttpFoundation\Request', array('getPathInfo'));
        $mockRequest->expects($this->once())
            ->method('getPathInfo')
            ->will($this->returnValue('/custom.check'));

        $response = $heartbeat->handle($mockRequest);
        $expected = new Response('CUSTOM');

        $this->assertEquals(
            $expected,
            $response,
            'When requesting new , the default message should be returned'
        );
    }
}
