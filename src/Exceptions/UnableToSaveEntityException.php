<?php

namespace PHPKitchen\Domain\Exceptions;

use Exception;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\Exceptions
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class UnableToSaveEntityException extends Exception {
    public array $errorsList = [];
}
