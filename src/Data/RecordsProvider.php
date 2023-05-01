<?php

namespace PHPKitchen\Domain\Data;

use PHPKitchen\Domain\Contracts\Repository;
use PHPKitchen\Domain\Contracts;
use PHPKitchen\Domain\DB\EntitiesRepository;
use PHPKitchen\Domain\DB\RecordQuery;
use PHPKitchen\Domain\DB\RecordsRepository;
use yii\data\ActiveDataProvider;

/**
 * Represents DB records provider.
 *
 * @property EntitiesRepository|RecordsRepository $repository
 * @property RecordQuery $query
 *
 * @package PHPKitchen\Domain\Data
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class RecordsProvider extends ActiveDataProvider {
    protected Repository|EntitiesRepository $_repository;

    public function getRepository(): Repository {
        return $this->_repository;
    }

    public function setRepository(Repository $repository): void {
        $this->_repository = $repository;
    }
}
