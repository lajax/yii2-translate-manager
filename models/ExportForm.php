<?php

namespace lajax\translatemanager\models;

use yii\base\Model;

/**
 * Export Form.
 *
 * @author rhertogh <>
 *
 * @since 1.5.0
 */
class ExportForm extends Model
{
    /**
     * @var string[] The languages to export
     */
    public $exportLanguages;

    /**
     * @var string The file format in which to export the data (json or xml)
     */
    public $format;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['exportLanguages', 'format'], 'required'],
        ];
    }

    /**
     * Find languages matching the minimumStatus
     *
     * @param $minimumStatus int The status of the returned language will be equal or larger than this number.
     *
     * @return Language[]
     */
    public function getDefaultExportLanguages($minimumStatus)
    {
        return Language::find()
            ->select('language_id')
            ->where(['>=', 'status', $minimumStatus])
            ->column();
    }

    /**
     * @return array[] Generate a two dimensional array of the translation data for the exportLanguages:
     *
     * ~~~
     * [
     *  'languages' => [],
     *  'languageSources' => [],
     *  'languageTranslations' => [],
     * ]
     * ~~~
     */
    public function getExportData()
    {
        $languages = Language::findAll($this->exportLanguages);
        $languageSources = LanguageSource::find()->all();
        $languageTranslations = LanguageTranslate::findAll(['language' => $this->exportLanguages]);

        $data = [
            'languages' => $languages,
            'languageSources' => $languageSources,
            'languageTranslations' => $languageTranslations,
        ];

        return $data;
    }
}
