<?php
declare(strict_types=1);

namespace Clearhaus\Api;

class Captures extends AbstractApi
{
    public function capture(string $authorizationId, array $params = []) : array
    {
        return $this->post(sprintf('/authorizations/%s/captures', $authorizationId), $params);
    }

    public function getCapture(string $id) : array
    {
        return $this->get(sprintf('/captures/%s', $id));
    }
}
