<?php

namespace lajax\translatemanager\bundles;

use yii\web\AssetBundle;

/**
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */
class TranslatePluginAsset extends AssetBundle {

    /**
     * @inheritdoc
     */
    public $sourcePath = '@lajax/translatemanager/assets';

    /**
     * @inheritdoc
     */
    public $js = [
        'javascripts/helpers.js',
        'javascripts/translate.js',
    ];

    /**
     * @inheritdoc
     */
    public $depends = [
        'yii\web\JqueryAsset',
    ];

}
