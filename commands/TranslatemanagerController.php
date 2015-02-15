<?php

namespace lajax\translatemanager\commands;

use lajax\translatemanager\services\Optimizer;
use lajax\translatemanager\services\Scanner;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Command for scanning and optimizing project translations
 * 
 * Register the command
 * 
 * ~~~
 * 'controllerMap' => [
 *     'translate' => \lajax\translatemanager\commands\TranslatemanagerController::className()
 * ],
 * ~~~
 * 
 * 
 * Use it with the Yii CLI
 * 
 * ~~~
 * ./yii translate/scan
 * ./yii translate/optimize
 * ~~~
 *
 * @author Tobias Munk <schmunk@usrbin.de>
 * @since 1.2.8
 */
class TranslatemanagerController extends Controller {

    /**
     * @inheritdoc
     */
    public $defaultAction = 'help';

    /**
     * Display this help.
     */
    public function actionHelp() {
        $this->run('/help', [$this->id]);
    }

    /**
     * Detecting new language elements.
     */
    public function actionScan() {
        $this->stdout("Scanning translations...\n", Console::BOLD);
        $scanner = new Scanner();

        $items = $scanner->scanning();
        $this->stdout("{$items} new item(s) inserted into database.\n");
    }

    /**
     * Removing unused language elements.
     */
    public function actionOptimize() {
        $this->stdout("Optimizing translations...\n", Console::BOLD);
        $optimizer = new Optimizer();
        $items = $optimizer->optimization();
        $this->stdout("{$items} removed from database.\n");
    }

}
