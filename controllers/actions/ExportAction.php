<?php

namespace lajax\translatemanager\controllers\actions;

use Yii;
use yii\web\JsonResponseFormatter;
use yii\web\Response;
use yii\web\XmlResponseFormatter;
use lajax\translatemanager\Module;
use lajax\translatemanager\models\ExportForm;
use lajax\translatemanager\bundles\LanguageAsset;
use lajax\translatemanager\bundles\LanguagePluginAsset;

/**
 * Class for exporting translations.
 */
class ExportAction extends \yii\base\Action
{
    /**
     * @inheritdoc
     */
    public function init()
    {
        LanguageAsset::register($this->controller->view);
        LanguagePluginAsset::register($this->controller->view);
        parent::init();
    }

    /**
     * Show export form or generate export file on post
     *
     * @return string
     */
    public function run()
    {
        /** @var Module $module */
        $module = $this->controller->module;

        $model = new ExportForm([
            'format' => $module->defaultExportFormat,
        ]);

        if ($model->load(Yii::$app->request->post())) {
            $fileName = Yii::t('language', 'translations') . '.' . $model->format;

            Yii::$app->response->format = $model->format;

            Yii::$app->response->formatters = [
                Response::FORMAT_XML => [
                    'class' => XmlResponseFormatter::className(),
                    'rootTag' => 'translations',
                ],
                Response::FORMAT_JSON => [
                    'class' => JsonResponseFormatter::className(),
                ],
            ];

            Yii::$app->response->setDownloadHeaders($fileName);

            return $model->getExportData();
        } else {
            if (empty($model->languages)) {
                $model->exportLanguages = $model->getDefaultExportLanguages($module->defaultExportStatus);
            }

            return $this->controller->render('export', [
                'model' => $model,
            ]);
        }
    }
}
