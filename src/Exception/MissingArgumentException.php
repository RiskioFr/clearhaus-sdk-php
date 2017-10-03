<?php

namespace Clearhaus\Exception;

use Throwable;

class MissingArgumentException extends ErrorException
{
    public function __construct(array $required, int $code = 0, Throwable $previous = null)
    {
        parent::__construct(
            sprintf(
                'One or more of required ("%s") parameters is missing!',
                implode('", "', $required)
            ),
            $code,
            $previous
        );
    }
}
