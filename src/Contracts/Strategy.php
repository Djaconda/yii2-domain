<?php

namespace PHPKitchen\Domain\Contracts;

/**
 * Represents
 *
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
interface Strategy {
    public const EVENT_BEFORE_CALL = 'beforeCall';
    public const EVENT_AFTER_CALL = 'afterCall';

    public function call();
}
