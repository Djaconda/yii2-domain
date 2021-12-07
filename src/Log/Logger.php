<?php

namespace PHPKitchen\Domain\Log;

use yii\log\Logger as BaseLogger;

/**
 * Extends base logger to provide ability to log messages with trace for any specific use case event if {@link traceLevel}
 * is disabled.
 * Such function useful for exception logging as on production trace level is disabled but for exceptions it's very important to include
 * trace level to message.
 *
 * @package PHPKitchen\Domain\log
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class Logger extends BaseLogger {
    public int $defaultTraceLevel = 7;

    public function logWithTrace(string $message, int $level, ?string $category = 'application'): void {
        if (!$this->traceLevel && $this->defaultTraceLevel) {
            $oldTraceLevel = $this->traceLevel;
            $this->traceLevel = $this->defaultTraceLevel;
            $this->log($message, $level, $category);
            $this->traceLevel = $oldTraceLevel;
        } else {
            $this->log($message, $level, $category);
        }
    }
}
