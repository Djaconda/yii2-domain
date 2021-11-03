<?php

namespace PHPKitchen\Domain\DB;

use PHPKitchen\Domain\Base\MagicObject;
use PHPKitchen\Domain\Contracts;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\DB
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class Finder extends MagicObject {
    private Contracts\Specification $_query;
    private Contracts\Repository $_repository;

    public function __construct(Contracts\Specification $query, Contracts\Repository $repository, $config = []) {
        $this->_query = $query;
        $this->_repository = $repository;
        parent::__construct($config);
    }

    public function asArray() {
        return $this->getQuery()->asArray();
    }

    public function all(): array {
        $queryResult = $this->getQuery()->all();
        $entities = [];
        foreach ($queryResult as $key => $record) {
            $entities[$key] = $this->createEntityFromRecord($record);
        }

        return $entities;
    }

    public function one() {
        $queryResult = $this->getQuery()->one();

        return $this->createEntityFromRecord($queryResult);
    }

    public function oneWithPk($pk) {
        $queryResult = $this->getQuery()->oneWithPk($pk);

        return $this->createEntityFromRecord($queryResult);
    }

    public function batch($batchSize = 100) {
        $iterator = $this->getQuery()->batch($batchSize);

        return $this->container->create(SearchResult::class, [$iterator, $this->getRepository()]);
    }

    public function each($batchSize = 100) {
        $iterator = $this->getQuery()->each($batchSize);

        return $this->container->create(SearchResult::class, [$iterator, $this->getRepository()]);
    }

    protected function createEntityFromRecord($record) {
        if ($record instanceof Contracts\EntityDataSource) {
            $entity = $this->getRepository()->createEntityFromSource($record);
        } else {
            $entity = $record;
        }

        return $entity;
    }

    public function __call($name, $params) {
        $query = $this->getQuery();
        if ($query->hasMethod($name)) {
            $result = call_user_func_array([$query, $name], $params);
            $queryClassName = get_class($query);
            if (is_object($result) && is_a($result, $queryClassName)) {
                $result = $this;
            }
        } else {
            $result = parent::__call($name, $params);
        }

        return $result;
    }

    /**
     * @return Contracts\Specification|RecordQuery
     */
    public function getQuery() {
        return $this->_query;
    }

    protected function getRepository(): Contracts\Repository {
        return $this->_repository;
    }
}
