<?php

namespace PHPKitchen\Examples\User;

use PHPKitchen\Domain\DB\Record;

/**
 * Represents user profile record in the DB.
 *
 * Attributes:
 *
 * @property int $fullName
 * @property int $dateOfBirth
 *
 * @package PHPKitchen\Examples\User
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class ProfileRecord extends Record {
    /**
     * @override
     * @inheritdoc
     */
    public static function tableName(): string {
        return 'UserProfile';
    }

    /**
     * @override
     * @inheritdoc
     */
    public function rules(): array {
        return [
            [
                [
                    'id',
                    'userId',
                    'fullName',
                    'dateOfBirth',
                ],
                'required',
            ],
        ];
    }
}
