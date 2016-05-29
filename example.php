<?php
require_once './vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Stack\CallableHttpKernel;
use BramR\Stack\Heartbeat;

$diContainer = array('db.name' => 'super_important_production_db');

$app = new  CallableHttpKernel(function (Request $request) {
    return new Response('#yolo');
});

$app = (new Stack\Builder)
    ->push(Heartbeat::class)
    ->push(Heartbeat::class, '/just-a-diffent-route')
    ->push(Heartbeat::class, '/custom', function () use ($diContainer) {
        return new Response(
            'Custom heartbeat to check some stuff in db:' . $diContainer['db.name']
        );
    })
    ->push(Heartbeat::class, '/cpu', new CPULoadChecker)
    ->push(Heartbeat::class, '/cpu15m', new CPULoadChecker(CPULoadChecker::INTERVAL_15M))
    ->resolve($app);

Stack\run($app);

/**
 * As an example: super simple cpu load checker class.
 **/
class CPULoadChecker
{
    const INTERVAL_1M = 0;
    const INTERVAL_5M = 1;
    const INTERVAL_15M = 2;

    private $interval;

    public function __construct($interval = self::INTERVAL_1M)
    {
        $this->interval = $interval;
    }

    public function __invoke()
    {
        $load = sys_getloadavg();
        if (isset($load[$this->interval])) {
            return new Response($load[$this->interval], 200, array('Content-Type' => 'text/plain'));
        } else {
            return new Response('Whoops!', 500, array('Content-Type' => 'text/plain'));
        }
    }
}
