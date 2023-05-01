<?php

namespace PHPKitchen\Domain\DB\Base;

use PHPKitchen\Domain\Base\ModelEvent;
use PHPKitchen\Domain\Contracts\DomainEntity;
use Exception;
use PHPKitchen\Domain;
use PHPKitchen\Domain\Base\Component;
use PHPKitchen\Domain\Contracts;
use PHPKitchen\Domain\Data\EntitiesProvider;
use PHPKitchen\Domain\DB\Finder;
use PHPKitchen\Domain\DB\RecordQuery;
use PHPKitchen\Domain\Mixins\TransactionAccess;
use yii\base\InvalidConfigException;

/**
 * Represents base DB repository.
 *
 * GETTERS/SETTERS:
 *
 * @property string $className public alias of the {@link _className}
 * @property string $entityClassName public alias of the {@link _entityClassName}
 * @property string $queryClassName public alias of the {@link _queryClassName}
 * @property string $defaultQueryClassName public alias of the {@link _defaultQueryClassName}
 * @property string $recordClassName public alias of the {@link _recordClassName}
 *
 * @package PHPKitchen\Domain\DB\Base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
abstract class Repository extends Component implements Contracts\Repository {
    use TransactionAccess;

    /**
     * @var array Stores errors which could occur during save process
     */
    public array $errors = [];
    /**
     * @var bool indicates whether to throw exception or handle it
     */
    public bool $throwExceptions = false;
    /**
     * @var bool indicates whether to use DB transaction or not.
     */
    public bool $useTransactions = true;
    /**
     * @var string entities provider class name. Change it in {@link init()} method if you need
     * custom provider.
     */
    public string $entitiesProviderClassName;
    /**
     * @var string class name of an event that being triggered on each important action. Change it in {@link init()} method
     * if you need custom event.
     */
    public string $modelEventClassName = ModelEvent::class;
    /**
     * @var string records query class name. This class being used if no query specified in morel directory. Change it
     * in {@link init()} method if you need custom default query.
     */
    private string $_defaultQueryClassName = RecordQuery::class;
    private ?string $_className = null;
    /**
     * @var nuLL|string indicates what entity to use. By default, equal following template "{model name}Entity" where model name is equal to
     * the repository class name without "Repository" suffix.
     */
    private ?string $_entityClassName = null;
    /**
     * @var null|string indicates what records query to use. By default, equal following template "{model name}Query" where model name is equal to
     * the repository class name without "Repository" suffix.
     */
    private ?string $_queryClassName = null;
    /**
     * @var null|string indicates what record to use. By default, equal following template "{model name}Record" where model name is equal to
     * the repository class name without "Repository" suffix.
     */
    private ?string $_recordClassName = null;

    abstract public function find(): Finder|RecordQuery;

    abstract protected function saveEntityInternal(DomainEntity $entity, bool $runValidation, ?array $attributes): bool;

    //region ----------------------- ENTITY MANIPULATION METHODS ------------------------

    public function validateAndSave(DomainEntity $entity, ?array $attributes = null): bool {
        $this->clearErrors();

        return $this->useTransactions ? $this->saveEntityUsingTransaction($entity, $runValidation = true,
            $attributes) : $this->saveEntityInternal($entity, $runValidation = true, $attributes);
    }

    public function saveWithoutValidation(DomainEntity $entity, ?array $attributes = null): bool {
        $this->clearErrors();

        return $this->useTransactions ? $this->saveEntityUsingTransaction($entity, $runValidation = false,
            $attributes) : $this->saveEntityInternal($entity, $runValidation = false, $attributes);
    }

    protected function saveEntityUsingTransaction(
        DomainEntity $entity,
        bool $runValidation,
        ?array $attributes
    ): bool {
        $this->beginTransaction();
        $exception = null;
        try {
            $result = $this->saveEntityInternal($entity, $runValidation, $attributes);
            if ($result) {
                $this->commitTransaction();
            }
        } catch (Exception $e) {
            $result = false;
            $exception = $e;
            $this->addError($e->getMessage());
        }
        if (!$result) {
            $this->rollbackTransaction();
        }
        if ($exception && $this->throwExceptions) {
            throw $e;
        }

        return $result;
    }

    /**
     * This method is called at the beginning of inserting or updating a record.
     * The default implementation will trigger an [[EVENT_BEFORE_INSERT]] event when `$insert` is `true`,
     * or an [[EVENT_BEFORE_UPDATE]] event if `$insert` is `false`.
     * When overriding this method, make sure you call the parent implementation like the following:
     *
     * ```php
     * public function beforeSave($insert)
     * {
     *     if (parent::beforeSave($insert)) {
     *         // ...custom code here...
     *         return true;
     *     } else {
     *         return false;
     *     }
     * }
     * ```
     *
     * @return bool whether the insertion or updating should continue.
     * If `false`, the insertion or updating will be cancelled.
     */
    protected function triggerModelEvent(string $eventName, DomainEntity $entity): bool {
        /**
         * @var domain\Base\ModelEvent $event
         */
        $event = $this->container->create($this->modelEventClassName, [$entity]);
        $this->trigger($eventName, $event);

        return $event->isValid();
    }

    /**
     * @return EntitiesProvider an instance of data provider.
     */
    public function getEntitiesProvider(): EntitiesProvider {
        return $this->container->create([
            'class' => $this->entitiesProviderClassName,
            'query' => $this->createQuery(),
            'repository' => $this,
        ]);
    }
    //endregion-

    //region ----------------------- SEARCH METHODS -------------------------------------

    /**
     * @param mixed $pk primary key of the entity
     *
     * @return Contracts\DomainEntity
     */
    public function findOneWithPk($pk): ?DomainEntity {
        return $this->find()->oneWithPk($pk);
    }

    /**
     * @return Contracts\DomainEntity[]
     */
    public function findAll(): array {
        return $this->find()->all();
    }

    /**
     * @return Contracts\DomainEntity[]
     */
    public function each(int $batchSize = 100): array {
        return $this->find()->each($batchSize);
    }

    /**
     * @return Contracts\DomainEntity[][]
     */
    public function getBatchIterator(int $batchSize = 100): array {
        return $this->find()->each($batchSize);
    }

    public function createQuery(): Contracts\RecordQuery {
        return $this->container->create($this->queryClassName, [$recordClass = $this->recordClassName]);
    }

    public function getErrors(): array {
        return $this->errors;
    }

    public function setErrors(array $errors): void {
        $this->errors = $errors;
    }

    /**
     * Adds error to the errors array
     *
     * @param $error
     */
    public function addError($error): void {
        $this->errors[] = $error;
    }

    /**
     * Clears errors
     */
    public function clearErrors(): void {
        $this->setErrors([]);
    }

    public function getDefaultQueryClassName(): string {
        return $this->_defaultQueryClassName;
    }

    public function setDefaultQueryClassName(string $defaultQueryClass): void {
        if (!class_exists($defaultQueryClass) && !interface_exists($defaultQueryClass)) {
            throw new InvalidConfigException('Default query class should be an existing class or interface!');
        }
        $this->_defaultQueryClassName = $defaultQueryClass;
    }

    public function getClassName(): string {
        if (null === $this->_className) {
            $this->_className = static::class;
        }

        return $this->_className;
    }

    public function setClassName(string $className): void {
        $this->_className = $className;
    }

    public function getEntityClassName(): string {
        if (null === $this->_entityClassName) {
            $this->_entityClassName = $this->buildModelElementClassName('Entity');
        }

        return $this->_entityClassName;
    }

    public function setEntityClassName(string $entityClassName): void {
        $this->_entityClassName = $entityClassName;
    }

    public function getQueryClassName(): string {
        if (null === $this->_queryClassName) {
            $this->_queryClassName = $this->buildModelElementClassName('Query', $this->defaultQueryClassName);
        }

        return $this->_queryClassName;
    }

    public function setQueryClassName(string $queryClassName): void {
        $this->_queryClassName = $queryClassName;
    }

    public function getRecordClassName(): string {
        if (null === $this->_recordClassName) {
            $this->_recordClassName = $this->buildModelElementClassName('Record');
        }

        return $this->_recordClassName;
    }

    public function setRecordClassName(string $recordClassName): void {
        $this->_recordClassName = $recordClassName;
    }

    protected function buildModelElementClassName(string $modelElement, ?string $defaultClass = null): string {
        $selfClassName = $this->className;
        $elementClassName = str_replace('Repository', $modelElement, $selfClassName);
        if (!class_exists($elementClassName) && !interface_exists($elementClassName)) {
            if ($defaultClass) {
                $elementClassName = $defaultClass;
            } else {
                throw new InvalidConfigException("{$modelElement} class should be an existing class or interface!");
            }
        }

        return $elementClassName;
    }
    //endregion
}
