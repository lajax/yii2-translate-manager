<?php

namespace lajax\translatemanager\controllers\actions;

use Yii;
use yii\base\Action;
use lajax\translatemanager\models\searches\LanguageSearch;
use lajax\translatemanager\bundles\LanguageAsset;
use lajax\translatemanager\bundles\LanguagePluginAsset;

/**
 * Class that creates a list of languages.
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */
class ListAction extends Action {

    /**
     * List of languages
     * @return string
     */
    public function run() {

        $searchModel = new LanguageSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        LanguageAsset::register($this->controller->view);
        LanguagePluginAsset::register($this->controller->view);

        return $this->controller->render('list', [
                    'dataProvider' => $dataProvider,
                    'searchModel' => $searchModel,
        ]);
    }

}
