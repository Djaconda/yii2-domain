<?php

namespace PHPKitchen\Domain\DB;

use PHPKitchen\Domain\Contracts\Specification;
use PHPKitchen\DI\Mixins\ContainerAccess;
use PHPKitchen\DI\Mixins\ServiceLocatorAccess;
use PHPKitchen\Domain\Contracts;
use PHPKitchen\Domain\DB\Mixins\QueryConditionBuilderAccess;
use PHPKitchen\Domain\DB\Mixins\RecordQueryFunctions;
use yii\db\ActiveQuery;

/**
 * Represents
 *
 * @package PHPKitchen\Domain\DB
 * @author Dmitry Kolodko <prowwid@gmail.com>
 */
class RecordQuery extends ActiveQuery implements Specification, Contracts\RecordQuery {
    use QueryConditionBuilderAccess;
    use RecordQueryFunctions;
    use ContainerAccess;
    use ServiceLocatorAccess;
}