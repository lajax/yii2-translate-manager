<?php

namespace lajax\translatemanager\controllers\actions;

use Yii;
use yii\base\Action;
use lajax\translatemanager\services\Scanner;

/**
 * Class for detecting language elements.
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */

class ScannAction extends Action {

    /**
     * Detecting new language elements.
     * @return string
     */
    public function run() {

        $scanner = new Scanner;
        $items_count = $scanner->scanning();

        $message = Yii::t('language', '{n, plural, =0{No new entries} =1{One new entry} other{# new entries}} were added!', ['n' => $items_count]);
        Yii::$app->session->setFlash('info', $message);


        return $this->controller->render('scann');
    }


}
