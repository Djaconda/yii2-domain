<?php

namespace PHPKitchen\Domain\DB;

use Iterator;
use PHPKitchen\Domain\Base\MagicObject;
use PHPKitchen\Domain\Contracts;
use yii\db\BatchQueryResult;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\DB
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class SearchResult extends MagicObject implements Iterator {
    private ?\PHPKitchen\Domain\Contracts\Repository $_repository = null;

    public function __construct(private BatchQueryResult $_queryResultIterator, Contracts\Repository $repository, $config = []) {
        $this->setRepository($repository);
        parent::__construct($config);
    }

    public function current() {
        $iterator = $this->getQueryResultIterator();
        $value = $iterator->current();
        if ($iterator->each && $value instanceof Contracts\EntityDataSource) {
            $entity = $this->getRepository()->createEntityFromSource($value);
        } elseif (!$iterator->each) {
            foreach ($value as $record) {
                $entity[] = $this->getRepository()->createEntityFromSource($record);
            }
        } else {
            $entity = null;
        }

        return $entity;
    }

    public function next(): void {
        $this->getQueryResultIterator()->next();
    }

    public function key() {
        return $this->getQueryResultIterator()->key();
    }

    public function valid(): bool {
        return $this->getQueryResultIterator()->valid();
    }

    public function rewind(): void {
        $this->getQueryResultIterator()->rewind();
    }

    protected function getQueryResultIterator(): BatchQueryResult {
        return $this->_queryResultIterator;
    }

    public function getRepository(): Contracts\Repository {
        return $this->_repository;
    }

    public function setRepository(Contracts\Repository $repository): void {
        $this->_repository = $repository;
    }
}
