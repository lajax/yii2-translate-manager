<?php

namespace lajax\translatemanager\bundles;

use yii\web\AssetBundle;

/**
 * FrontendTranslation asset bundle
 *
 * @author Lajos Molnár <lajax.m@gmail.com>
 *
 * @since 1.2
 */
class FrontendTranslationAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@lajax/translatemanager/assets';

    /**
     * @inheritdoc
     */
    public $css = [
        'stylesheets/helpers.css',
        'stylesheets/frontend-translation.css',
    ];
}
