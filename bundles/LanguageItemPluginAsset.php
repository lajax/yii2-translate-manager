<?php

namespace lajax\translatemanager\bundles;

use yii\web\AssetBundle;

/**
 * LanguageItem Plugin asset bundle
 *
 * @author Lajos Molnár <lajax.m@gmail.com>
 *
 * @since 1.0
 */
class LanguageItemPluginAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@lajax/translatemanager/assets';

    /**
     * @inheritdoc
     */
    public $publishOptions = [
        'forceCopy' => true,
    ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = \Yii::$app->getModule('translatemanager')->getLanguageItemsDirPath();
        if (file_exists(\Yii::getAlias($this->sourcePath . \Yii::$app->language . '.js'))) {
            $this->js = [
                \Yii::$app->language . '.js',
            ];
        } else {
            $this->sourcePath = null;
        }

        parent::init();
    }
}
