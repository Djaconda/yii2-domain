<?php

namespace PHPKitchen\Domain\Web\Contracts;

use PHPKitchen\Domain\Contracts\Repository;
use PHPKitchen\Domain\DB\EntitiesRepository;

/**
 * Represent classes aware of repository
 *
 * Own properties:
 *
 * @property EntitiesRepository $repository
 *
 * @package PHPKitchen\Domain\Web\Contracts
 * @author Vladimir Siritsa <vladimir.siritsa@bitfocus.com>
 */
interface RepositoryAware {
    /**
     * @return Repository|EntitiesRepository
     */
    public function getRepository(): Repository;

    /**
     * @param Repository $repository
     */
    public function setRepository($repository): void;
}
