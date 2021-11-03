<?php

namespace PHPKitchen\Domain\Web\Base\Models;

use PHPKitchen\Domain\Data\EntitiesProvider;

/**
 * Represents a view model designed to be used in recovered listing actions.
 *
 * @package PHPKitchen\Domain\Web\Base
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
class RecoverableEntitiesListModel extends ListingModel {
    /**
     * Override this method
     *
     * @return EntitiesProvider
     */
    public function getDeletedDataProvider(): EntitiesProvider {
        $provider = $this->getDataProvider();
        $provider->query->deleted();

        return $provider;
    }
}
