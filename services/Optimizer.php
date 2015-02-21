<?php

namespace lajax\translatemanager\services;

use Yii;
use yii\helpers\Console;
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
     * @deprecated since version 1.4
     */
    public function optimization() {

        return $this->run();
    }
    
    /**
     * Removing unused language elements from database.
     * @return integer|array The number of removed language elements, or removed language elements.
     */
    public function run() {

        $scanner = new Scanner;
        $this->_languageItems = $scanner->getLanguageItems();
        $scanner->stdout('Optimizing translations - BEGIN', Console::FG_RED);

        $this->_initLanguageSources();

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
        foreach ($this->_languageSources as $category => $messages) {
            foreach ($messages as $message => $id) {
                $ids[$id] = true;           // Duplication filtering
                $message = Console::ansiFormat($message, [Console::FG_RED]);
                $scanner->stdout('Remove message: ' . $message);
            }
        }

        LanguageSource::deleteAll(['IN', 'id', array_keys($ids)]);

        $scanner->stdout('Optimizing translations - END', Console::FG_RED);
        
        return (Yii::$app->request->isConsoleRequest) ? count($ids) : $this->_languageSources;
    }

    /**
     * Creating _languageSources array.
     */
    private function _initLanguageSources() {
        $languageSources = LanguageSource::find()->all();
        foreach ($languageSources as $languageSource) {
            $this->_languageSources[$languageSource->category][$languageSource->message] = $languageSource->id;
        }
    }

}
