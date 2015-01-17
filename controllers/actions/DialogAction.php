<?php

namespace lajax\translatemanager\controllers\actions;

use Yii;
use yii\base\Action;
use lajax\translatemanager\models\LanguageSource;
use lajax\translatemanager\models\LanguageTranslate;

/**
 * Class for creating front end translation dialoge box
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */
class DialogAction extends Action {

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

        $languageTranslate = LanguageTranslate::getLanguageTranslateByIdAndLanguageId($languageSource->id, Yii::$app->request->post('language_id', ''));
        return $this->controller->renderPartial('dialog', [
            'languageSource' => $languageSource,
            'languageTranslate' => $languageTranslate,
        ]);
    }

}
