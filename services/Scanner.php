<?php

namespace lajax\translatemanager\services;

use Yii;
use yii\helpers\Console;
use lajax\translatemanager\models\LanguageSource;

/**
 * Scanner class for scanning project, detecting new language elements
 *
 * @author Lajos Molnár <lajax.m@gmail.com>
 *
 * @since 1.0
 */
class Scanner
{
    /**
     * JavaScript category.
     */
    const CATEGORY_JAVASCRIPT = 'javascript';

    /**
     * Array category.
     */
    const CATEGORY_ARRAY = 'array';

    /**
     * Database category.
     */
    const CATEGORY_DATABASE = 'database';

    /**
     * @var array List of language element classes
     */
    public $scanners = [
        '\lajax\translatemanager\services\scanners\ScannerPhpFunction',
        '\lajax\translatemanager\services\scanners\ScannerPhpArray',
        '\lajax\translatemanager\services\scanners\ScannerJavaScriptFunction',
        '\lajax\translatemanager\services\scanners\ScannerDatabase',
    ];

    /**
     * @var array for storing language elements to be translated.
     */
    private $_languageElements = [];

    /**
     * @var array for storing removabla LanguageSource ids.
     */
    private $_removableLanguageSourceIds = [];

    /**
     * Scanning project for text not stored in database.
     *
     * @return int The number of new language elements.
     *
     * @deprecated since version 1.4
     */
    public function scanning()
    {
        return $this->run();
    }

    /**
     * Scanning project for text not stored in database.
     *
     * @return int The number of new language elements.
     */
    public function run()
    {
        $scanTimeLimit = Yii::$app->getModule('translatemanager')->scanTimeLimit;

        if (!is_null($scanTimeLimit)) {
            set_time_limit($scanTimeLimit);
        }

        $scanners = Yii::$app->getModule('translatemanager')->scanners;
        if (!empty($scanners)) {
            $this->scanners = $scanners; // override scanners from module configuration (custom scanners)
        }

        $this->_initLanguageArrays();

        $languageSource = new LanguageSource();

        return $languageSource->insertLanguageItems($this->_languageElements);
    }

    /**
     * Returns new language elements.
     *
     * @return array associative array containing the new language elements.
     */
    public function getNewLanguageElements()
    {
        return $this->_languageElements;
    }

    /**
     * Returns removable LanguageSource ids.
     *
     * @return array
     */
    public function getRemovableLanguageSourceIds()
    {
        return $this->_removableLanguageSourceIds;
    }

    /**
     * Returns existing language elements.
     *
     * @return array associative array containing the language elements.
     *
     * @deprecated since version 1.4.2
     */
    public function getLanguageItems()
    {
        $this->_initLanguageArrays();

        return $this->_languageElements;
    }

    /**
     * Initialising $_languageItems and $_removableLanguageSourceIds arrays.
     */
    private function _initLanguageArrays()
    {
        $this->_scanningProject();

        $languageSources = LanguageSource::find()->all();

        foreach ($languageSources as $languageSource) {
            if (isset($this->_languageElements[$languageSource->category][$languageSource->message])) {
                unset($this->_languageElements[$languageSource->category][$languageSource->message]);
            } else {
                $this->_removableLanguageSourceIds[$languageSource->id] = $languageSource->id;
            }
        }
    }

    /**
     * Scan project for new language elements.
     */
    private function _scanningProject()
    {
        foreach ($this->scanners as $scanner) {
            $object = new $scanner($this);
            $object->run('');
        }
    }

    /**
     * Adding language elements to the array.
     *
     * @param string $category
     * @param string $message
     */
    public function addLanguageItem($category, $message)
    {
        $this->_languageElements[$category][$message] = true;

        $coloredCategory = Console::ansiFormat($category, [Console::FG_YELLOW]);
        $coloredMessage = Console::ansiFormat($message, [Console::FG_YELLOW]);

        $this->stdout('Detected language element: [ ' . $coloredCategory . ' ] ' . $coloredMessage);
    }

    /**
     * Adding language elements to the array.
     *
     * @param array $languageItems
     * example:
     *
     * ~~~
     * [
     *      [
     *          'category' => 'language',
     *          'message' => 'Active'
     *      ],
     *      [
     *          'category' => 'language',
     *          'message' => 'Inactive'
     *      ],
     * ]
     * ~~~
     */
    public function addLanguageItems($languageItems)
    {
        foreach ($languageItems as $languageItem) {
            $this->addLanguageItem($languageItem['category'], $languageItem['message']);
        }
    }

    /**
     * Prints a string to STDOUT
     *
     * @param string $string
     */
    public function stdout($string)
    {
        if (Yii::$app->request->isConsoleRequest) {
            if (Console::streamSupportsAnsiColors(STDOUT)) {
                $args = func_get_args();
                array_shift($args);
                $string = Console::ansiFormat($string, $args);
            }

            Console::stdout($string . "\n");
        }
    }
}
