<?php

namespace lajax\translatemanager\commands;

use lajax\translatemanager\services\Optimizer;
use lajax\translatemanager\services\Scanner;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Command for scanning and optimizing project translations
 *
 * @author Tobias Munk <schmunk@usrbin.de>
 *
 * @since 1.2.8
 */
class TranslatemanagerController extends Controller
{
    /**
     * @inheritdoc
     */
    public $defaultAction = 'help';

    /**
     * Display this help.
     */
    public function actionHelp()
    {
        $this->run('/help', [$this->id]);
    }

    /**
     * Detecting new language elements.
     */
    public function actionScan()
    {
        $this->stdout("Scanning translations...\n", Console::BOLD);
        $scanner = new Scanner();

        $items = $scanner->run();
        $this->stdout("{$items} new item(s) inserted into database.\n");
    }

    /**
     * Removing unused language elements.
     */
    public function actionOptimize()
    {
        $this->stdout("Optimizing translations...\n", Console::BOLD);
        $optimizer = new Optimizer();
        $items = $optimizer->run();
        $this->stdout("{$items} removed from database.\n");
    }
}
