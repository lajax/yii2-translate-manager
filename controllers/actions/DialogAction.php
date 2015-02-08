<?php

namespace lajax\translatemanager\controllers\actions;

use Yii;
use lajax\translatemanager\models\LanguageSource;

/**
 * Class for creating front end translation dialoge box
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */
class DialogAction extends \yii\base\Action {

    /**
     * Creating dialogue box.
     * @return View
     */
    public function run() {

        $languageSource = LanguageSource::findOne([
                    'category' => Yii::$app->request->post('category', ''),
                    'MD5(message)' => Yii::$app->request->post('hash', '')
        ]);

        if (!$languageSource) {
            return '<div id="translate-manager-error">' . Yii::t('language', 'Text not found in database! Please run project scan before translating!') . '</div>';
        }

        $languageTranslate = $languageSource->getLanguageTranslateByLanguage(Yii::$app->request->post('language_id', ''))->one() ? :
                new \lajax\translatemanager\models\LanguageTranslate([
            'id' => $languageSource->id,
            'language' => Yii::$app->request->post('language_id', ''),
        ]);

        return $this->controller->renderPartial('dialog', [
                    'languageSource' => $languageSource,
                    'languageTranslate' => $languageTranslate,
        ]);
    }

}
