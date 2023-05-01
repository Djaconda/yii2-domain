<?php

namespace PHPKitchen\Domain\DB;

use PHPKitchen\Domain\Contracts\RecordQuery;
use PHPKitchen\Domain\Base\MagicObject;
use PHPKitchen\Domain\Contracts;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\DB
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class QueryConditionBuilder extends MagicObject {
    private array $_paramNamesCounters = [];

    public function __construct(protected RecordQuery $query, $config = []) {
        parent::__construct($config);
    }

    public function buildAliasedNameOfField($field, $alias = null): string {
        $alias = $alias ?: $this->query->alias;

        return "[[$alias]].[[$field]]";
    }

    public function buildAliasedNameOfParam($param, $alias = null): string {
        $alias = $alias ?: $this->query->alias;
        $paramName = ":{$alias}_{$param}";
        if ($this->isParamNameUsed($paramName)) {
            $index = $this->getParamNameNextIndexAndIncreaseCurrent($paramName);
            $paramName = "{$paramName}_{$index}";
        } else {
            $this->addParamNameToUsed($paramName);
        }

        return $paramName;
    }

    protected function isParamNameUsed(string $paramName): bool {
        return isset($this->_paramNamesCounters[$paramName]);
    }

    protected function addParamNameToUsed(string $paramName): void {
        $this->_paramNamesCounters[$paramName] = 0;
    }

    protected function getParamNameNextIndexAndIncreaseCurrent(string $paramName) {
        $this->_paramNamesCounters[$paramName]++;

        return $this->_paramNamesCounters[$paramName];
    }
}
