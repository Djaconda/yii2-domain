<?php

namespace PHPKitchen\Domain\Web\Mixins;

use PHPKitchen\DI\Mixins\ContainerAccess;
use PHPKitchen\DI\Mixins\ServiceLocatorAccess;
use PHPKitchen\Domain\Contracts\Repository;
use PHPKitchen\Domain\DB\EntitiesRepository;
use yii\base\InvalidConfigException;
use yii\web\NotFoundHttpException;

/**
 * Represents
 *
 * @property EntitiesRepository $repository
 *
 * @mixin ServiceLocatorAccess
 * @mixin ContainerAccess
 *
 * @package PHPKitchen\Domain\Web\Mixins
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
trait EntityManagement {
    public string $notFoundModelExceptionMessage = 'Requested page does not exist!';
    public string $notFoundModelExceptionClassName = NotFoundHttpException::class;
    /**
     * @var EntitiesRepository
     */
    private $_repository;

    public function findEntityByPk($pk) {
        $entity = $this->getRepository()->find()->oneWithPk($pk);
        if (null === $entity) {
            /**
             * @var NotFoundHttpException $exception
             */
            $exception = $this->getContainer()
                              ->create($this->notFoundModelExceptionClassName, [$this->notFoundModelExceptionMessage]);
            throw $exception;
        }

        return $entity;
    }

    public function getRepository(): Repository {
        if ($this->_repository === null) {
            throw new InvalidConfigException('Repository should be set in ' . static::class);
        }

        return $this->_repository;
    }

    public function setRepository($repository): void {
        if (is_string($repository) || is_array($repository)) {
            $this->_repository = $this->container->create($repository);
        } elseif (is_object($repository) && $repository instanceof Repository) {
            $this->_repository = $repository;
        } else {
            throw new InvalidConfigException('Repository should be a valid container config or an instance of ' . Repository::class);
        }
    }
}
