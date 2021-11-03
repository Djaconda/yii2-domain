<?php

namespace PHPKitchen\Domain\Contracts;

use PHPKitchen\Domain\DB\EntitiesRepository;

/**
 * Represents entities controller.
 *
 * @property EntitiesRepository $repository
 *
 * @package PHPKitchen\Domain\Contracts
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
interface EntityController {
    public function getRepository(): Repository;

    public function setRepository(Repository $repository);

    public function findEntityByPk($pk): DomainEntity;
}
