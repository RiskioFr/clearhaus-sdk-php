<?php
declare(strict_types=1);

namespace Clearhaus\Api;

class Accounts extends AbstractApi
{
    public function getAccount() : array
    {
        return $this->get('/accounts');
    }
}
