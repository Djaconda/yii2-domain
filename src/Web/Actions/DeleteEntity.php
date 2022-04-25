<?php

namespace PHPKitchen\Domain\Web\Actions;

use PHPKitchen\Domain\Exceptions\UnableToSaveEntityException;
use PHPKitchen\Domain\Web\Base\Actions\Action;
use PHPKitchen\Domain\Web\Mixins\ModelSearching;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\Web
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class DeleteEntity extends Action {
    use ModelSearching;

    public string $failToDeleteErrorFlashMessage = 'Unable to delete entity';
    public string $successfulDeleteFlashMessage = 'Entity successfully deleted';
    /** @var string|array|callable a url to redirect to a next page. */
    public $redirectUrl;

    public function init(): void {
        $this->setViewFileIfNotSetTo('list');
    }

    /**
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function run($id) {
        $entity = $this->findEntityByIdentifierOrFail($id);
        $this->tryToDeleteEntity($entity);

        return $this->redirectToNextPage();
    }

    protected function tryToDeleteEntity($entity): void {
        try {
            $savedSuccessfully = $this->getRepository()->delete($entity);
        } catch (UnableToSaveEntityException) {
            $savedSuccessfully = false;
        }
        if ($savedSuccessfully) {
            $this->addSuccessFlash($this->successfulDeleteFlashMessage);
        } else {
            $this->addErrorFlash($this->failToDeleteErrorFlashMessage);
        }
    }

    protected function redirectToNextPage(): Response {
        if (null === $this->redirectUrl) {
            $redirectUrl = ['list'];
        } elseif (is_callable($this->redirectUrl)) {
            $redirectUrl = call_user_func($this->redirectUrl, $this);
        } else {
            $redirectUrl = $this->redirectUrl;
        }

        return $this->controller->redirect($redirectUrl);
    }
}
