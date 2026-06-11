<?php

namespace App\Http;

use ReflectionMethod;

abstract class Controller
{
    /**
     * Dispatch a controller method, auto-injecting type-hinted dependencies.
     *
     * Supported injections:
     *   App\Http\Request  →  Request::fromGlobals()
     */
    public function callAction(string $method): void
    {
        $reflector = new ReflectionMethod($this, $method);
        $args      = [];

        foreach ($reflector->getParameters() as $param) {
            $type    = $param->getType()?->getName();
            $args[]  = match ($type) {
                Request::class => Request::fromGlobals(),
                default        => null,
            };
        }

        $this->$method(...$args);
    }
}
