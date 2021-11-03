<?php

namespace PHPKitchen\Domain\Base;

use PHPKitchen\Domain\Contracts;
use yii\base\Event;

/**
 * Represents
 *
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
abstract class Strategy extends Component implements Contracts\Strategy {
    abstract protected function executeCallAction();

    /**
     * @param array ...$params algorithm params.
     *
     * @return mixed strategy result.
     */
    public function __invoke(...$params) {
        return $this->call();
    }

    public function call() {
        $this->executeBeforeCall();

        $result = $this->executeCallAction();

        $this->executeAfterCall();

        return $result;
    }

    protected function executeBeforeCall(): void {
        $this->trigger(self::EVENT_BEFORE_CALL, new Event());
    }

    protected function executeAfterCall(): void {
        $this->trigger(self::EVENT_AFTER_CALL, new Event());
    }
}
