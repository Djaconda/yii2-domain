<?php

namespace PHPKitchen\Domain\DB;

use PHPKitchen\Domain;
use PHPKitchen\Domain\Contracts;
use Throwable;
use yii\db\StaleObjectException;

/**
 * Represents DB records repository.
 *
 * @package PHPKitchen\Domain\DB
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
abstract class RecordsRepository extends Base\Repository {
    public function __construct($config = []) {
        $this->entitiesProviderClassName = Domain\Data\RecordsProvider::class;
        parent::__construct($config);
    }

    //----------------------- ENTITY MANIPULATION METHODS -----------------------//
    /**
     *
     * @return bool result.
     * @throws Domain\Exceptions\UnableToSaveEntityException
     */
    protected function saveEntityInternal(Contracts\DomainEntity $entity, bool $runValidation, ?array $attributes): bool {
        $isEntityNew = $entity->isNew();
        if ($this->triggerModelEvent($isEntityNew ? self::EVENT_BEFORE_ADD : self::EVENT_BEFORE_UPDATE, $entity) && $this->triggerModelEvent(self::EVENT_BEFORE_SAVE, $entity)) {
            $result = $runValidation ? $entity->validateAndSave($attributes) : $entity->saveWithoutValidation($attributes);
        } else {
            $result = false;
        }
        if ($result) {
            $this->triggerModelEvent($isEntityNew ? self::EVENT_BEFORE_ADD : self::EVENT_AFTER_UPDATE, $entity);
            $this->triggerModelEvent(self::EVENT_AFTER_SAVE, $entity);
        } else {
            $exception = new Domain\Exceptions\UnableToSaveEntityException('Failed to save entity ' . $entity::class);
            $exception->errorsList = $entity->getErrors();
            throw $exception;
        }

        return true;
    }

    /**
     * @return bool result.
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function delete(Contracts\DomainEntity $entity): bool {
        $result = $this->triggerModelEvent(self::EVENT_BEFORE_DELETE, $entity) ? $entity->deleteRecord() : false;
        if ($result) {
            $this->triggerModelEvent(self::EVENT_AFTER_DELETE, $entity);
        }

        return $result;
    }

    /**
     * @return bool result.
     */
    public function validate(Contracts\DomainEntity $entity): bool {
        return $entity->validate();
    }

    //----------------------- INSTANTIATION METHODS -----------------------//

    public function createNewEntity(): Contracts\DomainEntity {
        return $this->container->create([
            'class' => $this->entityClassName,
        ]);
    }

    //----------------------- SEARCH METHODS -----------------------//

    public function find(): Finder|RecordQuery {
        return $this->createQuery();
    }

    //----------------------- GETTERS/SETTERS -----------------------//

    public function getRecordClassName(): string {
        return $this->getEntityClassName();
    }
}
