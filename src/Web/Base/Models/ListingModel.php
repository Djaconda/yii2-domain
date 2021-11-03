<?php

namespace PHPKitchen\Domain\Web\Base\Models;

use PHPKitchen\Domain\Contracts\Specification;
use PHPKitchen\Domain\Data\EntitiesProvider;

/**
 * Represents a view model designed to be used in listing actions.
 *
 * @package PHPKitchen\Domain\Web\Base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class ListingModel extends ViewModel {
    public bool $fetchDataAsArray = true;

    /**
     * Override this method
     *
     * @return EntitiesProvider
     */
    public function getDataProvider(): EntitiesProvider {
        $provider = $this->repository->getEntitiesProvider();
        if ($this->fetchDataAsArray) {
            $provider->query->asArray();
        }
        if ($provider->query instanceof Specification) {
            $provider->query->bySearchModel($this);
        }

        return $provider;
    }
}
