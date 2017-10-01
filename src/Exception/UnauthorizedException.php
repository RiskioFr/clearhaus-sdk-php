<?php

namespace Clearhaus\Exception;

class UnauthorizedException extends RuntimeException
{
    protected $message = 'You have provided an invalid API key.';
}
