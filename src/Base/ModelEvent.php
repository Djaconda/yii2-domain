<?php

namespace PHPKitchen\Domain\Base;

use PHPKitchen\Domain\Contracts\DomainEntity;
use yii\base\Event;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class ModelEvent extends Event {
    protected bool $_valid = true;

    public function __construct(protected DomainEntity $_entity, $config = []) {
        parent::__construct($config);
    }

    public function isValid(): bool {
        return $this->_valid;
    }

    public function failAndMarkHandled(): void {
        $this->fail()->markHandled();
    }

    public function fail(): self {
        $this->_valid = false;

        return $this;
    }

    public function markHandled(): self {
        $this->handled = true;

        return $this;
    }

    public function getEntity(): DomainEntity {
        return $this->_entity;
    }
}
