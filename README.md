# Stack\Heartbeat

A simple [Stack](http://stackphp.com) middleware which adds a route to your application to respond to heartbeat requests.

## Installation

The easiest way to install stack-heartbeat is through [Composer](https://getcomposer.org).

```
composer require bramr/stack-heartbeat
```

## Usage

By default the middleware responds to the route: **/heartbeat.check** with a plain text message "OK"

```php
public function __construct(HttpKernelInterface $app, $route = '/heartbeat.check', callable $handler = null)
```

The optional constructor arguments allows you to change the route or add a custom handler when the route is called.
This allows you to alter the response but also to add checks to your application which check dependencies like a database, files, etc.

An example:

```php
$app = new  CallableHttpKernel(function (Request $request) {
    return new Response('#yolo');
});

$app = (new Stack\Builder)
    ->push(BramR\Stack\Heartbeat::class)
    ->push(BramR\Stack\Heartbeat::class, '/custom', function () use ($diContainer) {
        return new Response(
            'Implement custom heartbeat check, to check some stuff in db:' . $diContainer['db.name']
        );
    })
    ->resolve($app);

Stack\run($app);
```

See example.php for a complete (and more complicated) example.

## License

MIT, for details see LICENSE file.

___

*Inspired by [Rack::Heartbeat](https://github.com/imajes/rack-heartbeat).*
