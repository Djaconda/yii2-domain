<?php

namespace PHPKitchen\Examples\User;

use PHPKitchen\Domain\DB\RecordQuery;

/**
 * Represents user DB record query.
 *
 * @package PHPKitchen\Examples\User
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class UserQuery extends RecordQuery {
    public function active(): self {
        return $this->andWhere('status=:status', ['status' => UserEntity::STATUS_ACTIVE]);
    }

    public function inactive(): self {
        return $this->andWhere('status=:status', ['status' => UserEntity::STATUS_INACTIVE]);
    }
}
