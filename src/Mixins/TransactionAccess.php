<?php

namespace PHPKitchen\Domain\Mixins;

use Exception;
use PHPKitchen\DI\Mixins\ServiceLocatorAccess;
use yii\base\InvalidCallException;
use yii\db\Transaction;

/**
 * Injects methods to manipulate db transactions.
 * Trait supposed to be used only in protected an private contexts!
 *
 * @mixin ServiceLocatorAccess
 *
 * @package PHPKitchen\Domain\Mixins
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
trait TransactionAccess {
    protected ?Transaction $_transaction = null;

    protected function beginTransaction(): void {
        if (null === $this->_transaction) {
            $this->_transaction = $this->serviceLocator->db->beginTransaction();
        } else {
            throw new InvalidCallException('Transaction already started, unable to start another transaction in class ' . static::class);
        }
    }

    protected function commitTransaction(): void {
        if (null === $this->_transaction) {
            throw new InvalidCallException('Transaction should be started before committing in class ' . static::class);
        }
        $this->_transaction->commit();
        $this->clearTransaction();
    }

    protected function rollbackTransaction(): void {
        if (null === $this->_transaction) {
            throw new InvalidCallException('Transaction should be started before rolling back in class ' . static::class);
        }
        $this->_transaction->rollBack();
        $this->clearTransaction();
    }

    protected function clearTransaction(): void {
        $this->_transaction = null;
    }

    /**
     * Allows wrapping method of a class by transaction.
     *
     * @param string $methodName class method name that should be wrapped by transaction.
     * @param array ...$methodArguments [optional] method arguments.
     *
     * @return bool|mixed returns method result or false if transaction failed.
     */
    protected function callTransactionalMethod(string $methodName, ...$methodArguments) {
        $this->beginTransaction();
        try {
            $result = call_user_func_array([$this, $methodName], $methodArguments);
            $this->commitTransaction();
        } catch (Exception $e) {
            $result = false;
            $this->rollbackTransaction();
        }

        return $result;
    }

    /**
     * Wraps passed callback in transaction.
     *
     * @param callable $callback valid callback to be wrapped by transaction.
     *
     * @return bool|mixed returns callback result or false if transaction failed.
     */
    protected function callInTransaction(callable $callback) {
        $this->beginTransaction();
        try {
            $result = $callback();
            $this->commitTransaction();
        } catch (Exception $e) {
            $result = false;
            $this->rollbackTransaction();
        }

        return $result;
    }
}
