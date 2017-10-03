<?php
declare(strict_types=1);

namespace Clearhaus\HttpClient\Plugin;

use Clearhaus\Exception\ApiLimitExceedException;
use Clearhaus\Exception\RuntimeException;
use Clearhaus\Exception\UnauthorizedException;
use Clearhaus\Exception\ValidationFailedException;
use Clearhaus\HttpClient\Message\ResponseMediator;
use Http\Client\Common\Plugin;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ErrorPlugin implements Plugin
{
    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first)
    {
        return $next($request)->then(function (ResponseInterface $response) use ($request) {
            if ($response->getStatusCode() < 400 || $response->getStatusCode() > 600) {
                return $response;
            }

            if (401 === $response->getStatusCode()) {
                throw new UnauthorizedException();
            }

            $content = ResponseMediator::getContent($response);

            if (is_array($content) && isset($content['status'])) {
                if ($content['status']['code'] == 40415) {
                    throw new ApiLimitExceedException();
                }

                if ($content['status']['code'] >= 40000 && $content['status']['code'] < 40200) {
                    throw new ValidationFailedException($content['status']['message'], 422);
                }

                if ($content['status']['code'] >= 40200 && $content['status']['code'] <= 50000) {
                    throw new RuntimeException($content['status']['message'], 422);
                }
            }
        });
    }
}
