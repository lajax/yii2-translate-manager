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

class ScanAction extends Action {

    /**
     * Detecting new language elements.
     * @return string
     */
    public function run() {

        $scanner = new Scanner;
        return $this->controller->render('scan', [
            'items_count' => $scanner->scanning()
        ]);
    }


}
