<?php

namespace PHPKitchen\Examples\User;

use PHPKitchen\Domain\Base\Entity;

/**
 * Represents user entity.
 *
 * Attributes fetched from UserRecord:
 *
 * @property int $id
 * @property int $status
 * @property int $email
 * Attributes fetched from ProfileRecord:
 * @property int $fullName
 * @property int $dateOfBirth
 *
 * @package PHPKitchen\Examples\User
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class UserEntity extends Entity {
    public const STATUS_ACTIVE = 1;
    public const STATUS_INACTIVE = 2;

    public function activate(): void {
        $this->status = self::STATUS_ACTIVE;
    }

    public function deActivate(): void {
        $this->status = self::STATUS_INACTIVE;
    }

    public function isActive(): bool {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isInactive(): bool {
        return $this->status === self::STATUS_INACTIVE;
    }
}
