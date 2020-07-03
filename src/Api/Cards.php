<?php
declare(strict_types=1);

namespace Clearhaus\Api;

use Clearhaus\Exception\MissingArgumentException;

class Cards extends AbstractApi
{
    public function createCard(array $params = []) : array
    {
        if (!isset($params['number'], $params['expire_month'], $params['expire_year'], $params['csc'])) {
            throw new MissingArgumentException(['number', 'expire_month', 'expire_year', 'csc']);
        }

        return $this->post('/cards', $params);
    }

    public function getCard(string $id) : array
    {
        return $this->get(\sprintf('/cards/%s', $id));
    }
}
