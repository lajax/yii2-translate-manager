<?php

namespace lajax\translatemanager\controllers\actions;

use Yii;
use yii\base\Action;
use yii\helpers\Json;
use yii\web\Response;
use lajax\translatemanager\models\Language;

/**
 * Class that modifies the state of a language.
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */
class ChangeStatusAction extends Action {

    /**
     * Modifying tha state of language.
     * @return Json
     */
    public function run() {

        Yii::$app->response->format = Response::FORMAT_JSON;

        $language = Language::findOne(Yii::$app->request->post('language_id', ''));
        if ($language !== null) {
            $language->status = Yii::$app->request->post('status', Language::STATUS_BETA);
            if ($language->validate()) {
                $language->save();
            }
        }

        return Json::encode($language->getErrors());
    }

}
