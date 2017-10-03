<?php

namespace Clearhaus\Api;

class Refunds extends AbstractApi
{
    public function refund(string $authorizationId, array $params = []) : array
    {
        return $this->post(sprintf('/authorizations/%s/refunds', $authorizationId), $params);
    }

    public function getRefund(string $id) : array
    {
        return $this->get(sprintf('/refunds/%s', $id));
    }
}
