<?php

namespace PHPKitchen\Domain\DB\Mixins;

use PHPKitchen\Domain\Contracts\DomainEntity;

/**
 * Represents mixin designed for recovering entities that was previously deleted.
 *
 * @package PHPKitchen\Domain\DB\Mixins
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
trait EntityRecovering {
    /**
     * @return bool result.
     */
    public function recover(DomainEntity $entity): bool {
        $result = false;
        if ($this->triggerModelEvent(self::EVENT_BEFORE_DELETE, $entity)) {
            $dataSource = $entity->getDataMapper()->getDataSource();
            if ($dataSource->hasMethod('restore')) {
                $result = $dataSource->restore();
            }
        }
        if ($result) {
            $this->triggerModelEvent(self::EVENT_AFTER_DELETE, $entity);
        }

        return $result;
    }

    abstract public function triggerModelEvent(string $event, DomainEntity $entity);
}
