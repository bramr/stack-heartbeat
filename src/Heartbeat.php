<?php

namespace BramR\Stack;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class Heartbeat implements HttpKernelInterface
{
    const MESSAGE = 'OK';

    protected $app;
    protected $handler;
    protected $route;

    /**
     * @param HttpKernelInterface $app
     * @param string $route = '/heartbeat.check'
     * @param callable|null $handler = null
     */
    public function __construct(HttpKernelInterface $app, $route = '/heartbeat.check', callable $handler = null)
    {
        $this->app = $app;
        $this->route = $route;
        $this->handler = $handler;
    }

    /**
     * {@inheritdoc}
     **/
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        if ($request->getPathInfo() === $this->route) {
            $handler = $this->getHandler();
            return $handler();
        } else {
            return $this->app->handle($request, $type, $catch);
        }
    }

    /**
     * getHandler
     * @return callable
     */
    protected function getHandler()
    {
        if (is_null($this->handler)) {
            $this->handler = function () {
                return new Response(self::MESSAGE, 200, array('Content-Type' => 'text/plain'));
            };
        }
        return $this->handler;
    }
}
