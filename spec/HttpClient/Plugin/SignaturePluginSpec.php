<?php

namespace spec\Clearhaus\HttpClient\Plugin;

use Clearhaus\HttpClient\Plugin\SignaturePlugin;
use phpseclib\Crypt\RSA as Encrypter;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;

class SignaturePluginSpec extends ObjectBehavior
{
    private $apiKey = '123456789';

    public function let(Encrypter $encrytper)
    {
        $this->beConstructedWith($encrytper, $this->apiKey);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SignaturePlugin::class);
    }

    function it_should_add_signature_header(RequestInterface $request, StreamInterface $stream)
    {
        $result = 'foo';
        $next = function() use ($result) { return $result; };
        $first = function() {};

        $requestBody = 'amount=2050&currency=EUR&ip=1.1.1.1';

        $request->getBody()->willReturn($stream);
        $stream->getContents()->willReturn($requestBody);

        $request
            ->withHeader('Signature', Argument::type('string'))
            ->willReturn(Argument::type(RequestInterface::class));

        $this->handleRequest($request, $next, $first)->shouldReturn($result);
    }
}
