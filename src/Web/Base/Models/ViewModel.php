<?php

namespace PHPKitchen\Domain\Web\Base\Models;

use PHPKitchen\DI\Contracts\ContainerAware;
use PHPKitchen\DI\Contracts\ServiceLocatorAware;
use PHPKitchen\DI\Mixins\ContainerAccess;
use PHPKitchen\DI\Mixins\ServiceLocatorAccess;
use PHPKitchen\Domain\Base\Entity;
use PHPKitchen\Domain\Contracts\DomainEntity;
use PHPKitchen\Domain\Contracts\EntityController;
use PHPKitchen\Domain\Contracts\Repository;
use PHPKitchen\Domain\Data\EntitiesProvider;
use PHPKitchen\Domain\DB\EntitiesRepository;
use PHPKitchen\Domain\Web\Contracts\RepositoryAware;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

/**
 * Represents base view model.
 *
 * @property mixed $id
 * @property EntityController|Controller $controller
 * @property EntitiesRepository $repository
 * @property EntitiesProvider $dataProvider
 * @property Entity $entity
 *
 * @package PHPKitchen\Domain\Web\Base
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class ViewModel extends Model implements ContainerAware, ServiceLocatorAware {
    use ContainerAccess;
    use ServiceLocatorAccess;

    private ?DomainEntity $_entity = null;
    /**
     * @var null|array Defines map of entity attributes required in {@link convertAttributesToEntityAttributes()}
     * Format of map:
     * <pre>
     * [
     *      'entityAttribute' => 'formAttributeName',
     *      'entityAttribute' => any value (for example digits, objects, array) except string equals to name of the form attributes,
     *      'entityAttribute' => callable // callable will be executed and result will be set as entity attribute
     * ]
     * </pre>
     */
    private ?array $_entityAttributesMap = null;
    private ?Controller $_controller = null;

    public function convertToEntity(): Entity {
        $defaultAttributes = $this->prepareDefaultEntityAttributes();
        $newAttributes = $this->convertToEntityAttributes();
        $entity = $this->getEntity();
        $entity?->load(ArrayHelper::merge($defaultAttributes, $newAttributes));

        return $entity;
    }

    /**
     * Override to set default entity attributes.
     *
     * @return array default entity attributes
     */
    protected function prepareDefaultEntityAttributes(): array {
        return [];
    }

    /**
     * Converts form to entity attributes.
     *
     * @return array entity attributes.
     */
    public function convertToEntityAttributes(): array {
        $entityAttributesMap = $this->getEntityAttributesMap();
        if ($entityAttributesMap === []) {
            return $this->getAttributes();
        }
        $attributes = [];
        foreach ($entityAttributesMap as $entityAttribute => $formValue) {
            if (is_string($formValue) && $this->canGetProperty($formValue)) {
                $attributeValue = $this->$formValue;
            } elseif (is_callable($formValue)) {
                $attributeValue = $formValue();
            } else {
                $attributeValue = $formValue;
            }
            $attributes[$entityAttribute] = $attributeValue;
        }

        return $attributes;
    }

    /**
     * Populates the form by entity data.
     */
    public function loadAttributesFromEntity(): bool {
        $attributes = $this->convertEntityToSelfAttributes();

        return $this->load($attributes, '');
    }

    /**
     * Converts AR attributes to form attributes.
     */
    protected function convertEntityToSelfAttributes(): array {
        $entity = $this->getEntity();
        $attributes = [];
        foreach ($this->getEntityAttributesMap() as $modelAttribute => $formValue) {
            if (is_string($formValue) && $this->canGetProperty($formValue) && $entity?->canGetProperty($modelAttribute)) {
                $attributes[$formValue] = $entity?->$modelAttribute;
            }
        }

        return $attributes;
    }

    /**
     * Returns the name of entity attribute mapped to specified form field
     *
     *
     */
    public function getEntityAttributeMappedToFieldName(string $formAttributeName): string {
        return array_flip($this->getEntityAttributesMap())[$formAttributeName];
    }

    protected function getEntityAttributesMap(): array {
        if (null === $this->_entityAttributesMap) {
            $selfAttributeNames = $this->attributes();
            $this->_entityAttributesMap = array_combine($selfAttributeNames, $selfAttributeNames);
        }

        return $this->_entityAttributesMap;
    }

    public function setEntityAttributesMap(array $entityAttributesMap): void {
        $this->_entityAttributesMap = $entityAttributesMap;
    }

    //region -------------------- GETTERS/SETTERS --------------------
    public function getEntity(): ?DomainEntity {
        return $this->_entity;
    }

    public function setEntity(DomainEntity $entity): void {
        $this->_entity = $entity;
    }

    public function getId(): ?int {
        return $this->getEntity()->id;
    }

    public function getController(): ?Controller {
        return $this->_controller;
    }

    public function setController(Controller $controller): void {
        $this->_controller = $controller;
    }

    public function getRepository(): Repository {
        if ($this->controller->action instanceof RepositoryAware) {
            return $this->controller->action->repository;
        }

        return $this->controller->repository;
    }
    //endregion
}
