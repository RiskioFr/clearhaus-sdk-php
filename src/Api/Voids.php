<?php

namespace Clearhaus\Api;

class Voids extends AbstractApi
{
    public function void(string $authorizationId, array $params = []) : array
    {
        return $this->post(sprintf('/authorizations/%s/voids', $authorizationId), $params);
    }

    public function getVoid(string $id) : array
    {
        return $this->get(sprintf('/voids/%s', $id));
    }
}
