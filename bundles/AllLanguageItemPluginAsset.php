<?php

namespace lajax\translatemanager\bundles;

/**
 * AllLanguageItem Plugin asset bundle
 *
 * @author Semenihin Maksim <semenihin.maksim@gmail.com>
 *
 * @since 1.0
 *
 * will include all active languages
 */
class AllLanguageItemPluginAsset extends LanguageItemPluginAsset
{
    public function init()
    {
        parent::init();
        $this->js = [];
        $this->sourcePath = \Yii::$app->getModule('translatemanager')->getLanguageItemsDirPath();

        $langs = \lajax\translatemanager\models\Language::findAll(['status' => \lajax\translatemanager\models\Language::STATUS_ACTIVE]);

        foreach ($langs as $key => $lang) {
            if (file_exists(\Yii::getAlias($this->sourcePath . $lang->language_id . '.js'))) {
                $this->js[] = $lang->language_id . '.js';
            }
        }
    }
}
