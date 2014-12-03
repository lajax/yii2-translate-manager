<?php

namespace lajax\translatemanager\helpers;

use Yii;
use lajax\translatemanager\services\Scanner;
use lajax\translatemanager\bundles\TranslationPluginAsset;

/**
 * Language helper.
 * 
 * Inserts the necessary JavaScripts for client side multilingual support into the content.
 * 
 * For translating one-dimensional arrays.
 * 
 * For translating database tables.
 * 
 * @author Lajos Molnar <lajax.m@gmail.com>
 * @since 1.0
 */
class Language {

    /**
     * Registering JavaScripts for client side multilingual support.
     */
    public static function registerAssets() {
        TranslationPluginAsset::register(Yii::$app->view);
    }

    /**
     * Translating one dimensional array.
     * e.g.:
     * $array = [
     *      'hello' => 'Hello {name}!',
     *      'hi' => 'Hi {name}',
     * ];
     * 
     * $params = [
     *      'hello' => [
     *          'name' => 'World,
     *      ],
     *      'hi' => [
     *          'name' => 'Jenny',
     *      ],
     * ];
     * $result = \lajax\translatemanager\helpers\Language::a($array, $params);
     * 
     * The result:
     * 
     * [
     *  'hello' => 'Hello World',
     *  'hi' => 'Hi Jenny',
     * ]
     * 
     * @param array $array One-dimensonal array to be translated.
     * @param array $params List of parameters to be changed.
     * @param string $language Language of translation.
     * @return array The translated array.
     */
    public static function a($array, $params = [], $language = null) {
        $data = [];

        if (empty($params)) {
            foreach ($array as $key => $message) {
                $data[$key] = Yii::t(Scanner::CATEGORY_ARRAY, $message, [], $language);
            }
        } else {
            foreach ($array as $key => $message) {
                $data[$key] = Yii::t(Scanner::CATEGORY_ARRAY, $message, $params[$key], $language);
            }
        }


        return $data;
    }

    /**
     * For translating language elements stored in a database.
     * Enable translating databases in config before use.
     * e.g.:
     * 'modules' => [
     *      'translatemanager' => [
     *          'class' => 'lajax\translatemanager\Module',
     *          'tables' => [
     *              [
     *                  'connection' => 'db',
     *                  'table' => 'language',
     *                  'columns' => ['name', 'name_ascii'],
     *              ],
     *              [
     *                  'connection' => 'db',
     *                  'table' => 'product',
     *                  'columns' => ['name', 'description'],
     *              ]
     *          ]
     *      ]
     * ]
     * @param string $message Language element stored in database.
     * @param array $params Parameters to be changed.
     * @param string $language Language of translation.
     * @return string Translated language element.
     */
    public static function d($message, $params = [], $language = null) {
        return Yii::t(Scanner::CATEGORY_DATABASE, $message, $params, $language);
    }

    /**
     * Saveing new language element by category.
     * @param string $message Language element save in database.
     * @param string $category the message category.
     */
    public static function saveMessage($message, $category = 'database') {
        $languageSources = \lajax\translatemanager\models\LanguageSource::find()->where(['category' => $category])->all();

        $messages = [];
        foreach ($languageSources as $languageSource) {
            $messages[$languageSource->message] = $languageSource->id;
        }

        if (empty($messages[$message])) {
            $languageSource = new \lajax\translatemanager\models\LanguageSource;
            $languageSource->category = $category;
            $languageSource->message = $message;
            $languageSource->save();
        }
    }

}
