<?php

namespace PHPKitchen\Domain\DB;

use PHPKitchen\Domain\Contracts\Repository;
use PHPKitchen\Domain\Contracts\EntityDataSource;
use PHPKitchen\Domain\Base\MagicObject;
use PHPKitchen\Domain\Contracts;
use PHPKitchen\Domain\Contracts\Specification;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\DB
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class Finder extends MagicObject {
    public function __construct(private Specification $_query, private Repository $_repository, $config = []) {
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
        return $record instanceof EntityDataSource
            ? $this->getRepository()->createEntityFromSource($record)
            : $record;
    }

    public function __call($name, $params) {
        $query = $this->getQuery();
        if ($query->hasMethod($name)) {
            $result = call_user_func_array([$query, $name], $params);
            $queryClassName = $query::class;
            if ($result instanceof $queryClassName) {
                $result = $this;
            }
        } else {
            $result = parent::__call($name, $params);
        }

        return $result;
    }

    public function getQuery() {
        return $this->_query;
    }

    protected function getRepository(): Repository {
        return $this->_repository;
    }
}
