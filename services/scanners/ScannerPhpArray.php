<?php

namespace lajax\translatemanager\services\scanners;

use yii\helpers\Console;
use lajax\translatemanager\services\Scanner;

/**
 * Class for processing PHP files.
 *
 * Language elements detected in constant arrays:
 *
 * ~~~
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
 * ~~~
 *
 * Translation of constant arrays:
 * Translation to site language:
 *
 * ~~~
 * $genders = \lajax\translatemanager\helpers\Language::a($this->_GENDERS);
 * ~~~
 *
 * Translating to the language of your coice:
 *
 * ~~~
 * $statuses = \lajax\translatemanager\helpers\Language::a($this->_STATUSES, [], 'de-DE');
 * ~~~
 *
 * @author Lajos Molnár <lajax.m@gmail.com>
 *
 * @since 1.0
 */
class ScannerPhpArray extends ScannerFile
{
    /**
     * Extension of PHP files.
     */
    const EXTENSION = '*.php';

    /**
     * Start scanning PHP files.
     *
     * @param string $route
     * @param array $params
     * @inheritdoc
     */
    public function run($route, $params = [])
    {
        $this->scanner->stdout('Detect PhpArray - BEGIN', Console::FG_BLUE);
        foreach (self::$files[static::EXTENSION] as $file) {
            foreach ($this->_getTranslators($file) as $translator) {
                $this->extractMessages($file, [
                    'translator' => [$translator],
                    'begin' => (preg_match('#array\s*$#i', $translator) != false) ? '(' : '[',
                    'end' => ';',
                ]);
            }
        }

        $this->scanner->stdout('Detect PhpArray - END', Console::FG_BLUE);
    }

    /**
     * Returns the names of the arrays storing the language elements to be translated.
     *
     * @param string $file Path to the file to scan.
     *
     * @return array List of arrays storing the language elements to be translated.
     */
    private function _getTranslators($file)
    {
        $subject = file_get_contents($file);
        preg_match_all($this->module->patternArrayTranslator, $subject, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        $translators = [];
        foreach ($matches as $data) {
            if (isset($data['translator'][0])) {
                $translators[$data['translator'][0]] = true;
            }
        }

        return array_keys($translators);
    }

    /**
     * @inheritdoc
     */
    protected function getLanguageItem($buffer)
    {
        $index = -1;
        $languageItems = [];
        foreach ($buffer as $key => $data) {
            if (isset($data[0], $data[1]) && $data[0] === T_CONSTANT_ENCAPSED_STRING) {
                $message = stripcslashes($data[1]);
                $message = mb_substr($message, 1, mb_strlen($message) - 2);
                if (isset($buffer[$key - 1][0]) && $buffer[$key - 1][0] === '.') {
                    $languageItems[$index]['message'] .= $message;
                } else {
                    $languageItems[++$index] = [
                        'category' => Scanner::CATEGORY_ARRAY,
                        'message' => $message,
                    ];
                }
            }
        }

        return $languageItems ?: null;
    }
}
