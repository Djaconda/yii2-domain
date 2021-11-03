<?php

namespace PHPKitchen\Domain\Web\Mixins;

use yii\helpers\ArrayHelper;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\Web\Mixins
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
trait ControllerActionsManagement {
    private array $_actions = [];

    public function actions(): array {
        return $this->_actions;
    }

    protected function addAction(string $name, $definition): void {
        $this->_actions[$name] = $definition;
    }

    protected function updateActionDefinition(string $name, $definition): void {
        if (is_string($definition) || is_object($definition)) {
            $this->_actions[$name] = $definition;
        } elseif (is_array($definition)) {
            if ($this->isDynamicActionDefined($name) && is_array($this->_actions[$name])) {
                $this->_actions[$name] = ArrayHelper::merge($this->_actions[$name], $definition);
            } else {
                $this->_actions[$name] = $definition;
            }
        }
    }

    protected function removeAction(string $name): void {
        unset($this->_actions[$name]);
    }

    protected function isDynamicActionDefined(string $name): bool {
        return isset($this->_actions[$name]);
    }

    protected function setActions(array $actions): void {
        $this->_actions = $actions;
    }
}
