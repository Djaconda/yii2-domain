<?php

namespace PHPKitchen\Domain\DB\Mixins;

use PHPKitchen\DI\Mixins\ContainerAccess;
use PHPKitchen\Domain\DB\QueryConditionBuilder;

/**
 * Represents
 *
 * @property QueryConditionBuilder $conditionBuilder protected alias of the {@link _conditionBuilder}
 *
 * @mixin ContainerAccess
 *
 * @package PHPKitchen\Domain\DB\Mixins
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
trait QueryConditionBuilderAccess {
    protected string $conditionBuilderClassName = QueryConditionBuilder::class;
    private ?QueryConditionBuilder $_conditionBuilder = null;

    /**
     * Alias of {@link QueryConditionBuilder::buildAliasedNameOfField}
     *
     * @param string $field field name.
     * @param string|null $alias optional alias. If not used query alias will be used.
     */
    public function buildAliasedNameOfField(string $field, ?string $alias = null): string {
        return $this->conditionBuilder->buildAliasedNameOfField($field, $alias);
    }

    public function buildAliasedNameOfParam($param, $alias = null): string {
        return $this->conditionBuilder->buildAliasedNameOfParam($param, $alias);
    }

    protected function getConditionBuilder(): QueryConditionBuilder {
        if (null === $this->_conditionBuilder) {
            $this->_conditionBuilder = $this->container->create($this->conditionBuilderClassName, [$query = $this]);
        }

        return $this->_conditionBuilder;
    }
}
