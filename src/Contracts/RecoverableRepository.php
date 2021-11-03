<?php

namespace PHPKitchen\Domain\Contracts;

/**
 * Defines interfaces for recovered entitys functionality of DB repository.
 *
 * @package PHPKitchen\Domain\Contracts
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
interface RecoverableRepository {
    public function recover(DomainEntity $entity): bool;
}
