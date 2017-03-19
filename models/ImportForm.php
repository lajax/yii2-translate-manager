<?php

namespace lajax\translatemanager\models;

use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

/**
 * Import Form.
 *
 * @author rhertogh <>
 *
 * @since 1.5.0
 */
class ImportForm extends Model
{
    /**
     * @var UploadedFile The file to import (json or xml)
     */
    public $importFile;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['importFile'],
                'file',
                'skipOnEmpty' => false,
                'mimeTypes' => [
                    'text/xml',
                    'application/xml',
                    'application/json',
                    'text/plain', //json is sometimes incorrectly marked as text/plain
                ],
                'enableClientValidation' => false,
            ],
        ];
    }

    /**
     * Import the uploaded file. Existing languages and translations will be updated, new ones will be created.
     * Source messages won't be updated, only created if they not exist.
     *
     * @return array
     *
     * @throws BadRequestHttpException
     * @throws Exception
     */
    public function import()
    {
        $result = [
            'languages' => ['new' => 0, 'updated' => 0],
            'languageSources' => ['new' => 0, 'updated' => 0],
            'languageTranslations' => ['new' => 0, 'updated' => 0],
        ];

        $data = $this->parseImportFile();

        /** @var Language[] $languages */
        $languages = Language::find()->indexBy('language_id')->all();

        foreach ($data['languages'] as $importedLanguage) {
            if (isset($languages[$importedLanguage['language_id']])) {
                $language = $languages[$importedLanguage['language_id']];
            } else {
                $language = new Language();
            }

            //cast integers
            $importedLanguage['status'] = (int) $importedLanguage['status'];

            $language->attributes = $importedLanguage;
            if (count($language->getDirtyAttributes())) {
                $saveType = $language->isNewRecord ? 'new' : 'updated';
                if ($language->save()) {
                    ++$result['languages'][$saveType];
                } else {
                    $this->throwInvalidModelException($language);
                }
            }
        }

        /** @var LanguageSource[] $languageSources */
        $languageSources = LanguageSource::find()->indexBy('id')->all();

        /** @var LanguageTranslate[] $languageTranslations */
        $languageTranslations = LanguageTranslate::find()->all();

        /*
         *  Create 2 dimensional array for current and imported translation, first index by LanguageSource->id
         *  and than indexed by LanguageTranslate->language.
         *  E.g.: [
         *      id => [
         *          language => LanguageTranslate (for $languageTranslations) / Array (for $importedLanguageTranslations)
         *          ...
         *      ]
         *      ...
         * ]
         */
        $languageTranslations = ArrayHelper::map($languageTranslations, 'language', function ($languageTranslation) {
            return $languageTranslation;
        }, 'id');
        $importedLanguageTranslations = ArrayHelper::map($data['languageTranslations'], 'language', function ($languageTranslation) {
            return $languageTranslation;
        }, 'id');

        foreach ($data['languageSources'] as $importedLanguageSource) {
            $languageSource = null;

            //check if id exist and if category and messages are matching
            if (isset($languageSources[$importedLanguageSource['id']]) &&
                ($languageSources[$importedLanguageSource['id']]->category == $importedLanguageSource['category']) &&
                ($languageSources[$importedLanguageSource['id']]->message == $importedLanguageSource['message'])
            ) {
                $languageSource = $languageSources[$importedLanguageSource['id']];
            }

            if (is_null($languageSource)) {
                //no match by id, search by message
                foreach ($languageSources as $languageSourceSearch) {
                    if (($languageSourceSearch->category == $importedLanguageSource['category']) &&
                        ($languageSourceSearch->message == $importedLanguageSource['message'])
                    ) {
                        $languageSource = $languageSourceSearch;
                        break;
                    }
                }
            }

            if (is_null($languageSource)) {
                //still no match, create new
                $languageSource = new LanguageSource([
                    'category' => $importedLanguageSource['category'],
                    'message' => $importedLanguageSource['message'],
                ]);

                if ($languageSource->save()) {
                    ++$result['languageSources']['new'];
                } else {
                    $this->throwInvalidModelException($languageSource);
                }
            }

            //do we have translations for the current source?
            if (isset($importedLanguageTranslations[$importedLanguageSource['id']])) {
                //loop through the translations for the current source
                foreach ($importedLanguageTranslations[$importedLanguageSource['id']] as $importedLanguageTranslation) {
                    $languageTranslate = null;

                    //is there already a translation for this souce
                    if (isset($languageTranslations[$languageSource->id]) &&
                        isset($languageTranslations[$languageSource->id][$importedLanguageTranslation['language']])
                    ) {
                        $languageTranslate = $languageTranslations[$languageSource->id][$importedLanguageTranslation['language']];
                    }

                    //no translation found, create a new one
                    if (is_null($languageTranslate)) {
                        $languageTranslate = new LanguageTranslate();
                    }

                    $languageTranslate->attributes = $importedLanguageTranslation;

                    //overwrite the id because the $languageSource->id might be different from the $importedLanguageTranslation['id']
                    $languageTranslate->id = $languageSource->id;

                    if (count($languageTranslate->getDirtyAttributes())) {
                        $saveType = $languageTranslate->isNewRecord ? 'new' : 'updated';
                        if ($languageTranslate->save()) {
                            ++$result['languageTranslations'][$saveType];
                        } else {
                            $this->throwInvalidModelException($languageTranslate);
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Parse the uploaded file (xml or json) and return it as an array
     *
     * @return array[]
     *
     * @throws BadRequestHttpException
     */
    protected function parseImportFile()
    {
        $importFileContent = file_get_contents($this->importFile->tempName);

        if ($this->importFile->extension == Response::FORMAT_JSON) {
            $data = Json::decode($importFileContent);
        } elseif ($this->importFile->extension == Response::FORMAT_XML) {
            $xml = simplexml_load_string($importFileContent);
            $json = json_encode($xml);
            $data = json_decode($json, true);

            //rebuild data due to simplexml merging duplicate elements
            foreach ($data as $key => $value) {
                $data[$key] = current($value);
            }
        } else {//should be caught by the form validation, but just in case
            throw new BadRequestHttpException('Only json and xml files are supported.');
        }

        return $data;
    }

    /**
     * Converts the model validation errors to a readable format an throws it as an exception
     *
     * @param ActiveRecord $model
     *
     * @throws Exception
     */
    protected function throwInvalidModelException($model)
    {
        $errorMessage = Yii::t('language', 'Invalid model "{model}":', ['model' => $model->className()]);
        foreach ($model->getErrors() as $attribute => $errors) {
            $errorMessage .= "\n $attribute: " . join(', ', $errors);
        }
        throw new Exception($errorMessage);
    }
}
