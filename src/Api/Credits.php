<?php
declare(strict_types=1);

namespace Clearhaus\Api;

use Clearhaus\Exception\MissingArgumentException;

class Credits extends AbstractApi
{
    public function credit(string $cardId, array $params = []) : array
    {
        if (!isset($params['amount'], $params['currency'])) {
            throw new MissingArgumentException(['amount', 'currency']);
        }

        return $this->post(\sprintf('/cards/%s/credits', $cardId), $params);
    }

    public function getCredit(string $id) : array
    {
        return $this->get(\sprintf('/credits/%s', $id));
    }
}
