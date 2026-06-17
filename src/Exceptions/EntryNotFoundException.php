<?php

declare(strict_types=1);

namespace PHPShots\Common\Exceptions;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Thrown when no entry was found in the container for a requested identifier.
 *
 * Implements PSR-11 {@see NotFoundExceptionInterface} so the container is a
 * fully compliant {@see \Psr\Container\ContainerInterface} implementation.
 */
class EntryNotFoundException extends Exception implements NotFoundExceptionInterface
{
    public function __construct(string $id = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            $id === ''
                ? 'No entry was found in the container.'
                : "No entry was found in the container for identifier [{$id}].",
            $code,
            $previous,
        );
    }
}
