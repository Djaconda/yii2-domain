<?php

namespace PHPKitchen\Domain\Web\Actions;

use PHPKitchen\Domain\Contracts\DomainEntity;
use PHPKitchen\Domain\Contracts\RecoverableRepository;
use PHPKitchen\Domain\Exceptions\UnableToSaveEntityException;
use PHPKitchen\Domain\Web\Base\Action;
use PHPKitchen\Domain\Web\Mixins\ModelSearching;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Represents entity recovering process.
 *
 * @package PHPKitchen\Domain\Web\Actions
 * @author Dmitry Bukavin <4o.djaconda@gmail.com>
 */
class RecoverEntity extends Action {
    use ModelSearching;

    /** @var string|array|callable a url to redirect to a next page. */
    public $redirectUrl;
    public string $failedToRecoverFlashMessage = 'Unable to recover entity';
    public string $successfullyRecoveredFlashMessage = 'Entity successfully recovered';
    public string $recoveredListFieldName = 'restored-ids';

    public function init(): void {
        $this->setViewFileIfNotSetTo('list');
    }

    /**
     * @param int|null $id
     *
     * @return Response
     * @throws NotFoundHttpException
     * @throws InvalidConfigException
     */
    public function run(?int $id = null): Response {
        $ids = ($id) ? [$id] : $this->serviceLocator->request->post($this->recoveredListFieldName, []);

        $savedResults = [];
        foreach ($ids as $_id) {
            $entity = $this->findEntityByIdentifierOrFail($_id);
            $savedResults[] = $this->tryToRecoverEntity($entity);
        }

        $savedNotSuccessfully = array_filter($savedResults, fn($value) => !$value);
        if ($savedNotSuccessfully !== []) {
            $this->addErrorFlash($this->failedToRecoverFlashMessage);
        } else {
            $this->addSuccessFlash($this->successfullyRecoveredFlashMessage);
        }

        return $this->redirectToNextPage();
    }

    protected function tryToRecoverEntity(DomainEntity $entity): bool {
        $repository = $this->repository;
        try {
            if ($repository instanceof RecoverableRepository) {
                $savedSuccessfully = $repository->recover($entity);
            } else {
                $savedSuccessfully = false;
            }
        } catch (UnableToSaveEntityException $e) {
            $savedSuccessfully = false;
        }

        return $savedSuccessfully;
    }

    // @todo fix duplicate with EntityModificationAction
    protected function redirectToNextPage(): Response {
        if (null === $this->redirectUrl) {
            $redirectUrl = ['list'];
        } elseif (is_callable($this->redirectUrl)) {
            $redirectUrl = call_user_func($this->redirectUrl, $this);
        } else {
            $redirectUrl = $this->redirectUrl;
        }

        return $this->controller->redirect($redirectUrl, $statusCode = 200);
    }
}
