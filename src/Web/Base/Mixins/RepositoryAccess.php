<?php

namespace PHPKitchen\Domain\Web\Base\Mixins;

use PHPKitchen\DI\Container;
use PHPKitchen\Domain\Contracts\Repository;
use PHPKitchen\Domain\DB\EntitiesRepository;
use yii\base\InvalidArgumentException;
use yii\web\Controller;

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
 * @package PHPKitchen\Domain\Web\Base\Mixins
 */
trait RepositoryAccess {
    /**
     * @var null|EntitiesRepository DB repository.
     */
    private ?EntitiesRepository $_repository = null;

    public function getRepository(): Repository {
        if (null === $this->_repository) {
            // fallback to support old approach with defining repositories in controllers
            $this->_repository = $this->controller->repository ?? null;
        }

        return $this->_repository;
    }

    public function setRepository($repository): void {
        if ($this->isObjectValidRepository($repository)) {
            $this->_repository = $repository;
        } else {
            $this->createAndSetRepositoryFromDefinition($repository);
        }
    }

    protected function createAndSetRepositoryFromDefinition($definition): void {
        $repository = $this->container->create($definition);
        if (!$this->isObjectValidRepository($repository)) {
            throw new InvalidArgumentException('Repository should be an instance of ' . EntitiesRepository::class);
        }
        $this->_repository = $repository;
    }

    protected function isObjectValidRepository($object): bool {
        return $object instanceof EntitiesRepository;
    }
}
