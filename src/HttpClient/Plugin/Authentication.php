<?php

namespace Clearhaus\HttpClient\Plugin;

use Http\Client\Common\Plugin;
use Http\Message\Authentication as AuthenticationMethod;
use Psr\Http\Message\RequestInterface;

class Authentication implements Plugin
{
    private $authenticationMethod;

    public function __construct(AuthenticationMethod $authenticationMethod)
    {
        $this->authenticationMethod = $authenticationMethod;
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first)
    {
        $request = $this->authenticationMethod->authenticate($request);

        return $next($request);
    }
}
