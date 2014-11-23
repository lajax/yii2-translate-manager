<?php

namespace lajax\translatemanager\services;

use lajax\translatemanager\models\LanguageSource;

/**
 * Scanner class for scanning project, detecting new language elements
 * 
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */
class Scanner {

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
     * @var array for storing language elements to be translated.
     */
    private $_languageItems = [];

    /**
     * Scanning project for text not stored in database.
     * @return integer The number of new language elements.
     */
    public function scanning() {

        $this->_scanningProject();

        $languageSources = LanguageSource::find()->all();
        foreach ($languageSources as $languageSource) {
            if (isset($this->_languageItems[$languageSource->category][$languageSource->message])) {
                unset($this->_languageItems[$languageSource->category][$languageSource->message]);
            }
        }

        $languageSource = new LanguageSource;
        return $languageSource->insertLanguageItems($this->_languageItems);
    }

    /**
     * Returns existing language elements.
     * @return array associative array containing the language elements.
     */
    public function getLanguageItems() {

        $this->_scanningProject();

        return $this->_languageItems;
    }

    /**
     * Scan project for new language elements.
     */
    private function _scanningProject() {

        $scanningFile = new scanners\ScannerFile($this->_languageItems);
        $this->_languageItems = $scanningFile->scanning();

        $scanningDatabase = new scanners\ScannerDatabase($this->_languageItems);
        $this->_languageItems = $scanningDatabase->scanning();
    }

}
