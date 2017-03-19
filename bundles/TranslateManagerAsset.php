<?php

namespace lajax\translatemanager\bundles;

use yii\web\AssetBundle;

/**
 * TranslateManager asset bundle
 *
 * @author Lajos Molnar <lajax.m@gmail.com>
 *
 * @since 1.0
 */
class TranslateManagerAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@lajax/translatemanager/assets';

    /**
     * @inheritdoc
     */
    public $css = [
        'stylesheets/translate-manager.css',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
