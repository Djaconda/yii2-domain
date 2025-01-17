<?php

namespace PHPKitchen\Domain\DB;

use PHPKitchen\Domain\DB\Base\Repository;
use PHPKitchen\Domain\Base\DataMapper;
use PHPKitchen\Domain\Contracts\DomainEntity;
use PHPKitchen\Domain\Contracts\Record;
use PHPKitchen\Domain\Contracts\EntityDataSource;
use PHPKitchen\Domain;
use PHPKitchen\Domain\Contracts;
use PHPKitchen\Domain\Data\EntitiesProvider;
use PHPKitchen\Domain\Exceptions\UnableToSaveEntityException;
use yii\base\InvalidConfigException;

/**
 * Represents entities DB repository.
 *
 * @property string $finderClassName public alias of the {@link _finderClass}
 * @property string $defaultFinderClassName public alias of the {@link _defaultFinderClass}
 *
 * @package PHPKitchen\Domain\base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class EntitiesRepository extends Repository {
    /**
     * @var string data mapper class name. Required to map data from record to entity. Change it in {@link init()} method
     * if you need custom mapper. But be aware - data mapper is internal class and it is strongly advised to not
     * touch this property.
     */
    public string $dataMapperClassName = DataMapper::class;
    /**
     * @var ?string indicates what finder to use. By default, equal following template "{model name}Finder" where model name is equal to
     * the repository class name without "Repository" suffix.
     */
    private ?string $_finderClassName = null;
    /**
     * @var string entities finder class name. This class being used if no finder specified in morel directory. Change it
     * in {@link init()} method if you need custom default finder.
     */
    private string $_defaultFinderClassName = Finder::class;

    public function __construct($config = []) {
        $this->entitiesProviderClassName = EntitiesProvider::class;
        parent::__construct($config);
    }

    //region ---------------------- ENTITY MANIPULATION METHODS -------------------

    /**
     * @throws UnableToSaveEntityException
     */
    protected function saveEntityInternal(DomainEntity $entity, bool $runValidation, ?array $attributes): bool {
        $isEntityNew = $entity->isNew();
        $dataSource = $entity->getDataMapper()->getDataSource();

        if ($this->triggerModelEvent($isEntityNew ? self::EVENT_BEFORE_ADD : self::EVENT_BEFORE_UPDATE, $entity) && $this->triggerModelEvent(self::EVENT_BEFORE_SAVE, $entity)) {
            $result = $runValidation ? $dataSource->validateAndSave($attributes) : $dataSource->saveWithoutValidation($attributes);
        } else {
            $result = false;
        }
        if ($result) {
            $this->triggerModelEvent($isEntityNew ? self::EVENT_AFTER_ADD : self::EVENT_AFTER_UPDATE, $entity);
            $this->triggerModelEvent(self::EVENT_AFTER_SAVE, $entity);
        } else {
            $exception = new UnableToSaveEntityException('Failed to save entity ' . $entity::class);
            $exception->errorsList = $dataSource->getErrors();
            throw $exception;
        }

        return $result;
    }

    public function delete(DomainEntity $entity): bool {
        if ($this->triggerModelEvent(self::EVENT_BEFORE_DELETE, $entity)) {
            $result = $entity->getDataMapper()->getDataSource()->deleteRecord();
        } else {
            $result = false;
        }
        if ($result) {
            $this->triggerModelEvent(self::EVENT_AFTER_DELETE, $entity);
        }

        return $result;
    }

    public function validate(DomainEntity $entity): bool {
        $dataSource = $entity->getDataMapper()->getDataSource();

        return $dataSource->validate();
    }

    public function refresh(DomainEntity $entity): bool {
        return $entity->getDataMapper()->refresh();
    }
    //endregion

    //region ----------------------- ENTITY DATA METHODS --------------------------
    public function isNewOrJustAdded(DomainEntity $entity): bool {
        return $entity->isNew() || $this->isJustAdded($entity);
    }

    public function isJustUpdated(DomainEntity $entity): bool {
        return !$this->isJustAdded($entity);
    }

    public function isJustAdded(DomainEntity $entity): bool {
        $dataSource = $entity->getDataMapper()->getDataSource();

        return $dataSource->isJustAdded();
    }

    public function getDirtyAttributes(DomainEntity $entity, array $names = null): array {
        $dataSource = $entity->getDataMapper()->getDataSource();

        return $dataSource->getDirtyAttributes($names);
    }

    public function getOldAttributes(DomainEntity $entity): array {
        $dataSource = $entity->getDataMapper()->getDataSource();

        return $dataSource->getOldAttributes();
    }

    public function getOldAttribute(DomainEntity $entity, string $name) {
        $dataSource = $entity->getDataMapper()->getDataSource();

        return $dataSource->getOldAttribute($name);
    }

    public function isAttributeChanged(DomainEntity $entity, string $name, bool $identical = true): bool {
        $dataSource = $entity->getDataMapper()->getDataSource();

        return $dataSource->isAttributeChanged($name, $identical);
    }

    public function setChangedAttributes(DomainEntity $entity, array $changedAttributes): void {
        $dataSource = $entity->getDataMapper()->getDataSource();

        $dataSource->setChangedAttributes($changedAttributes);
    }

    public function getChangedAttributes(DomainEntity $entity): array {
        $dataSource = $entity->getDataMapper()->getDataSource();

        return $dataSource->getChangedAttributes();
    }

    public function getChangedAttribute(DomainEntity $entity, string $name) {
        $dataSource = $entity->getDataMapper()->getDataSource();

        return $dataSource->getChangedAttribute($name);
    }

    /**
     * Method returns the result of checking whether the attribute was changed during
     * the saving of the entity.
     * Be aware! False positive possible because of Yii BaseActiveRecord::getDirtyAttributes()
     * method compares values with type matching
     *
     *
     */
    public function wasAttributeChanged(DomainEntity $entity, string $name): bool {
        $dataSource = $entity->getDataMapper()->getDataSource();

        return $dataSource->wasAttributeChanged($name);
    }

    /**
     * Method returns the result of checking whether the attribute value was changed during
     * the saving of the entity.
     * Be aware! This method compare old value with new without type comparison.
     */
    public function wasAttributeValueChanged(DomainEntity $entity, string $name): bool {
        $oldValue = $this->getChangedAttribute($entity, $name);
        if ($oldValue === false) {
            return false;
        }

        return $oldValue != $entity->{$name};
    }
    //endregion

    //region ----------------------- INSTANTIATION METHODS ------------------------
    public function createNewEntity(): DomainEntity {
        $container = $this->container;

        return $container->create([
            'class' => $this->entityClassName,
            'dataMapper' => $container->create($this->dataMapperClassName, [$this->createRecord()]),
        ]);
    }

    private function createRecord(): Record {
        return $this->container->create($this->recordClassName);
    }

    public function createEntityFromSource(EntityDataSource $record): DomainEntity {
        $container = $this->container;

        return $container->create([
            'class' => $this->entityClassName,
            'dataMapper' => $container->create($this->dataMapperClassName, [$record]),
        ]);
    }

    public function find(): Finder|RecordQuery {
        return $this->createFinder();
    }

    protected function createFinder(): Finder {
        return $this->container->create($this->finderClassName, [
            $query = $this->createQuery(),
            $repository = $this,
        ]);
    }
    //endregion

    //region ----------------------- GETTERS/SETTERS ------------------------------
    protected function getFinderClassName(): string {
        if (null === $this->_finderClassName) {
            $this->_finderClassName = $this->buildModelElementClassName('Finder', $this->defaultFinderClassName);
        }

        return $this->_finderClassName;
    }

    public function setFinderClassName(string $finderClassName): void {
        $this->_finderClassName = $finderClassName;
    }

    public function getDefaultFinderClassName(): string {
        return $this->_defaultFinderClassName;
    }

    public function setDefaultFinderClassName(string $defaultFinderClass): void {
        if (!class_exists($defaultFinderClass) && !interface_exists($defaultFinderClass)) {
            throw new InvalidConfigException('Default finder class should be an existing class or interface!');
        }
        $this->_defaultFinderClassName = $defaultFinderClass;
    }
    //endregion
}
