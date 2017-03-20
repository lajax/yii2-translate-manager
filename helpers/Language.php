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
 *
 * @since 1.1
 */
class Language
{
    /**
     * @var string parent span for front end translation.
     */
    private static $_template = '<span class="language-item" data-category="{category}" data-hash="{hash}" data-language_id="{language_id}" data-params="{params}">{message}</span>';

    /**
     * Registering JavaScripts for client side multilingual support.
     */
    public static function registerAssets()
    {
        TranslationPluginAsset::register(Yii::$app->view);
    }

    /**
     * @param string $category the message category.
     * @param string $message the message to be translated.
     * @param array $params the parameters that will be used to replace the corresponding placeholders in the message.
     * @param string $language the language code (e.g. `en-US`, `en`).
     * If this is null, the current [[\yii\base\Application::language|application language]] will be used.
     *
     * @return string the translated message.
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        if (self::isEnabledTranslate()) {
            return strtr(self::$_template, [
                '{language_id}' => $language ? $language : Yii::$app->language,
                '{category}' => $category,
                '{message}' => Yii::t($category, $message, $params, $language),
                '{params}' => \yii\helpers\Html::encode(\yii\helpers\Json::encode($params)),
                '{hash}' => md5($message),
            ]);
        } else {
            return Yii::t($category, $message, $params, $language);
        }
    }

    /**
     * Translating one-dimensional array.
     * e.g.:
     *
     * ~~~
     * $array = [
     *      'hello' => 'Hello {name}!',
     *      'hi' => 'Hi {name}',
     * ];
     *
     * $params = [
     *      'hello' => [
     *          'name' => 'World',
     *      ],
     *      'hi' => [
     *          'name' => 'Jenny',
     *      ],
     * ];
     * $result = \lajax\translatemanager\helpers\Language::a($array, $params);
     * ~~~
     *
     * The result:
     *
     * ~~~
     * [
     *  'hello' => 'Hello World',
     *  'hi' => 'Hi Jenny',
     * ]
     * ~~~
     *
     * Translating multi-dimensional array.
     * e.g.:
     *
     * ~~~
     * $array = [
     *      self::GENDER_MALE => [
     *          self::MATERIALSTATUS_MARRIED => 'Mr. {name}',
     *          self::MATERIALSTATUS_SINGLE => 'Mr. {name}'
     *      ],
     *      self::GENDER_FEMALE => [
     *          self::MATERIALSTATUS_MARRIED => 'Mrs. {name}',
     *          self::MATERIALSTATUS_SINGLE => 'Miss {name}'
     *      ]
     * ];
     *
     * $params = [
     *      self::GENDER_MALE => [
     *          self::MATERIALSTATUS_MARRIED => [
     *              'name' => 'Smith'
     *          ],
     *          self::MATERIALSTATUS_SINGLE => [
     *              'name' => 'Stark'
     *          ]
     *      ],
     *      self::GENDER_FEMALE => [
     *          self::MATERIALSTATUS_MARRIED => [
     *              'name' => 'Smith'
     *          ],
     *          self::MATERIALSTATUS_SINGLE => [
     *              'name' => 'Potts'
     *          ]
     *      ]
     * ];
     * $result = \lajax\translatemanager\helpers\Language::a($array, $params);
     * ~~~
     *
     * The result:
     *
     * ~~~
     * [
     *  self::GENDER_MALE => [
     *          self::MATERIALSTATUS_MARRIED => 'Mr. Smith',
     *          self::MATERIALSTATUS_SINGLE => 'Mr. Stark'
     *  ],
     *  self::GENDER_FEMALE => [
     *          self::MATERIALSTATUS_MARRIED => 'Mrs. Smith',
     *          self::MATERIALSTATUS_SINGLE => 'Miss Potts'
     *  ]
     * ]
     * ~~~
     *
     * @param array $array One-dimensonal or Multi-dimensional array to be translated.
     * @param array $params List of parameters to be changed.
     * @param string $language Language of translation.
     *
     * @return array The translated array.
     */
    public static function a($array, $params = [], $language = null)
    {
        $data = [];

        foreach ($array as $key => $message) {
            if (!is_array($message)) {
                $data[$key] = Yii::t(Scanner::CATEGORY_ARRAY, $message, isset($params[$key]) ? $params[$key] : [], $language);
            } else {
                $data[$key] = self::a($message, isset($params[$key]) ? $params[$key] : [], $language);
            }
        }

        return $data;
    }

    /**
     * For translating language elements stored in a database.
     * Enable translating databases in config before use.
     * e.g.:
     *
     * ~~~
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
     * ~~~
     *
     *
     * @param string $message Language element stored in database.
     * @param array $params Parameters to be changed.
     * @param string $language Language of translation.
     *
     * @return string Translated language element.
     */
    public static function d($message, $params = [], $language = null)
    {
        return Yii::t(Scanner::CATEGORY_DATABASE, $message, $params, $language);
    }

    /**
     * Determines whether the translation mode is active.
     *
     * @return bool
     */
    public static function isEnabledTranslate()
    {
        return Yii::$app->session->has(\lajax\translatemanager\Module::SESSION_KEY_ENABLE_TRANSLATE);
    }

    /**
     * Saveing new language element by category.
     *
     * @param string $message Language element save in database.
     * @param string $category the message category.
     */
    public static function saveMessage($message, $category = 'database')
    {
        $languageSources = \lajax\translatemanager\models\LanguageSource::find()->where(['category' => $category])->all();

        $messages = [];
        foreach ($languageSources as $languageSource) {
            $messages[$languageSource->message] = $languageSource->id;
        }

        if (empty($messages[$message])) {
            $languageSource = new \lajax\translatemanager\models\LanguageSource();
            $languageSource->category = $category;
            $languageSource->message = $message;
            $languageSource->save();
        }
    }

    /**
     * Returns the category of LanguageSource in an associative array.
     *
     * @return array
     */
    public static function getCategories()
    {
        $languageSources = \lajax\translatemanager\models\LanguageSource::find()->select('category')->distinct()->all();

        $categories = [];
        foreach ($languageSources as $languageSource) {
            $categories[$languageSource->category] = $languageSource->category;
        }

        return $categories;
    }
}
