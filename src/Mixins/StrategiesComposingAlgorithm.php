<?php

namespace PHPKitchen\Domain\Mixins;

use PHPKitchen\DI\Mixins\ContainerAccess;
use PHPKitchen\Domain\Base\Strategy;

/**
 * Represents
 *
 * @mixin ContainerAccess
 *
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
trait StrategiesComposingAlgorithm {
    /**
     * @var Strategy[]|array
     */
    private array $_chainedStrategies;

    public function executeCallAction(): void {
        $chainedStrategies = $this->getChainedStrategies();
        $container = $this->container;
        foreach ($chainedStrategies as $key => $chainedStrategy) {
            if (!is_object($chainedStrategy)) {
                $chainedStrategy = $container->create($chainedStrategy, $this->getStrategyConstructorArguments());
            }
            $chainedStrategy->call();
        }
    }

    protected function getStrategyConstructorArguments(): array {
        return [];
    }

    public function getChainedStrategies(): array {
        return $this->_chainedStrategies;
    }

    public function setChainedStrategies(array $chainedStrategies): void {
        $this->_chainedStrategies = $chainedStrategies;
    }
}
