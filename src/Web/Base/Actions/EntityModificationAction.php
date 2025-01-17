<?php

namespace PHPKitchen\Domain\Web\Base\Actions;

use PHPKitchen\Domain\Exceptions\UnableToSaveEntityException;
use PHPKitchen\Domain\Web\Base\Mixins\EntityActionHooks;
use PHPKitchen\Domain\Web\Mixins\ModelSearching;
use PHPKitchen\Domain\Web\Mixins\ViewModelManagement;
use yii\base\Model;

/**
 * Represents a base class for all actions that modify entity.
 *
 * @package PHPKitchen\Domain\Web\Base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
abstract class EntityModificationAction extends Action {
    use ViewModelManagement;
    use ModelSearching;
    use EntityActionHooks;

    /**
     * @var bool indicates whether to throw exception or handle it
     */
    public bool $throwExceptions = false;
    protected ?Model $_model = null;

    public function __construct($id, $controller, $config = []) {
        $this->defaultRedirectUrlAction = 'edit';

        parent::__construct($id, $controller, $config);
    }

    abstract protected function initModel();

    protected function loadModelAndSaveOrPrintView() {
        return $this->modelLoaded()
            ? $this->saveModel()
            : $this->printView();
    }

    protected function modelLoaded(): bool {
        return $this->getModel()->load($this->getRequest()->post());
    }

    protected function saveModel() {
        return $this->validateModelAndTryToSaveEntity()
            ? $this->handleSuccessfulOperation()
            : $this->handleFailedOperation();
    }

    protected function validateModelAndTryToSaveEntity(): bool {
        return $this->getModel()->validate() && $this->tryToSaveEntity();
    }

    protected function tryToSaveEntity(): bool {
        $model = $this->getModel();
        $entity = $model->convertToEntity();
        try {
            $savedSuccessfully = $this->getRepository()->validateAndSave($entity);
            $this->getRepository()->refresh($entity);
            $model->loadAttributesFromEntity();
        } catch (UnableToSaveEntityException $e) {
            $savedSuccessfully = false;
            throw $e;
        }
        if ($savedSuccessfully) {
            // @TODO seems like duplicates handleSuccessfulOperation - need to investigate
            $this->addSuccessFlash($this->successFlashMessage);
        } else {
            $this->addErrorFlash($this->failToSaveErrorFlashMessage);
        }

        return $savedSuccessfully;
    }

    /**
     * Defines default redirect URL.
     *
     * If you need to change redirect action, set {@link defaultRedirectUrlAction} at action init.
     *
     * Override this method if you need to define custom format of URL.
     *
     * @return array url definition;
     */
    protected function prepareDefaultRedirectUrl(): array {
        $entity = $this->getModel()->convertToEntity();

        return [$this->defaultRedirectUrlAction, 'id' => $entity->id];
    }

    /**
     * @override base implementation for BC compatibility.
     * @TODO remove it in the next major release
     */
    protected function callRedirectUrlCallback(): array {
        $entity = $this->getModel()->convertToEntity();

        return call_user_func($this->redirectUrl, $entity, $this);
    }

    public function getModel(): Model {
        if (!$this->_model instanceof Model) {
            $this->initModel();
        }

        return $this->_model;
    }

    /**
     * @override
     */
    protected function prepareViewContext(): array {
        $context = parent::prepareViewContext();
        $context['model'] = $this->getModel();

        return $context;
    }
}
