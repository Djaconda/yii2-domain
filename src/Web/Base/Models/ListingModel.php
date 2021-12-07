<?php

namespace PHPKitchen\Domain\Web\Base\Models;

use PHPKitchen\Domain\Contracts\Specification;
use PHPKitchen\Domain\Data\EntitiesProvider;
use yii\data\BaseDataProvider;

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
     */
    public function getDataProvider(): EntitiesProvider|BaseDataProvider {
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
