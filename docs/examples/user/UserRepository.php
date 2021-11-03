<?php

namespace PHPKitchen\Examples\User;

use PHPKitchen\Domain\DB\EntitiesRepository;

/**
 * Represents users repository.
 *
 * @package PHPKitchen\Examples\User
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class UserRepository extends EntitiesRepository {
    public function init(): void {
        $this->on(self::EVENT_BEFORE_SAVE, function () {
            $this->logInfo('here we can handle events');
        });
    }
}
