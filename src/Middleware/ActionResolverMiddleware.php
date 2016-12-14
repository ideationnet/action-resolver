<?php

namespace IdNet\Middleware;

use function FastRoute\simpleDispatcher;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use IdNet\Exception\MethodNotAllowedException;
use IdNet\Exception\NotFoundException;
use Interop\Container\ContainerInterface;
use Interop\Http\Middleware\DelegateInterface;
use Interop\Http\Middleware\ServerMiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;

class ActionResolverMiddleware implements ServerMiddlewareInterface
{
    const ACTION_ATTRIBUTE = 'idnet:action';

    public static function getConfig()
    {
        return [
            Dispatcher::class => function (ContainerInterface $c) {
                return simpleDispatcher(function (RouteCollector $r) use ($c) {
                    array_map(function ($route) use ($r) {
                        call_user_func_array([$r, 'addRoute'], $route);
                    }, $c->get('routes'));
                });
            }
        ];
    }

    /** @var Dispatcher */
    protected $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }


    public function process(ServerRequestInterface $request, DelegateInterface $delegate)
    {
        list($action, $vars) = $this->getRoute($request);

        $request = $request->withAttribute(self::ACTION_ATTRIBUTE, $action);

        foreach ($vars as $key => $value) {
            $request = $request->withAttribute($key, $value);
        }

        return $delegate->process($request);
    }


    private function getRoute(ServerRequestInterface $request)
    {
        $route = $this->dispatcher->dispatch(
            $request->getMethod(),
            $request->getUri()->getPath()
        );

        $status = array_shift($route);

        if ($status === Dispatcher::FOUND) {
            return $route;
        }

        if ($status === Dispatcher::METHOD_NOT_ALLOWED) {
            throw new MethodNotAllowedException($request, $route[0]);
        }

        throw new NotFoundException($request);
    }

}