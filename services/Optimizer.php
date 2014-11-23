<?php

namespace lajax\translatemanager\services;

use lajax\translatemanager\services\Scanner;
use lajax\translatemanager\models\LanguageSource;

/**
 * Optimizer class for optimizing database tables
 * 
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */
class Optimizer {

    /**
     * @var array Existing language element entries
     */
    private $_languageItems;

    /**
     * @var array a Current language elements in the translating system
     */
    private $_languageSources;

    /**
     * Removing unused language elements from database.
     * @return int Number of unused language elements detected.
     */
    public function optimization() {

        return $this->_optimizeDatabase();
    }

    /**
     * Removing unused language elements from database.
     * @return integer The number of removed language elements.
     */
    private function _optimizeDatabase() {

        $scanner = new Scanner;
        $this->_languageItems = $scanner->getLanguageItems();

        $this->_createLanguageSource();

        // Removing active elements from array.
        // Only removable inactive elements left in array.
        foreach ($this->_languageItems as $category => $messages) {
            foreach ($messages as $message => $id) {
                if (isset($this->_languageSources[$category][$message])) {
                    unset($this->_languageSources[$category][$message]);
                }
            }
        }

        $ids = [];
        foreach ($this->_languageSources as $messages) {
            foreach ($messages as $id) {
                $ids[$id] = true;           // Duplication filtering
            }
        }

        LanguageSource::deleteAll(['IN', 'id', array_keys($ids)]);

        return count($ids);
    }

    /**
     * Creating _languageSources array.
     */
    private function _createLanguageSource() {
        $languageSources = LanguageSource::find()->all();
        foreach ($languageSources as $languageSource) {
            $this->_languageSources[$languageSource->category][$languageSource->message] = $languageSource->id;
        }
    }

}
