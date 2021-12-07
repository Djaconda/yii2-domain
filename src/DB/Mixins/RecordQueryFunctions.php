<?php

namespace PHPKitchen\Domain\DB\Mixins;

use PHPKitchen\Domain\DB\Record;
use ReflectionMethod;

/**
 * Combines functions required for {@link \PHPKitchen\Domain\DB\RecordQuery}.
 * The only goal of this mixin is to allow building custom query classes without extending {@link PHPKitchen\Domain\DB\RecordQuery}
 *
 * @property string $alias public alias of the {@link _alias}
 * @property string $mainTableName public alias of the {@link _mainTableName}
 *
 * @mixin QueryConditionBuilderAccess
 *
 * @package PHPKitchen\Domain\DB\Mixins
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
trait RecordQueryFunctions {
    public string $primaryKeyName = 'id';
    private ?string $_alias = null;
    private ?string $_mainTableName = null;
    //region ------------------- SEARCH METHODS  --------------------

    /**
     * Method designed to make chain of query methods more accurate if query used as a stored object and not as a part
     * of active record.
     * Example:
     * <pre>
     * $finder = new ActiveQuery();
     *     $resultSet = $finder->find()
     *       ->active()
     *       ->withSomeRelation()
     *       ->all();
     *     $record = $finder->find()->one();
     * </pre>
     *
     * @return $this
     */
    public function find() {
        $clone = clone $this;
        foreach ($this->getBehaviors() as $name => $behavior) {
            $clone->attachBehavior($name, clone $behavior);
        }

        return $clone;
    }

    /**
     * @param $pk
     *
     * @return Record|array|null
     */
    public function oneWithPk($pk) {
        $pkParam = $this->buildAliasedNameOfParam('pk');
        $primaryKey = $this->buildAliasedNameOfField($this->primaryKeyName);
        $this->andWhere("$primaryKey = $pkParam", [$pkParam => $pk]);

        return $this->one();
    }

    /**
     * @override
     * @inheritdoc
     */
    public function alias($alias) {
        $this->_alias = $alias;

        return parent::alias($alias);
    }

    public function bySearchModel($model) {
        return $this;
    }

    //endregion

    //region ------------------- GETTERS/SETTERS  -------------------

    public function getMainTableName(): string {
        if ($this->_mainTableName === null) {
            $method = new ReflectionMethod($this->modelClass, 'tableName');
            $this->_mainTableName = $method->invoke(null);
        }

        return $this->_mainTableName;
    }

    public function setMainTableName(string $mainTableName): void {
        $this->_mainTableName = $mainTableName;
    }

    public function getAlias(): ?string {
        if ($this->_alias === null) {
            $this->_alias = $this->getMainTableName();
        }

        return $this->_alias;
    }
    //endregion
}
