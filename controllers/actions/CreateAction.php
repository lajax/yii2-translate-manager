<?php

namespace lajax\translatemanager\controllers\actions;

use Yii;
use yii\widgets\ActiveForm;
use lajax\translatemanager\models\Language;

/**
 * Creates a new Language model.
 *
 * @author Lajos Molnár <lajax.m@gmail.com>
 *
 * @since 1.3
 */
class CreateAction extends \yii\base\Action
{
    /**
     * Creates a new Language model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function run()
    {
        $model = new Language();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            return ActiveForm::validate($model);
        } elseif ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->controller->redirect(['view', 'id' => $model->language_id]);
        } else {
            return $this->controller->render('create', [
                'model' => $model,
            ]);
        }
    }
}
