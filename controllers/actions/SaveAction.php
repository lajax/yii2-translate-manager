<?php

namespace lajax\translatemanager\controllers\actions;

use Yii;
use yii\base\Action;
use yii\helpers\Json;
use yii\web\Response;
use lajax\translatemanager\services\Generator;
use lajax\translatemanager\models\LanguageTranslate;

/**
 * Class for saving translations.
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */
class SaveAction extends Action {

    /**
     * Saving translated language elements.
     * @return Json
     */
    public function run() {

        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id', 0);
        $languageId = Yii::$app->request->post('language_id', '');

        $languageTranslate = LanguageTranslate::getLanguageTranslateByIdAndLanguageId($id, $languageId);
        $languageTranslate->translation = Yii::$app->request->post('translation', '');
        if ($languageTranslate->validate() && $languageTranslate->save()) {
            $generator = new Generator($this->controller->module, $languageId);

            $generator->generate();
        }

        return Json::encode($languageTranslate->getErrors());
    }

}
