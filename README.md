# Action Resolver

A PSR-15 middleware that uses [Fast Route](https://github.com/nikic/FastRoute)
to resolve actions from the request path.

Use with [Action Dispatcher](https://github.com/ideationnet/action-dispatcher) 
to dispatch resolved actions using an [Invoker](https://github.com/PHP-DI/Invoker),
such as the one provided by [PHP-DI](https://github.com/PHP-DI/PHP-DI).


## Configuration

A default configuration is provided for [PHP-DI](https://github.com/PHP-DI/PHP-DI).
In addition to the provided configuration, you need
to provide an array of routes:

```php
'routes' => [
    [['GET', 'POST'], '/example/{id}', 'action.label'],
],
```

