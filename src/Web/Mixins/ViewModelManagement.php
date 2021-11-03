<?php

namespace PHPKitchen\Domain\Web\Mixins;

use PHPKitchen\DI\Mixins\ContainerAccess;
use PHPKitchen\DI\Mixins\ServiceLocatorAccess;
use PHPKitchen\Domain\Base\Entity;
use PHPKitchen\Domain\Web\Base\ViewModel;
use yii\web\Controller;

/**
 * Represents
 *
 * @property Controller $controller
 * @property string $id
 * @property string $viewModelClassName
 *
 * @mixin ServiceLocatorAccess
 * @mixin ContainerAccess
 *
 * @package PHPKitchen\Domain\Web\Mixins
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
trait ViewModelManagement {
    private $_viewModelClassName;
    /**
     * @var string the scenario to be assigned to the new model before it is validated and saved.
     */
    public string $scenario = ViewModel::SCENARIO_DEFAULT;

    protected function createNewModel() {
        $entity = $this->getRepository()->createNewEntity();

        return $this->createViewModel($entity);
    }

    /**
     * @param Entity $entity
     *
     * @return ViewModel
     */
    protected function createViewModel($entity) {
        $model = $this->container->create([
            'class' => $this->getViewModelClassName(),
            'entity' => $entity,
            'controller' => $this->controller,
        ]);
        $model->scenario = $this->scenario;

        return $model;
    }

    public function getViewModelClassName() {
        return $this->_viewModelClassName;
    }

    public function setViewModelClassName($viewModelClassName) {
        $this->_viewModelClassName = $viewModelClassName;
    }
}
