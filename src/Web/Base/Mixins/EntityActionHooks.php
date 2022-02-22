<?php

namespace PHPKitchen\Domain\Web\Base\Mixins;

use PHPKitchen\DI\Container;
use PHPKitchen\Domain\DB\EntitiesRepository;
use yii\web\Controller;
use yii\web\Response;

/**
 * Mixin that provides properties and methods to work with DB repository.
 *
 * Own properties:
 *
 * @property EntitiesRepository $repository
 *
 * Globally available properties:
 * @property Container $container
 *
 * Parent properties:
 * @property Controller $controller
 *
 * @uses SessionMessagesManagement
 * @uses ResponseManagement
 *
 * @package PHPKitchen\Domain\Web\Base\Mixins
 */
trait EntityActionHooks {
    public string $failToSaveErrorFlashMessage = 'Unable to save entity';
    public string|false $validationFailedFlashMessage = 'Please correct errors.';
    public string $successFlashMessage = 'Changes successfully saved.';

    abstract protected function printView(): array|string|Response;

    protected function handleSuccessfulOperation(): array|string|Response {
        $this->addSuccessFlash($this->successFlashMessage);
        if ($this->redirectUrl !== false) {
            return $this->redirectToNextPage();
        }

        return $this->printView();
    }

    protected function handleFailedOperation(): array|string|Response {
        if ($this->validationFailedFlashMessage !== false) {
            $this->addErrorFlash($this->validationFailedFlashMessage);
        }

        return $this->printView();
    }
}
