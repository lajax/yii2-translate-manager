<?php

namespace lajax\translatemanager\bundles;

use yii\web\AssetBundle;

/**
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */
class LanguageItemPluginAsset extends AssetBundle {

    /**
     * @inheritdoc
     */
    public $sourcePath = '@lajax/translatemanager/assets';

    /**
     * @inheritdoc
     */
    public $publishOptions = [
        'forceCopy' => true
    ];

    /**
     * @param array $config
     */
    public function __construct($config = []) {
        $this->sourcePath = \Yii::$app->getModule('translatemanager')->getLanguageItemsDirPath();
        $this->js = [\Yii::$app->language . '.js'];
        parent::__construct($config);
    }

}
