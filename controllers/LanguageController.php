<?php

namespace lajax\translatemanager\controllers;

use yii\web\Controller;
use yii\filters\AccessControl;

/**
 * Controller for managing multilinguality.
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */
class LanguageController extends Controller {

    /**
     * @var \lajax\translatemanager\Module TranslateManager module
     */
    public $module;

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['list', 'change-status', 'optimizer', 'scann', 'translate', 'save'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['list', 'change-status', 'optimizer', 'scann', 'translate', 'save'],
                        'roles' => $this->module->roles,
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'list' => [
                'class' => 'lajax\translatemanager\controllers\actions\ListAction',
            ],
            'change-status' => [
                'class' => 'lajax\translatemanager\controllers\actions\ChangeStatusAction',
            ],
            'optimizer' => [
                'class' => 'lajax\translatemanager\controllers\actions\OptimizerAction',
            ],
            'scann' => [
                'class' => 'lajax\translatemanager\controllers\actions\ScannAction',
            ],
            'translate' => [
                'class' => 'lajax\translatemanager\controllers\actions\TranslateAction',
            ],
            'save' => [
                'class' => 'lajax\translatemanager\controllers\actions\SaveAction',
            ],
        ];
    }

}
