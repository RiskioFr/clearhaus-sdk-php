<?php

namespace spec\Clearhaus\HttpClient\Plugin;

use Clearhaus\Client;
use Clearhaus\Exception\ApiLimitExceedException;
use Clearhaus\Exception\RuntimeException;
use Clearhaus\Exception\UnauthorizedException;
use Clearhaus\Exception\ValidationFailedException;
use Clearhaus\HttpClient\Plugin\ClearhausExceptionThrower;
use Exception;
use Http\Promise\FulfilledPromise;
use Http\Promise\Promise;
use Http\Promise\RejectedPromise;
use PhpSpec\ObjectBehavior;
use Psr\Http\Message\RequestInterface;
use Zend\Diactoros\Response;
use Zend\Diactoros\Stream;

class ClearhausExceptionThrowerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ClearhausExceptionThrower::class);
    }

    function it_shoud_return_http_response(RequestInterface $request)
    {
        $response = new Response();
        $promise = new FulfilledPromise($response);
        $next = function() use ($promise) {
            return $promise;
        };
        $first = function() {};

        $this->handleRequest($request, $next, $first)->shouldReturnAnInstanceOf(FulfilledPromise::class);
    }

    function it_shoud_throw_unauthorized_exception(RequestInterface $request)
    {
        $response = (new Response())->withStatus(401);
        $promise = new FulfilledPromise($response);
        $next = function() use ($promise) {
            return $promise;
        };
        $first = function() {};

        $this->handleRequest($request, $next, $first)->shouldBeRejectedPromise(UnauthorizedException::class);
    }

    function it_shoud_throw_api_limit_exceed_exception(RequestInterface $request)
    {
        $response = $this->createFailedHttpResponse(400, 40415, 'error message');
        $promise = new FulfilledPromise($response);
        $next = function() use ($promise) {
            return $promise;
        };
        $first = function() {};

        $this->handleRequest($request, $next, $first)->shouldBeRejectedPromise(ApiLimitExceedException::class);
    }

    function it_shoud_throw_validation_failed_exception(RequestInterface $request)
    {
        $response = $this->createFailedHttpResponse(400, 40120, 'error message');
        $promise = new FulfilledPromise($response);
        $next = function() use ($promise) {
            return $promise;
        };
        $first = function() {};

        $this->handleRequest($request, $next, $first)->shouldBeRejectedPromise(ValidationFailedException::class);
    }

    function it_shoud_throw_runtime_exception(RequestInterface $request)
    {
        $response = $this->createFailedHttpResponse(400, 40300, 'error message');
        $promise = new FulfilledPromise($response);
        $next = function() use ($promise) {
            return $promise;
        };
        $first = function() {};

        $this->handleRequest($request, $next, $first)->shouldBeRejectedPromise(RuntimeException::class);
    }

    public function getMatchers() : array
    {
        return [
            'beRejectedPromise' => function($subject, $exceptionClass) {
                return $this->isRejectedPromiseWithException($subject, $exceptionClass);
            },
        ];
    }

    public function isRejectedPromiseWithException(Promise $promise, string $exceptionClass) : bool
    {
        if (!$promise instanceof RejectedPromise) {
            return false;
        }

        try {
            $promise->wait();
        } catch (Exception $e) {
            return get_class($e) === $exceptionClass;
        }

        return false;
    }

    private function createFailedHttpResponse(int $statusCode,int  $errorCode, string $errorMessage = '')
    {
        return $this->createHttpResponse([
            'status' => [
                'code' => $errorCode,
                'message' => $errorMessage,
            ],
        ], $statusCode);
    }

    private function createHttpResponse(array $body, int $statusCode = 200) : Response
    {
        $stream = new Stream('php://memory', 'rw');
        $stream->write(json_encode($body, JSON_FORCE_OBJECT));
        $stream->rewind();

        $httpResponse = (new Response())
            ->withHeader('Content-Type', Client::CONTENT_TYPE)
            ->withStatus($statusCode)
            ->withBody($stream);

        return $httpResponse;
    }
}
