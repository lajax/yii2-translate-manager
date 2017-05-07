<?php

namespace lajax\translatemanager\migrations\namespaced;

require_once dirname(__DIR__) . '/m141002_030233_translate_manager.php';

/**
 * Migration to support namespaced migrations.
 *
 * This migration can be used instead of the one with global namespace.
 *
 * @see https://github.com/yiisoft/yii2/blob/2.0.10/framework/console/controllers/BaseMigrateController.php#L60
 *
 * @author moltam
 */
class m141002_030233_translate_manager_ns extends \m141002_030233_translate_manager
{
}
