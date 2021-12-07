<?php

namespace PHPKitchen\Domain\Web\Actions;

use PHPKitchen\Domain\Web\Base\Actions\EntityModificationAction;

/**
 * Represents entity creation process.
 *
 * @package PHPKitchen\Domain\Web\Actions
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class AddEntity extends EntityModificationAction {
    public function init(): void {
        $this->setViewFileIfNotSetTo('add');
    }

    public function run() {
        return $this->loadModelAndSaveOrPrintView();
    }

    protected function initModel(): void {
        $this->_model = $this->createNewModel();
    }
}
