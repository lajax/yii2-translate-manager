<?php

namespace lajax\translatemanager\controllers\actions;

use Yii;
use yii\base\Action;
use lajax\translatemanager\models\LanguageSource;
use lajax\translatemanager\models\LanguageTranslate;

/**
 * Class for returning messages in the given language
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */
class MessageAction extends Action {

    /**
     * Returning messages in the given language
     * @return string
     */
    public function run() {
        $languageTranslate = LanguageTranslate::findOne([
            'id' => Yii::$app->getRequest()->post('id', 0),
            'language' => Yii::$app->request->post('language_id', ''),
        ]);
        
        try {
            $translation = $languageTranslate->translation;
        } catch (Exception $ex) {
            $languageSource = LanguageSource::findOne(['id' => Yii::$app->getRequest()->post('id', 0)]);
            $translation = $languageSource->message;
        }
        
        return $translation;
    }

}
        