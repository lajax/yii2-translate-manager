<?php

namespace lajax\translatemanager\services\scanners;

use lajax\translatemanager\services\Scanner;

/**
 * Class for processing JavaScript files.
 * Language elements detected in JavaScript files:
 * "lajax.t" functions
 * ~~~
 * lajax.t('language element);
 * lajax.t('language element {replace}', {replace:'String'});
 * lajax.t("language element");
 * lajax.t("language element {replace}", {replace:'String'});
 * ~~~
 * 
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */
class ScannerJavaScriptFunction extends ScannerFile {

    /**
     * Extension of JavaScript files.
     */
    const EXTENSION = '*.js';

    /**
     * Start scanning JavaScript files.
     * @param string $route
     * @param array $params
     * @inheritdoc
     */
    public function run($route, $params = array()) {
        foreach (self::$files[static::EXTENSION] as $file) {
            if (preg_match('#' . preg_quote(implode('\s*\(|', $this->module->jsTranslators)) . '\s*\(#i', file_get_contents($file)) != false) {
                $this->extractMessages($file, [
                    'translator' => (array) $this->module->jsTranslators,
                    'begin' => '(',
                    'end' => ')'
                ]);
            }
        }
    }

    /**
     * Returns language elements in the token buffer.
     * If there is no recognisable language element in the array, returns null.
     * @param array $buffer
     * @return array|null
     */
    protected function getLanguageItem($buffer) {
        if (isset($buffer[0][0]) && $buffer[0][0] === T_CONSTANT_ENCAPSED_STRING) {

            foreach ($buffer as $data) {
                if (isset($data[0], $data[1]) && $data[0] === T_CONSTANT_ENCAPSED_STRING) {
                    $message = stripcslashes($data[1]);
                    $messages[] = mb_substr($message, 1, mb_strlen($message) - 2);
                } else if ($data === ',') {
                    break;
                }
            }

            $message = implode('', $messages);

            return [
                [
                    'category' => Scanner::CATEGORY_JAVASCRIPT,
                    'message' => $message
                ]
            ];
        }

        return null;
    }

}
