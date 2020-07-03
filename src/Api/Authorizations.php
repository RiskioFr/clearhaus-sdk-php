<?php
declare(strict_types=1);

namespace Clearhaus\Api;

use Clearhaus\Exception\MissingArgumentException;

class Authorizations extends AbstractApi
{
    public function authorize(array $params) : array
    {
        if (!isset($params['amount'], $params['currency'])) {
            throw new MissingArgumentException(['amount', 'currency']);
        }

        if (isset($params['card']['pares'])) {
            $params['card']['pares'] = urlencode($params['card']['pares']);
        }
        if (isset($params['mobilepayonline']['pares'])) {
            $params['mobilepayonline']['pares'] = urlencode($params['mobilepayonline']['pares']);
        }

        return $this->post('/authorizations', $params);
    }

    public function getAuthorization(string $id) : array
    {
        return $this->get(\sprintf('/authorizations/%s', $id));
    }
}
