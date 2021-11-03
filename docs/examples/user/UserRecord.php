<?php

namespace PHPKitchen\Examples\User;

use PHPKitchen\Domain\DB\Record;
use yii\db\ActiveQuery;
use yii2tech\ar\role\RoleBehavior;

/**
 * Represents record of a user in the DB.
 *
 * Attributes:
 *
 * @property int $id
 * @property int $status
 * @property int $email
 *
 * Relations:
 * @property ProfileRecord $profile link to profile table in the DB.
 *
 * @package PHPKitchen\Examples\User
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class UserRecord extends Record {
    public function behaviors(): array {
        return [
            'role' => [
                // see https://github.com/yii2tech/ar-role
                'class' => RoleBehavior::class,
                'roleRelation' => 'profile',
            ],
        ];
    }

    /**
     * @override
     * @inheritdoc
     */
    public static function tableName(): string {
        return 'User';
    }

    /**
     * @inheritdoc
     */
    public function rules(): array {
        return [
            [
                [
                    'id',
                    'status',
                ],
                'required',
            ],
        ];
    }

    public function getProfile(): ActiveQuery {
        return $this->hasOne(ProfileRecord::class, ['userId' => 'id'])->alias('profile');
    }
}
