<?php

namespace PHPKitchen\Domain\Web\Actions;

use PHPKitchen\Domain\Web\Base\Actions\EntityModificationAction;
use yii\web\Response;

/**
 * Represents entity modify process.
 *
 * @package PHPKitchen\Domain\Web\Actions
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class EditEntity extends EntityModificationAction {
    protected string|int $entityId;

    public function init(): void {
        $this->setViewFileIfNotSetTo('edit');
    }

    /**
     * @return Response
     */
    public function run($id) {
        $this->entityId = $id;

        return $this->loadModelAndSaveOrPrintView();
    }

    protected function initModel(): void {
        $entity = $this->findEntityByIdentifierOrFail($this->entityId);
        $this->_model = $this->createViewModel($entity);
        $this->_model->loadAttributesFromEntity();
    }
}
