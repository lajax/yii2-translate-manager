<?php

namespace lajax\translatemanager\controllers\actions;

use Yii;
use yii\base\Action;
use lajax\translatemanager\services\Optimizer;

/**
 * Class for optimizing language database.
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */
class OptimizerAction extends Action {

    /**
     * Removing unused language elements.
     * @return string
     */
    public function run() {
        $optimizer = new Optimizer;
        $items_count = $optimizer->optimization();
        $message = Yii::t('language', '{n, plural, =0{No entries} =1{One entry} other{# entries}} were removed!', ['n' => $items_count]);
        Yii::$app->session->setFlash('info', $message);

        return $this->controller->render('optimizer');
    }

}
