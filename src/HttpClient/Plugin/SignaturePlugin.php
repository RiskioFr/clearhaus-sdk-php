<?php
declare(strict_types=1);

namespace Clearhaus\HttpClient\Plugin;

use Http\Client\Common\Plugin;
use phpseclib\Crypt\RSA as Encrypter;
use Psr\Http\Message\RequestInterface;

class SignaturePlugin implements Plugin
{
    private $encrypter;
    private $apiKey;

    public function __construct(Encrypter $encrypter, string $apiKey)
    {
        $this->encrypter = $encrypter;
        $this->apiKey = $apiKey;
    }

    /**
     * {@inheritdoc}
     */
    public function handleRequest(RequestInterface $request, callable $next, callable $first)
    {
        $encryptedBody = $this->encrypter->encrypt($request->getBody()->getContents());
        $signature = \sprintf('%s %s %s', $this->apiKey, 'RS256-hex', $encryptedBody);

        return $next(
            $request->withHeader('Signature', $signature)
        );
    }
}
