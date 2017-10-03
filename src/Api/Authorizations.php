<?php
declare(strict_types=1);

namespace Clearhaus\Api;

use Clearhaus\Exception\MissingArgumentException;

class Authorizations extends AbstractApi
{
    public function authorize(array $params) : array
    {
        if (!isset($params['amount'], $params['currency'], $params['card'])) {
            throw new MissingArgumentException(['amount', 'currency', 'card']);
        }

        return $this->post('/authorizations', $params);
    }

    public function authorizeFromCardId(string $cardId, array $params) : array
    {
        if (!isset($params['amount'], $params['currency'])) {
            throw new MissingArgumentException(['amount', 'currency']);
        }

        return $this->post(sprintf('/cards/%s/authorizations', $cardId), $params);
    }

    public function getAuthorization(string $id) : array
    {
        return $this->get(sprintf('/authorizations/%s', $id));
    }
}
