<?php

namespace PHPKitchen\Domain\Contracts;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\Contracts
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
interface Repository {
    public const EVENT_BEFORE_SAVE = 'beforeSave';
    public const EVENT_AFTER_SAVE = 'afterSave';
    public const EVENT_BEFORE_ADD = 'beforeAdd';
    public const EVENT_BEFORE_UPDATE = 'beforeUpdate';
    public const EVENT_AFTER_ADD = 'afterAdd';
    public const EVENT_AFTER_UPDATE = 'afterUpdate';
    public const EVENT_BEFORE_DELETE = 'beforeDelete';
    public const EVENT_AFTER_DELETE = 'afterDelete';

    public function validateAndSave(DomainEntity $entity, ?array $attributes = null);

    public function saveWithoutValidation(DomainEntity $entity, ?array $attributes = null);

    public function delete(DomainEntity $entity): bool;

    public function validate(DomainEntity $entity): bool;

    public function refresh(DomainEntity $entity): bool;

    public function findOneWithPk($pk);

    public function findAll();

    public function each();

    public function find();

    public function createNewEntity();

    public function getEntitiesProvider();

    public function isNewOrJustAdded(DomainEntity $entity): bool;

    public function isJustUpdated(DomainEntity $entity): bool;

    public function isJustAdded(DomainEntity $entity): bool;

    public function getDirtyAttributes(DomainEntity $entity, array $names = null): array;

    public function getOldAttributes(DomainEntity $entity): array;

    public function getOldAttribute(DomainEntity $entity, string $name);

    public function isAttributeChanged(DomainEntity $entity, string $name, bool $identical = true): bool;

    public function setChangedAttributes(DomainEntity $entity, array $changedAttributes): void;

    public function getChangedAttributes(DomainEntity $entity): array;

    public function getChangedAttribute(DomainEntity $entity, string $name);

    public function wasAttributeChanged(DomainEntity $entity, string $name): bool;

    public function wasAttributeValueChanged(DomainEntity $entity, string $name): bool;
}
