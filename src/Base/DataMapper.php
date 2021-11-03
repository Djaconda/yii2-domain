<?php

namespace PHPKitchen\Domain\Base;

use Exception;
use PHPKitchen\Domain\Contracts\EntityDataSource;
use PHPKitchen\Domain\Contracts\Record;
use PHPKitchen\Domain\DB\EntitiesRepository;

/**
 * Represents
 *
 * @property mixed $primaryKey
 *
 * @package PHPKitchen\Domain\base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class DataMapper extends Component {
    /**
     * @var \PHPKitchen\Domain\DB\Record
     */
    protected Record $dataSource;
    protected ?array $relatedEntities = null;

    /**
     * DataMapper constructor.
     */
    public function __construct($dataSource, $config = []) {
        $this->dataSource = $dataSource;
        parent::__construct($config);
    }

    public function canGet($name): bool {
        $dataSource = $this->dataSource;

        return $dataSource->canGetProperty($name);
    }

    public function canSet($name): bool {
        $dataSource = $this->dataSource;

        return $dataSource->canSetProperty($name);
    }

    public function isPropertySet($name): bool {
        return isset($this->dataSource->$name);
    }

    public function getDataSource() {
        return $this->dataSource;
    }

    public function get($name) {
        if (isset($this->relatedEntities[$name])) {
            $property = $this->relatedEntities[$name];
        } else {
            $property = $this->getPropertyFromDataSource($name);
        }

        return $property;
    }

    public function refresh(): bool {
        $this->clearRelatedEntities();

        return $this->getDataSource()->refresh();
    }

    protected function getPropertyFromDataSource($propertyName) {
        $property = $this->canGet($propertyName) ? $this->dataSource->$propertyName : null;

        if ($property instanceof EntityDataSource && ($repository = $this->findRepositoryForRecord($property))) {
            $property = $repository->createEntityFromSource($property);
            $this->relatedEntities[$propertyName] = $property;
        } elseif ($this->propertyIsAnArrayOfRecords($property)) {
            $repository = $this->findRepositoryForRecord($property[0]);
            if ($repository !== null) {
                $entities = [];
                foreach ($property as $key => $item) {
                    $entities[$key] = $repository->createEntityFromSource($item);
                }
                $property = &$entities;
                $this->relatedEntities[$propertyName] = &$entities;
            }
        }

        return $property;
    }

    protected function propertyIsAnArrayOfRecords($property): bool {
        return is_array($property) && isset($property[0]) && ($property[0] instanceof Record) && $this->arrayHasOnlyRecords($property);
    }

    protected function arrayHasOnlyRecords(&$array) {
        return array_reduce(
            $array,
            static fn($result, $element) => $element instanceof Record
        );
    }

    /**
     * @param $record
     *
     * @return null|EntitiesRepository
     */
    protected function findRepositoryForRecord($record): ?EntitiesRepository {
        $recordClass = get_class($record);
        $repositoryClass = strpos($recordClass, 'Record') !== false ? str_replace('Record', 'Repository',
            $recordClass) : null;
        $container = $this->container;
        try {
            /** @var EntitiesRepository $repository */
            $repository = $repositoryClass ? $container->create($repositoryClass) : null;
        } catch (Exception $e) {
            $repository = null;
        }

        return $repository;
    }

    protected function clearRelatedEntities(): void {
        $this->relatedEntities = [];
    }

    public function set(string $name, $value) {
        return $this->canSet($name) ? $this->dataSource->$name = $value : null;
    }

    public function unSetProperty(string $name): void {
        if ($this->isPropertySet($name)) {
            unset($this->dataSource->$name);
        }
    }

    public function isRecordNew(): bool {
        return $this->dataSource->isNew();
    }

    public function getPrimaryKey() {
        return $this->dataSource->primaryKey;
    }

    public function load($data): bool {
        return $this->dataSource->load($data, '');
    }

    public function getAttributes(): array {
        return $this->dataSource->attributes;
    }
}
