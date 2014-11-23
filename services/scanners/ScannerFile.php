<?php

namespace lajax\translatemanager\services\scanners;

use Yii;
use yii\helpers\FileHelper;
use lajax\translatemanager\services\Scanner;

/**
 * <pre>Class for processing PHP and JavaScript files.
 * Language elements detected in JavaScript files:
 * lajax.t('language element);
 * lajax.t('language element {replace}', {replace:'String'});
 * lajax.t("language element");
 * lajax.t("language element {replace}", {replace:'String'});
 * Language elements detected in PHP files:
 * "t" functions:
 * ::t(‘category of language element', 'language element');
 * ::t('category of language element', 'language element {replace}', ['replace' => 'String']);
 * ::t('category of language element', "language element");
 * ::t('category of language element', "language element {replace}", ['replace' => 'String']);
 * Language elements detected in constant arrays:
 * 
 *  /**
 *   * @translate
 *   *\/
 *  private $_GENDERS = ['Male', 'Female'];
 *  /**
 *   * @translate
 *   *\/
 *   private $_STATUSES = [
 *      self::STATUS_ACTIVE => 'Active',
 *      self::STATUS_INACTIVE => 'Inactive'
 *   ];
 * Translation of constant arrays:
 * Translation to site language:
 * $genders = \lajax\translatemanager\helpers\Language::a($this->_GENDERS);
 * Translating to the language of your coice:
 * $statuses = \lajax\translatemanager\helpers\Language::a($this->_STATUSES, [], 'de_DE');
 * </pre>
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */
class ScannerFile {

    /**
     * @var array storing language elements to be translated.
     */
    private $_languageItems = [];

    /**
     * @var \lajax\translatemanager\Module TranslateManager Module
     */
    private $_module;

    /**
     * @param array $languageItems
     */
    public function __construct($languageItems = []) {
        $this->_module = Yii::$app->getModule('translatemanager');
        $this->_languageItems = $languageItems;
    }

    /**
     * Scans files searching for language elements not yet translated.
     */
    public function scanning() {
        $files = FileHelper::findFiles(realpath($this->_getRoot()), [
                    'except' => $this->_module->ignoredItems,
                    'only' => $this->_module->patterns,
        ]);
        foreach ($files as $filename) {
            $file = file_get_contents($filename);
            if (pathinfo($filename, PATHINFO_EXTENSION) === 'php') {
                $this->_regex($this->_module->patternPhp, $file);
                $this->_regex($this->_module->patternArray, $file, true);
            } else {
                $this->_regex($this->_module->patternJs, $file);
            }
        }

        return $this->_languageItems;
    }

    /**
     * Collects language elements stored in file.
     * @param string $pattern regular expression for detecting language elements
     * @param string $subject source file to scan
     * @param boolean $recursive True if search is recursive.
     */
    private function _regex($pattern, $subject, $recursive = false) {
        preg_match_all($pattern, $subject, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        foreach ($matches as $language_item) {
            if (!$this->_isLanguageItem($language_item['text'][0])) {
                continue;
            } else if ($recursive) {
                $this->_regex($this->_module->patternArrayRecursive, $language_item['text'][0]);
            } else {

                $category = $this->_getLanguageItemCategory($language_item);
                if ($this->_isValidCategory($category)) {
                    $message = eval("return {$language_item['text'][0]};");
                    $this->_languageItems[$category][$message] = true;
                }
            }
        }
    }

    /**
     * Returns the root directory of the project.
     * @return string
     */
    private function _getRoot() {
        $directories = explode('/', Yii::getAlias($this->_module->root));
        array_pop($directories);
        return implode('/', $directories);
    }

    /**
     * Returns the category of the given language.
     * @param array $data Contents of the matches array returned by preg_match_all
     * @return string The name of the category in the category key of the array received as a parameter.
     * If there is no such key, the file was a JavaScript file.
     */
    private function _getLanguageItemCategory($data) {
        if (isset($data['category'][0])) {
            return empty($data['category'][0]) ? Scanner::CATEGORY_ARRAY : substr($data['category'][0], 1, -1);
        }

        return Scanner::CATEGORY_JAVASCRIPT;
    }

    /**
     * Determines whether the category received as a parameter can be processed.
     * @param string $category
     * @return boolean
     */
    public function _isValidCategory($category) {
        return !in_array($category, $this->_module->ignoredCategories);
    }

    /**
     * Determines whether the text received as a parameter has to be translated.
     * @param string $language_item
     * @return boolean
     */
    private function _isLanguageItem($language_item) {
        if (mb_strlen(trim($language_item)) != 0) {
            return true;
        }

        return false;
    }

}
