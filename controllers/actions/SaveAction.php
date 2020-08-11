<?php

namespace lajax\translatemanager\controllers\actions;

use Yii;
use yii\web\Response;
use lajax\translatemanager\services\Generator;
use lajax\translatemanager\models\LanguageTranslate;

/**
 * Class for saving translations.
 *
 * @author Lajos Molnár <lajax.m@gmail.com>
 *
 * @since 1.0
 */
class SaveAction extends \yii\base\Action
{
    /**
     * Saving translated language elements.
     *
     * @return array
     */
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id', 0);
        $languageId = Yii::$app->request->post('language_id', Yii::$app->language);
        $translation = Yii::$app->request->post('translation', '');

        $languageTranslate = LanguageTranslate::findOne(['id' => $id, 'language' => $languageId]);

        $regenerate = false;

        if (trim($translation) == '') {
            if ($languageTranslate && $languageTranslate->delete()) {
                $regenerate = true;
            } else {
                return [];
            }
        } else {
            $languageTranslate = $languageTranslate ?:
                new LanguageTranslate(['id' => $id, 'language' => $languageId]);
            $languageTranslate->translation = $translation;
            $languageTranslate->status = 'done';

            if ($languageTranslate->validate() && $languageTranslate->save()) {
                $regenerate = true;
            }
        }

        if ($regenerate) {
            $generator = new Generator($this->controller->module, $languageId);

            $generator->run();
        }

        return $languageTranslate->getErrors();
    }
}
