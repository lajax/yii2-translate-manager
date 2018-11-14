<?php

namespace lajax\translatemanager\controllers\actions;

use Yii;
use yii\web\Response;
use yii\base\Exception as BaseException;
use lajax\translatemanager\services\Generator;
use lajax\translatemanager\models\LanguageTranslate;
use lajax\translatemanager\models\LanguageSource;
use lajax\translatemanager\translation\Translator;
use lajax\translatemanager\translation\Exception as TranslationException;

/**
 * Text translation with third party service.
 *
 * @author moltam
 */
class TranslateTextAction extends \yii\base\Action
{
    /**
     * @return array
     */
    public function run()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id', 0);
        $languageId = Yii::$app->request->post('language_id', Yii::$app->language);

        $languageTranslate = LanguageTranslate::findOne(['id' => $id, 'language' => $languageId]) ?:
            new LanguageTranslate(['id' => $id, 'language' => $languageId]);

        try {
            $languageTranslate->translation = $this->translateText($id, $languageId);
        } catch (BaseException $e) {
            Yii::error('Translation failed! ' . $e->getMessage(), 'translatemanager');
            $languageTranslate->addError('translation', 'API translation failed!');
        }

        if ($languageTranslate->validate(null, false) && $languageTranslate->save()) {
            $generator = new Generator($this->controller->module, $languageId);
            $generator->run();
        }

        return $languageTranslate->getErrors();
    }

    /**
     * @param int $sourceId
     * @param string $languageId
     *
     * @return string
     *
     * @throws BaseException
     */
    protected function translateText($sourceId, $languageId)
    {
        if (!$this->controller->module->translator) {
            throw new BaseException('No translator configured!');
        }

        $source = LanguageSource::findOne($sourceId);
        if (!$source) {
            throw new BaseException('Invalid language source id!');
        }

        /* @var $translator Translator */
        $translator = Yii::createObject($this->controller->module->translator);

        try {
            return $translator->translate($source->message, $languageId);
        } catch (TranslationException $e) {
            throw new BaseException('Translation failed: ' . $e->getMessage(), 1, $e);
        }
    }
}
